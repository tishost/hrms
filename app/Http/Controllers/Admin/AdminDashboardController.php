<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OwnerSubscription;
use App\Models\Billing;
use App\Models\SubscriptionPlan;
use App\Helpers\CountryHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            // Subscription Statistics
            $totalOwners = User::role('owner')
                ->whereHas('owner', function($query) {
                    $query->whereNull('deleted_at');
                })->count();
            $activeSubscriptions = OwnerSubscription::where('status', 'active')
                ->whereHas('owner', function($query) {
                    $query->whereHas('owner', function($subQuery) {
                        $subQuery->whereNull('deleted_at');
                    });
                })->count();
            $expiredSubscriptions = OwnerSubscription::where('status', 'expired')
                ->whereHas('owner', function($query) {
                    $query->whereHas('owner', function($subQuery) {
                        $subQuery->whereNull('deleted_at');
                    });
                })->count();

        // Revenue Statistics
        $monthlyRevenue = Billing::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('amount');

        $yearlyRevenue = Billing::where('status', 'paid')
            ->whereYear('paid_date', now()->year)
            ->sum('amount');

        $pendingPayments = Billing::where('status', 'pending')->count();
        $overduePayments = Billing::where('status', 'pending')
            ->where('due_date', '<', now())->count();

        // Plan Distribution
        $planDistribution = SubscriptionPlan::withCount(['subscriptions' => function($query) {
            $query->where('status', 'active');
        }])->get();

        // Recent Activities
        $recentSubscriptions = OwnerSubscription::with(['owner', 'plan'])
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = Billing::with(['owner', 'subscription.plan'])
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly Revenue Chart Data
        $monthlyRevenueData = [];
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i);
            $revenue = Billing::where('status', 'paid')
                ->whereYear('paid_date', $month->year)
                ->whereMonth('paid_date', $month->month)
                ->whereHas('owner', function($query) {
                    $query->whereHas('owner', function($subQuery) {
                        $subQuery->whereNull('deleted_at');
                    });
                })
                ->sum('amount');

            $monthlyRevenueData[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }
        $monthlyRevenueData = array_reverse($monthlyRevenueData);

        } catch (\Exception $e) {
            // Return default values if there's an error
            $totalOwners = 0;
            $activeSubscriptions = 0;
            $expiredSubscriptions = 0;
            $monthlyRevenue = 0;
            $yearlyRevenue = 0;
            $pendingPayments = 0;
            $overduePayments = 0;
            $planDistribution = collect();
            $recentSubscriptions = collect();
            $recentPayments = collect();
            $monthlyRevenueData = [];
        }

        return view('admin.dashboard', compact(
            'totalOwners',
            'activeSubscriptions',
            'expiredSubscriptions',
            'monthlyRevenue',
            'yearlyRevenue',
            'pendingPayments',
            'overduePayments',
            'planDistribution',
            'recentSubscriptions',
            'recentPayments',
            'monthlyRevenueData'
        ));
    }

    public function subscriptions()
    {
        $subscriptions = OwnerSubscription::with(['owner', 'plan'])
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function plans()
    {
        $plans = SubscriptionPlan::withCount(['subscriptions' => function($query) {
            $query->where('status', 'active');
        }])->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function billing()
    {
        $billing = Billing::with(['owner', 'subscription.plan', 'paymentMethod'])
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalRevenue = Billing::where('status', 'paid')
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })->sum('amount');
        $pendingAmount = Billing::where('status', 'pending')
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })->sum('amount');
        $monthlyRevenue = Billing::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->whereHas('owner', function($query) {
                $query->whereHas('owner', function($subQuery) {
                    $subQuery->whereNull('deleted_at');
                });
            })->sum('amount');

        return view('admin.billing.index', compact('billing', 'totalRevenue', 'pendingAmount', 'monthlyRevenue'));
    }

    public function owners()
    {
        $owners = User::role('owner')
            ->whereHas('owner', function($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['subscription.plan', 'owner'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.owners.index', compact('owners'));
    }

    public function createOwner()
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
        $countries = CountryHelper::countryList();
        return view('admin.owners.create', compact('plans', 'countries'));
    }

    public function storeOwner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'country' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'plan_id' => 'required|exists:subscription_plans,id'
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(),
        ]);

        // Assign owner role
        $user->assignRole('owner');

        // Create owner record
        \App\Models\Owner::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'gender' => $request->gender,
            'address' => $request->address,
            'status' => 'active',
            'phone_verified' => false,
            'is_super_admin' => false,
            'total_properties' => 0,
            'total_tenants' => 0
        ]);

                // Create subscription
        $subscription = \App\Models\OwnerSubscription::create([
            'owner_id' => $user->id,
            'plan_id' => $request->plan_id,
            'status' => 'pending', // Start as pending
            'start_date' => null, // Will be set after payment
            'end_date' => null, // Will be set after payment
            'auto_renew' => true,
            'sms_credits' => 0
        ]);

        // Get the plan
        $plan = \App\Models\SubscriptionPlan::find($request->plan_id);

        // If it's a paid plan, generate invoice
        if ($plan->price > 0) {
            $subscription->generateInvoice();
        } else {
            // Free plan - activate immediately
            $subscription->update([
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString()
            ]);
        }

        $message = 'Owner created successfully!';

        // If it's a paid plan, add invoice information
        if ($plan->price > 0) {
            $invoice = $subscription->getPendingInvoice();
            $message .= " Invoice #{$invoice->invoice_number} has been generated. Payment is required to activate the subscription.";
        } else {
            $message .= " Free plan activated immediately.";
        }

        return redirect()->route('admin.owners.index')
            ->with('success', $message);
    }
}
