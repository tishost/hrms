<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\PaymentMethod;
use App\Models\OwnerSubscription;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function showPaymentForm($invoiceId)
    {
        $billing = Billing::with(['owner', 'subscription.plan', 'paymentMethod'])
            ->findOrFail($invoiceId);

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('admin.payments.form', compact('billing', 'paymentMethods'));
    }

    public function processPayment(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'transaction_id' => 'required|string|max:100',
            'payment_date' => 'required|date'
        ]);

        $billing = Billing::findOrFail($invoiceId);
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Calculate transaction fee
        $transactionFee = ($billing->amount * $paymentMethod->transaction_fee) / 100;
        $netAmount = $billing->amount + $transactionFee;

        // Update billing record
        $billing->update([
            'status' => 'paid',
            'payment_method_id' => $request->payment_method_id,
            'transaction_id' => $request->transaction_id,
            'paid_date' => $request->payment_date,
            'transaction_fee' => $transactionFee,
            'net_amount' => $netAmount
        ]);

        // Activate subscription if it's pending
        if ($billing->subscription->status === 'pending') {
            $billing->subscription->activateAfterPayment();
        }

        return redirect()->route('admin.billing.index')
            ->with('success', 'Payment processed successfully! Invoice #' . $billing->invoice_number . ' has been paid.');
    }

    public function markAsPaid($invoiceId)
    {
        $billing = Billing::findOrFail($invoiceId);

        $billing->update([
            'status' => 'paid',
            'paid_date' => now()
        ]);

        // Activate subscription if it's pending
        if ($billing->subscription->status === 'pending') {
            $billing->subscription->activateAfterPayment();
        }

        return redirect()->route('admin.billing.index')
            ->with('success', 'Invoice marked as paid successfully!');
    }
}
