<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'billing_cycle',
        'duration_days',
        'properties_limit',
        'units_limit',
        'tenants_limit',
        'sms_notification',
        'sms_credit',
        'is_active',
        'is_popular',
        'features',
        'features_css'
    ];

    protected $casts = [
        'sms_notification' => 'boolean',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'features' => 'array',
        'features_css' => 'array'
    ];

    public function subscriptions()
    {
        return $this->hasMany(OwnerSubscription::class, 'plan_id');
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return '৳' . number_format($this->price);
    }

    public function getPropertiesLimitTextAttribute()
    {
        if ($this->properties_limit == -1) {
            return 'Unlimited';
        }
        return $this->properties_limit;
    }

    public function getUnitsLimitTextAttribute()
    {
        if ($this->units_limit == -1) {
            return 'Unlimited';
        }
        return $this->units_limit;
    }

    public function getTenantsLimitTextAttribute()
    {
        if ($this->tenants_limit == -1) {
            return 'Unlimited';
        }
        return $this->tenants_limit;
    }

    /**
     * Get billing cycle text
     */
    public function getBillingCycleTextAttribute()
    {
        switch ($this->billing_cycle) {
            case 'monthly':
                return 'Monthly';
            case 'yearly':
                return 'Yearly';
            case 'lifetime':
                return 'Lifetime';
            default:
                return 'Monthly';
        }
    }

    /**
     * Get formatted price with billing cycle
     */
    public function getFormattedPriceWithCycleAttribute()
    {
        if ($this->price == 0) {
            return 'Free';
        }

        $price = '৳' . number_format($this->price);
        
        switch ($this->billing_cycle) {
            case 'monthly':
                return $price . '/month';
            case 'yearly':
                return $price . '/year';
            case 'lifetime':
                return $price . ' (one-time)';
            default:
                return $price . '/month';
        }
    }

    /**
     * Check if plan is lifetime
     */
    public function isLifetime()
    {
        return $this->billing_cycle === 'lifetime';
    }

    /**
     * Get duration in days
     */
    public function getDurationInDays()
    {
        switch ($this->billing_cycle) {
            case 'monthly':
                return 30;
            case 'yearly':
                return 365;
            case 'lifetime':
                return 36500; // 100 years (effectively lifetime)
            default:
                return $this->duration_days ?? 30;
        }
    }
}
