<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
