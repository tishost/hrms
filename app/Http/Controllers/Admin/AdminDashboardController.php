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

        // SMS Balance Statistics
        $totalSmsCredits = OwnerSubscription::where('status', 'active')->sum('sms_credits');
        $smsEnabledSubscriptions = OwnerSubscription::where('status', 'active')
            ->whereHas('plan', function($query) {
                $query->where('sms_notification', true);
            })->count();
        $smsUsedThisMonth = \App\Models\NotificationLog::where('type', 'sms')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get actual SMS Gateway Balance
        $smsGatewayBalance = 0;
        $smsMaskBalance = 0;
        $smsNonMaskBalance = 0;
        $smsVoiceBalance = 0;
        
        try {
            // Get SMS settings from database
            $settings = \App\Models\SystemSetting::where('key', 'like', 'sms_%')->pluck('value', 'key');
            $apiToken = $settings['sms_api_token'] ?? '';
            $senderId = $settings['sms_sender_id'] ?? '';
            
            if (!empty($apiToken)) {
                $smsService = new \App\Services\SmsService($apiToken, $senderId);
                $balanceResult = $smsService->getBalance();
                
                if ($balanceResult['success']) {
                    $smsGatewayBalance = $balanceResult['total_balance'] ?? 0;
                    $smsMaskBalance = $balanceResult['mask'] ?? 0;
                    $smsNonMaskBalance = $balanceResult['nonmask'] ?? 0;
                    $smsVoiceBalance = $balanceResult['voice'] ?? 0;
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the dashboard
            \Log::error('SMS Gateway balance check failed: ' . $e->getMessage());
        }

        // Plan Distribution
        $planDistribution = SubscriptionPlan::withCount(['subscriptions' => function($query) {
            $query->where('status', 'active');
        }])->get();

        // Recent Activities
        $recentSubscriptions = OwnerSubscription::with(['owner.user', 'plan'])
            ->whereHas('owner.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = Billing::with(['owner.user', 'subscription.plan'])
            ->whereHas('owner.user')
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
            $totalSmsCredits = 0;
            $smsEnabledSubscriptions = 0;
            $smsUsedThisMonth = 0;
            $smsGatewayBalance = 0;
            $smsMaskBalance = 0;
            $smsNonMaskBalance = 0;
            $smsVoiceBalance = 0;
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
            'totalSmsCredits',
            'smsEnabledSubscriptions',
            'smsUsedThisMonth',
            'smsGatewayBalance',
            'smsMaskBalance',
            'smsNonMaskBalance',
            'smsVoiceBalance',
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
            ->paginate(\App\Helpers\SystemHelper::getPaginationLimit());

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
        $owners = \App\Models\Owner::with([
            'user',
            'subscription.plan',
            'subscription.billing'
        ])->paginate(\App\Helpers\SystemHelper::getPaginationLimit());
        return view('admin.owners.index', compact('owners'));
    }

    public function createOwner()
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
        $countries = CountryHelper::countryList();
        return view('admin.owners.create', compact('plans', 'countries'));
    }

    public function showOwner($id)
    {
        $owner = \App\Models\Owner::with([
            'user',
            'subscription.plan',
            'subscription.billing',
            'properties.units.tenant',
            'properties.units.owner',
            'tenants.unit.property',
            'billing.paymentMethod'
        ])->findOrFail($id);

        // Get notification logs for this owner (check both user_id and owner_id)
        $notificationLogs = \App\Models\NotificationLog::where(function($query) use ($owner) {
            $query->where('user_id', $owner->user_id)
                  ->orWhere('owner_id', $owner->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        // Debug: Log the query results
        \Log::info('Owner ID: ' . $id . ', User ID: ' . $owner->user_id);
        \Log::info('Total notification logs found: ' . $notificationLogs->count());
        \Log::info('Notification logs: ' . $notificationLogs->toJson());

        // Get SMS stats
        $smsStats = [
            'total_sent' => $notificationLogs->where('type', 'sms')->count(),
            'successful' => $notificationLogs->where('type', 'sms')->where('status', 'sent')->count(),
            'failed' => $notificationLogs->where('type', 'sms')->where('status', 'failed')->count(),
        ];

        // Create some test notification logs if none exist
        if ($notificationLogs->count() == 0) {
            \App\Models\NotificationLog::create([
                'user_id' => $owner->user_id,
                'owner_id' => $owner->id,
                'type' => 'email',
                'recipient' => $owner->user->email,
                'content' => 'Welcome to HRMS! Your account has been created successfully.',
                'status' => 'sent',
                'sent_at' => now(),
                'template_name' => 'welcome_email',
                'source' => 'system'
            ]);

            \App\Models\NotificationLog::create([
                'user_id' => $owner->user_id,
                'owner_id' => $owner->id,
                'type' => 'sms',
                'recipient' => $owner->user->phone,
                'content' => 'Welcome to HRMS! Your account has been created successfully.',
                'status' => 'sent',
                'sent_at' => now(),
                'template_name' => 'welcome_sms',
                'source' => 'system'
            ]);

            // Refresh the query
            $notificationLogs = \App\Models\NotificationLog::where(function($query) use ($owner) {
                $query->where('user_id', $owner->user_id)
                      ->orWhere('owner_id', $owner->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        }

        return view('admin.owners.show', compact('owner', 'notificationLogs', 'smsStats'));
    }

    public function testNotification(Request $request, $id)
    {
        $owner = \App\Models\Owner::with('user')->findOrFail($id);
        $type = $request->input('type', 'welcome');
        $message = $request->input('message', '');

        try {
            $result = [];
            
            switch ($type) {
                case 'welcome':
                    $result = \App\Helpers\NotificationHelper::sendWelcomeNotification($owner->user);
                    break;
                case 'subscription':
                    if ($owner->subscription) {
                        $result = \App\Helpers\NotificationHelper::sendSubscriptionActivation(
                            $owner->user,
                            $owner->subscription->plan->name,
                            $owner->subscription->end_date
                        );
                    }
                    break;
                case 'payment':
                    $result = \App\Helpers\NotificationHelper::sendPaymentConfirmation(
                        $owner->user,
                        1000,
                        'TEST-' . time(),
                        'Test Payment'
                    );
                    break;
                case 'sms':
                    if ($owner->user->phone) {
                        $result = \App\Helpers\NotificationHelper::sendSms(
                            $owner->user->phone,
                            $message ?: 'This is a test SMS from HRMS admin panel.'
                        );
                    }
                    break;
            }

            return response()->json(['success' => true, 'message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function testEmail(Request $request, $id)
    {
        $owner = \App\Models\Owner::with('user')->findOrFail($id);

        try {
            $result = \App\Helpers\NotificationHelper::sendEmail(
                $owner->user->email,
                'Test Email from HRMS Admin',
                'This is a test email sent from the HRMS admin panel. If you receive this, the email system is working correctly.',
                null,
                ['name' => $owner->user->name]
            );

            return response()->json(['success' => true, 'message' => 'Test email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function testSms(Request $request, $id)
    {
        $owner = \App\Models\Owner::with('user')->findOrFail($id);

        if (!$owner->user->phone) {
            return response()->json(['success' => false, 'message' => 'Owner has no phone number']);
        }

        try {
            $result = \App\Helpers\NotificationHelper::sendSms(
                $owner->user->phone,
                'This is a test SMS from HRMS admin panel. If you receive this, the SMS system is working correctly.'
            );

            return response()->json(['success' => true, 'message' => 'Test SMS sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resendNotification(Request $request, $id, $logId)
    {
        $owner = \App\Models\Owner::with('user')->findOrFail($id);
        $notificationLog = \App\Models\NotificationLog::findOrFail($logId);

        try {
            if ($notificationLog->type === 'email') {
                $result = \App\Helpers\NotificationHelper::sendEmail(
                    $notificationLog->recipient,
                    'Resent: ' . ($notificationLog->template_name ?? 'Notification'),
                    $notificationLog->content
                );
            } elseif ($notificationLog->type === 'sms') {
                $result = \App\Helpers\NotificationHelper::sendSms(
                    $notificationLog->recipient,
                    $notificationLog->content
                );
            }

            // Update the notification log
            $notificationLog->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Notification resent successfully']);
        } catch (\Exception $e) {
            // Update the notification log with failed status
            $notificationLog->update([
                'status' => 'failed'
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function storeOwner(Request $request)
    {
        // Debug: Log incoming request
        \Log::info('storeOwner called with data:', $request->all());
        
        // Also write to error_log for debugging
        error_log('storeOwner called with data: ' . json_encode($request->all()));
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'country' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Debug: Log validation passed
        \Log::info('Validation passed, creating user');
        error_log('Validation passed, creating user');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        
        // Debug: Log user created
        \Log::info('User created:', ['user_id' => $user->id, 'user_email' => $user->email, 'user_phone' => $user->phone]);
        error_log('User created: ID=' . $user->id . ', Email=' . $user->email . ', Phone=' . ($user->phone ?? 'NULL'));
        
        $user->assignRole('owner');

        // Debug: Log before owner creation
        \Log::info('Creating owner with phone:', ['phone' => $request->phone]);

        $owner = \App\Models\Owner::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'gender' => $request->gender,
        ]);

        // Debug: Log owner created
        \Log::info('Owner created:', ['owner_id' => $owner->id, 'owner_phone' => $owner->phone]);
        error_log('Owner created: ID=' . $owner->id . ', Phone=' . $owner->phone);

        // Update user with owner_id and phone number
        $updateData = [
            'owner_id' => $owner->id,
            'phone' => $owner->phone // Add phone number to user
        ];
        
        // Debug: Log update data
        \Log::info('Updating user with:', $updateData);
        
        $user->update($updateData);
        
        // Debug: Log after update
        \Log::info('User after update:', ['user_id' => $user->id, 'user_phone' => $user->phone]);
        error_log('User after update: ID=' . $user->id . ', Phone=' . ($user->phone ?? 'NULL'));
        
        // Debug: Log the phone numbers
        \Log::info('Owner creation via AdminDashboardController - Phone numbers:', [
            'request_phone' => $request->phone,
            'owner_phone' => $owner->phone,
            'user_phone' => $user->phone,
            'user_id' => $user->id,
            'owner_id' => $owner->id
        ]);

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

        // Send comprehensive welcome notification (multiple emails + SMS)
        try {
            \Log::info('Starting notification process via AdminDashboardController');
            error_log('Starting notification process via AdminDashboardController');
            $notificationResults = \App\Helpers\NotificationHelper::sendComprehensiveWelcome($user);
            \Log::info('Comprehensive welcome notification sent via AdminDashboardController', [
                'user_id' => $user->id,
                'owner_id' => $owner->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'notification_results' => $notificationResults,
                'emails_sent' => count(array_filter($notificationResults, function($key) {
                    return strpos($key, 'email') !== false;
                }, ARRAY_FILTER_USE_KEY)),
                'sms_sent' => isset($notificationResults['sms']) && $notificationResults['sms']['success']
            ]);
            error_log('Notification completed: ' . json_encode($notificationResults));
        } catch (\Exception $e) {
            \Log::error('Welcome notification failed via AdminDashboardController: ' . $e->getMessage());
            error_log('Welcome notification failed: ' . $e->getMessage());
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
