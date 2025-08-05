<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'properties_limit' => 'required|integer',
            'units_limit' => 'required|integer',
            'tenants_limit' => 'required|integer',
            'sms_notification' => 'boolean',
            'sms_credit' => 'required_if:sms_notification,1|integer|min:0',
            'is_active' => 'boolean',
            'is_popular' => 'boolean'
        ]);

        $data = $request->all();
        
        // Handle features from textarea
        if ($request->has('features_text')) {
            $features = explode("\n", $request->features_text);
            $features = array_map('trim', $features);
            $features = array_filter($features, function($feature) {
                return !empty($feature);
            });
            $data['features'] = array_values($features);
        } else {
            $data['features'] = [];
        }
        
        // Handle features_css from textarea
        if ($request->has('features_css_text')) {
            $features_css = explode("\n", $request->features_css_text);
            $features_css = array_map('trim', $features_css);
            $features_css = array_filter($features_css, function($css) {
                return !empty($css);
            });
            $data['features_css'] = array_values($features_css);
        } else {
            $data['features_css'] = [];
        }

        SubscriptionPlan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'properties_limit' => 'required|integer',
            'units_limit' => 'required|integer',
            'tenants_limit' => 'required|integer',
            'sms_notification' => 'boolean',
            'sms_credit' => 'required_if:sms_notification,1|integer|min:0',
            'is_active' => 'boolean',
            'is_popular' => 'boolean'
        ]);

        $data = $request->all();
        
        // Handle features from textarea
        if ($request->has('features_text')) {
            $features = explode("\n", $request->features_text);
            $features = array_map('trim', $features);
            $features = array_filter($features, function($feature) {
                return !empty($feature);
            });
            $data['features'] = array_values($features);
        } else {
            $data['features'] = [];
        }
        
        // Handle features_css from textarea
        if ($request->has('features_css_text')) {
            $features_css = explode("\n", $request->features_css_text);
            $features_css = array_map('trim', $features_css);
            $features_css = array_filter($features_css, function($css) {
                return !empty($css);
            });
            $data['features_css'] = array_values($features_css);
        } else {
            $data['features_css'] = [];
        }

        // Debug: Log the features data
        \Log::info('Features data:', [
            'has_features_text' => $request->has('features_text'),
            'features_text' => $request->features_text,
            'processed_features' => $data['features'],
            'features_css' => $data['features_css']
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->count() > 0) {
            return redirect()->route('admin.plans.index')->with('error', 'Cannot delete plan with active subscriptions.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        
        $status = $plan->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.plans.index')->with('success', "Plan {$status} successfully.");
    }
} 