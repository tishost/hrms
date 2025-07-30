<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'logo',
        'transaction_fee',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'transaction_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    public function getFormattedFeeAttribute()
    {
        if ($this->transaction_fee == 0) {
            return 'No Fee';
        }
        return $this->transaction_fee . '%';
    }

    public function calculateFee($amount)
    {
        return ($amount * $this->transaction_fee) / 100;
    }

    public function calculateTotalAmount($amount)
    {
        return $amount + $this->calculateFee($amount);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getGatewayUrlAttribute()
    {
        return $this->settings['gateway_url'] ?? null;
    }

    public function getMerchantIdAttribute()
    {
        return $this->settings['merchant_id'] ?? null;
    }

    public function getApiKeyAttribute()
    {
        return $this->settings['api_key'] ?? null;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?? ucfirst($this->code);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/payment-methods/' . $this->code . '.png');
    }
}
