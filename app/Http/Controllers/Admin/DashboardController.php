<?php

namespace App\Http\Controllers\Admin;

use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $ownerCount = Owner::count();
        $superAdminCount = Owner::where('is_super_admin', true)->count();
        $propertyCount = Property::count();
        $tenantCount = Tenant::count();
        $recentOwners = Owner::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'ownerCount',
            'superAdminCount',
            'propertyCount',
            'tenantCount',
            'recentOwners'
        ));
    }
}
