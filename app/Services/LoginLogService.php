<?php

namespace App\Services;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
class LoginLogService
{

    /**
     * Log a login attempt
     */
    public function logLogin(Request $request, ?User $user = null, $status = 'success', $failureReason = null)
    {
        try {
            $deviceInfo = $this->getDeviceInfo($request);
            $locationInfo = $this->getLocationInfo($request);
            
            $loginData = [
                'user_id' => $user ? $user->id : null,
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $deviceInfo['device_type'],
                'platform' => $deviceInfo['platform'],
                'browser' => $deviceInfo['browser'],
                'browser_version' => $deviceInfo['browser_version'],
                'os' => $deviceInfo['os'],
                'os_version' => $deviceInfo['os_version'],
                'device_model' => $deviceInfo['device_model'],
                'location' => $locationInfo['location'],
                'city' => $locationInfo['city'],
                'state' => $locationInfo['state'],
                'country' => $locationInfo['country'],
                'timezone' => $locationInfo['timezone'],
                'status' => $status,
                'failure_reason' => $failureReason,
                'login_method' => $this->getLoginMethod($request),
                'app_version' => $request->header('App-Version'),
                'api_version' => $request->header('API-Version'),
                'additional_data' => $this->getAdditionalData($request),
                'login_at' => Carbon::now(),
            ];

            $log = LoginLog::create($loginData);
            \Log::info('Login log created successfully', ['log_id' => $log->id, 'user_id' => $user?->id, 'status' => $status]);
            return $log;
        } catch (\Exception $e) {
            \Log::error('Failed to create login log: ' . $e->getMessage(), [
                'user_id' => $user?->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Log a logout
     */
    public function logLogout(User $user, $sessionDuration = null)
    {
        $latestLogin = LoginLog::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($latestLogin) {
            $latestLogin->update([
                'logout_at' => Carbon::now(),
                'session_duration' => $sessionDuration ?? $latestLogin->login_at->diffInSeconds(Carbon::now())
            ]);
        }
    }

    /**
     * Get device information from request
     */
    protected function getDeviceInfo(Request $request)
    {
        $userAgent = $request->userAgent();
        
        // Check for Flutter app headers first
        $appVersion = $request->header('App-Version');
        $userAgentString = $request->header('User-Agent');
        $appType = $request->header('X-App-Type');
        $platform = $request->header('X-Platform');
        
        // Debug logging
        \Log::info('Device detection debug', [
            'user_agent' => $userAgent,
            'app_version' => $appVersion,
            'user_agent_header' => $userAgentString,
            'app_type' => $appType,
            'platform' => $platform,
            'all_headers' => $request->headers->all()
        ]);
        
        // Detect Flutter app - check multiple conditions
        if ($appVersion || 
            $appType === 'mobile' ||
            strpos($userAgentString, 'Flutter') !== false || 
            strpos($userAgentString, 'Dart') !== false ||
            strpos($userAgent, 'Flutter') !== false ||
            strpos($userAgent, 'Dart') !== false) {
            
            // Flutter app detection
            $deviceType = 'mobile';
            $platform = 'flutter';
            $browser = 'Flutter App';
            $os = 'Mobile';
            
            // Try to detect OS from user agent and headers
            if ($platform === 'android' || preg_match('/Android/i', $userAgentString) || preg_match('/Android/i', $userAgent)) {
                $platform = 'android';
                $os = 'Android';
            } elseif ($platform === 'ios' || preg_match('/iPhone|iPad|iPod/i', $userAgentString) || preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
                $platform = 'ios';
                $os = 'iOS';
            } else {
                // Default to android for mobile app
                $platform = 'android';
                $os = 'Android';
            }
            
            \Log::info('Flutter app detected', [
                'device_type' => $deviceType,
                'platform' => $platform,
                'browser' => $browser,
                'os' => $os
            ]);
            
            return [
                'device_type' => $deviceType,
                'platform' => $platform,
                'browser' => $browser,
                'browser_version' => $appVersion,
                'os' => $os,
                'os_version' => null,
                'device_model' => $this->getDeviceModel($userAgentString ?: $userAgent)
            ];
        }
        
        // Regular web browser detection
        $deviceType = 'web';
        $platform = 'web';
        $browser = 'Unknown';
        $os = 'Unknown';
        
        // Detect mobile devices
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $userAgent)) {
            $deviceType = 'mobile';
            if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
                $platform = 'ios';
                $os = 'iOS';
            } elseif (preg_match('/Android/i', $userAgent)) {
                $platform = 'android';
                $os = 'Android';
            }
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            $deviceType = 'tablet';
            $platform = 'ios';
            $os = 'iOS';
        }
        
        // Detect desktop OS
        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'desktop';
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X|Macintosh/i', $userAgent)) {
            $platform = 'desktop';
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'desktop';
            $os = 'Linux';
        }
        
        // Detect browser
        if (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        }

        return [
            'device_type' => $deviceType,
            'platform' => $platform,
            'browser' => $browser,
            'browser_version' => null,
            'os' => $os,
            'os_version' => null,
            'device_model' => $this->getDeviceModel($userAgent)
        ];
    }

