<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LoginLogService;
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
    protected $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

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

            // Check OTP settings
            $otpSettings = \App\Models\OtpSetting::getSettings();
            $requiresOtp = $otpSettings->is_enabled && $otpSettings->isOtpRequiredFor('registration');

            // If OTP is required, verify it
            if ($requiresOtp) {
                if (!$request->has('otp')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP is required for registration'
                    ], 422);
                }

                $isValidOtp = \App\Models\Otp::verifyOtp($request->phone, $request->otp, 'registration');
                if (!$isValidOtp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or expired OTP'
                    ], 422);
                }
            }

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Assign Owner role
            $user->assignRole('owner');

            // Create Owner with phone_verified status
            $owner = Owner::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'user_id' => $user->id,
                'total_properties' => 0,
                'total_tenants' => 0,
                'phone_verified' => $requiresOtp ? true : false, // Set based on OTP requirement
            ]);

            // Update user with owner_id
            $user->update(['owner_id' => $owner->id]);

            // Send comprehensive welcome notification (multiple emails + SMS)
            try {
                $notificationResults = \App\Helpers\NotificationHelper::sendComprehensiveWelcome($user);
                \Log::info('Comprehensive welcome notification sent via API', [
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
                \Log::error('Welcome notification failed via API: ' . $e->getMessage());
            }

            // Automatically activate free package for new owner
            $freePlan = \App\Models\SubscriptionPlan::where('price', 0)->first();
            if ($freePlan) {
                \Log::info('Activating free package for new API owner', [
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

                \Log::info('Free subscription created via API', [
                    'subscription_id' => $freeSubscription->id,
                    'owner_id' => $freeSubscription->owner_id,
                    'plan_id' => $freeSubscription->plan_id,
                    'status' => $freeSubscription->status
                ]);
            }

            // Generate token
            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Owner registered successfully',
                'user' => $user,
                'owner' => $owner,
                'token' => $token,
                'role' => 'owner',
                'phone_verified' => $owner->phone_verified
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Role-based login (Owner/Tenant)
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required_without:email|string',
            'email' => 'required_without:mobile|string|email',
            'password' => 'required|string',
        ]);

        try {
            // Check if mobile or email is provided
            $mobile = $request->mobile;
            $email = $request->email;

            $user = null;

            if ($mobile) {
                // Login with mobile
                $user = User::where('phone', $mobile)->first();
            } elseif ($email) {
                // Login with email
                $user = User::where('email', $email)->first();
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                // Log failed login attempt
                try {
                    $this->loginLogService->logLogin($request, null, 'failed', 'Invalid credentials');
                } catch (\Exception $e) {
                    \Log::error('Failed to log login attempt: ' . $e->getMessage());
                }
                
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            // Check role and set owner_id if needed
            if ($user->hasRole('owner')) {
                $role = 'owner';
                // Ensure owner_id is set
                if (!$user->owner_id) {
                    $owner = Owner::where('user_id', $user->id)->first();
                    if ($owner) {
                        $user->update(['owner_id' => $owner->id]);
                    }
                }
            } elseif ($user->hasRole('tenant')) {
                $role = 'tenant';
            } else {
                // Log failed login attempt (unauthorized role)
                try {
                    $this->loginLogService->logLogin($request, null, 'failed', 'Unauthorized role');
                } catch (\Exception $e) {
                    \Log::error('Failed to log login attempt: ' . $e->getMessage());
                }
                
                return response()->json([
                    'error' => 'Unauthorized role'
                ], 403);
            }

            // Log successful login
            try {
                $this->loginLogService->logLogin($request, $user, 'success');
            } catch (\Exception $e) {
                \Log::error('Failed to log successful login: ' . $e->getMessage());
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'role' => $role,
                'token' => $token,
                'message' => 'Login successful'
            ]);

        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Login failed'
            ], 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            // Log logout
            $this->loginLogService->logLogout($user);
        }
        
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    // Authenticated user info
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $owner = \App\Models\Owner::where('user_id', $user->id)->first();

            // Build full name
            $fullName = '';
            if ($owner) {
                $firstName = $owner->first_name ?? $owner->name ?? '';
                $lastName = $owner->last_name ?? '';
                $fullName = trim($firstName . ' ' . $lastName);
            } else {
                $fullName = $user->name ?? '';
            }

            return response()->json([
                'id' => $user->id,
                'name' => $fullName,
                'first_name' => $owner ? ($owner->first_name ?? $owner->name ?? '') : ($user->name ?? ''),
                'last_name' => $owner ? ($owner->last_name ?? '') : '',
                'email' => $user->email,
                'phone' => $owner ? $owner->phone : $user->phone,
                'address' => $owner ? $owner->address : null,
                'country' => $owner ? $owner->country : null,
                'gender' => $owner ? $owner->gender : null,
                'phone_verified' => $owner ? (bool)$owner->phone_verified : false,
                'owner_id' => $owner ? $owner->id : null,
                'tenant_id' => $user->tenant_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    // Get user profile with owner/tenant detection
    public function getUserProfile(Request $request)
    {
        try {
            $user = $request->user();

            $profileData = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone,
                'roles' => $user->roles->pluck('name'),
            ];

            // Check if user is owner
            if ($user->owner) {
                $profileData['owner'] = [
                    'id' => $user->owner->id,
                    'first_name' => $user->owner->first_name ?? $user->owner->name,
                    'last_name' => $user->owner->last_name ?? '',
                    'mobile' => $user->owner->mobile ?? $user->owner->phone,
                    'email' => $user->owner->email ?? $user->email,
                ];
                $profileData['tenant'] = null;
            }
            // Check if user is tenant
            else if ($user->tenant) {
                $profileData['owner'] = null;
                $profileData['tenant'] = [
                    'id' => $user->tenant->id,
                    'first_name' => $user->tenant->first_name,
                    'last_name' => $user->tenant->last_name,
                    'mobile' => $user->tenant->mobile,
                    'email' => $user->tenant->email,
                ];
            }
            // Neither owner nor tenant
            else {
                $profileData['owner'] = null;
                $profileData['tenant'] = null;
            }

            return response()->json([
                'success' => true,
                'user' => $profileData
            ]);

        } catch (\Exception $e) {
            \Log::error('Get user profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get user profile: ' . $e->getMessage()
            ], 500);
        }
    }

    // Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required_without:mobile|email',
            'mobile' => 'required_without:email|string',
        ]);

        try {
            $email = $request->email;
            $mobile = $request->mobile;
            $user = null;

            // Find user by email or mobile
            if ($email) {
                $user = User::where('email', $email)->first();
            } elseif ($mobile) {
                $user = User::where('phone', $mobile)->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with provided email/mobile'
                ], 404);
            }

            // Check if user has email or mobile
            $hasEmail = !empty($user->email);
            $hasMobile = !empty($user->phone);

            // Determine reset method
            if ($hasEmail && $hasMobile) {
                // Both available, use the one provided in request
                $resetMethod = $email ? 'email' : 'mobile';
            } elseif ($hasEmail) {
                $resetMethod = 'email';
            } elseif ($hasMobile) {
                $resetMethod = 'mobile';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No email or mobile found for password reset'
                ], 400);
            }

            // Generate reset token
            $token = \Str::random(60);
            $user->update([
                'password_reset_token' => $token,
                'password_reset_expires_at' => now()->addHours(1)
            ]);

            if ($resetMethod === 'email') {
                // Use NotificationHelper to send password reset email with template
                \App\Helpers\NotificationHelper::sendPasswordResetEmail($user, $token);

                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email',
                    'method' => 'email'
                ]);

            } else {
                // Send OTP for mobile
                $otp = \App\Models\Otp::generateOtp($user->phone, 'password_reset');
                
                // Use NotificationHelper to send OTP SMS with template
                \App\Helpers\NotificationHelper::sendOtpSms($user->phone, $otp);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent to your mobile number',
                    'method' => 'mobile'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Forgot password error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process password reset request'
            ], 500);
        }
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required_without:mobile|email',
                'mobile' => 'required_without:email|string',
                'otp' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $email = $request->email;
            $mobile = $request->mobile;
            $otp = $request->otp;

            $user = null;

            // Find user
            if ($email) {
                $user = User::where('email', $email)->first();
            } elseif ($mobile) {
                $user = User::where('phone', $mobile)->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Verify OTP for mobile
            if ($mobile && !\App\Models\Otp::verifyOtp($mobile, $otp, 'password_reset')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            // For email, we'll verify the token later in resetPassword
            if ($email) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verification will be done during password reset'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Verify OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP'
            ], 500);
        }
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'required_without:mobile|email',
            'mobile' => 'required_without:email|string',
            'otp' => 'required_with:mobile|string',
        ]);

        try {
            $email = $request->email;
            $mobile = $request->mobile;
            $token = $request->token;
            $password = $request->password;
            $otp = $request->otp;

            $user = null;

            // Find user
            if ($email) {
                $user = User::where('email', $email)
                    ->where('password_reset_token', $token)
                    ->where('password_reset_expires_at', '>', now())
                    ->first();
            } elseif ($mobile) {
                $user = User::where('phone', $mobile)->first();
                
                // Verify OTP for mobile reset
                if ($user && !\App\Models\Otp::verifyOtp($mobile, $otp, 'password_reset')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or expired OTP'
                    ], 400);
                }
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($password),
                'password_reset_token' => null,
                'password_reset_expires_at' => null
            ]);

            // Log the password reset
            $this->loginLogService->logLogin($request, $user, 'success', 'Password reset successful');

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Reset password error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password'
            ], 500);
        }
    }
}
