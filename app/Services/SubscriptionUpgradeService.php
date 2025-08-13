<?php

namespace App\Services;

use App\Models\OwnerSubscription;
use App\Models\SubscriptionUpgradeRequest;
use App\Models\Billing;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionUpgradeService
{
    /**
     * Initiate a subscription upgrade
     */
    public function initiateUpgrade($ownerId, $newPlanId, $amount = null)
    {
        try {
            DB::beginTransaction();

            $currentSubscription = OwnerSubscription::where('owner_id', $ownerId)
                ->where('status', 'active')
                ->first();

            if (!$currentSubscription) {
                throw new \Exception('No active subscription found for this owner');
            }

            // Check if already upgrading
            if ($currentSubscription->isUpgrading()) {
                throw new \Exception('Upgrade already in progress');
            }

            // Get the requested plan
            $requestedPlan = SubscriptionPlan::findOrFail($newPlanId);
            if (!$requestedPlan->is_active) {
                throw new \Exception('Selected plan is not available');
            }

            // Calculate upgrade amount (difference between new and current plan price unless provided)
            $currentPrice = (float) ($currentSubscription->plan->price ?? 0);
            $requestedPrice = (float) ($requestedPlan->price ?? 0);
            $calculatedAmount = $amount !== null ? (float)$amount : max(0.0, $requestedPrice - $currentPrice);

            // Create upgrade request
            $upgradeRequest = SubscriptionUpgradeRequest::create([
                'owner_id' => $ownerId,
                'current_subscription_id' => $currentSubscription->id,
                'requested_plan_id' => $newPlanId,
                'upgrade_type' => 'upgrade',
                'status' => 'pending',
                'amount' => $calculatedAmount,
                'notes' => "Upgrade from {$currentSubscription->plan->name} to {$requestedPlan->name}"
            ]);

            // Mark subscription as upgrading
            $currentSubscription->update([
                'is_upgrading' => true,
                'upgrade_request_id' => $upgradeRequest->id
            ]);

            // Create upgrade invoice
            $invoice = Billing::createUpgradeInvoice($upgradeRequest);

            // Update upgrade request with invoice
            $upgradeRequest->update(['invoice_id' => $invoice->id]);

            DB::commit();

            Log::info('Subscription upgrade initiated', [
                'owner_id' => $ownerId,
                'current_plan' => $currentSubscription->plan->name,
                'requested_plan' => $requestedPlan->name,
                'upgrade_request_id' => $upgradeRequest->id,
                'invoice_id' => $invoice->id
            ]);

            return [
                'success' => true,
                'upgrade_request' => $upgradeRequest,
                'invoice' => $invoice,
                'message' => 'Upgrade request created successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to initiate subscription upgrade', [
                'owner_id' => $ownerId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Complete a subscription upgrade after payment
     */
    public function completeUpgrade($invoiceId)
    {
        try {
            DB::beginTransaction();

            $invoice = Billing::findOrFail($invoiceId);
            
            if ($invoice->status !== 'paid') {
                throw new \Exception('Invoice must be paid to complete upgrade');
            }

            $upgradeRequest = $invoice->upgradeRequest;
            if (!$upgradeRequest) {
                throw new \Exception('No upgrade request found for this invoice');
            }

            if ($upgradeRequest->status !== 'pending') {
                throw new \Exception('Upgrade request is not in pending status');
            }

            // Complete the upgrade
            $subscription = $upgradeRequest->currentSubscription;
            $subscription->completeUpgrade();

            DB::commit();

            Log::info('Subscription upgrade completed', [
                'owner_id' => $subscription->owner_id,
                'upgrade_request_id' => $upgradeRequest->id,
                'new_plan' => $subscription->plan->name
            ]);

            return [
                'success' => true,
                'subscription' => $subscription,
                'message' => 'Subscription upgraded successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete subscription upgrade', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel a subscription upgrade
     */
    public function cancelUpgrade($upgradeRequestId)
    {
        try {
            DB::beginTransaction();

            $upgradeRequest = SubscriptionUpgradeRequest::findOrFail($upgradeRequestId);
            
            if ($upgradeRequest->status !== 'pending') {
                throw new \Exception('Can only cancel pending upgrade requests');
            }

            // Mark as cancelled
            $upgradeRequest->markAsCancelled();

            // Remove upgrading flag from subscription
            $subscription = $upgradeRequest->currentSubscription;
            $subscription->update([
                'is_upgrading' => false,
                'upgrade_request_id' => null
            ]);

            // Cancel related invoice if exists
            if ($upgradeRequest->invoice_id) {
                $invoice = Billing::find($upgradeRequest->invoice_id);
                if ($invoice && $invoice->status === 'unpaid') {
                    $invoice->update(['status' => 'cancel']);
                }
            }

            DB::commit();

            Log::info('Subscription upgrade cancelled', [
                'upgrade_request_id' => $upgradeRequestId,
                'owner_id' => $upgradeRequest->owner_id
            ]);

            return [
                'success' => true,
                'message' => 'Upgrade request cancelled successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel subscription upgrade', [
                'upgrade_request_id' => $upgradeRequestId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get upgrade status for an owner
     */
    public function getUpgradeStatus($ownerId)
    {
        $subscription = OwnerSubscription::where('owner_id', $ownerId)
            ->with(['upgradeRequest.requestedPlan', 'plan'])
            ->first();

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'is_upgrading' => false
            ];
        }

        return [
            'has_subscription' => true,
            'current_plan' => $subscription->plan->name,
            'is_upgrading' => $subscription->isUpgrading(),
            'upgrade_request' => $subscription->upgradeRequest ? [
                'id' => $subscription->upgradeRequest->id,
                'requested_plan' => $subscription->upgradeRequest->requestedPlan->name,
                'status' => $subscription->upgradeRequest->status,
                'amount' => $subscription->upgradeRequest->amount,
                'invoice' => $subscription->upgradeRequest->invoice ? [
                    'id' => $subscription->upgradeRequest->invoice->id,
                    'invoice_number' => $subscription->upgradeRequest->invoice->invoice_number,
                    'status' => $subscription->upgradeRequest->invoice->status,
                    'amount' => $subscription->upgradeRequest->invoice->amount
                ] : null
            ] : null
        ];
    }
}
