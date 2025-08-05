<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Helpers\CountryHelper;


class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = auth()->user()->owner;
        $buildingCount = $owner->properties()->count();
        $unitCount = $owner->properties()->withCount('units')->get()->sum('units_count');
        // Owner-wise tenant count using owner_id
        $tenantCount = \App\Models\Tenant::where('owner_id', $owner->id)->count();
        
        // Get SMS credit information
        $smsCredits = 0;
        $usedSmsCredits = 0;
        $subscription = $owner->subscription;
        if ($subscription) {
            $smsCredits = $subscription->sms_credits ?? 0;
            $usedSmsCredits = $subscription->used_sms_credits ?? 0;
        }
        
        $data = [
            'ordersCount' => 1284,
            'ordersGrowth' => 12.5,
            'revenue' => 24780,
            'revenueGrowth' => 8.3,
            'visitorsData' => [1200, 1900, 1700, 2100, 2400, 2200, 2600],
            'customerSegments' => [35, 25, 20, 10, 5, 5]
        ];

        return view('owner.dashboard', compact('buildingCount', 'unitCount', 'tenantCount', 'data', 'smsCredits', 'usedSmsCredits', 'subscription'));
    }
}
