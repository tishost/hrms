<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Owner;
use App\Models\Otp;
use App\Helpers\NotificationHelper;
use App\Services\OtpSecurityService;
use Carbon\Carbon;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        
        // Find user by email
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        // Generate password reset token
        $token = Password::createToken($user);
        
        // Send password reset email using NotificationHelper
        $emailResult = NotificationHelper::sendPasswordResetEmail($user, $token);
        
        if ($emailResult['success']) {
            return back()->with('status', 'We have emailed your password reset link.');
        } else {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Failed to send password reset email. Please try again.']);
        }
    }

    /**
     * Handle OTP request for mobile password reset
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'string', 'min:10', 'max:15'],
        ]);

        $mobile = $request->mobile;
        
        // Check if IP is blocked
        if (OtpSecurityService::isIpBlocked($request->ip())) {
            return response()->json([
                'success' => false,
                'message' => 'Your IP address is temporarily blocked due to suspicious activity.'
            ], 403);
        }

        // Check if phone is blocked
        if (OtpSecurityService::isPhoneBlocked($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is temporarily blocked due to suspicious activity.'
            ], 403);
        }

        // Find user by mobile number
        $user = User::whereHas('owner', function($query) use ($mobile) {
            $query->where('phone', $mobile);
        })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this mobile number.'
            ], 404);
        }

        // Generate new OTP using existing Otp model
        $otpRecord = Otp::generateOtp($mobile, 'password_reset', 6);

        // Log OTP activity for security monitoring
        OtpSecurityService::logOtpActivity($mobile, $otpRecord->otp, 'password_reset', $request, 'sent', $user->id);

        // Send OTP via SMS using NotificationHelper
        try {
            $smsResult = NotificationHelper::sendOtpSms($mobile, $otpRecord->otp);
            
            if ($smsResult['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to your mobile number.',
                    'otp' => $otpRecord->otp // For testing purposes, remove in production
                ]);
            } else {
                // If SMS fails, still return success but log the SMS error
                \Log::warning("SMS failed for password reset OTP: " . $smsResult['message']);
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to your mobile number.',
                    'otp' => $otpRecord->otp // For testing purposes, remove in production
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP and redirect to password reset
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'string'],
            'otp' => ['required', 'array'],
            'otp.*' => ['required', 'string', 'size:1'],
        ]);

        $mobile = $request->mobile;
        $otp = implode('', $request->otp);
        
        // Check if IP is blocked
        if (OtpSecurityService::isIpBlocked($request->ip())) {
            return response()->json([
                'success' => false,
                'message' => 'Your IP address is temporarily blocked due to suspicious activity.'
            ], 403);
        }

        // Verify OTP using existing Otp model
        if (!Otp::verifyOtp($mobile, $otp, 'password_reset')) {
            // Log failed verification attempt
            OtpSecurityService::logOtpActivity($mobile, $otp, 'password_reset', $request, 'failed');
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please try again.'
            ], 400);
        }

        // Log successful verification
        OtpSecurityService::logOtpActivity($mobile, $otp, 'password_reset', $request, 'verified');

        // Get user by mobile number
        $user = User::whereHas('owner', function($query) use ($mobile) {
            $query->where('phone', $mobile);
        })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Generate password reset token
        $token = Password::createToken($user);

        // Redirect to password reset page with token
        return response()->json([
            'success' => true,
            'redirect_url' => route('password.reset', ['token' => $token, 'email' => $user->email])
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'string', 'min:10', 'max:15'],
        ]);

        $mobile = $request->mobile;
        
        // Check if IP is blocked
        if (OtpSecurityService::isIpBlocked($request->ip())) {
            return response()->json([
                'success' => false,
                'message' => 'Your IP address is temporarily blocked due to suspicious activity.'
            ], 403);
        }

        // Check if phone is blocked
        if (OtpSecurityService::isPhoneBlocked($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is temporarily blocked due to suspicious activity.'
            ], 403);
        }

        // Check if user exists
        $user = User::whereHas('owner', function($query) use ($mobile) {
            $query->where('phone', $mobile);
        })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this mobile number.'
            ], 404);
        }

        // Generate new OTP using existing Otp model
        $otpRecord = Otp::generateOtp($mobile, 'password_reset', 6);

        // Log OTP activity for security monitoring
        OtpSecurityService::logOtpActivity($mobile, $otpRecord->otp, 'password_reset', $request, 'sent', $user->id);

        // Send OTP via SMS using NotificationHelper
        try {
            $smsResult = NotificationHelper::sendOtpSms($mobile, $otpRecord->otp);
            
            if ($smsResult['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP resent successfully to your mobile number.',
                    'otp' => $otpRecord->otp // For testing purposes, remove in production
                ]);
            } else {
                // If SMS fails, still return success but log the SMS error
                \Log::warning("SMS failed for password reset OTP (resend): " . $smsResult['message']);
                return response()->json([
                    'success' => true,
                    'message' => 'OTP resent successfully to your mobile number.',
                    'otp' => $otpRecord->otp // For testing purposes, remove in production
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.'
            ], 500);
        }
    }
}
