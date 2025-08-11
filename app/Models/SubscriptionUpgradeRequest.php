<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUpgradeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'current_subscription_id',
        'requested_plan_id',
        'upgrade_type',
        'status',
        'amount',
        'invoice_id',
        'notes'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function currentSubscription()
    {
        return $this->belongsTo(OwnerSubscription::class, 'current_subscription_id');
    }

    public function requestedPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'requested_plan_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Billing::class, 'invoice_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now()
        ]);
    }

    public function markAsCancelled()
    {
        $this->update([
            'status' => 'cancelled',
            'processed_at' => now()
        ]);
    }
}
