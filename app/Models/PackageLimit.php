<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'limit_type',
        'current_usage',
        'max_limit',
        'reset_date',
        'reset_frequency',
        'is_active'
    ];

    protected $casts = [
        'reset_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Check if limit is exceeded
     */
    public function isExceeded()
    {
        return $this->current_usage >= $this->max_limit;
    }

    /**
     * Get remaining limit
     */
    public function getRemaining()
    {
        return max(0, $this->max_limit - $this->current_usage);
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentage()
    {
        if ($this->max_limit == 0) return 0;
        return min(100, ($this->current_usage / $this->max_limit) * 100);
    }

    /**
     * Increment usage
     */
    public function incrementUsage($amount = 1)
    {
        $this->increment('current_usage', $amount);
        $this->refresh();
    }

    /**
     * Decrement usage
     */
    public function decrementUsage($amount = 1)
    {
        $this->decrement('current_usage', $amount);
        $this->refresh();
    }

    /**
     * Reset usage based on frequency
     */
    public function resetUsage()
    {
        $this->update([
            'current_usage' => 0,
            'reset_date' => $this->calculateNextResetDate()
        ]);
    }

    /**
     * Calculate next reset date
     */
    private function calculateNextResetDate()
    {
        switch ($this->reset_frequency) {
            case 'monthly':
                return now()->addMonth();
            case 'yearly':
                return now()->addYear();
            case 'never':
                return $this->reset_date;
            default:
                return now()->addMonth();
        }
    }

    /**
     * Check if reset is due
     */
    public function isResetDue()
    {
        return $this->reset_date && $this->reset_date->isPast();
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor()
    {
        $percentage = $this->getUsagePercentage();

        if ($percentage >= 90) return 'danger';
        if ($percentage >= 75) return 'warning';
        if ($percentage >= 50) return 'info';
        return 'success';
    }
}
