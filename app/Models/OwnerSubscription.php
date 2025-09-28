<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\NotificationHelper;
use App\Services\PackageLimitService;

class OwnerSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'plan_id',
        'status',
        'start_date',
        'end_date',
        'auto_renew',
        'sms_credits',
        'upgrade_request_id',
        'previous_plan_id',
        'upgrade_date',
        'is_upgrading'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'status' => 'string',
        'upgrade_date' => 'datetime',
        'is_upgrading' => 'boolean'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function billing()
    {
        return $this->hasMany(Billing::class, 'subscription_id');
    }

    public function upgradeRequest()
    {
        return $this->belongsTo(SubscriptionUpgradeRequest::class, 'upgrade_request_id');
    }

    public function previousPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'previous_plan_id');
    }

    public function isActive()
    {
        return $this->status === 'active'
            && (
                // Treat null end_date as lifetime (e.g., free package)
                !$this->end_date || $this->end_date->isFuture()
            );
    }

    public function isExpired()
    {
        return $this->status === 'expired' || ($this->end_date && $this->end_date->isPast());
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }

    public function daysUntilExpiry()
    {
        if (!$this->end_date) {
            return null;
        }
        return now()->diffInDays($this->end_date, false);
    }

    public function canAddProperty()
    {
        if ($this->plan->properties_limit == -1) {
            return true;
        }

        $currentCount = $this->owner->properties()->count();
        return $currentCount < $this->plan->properties_limit;
    }

    public function canAddUnit()
    {
        if ($this->plan->units_limit == -1) {
            return true;
        }

        $currentCount = $this->owner->units()->count();
        return $currentCount < $this->plan->units_limit;
    }

    public function canAddTenant()
    {
        if ($this->plan->tenants_limit == -1) {
            return true;
        }

        $currentCount = $this->owner->tenants()->count();
        return $currentCount < $this->plan->tenants_limit;
    }

    public function canSendSms()
    {
        // If plan includes SMS notifications, always allow
        if ($this->plan->sms_notification) {
            return true;
        }

        // Check if owner has SMS credits
        return $this->sms_credits > 0;
    }

    public function deductSmsCredit()
    {
        // If plan includes SMS, don't deduct credits
        if ($this->plan->sms_notification) {
            return true;
        }

        // Deduct credit if available
        if ($this->sms_credits > 0) {
            $this->decrement('sms_credits');
            return true;
        }

        return false;
    }

    public function addSmsCredits($credits)
    {
        $this->increment('sms_credits', $credits);
        return $this->sms_credits;
    }

    public function getSmsCreditsRemaining()
    {
        return $this->sms_credits;
    }

    public function getSmsUsageStats()
    {
        return [
            'credits_remaining' => $this->sms_credits,
            'plan_includes_sms' => $this->plan->sms_notification,
            'can_send_sms' => $this->canSendSms(),
            'plan_name' => $this->plan->name
        ];
    }

    public function generateInvoice()
    {
        // Generate unique invoice number - S + Year (last 2 digits) + 5 digit sequence
        $year = substr(date('Y'), -2);
        $lastBilling = \App\Models\Billing::where('invoice_number', 'like', 'S' . $year . '%')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastBilling) {
            $lastSequence = (int) substr($lastBilling->invoice_number, 3);
            $sequence = str_pad($lastSequence + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $sequence = '00001';
        }
        
        $invoiceNumber = 'S' . $year . $sequence;

        // Log before creating billing
        \Log::info('Generating invoice', [
            'subscription_id' => $this->id,
            'owner_id' => $this->owner_id,
            'plan_id' => $this->plan_id,
            'plan_price' => $this->plan->price ?? 'N/A',
            'invoice_number' => $invoiceNumber
        ]);

        // Create billing record
        $billing = Billing::create([
            'owner_id' => $this->owner_id,
            'subscription_id' => $this->id,
            'amount' => $this->plan->price,
            'status' => 'unpaid',
            'due_date' => now()->addDays(7), // 7 days to pay
            'invoice_number' => $invoiceNumber,
            'payment_method' => null,
            'transaction_id' => null,
            'transaction_fee' => 0,
            'net_amount' => $this->plan->price
        ]);

        // Log after creating billing
        \Log::info('Invoice created', [
            'billing_id' => $billing->id,
            'owner_id' => $billing->owner_id,
            'subscription_id' => $billing->subscription_id,
            'amount' => $billing->amount,
            'invoice_number' => $billing->invoice_number
        ]);

        return $billing;
    }

    public function activateAfterPayment()
    {
        $this->update([
            'status' => 'active',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString()
        ]);

        // Initialize package limits for the owner
        try {
            $packageLimitService = new PackageLimitService();
            $packageLimitService->initializeLimits($this->owner);
        } catch (\Exception $e) {
            \Log::error('Package limits initialization failed: ' . $e->getMessage());
        }

        // Send subscription activation notification
        try {
            $user = $this->owner->user;
            NotificationHelper::sendSubscriptionActivation($user, $this->plan->name, $this->end_date);
        } catch (\Exception $e) {
            \Log::error('Subscription activation notification failed: ' . $e->getMessage());
        }
    }

    public function isPendingPayment()
    {
        return $this->status === 'pending' && $this->billing()->whereIn('status', ['pending', 'unpaid', 'fail', 'cancel'])->exists();
    }

    public function getPendingInvoice()
    {
        return $this->billing()->whereIn('status', ['pending', 'unpaid', 'fail', 'cancel'])->first();
    }

    // Upgrade Methods
    public function requestUpgrade($newPlanId, $amount = null)
    {
        // Create upgrade request
        $upgradeRequest = SubscriptionUpgradeRequest::create([
            'owner_id' => $this->owner_id,
            'current_subscription_id' => $this->id,
            'requested_plan_id' => $newPlanId,
            'upgrade_type' => 'upgrade',
            'status' => 'pending',
            'amount' => $amount ?? SubscriptionPlan::find($newPlanId)->price
        ]);

        // Mark subscription as upgrading
        $this->update([
            'is_upgrading' => true,
            'upgrade_request_id' => $upgradeRequest->id
        ]);

        return $upgradeRequest;
    }

    public function completeUpgrade()
    {
        if (!$this->upgradeRequest) {
            throw new \Exception('No upgrade request found');
        }

        // Store current plan as previous
        $this->update([
            'previous_plan_id' => $this->plan_id,
            'plan_id' => $this->upgradeRequest->requested_plan_id,
            'is_upgrading' => false,
            'upgrade_date' => now(),
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        // Mark upgrade request as completed
        $this->upgradeRequest->markAsCompleted();

        return $this;
    }

    public function cancelUpgrade()
    {
        if (!$this->upgradeRequest) {
            throw new \Exception('No upgrade request found');
        }

        // Mark upgrade request as cancelled
        $this->upgradeRequest->markAsCancelled();

        // Remove upgrading flag
        $this->update([
            'is_upgrading' => false,
            'upgrade_request_id' => null
        ]);

        return $this;
    }

    public function isUpgrading()
    {
        return $this->is_upgrading && $this->upgradeRequest && $this->upgradeRequest->isPending();
    }

    public function hasUpgradeRequest()
    {
        return $this->upgradeRequest !== null;
    }
}
