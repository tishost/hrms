<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\OtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SystemSetting;

class OtpController extends Controller
{
    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request)
    {
        // Check if OTP system is enabled
        $otpSettings = OtpSetting::getSettings();
        if (!$otpSettings->is_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification system is currently disabled'
            ], 503);
        }

        // Check if OTP is required for this action
        // For profile_update, apply registration requirement settings and templates,
        // but keep record type as the requested type to avoid mismatches during verify
        $requestedType = $request->type;
        $isProfileUpdate = ($requestedType === 'profile_update');
        $effectiveType = $isProfileUpdate ? 'registration' : $requestedType; // for settings/templates only
        $otpRecordType = $requestedType; // persist OTP with the original requested type
        if (!$otpSettings->isOtpRequiredFor($effectiveType)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification is not required for this action'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'type' => 'required|in:registration,login,reset,profile_update',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        // Use effective type for OTP generation and templates
        $type = $effectiveType;
        $userId = $request->user_id;

        // Load OTP settings
        $otpSettings = OtpSetting::getSettings();

        // Enforce daily OTP send limit (use logs, not OTP records)
        $otpLimitSetting = SystemSetting::where('key', 'otp_send_limit')->first();
        $otpLimit = $otpLimitSetting ? intval($otpLimitSetting->value) : 5;
        if ($otpLimit > 0) {
            // Check if this phone was recently reset by admin (within last 5 minutes)
            $recentReset = \App\Models\OtpLog::where('phone', $phone)
                ->where('status', 'sent')
                ->where('reason', 'admin_reset')
                ->where('created_at', '>', now()->subMinutes(5))
                ->exists();

            if ($recentReset) {
                // If recently reset by admin, start fresh count
                $todaySendCount = 0;
                \Log::info("Phone {$phone} was recently reset by admin, starting fresh daily limit count");
            } else {
                // Normal daily limit check
                $todaySendCount = \App\Models\OtpLog::where('phone', $phone)
                    ->where('type', $otpRecordType)
                    ->where('status', 'sent')
                    ->whereDate('created_at', now()->toDateString())
                    ->where('reason', '!=', 'admin_reset') // Exclude admin reset logs
                    ->count();
            }

            if ($todaySendCount >= $otpLimit) {
                // Log blocked attempt
                try {
                    \App\Models\OtpLog::create([
                        'phone' => $phone,
                        'otp' => null,
                        'type' => $otpRecordType,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'blocked',
                        'reason' => 'daily_limit',
                        'user_id' => $userId ?? optional($request->user())->id,
                        'session_id' => session()->getId(),
                    ]);
                } catch (\Exception $e) {
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Daily OTP limit reached',
                    'error_type' => 'daily_limit',
                    'details' => [
                        'phone' => $phone,
                        'limit' => $otpLimit,
                        'reset_time' => 'tomorrow',
                        'message' => 'You have reached the daily OTP send limit. Please try again tomorrow or contact admin for assistance.'
                    ]
                ], 429);
            }
        }

        try {
            // If OTP is for profile update, allow existing phone (do not block), regardless of auth state
            if ($effectiveType === 'registration') {
                if (!$isProfileUpdate) {
                    // True registration flow: if unauthenticated, block duplicate numbers
                    if (!$request->user()) {
                        $existingOwner = \App\Models\Owner::where('phone', $phone)->first();
                        if ($existingOwner) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Phone number is already registered'
                            ], 422);
                        }
                    }
                }
                // profile_update flow falls through without duplicate check
            }

            // If a valid OTP already exists within expiry, reuse it instead of generating a new one
            $existing = Otp::where('phone', $phone)
                ->where('type', $otpRecordType)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existing) {
                $expiresInSeconds = max(0, $existing->expires_at->diffInSeconds(now()));
                $resendCooldown = (int) $otpSettings->resend_cooldown_seconds;
                if ($isProfileUpdate) {
                    $resendCooldown = 30; // 30 seconds for profile update (more user-friendly)
                }
                $elapsedSinceCreate = now()->diffInSeconds($existing->created_at);
                $resendInSeconds = max(0, $resendCooldown - $elapsedSinceCreate);

                // Log send attempt (reuse)
                try {
                    \App\Models\OtpLog::create([
                        'phone' => $phone,
                        'otp' => $existing->otp,
                        'type' => $otpRecordType,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'sent',
                        'user_id' => $userId ?? optional($request->user())->id,
                        'session_id' => session()->getId(),
                        'reason' => 'reuse_existing',
                    ]);
                } catch (\Exception $e) {
                }

                return response()->json([
                    'success' => true,
                    'message' => 'OTP already sent',
                    'otp' => $existing->otp, // Remove in production
                    'expires_in' => ceil($expiresInSeconds / 60), // minutes
                    'expires_in_seconds' => $expiresInSeconds,
                    'resend_in_seconds' => $resendInSeconds,
                ]);
            }

            // Generate OTP with settings (effective type)
            $otp = Otp::generateOtp($phone, $otpRecordType, $otpSettings->otp_length);

            // Log sent OTP
            try {
                \App\Models\OtpLog::create([
                    'phone' => $phone,
                    'otp' => $otp->otp,
                    'type' => $otpRecordType,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'status' => 'sent',
                    'user_id' => $userId ?? optional($request->user())->id,
                    'session_id' => session()->getId(),
                ]);
            } catch (\Exception $e) {
            }

            // Send SMS via SMS service
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendSms($phone, "Your OTP is: {$otp->otp}. Valid for {$otpSettings->otp_expiry_minutes} minutes. - Bari Manager");

            // Log SMS result
            \Log::info('OTP SMS send result', [
                'phone' => $phone,
                'otp' => $otp->otp,
                'sms_success' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['message'] ?? 'No message',
                'sms_response' => $smsResult
            ]);

            $resendCooldownForType = (int) $otpSettings->resend_cooldown_seconds;
            if ($isProfileUpdate) {
                $resendCooldownForType = 300; // 5 minutes for profile update
            }

            return response()->json([
                'success' => true,
                'message' => $smsResult['success'] ? 'OTP sent successfully via SMS' : 'OTP generated but SMS delivery failed',
                'otp' => $otp->otp, // Keep for testing
                'sms_delivery' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['message'] ?? 'SMS not sent',
                'expires_in' => $otpSettings->otp_expiry_minutes, // minutes
                'expires_in_seconds' => $otpSettings->otp_expiry_minutes * 60,
                'resend_in_seconds' => $resendCooldownForType,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'otp' => 'required|string|size:6',
            'type' => 'required|in:registration,login,reset,profile_update',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otp = $request->otp;
        $type = $request->type;
        $userId = $request->user_id;

        try {
            \Log::info('OTP verification attempt', [
                'phone' => $phone,
                'otp_length' => strlen($otp),
                'type' => $type,
                'user_id' => $userId
            ]);

            // Verify first; if correct, succeed regardless of prior failed attempts
            $settings = OtpSetting::getSettings();
            $maxAttempts = (int) $settings->max_attempts;
            $latestOtp = \App\Models\Otp::where('phone', $phone)
                ->where('type', $type)
                ->orderBy('created_at', 'desc')
                ->first();

            \Log::info('OTP lookup result', [
                'phone' => $phone,
                'type' => $type,
                'latest_otp_found' => $latestOtp ? true : false,
                'latest_otp_id' => $latestOtp ? $latestOtp->id : null
            ]);

            $isValid = Otp::verifyOtp($phone, $otp, $type);

            if ($isValid) {
                // Update phone_verified status for profile_update and registration
                if ($type === 'profile_update' || $type === 'registration') {
                    // Update Owner phone verification
                    $owner = \App\Models\Owner::where('phone', $phone)->first();
                    if ($owner) {
                        $owner->phone_verified = true;
                        $owner->save();
                    }

                    // Update Tenant phone verification
                    $tenant = \App\Models\Tenant::where('mobile', $phone)->first();
                    if ($tenant) {
                        $tenant->phone_verified = true;
                        $tenant->save();
                    }
                }
                // Log verified
                try {
                    $logData = [
                        'phone' => $phone,
                        'otp' => $otp,
                        'type' => $type,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'verified',
                    ];

                    if ($userId) {
                        $logData['user_id'] = $userId;
                    } elseif ($request->user()) {
                        $logData['user_id'] = $request->user()->id;
                    }

                    if (session()->isStarted()) {
                        $logData['session_id'] = session()->getId();
                    }

                    \App\Models\OtpLog::create($logData);
                    \Log::info('OTP verification logged successfully', [
                        'phone' => $phone,
                        'type' => $type,
                        'status' => 'verified'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to log OTP verification', [
                        'phone' => $phone,
                        'type' => $type,
                        'error' => $e->getMessage()
                    ]);
                }
                // Get user details for response
                $userDetails = null;
                if ($type === 'profile_update' || $type === 'registration') {
                    // Check if it's a tenant
                    $tenant = \App\Models\Tenant::where('mobile', $phone)->first();
                    if ($tenant) {
                        $userDetails = [
                            'type' => 'tenant',
                            'name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
                            'phone' => $tenant->mobile,
                            'phone_verified' => true
                        ];
                    }

                    // Check if it's an owner
                    $owner = \App\Models\Owner::where('phone', $phone)->first();
                    if ($owner) {
                        $userDetails = [
                            'type' => 'owner',
                            'name' => $owner->name,
                            'phone' => $owner->phone,
                            'phone_verified' => true
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => '🎉 Mobile number verified successfully!',
                    'data' => [
                        'phone' => $phone,
                        'verification_status' => 'verified',
                        'user_details' => $userDetails,
                        'verified_at' => now()->toISOString()
                    ]
                ]);
            } else {
                // Before enforcing limit, compute failed attempts AFTER the latest OTP was created
                $failedAttempts = 0;
                if ($maxAttempts > 0 && $latestOtp) {
                    $failedAttempts = \App\Models\OtpLog::where('phone', $phone)
                        ->where('type', $type)
                        ->where('status', 'failed')
                        ->where('created_at', '>', $latestOtp->created_at)
                        ->count();
                }

                // Log failed verification
                try {
                    $logData = [
                        'phone' => $phone,
                        'otp' => $otp,
                        'type' => $type,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'failed',
                    ];

                    if ($userId) {
                        $logData['user_id'] = $userId;
                    } elseif ($request->user()) {
                        $logData['user_id'] = $request->user()->id;
                    }

                    if (session()->isStarted()) {
                        $logData['session_id'] = session()->getId();
                    }

                    \App\Models\OtpLog::create($logData);
                    \Log::info('OTP failed verification logged successfully', [
                        'phone' => $phone,
                        'type' => $type,
                        'status' => 'failed'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to log OTP failed verification', [
                        'phone' => $phone,
                        'type' => $type,
                        'error' => $e->getMessage()
                    ]);
                }

                // After logging this failed attempt, check if limit is exceeded
                $failedAttemptsPlusOne = $failedAttempts + 1;
                if ($maxAttempts > 0 && $latestOtp && $failedAttemptsPlusOne >= $maxAttempts) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP verification attempt limit exceeded. Please request a new OTP.',
                        'resend_required' => true,
                        'failed_attempts' => $failedAttemptsPlusOne,
                        'max_attempts' => $maxAttempts,
                    ], 429);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP',
                    'failed_attempts' => $failedAttemptsPlusOne,
                    'max_attempts' => $maxAttempts,
                ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('OTP verification error', [
                'phone' => $phone,
                'otp' => $otp,
                'type' => $type,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP: ' . $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'type' => 'required|in:registration,login,reset,profile_update',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $requestedType = $request->type;
        $isProfileUpdate = ($requestedType === 'profile_update');
        $effectiveType = $isProfileUpdate ? 'registration' : $requestedType; // for settings/templates only
        $otpRecordType = $requestedType; // keep original type for persistence/logs
        $userId = $request->user_id;

        try {
            // Load settings and basic guards
            $settings = OtpSetting::getSettings();
            if (!$settings->is_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification system is currently disabled'
                ], 503);
            }

            if (!$settings->isOtpRequiredFor($effectiveType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification is not required for this action'
                ], 400);
            }

            // Enforce daily send limit using logs (same as sendOtp)
            $otpLimitSetting = SystemSetting::where('key', 'otp_send_limit')->first();
            $otpLimit = $otpLimitSetting ? intval($otpLimitSetting->value) : 5;
            if ($otpLimit > 0) {
                // Admin reset bypass window
                $recentReset = \App\Models\OtpLog::where('phone', $phone)
                    ->where('status', 'sent')
                    ->where('reason', 'admin_reset')
                    ->where('created_at', '>', now()->subMinutes(5))
                    ->exists();

                if ($recentReset) {
                    $todaySendCount = 0;
                } else {
                    $todaySendCount = \App\Models\OtpLog::where('phone', $phone)
                        ->where('type', $otpRecordType)
                        ->where('status', 'sent')
                        ->whereDate('created_at', now()->toDateString())
                        ->where('reason', '!=', 'admin_reset')
                        ->count();
                }

                if ($todaySendCount >= $otpLimit) {
                    // Log blocked attempt
                    try {
                        \App\Models\OtpLog::create([
                            'phone' => $phone,
                            'otp' => null,
                            'type' => $otpRecordType,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->header('User-Agent'),
                            'status' => 'blocked',
                            'reason' => 'daily_limit',
                            'user_id' => $userId ?? optional($request->user())->id,
                            'session_id' => session()->getId(),
                        ]);
                    } catch (\Exception $e) {
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Daily OTP limit reached',
                        'error_type' => 'daily_limit',
                        'details' => [
                            'phone' => $phone,
                            'limit' => $otpLimit,
                            'reset_time' => 'tomorrow',
                            'message' => 'You have reached the daily OTP send limit. Please try again tomorrow or contact admin for assistance.'
                        ]
                    ], 429);
                }
            }

            // Enforce cooldown
            $cooldownSeconds = (int) $settings->resend_cooldown_seconds;
            if ($requestedType === 'profile_update') {
                $cooldownSeconds = 30; // 30 seconds for profile update (more user-friendly)
            }
            // Check if there's a recent OTP within cooldown window
            $recentOtp = Otp::where('phone', $phone)
                ->where('type', $otpRecordType)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($recentOtp && $recentOtp->created_at->gt(now()->subSeconds($cooldownSeconds))) {
                $remaining = max(0, $cooldownSeconds - now()->diffInSeconds($recentOtp->created_at));
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before requesting another OTP',
                    'resend_in_seconds' => $remaining,
                ], 429);
            }

            // Generate new OTP
            $otp = Otp::generateOtp($phone, $otpRecordType);

            // Log sent OTP for daily limit accounting
            try {
                \App\Models\OtpLog::create([
                    'phone' => $phone,
                    'otp' => $otp->otp,
                    'type' => $otpRecordType,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'status' => 'sent',
                    'user_id' => $userId ?? optional($request->user())->id,
                    'session_id' => session()->getId(),
                    'reason' => 'resend',
                ]);
            } catch (\Exception $e) {
            }

            // Send SMS via SMS service
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendSms($phone, "Your OTP is: {$otp->otp}. Valid for {$settings->otp_expiry_minutes} minutes. - Bari Manager");

            // Log SMS result
            \Log::info('OTP Resend SMS result', [
                'phone' => $phone,
                'otp' => $otp->otp,
                'sms_success' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['message'] ?? 'No message',
                'sms_response' => $smsResult
            ]);

            return response()->json([
                'success' => true,
                'message' => $smsResult['success'] ? 'OTP resent successfully via SMS' : 'OTP regenerated but SMS delivery failed',
                'otp' => $otp->otp, // Keep for testing
                'sms_delivery' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['message'] ?? 'SMS not sent',
                'expires_in' => $settings->otp_expiry_minutes, // minutes
                'expires_in_seconds' => $settings->otp_expiry_minutes * 60,
                'resend_in_seconds' => $cooldownSeconds,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get OTP settings
     */
    public function getOtpSettings()
    {
        try {
            $otpSettings = OtpSetting::getSettings();

            return response()->json([
                'success' => true,
                'settings' => [
                    'is_enabled' => $otpSettings->is_enabled,
                    'registration_required' => $otpSettings->isOtpRequiredFor('registration'),
                    'login_required' => $otpSettings->isOtpRequiredFor('login'),
                    'reset_required' => $otpSettings->isOtpRequiredFor('reset'),
                    'profile_update_required' => $otpSettings->isOtpRequiredFor('profile_update'),
                    'require_otp_for_tenant_registration' => (bool) ($otpSettings->require_otp_for_tenant_registration ?? 0), // Only for tenant profile update
                    'otp_length' => $otpSettings->otp_length,
                    'otp_expiry_minutes' => $otpSettings->otp_expiry_minutes,
                    'resend_cooldown_seconds' => (int) $otpSettings->resend_cooldown_seconds,
                    'max_attempts' => (int) $otpSettings->max_attempts,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get OTP settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