    /**
     * Get device model from user agent
     */
    protected function getDeviceModel($userAgent)
    {
        // Extract device model from user agent
        if (preg_match('/iPhone|iPad|iPod/', $userAgent)) {
            return 'Apple Device';
        } elseif (preg_match('/Android/', $userAgent)) {
            if (preg_match('/Samsung/', $userAgent)) {
                return 'Samsung';
            } elseif (preg_match('/Huawei/', $userAgent)) {
                return 'Huawei';
            } elseif (preg_match('/Xiaomi/', $userAgent)) {
                return 'Xiaomi';
            } elseif (preg_match('/OnePlus/', $userAgent)) {
                return 'OnePlus';
            } else {
                return 'Android Device';
            }
        }
        
        return null;
    }

    /**
     * Get location information from IP
     */
    protected function getLocationInfo(Request $request)
    {
        $ip = $request->ip();
        
        // For local development, return default values
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return [
                'location' => 'Local Development',
                'city' => 'Local',
                'state' => 'Development',
                'country' => 'Local',
                'timezone' => 'UTC'
            ];
        }

        // Try to get location from IP using free API
        try {
            $locationData = $this->getLocationFromIP($ip);
            return $locationData;
        } catch (\Exception $e) {
            \Log::warning('Failed to get location from IP: ' . $e->getMessage());
            return [
                'location' => 'Unknown',
                'city' => null,
                'state' => null,
                'country' => null,
                'timezone' => 'UTC'
            ];
        }
    }

    /**
     * Get location from IP using free API
     */
    protected function getLocationFromIP($ip)
    {
        // Debug logging
        \Log::info('Getting location for IP: ' . $ip);
        
        // Try multiple IP geolocation services
        $services = [
            'ipapi' => "http://ip-api.com/json/{$ip}",
            'ipinfo' => "https://ipinfo.io/{$ip}/json",
            'freegeoip' => "http://freegeoip.app/json/{$ip}"
        ];
        
        foreach ($services as $service => $url) {
            try {
                \Log::info("Trying {$service} service for IP: {$ip}");
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'HRMS-App/1.0'
                    ]
                ]);
                
                $response = @file_get_contents($url, false, $context);
                
                if ($response === false) {
                    continue;
                }
                
                $data = json_decode($response, true);
                
                if (!$data) {
                    continue;
                }
                
                // Parse response based on service
                $locationData = $this->parseLocationData($data, $service);
                
                if ($locationData) {
                    \Log::info("Location data from {$service}: " . json_encode($locationData));
                    return $locationData;
                }
                
            } catch (\Exception $e) {
                \Log::warning("Failed to get location from {$service}: " . $e->getMessage());
                continue;
            }
        }
        
        throw new \Exception('All location services failed');
    }
    
    /**
     * Parse location data from different services
     */
    protected function parseLocationData($data, $service)
    {
        switch ($service) {
            case 'ipapi':
                if (isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'location' => ($data['city'] ?? 'Unknown') . ', ' . ($data['country'] ?? 'Unknown'),
                        'city' => $data['city'] ?? null,
                        'state' => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                        'timezone' => $data['timezone'] ?? 'UTC'
                    ];
                }
                break;
                
            case 'ipinfo':
                if (isset($data['city']) || isset($data['country'])) {
                    return [
                        'location' => ($data['city'] ?? 'Unknown') . ', ' . ($data['country'] ?? 'Unknown'),
                        'city' => $data['city'] ?? null,
                        'state' => $data['region'] ?? null,
                        'country' => $data['country'] ?? null,
                        'timezone' => $data['timezone'] ?? 'UTC'
                    ];
                }
                break;
                
            case 'freegeoip':
                if (isset($data['city']) || isset($data['country_name'])) {
                    return [
                        'location' => ($data['city'] ?? 'Unknown') . ', ' . ($data['country_name'] ?? 'Unknown'),
                        'city' => $data['city'] ?? null,
                        'state' => $data['region_name'] ?? null,
                        'country' => $data['country_name'] ?? null,
                        'timezone' => $data['time_zone'] ?? 'UTC'
                    ];
                }
                break;
        }
        
        return null;
    }

    /**
     * Get login method
     */
    protected function getLoginMethod(Request $request)
    {
        if ($request->has('phone')) {
            return 'phone';
        } elseif ($request->has('social_provider')) {
            return 'social';
        } else {
            return 'email';
        }
    }

    /**
     * Get additional data from request
     */
    protected function getAdditionalData(Request $request)
    {
        $data = [];
        
        // Add request headers that might be useful
        $headers = ['Accept', 'Accept-Language', 'Accept-Encoding', 'Connection'];
        foreach ($headers as $header) {
            if ($request->header($header)) {
                $data[$header] = $request->header($header);
            }
        }
        
        // Add request data
        $data['request_data'] = $request->only(['email', 'phone']);
        
        return $data;
    }

    /**
     * Get login statistics
     */
    public function getLoginStatistics($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $stats = LoginLog::where('login_at', '>=', $startDate)
            ->selectRaw('
                DATE(login_at) as date,
                COUNT(*) as total_logins,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful_logins,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_logins,
                SUM(CASE WHEN status = "blocked" THEN 1 ELSE 0 END) as blocked_logins,
                SUM(CASE WHEN device_type = "web" THEN 1 ELSE 0 END) as web_logins,
                SUM(CASE WHEN device_type = "mobile" THEN 1 ELSE 0 END) as mobile_logins,
                SUM(CASE WHEN device_type = "tablet" THEN 1 ELSE 0 END) as tablet_logins
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return $stats;
    }

    /**
     * Get suspicious login attempts
     */
    public function getSuspiciousLogins($hours = 24)
    {
        $startTime = Carbon::now()->subHours($hours);
        
        return LoginLog::where('login_at', '>=', $startTime)
            ->where('status', 'failed')
            ->select('ip_address')
            ->selectRaw('COUNT(*) as failed_attempts')
            ->groupBy('ip_address')
            ->having('failed_attempts', '>', 5)
            ->get();
    }

    /**
     * Get user login history
     */
    public function getUserLoginHistory(User $user, $limit = 10)
    {
        return LoginLog::where('user_id', $user->id)
            ->orderBy('login_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions()
    {
        return LoginLog::where('status', 'success')
            ->whereNull('logout_at')
            ->where('login_at', '>=', Carbon::now()->subDay())
            ->with('user')
            ->get();
    }

    /**
     * Block IP address
     */
    public function blockIpAddress($ipAddress, $reason = 'Multiple failed attempts')
    {
        // Log the block
        LoginLog::create([
            'ip_address' => $ipAddress,
            'status' => 'blocked',
            'failure_reason' => $reason,
            'login_at' => Carbon::now(),
            'device_type' => 'unknown',
            'platform' => 'unknown'
        ]);
    }

    /**
     * Check if IP is blocked
     */
    public function isIpBlocked($ipAddress)
    {
        $recentBlock = LoginLog::where('ip_address', $ipAddress)
            ->where('status', 'blocked')
            ->where('login_at', '>=', Carbon::now()->subHour())
            ->first();
        
        return $recentBlock !== null;
    }
} 