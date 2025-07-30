<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\CheckoutRecord;
use App\Models\RentPayment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TenantLedgerController;

class CheckoutController extends Controller
{
    /**
     * Store a new checkout record
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            \Log::info('Checkout request received', [
                'user_id' => $user->id,
                'owner_id' => $user->owner_id,
                'request_data' => $request->all()
            ]);

            // Validate request
            $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'unit_id' => 'required|exists:units,id',
                'property_id' => 'required|exists:properties,id',
                'checkout_date' => 'required|date',
                'checkout_reason' => 'required|string|max:255',
                'advance_amount' => 'nullable|numeric|min:0',
                'outstanding_dues' => 'nullable|numeric|min:0',
                'utility_bills' => 'nullable|numeric|min:0',
                'cleaning_charges' => 'nullable|numeric|min:0',
                'damage_charges' => 'nullable|numeric|min:0',
                'handover_date' => 'required|date',
                'property_condition' => 'nullable|string',
                'additional_note' => 'nullable|string',
                'property_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'payment_reference' => 'nullable|string|max:255',
                'payment_method' => 'nullable|string|max:255',
            ]);

            // Verify tenant belongs to the owner
            $tenant = Tenant::whereHas('unit.property', function($query) use ($user) {
                $query->where('owner_id', $user->owner_id);
            })->find($request->tenant_id);

            if (!$tenant) {
                return response()->json([
                    'error' => 'Tenant not found or unauthorized access'
                ], 404);
            }

            // Calculate outstanding dues
            $outstandingDues = RentPayment::where('tenant_id', $request->tenant_id)
                ->where('unit_id', $request->unit_id)
                ->where('owner_id', $user->owner_id)
                ->where('status', 'Partial')
                ->sum(DB::raw('amount_due - amount_paid'));

            // Calculate unpaid invoices
            $unpaidInvoices = Invoice::where('tenant_id', $request->tenant_id)
                ->where('unit_id', $request->unit_id)
                ->where('owner_id', $user->owner_id)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->sum('amount');

            $totalOutstanding = $outstandingDues + $unpaidInvoices;

            // Calculate final settlement
            $advanceAmount = $request->advance_amount ?? 0;
            $utilityBills = $request->utility_bills ?? 0;
            $cleaningCharges = $request->cleaning_charges ?? 0;
            $damageCharges = $request->damage_charges ?? 0;
            $outstandingDuesInput = max(0, $request->outstanding_dues ?? 0); // Ensure non-negative value

            $totalDeductions = $outstandingDuesInput + $utilityBills + $cleaningCharges + $damageCharges;
            $depositReturned = $advanceAmount - $totalDeductions;
            $finalSettlement = $depositReturned;

            // Handle property image upload
            $propertyImagePath = null;
            if ($request->hasFile('property_image')) {
                $file = $request->file('property_image');
                $fileName = 'checkout_' . time() . '_' . $file->getClientOriginalName();
                $propertyImagePath = $file->storeAs('checkout_images', $fileName, 'public');
            }

            // Create checkout record
            $checkout = CheckoutRecord::create([
                'tenant_id' => $request->tenant_id,
                'unit_id' => $request->unit_id,
                'owner_id' => $user->owner_id,
                'check_out_date' => $request->checkout_date,
                'security_deposit' => $advanceAmount,
                'deposit_returned' => $depositReturned,
                'outstanding_dues' => $outstandingDuesInput,
                'utility_bills' => $utilityBills,
                'cleaning_charges' => $cleaningCharges,
                'other_charges' => $damageCharges,
                'final_settlement_amount' => $finalSettlement,
                'settlement_status' => $finalSettlement >= 0 ? 'completed' : 'partial',
                'check_out_reason' => $request->checkout_reason,
                'handover_date' => $request->handover_date,
                'handover_condition' => $request->property_condition,
                'notes' => $request->additional_note,
                'property_image' => $propertyImagePath,
                'payment_reference' => $request->payment_reference,
                'payment_method' => $request->payment_method,
            ]);

            // Ledger entry for checkout settlement
            TenantLedgerController::log([
                'tenant_id'        => $request->tenant_id,
                'unit_id'          => $request->unit_id,
                'owner_id'         => $user->owner_id,
                'transaction_type' => 'checkout_settlement',
                'reference_type'   => 'checkout',
                'reference_id'     => $checkout->id,
                'invoice_number'   => null,
                'debit_amount'     => $finalSettlement < 0 ? abs($finalSettlement) : 0,
                'credit_amount'    => $finalSettlement > 0 ? $finalSettlement : 0,
                'description'      => 'Tenant checkout settlement',
                'transaction_date' => $request->checkout_date,
            ]);

            // Update tenant status
            $tenant->update([
                'status' => 'checked_out',
                'check_out_date' => $request->checkout_date,
                'security_deposit' => $advanceAmount,
                'cleaning_charges' => $cleaningCharges,
                'other_charges' => $damageCharges,
                'check_out_reason' => $request->checkout_reason,
                'handover_date' => $request->handover_date,
                'handover_condition' => $request->property_condition,
            ]);

            // Update unit status
            $unit = Unit::find($request->unit_id);
            $unit->update([
                'status' => 'vacant',
                'tenant_id' => null,
            ]);

            // Generate invoices for checkout settlement
            $this->generateCheckoutInvoices($request, $user, $checkout, $finalSettlement);

            // Mark all unpaid invoices as paid with checkout settlement payment method
            $unpaidInvoices = Invoice::where('tenant_id', $request->tenant_id)
                ->where('unit_id', $request->unit_id)
                ->where('owner_id', $user->owner_id)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->get();

            foreach ($unpaidInvoices as $invoice) {
                // Add ledger entry for each paid invoice
                TenantLedgerController::log([
                    'tenant_id'        => $request->tenant_id,
                    'unit_id'          => $request->unit_id,
                    'owner_id'         => $user->owner_id,
                    'transaction_type' => 'invoice_payment',
                    'reference_type'   => 'invoice',
                    'reference_id'     => $invoice->id,
                    'invoice_number'   => $invoice->invoice_number,
                    'debit_amount'     => 0,
                    'credit_amount'    => $invoice->amount,
                    'description'      => 'Payment for invoice: ' . $invoice->invoice_number,
                    'transaction_date' => $request->checkout_date,
                ]);
            }

            // Update all unpaid invoices status
            Invoice::where('tenant_id', $request->tenant_id)
                ->where('unit_id', $request->unit_id)
                ->where('owner_id', $user->owner_id)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->update([
                    'status' => 'Paid',
                    'paid_date' => now(),
                    'payment_method' => 'Checkout Settlement'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout completed successfully',
                'checkout' => [
                    'id' => $checkout->id,
                    'tenant_name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'unit_name' => $unit->name,
                    'checkout_date' => $checkout->check_out_date,
                    'final_settlement_amount' => $checkout->final_settlement_amount,
                    'settlement_status' => $checkout->settlement_status,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in checkout: ' . $e->getMessage());
            return response()->json([
                'error' => 'Validation error: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error processing checkout: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to process checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get checkout records for the authenticated owner
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $checkouts = CheckoutRecord::with(['tenant', 'unit.property'])
                ->where('owner_id', $user->owner_id)
                ->orderByDesc('created_at')
                ->get();

            $checkoutData = $checkouts->map(function ($checkout) {
                return [
                    'id' => $checkout->id,
                    'tenant_name' => $checkout->tenant->first_name . ' ' . $checkout->tenant->last_name,
                    'unit_name' => $checkout->unit->name,
                    'property_name' => $checkout->unit->property->name,
                    'checkout_date' => $checkout->check_out_date,
                    'handover_date' => $checkout->handover_date,
                    'final_settlement_amount' => $checkout->final_settlement_amount,
                    'settlement_status' => $checkout->settlement_status,
                    'checkout_reason' => $checkout->check_out_reason,
                    'created_at' => $checkout->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'checkouts' => $checkoutData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching checkouts: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch checkouts'
            ], 500);
        }
    }

    /**
     * Get a specific checkout record
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $checkout = CheckoutRecord::with(['tenant', 'unit.property'])
                ->where('owner_id', $user->owner_id)
                ->find($id);

            if (!$checkout) {
                return response()->json([
                    'error' => 'Checkout record not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'checkout' => [
                    'id' => $checkout->id,
                    // Flat fields for easy access
                    'tenant_name' => $checkout->tenant->first_name . ' ' . $checkout->tenant->last_name,
                    'property_name' => $checkout->unit->property->name,
                    'unit_name' => $checkout->unit->name,
                    // Nested objects for detailed info
                    'tenant' => [
                        'id' => $checkout->tenant->id,
                        'name' => $checkout->tenant->first_name . ' ' . $checkout->tenant->last_name,
                        'mobile' => $checkout->tenant->mobile,
                        'email' => $checkout->tenant->email,
                    ],
                    'unit' => [
                        'id' => $checkout->unit->id,
                        'name' => $checkout->unit->name,
                        'unit_number' => $checkout->unit->unit_number,
                        'unit_type' => $checkout->unit->unit_type,
                    ],
                    'property' => [
                        'id' => $checkout->unit->property->id,
                        'name' => $checkout->unit->property->name,
                        'address' => $checkout->unit->property->address,
                    ],
                    'checkout_date' => $checkout->check_out_date,
                    'handover_date' => $checkout->handover_date,
                    'checkout_reason' => $checkout->check_out_reason,
                    'advance_amount' => $checkout->security_deposit,
                    'outstanding_dues' => $checkout->outstanding_dues,
                    'utility_bills' => $checkout->utility_bills,
                    'cleaning_charges' => $checkout->cleaning_charges,
                    'damage_charges' => $checkout->other_charges,
                    'deposit_returned' => $checkout->deposit_returned,
                    'final_settlement_amount' => $checkout->final_settlement_amount,
                    'settlement_status' => $checkout->settlement_status,
                    'property_condition' => $checkout->handover_condition,
                    'additional_note' => $checkout->notes,
                    'property_image' => $checkout->property_image ? Storage::url($checkout->property_image) : null,
                    'created_at' => $checkout->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching checkout details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch checkout details'
            ], 500);
        }
    }

    /**
     * Generate invoices for checkout settlement
     */
    private function generateCheckoutInvoices($request, $user, $checkout, $finalSettlement)
    {
        try {
            $tenant = Tenant::find($request->tenant_id);
            $unit = Unit::find($request->unit_id);
            $property = $unit->property;

            // 1. Generate refund invoice if tenant gets refund
            if ($finalSettlement > 0) {
                $refundInvoice = Invoice::create([
                    'tenant_id' => $request->tenant_id,
                    'unit_id' => $request->unit_id,
                    'owner_id' => $user->owner_id,
                    'invoice_number' => 'REF-' . date('Ymd') . '-' . $checkout->id,
                    'invoice_type' => 'refund',
                    'amount' => $finalSettlement,
                    'issue_date' => $request->checkout_date,
                    'due_date' => $request->checkout_date,
                    'status' => 'Paid',
                    'paid_date' => $request->checkout_date,
                    'payment_method' => $request->payment_method ?? 'Checkout Settlement',
                    'description' => 'Refund for checkout settlement - ' . $tenant->first_name . ' ' . $tenant->last_name,
                    'notes' => 'Auto-generated refund invoice for checkout settlement',
                    'rent_month' => date('Y-m'),
                ]);

                \Log::info("Generated refund invoice: " . $refundInvoice->invoice_number . " for amount: " . $finalSettlement);
            }

            // 2. Generate single adjustment invoice for all charges and refunds
            $adjustmentAmount = 0;
            $adjustmentDescription = [];

            // Add cleaning charges
            if ($request->cleaning_charges > 0) {
                $adjustmentAmount += $request->cleaning_charges;
                $adjustmentDescription[] = 'Cleaning: ৳' . $request->cleaning_charges;
            }

            // Add damage charges
            if ($request->damage_charges > 0) {
                $adjustmentAmount += $request->damage_charges;
                $adjustmentDescription[] = 'Damage: ৳' . $request->damage_charges;
            }

            // Subtract refund amount (negative for refund)
            if ($finalSettlement > 0) {
                $adjustmentAmount -= $finalSettlement;
                $adjustmentDescription[] = 'Refund: ৳' . $finalSettlement;
            }

            // Create single adjustment invoice if there are any charges or refunds
            if ($adjustmentAmount != 0) {
                $adjustmentInvoice = Invoice::create([
                    'tenant_id' => $request->tenant_id,
                    'unit_id' => $request->unit_id,
                    'owner_id' => $user->owner_id,
                    'invoice_number' => 'ADJUST-' . date('Ymd') . '-' . $checkout->id,
                    'invoice_type' => 'checkout_adjustment',
                    'amount' => abs($adjustmentAmount),
                    'issue_date' => $request->checkout_date,
                    'due_date' => $request->checkout_date,
                    'status' => 'Paid',
                    'paid_date' => $request->checkout_date,
                    'payment_method' => $request->payment_method ?? 'Checkout Settlement',
                    'description' => 'Checkout settlement adjustment - ' . implode(', ', $adjustmentDescription),
                    'notes' => 'Auto-generated adjustment invoice for checkout settlement',
                    'rent_month' => date('Y-m'),
                ]);

                \Log::info("Generated adjustment invoice: " . $adjustmentInvoice->invoice_number . " for amount: " . $adjustmentAmount . " with breakdown: " . implode(', ', $adjustmentDescription));

                // Add ledger entry for adjustment invoice
                TenantLedgerController::log([
                    'tenant_id'        => $request->tenant_id,
                    'unit_id'          => $request->unit_id,
                    'owner_id'         => $user->owner_id,
                    'transaction_type' => 'checkout_adjustment',
                    'reference_type'   => 'invoice',
                    'reference_id'     => $adjustmentInvoice->id,
                    'invoice_number'   => $adjustmentInvoice->invoice_number,
                    'debit_amount'     => $adjustmentAmount > 0 ? abs($adjustmentAmount) : 0,
                    'credit_amount'    => $adjustmentAmount < 0 ? abs($adjustmentAmount) : 0,
                    'description'      => 'Checkout settlement adjustment - ' . implode(', ', $adjustmentDescription),
                    'transaction_date' => $request->checkout_date,
                ]);
            }

            // Add ledger entries for individual charges (for detailed tracking)
            if ($request->cleaning_charges > 0) {
                TenantLedgerController::log([
                    'tenant_id'        => $request->tenant_id,
                    'unit_id'          => $request->unit_id,
                    'owner_id'         => $user->owner_id,
                    'transaction_type' => 'cleaning_charges',
                    'reference_type'   => 'checkout',
                    'reference_id'     => $checkout->id,
                    'invoice_number'   => null,
                    'debit_amount'     => $request->cleaning_charges,
                    'credit_amount'    => 0,
                    'description'      => 'Cleaning charges for checkout',
                    'transaction_date' => $request->checkout_date,
                ]);
            }

            if ($request->damage_charges > 0) {
                TenantLedgerController::log([
                    'tenant_id'        => $request->tenant_id,
                    'unit_id'          => $request->unit_id,
                    'owner_id'         => $user->owner_id,
                    'transaction_type' => 'damage_charges',
                    'reference_type'   => 'checkout',
                    'reference_id'     => $checkout->id,
                    'invoice_number'   => null,
                    'debit_amount'     => $request->damage_charges,
                    'credit_amount'    => 0,
                    'description'      => 'Damage/Others charges for checkout',
                    'transaction_date' => $request->checkout_date,
                ]);
            }

            if ($finalSettlement > 0) {
                TenantLedgerController::log([
                    'tenant_id'        => $request->tenant_id,
                    'unit_id'          => $request->unit_id,
                    'owner_id'         => $user->owner_id,
                    'transaction_type' => 'refund_payment',
                    'reference_type'   => 'checkout',
                    'reference_id'     => $checkout->id,
                    'invoice_number'   => null,
                    'debit_amount'     => 0,
                    'credit_amount'    => $finalSettlement,
                    'description'      => 'Refund payment for checkout settlement',
                    'transaction_date' => $request->checkout_date,
                ]);
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('Error generating checkout invoices: ' . $e->getMessage());
            throw $e;
        }
    }
}
