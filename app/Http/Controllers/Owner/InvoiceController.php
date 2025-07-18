<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::user()->owner->id ?? null;
        $query = Invoice::with(['tenant', 'unit'])
            ->where('owner_id', $ownerId)
            ->where('status', 'Unpaid');

        if ($request->filled('invoice')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice . '%');
        }
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $invoices = $query->orderByDesc('issue_date')->get();
        $tenants = Tenant::where('owner_id', $ownerId)->get();
        $units = Unit::whereHas('property', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->get();

        return view('owner.invoices.index', compact('invoices', 'tenants', 'units'));
    }
    public function show(\App\Models\Invoice $invoice)
    {
        return view('owner.invoices.show', compact('invoice'));
    }

    public function pdf(\App\Models\Invoice $invoice)
    {
        $pdf = \PDF::loadView('owner.invoices.pdf', compact('invoice'));
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
