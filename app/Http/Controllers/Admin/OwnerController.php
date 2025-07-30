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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'gender' => 'nullable|in:male,female,other',
        ]);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
            'email_verified_at' => now(),
        ]);

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

        // Send welcome notification
        try {
            NotificationHelper::sendWelcomeNotification($user);
        } catch (\Exception $e) {
            \Log::error('Welcome notification failed: ' . $e->getMessage());
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
