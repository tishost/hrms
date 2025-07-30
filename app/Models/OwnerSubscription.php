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
        'sms_credits'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'status' => 'string'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function billing()
    {
        return $this->hasMany(Billing::class, 'subscription_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date && $this->end_date->isFuture();
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
        if ($this->plan->sms_notification) {
            return true;
        }

        return $this->sms_credits > 0;
    }

    public function deductSmsCredit()
    {
        if (!$this->plan->sms_notification && $this->sms_credits > 0) {
            $this->decrement('sms_credits');
            return true;
        }

        return false;
    }

    public function generateInvoice()
    {
        // Generate unique invoice number
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);

        // Create billing record
        $billing = Billing::create([
            'owner_id' => $this->owner_id,
            'subscription_id' => $this->id,
            'amount' => $this->plan->price,
            'status' => 'pending',
            'due_date' => now()->addDays(7), // 7 days to pay
            'invoice_number' => $invoiceNumber,
            'payment_method' => null,
            'transaction_id' => null,
            'transaction_fee' => 0,
            'net_amount' => $this->plan->price
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
        return $this->status === 'pending' && $this->billing()->where('status', 'pending')->exists();
    }

    public function getPendingInvoice()
    {
        return $this->billing()->where('status', 'pending')->first();
    }
}
