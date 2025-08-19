<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'url',
        'is_active',
        'show_on_owner_dashboard',
        'show_on_tenant_dashboard',
        'start_date',
        'end_date',
        'display_order',
        'clicks_count',
        'impressions_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_owner_dashboard' => 'boolean',
        'show_on_tenant_dashboard' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'clicks_count' => 'integer',
        'impressions_count' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Scope to get only active ads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get ads for owner dashboard
     */
    public function scopeForOwnerDashboard($query)
    {
        return $query->where('show_on_owner_dashboard', true)
                    ->where('is_active', true)
                    ->where('start_date', '<=', Carbon::today())
                    ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Scope to get ads for tenant dashboard
     */
    public function scopeForTenantDashboard($query)
    {
        return $query->where('show_on_tenant_dashboard', true)
                    ->where('is_active', true)
                    ->where('start_date', '<=', Carbon::today())
                    ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Scope to get ads within date range
     */
    public function scopeWithinDateRange($query)
    {
        return $query->where('start_date', '<=', Carbon::today())
                    ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Check if ad is currently active and within date range
     */
    public function isCurrentlyActive(): bool
    {
        $today = Carbon::today();
        return $this->is_active && 
               $this->start_date <= $today && 
               $this->end_date >= $today;
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }

    /**
     * Increment click count
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    /**
     * Increment impression count
     */
    public function incrementImpressions(): void
    {
        $this->increment('impressions_count');
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $today = Carbon::today();
        
        if ($this->start_date > $today) {
            return 'Scheduled';
        }
        
        if ($this->end_date < $today) {
            return 'Expired';
        }
        
        return 'Active';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'danger';
        }

        $today = Carbon::today();
        
        if ($this->start_date > $today) {
            return 'warning';
        }
        
        if ($this->end_date < $today) {
            return 'secondary';
        }
        
        return 'success';
    }
}
