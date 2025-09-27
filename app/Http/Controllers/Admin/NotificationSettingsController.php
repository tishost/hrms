<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\SystemSetting;
use App\Models\NotificationLog;
use App\Services\NotificationService;

class NotificationSettingsController extends Controller
{
    private function checkSuperAdmin()
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                \Log::warning('Authentication failed - no user logged in');
                abort(401, 'Authentication required. Please log in again.');
            }

            $user = auth()->user();
            
            // Log user details for debugging
            \Log::info('User authentication check', [
                'user_id' => $user->id ?? 'null',
                'user_email' => $user->email ?? 'null',
                'has_roles' => $user->roles ? $user->roles->pluck('name') : 'no roles',
                'has_owner' => $user->owner ? 'yes' : 'no',
                'owner_is_super_admin' => $user->owner && $user->owner->is_super_admin ? 'yes' : 'no'
            ]);

            // Check if user has super_admin role
            if ($user && $user->hasRole('super_admin')) {
                \Log::info('User has super_admin role');
                return;
            }

            // Check if user is super admin through owner relationship
            if ($user && $user->owner && $user->owner->is_super_admin) {
                \Log::info('User is super admin through owner relationship');
                return;
            }

            \Log::warning('Access denied - user does not have super admin privileges', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            
            abort(403, 'Access denied. Super admin privileges required.');
        } catch (\Exception $e) {
            \Log::error('checkSuperAdmin error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Internal server error. Please try again.');
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();
        
        $notificationService = new NotificationService();

        // Get SMS Group Settings
        $smsGroupSettings = [];
        $smsGroupKeys = [
            'system_welcome_sms', 'system_otp_sms', 'system_password_reset_sms', 'system_password_reset_otp_sms', 'system_security_alert_sms',
            'owner_welcome_sms', 'owner_package_purchase_sms', 'owner_payment_confirmation_sms', 
            'owner_invoice_reminder_sms', 'owner_subscription_expiry_sms', 'owner_subscription_renewal_sms',
            'tenant_welcome_sms', 'tenant_rent_reminder_sms', 'tenant_payment_confirmation_sms',
            'tenant_maintenance_update_sms', 'tenant_checkout_reminder_sms', 'tenant_lease_expiry_sms'
        ];

        foreach ($smsGroupKeys as $key) {
            $value = SystemSetting::where('key', $key)->value('value');
            $smsGroupSettings[$key] = $value === null ? true : ($value === '1');
        }

        // Get Language Settings
        $languageSettings = [];
        $languageKeys = [
            'notification_language',
            'user_language_preference'
        ];

        foreach ($languageKeys as $key) {
            $value = SystemSetting::where('key', $key)->value('value');
            $languageSettings[$key] = $value ?: ($key === 'notification_language' ? 'bangla' : 'enabled');
        }

        // Get notification logs with pagination
        $notificationLogs = NotificationLog::orderBy('created_at', 'desc')
            ->paginate(\App\Helpers\SystemHelper::getPaginationLimit()); // Show dynamic logs per page

        return view('admin.settings.notifications', compact('smsGroupSettings', 'languageSettings', 'notificationLogs'));
    }




    /**
     * Get all settings for template pages
     */
    private function getAllSettings()
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();
        return $settings;
    }






    public function updateSmsGroupSettings(Request $request)
    {
        $this->checkSuperAdmin();
        
        try {
            // Define all SMS group settings
            $smsGroupKeys = [
                'system_welcome_sms', 'system_otp_sms', 'system_password_reset_sms', 'system_password_reset_otp_sms', 'system_security_alert_sms',
                'owner_welcome_sms', 'owner_package_purchase_sms', 'owner_payment_confirmation_sms', 
                'owner_invoice_reminder_sms', 'owner_subscription_expiry_sms', 'owner_subscription_renewal_sms',
                'tenant_welcome_sms', 'tenant_rent_reminder_sms', 'tenant_payment_confirmation_sms',
                'tenant_maintenance_update_sms', 'tenant_checkout_reminder_sms', 'tenant_lease_expiry_sms'
            ];

            // Process all SMS group settings
            foreach ($smsGroupKeys as $key) {
                $value = $request->has($key) && $request->filled($key) ? '1' : '0';
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // Log the SMS group settings update
            \Log::info('SMS group settings updated', [
                'request_data' => $request->all(),
                'updated_settings' => SystemSetting::whereIn('key', $smsGroupKeys)->pluck('value', 'key')
            ]);

            return redirect()->back()->with('success', 'SMS group settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('SMS group settings update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update SMS group settings: ' . $e->getMessage());
        }
    }

    public function updateLanguageSettings(Request $request)
    {
        $this->checkSuperAdmin();
        
        try {
            // Validate the request
            $request->validate([
                'notification_language' => 'required|in:bangla,english,both',
                'user_language_preference' => 'required|in:enabled,disabled'
            ]);

            // Update notification language setting
            SystemSetting::updateOrCreate(
                ['key' => 'notification_language'],
                ['value' => $request->notification_language]
            );

            // Update user language preference setting
            SystemSetting::updateOrCreate(
                ['key' => 'user_language_preference'],
                ['value' => $request->user_language_preference]
            );

            // Log the language settings update
            \Log::info('Language settings updated', [
                'notification_language' => $request->notification_language,
                'user_language_preference' => $request->user_language_preference
            ]);

            return redirect()->back()->with('success', 'Language settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Language settings update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update language settings: ' . $e->getMessage());
        }
    }




    public function viewLog(Request $request)
    {
        $this->checkSuperAdmin();
        
        $logs = NotificationLog::orderBy('created_at', 'desc')
            ->paginate(\App\Helpers\SystemHelper::getPaginationLimit());

        return view('admin.settings.notification-log', compact('logs'));
    }

    public function getLogDetails(Request $request)
    {
        $this->checkSuperAdmin();
        
        $logId = $request->get('id');

        $log = NotificationLog::find($logId);

        if ($log) {
            return response()->json([
                'success' => true,
                'log' => [
                    'created_at' => $log->created_at->format('M d, Y H:i:s'),
                    'type' => $log->type,
                    'recipient' => $log->recipient,
                    'content' => $log->content,
                    'status' => $log->status,
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Log not found']);
    }

    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);

            foreach ($data as $key => $value) {
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
            }

            file_put_contents($path, $content);
        }
    }

    public function notificationLogs(Request $request)
    {
        $this->checkSuperAdmin();
        
        // Get notification logs with pagination and filtering
        $query = NotificationLog::orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->filled('recipient')) {
            $query->where('recipient', 'like', '%' . $request->recipient . '%');
        }
        
        $notificationLogs = $query->paginate(\App\Helpers\SystemHelper::getPaginationLimit());
        
        // Get statistics
        $stats = [
            'total' => NotificationLog::count(),
            'sent' => NotificationLog::where('status', 'sent')->count(),
            'failed' => NotificationLog::where('status', 'failed')->count(),
            'pending' => NotificationLog::where('status', 'pending')->count(),
        ];
        
        return view('admin.settings.notification-logs', compact('notificationLogs', 'stats'));
    }
}
