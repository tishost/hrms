<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = [
        'label',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Scope to get only active charges
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '৳' . number_format($this->amount, 2);
    }

    /**
     * Get formatted created date safely
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : 'N/A';
    }

    /**
     * Get formatted updated date safely
     */
    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('M d, Y H:i') : 'N/A';
    }

    /**
     * Get formatted created date for display
     */
    public function getFormattedCreatedAtDisplayAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y H:i') : 'N/A';
    }
}
