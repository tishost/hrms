<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantOtp extends Model
{
    protected $fillable = [
        'mobile',
        'otp',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public function isValid()
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}
