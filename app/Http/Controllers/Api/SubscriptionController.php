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

                        $paymentId = 'PAY_' . time() . '_' . uniqid();
                        $result = $bkashService->createTokenizedCheckout($invoice->net_amount ?? $invoice->amount, $invoice->invoice_number, $paymentId, 'Subscription: ' . ($subscription->plan->name ?? ''));

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
}


