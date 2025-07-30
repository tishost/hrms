<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\RentPayment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all rent payments for properties owned by this user
        $invoices = RentPayment::whereHas('tenant.property', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->with(['tenant', 'tenant.property'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('owner.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $invoice = RentPayment::whereHas('tenant.property', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->with(['tenant', 'tenant.property', 'tenant.unit'])
        ->findOrFail($id);

        return view('owner.invoices.show', compact('invoice'));
    }
}
