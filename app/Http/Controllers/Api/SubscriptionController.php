<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionPlan;
use App\Models\OwnerSubscription;
use App\Models\Billing;
use App\Models\PaymentMethod;
use App\Services\SubscriptionUpgradeService;

class SubscriptionController extends Controller
{
    /**
     * Public: List active subscription plans for mobile app
     */
    public function plans(Request $request)
    {
        try {
            $plans = SubscriptionPlan::where('is_active', true)
                ->orderBy('price', 'asc')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'price' => (float) $plan->price,
                        'formatted_price' => $plan->formatted_price,
                        'billing_cycle' => $plan->billing_cycle,
                        'billing_cycle_text' => $plan->billing_cycle_text,
                        'duration_days' => $plan->duration_days,
                        'properties_limit' => $plan->properties_limit,
                        'units_limit' => $plan->units_limit,
                        'tenants_limit' => $plan->tenants_limit,
                        'sms_notification' => (bool) $plan->sms_notification,
                        'sms_credit' => (int) ($plan->sms_credit ?? 0),
                        'is_popular' => (bool) $plan->is_popular,
                        'features' => $plan->features ?? [],
                        'features_css' => $plan->features_css ?? [],
                    ];
                });

            return response()->json([
                'success' => true,
                'plans' => $plans,
            ]);
        } catch (\Exception $e) {
            Log::error('Plans listing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load plans',
            ], 500);
        }
    }

    /**
     * Auth: Purchase a subscription plan and return payment info (if paid)
     */
    public function purchase(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:subscription_plans,id',
                'payment_method' => 'nullable|string|in:bkash,nagad,rocket,bank_transfer',
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Must be an owner
            $owner = \App\Models\Owner::where('user_id', $user->id)->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner profile not found',
                ], 403);
            }

            $plan = SubscriptionPlan::findOrFail($request->plan_id);

            // If already active subscription exists and is active, block duplicate purchase
            $activeSubscription = OwnerSubscription::where('owner_id', $owner->id)
                ->where('status', 'active')
                ->where('end_date', '>=', now()->toDateString())
                ->first();

            if ($activeSubscription && $plan->price <= ($activeSubscription->plan->price ?? 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active subscription. Upgrade to a higher plan to proceed.',
                ], 400);
            }

            // Create or update subscription
            $subscription = OwnerSubscription::updateOrCreate(
                ['owner_id' => $owner->id],
                [
                    'plan_id' => $plan->id,
                    'status' => $plan->price > 0 ? 'pending' : 'active',
                    'auto_renew' => true,
                    'sms_credits' => $plan->sms_notification ? 100 : 0,
                ]
            );

            // Free plan: activate immediately
            if ((float) $plan->price == 0.0) {
                $subscription->update([
                    'status' => 'active',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addYear()->toDateString(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Free plan activated successfully',
                    'subscription' => [
                        'id' => $subscription->id,
                        'status' => $subscription->status,
                        'start_date' => $subscription->start_date,
                        'end_date' => $subscription->end_date,
                    ],
                ]);
            }

            // Paid plan: create invoice
            $invoice = $subscription->generateInvoice();

            $paymentMethodCode = $request->input('payment_method', 'bkash');
            $paymentMethod = PaymentMethod::where('code', $paymentMethodCode)->where('is_active', true)->first();

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected payment method is not available',
                ], 422);
            }

            // Default response with invoice details
            $response = [
                'success' => true,
                'message' => 'Invoice generated. Proceed to payment.',
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => (float) $invoice->amount,
                    'status' => $invoice->status,
                    'due_date' => optional($invoice->due_date)->toDateString(),
                ],
                'payment' => null,
            ];

            // Initiate payment for supported gateways
            switch ($paymentMethodCode) {
                case 'bkash':
                    try {
                        $bkashService = new \App\Services\BkashTokenizedService();
                        if (!$bkashService->isConfigured()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'bKash is not configured. Please contact support.',
                            ], 500);
                        }

                        $connection = $bkashService->testConnection();
                        if (!$connection['success']) {
                            return response()->json([
                                'success' => false,
                                'message' => 'bKash connection failed: ' . ($connection['message'] ?? 'Unknown error'),
                            ], 500);
                        }

                        // Determine amount to charge (prefer net_amount only when > 0)
                        $amountToCharge = (float) (isset($invoice->net_amount) && (float)$invoice->net_amount > 0
                            ? $invoice->net_amount
                            : $invoice->amount);

                        // Guard: bKash requires positive amount
                        if ($amountToCharge < 1) {
                            return response()->json([
                                'success' => false,
                                'message' => 'bKash minimum amount is 1 BDT. Please use Bank Transfer.',
                            ], 422);
                        }

                        $paymentId = 'PAY_' . time() . '_' . uniqid();
                        $result = $bkashService->createTokenizedCheckout($amountToCharge, $invoice->invoice_number, $paymentId, 'Subscription: ' . ($subscription->plan->name ?? ''));

                        if (!($result['success'] ?? false)) {
                            return response()->json([
                                'success' => false,
                                'message' => $result['error'] ?? 'Failed to create bKash payment',
                                'details' => $result['details'] ?? null,
                            ], 500);
                        }

                        // Update billing with transaction id
                        $invoice->update([
                            'transaction_id' => $result['paymentID'] ?? null,
                            'payment_method_id' => $paymentMethod->id,
                        ]);

                        $response['payment'] = [
                            'method' => 'bkash',
                            'payment_id' => $result['paymentID'] ?? null,
                            'payment_url' => $result['bkashURL'] ?? null,
                        ];
                    } catch (\Exception $e) {
                        Log::error('bKash payment init error (API): ' . $e->getMessage());
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to initiate bKash payment: ' . $e->getMessage(),
                        ], 500);
                    }
                    break;

                case 'bank_transfer':
                    // Just return invoice details; payment handled manually in app/web
                    $response['payment'] = [
                        'method' => 'bank_transfer',
                        'instructions' => 'Please contact support to complete payment via bank transfer.',
                    ];
                    break;

                default:
                    // Not implemented gateways for API yet
                    $response['payment'] = [
                        'method' => $paymentMethodCode,
                        'supported' => false,
                        'message' => 'Selected payment method is not yet supported in mobile API.',
                    ];
                    break;
            }

            return response()->json($response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Subscription purchase error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auth: List active payment methods for subscription
     */
    public function paymentMethods(Request $request)
    {
        try {
            $methods = PaymentMethod::where('is_active', true)
                ->orderBy('id', 'asc')
                ->get()
                ->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'code' => $m->code,
                        'name' => $m->display_name,
                        'logo_url' => $m->logo_url,
                        'transaction_fee' => (float)($m->transaction_fee ?? 0),
                    ];
                });

            return response()->json([
                'success' => true,
                'methods' => $methods,
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment methods API error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load payment methods'], 500);
        }
    }

    /**
     * Auth: List subscription invoices (billing) for owner
     */
    public function invoices(Request $request)
    {
        try {
            $user = $request->user();
            $owner = \App\Models\Owner::where('user_id', $user->id)->firstOrFail();

            $invoices = Billing::with(['subscription.plan'])
                ->where('owner_id', $owner->id)
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'invoice_number' => $b->invoice_number,
                        'amount' => (float)$b->amount,
                        'status' => $b->status,
                        'due_date' => optional($b->due_date)->toDateString(),
                        'paid_date' => optional($b->paid_date)->toDateString(),
                        'plan' => $b->subscription && $b->subscription->plan ? [
                            'id' => $b->subscription->plan->id,
                            'name' => $b->subscription->plan->name,
                        ] : null,
                    ];
                });

            return response()->json(['success' => true, 'invoices' => $invoices]);
        } catch (\Exception $e) {
            \Log::error('Subscription invoices API error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load invoices'], 500);
        }
    }

    /**
     * Auth: Initiate checkout for a subscription invoice with selected method
     */
    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'invoice_id' => 'required|exists:billing,id',
                'payment_method' => 'required|string',
            ]);

            $user = $request->user();
            $owner = \App\Models\Owner::where('user_id', $user->id)->firstOrFail();
            $invoice = Billing::with(['subscription.plan'])
                ->where('id', $request->invoice_id)
                ->where('owner_id', $owner->id)
                ->firstOrFail();

            $method = PaymentMethod::where('code', $request->payment_method)
                ->where('is_active', true)
                ->first();

            if (!$method) {
                return response()->json(['success' => false, 'message' => 'Payment method not available'], 422);
            }

            // Reject already paid invoices
            if (strtolower($invoice->status) === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice is already paid'
                ], 422);
            }

            // Amount to charge
            $amountToCharge = (float) ($invoice->net_amount ?? $invoice->amount);

            // Guard: bKash requires positive amount (>= 1 BDT typically)
            if ($method->code === 'bkash' && $amountToCharge < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'bKash minimum amount is 1 BDT. Please use Bank Transfer.',
                ], 422);
            }

            switch ($method->code) {
                case 'bkash':
                    try {
                        $bkashService = new \App\Services\BkashTokenizedService();
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'bKash not configured: ' . $e->getMessage()
                        ], 422);
                    }

                    if (!$bkashService->isConfigured()) {
                        return response()->json(['success' => false, 'message' => 'bKash not configured'], 422);
                    }

                    $conn = $bkashService->testConnection();
                    if (!$conn['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'bKash connection failed: ' . ($conn['message'] ?? 'Unknown error')
                        ], 422);
                    }
                    $paymentId = 'PAY_' . time() . '_' . uniqid();
                    $planName = '';
                    if ($invoice->subscription && $invoice->subscription->plan) {
                        $planName = $invoice->subscription->plan->name ?? '';
                    }
                    $result = $bkashService->createTokenizedCheckout(
                        $amountToCharge,
                        $invoice->invoice_number,
                        $paymentId,
                        'Subscription: ' . $planName
                    );
                    if (!($result['success'] ?? false)) {
                        return response()->json([
                            'success' => false,
                            'message' => $result['error'] ?? 'Payment create failed',
                            'details' => $result['details'] ?? null
                        ], 422);
                    }
                    $invoice->update([
                        'transaction_id' => $result['paymentID'] ?? null,
                        'payment_method_id' => $method->id,
                        // Keep original status (e.g., 'unpaid') until success callback
                    ]);
                    return response()->json([
                        'success' => true,
                        'payment' => [
                            'method' => 'bkash',
                            'payment_id' => $result['paymentID'] ?? null,
                            'payment_url' => $result['bkashURL'] ?? null,
                            'callback_url' => $result['callbackURL'] ?? null,
                        ],
                        'invoice' => [
                            'id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                            'amount' => (float)$invoice->amount,
                            'status' => $invoice->status,
                        ],
                    ]);
                case 'bank_transfer':
                    return response()->json([
                        'success' => true,
                        'payment' => [
                            'method' => 'bank_transfer',
                            'instructions' => 'Contact support to complete payment via bank transfer.',
                        ],
                        'invoice' => [
                            'id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                            'amount' => (float)$invoice->amount,
                            'status' => $invoice->status,
                        ],
                    ]);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment method not supported yet',
                    ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            \Log::error('Subscription checkout API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auth: Upgrade subscription plan
     */
    public function upgradePlan(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:subscription_plans,id',
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Must be an owner
            $owner = \App\Models\Owner::where('user_id', $user->id)->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner profile not found',
                ], 403);
            }

            $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
            // Find current active subscription (allow null end_date as lifetime)
            $currentSubscription = \App\Models\OwnerSubscription::where('owner_id', $owner->id)
                ->where('status', 'active')
                ->where(function($q){
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
                })
                ->with('plan')
                ->first();

            // Check if user has active subscription
            if (!$currentSubscription || !$currentSubscription->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found.',
                ], 400);
            }

            $currentPlan = $currentSubscription->plan;

            // Check if it's an upgrade (higher price)
            if ($newPlan->price <= $currentPlan->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only upgrade to a higher-priced plan.',
                ], 400);
            }

            // Check if already upgrading
            if ($currentSubscription->isUpgrading()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upgrade already in progress. Please complete the current upgrade first.',
                ], 400);
            }

            try {
                // Use the upgrade service
                $upgradeService = new SubscriptionUpgradeService();
                $result = $upgradeService->initiateUpgrade($owner->id, $newPlan->id);

                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Upgrade request created successfully',
                        'upgrade_request' => [
                            'id' => $result['upgrade_request']->id,
                            'status' => $result['upgrade_request']->status,
                            'requested_plan' => $result['upgrade_request']->requestedPlan->name,
                            'amount' => (float) $result['upgrade_request']->amount,
                        ],
                        'invoice' => [
                            'id' => $result['invoice']->id,
                            'invoice_number' => $result['invoice']->invoice_number,
                            'amount' => (float) $result['invoice']->amount,
                            'status' => $result['invoice']->status,
                            'due_date' => $result['invoice']->due_date,
                        ],
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                    ], 400);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create upgrade request: ' . $e->getMessage(),
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            \Log::error('Subscription upgrade API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auth: Complete subscription upgrade after payment
     */
    public function completeUpgrade(Request $request, $invoiceId)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $upgradeService = new SubscriptionUpgradeService();
            $result = $upgradeService->completeUpgrade($invoiceId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription upgraded successfully!',
                    'subscription' => [
                        'id' => $result['subscription']->id,
                        'plan_name' => $result['subscription']->plan->name,
                        'status' => $result['subscription']->status,
                        'start_date' => $result['subscription']->start_date,
                        'end_date' => $result['subscription']->end_date,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Subscription upgrade completion API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auth: Cancel subscription upgrade
     */
    public function cancelUpgrade(Request $request, $upgradeRequestId)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $upgradeService = new SubscriptionUpgradeService();
            $result = $upgradeService->cancelUpgrade($upgradeRequestId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upgrade request cancelled successfully.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Subscription upgrade cancellation API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auth: Get upgrade status for current user
     */
    public function getUpgradeStatus(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $owner = \App\Models\Owner::where('user_id', $user->id)->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner profile not found',
                ], 403);
            }

            $upgradeService = new SubscriptionUpgradeService();
            $status = $upgradeService->getUpgradeStatus($owner->id);

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            \Log::error('Subscription upgrade status API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}


