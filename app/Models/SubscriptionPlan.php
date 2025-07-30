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
        'properties_limit',
        'units_limit',
        'tenants_limit',
        'sms_notification',
        'is_active',
        'features'
    ];

    protected $casts = [
        'sms_notification' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'array'
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
}
