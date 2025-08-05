<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LoginLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'browser',
        'browser_version',
        'os',
        'os_version',
        'device_model',
        'location',
        'city',
        'state',
        'country',
        'timezone',
        'status',
        'failure_reason',
        'login_method',
        'app_version',
        'api_version',
        'additional_data',
        'login_at',
        'logout_at',
        'session_duration'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'additional_data' => 'array'
    ];

    /**
     * Get the user that owns the login log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful logins
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logins
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for blocked logins
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /**
     * Scope for web logins
     */
    public function scopeWeb($query)
    {
        return $query->where('device_type', 'web');
    }

    /**
     * Scope for mobile app logins
     */
    public function scopeMobile($query)
    {
        return $query->where('device_type', 'mobile');
    }

    /**
     * Scope for tablet logins
     */
    public function scopeTablet($query)
    {
        return $query->where('device_type', 'tablet');
    }

    /**
     * Scope for recent logins (last 24 hours)
     */
    public function scopeRecent($query)
    {
        return $query->where('login_at', '>=', Carbon::now()->subDay());
    }

    /**
     * Get session duration in human readable format
     */
    public function getSessionDurationAttribute($value)
    {
        if (!$value) return null;
        
        $hours = floor($value / 3600);
        $minutes = floor(($value % 3600) / 60);
        $seconds = $value % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m {$seconds}s";
        } elseif ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        } else {
            return "{$seconds}s";
        }
    }

    /**
     * Get device info as string
     */
    public function getDeviceInfoAttribute()
    {
        $parts = [];
        
        if ($this->browser) {
            $parts[] = $this->browser;
            if ($this->browser_version) {
                $parts[] = $this->browser_version;
            }
        }
        
        if ($this->os) {
            $parts[] = $this->os;
            if ($this->os_version) {
                $parts[] = $this->os_version;
            }
        }
        
        if ($this->device_model) {
            $parts[] = $this->device_model;
        }
        
        return implode(' / ', $parts) ?: 'Unknown Device';
    }

    /**
     * Get location as string
     */
    public function getLocationStringAttribute()
    {
        $parts = [];
        
        if ($this->city) {
            $parts[] = $this->city;
        }
        
        if ($this->state) {
            $parts[] = $this->state;
        }
        
        if ($this->country) {
            $parts[] = $this->country;
        }
        
        return implode(', ', $parts) ?: 'Unknown Location';
    }

    /**
     * Check if login is suspicious
     */
    public function getIsSuspiciousAttribute()
    {
        // Check for multiple failed attempts from same IP
        $failedAttempts = self::where('ip_address', $this->ip_address)
            ->where('status', 'failed')
            ->where('login_at', '>=', Carbon::now()->subHour())
            ->count();
        
        return $failedAttempts > 5;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'success':
                return 'badge-success';
            case 'failed':
                return 'badge-danger';
            case 'blocked':
                return 'badge-warning';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Get device type badge class
     */
    public function getDeviceTypeBadgeClassAttribute()
    {
        switch ($this->device_type) {
            case 'web':
                return 'badge-primary';
            case 'mobile':
                return 'badge-info';
            case 'tablet':
                return 'badge-warning';
            default:
                return 'badge-secondary';
        }
    }
} 