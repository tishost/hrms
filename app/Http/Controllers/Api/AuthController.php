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
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    protected $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

    /**
     * Safely read a field from stdClass without triggering undefined property errors
     */
    private function safeField($record, string $field)
    {
        return (is_object($record) && property_exists($record, $field)) ? $record->{$field} : null;
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
                'district' => $request->district,
                'country' => $request->country ?: 'Bangladesh',
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
                'email_verified_at' => $user->email_verified_at,
                'phone' => $owner ? $owner->phone : $user->phone,
                'address' => $owner ? $owner->address : null,
                'country' => $owner ? $owner->country : null,
                'district' => $owner ? ($owner->district ?? null) : null,
                'gender' => $owner ? $owner->gender : null,
                'phone_verified' => $owner ? (bool)$owner->phone_verified : false,
                'profile_pic' => $owner ? ($owner->profile_pic ?? null) : null,
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
            'identifier' => 'required|string', // Single field for email or mobile
        ]);

        try {
            $identifier = $request->identifier;
            $user = null;

            // Check if identifier is email or mobile
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                // Search by email
                $user = User::where('email', $identifier)->first();
            } else {
                // Search by mobile (remove any non-digit characters)
                $mobile = preg_replace('/[^0-9]/', '', $identifier);
                $user = User::where('phone', $mobile)->first();
            }

            if (!$user) {
                // Don't reveal if user exists or not for security reasons
                return response()->json([
                    'success' => false,
                    'message' => 'If this email or mobile number is registered, you will receive an OTP shortly.'
                ], 200); // Return 200 instead of 404 to avoid revealing user existence
            }

            // Check if user has email or mobile
            $hasEmail = !empty($user->email);
            $hasMobile = !empty($user->phone);

            if (!$hasEmail && !$hasMobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'No email or mobile found for password reset'
                ], 400);
            }

            // Generate OTP for password reset
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP in password_reset_tokens table
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => $otp,
                    'created_at' => now()
                ]
            );

            $results = [];
            $sentMethods = [];

            // Send OTP via SMS if mobile exists
            if ($hasMobile) {
                try {
                    $smsResult = \App\Helpers\NotificationHelper::sendOtpSms($user->phone, $otp);
                    $results['sms'] = $smsResult;
                    if ($smsResult['success']) {
                        $sentMethods[] = 'SMS';
                    }
                } catch (\Exception $e) {
                    \Log::error('SMS sending failed: ' . $e->getMessage());
                    $results['sms'] = ['success' => false, 'message' => 'Failed to send SMS'];
                }
            }

            // Send OTP via Email if email exists
            if ($hasEmail) {
                try {
                    $emailResult = \App\Helpers\NotificationHelper::sendPasswordResetEmail($user, $otp);
                    $results['email'] = $emailResult;
                    if ($emailResult['success']) {
                        $sentMethods[] = 'Email';
                    }
                } catch (\Exception $e) {
                    \Log::error('Email sending failed: ' . $e->getMessage());
                    $results['email'] = ['success' => false, 'message' => 'Failed to send email'];
                }
            }

            // Check if at least one method succeeded
            $successCount = 0;
            foreach ($results as $method => $result) {
                if ($result['success']) {
                    $successCount++;
                }
            }

            if ($successCount > 0) {
                $methodText = implode(' and ', $sentMethods);
                return response()->json([
                    'success' => true,
                    'message' => "OTP sent via {$methodText}",
                    'methods' => $sentMethods,
                    'results' => $results
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP via any method',
                    'results' => $results
                ], 500);
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
                'identifier' => 'required|string', // Single field for email or mobile
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
            $identifier = $request->identifier;
            $otp = $request->otp;
            $user = null;

            // Check if identifier is email or mobile
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                // Search by email
                $user = User::where('email', $identifier)->first();
            } else {
                // Search by mobile (remove any non-digit characters)
                $mobile = preg_replace('/[^0-9]/', '', $identifier);
                $user = User::where('phone', $mobile)->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP or user information'
                ], 400);
            }

            // Check if OTP matches the stored token and is not expired
            // For password reset, OTP is always stored with email in password_reset_tokens table
            $resetToken = \DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->where('token', $otp)
                ->where('created_at', '>', now()->subMinutes(10))
                ->first();
                
            if (!$resetToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
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
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'identifier' => 'required|string', // Single field for email or mobile
        ]);

        try {
            $identifier = $request->identifier;
            $otp = $request->otp;
            $password = $request->password;
            $user = null;

            // Check if identifier is email or mobile
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                // Search by email
                $user = User::where('email', $identifier)->first();
            } else {
                // Search by mobile (remove any non-digit characters)
                $mobile = preg_replace('/[^0-9]/', '', $identifier);
                $user = User::where('phone', $mobile)->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request or user information'
                ], 400);
            }

            // Verify OTP matches and is not expired
            // For password reset, OTP is always stored with email in password_reset_tokens table
            $resetToken = \DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->where('token', $otp)
                ->where('created_at', '>', now()->subMinutes(10))
                ->first();
                
            if (!$resetToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($password)
            ]);
            
            // Delete the used reset token
            \DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->delete();

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

    /**
     * Check mobile number in database and return role
     */
    public function checkMobileRole(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:15',
        ]);

        $mobile = $request->mobile;

        try {
            // Check in tenants table (conditionally support 'mobile'/'phone')
            $tenantQuery = DB::table('tenants');
            $tenantHasMobile = Schema::hasColumn('tenants', 'mobile');
            $tenantHasPhone  = Schema::hasColumn('tenants', 'phone');
            if ($tenantHasMobile && $tenantHasPhone) {
                $tenantQuery->where(function($q) use ($mobile) {
                    $q->where('mobile', $mobile)->orWhere('phone', $mobile);
                });
            } elseif ($tenantHasMobile) {
                $tenantQuery->where('mobile', $mobile);
            } elseif ($tenantHasPhone) {
                $tenantQuery->where('phone', $mobile);
            } else {
                $tenantQuery->whereRaw('1=0'); // no such column
            }
            $tenant = $tenantQuery->first();
            if ($tenant) {
                return response()->json([
                    'success' => true,
                    'role' => 'tenant',
                    'message' => 'Mobile number found in tenant records',
                    'user_data' => [
                        'name' => $tenant->name,
                        'email' => $tenant->email,
                        'mobile' => $tenant->mobile ?? $tenant->phone,
                    ]
                ]);
            }

            // Check in owners table (conditionally support 'mobile'/'phone')
            $ownerQuery = DB::table('owners');
            $ownerHasMobile = Schema::hasColumn('owners', 'mobile');
            $ownerHasPhone  = Schema::hasColumn('owners', 'phone');
            if ($ownerHasMobile && $ownerHasPhone) {
                $ownerQuery->where(function($q) use ($mobile) {
                    $q->where('mobile', $mobile)->orWhere('phone', $mobile);
                });
            } elseif ($ownerHasMobile) {
                $ownerQuery->where('mobile', $mobile);
            } elseif ($ownerHasPhone) {
                $ownerQuery->where('phone', $mobile);
            } else {
                $ownerQuery->whereRaw('1=0');
            }
            $owner = $ownerQuery->first();
            if ($owner) {
                return response()->json([
                    'success' => true,
                    'role' => 'owner',
                    'message' => 'Mobile number found in owner records',
                    'user_data' => [
                        'name' => $owner->name,
                        'email' => $owner->email,
                        'mobile' => $owner->mobile ?? $owner->phone,
                    ]
                ]);
            }

            // Not found in any table
            return response()->json([
                'success' => true,
                'role' => null,
                'message' => 'Mobile number not found in database',
                'user_data' => null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking mobile number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check Google email in database and return role
     */
    public function checkGoogleRole(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        try {
            // Check in tenants table
            $tenant = DB::table('tenants')->where('email', $email)->first();
            if ($tenant) {
                // Try to locate linked user and create token
                $linkedUser = User::where('email', $email)->first();
                $token = $linkedUser ? $linkedUser->createToken('auth_token')->plainTextToken : null;
                return response()->json([
                    'success' => true,
                    'role' => 'tenant',
                    'message' => 'Email found in tenant records',
                    'user_data' => [
                        'name' => $this->safeField($tenant, 'name'),
                        'email' => $this->safeField($tenant, 'email') ?? $email,
                        'mobile' => $this->safeField($tenant, 'mobile') ?? $this->safeField($tenant, 'phone'),
                        'token' => $token,
                    ]
                ]);
            }

            // Check in owners table
            $owner = DB::table('owners')->where('email', $email)->first();
            if ($owner) {
                // Try to locate linked user and create token
                $linkedUser = User::where('email', $email)->first();
                if (!$linkedUser && $this->safeField($owner, 'user_id')) {
                    $linkedUser = User::find($this->safeField($owner, 'user_id'));
                }
                $token = $linkedUser ? $linkedUser->createToken('auth_token')->plainTextToken : null;
                return response()->json([
                    'success' => true,
                    'role' => 'owner',
                    'message' => 'Email found in owner records',
                    'user_data' => [
                        'name' => $this->safeField($owner, 'name'),
                        'email' => $this->safeField($owner, 'email') ?? $email,
                        'mobile' => $this->safeField($owner, 'mobile') ?? $this->safeField($owner, 'phone'),
                        'token' => $token,
                    ]
                ]);
            }

            // Not found in any table
            return response()->json([
                'success' => true,
                'role' => null,
                'message' => 'Email not found in database',
                'user_data' => null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking email: ' . $e->getMessage()
            ], 500);
        }
    }
}
