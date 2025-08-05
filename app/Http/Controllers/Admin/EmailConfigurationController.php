<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\SystemSetting;
use App\Services\NotificationService;

class EmailConfigurationController extends Controller
{
    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required. Please log in again.');
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
        
        $notificationService = new NotificationService();

        // Get email settings from database first, fallback to config
        $dbSettings = \App\Models\SystemSetting::where('key', 'like', 'mail_%')->pluck('value', 'key');
        $emailEnabledValue = \App\Models\SystemSetting::where('key', 'email_enabled')->value('value');
        
        // If no email_enabled setting exists, create default (enabled)
        if ($emailEnabledValue === null) {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'email_enabled'],
                ['value' => '1']
            );
            $emailEnabledValue = '1';
        }
        
        $emailSettings = [
            'mail_host' => $dbSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
            'mail_port' => $dbSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
            'mail_username' => $dbSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
            'mail_password' => $dbSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
            'mail_encryption' => $dbSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
            'mail_from_address' => $dbSettings['mail_from_address'] ?? config('mail.from.address'),
            'email_enabled' => $emailEnabledValue === '1',
        ];

        // Log email settings for debugging
        \Log::info('Email settings loaded', [
            'email_enabled_value' => $emailEnabledValue,
            'email_enabled_boolean' => $emailSettings['email_enabled'],
            'db_settings' => $dbSettings->toArray(),
            'final_settings' => $emailSettings
        ]);

        return view('admin.settings.email-configuration', compact('emailSettings'));
    }

    public function updateEmailSettings(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
        ]);

        try {
            // Update .env file
            $this->updateEnvironmentFile([
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            ]);

            // Update database settings
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'mail_host'],
                ['value' => $request->mail_host]
            );
            
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'mail_port'],
                ['value' => $request->mail_port]
            );
            
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'mail_username'],
                ['value' => $request->mail_username]
            );
            
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'mail_password'],
                ['value' => $request->mail_password]
            );
            
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'mail_encryption'],
                ['value' => $request->mail_encryption]
            );
            
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'mail_from_address'],
                ['value' => $request->mail_from_address]
            );

            // Handle email enabled checkbox
            $emailEnabled = '0';
            if ($request->has('email_enabled') && $request->filled('email_enabled')) {
                $emailEnabled = '1';
            }
            
            // Update email enabled setting in database
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'email_enabled'],
                ['value' => $emailEnabled]
            );

            // Log the email settings update
            \Log::info('Email settings updated', [
                'email_enabled' => $emailEnabled,
                'checkbox_checked' => $request->has('email_enabled'),
                'checkbox_filled' => $request->filled('email_enabled'),
                'checkbox_value' => $request->input('email_enabled'),
                'request_data' => $request->all(),
                'database_values' => \App\Models\SystemSetting::where('key', 'like', 'mail_%')->pluck('value', 'key'),
                'email_enabled_db' => \App\Models\SystemSetting::where('key', 'email_enabled')->value('value')
            ]);

            return redirect()->back()->with('success', 'Email settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Email settings update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        $this->checkSuperAdmin();
        
        try {
            $notificationService = new NotificationService();
            $testEmail = $request->input('email', config('mail.from.address'));

            // Log email configuration
            \Log::info('Email configuration for test', [
                'test_email' => $testEmail,
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_address' => config('mail.from.address')
            ]);

            $result = $notificationService->testEmail($testEmail);

            // Log the result
            \Log::info('Test email result', $result);

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Test email failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    public function debugEmailSettings()
    {
        $this->checkSuperAdmin();
        
        $emailEnabledValue = \App\Models\SystemSetting::where('key', 'email_enabled')->value('value');
        
        // If no setting exists, create default (enabled)
        if ($emailEnabledValue === null) {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => 'email_enabled'],
                ['value' => '1']
            );
            $emailEnabledValue = '1';
        }
        
        $allSettings = \App\Models\SystemSetting::where('key', 'like', 'email_%')->get();
        $mailSettings = \App\Models\SystemSetting::where('key', 'like', 'mail_%')->get();
        
        return response()->json([
            'success' => true,
            'email_enabled_value' => $emailEnabledValue,
            'email_enabled_boolean' => $emailEnabledValue === '1',
            'all_email_settings' => $allSettings->pluck('value', 'key'),
            'mail_settings' => $mailSettings->pluck('value', 'key'),
            'request_data' => request()->all(),
            'setting_exists' => $emailEnabledValue !== null,
            'default_created' => $emailEnabledValue === '1' && $emailEnabledValue !== null,
            'database_check' => [
                'raw_value' => $emailEnabledValue,
                'is_string' => is_string($emailEnabledValue),
                'is_null' => is_null($emailEnabledValue),
                'equals_1' => $emailEnabledValue === '1',
                'equals_1_bool' => $emailEnabledValue === '1' ? true : false
            ]
        ]);
    }

    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);

            foreach ($data as $key => $value) {
                // Escape any quotes in the value
                $value = str_replace('"', '\\"', $value);
                
                // Check if the key exists in the .env file
                if (strpos($content, $key . '=') !== false) {
                    // Update existing key
                    $content = preg_replace(
                        '/^' . $key . '=.*/m',
                        $key . '="' . $value . '"',
                        $content
                    );
                } else {
                    // Add new key at the end
                    $content .= "\n" . $key . '="' . $value . '"';
                }
            }

            file_put_contents($path, $content);
        }
    }
} 