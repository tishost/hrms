<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpSettingsController extends Controller
{
    /**
     * Show OTP settings page
     */
    public function index()
    {
        $settings = OtpSetting::getSettings();
        return view('admin.settings.otp', compact('settings'));
    }

    /**
     * Update OTP settings
     */
    public function update(Request $request)
    {
        // Debug: Log incoming request data
        \Log::info('OTP Settings Update Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'is_enabled' => 'boolean',
            'otp_length' => 'integer|min:4|max:8',
            'otp_expiry_minutes' => 'integer|min:1|max:60',
            'max_attempts' => 'integer|min:1|max:10',
            'resend_cooldown_seconds' => 'integer|min:30|max:300',
            'require_otp_for_registration' => 'boolean',
            'require_otp_for_tenant_registration' => 'boolean',
            'require_otp_for_login' => 'boolean',
            'require_otp_for_password_reset' => 'boolean',
            'otp_message_template' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            \Log::error('OTP Settings Validation Failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle checkbox values properly
        $data = $request->all();

        // Convert checkbox values to boolean
        $data['is_enabled'] = $request->has('is_enabled');
        $data['require_otp_for_registration'] = $request->has('require_otp_for_registration');
        $data['require_otp_for_tenant_registration'] = $request->has('require_otp_for_tenant_registration');
        $data['require_otp_for_login'] = $request->has('require_otp_for_login');
        $data['require_otp_for_password_reset'] = $request->has('require_otp_for_password_reset');

        // Debug: Log processed data
        \Log::info('OTP Settings Processed Data:', $data);

        $settings = OtpSetting::getSettings();

        // Debug: Log current settings before update
        \Log::info('Current OTP Settings:', $settings->toArray());

        $settings->update($data);

        // Debug: Log updated settings
        $settings->refresh();
        \Log::info('Updated OTP Settings:', $settings->toArray());

        return redirect()->back()
            ->with('success', 'OTP settings updated successfully!');
    }

    /**
     * Toggle OTP system on/off
     */
    public function toggle(Request $request)
    {
        $settings = OtpSetting::getSettings();
        $settings->update([
            'is_enabled' => !$settings->is_enabled
        ]);

        $status = $settings->is_enabled ? 'enabled' : 'disabled';

        return response()->json([
            'success' => true,
            'message' => "OTP system {$status} successfully!",
            'is_enabled' => $settings->is_enabled
        ]);
    }

    /**
     * Get OTP settings for API
     */
    public function getSettings()
    {
        $settings = OtpSetting::getSettings();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
