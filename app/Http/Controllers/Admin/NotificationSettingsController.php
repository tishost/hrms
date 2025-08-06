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

        // Get notification logs with pagination
        $notificationLogs = NotificationLog::orderBy('created_at', 'desc')
            ->paginate(\App\Helpers\SystemHelper::getPaginationLimit()); // Show dynamic logs per page

        return view('admin.settings.notifications', compact('smsGroupSettings', 'notificationLogs'));
    }

    public function emailTemplates()
    {
        $this->checkSuperAdmin();
        return view('admin.settings.email-templates');
    }

    public function smsTemplates()
    {
        $this->checkSuperAdmin();
        return view('admin.settings.sms-templates');
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
        // Log request details for debugging
        \Log::info('Template get request', [
            'template' => $request->get('template'),
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);
        
        // Check authentication first
        if (!auth()->check()) {
            \Log::warning('User not authenticated for template get');
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please log in again.',
                'code' => 'AUTH_REQUIRED'
            ], 401);
        }
        
        // Check super admin privileges
        try {
            $this->checkSuperAdmin();
        } catch (\Exception $e) {
            \Log::error('Super admin check failed for template get: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Super admin privileges required.',
                'code' => 'ACCESS_DENIED'
            ], 403);
        }
        
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
        // Log request details for debugging
        \Log::info('Template save request', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'input' => $request->all(),
            'csrf_token' => $request->header('X-CSRF-TOKEN'),
            'form_token' => $request->input('_token'),
            'session_id' => session()->getId(),
            'session_status' => session()->isStarted() ? 'Started' : 'Not Started',
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_token' => session()->token(),
            'csrf_token_valid' => $request->header('X-CSRF-TOKEN') === session()->token()
        ]);
        
        // Check authentication first
        if (!auth()->check()) {
            \Log::warning('User not authenticated for template save');
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please log in again.',
                'code' => 'AUTH_REQUIRED'
            ], 401);
        }

        // Use Laravel's built-in CSRF validation
        try {
            $request->validate([
                '_token' => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('CSRF validation failed', [
                'errors' => $e->errors(),
                'csrf_token' => $request->header('X-CSRF-TOKEN') ? substr($request->header('X-CSRF-TOKEN'), 0, 20) . '...' : 'null',
                'form_token' => $request->input('_token') ? substr($request->input('_token'), 0, 20) . '...' : 'null'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'CSRF token validation failed. Please refresh the page and try again.',
                'code' => 'CSRF_VALIDATION_FAILED'
            ], 419);
        }
        
        // Validate template data
        try {
            $request->validate([
                'template_name' => 'required|string|max:255',
                'content' => 'required|string|max:10000'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Template validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Template validation failed: ' . implode(', ', array_flatten($e->errors())),
                'code' => 'VALIDATION_FAILED'
            ], 422);
        }
        
        // Check super admin privileges
        try {
            $this->checkSuperAdmin();
        } catch (\Exception $e) {
            \Log::error('Super admin check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Super admin privileges required.',
                'code' => 'ACCESS_DENIED'
            ], 403);
        }
        
        // Handle both GET and POST requests
        if ($request->isMethod('GET')) {
            $templateName = $request->get('template_name');
            $content = $request->get('content');
        } else {
            // Handle JSON input
            if ($request->isJson()) {
                $data = $request->json()->all();
                $templateName = $data['template_name'] ?? null;
                $content = $data['content'] ?? null;
            } else {
                $templateName = $request->input('template_name');
                $content = $request->input('content');
            }
        }
        
        if (empty($templateName) || empty($content)) {
            return response()->json([
                'success' => false, 
                'message' => 'Template name and content are required'
            ]);
        }
        
        try {
            // Save template directly as string (no JSON encoding)
            $result = SystemSetting::updateOrCreate(
                ['key' => 'template_' . $templateName],
                ['value' => $content]
            );
            
            \Log::info('Template saved successfully', [
                'template_name' => $templateName,
                'content_length' => strlen($content),
                'saved_id' => $result->id,
                'saved_value' => $result->value
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully!',
                'template_name' => $templateName,
                'content_length' => strlen($content)
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error while saving template', [
                'error' => $e->getMessage(),
                'template_name' => $templateName,
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Database error: Unable to save template. Please try again.',
                'code' => 'DATABASE_ERROR'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Template save failed', [
                'error' => $e->getMessage(),
                'template_name' => $templateName,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save template: ' . $e->getMessage(),
                'code' => 'SAVE_ERROR'
            ], 500);
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
