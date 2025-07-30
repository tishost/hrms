<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\OwnerSubscription;
use App\Models\Billing;
use App\Models\PaymentMethod;
use App\Services\BkashTokenizedService;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function currentPlan()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription ? $subscription->plan : null;

        // Get usage statistics
        $propertiesCount = $user->properties()->count();
        $unitsCount = $user->units()->count();
        $tenantsCount = $user->tenants()->count();

        return view('owner.subscription.current', compact('subscription', 'plan', 'propertiesCount', 'unitsCount', 'tenantsCount'));
    }

    public function availablePlans()
    {
        $plans = SubscriptionPlan::all();
        $currentSubscription = Auth::user()->activeSubscription;

        return view('owner.subscription.plans', compact('plans', 'currentSubscription'));
    }

    public function purchasePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id'
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has an active subscription
        if ($user->activeSubscription && $user->activeSubscription->isActive()) {
            return redirect()->back()->with('error', 'You already have an active subscription. Please wait for it to expire before purchasing a new plan.');
        }

        // Create or update subscription
        $subscription = OwnerSubscription::updateOrCreate(
            ['owner_id' => $user->id],
            [
                'plan_id' => $plan->id,
                'status' => $plan->price > 0 ? 'pending' : 'active',
                'auto_renew' => true,
                'sms_credits' => $plan->sms_notification ? 100 : 0
            ]
        );

        // If free plan, activate immediately
        if ($plan->price == 0) {
            $subscription->update([
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString()
            ]);

            return redirect()->route('owner.subscription.current')->with('success', 'Free plan activated successfully!');
        }

        // Generate invoice for paid plans
        $invoice = $subscription->generateInvoice();

        return redirect()->route('owner.subscription.payment', ['invoice_id' => $invoice->id])->with('success', 'Invoice generated successfully. Please complete the payment.');
    }

            public function billingHistory()
    {
        $user = Auth::user();

        // Load billing with all relationships
        $billing = $user->billing()
            ->with(['subscription.plan', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Force load relationships for paginated results
        $billing->getCollection()->load(['subscription.plan', 'paymentMethod']);



        return view('owner.subscription.billing', compact('billing'));
    }

    public function paymentMethods(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $pendingInvoice = null;

        if ($invoiceId) {
            $pendingInvoice = Billing::with(['subscription.plan', 'owner'])
                ->where('id', $invoiceId)
                ->where('owner_id', Auth::id())
                ->where('status', 'pending')
                ->first();
        } else {
            // Get the most recent pending invoice
            $pendingInvoice = Auth::user()->billing()
                ->with(['subscription.plan'])
                ->where('status', 'pending')
                ->latest()
                ->first();
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('owner.subscription.payment', compact('pendingInvoice', 'paymentMethods'));
    }

    public function processPayment(Request $request, $subscriptionId)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'transaction_id' => 'required|string|max:255',
            'payment_date' => 'required|date'
        ]);

        $user = Auth::user();
        $subscription = OwnerSubscription::where('id', $subscriptionId)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Calculate fees
        $invoiceAmount = $subscription->plan->price;
        $transactionFee = ($invoiceAmount * $paymentMethod->transaction_fee) / 100;
        $totalAmount = $invoiceAmount + $transactionFee;

        // Update billing record
        $billing = $subscription->getPendingInvoice();
        if ($billing) {
            $billing->update([
                'payment_method_id' => $paymentMethod->id,
                'transaction_id' => $request->transaction_id,
                'paid_date' => $request->payment_date,
                'status' => 'paid',
                'transaction_fee' => $transactionFee,
                'net_amount' => $totalAmount
            ]);
        }

        // Activate subscription
        $subscription->activateAfterPayment();

        return redirect()->route('owner.subscription.current')->with('success', 'Payment completed successfully! Your subscription is now active.');
    }

    public function initiatePaymentGateway(Request $request)
    {
        // Handle GET requests by redirecting to payment page
        if ($request->isMethod('GET')) {
            return redirect()->route('owner.subscription.payment');
        }

        $request->validate([
            'invoice_id' => 'required|exists:billing,id',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        $user = Auth::user();
        $billing = Billing::with(['subscription.plan', 'paymentMethod'])
            ->where('id', $request->invoice_id)
            ->where('owner_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Calculate total amount with fees
        $invoiceAmount = $billing->amount;
        $transactionFee = ($invoiceAmount * $paymentMethod->transaction_fee) / 100;
        $totalAmount = $invoiceAmount + $transactionFee;

        // Update billing with payment method
        $billing->update([
            'payment_method_id' => $paymentMethod->id,
            'transaction_fee' => $transactionFee,
            'net_amount' => $totalAmount
        ]);

        // Redirect to payment gateway based on payment method
        return $this->redirectToPaymentGateway($billing, $paymentMethod);
    }

    private function redirectToPaymentGateway($billing, $paymentMethod)
    {
        $amount = $billing->net_amount;
        $invoiceNumber = $billing->invoice_number;
        $description = "Subscription: " . $billing->subscription->plan->name;

        switch ($paymentMethod->code) {
            case 'bkash':
                return $this->redirectToBkash($amount, $invoiceNumber, $description);
            case 'nagad':
                return $this->redirectToNagad($amount, $invoiceNumber, $description);
            case 'rocket':
                return $this->redirectToRocket($amount, $invoiceNumber, $description);
            case 'bank_transfer':
                return $this->redirectToBankTransfer($billing);
            default:
                return redirect()->back()->with('error', 'Payment method not supported.');
        }
    }

        private function redirectToBkash($amount, $invoiceNumber, $description)
    {
        $bkashService = new BkashTokenizedService();

        // Check if bKash TokenizedCheckout is configured
        if (!$bkashService->isConfigured()) {
            return redirect()->back()->with('error', 'bKash TokenizedCheckout is not properly configured. Please contact administrator.');
        }

        // Test connection
        $connectionTest = $bkashService->testConnection();
        if (!$connectionTest['success']) {
            return redirect()->back()->with('error', 'bKash TokenizedCheckout connection failed: ' . $connectionTest['message']);
        }

        // Create TokenizedCheckout payment request
        $paymentId = 'PAY_' . time() . '_' . uniqid();
        $paymentResult = $bkashService->createTokenizedCheckout($amount, $invoiceNumber, $paymentId, $description);

        if (!$paymentResult['success']) {
            // Check if manual payment is available
            if (isset($paymentResult['manual_payment_available']) && $paymentResult['manual_payment_available']) {
                // Store payment data for manual payment
                session([
                    'manual_payment_data' => [
                        'payment_id' => $paymentResult['payment_id'] ?? 'MANUAL_' . time(),
                        'amount' => $paymentResult['amount'] ?? $amount,
                        'invoice_id' => $paymentResult['invoice_id'] ?? $invoiceNumber,
                        'invoice_number' => $invoiceNumber,
                        'description' => 'Manual bKash Payment'
                    ]
                ]);

                $paymentData = [
                    'paymentID' => $paymentResult['payment_id'] ?? 'MANUAL_' . time(),
                    'bkashURL' => null,
                    'amount' => $amount,
                    'invoice_number' => $invoiceNumber,
                    'description' => $description,
                    'success_url' => route('owner.subscription.payment.success'),
                    'cancel_url' => route('owner.subscription.payment.cancel'),
                    'fail_url' => route('owner.subscription.payment.fail'),
                    'manual_payment' => true,
                    'error_message' => $paymentResult['error'] ?? 'Unknown error',
                    'suggestion' => $paymentResult['suggestion'] ?? 'Please use manual payment method'
                ];

                return view('owner.subscription.payment_gateway.bkash', compact('paymentData'));
            }

            $errorMessage = $paymentResult['error'] ?? 'Unknown error';
            $suggestion = $paymentResult['suggestion'] ?? '';

            $fullErrorMessage = 'Failed to create bKash TokenizedCheckout payment: ' . $errorMessage;
            if ($suggestion) {
                $fullErrorMessage .= ' Suggestion: ' . $suggestion;
            }

            \Log::error('bKash payment creation failed in SubscriptionController', [
                'error' => $paymentResult['error'] ?? 'Unknown error',
                'details' => $paymentResult['details'] ?? [],
                'amount' => $amount,
                'invoice_number' => $invoiceNumber
            ]);

            return redirect()->back()->with('error', $fullErrorMessage);
        }

        // Store payment data in session for later use
        session([
            'bkash_payment_id' => $paymentResult['paymentID'],
            'bkash_invoice_number' => $invoiceNumber,
            'bkash_amount' => $amount,
            'bkash_payer_reference' => $paymentId
        ]);

        // Also store in database for backup
        $billing = Billing::where('invoice_number', $invoiceNumber)
            ->where('owner_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($billing) {
            $billing->update([
                'transaction_id' => $paymentResult['paymentID'],
                'payment_method_id' => PaymentMethod::where('code', 'bkash')->first()->id ?? null
            ]);
        }

        $paymentData = [
            'paymentID' => $paymentResult['paymentID'],
            'bkashURL' => $paymentResult['bkashURL'],
            'amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'description' => $description,
            'success_url' => route('owner.subscription.payment.success'),
            'cancel_url' => route('owner.subscription.payment.cancel'),
            'fail_url' => route('owner.subscription.payment.fail')
        ];

        return view('owner.subscription.payment_gateway.bkash', compact('paymentData'));
    }

    private function redirectToNagad($amount, $invoiceNumber, $description)
    {
        // Nagad payment gateway integration
        $merchantId = config('payment.nagad.merchant_id');
        $apiKey = config('payment.nagad.api_key');

        $paymentData = [
            'merchant_id' => $merchantId,
            'amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'description' => $description,
            'success_url' => route('owner.subscription.payment.success'),
            'cancel_url' => route('owner.subscription.payment.cancel'),
            'fail_url' => route('owner.subscription.payment.fail')
        ];

        return view('owner.subscription.payment_gateway.nagad', compact('paymentData'));
    }

    private function redirectToRocket($amount, $invoiceNumber, $description)
    {
        // Rocket payment gateway integration
        $merchantId = config('payment.rocket.merchant_id');
        $apiKey = config('payment.rocket.api_key');

        $paymentData = [
            'merchant_id' => $merchantId,
            'amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'description' => $description,
            'success_url' => route('owner.subscription.payment.success'),
            'cancel_url' => route('owner.subscription.payment.cancel'),
            'fail_url' => route('owner.subscription.payment.fail')
        ];

        return view('owner.subscription.payment_gateway.rocket', compact('paymentData'));
    }

    private function redirectToBankTransfer($billing)
    {
        // Show bank transfer details
        return view('owner.subscription.payment_gateway.bank_transfer', compact('billing'));
    }

        public function paymentSuccess(Request $request)
    {
        $paymentID = $request->get('paymentID');
        $payerReference = $request->get('payerReference');
        $trxID = $request->get('trxID');
        $status = $request->get('status');

        // Get stored payment data from session or localStorage
        $sessionPaymentID = session('bkash_payment_id');
        $sessionInvoiceNumber = session('bkash_invoice_number');
        $sessionAmount = session('bkash_amount');
        $sessionPayerReference = session('bkash_payer_reference');

        // If paymentID is provided via URL (from bKash callback), use it
        if ($paymentID && $status === 'success') {
            $sessionPaymentID = $paymentID;
            $sessionInvoiceNumber = $payerReference;
        }

        // If no session data, try to get from database using paymentID
        if (!$sessionInvoiceNumber && $paymentID) {
            // Try to find billing record by payment ID or create a test one
            $billing = Billing::where('status', 'pending')
                ->where('owner_id', Auth::id())
                ->latest()
                ->first();

            if ($billing) {
                $sessionInvoiceNumber = $billing->invoice_number;
                $sessionAmount = $billing->amount;
                $sessionPayerReference = $billing->invoice_number;
            }
        }

        // Log payment success attempt
        \Log::info('bKash Payment Success Attempt', [
            'paymentID' => $paymentID,
            'sessionPaymentID' => $sessionPaymentID,
            'payerReference' => $payerReference,
            'trxID' => $trxID,
            'sessionInvoiceNumber' => $sessionInvoiceNumber
        ]);

        if (!$paymentID || !$sessionPaymentID || $paymentID !== $sessionPaymentID) {
            \Log::error('bKash Payment Verification Failed', [
                'paymentID' => $paymentID,
                'sessionPaymentID' => $sessionPaymentID
            ]);
            return redirect()->route('owner.subscription.current')->with('error', 'Invalid payment verification.');
        }

        $bkashService = new BkashTokenizedService();

        // Execute TokenizedCheckout payment
        $executeResult = $bkashService->executeTokenizedPayment($paymentID, $sessionPayerReference ?? $payerReference);

        \Log::info('bKash Payment Execution Result', [
            'executeResult' => $executeResult
        ]);

        if (!$executeResult['success']) {
            return redirect()->route('owner.subscription.current')->with('error', 'TokenizedCheckout payment execution failed: ' . ($executeResult['error'] ?? 'Unknown error'));
        }

        // Verify payment status
        if ($executeResult['transactionStatus'] !== 'Completed') {
            return redirect()->route('owner.subscription.current')->with('error', 'Payment not completed. Status: ' . $executeResult['transactionStatus']);
        }

        // Find billing record
        $billing = null;

        if ($sessionInvoiceNumber) {
            $billing = Billing::where('invoice_number', $sessionInvoiceNumber)
                ->where('status', 'pending')
                ->where('owner_id', Auth::id())
                ->first();
        }

        // If still not found, try to find any pending billing for this user
        if (!$billing) {
            $billing = Billing::where('status', 'pending')
                ->where('owner_id', Auth::id())
                ->latest()
                ->first();
        }

        if (!$billing) {
            \Log::error('bKash Payment - Billing record not found', [
                'invoice_number' => $sessionInvoiceNumber,
                'user_id' => Auth::id(),
                'paymentID' => $paymentID
            ]);
            return redirect()->route('owner.subscription.current')->with('error', 'Billing record not found. Please try again.');
        }

        // Update billing record
        $billing->update([
            'transaction_id' => $trxID ?? $executeResult['trxID'] ?? $paymentID,
            'paid_date' => now(),
            'status' => 'paid',
            'payment_method_id' => PaymentMethod::where('code', 'bkash')->first()->id ?? null
        ]);

        // Activate subscription
        $subscription = $billing->subscription;
        $subscription->activateAfterPayment();

        // Send payment confirmation notification
        try {
            $user = Auth::user();
            $paymentMethod = PaymentMethod::where('code', 'bkash')->first();
            $paymentMethodName = $paymentMethod ? $paymentMethod->name : 'bKash';

            NotificationHelper::sendPaymentConfirmation(
                $user,
                '৳' . number_format($billing->amount, 2),
                $billing->invoice_number,
                $paymentMethodName
            );
        } catch (\Exception $e) {
            \Log::error('Payment confirmation notification failed: ' . $e->getMessage());
        }

        // Clear session data
        session()->forget(['bkash_payment_id', 'bkash_invoice_number', 'bkash_amount', 'bkash_payer_reference']);

        \Log::info('bKash Payment Successfully Completed', [
            'billing_id' => $billing->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => $trxID ?? $executeResult['trxID'] ?? $paymentID
        ]);

        return redirect()->route('owner.subscription.current')->with('success', 'Payment completed successfully! Your subscription is now active.');
    }

    public function paymentCancel()
    {
        return redirect()->route('owner.subscription.payment')->with('error', 'Payment was cancelled.');
    }

    public function paymentFail()
    {
        return redirect()->route('owner.subscription.payment')->with('error', 'Payment failed. Please try again.');
    }
}
