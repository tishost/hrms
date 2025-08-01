<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OwnerSubscription;
use App\Models\Billing;
use App\Models\SubscriptionPlan;
use App\Models\ContactTicket;
use App\Helpers\CountryHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            // Subscription Statistics
            $totalOwners = User::role('owner')->count();
            $expiredSubscriptions = OwnerSubscription::where('status', 'expired')->count();
            
            // Paid and Free Subscriptions
            $paidSubscriptions = OwnerSubscription::where('status', 'active')
                ->whereHas('plan', function($query) {
                    $query->where('price', '>', 0);
                })->count();
            
            $freeSubscriptions = OwnerSubscription::where('status', 'active')
                ->whereHas('plan', function($query) {
                    $query->where('price', 0);
                })->count();

        // Revenue Statistics
        $monthlyRevenue = Billing::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('amount');

        $yearlyRevenue = Billing::where('status', 'paid')
            ->whereYear('paid_date', now()->year)
            ->sum('amount');

        $pendingPayments = Billing::whereIn('status', ['pending', 'unpaid'])->count();
        $overduePayments = Billing::whereIn('status', ['pending', 'unpaid'])
            ->where('due_date', '<', now())->count();

        // Ticket Statistics
        $totalTickets = ContactTicket::count();
        $pendingTickets = ContactTicket::where('status', 'pending')->count();
        $inProgressTickets = ContactTicket::where('status', 'in_progress')->count();
        $resolvedTickets = ContactTicket::where('status', 'resolved')->count();

        // Plan Distribution
        $planDistribution = SubscriptionPlan::withCount(['subscriptions' => function($query) {
            $query->where('status', 'active');
        }])->get();

        // Recent Activities
        $recentSubscriptions = OwnerSubscription::with(['owner.user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = Billing::with(['owner.user', 'subscription.plan'])
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
            $expiredSubscriptions = 0;
            $paidSubscriptions = 0;
            $freeSubscriptions = 0;
            $monthlyRevenue = 0;
            $yearlyRevenue = 0;
            $pendingPayments = 0;
            $overduePayments = 0;
            $totalTickets = 0;
            $pendingTickets = 0;
            $inProgressTickets = 0;
            $resolvedTickets = 0;
            $planDistribution = collect();
            $recentSubscriptions = collect();
            $recentPayments = collect();
            $monthlyRevenueData = [];
        }

        return view('admin.dashboard', compact(
            'totalOwners',
            'expiredSubscriptions',
            'paidSubscriptions',
            'freeSubscriptions',
            'monthlyRevenue',
            'yearlyRevenue',
            'pendingPayments',
            'overduePayments',
            'totalTickets',
            'pendingTickets',
            'inProgressTickets',
            'resolvedTickets',
            'planDistribution',
            'recentSubscriptions',
            'recentPayments',
            'monthlyRevenueData'
        ));
    }

    public function subscriptions()
    {
        $subscriptions = OwnerSubscription::with(['owner.user', 'plan'])
            ->whereHas('owner.user', function($query) {
                $query->whereNull('deleted_at');
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
        $billing = Billing::with(['owner.user', 'subscription.plan', 'paymentMethod'])
            ->whereHas('owner.user', function($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalRevenue = Billing::where('status', 'paid')
            ->whereHas('owner.user', function($query) {
                $query->whereNull('deleted_at');
            })->sum('amount');
        $pendingAmount = Billing::where('status', 'pending')
            ->whereHas('owner.user', function($query) {
                $query->whereNull('deleted_at');
            })->sum('amount');
        $monthlyRevenue = Billing::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->whereHas('owner.user', function($query) {
                $query->whereNull('deleted_at');
            })->sum('amount');

        return view('admin.billing.index', compact('billing', 'totalRevenue', 'pendingAmount', 'monthlyRevenue'));
    }

    public function owners()
    {
        $owners = \App\Models\Owner::with('user')->paginate(20);
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
            'address' => 'required|string|max:500',
            'country' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->assignRole('owner');

        $owner = \App\Models\Owner::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'gender' => $request->gender,
        ]);

        // Update user with owner_id
        $user->update(['owner_id' => $owner->id]);

        // Automatically activate free package for new owner
        $freePlan = \App\Models\SubscriptionPlan::where('price', 0)->first();
        if ($freePlan) {
            \Log::info('Activating free package for admin-created owner', [
                'user_id' => $user->id,
                'owner_id' => $owner->id,
                'free_plan_id' => $freePlan->id,
                'free_plan_name' => $freePlan->name
            ]);

            // Create free subscription
            $freeSubscription = \App\Models\OwnerSubscription::create([
                'owner_id' => $owner->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'auto_renew' => true,
                'sms_credits' => $freePlan->sms_notification ? 100 : 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'plan_name' => $freePlan->name
            ]);

            \Log::info('Free subscription created by admin', [
                'subscription_id' => $freeSubscription->id,
                'owner_id' => $freeSubscription->owner_id,
                'plan_id' => $freeSubscription->plan_id,
                'status' => $freeSubscription->status
            ]);
        }

        return redirect()->route('admin.owners.index')->with('success', 'Owner created successfully!');
    }

    /**
     * Remove owner from the system
     */
    public function destroyOwner($id)
    {
        try {
            // First try to find by Owner ID
            $owner = \App\Models\Owner::find($id);

            if (!$owner) {
                // If not found, try to find by User ID
                $owner = \App\Models\Owner::where('user_id', $id)->first();
            }

            if (!$owner) {
                return redirect()->route('admin.owners.index')->with('error', 'Owner not found!');
            }

            $user = \App\Models\User::find($owner->user_id);

            if ($user) {
                // Remove owner role
                $user->removeRole('owner');

                // Delete owner record
                $owner->delete();

                // Delete user record
                $user->delete();

                return redirect()->route('admin.owners.index')->with('success', 'Owner removed successfully!');
            } else {
                return redirect()->route('admin.owners.index')->with('error', 'User not found for this owner!');
            }
        } catch (\Exception $e) {
            \Log::error('Error removing owner: ' . $e->getMessage());
            return redirect()->route('admin.owners.index')->with('error', 'Error removing owner: ' . $e->getMessage());
        }
    }
}
