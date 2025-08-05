<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\SubscriptionPlan;
use App\Models\OwnerSubscription;
use App\Models\Billing;
use Illuminate\Support\Str;
use App\Helpers\CountryHelper;
use App\Helpers\NotificationHelper;
use App\Models\User; // if needed
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class OwnerRegisterController extends Controller
{
    public function showForm()
    {
        $countries = CountryHelper::countryList();
        return view('auth.owner-register',compact('countries'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                'unique:users,email',
                'unique:owners,email', // <-- Owner table-এও unique validation
            ],
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'country'  => 'nullable|string|max:100',
            'gender'   => 'required|in:male,female,other',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        $user->assignRole('owner');

        $owner = Owner::create([
            'user_id'  => $user->id,
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'country'  => $request->country,
            'gender'   => $request->gender,

        ]);

        // Update user with owner_id
        $user->update(['owner_id' => $owner->id]);

        // Refresh user to load the updated data
        $user->refresh();

        // Log owner creation
        \Log::info('Owner created', [
            'user_id' => $user->id,
            'owner_id' => $owner->id,
            'owner_user_id' => $owner->user_id,
            'user_owner_id' => $user->owner_id,
            'user_phone' => $user->phone
        ]);

        // Send comprehensive welcome notification (multiple emails + SMS)
        try {
            $notificationResults = NotificationHelper::sendComprehensiveWelcome($user);
            \Log::info('Comprehensive welcome notification sent', [
                'user_id' => $user->id,
                'owner_id' => $owner->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'emails_sent' => count(array_filter($notificationResults, function($key) {
                    return strpos($key, 'email') !== false;
                }, ARRAY_FILTER_USE_KEY)),
                'sms_sent' => isset($notificationResults['sms']) && $notificationResults['sms']['success']
            ]);
        } catch (\Exception $e) {
            \Log::error('Welcome notification failed: ' . $e->getMessage());
        }

        // Automatically activate free package for new owner
        $freePlan = SubscriptionPlan::where('price', 0)->first();
        if ($freePlan) {
            \Log::info('Activating free package for new owner', [
                'user_id' => $user->id,
                'owner_id' => $owner->id,
                'free_plan_id' => $freePlan->id,
                'free_plan_name' => $freePlan->name
            ]);

            // Create free subscription
            $freeSubscription = OwnerSubscription::create([
                'owner_id' => $owner->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'auto_renew' => true,
                'sms_credits' => $freePlan->sms_notification ? 100 : 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'plan_name' => $freePlan->name
            ]);

            \Log::info('Free subscription created', [
                'subscription_id' => $freeSubscription->id,
                'owner_id' => $freeSubscription->owner_id,
                'plan_id' => $freeSubscription->plan_id,
                'status' => $freeSubscription->status,
                'start_date' => $freeSubscription->start_date,
                'end_date' => $freeSubscription->end_date
            ]);
        }

        // Check if there's a selected plan in request or session
        $selectedPlan = $request->input('selected_plan') ?? session('selected_plan');
        $directSubscribe = session('direct_subscribe');

        // Log for debugging
        \Log::info('Registration process started', [
            'selected_plan' => $selectedPlan,
            'direct_subscribe' => $directSubscribe,
            'session_data' => session()->all()
        ]);

        if ($selectedPlan && $directSubscribe) {
            // Clear the session
            session()->forget(['selected_plan', 'direct_subscribe']);

            // Get the plan
            $plan = SubscriptionPlan::find($selectedPlan);

            \Log::info('Plan found', [
                'plan_id' => $selectedPlan,
                'plan_found' => $plan ? true : false,
                'plan_name' => $plan ? $plan->name : null,
                'plan_price' => $plan ? $plan->price : null
            ]);

            if ($plan) {
                // Log before creating subscription
                \Log::info('Creating subscription', [
                    'user_id' => $user->id,
                    'user_owner_id' => $user->owner_id,
                    'owner_id' => $owner->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'plan_price' => $plan->price
                ]);

                // Create subscription
                $subscription = OwnerSubscription::create([
                    'owner_id' => $user->owner_id, // Use user's owner_id instead of owner->id
                    'plan_id' => $plan->id,
                    'status' => $plan->price > 0 ? 'pending' : 'active',
                    'auto_renew' => true,
                    'sms_credits' => $plan->sms_notification ? 100 : 0
                ]);

                \Log::info('Subscription created', [
                    'subscription_id' => $subscription->id,
                    'owner_id' => $user->owner_id, // Log user's owner_id
                    'user_id' => $user->id, // Added for debugging
                    'plan_id' => $plan->id,
                    'status' => $subscription->status,
                    'subscription_owner_id' => $subscription->owner_id // Log the actual owner_id saved
                ]);

                // If free plan, activate immediately
                if ($plan->price == 0) {
                    $subscription->update([
                        'status' => 'active',
                        'start_date' => now()->toDateString(),
                        'end_date' => now()->addYear()->toDateString()
                    ]);

                    // Auto login the user
                    Auth::login($user);

                    return redirect()->route('owner.dashboard')->with('success', 'Registration successful. Free plan activated!');
                }

                // Generate invoice for paid plans
                $invoice = $subscription->generateInvoice();

                \Log::info('Invoice generated', [
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $invoice->amount,
                    'invoice_number' => $invoice->invoice_number
                ]);

                // Auto login the user
                Auth::login($user);

                // Redirect to payment page with invoice_id
                return redirect()->route('owner.subscription.payment', ['invoice_id' => $invoice->id])->with('success', 'Registration successful. Please complete the payment for your selected plan.');
            }
        } elseif ($selectedPlan) {
            // Clear the session
            session()->forget('selected_plan');

            // Auto login the user
            Auth::login($user);

            // Redirect to subscription plans page
            return redirect()->route('owner.subscription.plans')->with('success', 'Registration successful. Please select your plan.');
        }

        // Auto login the user
        Auth::login($user);

        return redirect()->route('owner.dashboard')->with('success', 'Registration successful!');
    }
}
