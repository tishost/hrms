<?php

namespace App\Http\Controllers\Owner\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function downloadInvoice($billingId)
    {
        $billing = Billing::with(['subscription.plan', 'subscription', 'owner', 'paymentMethod'])
            ->where('id', $billingId)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $data = [
            'billing' => $billing,
            'owner' => $billing->owner,
            'plan' => $billing->subscription->plan,
            'payment_method' => $billing->paymentMethod,
            'invoice_number' => $billing->invoice_number,
            'amount' => $billing->amount,
            'paid_date' => $billing->paid_date,
            'status' => $billing->status,
            'transaction_id' => $billing->transaction_id,
            'created_at' => $billing->created_at,
            'company_name' => 'HRMS System',
            'company_address' => 'Dhaka, Bangladesh',
            'company_phone' => '+880 1234567890',
            'company_email' => 'info@hrms.com'
        ];

                $pdf = PDF::loadView('owner.subscription.invoice', $data);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Noto Sans Bengali'
        ]);

        return $pdf->download('invoice-' . $billing->invoice_number . '.pdf');
    }

    public function viewInvoice($billingId)
    {
        $billing = Billing::with(['subscription.plan', 'subscription', 'owner', 'paymentMethod'])
            ->where('id', $billingId)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $data = [
            'billing' => $billing,
            'owner' => $billing->owner,
            'plan' => $billing->subscription->plan,
            'payment_method' => $billing->paymentMethod,
            'invoice_number' => $billing->invoice_number,
            'amount' => $billing->amount,
            'paid_date' => $billing->paid_date,
            'status' => $billing->status,
            'transaction_id' => $billing->transaction_id,
            'created_at' => $billing->created_at,
            'company_name' => 'HRMS System',
            'company_address' => 'Dhaka, Bangladesh',
            'company_phone' => '+880 1234567890',
            'company_email' => 'info@hrms.com'
        ];

                $pdf = PDF::loadView('owner.subscription.invoice', $data);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Noto Sans Bengali'
        ]);

        return $pdf->stream('invoice-' . $billing->invoice_number . '.pdf');
    }
}
