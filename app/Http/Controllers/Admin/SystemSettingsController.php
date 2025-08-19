<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.system');
    }

    public function update(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'system_currency' => 'required|string|max:10',
                'system_currency_symbol' => 'required|string|max:5',
                'system_currency_position' => 'required|in:left,right,left_space,right_space',
                'system_decimal_places' => 'required|in:0,1,2',
                'system_thousand_separator' => 'required|string|max:1',
                'system_timezone' => 'required|string|max:50',
                'system_date_format' => 'required|string|max:20',
                'system_time_format' => 'required|string|max:20',
                'system_datetime_format' => 'required|string|max:30',
                'system_week_start' => 'required|in:monday,sunday',
                'system_language' => 'required|in:en,bn,ar',
                'system_pagination' => 'required|in:10,20,50,100',
                'system_maintenance_mode' => 'required|in:0,1',
                'system_debug_mode' => 'required|in:0,1',
                'system_email_notifications' => 'required|in:0,1',
                'system_sms_notifications' => 'required|in:0,1',
                'system_push_notifications' => 'required|in:0,1',
                'system_notification_sound' => 'required|in:0,1',
                'system_ads_enabled' => 'required|in:0,1',
            ]);

            // Save all system settings
            $settings = [
                'system_currency', 'system_currency_symbol', 'system_currency_position', 'system_decimal_places',
                'system_thousand_separator', 'system_timezone', 'system_date_format', 'system_time_format',
                'system_datetime_format', 'system_week_start', 'system_language', 'system_pagination',
                'system_maintenance_mode', 'system_debug_mode', 'system_email_notifications', 'system_sms_notifications',
                'system_push_notifications', 'system_notification_sound', 'system_ads_enabled'
            ];

            foreach ($settings as $setting) {
                if ($request->has($setting)) {
                    SystemSetting::setValue($setting, $request->input($setting));
                }
            }

            return redirect()->back()->with('success', 'System settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update system settings: ' . $e->getMessage());
        }
    }
} 