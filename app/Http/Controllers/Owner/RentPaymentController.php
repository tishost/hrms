<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\RentPayment;
use Illuminate\Support\Facades\Auth;

class RentPaymentController extends Controller
{
    public function create(Request $request)
    {
        $ownerId = Auth::user()->owner->id ?? null;
        $invoiceId = $request->query('invoice_id');
        $tenant = null;
        $unit = null;
        $fees = [];
        $previous_due = 0;

        if ($invoiceId) {
            $invoice = \App\Models\Invoice::where('owner_id', $ownerId)->find($invoiceId);
            if ($invoice) {
                $tenant = \App\Models\Tenant::with('unit.charges')->find($invoice->tenant_id);
                $unit = \App\Models\Unit::with('charges')->find($invoice->unit_id);
            }
        }
        // fallback: if not from invoice, pick first tenant/unit
        if (!$tenant) {
            $tenant = \App\Models\Tenant::with('unit.charges')->where('owner_id', $ownerId)->whereNotNull('unit_id')->first();
        }
        if (!$unit && $tenant && $tenant->unit) {
            $unit = $tenant->unit;
        }
        // Fees calculation
        if ($unit) {
            $baseRent = $unit->rent;
            if ($baseRent) {
                $fees[] = ['label' => 'Base Rent', 'amount' => $baseRent];
            }
            foreach ($unit->charges as $charge) {
                $fees[] = ['label' => $charge->label, 'amount' => $charge->amount];
            }
        }
        // Previous due calculation
        if ($tenant && $unit) {
            $previous_due = \App\Models\RentPayment::where('tenant_id', $tenant->id)
                ->where('unit_id', $unit->id)
                ->where('owner_id', $ownerId)
                ->where('status', 'Partial')
                ->sum(\DB::raw('amount_due - amount_paid'));
        }
        return view('owner.rent_payments.create', compact('tenant', 'unit', 'fees', 'previous_due'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'unit_id' => 'required|exists:units,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
        ]);
        \Log::info('Tenant:', [$request->tenant_id]);
        \Log::info('Unit:', [$request->unit_id]);
        $tenant = \App\Models\Tenant::findOrFail($request->tenant_id);
        // Check if tenant already has a unit assigned and matches the selected unit
        if (!$tenant->unit_id || $tenant->unit_id != $request->unit_id) {
            return back()->with('error', 'This tenant is not assigned to the selected unit.');
        }
        $unit = \App\Models\Unit::findOrFail($request->unit_id);
        $ownerId = Auth::user()->owner->id ?? null;

        // Calculate total due (previous due + current rent)
        $previousDue = \App\Models\RentPayment::where('tenant_id', $tenant->id)
            ->where('unit_id', $unit->id)
            ->where('owner_id', $ownerId)
            ->where('status', 'Partial')
            ->sum(\DB::raw('amount_due - amount_paid'));
        $currentDue = $unit->rent;
        $totalDue = $previousDue + $currentDue;

        $amountPaid = $request->amount_paid;
        $status = ($amountPaid >= $totalDue) ? 'Paid' : 'Partial';

        \App\Models\RentPayment::create([
            'owner_id' => $ownerId,
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'amount_due' => $totalDue,
            'amount_paid' => $amountPaid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'status' => $status,
        ]);

        // Update related invoice status
        $invoice = \App\Models\Invoice::where('owner_id', $ownerId)
            ->where('tenant_id', $tenant->id)
            ->where('unit_id', $unit->id)
            ->where('status', '!=', 'Paid')
            ->orderByDesc('id')
            ->first();
        if ($invoice) {
            $invoice->status = $status; // 'Paid' or 'Partial'
            $invoice->paid_date = now();
            $invoice->save();
        }

        return redirect()->route('owner.rent_payments.create')->with('success', 'Rent payment recorded successfully.');
    }

    public function getFeesAndDues(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $unitId = $request->input('unit_id');
        $ownerId = Auth::user()->owner->id ?? null;

        $unit = \App\Models\Unit::find($unitId);
        $tenant = \App\Models\Tenant::find($tenantId);
        $fees = [];
        $baseRent = $unit ? $unit->rent : 0;
        if ($baseRent) {
            $fees[] = ['label' => 'Base Rent', 'amount' => $baseRent];
        }
        // Add extra/unit charges if any
        $unitCharges = $unit ? $unit->charges : [];
        foreach ($unitCharges as $charge) {
            $fees[] = ['label' => $charge->label, 'amount' => $charge->amount];
        }
        // Calculate previous dues
        $previousDue = \App\Models\RentPayment::where('tenant_id', $tenantId)
            ->where('unit_id', $unitId)
            ->where('owner_id', $ownerId)
            ->where('status', 'Partial')
            ->sum(\DB::raw('amount_due - amount_paid'));
        return response()->json([
            'fees' => $fees,
            'previous_due' => $previousDue,
        ]);
    }
}
