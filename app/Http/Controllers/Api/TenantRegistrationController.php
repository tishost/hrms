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
            'phone' => $request->phone,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $request->validate([
            'phone' => 'required|string|max:20'
        ]);

        try {
            // Check if OTP is required for tenant registration
            $otpSettings = \App\Models\OtpSetting::getSettings();
            if (!$otpSettings->isOtpRequiredFor('tenant_registration')) {
                return response()->json([
                    'success' => false,
                    'error' => 'OTP verification is not required for tenant registration'
                ], 400);
            }

            // Check if tenant exists
            $tenant = Tenant::where('mobile', $request->phone)->first();

            \Log::info('Tenant lookup result', [
                'phone' => $request->phone,
                'tenant_found' => $tenant ? true : false,
                'tenant_id' => $tenant ? $tenant->id : null
            ]);

            if (!$tenant) {
                \Log::warning('Tenant not found for OTP request', ['phone' => $request->phone]);
                return response()->json([
                    'error' => 'Tenant not found. Please contact your owner.'
                ], 404);
            }

            // Check if user already exists
            $existingUser = User::where('phone', $request->phone)->first();
            if ($existingUser) {
                \Log::warning('User already exists for OTP request', ['phone' => $request->phone]);
                return response()->json([
                    'error' => 'Account already registered. Please login.'
                ], 400);
            }

            // Generate OTP using main OTP system
            $otp = \App\Models\Otp::generateOtp($request->phone, 'profile_update');

            \Log::info('Generated OTP', [
                'phone' => $request->phone,
                'otp' => $otp->otp,
                'otp_id' => $otp->id
            ]);

            // Log OTP generation
            try {
                $otpLogData = [
                    'phone' => $request->phone,
                    'otp' => $otp->otp,
                    'type' => 'profile_update',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'status' => 'sent',
                ];

                // Add user_id if available
                if ($request->user()) {
                    $otpLogData['user_id'] = $request->user()->id;
                }

                // Add session_id if available
                if (session()->isStarted()) {
                    $otpLogData['session_id'] = session()->getId();
                }

                \App\Models\OtpLog::create($otpLogData);
            } catch (\Exception $e) {
                \Log::error('Failed to create OTP log: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            \Log::info('OTP saved to database', [
                'mobile' => $request->mobile,
                'otp_id' => $otp->id,
                'expires_at' => $otp->expires_at
            ]);

            // Send SMS/Email (you can integrate your SMS/Email service here)
            // For now, we'll just return the OTP in response for testing
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp->otp, // Remove this in production
                'expires_in' => $otp->expires_at->diffInMinutes(now()),
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
                'phone' => $request->phone,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => 'Failed to send OTP: ' . $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        \Log::info('Tenant OTP verification request', [
            'phone' => $request->phone,
            'otp_length' => strlen($request->otp)
        ]);

        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6'
        ]);

        try {
            // Use main OTP system for tenant profile update
            $otpRecord = \App\Models\Otp::where('phone', $request->phone)
                ->where('otp', $request->otp)
                ->where('type', 'profile_update')
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            \Log::info('OTP verification lookup', [
                'mobile' => $request->phone,
                'otp_found' => $otpRecord ? true : false,
                'otp_valid' => $otpRecord ? true : false,
                'otp_used' => $otpRecord ? $otpRecord->is_used : null,
                'otp_expires' => $otpRecord ? $otpRecord->expires_at : null
            ]);

            if (!$otpRecord) {
                // Log failed verification attempt
                try {
                    $otpLogData = [
                        'phone' => $request->phone,
                        'otp' => $request->otp,
                        'type' => 'profile_update',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'failed',
                    ];

                    // Add user_id if available
                    if ($request->user()) {
                        $otpLogData['user_id'] = $request->user()->id;
                    }

                    // Add session_id if available
                    if (session()->isStarted()) {
                        $otpLogData['session_id'] = session()->getId();
                    }

                    \App\Models\OtpLog::create($otpLogData);
                } catch (\Exception $e) {
                    \Log::error('Failed to create OTP log: ' . $e->getMessage(), [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                \Log::warning('Invalid OTP verification attempt', [
                    'mobile' => $request->phone,
                    'otp_provided' => $request->otp,
                    'otp_found' => false
                ]);
                return response()->json([
                    'error' => 'Invalid or expired OTP'
                ], 400);
            }

            // Mark OTP as used
            $otpRecord->update(['is_used' => true]);

            // Update tenant phone verification status
            $tenant = Tenant::where('mobile', $request->phone)->first();
            if ($tenant) {
                $tenant->phone_verified = true;
                $tenant->save();
                \Log::info('Tenant phone verification updated', [
                    'tenant_id' => $tenant->id,
                    'mobile' => $request->phone
                ]);
            }

            // Log successful verification
            try {
                $otpLogData = [
                    'phone' => $request->phone,
                    'otp' => $request->otp,
                    'type' => 'profile_update',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'status' => 'verified',
                ];

                // Add user_id if available
                if ($request->user()) {
                    $otpLogData['user_id'] = $request->user()->id;
                }

                // Add session_id if available
                if (session()->isStarted()) {
                    $otpLogData['session_id'] = session()->getId();
                }

                \App\Models\OtpLog::create($otpLogData);
            } catch (\Exception $e) {
                \Log::error('Failed to create OTP log: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            \Log::info('OTP verified successfully', [
                'mobile' => $request->phone,
                'otp_id' => $otpRecord->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant OTP verification error: ' . $e->getMessage(), [
                'mobile' => $request->phone,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return detailed error for debugging
            return response()->json([
                'error' => 'Failed to verify OTP: ' . $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
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
