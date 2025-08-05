<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'otp',
        'type',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
        'status', // sent, verified, failed, blocked
        'attempt_count',
        'is_suspicious',
        'abuse_score',
        'blocked_until',
        'reason',
        'user_id',
        'session_id',
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
        'blocked_until' => 'datetime',
        'attempt_count' => 'integer',
        'abuse_score' => 'integer',
    ];

    /**
     * Get user associated with this OTP log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get OTP record associated with this log
     */
    public function otp()
    {
        return $this->belongsTo(Otp::class, 'otp', 'otp');
    }

    /**
     * Check if this IP is blocked
     */
    public static function isIpBlocked($ipAddress)
    {
        return self::where('ip_address', $ipAddress)
            ->where('status', 'blocked')
            ->where('blocked_until', '>', now())
            ->exists();
    }

    /**
     * Check if this phone is blocked
     */
    public static function isPhoneBlocked($phone)
    {
        return self::where('phone', $phone)
            ->where('status', 'blocked')
            ->where('blocked_until', '>', now())
            ->exists();
    }

    /**
     * Get abuse score for IP address
     */
    public static function getIpAbuseScore($ipAddress, $hours = 24)
    {
        $logs = self::where('ip_address', $ipAddress)
            ->where('created_at', '>', now()->subHours($hours))
            ->get();

        $score = 0;
        
        // Failed attempts
        $score += $logs->where('status', 'failed')->count() * 10;
        
        // Suspicious activities
        $score += $logs->where('is_suspicious', true)->count() * 20;
        
        // Multiple OTP requests in short time
        $recentLogs = $logs->where('created_at', '>', now()->subMinutes(10));
        $score += $recentLogs->count() * 5;
        
        // Multiple phone numbers from same IP
        $uniquePhones = $logs->pluck('phone')->unique()->count();
        if ($uniquePhones > 5) {
            $score += ($uniquePhones - 5) * 15;
        }

        return $score;
    }

    /**
     * Get abuse score for phone number
     */
    public static function getPhoneAbuseScore($phone, $hours = 24)
    {
        $logs = self::where('phone', $phone)
            ->where('created_at', '>', now()->subHours($hours))
            ->get();

        $score = 0;
        
        // Failed attempts
        $score += $logs->where('status', 'failed')->count() * 10;
        
        // Multiple OTP requests in short time
        $recentLogs = $logs->where('created_at', '>', now()->subMinutes(5));
        $score += $recentLogs->count() * 8;
        
        // Multiple IPs for same phone
        $uniqueIps = $logs->pluck('ip_address')->unique()->count();
        if ($uniqueIps > 3) {
            $score += ($uniqueIps - 3) * 12;
        }

        return $score;
    }

    /**
     * Check if activity is suspicious
     */
    public static function isSuspiciousActivity($phone, $ipAddress)
    {
        $phoneScore = self::getPhoneAbuseScore($phone, 1);
        $ipScore = self::getIpAbuseScore($ipAddress, 1);
        
        return $phoneScore > 50 || $ipScore > 100;
    }

    /**
     * Block IP address
     */
    public static function blockIp($ipAddress, $duration = 60, $reason = 'Abuse detected')
    {
        self::where('ip_address', $ipAddress)
            ->update([
                'status' => 'blocked',
                'blocked_until' => now()->addMinutes($duration),
                'reason' => $reason
            ]);
    }

    /**
     * Block phone number
     */
    public static function blockPhone($phone, $duration = 60, $reason = 'Abuse detected')
    {
        self::where('phone', $phone)
            ->update([
                'status' => 'blocked',
                'blocked_until' => now()->addMinutes($duration),
                'reason' => $reason
            ]);
    }

    /**
     * Get suspicious activities report
     */
    public static function getSuspiciousActivitiesReport($hours = 24)
    {
        $logs = self::where('created_at', '>', now()->subHours($hours))
            ->where('is_suspicious', true)
            ->get();

        $report = [
            'total_suspicious' => $logs->count(),
            'blocked_ips' => $logs->where('status', 'blocked')->pluck('ip_address')->unique()->count(),
            'blocked_phones' => $logs->where('status', 'blocked')->pluck('phone')->unique()->count(),
            'top_suspicious_ips' => $logs->groupBy('ip_address')
                ->map(function ($group) {
                    return [
                        'ip' => $group->first()->ip_address,
                        'count' => $group->count(),
                        'score' => self::getIpAbuseScore($group->first()->ip_address, 24)
                    ];
                })
                ->sortByDesc('score')
                ->take(10),
            'top_suspicious_phones' => $logs->groupBy('phone')
                ->map(function ($group) {
                    return [
                        'phone' => $group->first()->phone,
                        'count' => $group->count(),
                        'score' => self::getPhoneAbuseScore($group->first()->phone, 24)
                    ];
                })
                ->sortByDesc('score')
                ->take(10),
        ];

        return $report;
    }
} 