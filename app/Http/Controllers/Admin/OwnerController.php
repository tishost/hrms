<?php

namespace App\Http\Controllers\Admin;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CountryHelper;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\NotificationHelper;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::with('user')->get();
        $countries = CountryHelper::countryList();
        return view('admin.owners.index', compact('owners', 'countries'));
    }

    public function create()
    {
        $countries = CountryHelper::countryList();
        return view('admin.owners.create', compact('countries'));
    }

    public function store(Request $request)
    {
        // Debug: Log that the method is called
        \Log::info('OwnerController@store called', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'gender' => 'nullable|in:male,female,other',
        ]);

        // Debug: Log validation passed
        \Log::info('Validation passed, creating user and owner');

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('password123'), // Default password
            'email_verified_at' => now(),
        ]);

        // Debug: Log user created
        \Log::info('User created', ['user_id' => $user->id, 'user_email' => $user->email]);

        // Create Owner
        $owner = Owner::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'address' => $request->address,
            'gender' => $request->gender,
            'status' => 'active',
            'is_super_admin' => false,
        ]);

        // Debug: Log owner created
        \Log::info('Owner created', ['owner_id' => $owner->id, 'owner_phone' => $owner->phone]);

        // Update user with owner_id and phone number
        $user->update([
            'owner_id' => $owner->id,
            'phone' => $owner->phone // Add phone number to user
        ]);
        
        // Debug: Log the phone numbers
        \Log::info('Owner creation - Phone numbers:', [
            'request_phone' => $request->phone,
            'owner_phone' => $owner->phone,
            'user_phone' => $user->phone,
            'user_id' => $user->id,
            'owner_id' => $owner->id
        ]);

        // Send comprehensive welcome notification (multiple emails + SMS)
        try {
            \Log::info('Starting notification process');
            $notificationResults = NotificationHelper::sendComprehensiveWelcome($user);
            \Log::info('Comprehensive welcome notification sent via admin', [
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
        } catch (\Exception $e) {
            \Log::error('Welcome notification failed via admin: ' . $e->getMessage());
        }

        return redirect()->route('admin.owners.index')->with('success', 'Owner created successfully!');
    }

    public function edit($id)
    {
        $countries = CountryHelper::countryList();
        $owner = Owner::findOrFail($id);

        return response()->json([
            'owner' => $owner,
            'countries' => $countries,
        ]);
    }

    public function update(Request $request, Owner $owner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $owner->user_id,
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'gender' => 'nullable|in:male,female,other',
        ]);

        // Update User
        $owner->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update Owner
        $owner->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Owner $owner)
    {
        // Delete associated user
        $owner->user->delete();

        // Delete owner
        $owner->delete();

        return redirect()->route('admin.owners.index')->with('success', 'Owner deleted successfully!');
    }
}
