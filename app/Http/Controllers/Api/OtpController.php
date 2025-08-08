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
        // For profile_update, apply registration requirement settings
        $requestedType = $request->type;
        $effectiveType = $requestedType === 'profile_update' ? 'registration' : $requestedType;
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
        $type = $request->type;

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
            // If OTP is for profile update, allow existing phone (do not block)
            if ($type === 'registration') {
                // Allow existing phone when request originally came for profile_update
                if ($request->type !== 'profile_update') {
                    $existingOwner = \App\Models\Owner::where('phone', $phone)->first();
                    if ($existingOwner) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Phone number is already registered'
                        ], 422);
                    }
                }
            }

            // Generate OTP with settings
            $otp = Otp::generateOtp($phone, $type, $otpSettings->otp_length);

            // TODO: Integrate with SMS service (Twilio, etc.)
            // For now, we'll return the OTP in response for testing
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp->otp, // Remove this in production
                'expires_in' => $otpSettings->otp_expiry_minutes // minutes
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
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully'
                ]);
            } else {
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
            // Check if there's a recent OTP (within 1 minute)
            $recentOtp = Otp::where('phone', $phone)
                ->where('type', $type)
                ->where('created_at', '>', now()->subMinute())
                ->first();

            if ($recentOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait 1 minute before requesting another OTP'
                ], 429);
            }

            // Generate new OTP
            $otp = Otp::generateOtp($phone, $type);

            // TODO: Integrate with SMS service
            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
                'otp' => $otp->otp, // Remove this in production
                'expires_in' => 10 // minutes
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
