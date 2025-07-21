<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Owner;
use App\Http\Requests\OwnerRegistrationRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Owner Registration
    public function registerOwner(OwnerRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Assign Owner role
            $user->assignRole('Owner');

            // Create Owner
            $owner = Owner::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'user_id' => $user->id,
                'total_properties' => 0,
                'total_tenants' => 0,
            ]);

            // Generate token
            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Owner registered successfully',
                'user' => $user,
                'owner' => $owner,
                'token' => $token,
                'role' => 'Owner'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required', // email or phone
            'password' => 'required',
        ]);
        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($login_type, $request->login)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        $role = $user->getRoleNames()->first(); // Get the user's primary role
        return response()->json([
            'user' => $user,
            'role' => $role,
            'token' => $token,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    // Authenticated user info
    public function user(Request $request)
    {
        $user = $request->user();
        $owner = \App\Models\Owner::where('user_id', $user->id)->first();

        return response()->json([
            'id' => $user->id,
            'name' => $owner ? $owner->name : $user->name,
            'email' => $user->email,
            'phone' => $owner ? $owner->phone : $user->phone,
            'address' => $owner ? $owner->address : null,
            'country' => $owner ? $owner->country : null,
            'gender' => $owner ? $owner->gender : null,
            'phone_verified' => $owner ? (bool)$owner->phone_verified : false,
        ]);
    }
}
