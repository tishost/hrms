<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Invoice;
use App\Models\RentPayment;
use App\Models\TenantLedger;
use App\Http\Controllers\TenantLedgerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * View invoice details
     */
    public function viewInvoice($billingId)
    {
        $ownerId = Auth::user()->owner_id ?? Auth::id();

        $invoice = Billing::with(['subscription.plan', 'owner', 'paymentMethod'])
            ->where('id', $billingId)
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        return view('owner.invoice.view', compact('invoice'));
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice($billingId)
    {
        $ownerId = Auth::user()->owner_id ?? Auth::id();

        $invoice = Billing::with(['subscription.plan', 'owner', 'paymentMethod'])
            ->where('id', $billingId)
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        // For now, just redirect to view page
        // In future, you can implement PDF generation here
        return redirect()->route('owner.invoice.view', $billingId)
            ->with('info', 'PDF download feature will be implemented soon.');
    }

    /**
     * Display a listing of tenant invoices
     */
    public function index()
    {
        $owner = auth()->user()->owner;
        if (!$owner) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get all invoices for this owner
        $invoices = Invoice::where('owner_id', $owner->id)
            ->with(['tenant:id,first_name,last_name,mobile', 'unit:id,name,property_id', 'unit.property:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate summary statistics
        $totalInvoices = $invoices->total();
        $totalAmount = Invoice::where('owner_id', $owner->id)->sum('amount');
        $totalPaid = Invoice::where('owner_id', $owner->id)->where('status', 'Paid')->sum('paid_amount');
        $totalDue = Invoice::where('owner_id', $owner->id)->where('status', 'Unpaid')->sum('amount');

        return view('owner.invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'totalPaid', 'totalDue'));
    }

    /**
     * Display the specified tenant invoice
     */
    public function show(Invoice $invoice)
    {
        $owner = auth()->user()->owner;
        if (!$owner) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Check if invoice belongs to this owner
        if ($invoice->owner_id !== $owner->id) {
            return redirect()->route('owner.invoices.index')->with('error', 'Unauthorized access.');
        }

        // Load related data
        $invoice->load(['tenant', 'unit.property', 'owner']);

        return view('owner.invoices.show', compact('invoice'));
    }

    /**
     * Process payment for tenant invoice (same as API)
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $owner = auth()->user()->owner;
        if (!$owner) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Check if invoice belongs to this owner
        if ($invoice->owner_id !== $owner->id) {
            return redirect()->route('owner.invoices.index')->with('error', 'Unauthorized access.');
        }

        // Validate payment data (same as API)
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_banking,check,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $paymentAmount = $request->amount;
            $invoiceAmount = $invoice->amount;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $invoiceAmount - $paidAmount;

            // Check if payment amount exceeds remaining amount
            if ($paymentAmount > $remainingAmount) {
                return redirect()->back()
                    ->with('error', 'Payment amount cannot exceed remaining invoice amount (৳' . number_format($remainingAmount, 2) . ')')
                    ->withInput();
            }

            // Format payment method (replace underscores with spaces)
            $paymentMethod = str_replace('_', ' ', $request->payment_method);

            // Create rent payment record (same as API)
            $rentPayment = RentPayment::create([
                'owner_id' => $owner->id,
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

            // Update invoice paid amount (cumulative)
            $newPaidAmount = $paidAmount + $paymentAmount;
            $invoice->paid_amount = $newPaidAmount;

            // Update invoice status (same as API)
            if ($newPaidAmount >= $invoiceAmount) {
                $invoice->status = 'Paid';
            } elseif ($newPaidAmount > 0) {
                $invoice->status = 'Partial';
            }

            $invoice->save();

            // Create ledger entry for payment (same as API)
            TenantLedger::create([
                'owner_id' => $owner->id,
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
                'reference_type' => 'invoice',
                'reference_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ]);

            DB::commit();

            return redirect()->route('owner.invoices.show', $invoice->id)
                ->with('success', 'Payment of ৳' . number_format($paymentAmount, 2) . ' recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process payment. Please try again.');
        }
    }

    /**
     * Calculate new balance for tenant (same as API)
     */
    private function calculateNewBalance($tenantId, $paymentAmount)
    {
        $lastLedger = TenantLedger::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->first();

        $currentBalance = $lastLedger ? $lastLedger->balance : 0;
        return $currentBalance - $paymentAmount; // Payment reduces balance
    }
}
