<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billing';

    protected $fillable = [
        'owner_id',
        'subscription_id',
        'amount',
        'status',
        'due_date',
        'paid_date',
        'invoice_number',
        'payment_method',
        'transaction_id',
        'payment_method_id',
        'transaction_fee',
        'net_amount',
        'upgrade_request_id',
        'billing_type'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'transaction_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'billing_type' => 'string'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function subscription()
    {
        return $this->belongsTo(OwnerSubscription::class, 'subscription_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function upgradeRequest()
    {
        return $this->belongsTo(SubscriptionUpgradeRequest::class, 'upgrade_request_id');
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->isPending() && $this->due_date->isPast();
    }

    public function getFormattedAmountAttribute()
    {
        return '৳' . number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Upgrade Billing Methods
    public static function createUpgradeInvoice($upgradeRequest)
    {
        return self::create([
            'owner_id' => $upgradeRequest->owner_id,
            'subscription_id' => $upgradeRequest->current_subscription_id,
            'upgrade_request_id' => $upgradeRequest->id,
            'amount' => $upgradeRequest->amount,
            'status' => 'unpaid',
            'billing_type' => 'upgrade',
            'due_date' => now()->addDays(7),
            'invoice_number' => 'UPG-' . date('Y') . '-' . str_pad($upgradeRequest->id, 6, '0', STR_PAD_LEFT)
        ]);
    }

    public function isUpgradeBilling()
    {
        return $this->billing_type === 'upgrade';
    }

    public function isSubscriptionBilling()
    {
        return $this->billing_type === 'subscription';
    }

    public function isRenewalBilling()
    {
        return $this->billing_type === 'renewal';
    }
}
