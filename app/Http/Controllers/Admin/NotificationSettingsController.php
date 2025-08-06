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
            if (!auth()->check()) {
                abort(401, 'Authentication required. Please log in again.');
            }

            $user = auth()->user();

            // Check if user has super_admin role
            if ($user && $user->hasRole('super_admin')) {
                return;
            }

            // Check if user is super admin through owner relationship
            if ($user && $user->owner && $user->owner->is_super_admin) {
                return;
            }

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

        // Get notification logs with pagination
        $notificationLogs = NotificationLog::orderBy('created_at', 'desc')
            ->paginate(\App\Helpers\SystemHelper::getPaginationLimit()); // Show dynamic logs per page

        return view('admin.settings.notifications', compact('smsGroupSettings', 'notificationLogs'));
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


    public function getTemplate(Request $request)
    {
        $this->checkSuperAdmin();
        
        $templateName = $request->get('template');
        
        // Get template directly from database
        $template = SystemSetting::where('key', 'template_' . $templateName)->first();
        
        if ($template) {
            // Return the raw template value - let frontend handle JSON parsing
            return response()->json([
                'success' => true,
                'template' => [
                    'content' => $template->value
                ]
            ]);
        }
        
        // Return empty template if not found
        return response()->json([
            'success' => true,
            'template' => [
                'content' => ''
            ]
        ]);
    }

    public function saveTemplate(Request $request)
    {
        $this->checkSuperAdmin();
        
        $templateName = $request->input('template_name');
        $content = $request->input('content');
        
        if (empty($templateName) || empty($content)) {
            return response()->json([
                'success' => false, 
                'message' => 'Template name and content are required'
            ]);
        }
        
        try {
            // Save template directly as string (no JSON encoding)
            SystemSetting::updateOrCreate(
                ['key' => 'template_' . $templateName],
                ['value' => $content]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save template: ' . $e->getMessage()
            ]);
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
}
