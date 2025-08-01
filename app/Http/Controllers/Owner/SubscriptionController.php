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

        // Get the owner record for this user
        $owner = $user->owner;
        if (!$owner) {
            return redirect()->back()->with('error', 'Owner profile not found. Please contact support.');
        }

        // Create or update subscription
        $subscription = OwnerSubscription::updateOrCreate(
            ['owner_id' => $owner->id],
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

    public function upgradePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id'
        ]);

        $user = Auth::user();
        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
        $currentSubscription = $user->activeSubscription;

        // Check if user has active subscription
        if (!$currentSubscription || !$currentSubscription->isActive()) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        $currentPlan = $currentSubscription->plan;

        // Check if it's an upgrade (higher price)
        if ($newPlan->price <= $currentPlan->price) {
            return redirect()->back()->with('error', 'You can only upgrade to a higher-priced plan.');
        }

        // Calculate price difference
        $priceDifference = $newPlan->price - $currentPlan->price;

        // Get the owner record
        $owner = $user->owner;
        if (!$owner) {
            return redirect()->back()->with('error', 'Owner profile not found. Please contact support.');
        }

        // Create billing record for upgrade (unpaid status)
        $billing = Billing::create([
            'owner_id' => $owner->id,
            'subscription_id' => $currentSubscription->id,
            'invoice_number' => 'INV-UPGRADE-' . date('Y') . '-' . str_pad($currentSubscription->id, 6, '0', STR_PAD_LEFT),
            'amount' => $priceDifference,
            'status' => 'unpaid',
            'payment_method' => 'upgrade',
            'due_date' => now()->addDays(7),
            'description' => "Upgrade from {$currentPlan->name} to {$newPlan->name} Plan"
        ]);

        // Update subscription plan but keep status pending until payment
        $currentSubscription->update([
            'plan_id' => $newPlan->id,
            'status' => 'pending_upgrade'
        ]);

        // Redirect to payment page
        return redirect()->route('owner.subscription.payment', ['invoice_id' => $billing->id])->with('success', 'Upgrade invoice generated. Please complete the payment to activate the new plan.');
    }

    public function billingHistory()
    {
        // Get the correct owner_id from the authenticated user
        $user = Auth::user();
        $owner = $user->owner;
        $ownerId = $owner ? $owner->id : null;

        if (!$ownerId) {
            return redirect()->back()->with('error', 'Owner profile not found.');
        }

        $billingHistory = Billing::with(['subscription.plan', 'owner'])
            ->where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Log for debugging
        \Log::info('Billing History accessed', [
            'user_id' => Auth::id(),
            'owner_id' => $ownerId,
            'user_owner_id' => Auth::user()->owner_id,
            'total_bills' => $billingHistory->count(),
            'bills' => $billingHistory->map(function($bill) {
                return [
                    'id' => $bill->id,
                    'invoice_number' => $bill->invoice_number,
                    'amount' => $bill->amount,
                    'status' => $bill->status,
                    'owner_id' => $bill->owner_id,
                    'subscription_id' => $bill->subscription_id
                ];
            })->toArray()
        ]);

        return view('owner.subscription.billing', compact('billingHistory'));
    }

    public function paymentMethods(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $pendingInvoice = null;

        \Log::info('Payment page accessed', [
            'invoice_id' => $invoiceId,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'not logged in'
        ]);

        if ($invoiceId) {
            // Get the correct owner_id from the authenticated user
            $ownerId = Auth::user()->owner_id ?? Auth::id();

            $pendingInvoice = Billing::with(['subscription.plan', 'owner'])
                ->where('id', $invoiceId)
                ->where('owner_id', $ownerId)
                ->whereIn('status', ['pending', 'unpaid', 'fail', 'cancel'])
                ->first();

            // Log for debugging
            \Log::info('Payment page accessed with invoice_id', [
                'invoice_id' => $invoiceId,
                'pending_invoice_found' => $pendingInvoice ? true : false,
                'user_id' => Auth::id(),
                'owner_id' => $ownerId,
                'user_owner_id' => Auth::user()->owner_id,
                'invoice_details' => $pendingInvoice ? [
                    'id' => $pendingInvoice->id,
                    'amount' => $pendingInvoice->amount,
                    'status' => $pendingInvoice->status,
                    'owner_id' => $pendingInvoice->owner_id,
                    'subscription_id' => $pendingInvoice->subscription_id
                ] : null,
                'query_conditions' => [
                    'invoice_id' => $invoiceId,
                    'owner_id' => $ownerId,
                    'status' => ['pending', 'unpaid', 'fail', 'cancel']
                ]
            ]);
        } else {
            // Get the correct owner_id from the authenticated user
            $ownerId = Auth::user()->owner_id ?? Auth::id();

            // Get the most recent pending invoice
            $pendingInvoice = Billing::with(['subscription.plan'])
                ->where('owner_id', $ownerId)
                ->whereIn('status', ['pending', 'unpaid', 'fail', 'cancel'])
                ->latest()
                ->first();

            // Log for debugging
            \Log::info('Payment page accessed without invoice_id', [
                'pending_invoice_found' => $pendingInvoice ? true : false,
                'user_id' => Auth::id(),
                'owner_id' => $ownerId,
                'user_owner_id' => Auth::user()->owner_id,
                'invoice_details' => $pendingInvoice ? [
                    'id' => $pendingInvoice->id,
                    'amount' => $pendingInvoice->amount,
                    'status' => $pendingInvoice->status,
                    'owner_id' => $pendingInvoice->owner_id,
                    'subscription_id' => $pendingInvoice->subscription_id
                ] : null
            ]);
        }

        // Get payment methods
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
        // Log the request for debugging
        \Log::info('initiatePaymentGateway method called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'invoice_id' => $request->input('invoice_id'),
            'payment_method_id' => $request->input('payment_method_id'),
            'all_data' => $request->all(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'not logged in',
            'headers' => $request->headers->all()
        ]);

        $request->validate([
            'invoice_id' => 'required|exists:billing,id',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        // Handle GET requests by redirecting to payment page
        if ($request->isMethod('GET')) {
            \Log::info('GET request detected, redirecting to payment page');
            return redirect()->route('owner.subscription.payment', ['invoice_id' => $request->invoice_id]);
        }

        // Handle POST requests for payment processing
        try {
            $invoice = Billing::findOrFail($request->invoice_id);
            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

            \Log::info('Payment processing started', [
                'invoice_id' => $invoice->id,
                'payment_method' => $paymentMethod->name,
                'amount' => $invoice->amount
            ]);

            // Validate payment method
            if (!$paymentMethod->is_active) {
                throw new \Exception('Selected payment method is not active');
            }

            // Calculate transaction fee
            $transactionFee = ($invoice->amount * $paymentMethod->transaction_fee) / 100;
            $totalAmount = $invoice->amount + $transactionFee;

            // Update invoice with payment method
            $invoice->update([
                'payment_method_id' => $paymentMethod->id,
                'transaction_fee' => $transactionFee,
                'net_amount' => $totalAmount
            ]);

            \Log::info('Payment method updated', [
                'invoice_id' => $invoice->id,
                'payment_method_id' => $paymentMethod->id,
                'transaction_fee' => $transactionFee,
                'total_amount' => $totalAmount
            ]);

            // Redirect directly to payment gateway based on payment method
            return $this->redirectToPaymentGateway($invoice, $paymentMethod);

        } catch (\Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
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

        // Redirect directly to bKash payment URL
        if ($paymentResult['bkashURL'] && $paymentResult['bkashURL'] !== '#' && $paymentResult['bkashURL'] !== 'null') {
            return redirect($paymentResult['bkashURL']);
        } else {
            // Fallback to manual payment page if URL is not available
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

        /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request)
    {
        \Log::info('Payment success page accessed', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Check bKash payment status
        $paymentStatus = $request->get('status');
        $paymentId = $request->get('paymentID');
        $payerReference = $request->get('payerReference');
        $invoiceId = $request->get('invoice_id');

        // If payment failed or cancelled, redirect to appropriate page
        if ($paymentStatus === 'failure') {
            \Log::info('Payment failed - redirecting to fail page', [
                'payment_id' => $paymentId,
                'status' => $paymentStatus,
                'payer_reference' => $payerReference
            ]);
            
            return redirect()->route('owner.subscription.payment.fail', [
                'paymentID' => $paymentId,
                'payerReference' => $payerReference,
                'status' => $paymentStatus
            ]);
        }

        // If payment cancelled, redirect to cancel page
        if ($paymentStatus === 'cancel' || $paymentStatus === 'cancelled') {
            \Log::info('Payment cancelled - redirecting to cancel page', [
                'payment_id' => $paymentId,
                'status' => 'unpaid',
                'payer_reference' => $payerReference
            ]);
            
            return redirect()->route('owner.subscription.payment.cancel', [
                'paymentID' => $paymentId,
                'payerReference' => $payerReference,
                'status' => $paymentStatus
            ]);
        }

        // Find billing record by various methods
        $billing = null;
        
        // Try to find by invoice_id
        if ($invoiceId) {
            $billing = Billing::with(['subscription.plan'])->find($invoiceId);
        }
        
        // Try to find by payerReference (invoice number)
        if (!$billing && $payerReference) {
            $billing = Billing::with(['subscription.plan'])->where('invoice_number', $payerReference)->first();
        }
        
        // Try to find by payment ID
        if (!$billing && $paymentId) {
            $billing = Billing::with(['subscription.plan'])->where('transaction_id', $paymentId)->first();
        }

        if ($billing) {
            // Verify payment with bKash API
            $paymentVerified = false;
            $verificationDetails = null;
            
            if ($paymentId) {
                try {
                    $bkashService = new \App\Services\BkashTokenizedService();
                    
                    // Query payment status from bKash
                    $verificationResult = $bkashService->queryTokenizedPayment($paymentId);
                    
                    if ($verificationResult['success']) {
                        $transactionStatus = $verificationResult['transactionStatus'] ?? '';
                        $statusCode = $verificationResult['statusCode'] ?? '';
                        $statusMessage = $verificationResult['statusMessage'] ?? '';
                        
                        // Check if payment is actually completed
                        if ($transactionStatus === 'Completed' || $statusCode === '0000') {
                            $paymentVerified = true;
                            $verificationDetails = $verificationResult;
                            
                            \Log::info('Payment verified with bKash API', [
                                'payment_id' => $paymentId,
                                'transaction_status' => $transactionStatus,
                                'status_code' => $statusCode,
                                'status_message' => $statusMessage,
                                'trx_id' => $verificationResult['trxID'] ?? null,
                                'amount' => $verificationResult['amount'] ?? null
                            ]);
                        } else {
                            \Log::warning('Payment not verified - status indicates failure', [
                                'payment_id' => $paymentId,
                                'transaction_status' => $transactionStatus,
                                'status_code' => $statusCode,
                                'status_message' => $statusMessage
                            ]);
                        }
                    } else {
                        \Log::error('Payment verification failed', [
                            'payment_id' => $paymentId,
                            'error' => $verificationResult['error'] ?? 'Unknown error'
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Payment verification exception', [
                        'payment_id' => $paymentId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Only update billing if payment is verified or if verification failed (fallback)
            if ($paymentVerified || !$paymentId) {
                // Update billing status to paid
                $billing->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                    'transaction_id' => $paymentId ?? $billing->transaction_id,
                    'verification_details' => $verificationDetails ? json_encode($verificationDetails) : null
                ]);

                \Log::info('Payment success - Billing updated', [
                    'invoice_id' => $billing->id,
                    'invoice_number' => $billing->invoice_number,
                    'status' => 'paid',
                    'payment_id' => $paymentId,
                    'verified' => $paymentVerified
                ]);
            } else {
                \Log::warning('Payment not verified - billing not updated', [
                    'invoice_id' => $billing->id,
                    'invoice_number' => $billing->invoice_number,
                    'payment_id' => $paymentId
                ]);
                
                // Redirect to payment fail page
                return redirect()->route('owner.subscription.payment.fail', [
                    'paymentID' => $paymentId,
                    'payerReference' => $payerReference,
                    'status' => 'verification_failed'
                ]);
            }

            // Get the subscription
            $subscription = $billing->subscription;
            
            if ($subscription) {
                $newPlan = SubscriptionPlan::find($subscription->plan_id);
                
                // Check if this is an upgrade payment
                if ($billing->payment_method === 'upgrade' && $subscription->status === 'pending_upgrade') {
                    // Activate the upgrade
                    if ($newPlan) {
                        $subscription->update([
                            'status' => 'active',
                            'sms_credits' => $newPlan->sms_notification ? 200 : 0,
                            'start_date' => now(),
                            'end_date' => now()->addDays(30)
                        ]);

                        \Log::info('Upgrade activated after payment', [
                            'subscription_id' => $subscription->id,
                            'new_plan' => $newPlan->name,
                            'user_id' => Auth::id()
                        ]);
                    }
                } 
                // Check if this is a new purchase payment
                elseif ($subscription->status === 'pending') {
                    // Activate the new subscription
                    if ($newPlan) {
                        $subscription->update([
                            'status' => 'active',
                            'sms_credits' => $newPlan->sms_notification ? 200 : 0,
                            'start_date' => now(),
                            'end_date' => now()->addDays(30)
                        ]);

                        \Log::info('New subscription activated after payment', [
                            'subscription_id' => $subscription->id,
                            'new_plan' => $newPlan->name,
                            'user_id' => Auth::id()
                        ]);
                    }
                }
            }
        }

        return view('owner.subscription.payment-success');
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Request $request)
    {
        \Log::info('Payment cancelled', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Get invoice details from request
        $invoiceId = $request->get('invoice_id');
        $paymentId = $request->get('paymentID');
        $payerReference = $request->get('payerReference');

        // Find the billing record
        $billing = null;
        if ($invoiceId) {
            $billing = Billing::find($invoiceId);
        } elseif ($payerReference) {
            $billing = Billing::where('invoice_number', $payerReference)->first();
        }

        // If still not found, try to find by payment ID in transaction_id
        if (!$billing && $paymentId) {
            $billing = Billing::where('transaction_id', $paymentId)->first();
        }

        if ($billing) {
            // Update billing status to cancel
            $billing->update([
                'status' => 'cancel',
                'transaction_id' => $paymentId ?? null,
                'updated_at' => now()
            ]);

            \Log::info('Payment cancelled - Billing updated', [
                'invoice_id' => $billing->id,
                'invoice_number' => $billing->invoice_number,
                'status' => 'cancel',
                'payment_id' => $paymentId
            ]);
        }

        return redirect()->route('owner.subscription.payment')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Handle failed payment
     */
    public function paymentFail(Request $request)
    {
        \Log::info('Payment failed', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Get invoice details from request
        $invoiceId = $request->get('invoice_id');
        $paymentId = $request->get('paymentID');
        $payerReference = $request->get('payerReference');

        // Find the billing record
        $billing = null;
        if ($invoiceId) {
            $billing = Billing::find($invoiceId);
        } elseif ($payerReference) {
            $billing = Billing::where('invoice_number', $payerReference)->first();
        }

        // If still not found, try to find by payment ID in transaction_id
        if (!$billing && $paymentId) {
            $billing = Billing::where('transaction_id', $paymentId)->first();
        }

        if ($billing) {
            // Update billing status to fail
            $billing->update([
                'status' => 'fail',
                'transaction_id' => $paymentId ?? null,
                'updated_at' => now()
            ]);

            \Log::info('Payment failed - Billing updated', [
                'invoice_id' => $billing->id,
                'invoice_number' => $billing->invoice_number,
                'status' => 'fail',
                'payment_id' => $paymentId
            ]);
        }

        return redirect()->route('owner.subscription.payment')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle payment gateway processing
     */
    public function paymentGateway(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $paymentMethodId = $request->get('payment_method_id');

        \Log::info('Payment gateway accessed', [
            'invoice_id' => $invoiceId,
            'payment_method_id' => $paymentMethodId,
            'user_id' => Auth::id()
        ]);

        try {
            $invoice = Billing::with(['subscription.plan'])->findOrFail($invoiceId);
            $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

            // Validate that this invoice belongs to the current user
            $ownerId = Auth::user()->owner_id ?? Auth::id();
            if ($invoice->owner_id != $ownerId) {
                throw new \Exception('Invoice does not belong to current user');
            }

            // Process payment based on payment method
            switch ($paymentMethod->code) {
                case 'bkash':
                    return $this->processBkashPayment($invoice, $paymentMethod);
                case 'nagad':
                    return $this->processNagadPayment($invoice, $paymentMethod);
                case 'rocket':
                    return $this->processRocketPayment($invoice, $paymentMethod);
                default:
                    throw new \Exception('Unsupported payment method');
            }

        } catch (\Exception $e) {
            \Log::error('Payment gateway error: ' . $e->getMessage());
            return redirect()->route('owner.subscription.payment', ['invoice_id' => $invoiceId])
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process bKash payment
     */
    private function processBkashPayment($invoice, $paymentMethod)
    {
        \Log::info('Processing bKash payment', [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount
        ]);

        // Redirect to bKash payment gateway
        return $this->redirectToBkash(
            $invoice->amount,
            $invoice->invoice_number,
            'Payment for invoice: ' . $invoice->invoice_number
        );
    }

    /**
     * Process Nagad payment
     */
    private function processNagadPayment($invoice, $paymentMethod)
    {
        \Log::info('Processing Nagad payment', [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount
        ]);

        // Redirect to Nagad payment gateway
        return $this->redirectToNagad(
            $invoice->amount,
            $invoice->invoice_number,
            'Payment for invoice: ' . $invoice->invoice_number
        );
    }

    /**
     * Process Rocket payment
     */
    private function processRocketPayment($invoice, $paymentMethod)
    {
        \Log::info('Processing Rocket payment', [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount
        ]);

        // Redirect to Rocket payment gateway
        return $this->redirectToRocket(
            $invoice->amount,
            $invoice->invoice_number,
            'Payment for invoice: ' . $invoice->invoice_number
        );
    }
}
