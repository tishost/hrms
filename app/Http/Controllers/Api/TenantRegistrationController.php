<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{




    // Complete tenant registration
    public function register(Request $request)
    {
        \Log::info('Tenant registration request', [
            'mobile' => $request->mobile,
            'has_password' => !empty($request->password)
        ]);

        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'email' => 'nullable|email|max:191',
        ]);

        try {
            // Start database transaction
            DB::beginTransaction();

            // Check if tenant exists
            $tenant = Tenant::where('mobile', $request->mobile)->first();

            if (!$tenant) {
                \Log::warning('Tenant not found for registration', ['mobile' => $request->mobile]);
                return response()->json([
                    'error' => 'Tenant not found'
                ], 404);
            }

            // Check if user already exists
            $existingUser = User::where('phone', $request->mobile)->first();
            if ($existingUser) {
                \Log::warning('User already exists for registration', ['mobile' => $request->mobile]);
                return response()->json([
                    'error' => 'Account already registered'
                ], 400);
            }

            // OTP verification removed - Tenant registration now works without OTP

            // If email provided in registration form, update tenant email first
            if ($request->filled('email')) {
                $tenant->email = $request->email;
                $tenant->save();
                \Log::info('Tenant email updated during registration', [
                    'tenant_id' => $tenant->id,
                    'email' => $tenant->email,
                ]);
            }

            // Create user
            $user = User::create([
                'name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
                'phone' => $tenant->mobile,
                'email' => $tenant->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'owner_id' => $tenant->owner_id,
            ]);

            \Log::info('User created successfully', [
                'user_id' => $user->id,
                'mobile' => $request->mobile,
                'tenant_id' => $tenant->id
            ]);

            // Assign tenant role
            $user->assignRole('tenant');

            \Log::info('Tenant role assigned', [
                'user_id' => $user->id,
                'role' => 'tenant'
            ]);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            \Log::info('Auth token generated', [
                'user_id' => $user->id,
                'token_length' => strlen($token)
            ]);

            // Commit transaction
            DB::commit();

            \Log::info('Tenant registration completed successfully', [
                'user_id' => $user->id,
                'mobile' => $request->mobile,
                'tenant_id' => $tenant->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                    'owner_id' => $user->owner_id,
                ],
                'role' => 'tenant',
                'token' => $token
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            \Log::error('Tenant registration error: ' . $e->getMessage(), [
                'mobile' => $request->mobile,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to complete registration: ' . $e->getMessage()
            ], 500);
        }
    }
}
