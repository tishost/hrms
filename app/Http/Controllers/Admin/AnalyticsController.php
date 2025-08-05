<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\SystemHelper;

class AnalyticsController extends Controller
{
    public function index()
    {
        // User Growth Analytics
        $userGrowth = $this->getUserGrowth();
        
        // Property Analytics
        $propertyAnalytics = $this->getPropertyAnalytics();
        
        // Revenue Analytics
        $revenueAnalytics = $this->getRevenueAnalytics();
        
        // Notification Analytics
        $notificationAnalytics = $this->getNotificationAnalytics();
        
        // System Performance
        $systemPerformance = $this->getSystemPerformance();
        
        // Geographic Analytics
        $geographicAnalytics = $this->getGeographicAnalytics();
        
        return view('admin.analytics.index', compact(
            'userGrowth',
            'propertyAnalytics',
            'revenueAnalytics',
            'notificationAnalytics',
            'systemPerformance',
            'geographicAnalytics'
        ));
    }

    /**
     * Get user growth analytics
     */
    private function getUserGrowth()
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $newUsers = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $activeUsers = User::whereHas('owner', function($query) use ($month) {
                $query->whereHas('subscription', function($subQuery) use ($month) {
                    $subQuery->where('status', 'active')
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month);
                });
            })->count();
            
            $months->push([
                'month' => $month->format('M Y'),
                'new_users' => $newUsers,
                'active_users' => $activeUsers,
                'year' => $month->year,
                'month_num' => $month->month
            ]);
        }
        
        return $months;
    }

    /**
     * Get property analytics
     */
    private function getPropertyAnalytics()
    {
        $totalProperties = Property::count();
        $occupiedProperties = Property::whereHas('units.tenant')->count();
        $vacantProperties = $totalProperties - $occupiedProperties;
        
        $avgRent = Property::with('units')->get()->avg(function($property) {
            return $property->units->avg('rent');
        });
        
        $propertyTypes = Property::select('property_type', DB::raw('COUNT(*) as count'))
            ->groupBy('property_type')
            ->get();
        
        return [
            'total_properties' => $totalProperties,
            'occupied_properties' => $occupiedProperties,
            'vacant_properties' => $vacantProperties,
            'occupancy_rate' => $totalProperties > 0 ? round(($occupiedProperties / $totalProperties) * 100, 2) : 0,
            'avg_rent' => $avgRent ?? 0,
            'property_types' => $propertyTypes
        ];
    }

    /**
     * Get revenue analytics
     */
    private function getRevenueAnalytics()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        $monthlyRevenue = collect();
        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create($currentYear, $i, 1);
            $revenue = Billing::where('status', 'paid')
                ->whereYear('paid_date', $month->year)
                ->whereMonth('paid_date', $month->month)
                ->sum('amount');
            
            $monthlyRevenue->push([
                'month' => $month->format('M'),
                'revenue' => $revenue,
                'month_num' => $i
            ]);
        }
        
        $totalRevenue = Billing::where('status', 'paid')->sum('amount');
        $avgRevenue = Billing::where('status', 'paid')->avg('amount');
        
        return [
            'monthly_revenue' => $monthlyRevenue,
            'total_revenue' => $totalRevenue,
            'avg_revenue' => $avgRevenue,
            'current_month_revenue' => $monthlyRevenue->where('month_num', $currentMonth)->first()['revenue'] ?? 0
        ];
    }

    /**
     * Get notification analytics
     */
    private function getNotificationAnalytics()
    {
        $totalNotifications = NotificationLog::count();
        $successfulNotifications = NotificationLog::where('status', 'sent')->count();
        $failedNotifications = NotificationLog::where('status', 'failed')->count();
        
        $notificationTypes = NotificationLog::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();
        
        $dailyNotifications = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = NotificationLog::whereDate('created_at', $date)->count();
            
            $dailyNotifications->push([
                'date' => $date->format('M d'),
                'count' => $count,
                'day' => $date->format('D')
            ]);
        }
        
        return [
            'total_notifications' => $totalNotifications,
            'successful_notifications' => $successfulNotifications,
            'failed_notifications' => $failedNotifications,
            'success_rate' => $totalNotifications > 0 ? round(($successfulNotifications / $totalNotifications) * 100, 2) : 0,
            'notification_types' => $notificationTypes,
            'daily_notifications' => $dailyNotifications
        ];
    }

    /**
     * Get system performance analytics
     */
    private function getSystemPerformance()
    {
        $totalUsers = User::count();
        $activeUsers = User::whereHas('owner.subscription', function($query) {
            $query->where('status', 'active');
        })->count();
        
        $totalProperties = Property::count();
        $totalTenants = Tenant::count();
        
        $avgResponseTime = 0; // This would be calculated from actual performance monitoring
        $uptime = 99.9; // This would be calculated from actual uptime monitoring
        
        $diskUsage = $this->getDiskUsage();
        $memoryUsage = $this->getMemoryUsage();
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'user_activity_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
            'total_properties' => $totalProperties,
            'total_tenants' => $totalTenants,
            'avg_response_time' => $avgResponseTime,
            'uptime' => $uptime,
            'disk_usage' => $diskUsage,
            'memory_usage' => $memoryUsage
        ];
    }

    /**
     * Get geographic analytics
     */
    private function getGeographicAnalytics()
    {
        $propertyLocations = Property::select('city', DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $ownerLocations = Owner::with('user')
            ->get()
            ->groupBy(function($owner) {
                return $owner->user->city ?? 'Unknown';
            })
            ->map(function($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);
        
        return [
            'property_locations' => $propertyLocations,
            'owner_locations' => $ownerLocations
        ];
    }

    /**
     * Get disk usage (simulated)
     */
    private function getDiskUsage()
    {
        // In a real application, this would use actual disk usage monitoring
        return [
            'used' => 75, // percentage
            'free' => 25, // percentage
            'total' => '500GB',
            'used_gb' => '375GB'
        ];
    }

    /**
     * Get memory usage (simulated)
     */
    private function getMemoryUsage()
    {
        // In a real application, this would use actual memory monitoring
        return [
            'used' => 60, // percentage
            'free' => 40, // percentage
            'total' => '8GB',
            'used_gb' => '4.8GB'
        ];
    }

    /**
     * Get real-time analytics
     */
    public function getRealTimeAnalytics()
    {
        $currentHour = Carbon::now()->hour;
        $today = Carbon::today();
        
        $hourlyData = collect();
        for ($i = 0; $i < 24; $i++) {
            $hour = Carbon::today()->addHours($i);
            $count = NotificationLog::whereDate('created_at', $today)
                ->whereHour('created_at', $i)
                ->count();
            
            $hourlyData->push([
                'hour' => $hour->format('H:00'),
                'count' => $count,
                'hour_num' => $i
            ]);
        }
        
        return response()->json([
            'success' => true,
            'hourly_data' => $hourlyData,
            'current_hour' => $currentHour
        ]);
    }

    /**
     * Get custom date range analytics
     */
    public function getCustomAnalytics(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        
        $revenue = Billing::where('status', 'paid')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->sum('amount');
        
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $notifications = NotificationLog::whereBetween('created_at', [$startDate, $endDate])->count();
        
        return response()->json([
            'success' => true,
            'revenue' => $revenue,
            'new_users' => $newUsers,
            'notifications' => $notifications,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);
    }
} 