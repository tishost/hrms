<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatSettingsController extends Controller
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
            
            // Default chat settings
            $chatSettings = [
                'chat_enabled' => $settings['chat_enabled'] ?? '1',
                'chat_welcome_message' => $settings['chat_welcome_message'] ?? 'Hello! 👋 Welcome to Bari Manager. How can I help you today?',
                'chat_bot_name' => $settings['chat_bot_name'] ?? 'Bari Manager Support',
                'chat_agent_transfer_enabled' => $settings['chat_agent_transfer_enabled'] ?? '1',
                'chat_auto_response_enabled' => $settings['chat_auto_response_enabled'] ?? '1',
                'chat_working_hours' => $settings['chat_working_hours'] ?? '9 AM - 6 PM (GMT+6)',
                'chat_offline_message' => $settings['chat_offline_message'] ?? 'We are currently offline. Please leave a message and we\'ll get back to you soon.',
                'chat_position' => $settings['chat_position'] ?? 'bottom-right',
                'chat_theme_color' => $settings['chat_theme_color'] ?? 'purple',
                'chat_notification_sound' => $settings['chat_notification_sound'] ?? '1',
                'chat_typing_indicator' => $settings['chat_typing_indicator'] ?? '1',
                'chat_file_upload_enabled' => $settings['chat_file_upload_enabled'] ?? '0',
                'chat_max_file_size' => $settings['chat_max_file_size'] ?? '5',
                'chat_allowed_file_types' => $settings['chat_allowed_file_types'] ?? 'jpg,jpeg,png,pdf,doc,docx',
            ];

            return view('admin.settings.chat', compact('chatSettings'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading chat settings: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'chat_enabled' => 'boolean',
            'chat_welcome_message' => 'required|string|max:500',
            'chat_bot_name' => 'required|string|max:100',
            'chat_agent_transfer_enabled' => 'boolean',
            'chat_auto_response_enabled' => 'boolean',
            'chat_working_hours' => 'required|string|max:100',
            'chat_offline_message' => 'required|string|max:500',
            'chat_position' => 'required|in:bottom-right,bottom-left,top-right,top-left',
            'chat_theme_color' => 'required|in:purple,blue,green,red,orange,pink',
            'chat_notification_sound' => 'boolean',
            'chat_typing_indicator' => 'boolean',
            'chat_file_upload_enabled' => 'boolean',
            'chat_max_file_size' => 'required|integer|min:1|max:50',
            'chat_allowed_file_types' => 'required|string|max:200',
        ]);

        try {
            // Define all checkbox fields
            $checkboxFields = ['chat_enabled', 'chat_agent_transfer_enabled', 'chat_auto_response_enabled', 'chat_notification_sound', 'chat_typing_indicator', 'chat_file_upload_enabled'];

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
            Cache::forget('chat_settings');

            return back()->with('success', 'Chat settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating chat settings: ' . $e->getMessage());
        }
    }

    public function testChat()
    {
        $this->checkSuperAdmin();

        return response()->json([
            'success' => true,
            'message' => 'Chat system is working properly',
            'settings' => SystemSetting::where('key', 'like', 'chat_%')->pluck('value', 'key')
        ]);
    }

    public function getChatSettings()
    {
        try {
            $settings = SystemSetting::where('key', 'like', 'chat_%')->pluck('value', 'key');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $settings['chat_enabled'] ?? '1',
                    'welcome_message' => $settings['chat_welcome_message'] ?? 'Hello! 👋 Welcome to Bari Manager. How can I help you today?',
                    'bot_name' => $settings['chat_bot_name'] ?? 'Bari Manager Support',
                    'agent_transfer_enabled' => $settings['chat_agent_transfer_enabled'] ?? '1',
                    'auto_response_enabled' => $settings['chat_auto_response_enabled'] ?? '1',
                    'working_hours' => $settings['chat_working_hours'] ?? '9 AM - 6 PM (GMT+6)',
                    'offline_message' => $settings['chat_offline_message'] ?? 'We are currently offline. Please leave a message and we\'ll get back to you soon.',
                    'position' => $settings['chat_position'] ?? 'bottom-right',
                    'theme_color' => $settings['chat_theme_color'] ?? 'purple',
                    'notification_sound' => $settings['chat_notification_sound'] ?? '1',
                    'typing_indicator' => $settings['chat_typing_indicator'] ?? '1',
                    'file_upload_enabled' => $settings['chat_file_upload_enabled'] ?? '0',
                    'max_file_size' => $settings['chat_max_file_size'] ?? '5',
                    'allowed_file_types' => $settings['chat_allowed_file_types'] ?? 'jpg,jpeg,png,pdf,doc,docx',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading chat settings: ' . $e->getMessage()
            ]);
        }
    }
}
