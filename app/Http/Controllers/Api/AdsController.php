<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdsController extends Controller
{
    /**
     * Get ads for dashboard
     */
    public function getDashboardAds(Request $request)
    {
        try {
            $type = $request->get('type', 'tenant'); // 'tenant' or 'owner'
            
            $ads = Ad::when($type === 'owner', function($query) {
                    return $query->forOwnerDashboard();
                })
                ->when($type === 'tenant', function($query) {
                    return $query->forTenantDashboard();
                })
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Increment impression count for each ad
            foreach ($ads as $ad) {
                $ad->incrementImpressions();
            }

            return response()->json([
                'success' => true,
                'message' => 'Ads retrieved successfully',
                'data' => [
                    'ads' => $ads->map(function($ad) {
                        return [
                            'id' => $ad->id,
                            'title' => $ad->title,
                            'description' => $ad->description,
                            'image_url' => $ad->image_url,
                            'url' => $ad->url,
                            'display_order' => $ad->display_order,
                            'is_clickable' => !empty($ad->url),
                        ];
                    }),
                    'total_count' => $ads->count(),
                    'timestamp' => Carbon::now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ads',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record ad click
     */
    public function recordClick(Request $request, $adId)
    {
        try {
            $ad = Ad::findOrFail($adId);
            
            // Check if ad is currently active and within date range
            if (!$ad->isCurrentlyActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ad is not currently active'
                ], 400);
            }

            // Increment click count
            $ad->incrementClicks();
            
            return response()->json([
                'success' => true,
                'message' => 'Click recorded successfully',
                'data' => [
                    'ad_id' => $ad->id,
                    'clicks_count' => $ad->clicks_count,
                    'timestamp' => Carbon::now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record click',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ads statistics (public)
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_ads' => Ad::count(),
                'active_ads' => Ad::active()->count(),
                'scheduled_ads' => Ad::where('start_date', '>', Carbon::today())->count(),
                'expired_ads' => Ad::where('end_date', '<', Carbon::today())->count(),
                'owner_dashboard_ads' => Ad::where('show_on_owner_dashboard', true)->count(),
                'tenant_dashboard_ads' => Ad::where('show_on_tenant_dashboard', true)->count(),
                'total_clicks' => Ad::sum('clicks_count'),
                'total_impressions' => Ad::sum('impressions_count'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ads by location type
     */
    public function getAdsByLocation(Request $request)
    {
        try {
            $location = $request->get('location', 'tenant'); // 'tenant' or 'owner'
            $limit = $request->get('limit', 10);
            
            $ads = Ad::when($location === 'owner', function($query) {
                    return $query->forOwnerDashboard();
                })
                ->when($location === 'tenant', function($query) {
                    return $query->forTenantDashboard();
                })
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            // Increment impression count for each ad
            foreach ($ads as $ad) {
                $ad->incrementImpressions();
            }

            return response()->json([
                'success' => true,
                'message' => 'Ads retrieved successfully',
                'data' => [
                    'ads' => $ads->map(function($ad) {
                        return [
                            'id' => $ad->id,
                            'title' => $ad->title,
                            'description' => $ad->description,
                            'image_url' => $ad->image_url,
                            'url' => $ad->url,
                            'display_order' => $ad->display_order,
                            'is_clickable' => !empty($ad->url),
                            'start_date' => $ad->start_date->format('Y-m-d'),
                            'end_date' => $ad->end_date->format('Y-m-d'),
                        ];
                    }),
                    'location' => $location,
                    'total_count' => $ads->count(),
                    'timestamp' => Carbon::now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ads',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
