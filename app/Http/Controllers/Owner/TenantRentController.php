<?php

namespace App\Http\Controllers\Owner;
use App\Models\TenantRent;
use App\Models\Tenant;
use App\Models\Unit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Owner;
use App\Models\Invoice;
use App\Http\Controllers\TenantLedgerController;

class TenantRentController extends Controller
{


public function create($tenantId)
{
    $tenant = Tenant::findOrFail($tenantId);
    $ownerId = auth()->user()->owner->id;
    $buildings = Property::where('owner_id', $ownerId)->get();
    return view('owner.rents.create', compact('tenant', 'buildings'));
}

public function store(Request $request, $tenantId)
{
    $validated = $request->validate([
        'unit_id' => 'required|exists:units,id',
        'start_month' => 'required|date',
        'due_day' => 'required|integer|min:1|max:31',
        'advance_amount' => 'nullable|numeric',
        'frequency' => 'required|in:monthly,quarterly,yearly',
        'fees' => 'nullable|array',
    ]);

    $validated['start_month'] .= '-01'; // make it '2025-07-01'
    $validated['tenant_id'] = $tenantId;
    $validated['owner_id'] = auth()->user()->owner->id;

    $tenantRent = TenantRent::create([
        ...$validated,
        'remarks' => $request->remarks,
    ]);

    // Update the assigned unit's tenant_id and status
    $unit = \App\Models\Unit::find($validated['unit_id']);
    $unit->tenant_id = $tenantId;
    $unit->status = 'occupied';
    $unit->save();

    // Generate Advance Invoice if advance_amount > 0
    if (!empty($validated['advance_amount']) && $validated['advance_amount'] > 0) {
        $advanceInvoice = Invoice::create([
            'invoice_number' => 'ADV-' . time() . rand(100,999),
            'owner_id' => $validated['owner_id'],
            'tenant_id' => $tenantId,
            'unit_id' => $validated['unit_id'],
            'type' => 'advance',
            'amount' => $validated['advance_amount'],
            'status' => 'Unpaid',
            'issue_date' => now(),
            'due_date' => now(),
            'notes' => 'Advance payment on unit assignment',
            'breakdown' => json_encode(['advance' => $validated['advance_amount']]),
            'rent_month' => date('Y-m', strtotime($validated['start_month'])),
        ]);
        // Ledger entry for advance
        TenantLedgerController::log([
            'tenant_id'        => $tenantId,
            'unit_id'          => $unit->id,
            'owner_id'         => $validated['owner_id'],
            'transaction_type' => 'security_deposit',
            'reference_type'   => 'invoice',
            'reference_id'     => $advanceInvoice->id,
            'invoice_number'   => $advanceInvoice->invoice_number,
            'debit_amount'     => $validated['advance_amount'],
            'credit_amount'    => 0,
            'description'      => 'Advance payment invoice generated',
            'transaction_date' => now(),
        ]);
    }

    // Generate First Month Rent Invoice
    $rentAmount = $unit->rent + ($unit->charges ? $unit->charges->sum('amount') : 0);
    $rentInvoice = Invoice::create([
        'invoice_number' => 'RENT-' . time() . rand(100,999),
        'owner_id' => $validated['owner_id'],
        'tenant_id' => $tenantId,
        'unit_id' => $validated['unit_id'],
        'type' => 'rent',
        'amount' => $rentAmount,
        'status' => 'Unpaid',
        'issue_date' => now(),
        'due_date' => now()->addDays(7),
        'notes' => 'First month rent invoice on unit assignment',
        'breakdown' => json_encode([
            'base_rent' => $unit->rent,
            'charges' => $unit->charges ? $unit->charges->toArray() : [],
        ]),
        'rent_month' => date('Y-m', strtotime($validated['start_month'])),
    ]);
    // Ledger entry for first rent invoice
    TenantLedgerController::log([
        'tenant_id'        => $tenantId,
        'unit_id'          => $unit->id,
        'owner_id'         => $validated['owner_id'],
        'transaction_type' => 'rent_due',
        'reference_type'   => 'invoice',
        'reference_id'     => $rentInvoice->id,
        'invoice_number'   => $rentInvoice->invoice_number,
        'debit_amount'     => $rentAmount,
        'credit_amount'    => 0,
        'description'      => 'First month rent invoice generated',
        'transaction_date' => now(),
    ]);

    return redirect()->route('owner.tenants.index')->with('success', 'Rent assigned and invoices generated successfully.');
}
}
