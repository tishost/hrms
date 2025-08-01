<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    // Request OTP for tenant registration
    public function requestOtp(Request $request)
    {
        \Log::info('Tenant OTP request received', [
            'mobile' => $request->mobile,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $request->validate([
            'mobile' => 'required|string|max:20'
        ]);

        try {
            // Check if tenant exists
            $tenant = Tenant::where('mobile', $request->mobile)->first();

            \Log::info('Tenant lookup result', [
                'mobile' => $request->mobile,
                'tenant_found' => $tenant ? true : false,
                'tenant_id' => $tenant ? $tenant->id : null
            ]);

            if (!$tenant) {
                \Log::warning('Tenant not found for OTP request', ['mobile' => $request->mobile]);
                return response()->json([
                    'error' => 'Tenant not found. Please contact your owner.'
                ], 404);
            }

            // Check if user already exists
            $existingUser = User::where('phone', $request->mobile)->first();
            if ($existingUser) {
                \Log::warning('User already exists for OTP request', ['mobile' => $request->mobile]);
                return response()->json([
                    'error' => 'Account already registered. Please login.'
                ], 400);
            }

            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            \Log::info('Generated OTP', [
                'mobile' => $request->mobile,
                'otp' => $otp
            ]);

            // Save OTP
            $otpRecord = TenantOtp::updateOrCreate(
                ['mobile' => $request->mobile],
                [
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(10),
                    'is_used' => false
                ]
            );

            \Log::info('OTP saved to database', [
                'mobile' => $request->mobile,
                'otp_id' => $otpRecord->id,
                'expires_at' => $otpRecord->expires_at
            ]);

            // Send SMS/Email (you can integrate your SMS/Email service here)
            // For now, we'll just return the OTP in response for testing
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp, // Remove this in production
                'tenant' => [
                    'name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
                    'mobile' => $tenant->mobile,
                    'email' => $tenant->email,
                    'property_name' => $tenant->property ? $tenant->property->name : null,
                    'unit_name' => $tenant->unit ? $tenant->unit->name : null,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant OTP request error: ' . $e->getMessage(), [
                'mobile' => $request->mobile,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to send OTP'
            ], 500);
        }
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        \Log::info('Tenant OTP verification request', [
            'mobile' => $request->mobile,
            'otp_length' => strlen($request->otp)
        ]);

        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $otpRecord = TenantOtp::where('mobile', $request->mobile)
                ->where('otp', $request->otp)
                ->first();

            \Log::info('OTP verification lookup', [
                'mobile' => $request->mobile,
                'otp_found' => $otpRecord ? true : false,
                'otp_valid' => $otpRecord ? $otpRecord->isValid() : false,
                'otp_used' => $otpRecord ? $otpRecord->is_used : null,
                'otp_expires' => $otpRecord ? $otpRecord->expires_at : null
            ]);

            if (!$otpRecord || !$otpRecord->isValid()) {
                \Log::warning('Invalid OTP verification attempt', [
                    'mobile' => $request->mobile,
                    'otp_provided' => $request->otp,
                    'otp_found' => $otpRecord ? true : false,
                    'otp_valid' => $otpRecord ? $otpRecord->isValid() : false
                ]);
                return response()->json([
                    'error' => 'Invalid or expired OTP'
                ], 400);
            }

            // Mark OTP as used
            $otpRecord->update(['is_used' => true]);

            \Log::info('OTP verified successfully', [
                'mobile' => $request->mobile,
                'otp_id' => $otpRecord->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant OTP verification error: ' . $e->getMessage(), [
                'mobile' => $request->mobile,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to verify OTP'
            ], 500);
        }
    }

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
            'password_confirmation' => 'required'
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

            // Verify OTP was used
            $otpRecord = TenantOtp::where('mobile', $request->mobile)
                ->where('is_used', true)
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpRecord) {
                \Log::warning('No valid OTP verification found for registration', ['mobile' => $request->mobile]);
                return response()->json([
                    'error' => 'Please verify your OTP first'
                ], 400);
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
