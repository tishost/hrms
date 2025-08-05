<?php

namespace App\Services;

use App\Models\OtpLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpSecurityService
{
    /**
     * Log OTP activity
     */
    public static function logOtpActivity($phone, $otp, $type, Request $request, $status = 'sent', $userId = null)
    {
        try {
            $deviceInfo = self::getDeviceInfo($request);
            $location = self::getLocation($request);
            
            $isSuspicious = self::isSuspiciousActivity($phone, $request->ip());
            $abuseScore = self::calculateAbuseScore($phone, $request->ip());
            
            $log = OtpLog::create([
                'phone' => $phone,
                'otp' => $otp,
                'type' => $type,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_info' => json_encode($deviceInfo),
                'location' => $location,
                'status' => $status,
                'is_suspicious' => $isSuspicious,
                'abuse_score' => $abuseScore,
                'user_id' => $userId,
                'session_id' => $request->session()->getId(),
            ]);

            // Check if we need to block
            if ($abuseScore > 100) {
                self::blockIp($request->ip(), 60, 'High abuse score: ' . $abuseScore);
                Log::warning("IP blocked due to high abuse score: {$request->ip()}, Score: {$abuseScore}");
            }

            if ($abuseScore > 80) {
                self::blockPhone($phone, 30, 'High abuse score: ' . $abuseScore);
                Log::warning("Phone blocked due to high abuse score: {$phone}, Score: {$abuseScore}");
            }

            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to log OTP activity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if IP is blocked
     */
    public static function isIpBlocked($ipAddress)
    {
        return OtpLog::isIpBlocked($ipAddress);
    }

    /**
     * Check if phone is blocked
     */
    public static function isPhoneBlocked($phone)
    {
        return OtpLog::isPhoneBlocked($phone);
    }

    /**
     * Check if activity is suspicious
     */
    public static function isSuspiciousActivity($phone, $ipAddress)
    {
        return OtpLog::isSuspiciousActivity($phone, $ipAddress);
    }

    /**
     * Calculate abuse score
     */
    public static function calculateAbuseScore($phone, $ipAddress)
    {
        $phoneScore = OtpLog::getPhoneAbuseScore($phone, 1);
        $ipScore = OtpLog::getIpAbuseScore($ipAddress, 1);
        
        return max($phoneScore, $ipScore);
    }

    /**
     * Block IP address
     */
    public static function blockIp($ipAddress, $duration = 60, $reason = 'Abuse detected')
    {
        OtpLog::blockIp($ipAddress, $duration, $reason);
        Log::warning("IP blocked: {$ipAddress}, Duration: {$duration} minutes, Reason: {$reason}");
    }

    /**
     * Block phone number
     */
    public static function blockPhone($phone, $duration = 60, $reason = 'Abuse detected')
    {
        OtpLog::blockPhone($phone, $duration, $reason);
        Log::warning("Phone blocked: {$phone}, Duration: {$duration} minutes, Reason: {$reason}");
    }

    /**
     * Get device information
     */
    private static function getDeviceInfo(Request $request)
    {
        $userAgent = $request->userAgent();
        
        return [
            'browser' => self::getBrowser($userAgent),
            'os' => self::getOS($userAgent),
            'device' => self::getDevice($userAgent),
            'is_mobile' => self::isMobile($userAgent),
            'is_bot' => self::isBot($userAgent),
        ];
    }

    /**
     * Get browser from user agent
     */
    private static function getBrowser($userAgent)
    {
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        if (preg_match('/Opera/i', $userAgent)) return 'Opera';
        return 'Unknown';
    }

    /**
     * Get OS from user agent
     */
    private static function getOS($userAgent)
    {
        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Mac/i', $userAgent)) return 'Mac';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        if (preg_match('/iOS/i', $userAgent)) return 'iOS';
        return 'Unknown';
    }

    /**
     * Get device type
     */
    private static function getDevice($userAgent)
    {
        if (preg_match('/Mobile/i', $userAgent)) return 'Mobile';
        if (preg_match('/Tablet/i', $userAgent)) return 'Tablet';
        return 'Desktop';
    }

    /**
     * Check if mobile
     */
    private static function isMobile($userAgent)
    {
        return preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent);
    }

    /**
     * Check if bot
     */
    private static function isBot($userAgent)
    {
        return preg_match('/bot|crawler|spider|crawling/i', $userAgent);
    }

    /**
     * Get location (basic implementation)
     */
    private static function getLocation(Request $request)
    {
        // In production, you might want to use a geolocation service
        // For now, we'll just return the IP
        return $request->ip();
    }

    /**
     * Get security report
     */
    public static function getSecurityReport($hours = 24)
    {
        return OtpLog::getSuspiciousActivitiesReport($hours);
    }

    /**
     * Get abuse statistics
     */
    public static function getAbuseStatistics($hours = 24)
    {
        $logs = OtpLog::where('created_at', '>', now()->subHours($hours))->get();
        
        return [
            'total_otp_requests' => $logs->count(),
            'successful_otps' => $logs->where('status', 'verified')->count(),
            'failed_otps' => $logs->where('status', 'failed')->count(),
            'blocked_requests' => $logs->where('status', 'blocked')->count(),
            'suspicious_activities' => $logs->where('is_suspicious', true)->count(),
            'unique_ips' => $logs->pluck('ip_address')->unique()->count(),
            'unique_phones' => $logs->pluck('phone')->unique()->count(),
            'top_abusive_ips' => $logs->groupBy('ip_address')
                ->map(function ($group) {
                    return [
                        'ip' => $group->first()->ip_address,
                        'count' => $group->count(),
                        'score' => OtpLog::getIpAbuseScore($group->first()->ip_address, 24)
                    ];
                })
                ->sortByDesc('score')
                ->take(10),
            'top_abusive_phones' => $logs->groupBy('phone')
                ->map(function ($group) {
                    return [
                        'phone' => $group->first()->phone,
                        'count' => $group->count(),
                        'score' => OtpLog::getPhoneAbuseScore($group->first()->phone, 24)
                    ];
                })
                ->sortByDesc('score')
                ->take(10),
        ];
    }
} 