<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\CheckoutRecord;
use App\Models\RentPayment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TenantLedgerController;

class CheckoutController extends Controller
{
    public function index()
    {
        $ownerId = Auth::user()->owner->id;
        $checkouts = CheckoutRecord::with(['tenant', 'unit'])
            ->where('owner_id', $ownerId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('owner.checkouts.index', compact('checkouts'));
    }

    public function showCheckoutForm($tenantId)
    {
        $tenant = Tenant::with(['unit', 'unit.charges'])->findOrFail($tenantId);
        $ownerId = Auth::user()->owner->id;

        // Check if tenant belongs to this owner
        if ($tenant->owner_id != $ownerId) {
            return back()->with('error', 'Unauthorized access.');
        }

        // Calculate outstanding dues
        $outstandingDues = RentPayment::where('tenant_id', $tenantId)
            ->where('unit_id', $tenant->unit_id)
            ->where('owner_id', $ownerId)
            ->where('status', 'Partial')
            ->sum(DB::raw('amount_due - amount_paid'));

        // Calculate unpaid invoices
        $unpaidInvoices = Invoice::where('tenant_id', $tenantId)
            ->where('unit_id', $tenant->unit_id)
            ->where('owner_id', $ownerId)
            ->whereIn('status', ['Unpaid', 'Partial'])
            ->sum('amount');

        $totalOutstanding = $outstandingDues + $unpaidInvoices;

        return view('owner.checkouts.create', compact('tenant', 'totalOutstanding'));
    }

    public function processCheckout(Request $request, $tenantId)
    {
        $request->validate([
            'check_out_date' => 'required|date',
            'check_out_reason' => 'required|string',
            'security_deposit' => 'required|numeric|min:0',
            'utility_bills' => 'required|numeric|min:0',
            'cleaning_charges' => 'required|numeric|min:0',
            'other_charges' => 'required|numeric|min:0',
            'handover_date' => 'required|date',
            'handover_condition' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $tenant = Tenant::findOrFail($tenantId);
        $ownerId = Auth::user()->owner->id;

        // Calculate final settlement
        $outstandingDues = RentPayment::where('tenant_id', $tenantId)
            ->where('unit_id', $tenant->unit_id)
            ->where('owner_id', $ownerId)
            ->where('status', 'Partial')
            ->sum(DB::raw('amount_due - amount_paid'));

        $unpaidInvoices = Invoice::where('tenant_id', $tenantId)
            ->where('unit_id', $tenant->unit_id)
            ->where('owner_id', $ownerId)
            ->whereIn('status', ['Unpaid', 'Partial'])
            ->sum('amount');

        $totalDeductions = $outstandingDues + $unpaidInvoices + $request->utility_bills + $request->cleaning_charges + $request->other_charges;
        $depositReturned = $request->security_deposit - $totalDeductions;
        $finalSettlement = $depositReturned;

        // Create checkout record
        $checkout = CheckoutRecord::create([
            'tenant_id' => $tenantId,
            'unit_id' => $tenant->unit_id,
            'owner_id' => $ownerId,
            'check_out_date' => $request->check_out_date,
            'security_deposit' => $request->security_deposit,
            'deposit_returned' => $depositReturned,
            'outstanding_dues' => $outstandingDues + $unpaidInvoices,
            'utility_bills' => $request->utility_bills,
            'cleaning_charges' => $request->cleaning_charges,
            'other_charges' => $request->other_charges,
            'final_settlement_amount' => $finalSettlement,
            'settlement_status' => $finalSettlement >= 0 ? 'completed' : 'partial',
            'check_out_reason' => $request->check_out_reason,
            'handover_date' => $request->handover_date,
            'handover_condition' => $request->handover_condition,
            'notes' => $request->notes,
        ]);

        // Ledger entry for checkout settlement
        TenantLedgerController::log([
            'tenant_id'        => $tenantId,
            'unit_id'          => $tenant->unit_id,
            'owner_id'         => $ownerId,
            'transaction_type' => 'checkout_settlement',
            'reference_type'   => 'checkout',
            'reference_id'     => $checkout->id,
            'invoice_number'   => null,
            'debit_amount'     => $finalSettlement < 0 ? abs($finalSettlement) : 0,
            'credit_amount'    => $finalSettlement > 0 ? $finalSettlement : 0,
            'description'      => 'Tenant checkout settlement',
            'transaction_date' => $request->check_out_date,
        ]);

        // Update tenant status
        $tenant->update([
            'status' => 'checked_out',
            'check_out_date' => $request->check_out_date,
            'security_deposit' => $request->security_deposit,
            'cleaning_charges' => $request->cleaning_charges,
            'other_charges' => $request->other_charges,
            'check_out_reason' => $request->check_out_reason,
            'handover_date' => $request->handover_date,
            'handover_condition' => $request->handover_condition,
        ]);

        // Update unit status
        $unit = Unit::find($tenant->unit_id);
        $unit->update([
            'status' => 'vacant',
            'tenant_id' => null,
        ]);

        // Mark all unpaid invoices as paid
        Invoice::where('tenant_id', $tenantId)
            ->where('unit_id', $tenant->unit_id)
            ->where('owner_id', $ownerId)
            ->whereIn('status', ['Unpaid', 'Partial'])
            ->update(['status' => 'Paid', 'paid_date' => now()]);

        return redirect()->route('owner.checkouts.show', $checkout->id)
            ->with('success', 'Tenant checked out successfully!');
    }

    public function show($checkoutId)
    {
        $ownerId = Auth::user()->owner->id;
        $checkout = CheckoutRecord::with(['tenant', 'unit'])
            ->where('owner_id', $ownerId)
            ->findOrFail($checkoutId);

        return view('owner.checkouts.show', compact('checkout'));
    }

    public function generateInvoice($checkoutId)
    {
        $ownerId = Auth::user()->owner->id;
        $checkout = CheckoutRecord::with(['tenant', 'unit'])
            ->where('owner_id', $ownerId)
            ->findOrFail($checkoutId);

        return view('owner.checkouts.invoice', compact('checkout'));
    }
}
