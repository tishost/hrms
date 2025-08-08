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
        // For profile_update, apply registration requirement settings and templates
        $requestedType = $request->type;
        $isProfileUpdate = ($requestedType === 'profile_update');
        $effectiveType = $isProfileUpdate ? 'registration' : $requestedType;
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

        // Load OTP settings
        $otpSettings = OtpSetting::getSettings();

        // Enforce daily OTP send limit
        $otpLimitSetting = SystemSetting::where('key', 'otp_send_limit')->first();
        $otpLimit = $otpLimitSetting ? intval($otpLimitSetting->value) : 5;
        $todayCount = Otp::where('phone', $phone)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        if ($todayCount >= $otpLimit) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the daily OTP send limit. Please try again tomorrow.'
            ], 429);
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
                ->where('type', $type)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existing) {
                $expiresInSeconds = max(0, $existing->expires_at->diffInSeconds(now()));
                $resendCooldown = (int) $otpSettings->resend_cooldown_seconds;
                if ($isProfileUpdate) {
                    $resendCooldown = 300; // 5 minutes for profile update
                }
                $elapsedSinceCreate = now()->diffInSeconds($existing->created_at);
                $resendInSeconds = max(0, $resendCooldown - $elapsedSinceCreate);

                // Log send attempt (reuse)
                try {
                    \App\Models\OtpLog::create([
                        'phone' => $phone,
                        'otp' => $existing->otp,
                        'type' => $type,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'sent',
                        'user_id' => optional($request->user())->id,
                        'session_id' => session()->getId(),
                        'reason' => 'reuse_existing',
                    ]);
                } catch (\Exception $e) {}

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
            $otp = Otp::generateOtp($phone, $type, $otpSettings->otp_length);

            // Log sent OTP
            try {
                \App\Models\OtpLog::create([
                    'phone' => $phone,
                    'otp' => $otp->otp,
                    'type' => $type,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'status' => 'sent',
                    'user_id' => optional($request->user())->id,
                    'session_id' => session()->getId(),
                ]);
            } catch (\Exception $e) {}

            // TODO: Integrate with SMS service (Twilio, etc.)
            // For now, we'll return the OTP in response for testing
            $resendCooldownForType = (int) $otpSettings->resend_cooldown_seconds;
            if ($isProfileUpdate) {
                $resendCooldownForType = 300; // 5 minutes for profile update
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp->otp, // Remove this in production
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

        try {
            $isValid = Otp::verifyOtp($phone, $otp, $type);

            if ($isValid) {
                // Update phone_verified status for profile_update and registration
                if ($type === 'profile_update' || $type === 'registration') {
                    $owner = \App\Models\Owner::where('phone', $phone)->first();
                    if ($owner) {
                        $owner->phone_verified = true;
                        $owner->save();
                    }
                }
                // Log verified
                try {
                    \App\Models\OtpLog::create([
                        'phone' => $phone,
                        'otp' => $otp,
                        'type' => $type,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'verified',
                        'user_id' => optional($request->user())->id,
                        'session_id' => session()->getId(),
                    ]);
                } catch (\Exception $e) {}
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully'
                ]);
            } else {
                // Log failed verification
                try {
                    \App\Models\OtpLog::create([
                        'phone' => $phone,
                        'otp' => $otp,
                        'type' => $type,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'status' => 'failed',
                        'user_id' => optional($request->user())->id,
                        'session_id' => session()->getId(),
                    ]);
                } catch (\Exception $e) {}
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
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
        $type = $request->type;

        try {
            $settings = OtpSetting::getSettings();
            $cooldownSeconds = (int) $settings->resend_cooldown_seconds;
            if ($type === 'profile_update') {
                $cooldownSeconds = 300; // 5 minutes for profile update
            }
            // Check if there's a recent OTP within cooldown window
            $recentOtp = Otp::where('phone', $phone)
                ->where('type', $type)
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
            $otp = Otp::generateOtp($phone, $type);

            // TODO: Integrate with SMS service
            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
                'otp' => $otp->otp, // Remove this in production
                'expires_in' => OtpSetting::getSettings()->otp_expiry_minutes, // minutes
                'expires_in_seconds' => OtpSetting::getSettings()->otp_expiry_minutes * 60,
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
