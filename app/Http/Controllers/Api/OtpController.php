<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
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
            // Check if phone already exists for registration
            if ($type === 'registration') {
                $existingOwner = \App\Models\Owner::where('phone', $phone)->first();
                if ($existingOwner) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone number is already registered'
                    ], 422);
                }
            }

            // Generate OTP
            $otp = Otp::generateOtp($phone, $type);

            // TODO: Integrate with SMS service (Twilio, etc.)
            // For now, we'll return the OTP in response for testing
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp->otp, // Remove this in production
                'expires_in' => 10 // minutes
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
                // If profile_update, set phone_verified true
                if ($type === 'profile_update') {
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
}
