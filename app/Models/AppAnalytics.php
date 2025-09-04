<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppAnalytics extends Model
{
    use HasFactory;

    protected $table = 'app_analytics';

    protected $fillable = [
        'event_type',
        'device_type',
        'os_version',
        'app_version',
        'device_model',
        'manufacturer',
        'screen_resolution',
        'user_id',
        'additional_data',
        'session_id',
        'ip_address',
        'user_agent',
        'event_timestamp',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'event_timestamp' => 'datetime',
    ];

    /**
     * Get the user that owns the analytics record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by event type
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for filtering by device type
     */
    public function scopeDeviceType($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get analytics summary for dashboard
     */
    public static function getDashboardSummary()
    {
        $now = now();
        $lastMonth = $now->subMonth();
        $lastWeek = $now->subWeek();

        return [
            'total_events' => self::count(),
            'events_this_month' => self::whereMonth('created_at', $now->month)->count(),
            'events_this_week' => self::where('created_at', '>=', $lastWeek)->count(),
            'unique_users' => self::distinct('user_id')->count(),
            'device_distribution' => self::selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->get()
                ->pluck('count', 'device_type')
                ->toArray(),
            'event_distribution' => self::selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->get()
                ->pluck('count', 'event_type')
                ->toArray(),
        ];
    }

    /**
     * Get device analytics data
     */
    public static function getDeviceAnalytics($days = 30)
    {
        $startDate = now()->subDays($days);

        return [
            'device_types' => self::where('created_at', '>=', $startDate)
                ->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->get()
                ->pluck('count', 'device_type')
                ->toArray(),
            'os_versions' => self::where('created_at', '>=', $startDate)
                ->whereNotNull('os_version')
                ->selectRaw('os_version, COUNT(*) as count')
                ->groupBy('os_version')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'os_version')
                ->toArray(),
            'app_versions' => self::where('created_at', '>=', $startDate)
                ->whereNotNull('app_version')
                ->selectRaw('app_version, COUNT(*) as count')
                ->groupBy('app_version')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'app_version')
                ->toArray(),
            'manufacturers' => self::where('created_at', '>=', $startDate)
                ->whereNotNull('manufacturer')
                ->selectRaw('manufacturer, COUNT(*) as count')
                ->groupBy('manufacturer')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'manufacturer')
                ->toArray(),
        ];
    }

    /**
     * Get installation trends
     */
    public static function getInstallationTrends($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $dailyInstallations = collect();
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $installations = self::where('event_type', 'app_install')
                ->whereDate('created_at', $date)
                ->count();
            
            $dailyInstallations->push([
                'date' => $date->format('M d'),
                'installations' => $installations,
                'day' => $date->format('D'),
                'date_iso' => $date->format('Y-m-d')
            ]);
        }
        
        return [
            'daily_installations' => $dailyInstallations,
            'total_installations' => $dailyInstallations->sum('installations'),
            'average_installations' => $dailyInstallations->sum('installations') / max($days, 1),
            'period_days' => $days,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ];
    }

    /**
     * Get real-time device stats
     */
    public static function getRealTimeStats()
    {
        $now = now();
        $today = $now->startOfDay();
        $currentHour = $now->hour;

        // Hourly device activity for today
        $hourlyActivity = collect();
        for ($i = 0; $i < 24; $i++) {
            $hour = $today->copy()->addHours($i);
            $activeDevices = self::where('created_at', '>=', $hour)
                ->where('created_at', '<', $hour->copy()->addHour())
                ->distinct('user_id')
                ->count();
            
            $hourlyActivity->push([
                'hour' => $hour->format('H:00'),
                'active_devices' => $activeDevices,
                'hour_num' => $i
            ]);
        }

        // Current device status
        $currentStatus = [
            'online_devices' => self::where('created_at', '>=', $now->subMinutes(5))->distinct('user_id')->count(),
            'offline_devices' => self::where('created_at', '<', $now->subMinutes(30))->distinct('user_id')->count(),
            'new_installations_today' => self::where('event_type', 'app_install')->whereDate('created_at', $today)->count(),
            'active_sessions' => self::where('created_at', '>=', $now->subMinutes(15))->distinct('session_id')->count(),
        ];

        return [
            'hourly_device_activity' => $hourlyActivity,
            'current_device_status' => $currentStatus,
            'current_hour' => $currentHour,
            'timestamp' => $now->toISOString()
        ];
    }
}
