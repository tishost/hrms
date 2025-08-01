<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SmsSettingsController extends Controller
{
    public function __construct()
    {
        // Laravel 11 doesn't support middleware() in controllers
        // Middleware is handled in routes
    }

    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            abort(403, 'Access denied. Authentication required.');
        }

        $user = auth()->user();

        // Check if user has super_admin role
        if ($user->hasRole('super_admin')) {
            return;
        }

        // Check if user is super admin through owner relationship
        if ($user->owner && $user->owner->is_super_admin) {
            return;
        }

        abort(403, 'Access denied. Super admin privileges required.');
    }

    public function index()
    {
        $this->checkSuperAdmin();

        try {
            $settings = SystemSetting::pluck('value', 'key');
            
            // Default SMS settings
            $smsSettings = [
                'sms_enabled' => $settings['sms_enabled'] ?? '0',
                'sms_api_token' => $settings['sms_api_token'] ?? '',
                'sms_sender_id' => $settings['sms_sender_id'] ?? '',
                'sms_rent_reminder_enabled' => $settings['sms_rent_reminder_enabled'] ?? '0',
                'sms_maintenance_update_enabled' => $settings['sms_maintenance_update_enabled'] ?? '0',
                'sms_welcome_message_enabled' => $settings['sms_welcome_message_enabled'] ?? '0',
                'sms_payment_confirmation_enabled' => $settings['sms_payment_confirmation_enabled'] ?? '0',
                'sms_checkout_reminder_enabled' => $settings['sms_checkout_reminder_enabled'] ?? '0',
                'sms_reminder_days_before' => $settings['sms_reminder_days_before'] ?? '3',
                'sms_test_number' => $settings['sms_test_number'] ?? '',
                'sms_working_hours_start' => $settings['sms_working_hours_start'] ?? '09:00',
                'sms_working_hours_end' => $settings['sms_working_hours_end'] ?? '18:00',
                'sms_max_retries' => $settings['sms_max_retries'] ?? '3',
                'sms_retry_delay' => $settings['sms_retry_delay'] ?? '5',
            ];

            return view('admin.settings.sms', compact('smsSettings'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading SMS settings: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'sms_enabled' => 'boolean',
            'sms_api_token' => 'required_if:sms_enabled,1|string|max:255',
            'sms_sender_id' => 'required_if:sms_enabled,1|string|max:20',
            'sms_rent_reminder_enabled' => 'boolean',
            'sms_maintenance_update_enabled' => 'boolean',
            'sms_welcome_message_enabled' => 'boolean',
            'sms_payment_confirmation_enabled' => 'boolean',
            'sms_checkout_reminder_enabled' => 'boolean',
            'sms_reminder_days_before' => 'required|integer|min:1|max:30',
            'sms_test_number' => 'nullable|string|max:20',
            'sms_working_hours_start' => 'required|date_format:H:i',
            'sms_working_hours_end' => 'required|date_format:H:i',
            'sms_max_retries' => 'required|integer|min:1|max:10',
            'sms_retry_delay' => 'required|integer|min:1|max:60',
        ]);

        try {
            // Define all checkbox fields
            $checkboxFields = [
                'sms_enabled', 
                'sms_rent_reminder_enabled', 
                'sms_maintenance_update_enabled', 
                'sms_welcome_message_enabled', 
                'sms_payment_confirmation_enabled', 
                'sms_checkout_reminder_enabled'
            ];

            // Process all form fields
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                // Handle null values for checkboxes
                if ($value === null) {
                    $value = '';
                }

                // Convert boolean values to string
                if (in_array($key, $checkboxFields)) {
                    $value = $value ? '1' : '0';
                }

                SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            // Handle unchecked checkboxes (they don't appear in request)
            foreach ($checkboxFields as $checkboxField) {
                if (!$request->has($checkboxField)) {
                    SystemSetting::updateOrCreate(['key' => $checkboxField], ['value' => '0']);
                }
            }

            // Clear cache
            Cache::forget('sms_settings');

            return back()->with('success', 'SMS settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating SMS settings: ' . $e->getMessage());
        }
    }

    public function testSms(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'test_number' => 'required|string|max:20',
            'test_message' => 'required|string|max:160',
        ]);

        try {
            $smsService = new SmsService();
            
            // Test the SMS gateway
            $result = $smsService->sendSms($request->test_number, $request->test_message);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test SMS sent successfully!',
                    'details' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Test SMS failed: ' . $result['message'],
                    'details' => $result
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing SMS: ' . $e->getMessage()
            ]);
        }
    }

    public function testConnection()
    {
        $this->checkSuperAdmin();

        try {
            $smsService = new SmsService();
            $result = $smsService->testConnection();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ]);
        }
    }

    public function getSmsSettings()
    {
        try {
            $settings = SystemSetting::where('key', 'like', 'sms_%')->pluck('value', 'key');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $settings['sms_enabled'] ?? '0',
                    'api_token' => $settings['sms_api_token'] ?? '',
                    'sender_id' => $settings['sms_sender_id'] ?? '',
                    'rent_reminder_enabled' => $settings['sms_rent_reminder_enabled'] ?? '0',
                    'maintenance_update_enabled' => $settings['sms_maintenance_update_enabled'] ?? '0',
                    'welcome_message_enabled' => $settings['sms_welcome_message_enabled'] ?? '0',
                    'payment_confirmation_enabled' => $settings['sms_payment_confirmation_enabled'] ?? '0',
                    'checkout_reminder_enabled' => $settings['sms_checkout_reminder_enabled'] ?? '0',
                    'reminder_days_before' => $settings['sms_reminder_days_before'] ?? '3',
                    'working_hours_start' => $settings['sms_working_hours_start'] ?? '09:00',
                    'working_hours_end' => $settings['sms_working_hours_end'] ?? '18:00',
                    'max_retries' => $settings['sms_max_retries'] ?? '3',
                    'retry_delay' => $settings['sms_retry_delay'] ?? '5',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading SMS settings: ' . $e->getMessage()
            ]);
        }
    }

    public function sendBulkSms(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'phone_numbers' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        try {
            $smsService = new SmsService();
            
            // Split phone numbers by comma or space
            $phoneNumbers = preg_split('/[, ]+/', $request->phone_numbers);
            $phoneNumbers = array_filter($phoneNumbers); // Remove empty values
            
            if (empty($phoneNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid phone numbers provided'
                ]);
            }
            
            $result = $smsService->sendBulkSms($phoneNumbers, $request->message);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending bulk SMS: ' . $e->getMessage()
            ]);
        }
    }
} 