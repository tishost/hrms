<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount(['subscriptions' => function($query) {
            $query->where('status', 'active');
        }])->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'properties_limit' => 'required|integer',
            'units_limit' => 'required|integer',
            'tenants_limit' => 'required|integer',
            'sms_notification' => 'boolean',
            'features' => 'nullable|array'
        ]);

        SubscriptionPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'properties_limit' => $request->properties_limit,
            'units_limit' => $request->units_limit,
            'tenants_limit' => $request->tenants_limit,
            'sms_notification' => $request->sms_notification ?? false,
            'features' => $request->features ?? [],
            'is_active' => true
        ]);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully!');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'properties_limit' => 'required|integer',
            'units_limit' => 'required|integer',
            'tenants_limit' => 'required|integer',
            'sms_notification' => 'boolean',
            'features' => 'nullable|array'
        ]);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'properties_limit' => $request->properties_limit,
            'units_limit' => $request->units_limit,
            'tenants_limit' => $request->tenants_limit,
            'sms_notification' => $request->sms_notification ?? false,
            'features' => $request->features ?? []
        ]);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully!');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('admin.plans.index')
                ->with('error', 'Cannot delete plan with active subscriptions!');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully!');
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.plans.index')
            ->with('success', "Plan {$status} successfully!");
    }
}
