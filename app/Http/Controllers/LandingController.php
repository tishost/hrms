<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Get active subscription plans
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        // Get statistics
        $totalOwners = \App\Models\User::role('owner')->count();
        $totalProperties = \App\Models\Property::count();
        $totalTenants = \App\Models\Tenant::count();

        return view('landing', compact('plans', 'totalOwners', 'totalProperties', 'totalTenants'));
    }
} 