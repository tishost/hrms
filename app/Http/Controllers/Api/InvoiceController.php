<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\RentPayment;
use App\Models\TenantLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = $request->user()->owner->id;

        $invoices = Invoice::where('owner_id', $ownerId)
            ->with(['tenant:id,first_name,last_name', 'unit:id,name', 'property:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($invoice) {
                // Parse breakdown JSON to get fees with detailed descriptions
                $breakdown = [];
                if ($invoice->breakdown) {
                    try {
                        $breakdown = json_decode($invoice->breakdown, true) ?? [];

                        // Add descriptions for common fee types
                        foreach ($breakdown as &$fee) {
                            if (!isset($fee['description']) || empty($fee['description'])) {
                                $feeName = strtolower($fee['name'] ?? '');

                                // Add descriptions based on fee name
                                if (strpos($feeName, 'base rent') !== false || strpos($feeName, 'monthly rent') !== false) {
                                    $fee['description'] = 'Base monthly rent payment for the rental unit';
                                } elseif (strpos($feeName, 'rent') !== false) {
                                    $fee['description'] = 'Monthly rent payment for the unit';
                                } elseif (strpos($feeName, 'electricity') !== false || strpos($feeName, 'power') !== false || strpos($feeName, 'electric') !== false) {
                                    $fee['description'] = 'Electricity bill charges for the month';
                                } elseif (strpos($feeName, 'gas') !== false || strpos($feeName, 'gas bill') !== false) {
                                    $fee['description'] = 'Gas bill charges for the month';
                                } elseif (strpos($feeName, 'water') !== false || strpos($feeName, 'water bill') !== false) {
                                    $fee['description'] = 'Water bill charges for the month';
                                } elseif (strpos($feeName, 'cleaning') !== false) {
                                    $fee['description'] = 'Cleaning and maintenance charges';
                                } elseif (strpos($feeName, 'maintenance') !== false) {
                                    $fee['description'] = 'Building maintenance and repair charges';
                                } elseif (strpos($feeName, 'late') !== false || strpos($feeName, 'penalty') !== false) {
                                    $fee['description'] = 'Late payment penalty charges';
                                } elseif (strpos($feeName, 'security') !== false || strpos($feeName, 'deposit') !== false) {
                                    $fee['description'] = 'Security deposit or related charges';
                                } elseif (strpos($feeName, 'utility') !== false) {
                                    $fee['description'] = 'Utility service charges (electricity, water, gas)';
                                } elseif (strpos($feeName, 'service') !== false) {
                                    $fee['description'] = 'Additional service charges';
                                } else {
                                    $fee['description'] = 'Additional service or charge';
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $breakdown = [];
                    }
                }

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    // Support both `invoice_type` and legacy `type`
                    'invoice_type' => $invoice->invoice_type ?? $invoice->type,
                    'type' => $invoice->type,
                    'description' => $invoice->description,
                    'amount' => $invoice->amount,
                    'paid_amount' => $invoice->paid_amount ?? 0,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'breakdown' => $breakdown,
                    'tenant_name' => $invoice->tenant ? trim(($invoice->tenant->first_name ?? '') . ' ' . ($invoice->tenant->last_name ?? '')) : 'N/A',
                    'unit_name' => $invoice->unit ? $invoice->unit->name : 'N/A',
                    'property_name' => $invoice->property ? $invoice->property->name : 'N/A',
                ];
            });

        return response()->json([
            'invoices' => $invoices
        ]);
    }

    public function pay(Request $request, $invoiceId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_banking,check,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Log the request data for debugging
        \Log::info('Payment request data:', $request->all());

        try {
            DB::beginTransaction();

            $ownerId = $request->user()->owner->id;
            $invoice = Invoice::where('id', $invoiceId)
                ->where('owner_id', $ownerId)
                ->with(['tenant', 'unit'])
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $paymentAmount = $request->amount;
            $invoiceAmount = $invoice->amount;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $invoiceAmount - $paidAmount;

            if ($paymentAmount > $remainingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed remaining invoice amount'
                ], 400);
            }

            // Format payment method (replace underscores with spaces)
            $paymentMethod = str_replace('_', ' ', $request->payment_method);

            // Create rent payment record
            $rentPayment = RentPayment::create([
                'owner_id' => $ownerId,
                'tenant_id' => $invoice->tenant_id,
                'unit_id' => $invoice->unit_id,
                'invoice_id' => $invoice->id,
                'amount' => $paymentAmount,
                'amount_due' => $invoiceAmount,
                'amount_paid' => $paymentAmount,
                'payment_method' => $paymentMethod,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'payment_date' => now(),
            ]);

            // Update invoice paid amount
            $newPaidAmount = $paidAmount + $paymentAmount;
            $invoice->paid_amount = $newPaidAmount;

            // Update invoice status
            if ($newPaidAmount >= $invoiceAmount) {
                $invoice->status = 'paid';
            } elseif ($newPaidAmount > 0) {
                $invoice->status = 'partial';
            }

            $invoice->save();

            // Create ledger entry for payment
            TenantLedger::create([
                'owner_id' => $ownerId,
                'tenant_id' => $invoice->tenant_id,
                'unit_id' => $invoice->unit_id,
                'transaction_type' => 'rent_payment',
                'credit_amount' => $paymentAmount,
                'debit_amount' => 0,
                'balance' => $this->calculateNewBalance($invoice->tenant_id, $paymentAmount),
                'description' => "Payment for invoice #{$invoice->invoice_number}",
                'payment_status' => 'completed',
                'payment_reference' => $request->reference_number,
                'notes' => $request->notes,
                'transaction_date' => now(),
            ]);

            DB::commit();

            \Log::info('Payment processed successfully', [
                'invoice_id' => $invoice->id,
                'payment_id' => $rentPayment->id,
                'amount' => $paymentAmount,
                'new_balance' => $this->calculateNewBalance($invoice->tenant_id, $paymentAmount)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment' => $rentPayment,
                'invoice' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Invoice payment error: ' . $e->getMessage());
            \Log::error('Invoice payment error stack: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePdf(Request $request, $invoiceId)
    {
        try {
            $user = $request->user();
            $invoice = null;

            // Check if user is owner or tenant
            if ($user->owner) {
                // Owner accessing invoice
                $ownerId = $user->owner->id;
                $invoice = Invoice::where('id', $invoiceId)
                    ->where('owner_id', $ownerId)
                    ->with(['tenant:id,first_name,last_name,mobile,email', 'unit:id,name', 'property:id,name,address'])
                    ->first();
            } elseif ($user->tenant) {
                // Tenant accessing their own invoice
                $tenantId = $user->tenant->id;
                $invoice = Invoice::where('id', $invoiceId)
                    ->where('tenant_id', $tenantId)
                    ->with(['tenant:id,first_name,last_name,mobile,email', 'unit:id,name', 'property:id,name,address'])
                    ->first();
            }

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            // Parse breakdown with detailed descriptions
            $breakdown = [];
            if ($invoice->breakdown) {
                try {
                    $breakdown = json_decode($invoice->breakdown, true) ?? [];

                    // Add descriptions for common fee types
                    foreach ($breakdown as &$fee) {
                        if (!isset($fee['description']) || empty($fee['description'])) {
                            $feeName = strtolower($fee['name'] ?? '');

                            // Add descriptions based on fee name
                            if (strpos($feeName, 'base rent') !== false || strpos($feeName, 'monthly rent') !== false) {
                                $fee['description'] = 'Base monthly rent payment for the rental unit';
                            } elseif (strpos($feeName, 'rent') !== false) {
                                $fee['description'] = 'Monthly rent payment for the unit';
                            } elseif (strpos($feeName, 'electricity') !== false || strpos($feeName, 'power') !== false || strpos($feeName, 'electric') !== false) {
                                $fee['description'] = 'Electricity bill charges for the month';
                            } elseif (strpos($feeName, 'gas') !== false || strpos($feeName, 'gas bill') !== false) {
                                $fee['description'] = 'Gas bill charges for the month';
                            } elseif (strpos($feeName, 'water') !== false || strpos($feeName, 'water bill') !== false) {
                                $fee['description'] = 'Water bill charges for the month';
                            } elseif (strpos($feeName, 'cleaning') !== false) {
                                $fee['description'] = 'Cleaning and maintenance charges';
                            } elseif (strpos($feeName, 'maintenance') !== false) {
                                $fee['description'] = 'Building maintenance and repair charges';
                            } elseif (strpos($feeName, 'late') !== false || strpos($feeName, 'penalty') !== false) {
                                $fee['description'] = 'Late payment penalty charges';
                            } elseif (strpos($feeName, 'security') !== false || strpos($feeName, 'deposit') !== false) {
                                $fee['description'] = 'Security deposit or related charges';
                            } elseif (strpos($feeName, 'utility') !== false) {
                                $fee['description'] = 'Utility service charges (electricity, water, gas)';
                            } elseif (strpos($feeName, 'service') !== false) {
                                $fee['description'] = 'Additional service charges';
                            } else {
                                $fee['description'] = 'Additional service or charge';
                            }
                        }
                    }
                } catch (Exception $e) {
                    $breakdown = [];
                }
            }

            // Prepare data for PDF
            $pdfData = [
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_type' => $invoice->invoice_type,
                    'description' => $invoice->description,
                    'amount' => $invoice->amount,
                    'paid_amount' => $invoice->paid_amount ?? 0,
                    'remaining_amount' => ($invoice->amount - ($invoice->paid_amount ?? 0)),
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'breakdown' => $breakdown,
                ],
                'tenant' => [
                    'name' => $invoice->tenant ? trim(($invoice->tenant->first_name ?? '') . ' ' . ($invoice->tenant->last_name ?? '')) : 'N/A',
                    'phone' => $invoice->tenant->mobile ?? 'N/A',
                    'email' => $invoice->tenant->email ?? 'N/A',
                ],
                'unit' => [
                    'name' => $invoice->unit ? $invoice->unit->name : 'N/A',
                ],
                'property' => [
                    'name' => $invoice->property ? $invoice->property->name : 'N/A',
                    'address' => $invoice->property ? $invoice->property->address : 'N/A',
                    'email' => $invoice->property ? $invoice->property->email : 'sales@samitpark.com',
                    'mobile' => $invoice->property ? $invoice->property->mobile : '9611 677170',
                ],
                'owner' => $user->owner ? [
                    'name' => $user->owner->name ?? 'N/A',
                    'phone' => $user->owner->phone ?? 'N/A',
                    'email' => $user->owner->email ?? 'N/A',
                ] : null,
                'generated_at' => now()->format('Y-m-d H:i:s'),
            ];

            // Generate PDF
            $pdf = PDF::loadView('pdf.invoice', $pdfData);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('dpi', 72);
            $pdf->setOption('image-dpi', 72);

            // Return PDF as response
            return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateNewBalance($tenantId, $paymentAmount)
    {
        $lastLedger = TenantLedger::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->first();

        $currentBalance = $lastLedger ? $lastLedger->balance : 0;
        return $currentBalance - $paymentAmount; // Payment reduces balance
    }
}
