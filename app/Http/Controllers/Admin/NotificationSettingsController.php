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





        // Get SMS Group Settings
        $smsGroupSettings = [];
        $smsGroupKeys = [
            'system_welcome_sms', 'system_otp_sms', 'system_password_reset_sms', 'system_security_alert_sms',
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
                'system_welcome_sms', 'system_otp_sms', 'system_password_reset_sms', 'system_security_alert_sms',
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
        // Temporarily disable super admin check for testing
        // $this->checkSuperAdmin();
        
        $templateName = $request->get('template');

        // Get template from database or return default
        $template = SystemSetting::where('key', 'template_' . $templateName)->first();

        if ($template) {
            $data = json_decode($template->value, true);
            return response()->json([
                'success' => true,
                'template' => [
                    'subject' => $data['subject'] ?? '',
                    'content' => $data['content'] ?? '',
                ]
            ]);
        }

        // Return default template
        $defaultTemplates = [
            'payment_confirmation_email' => [
                'subject' => 'Payment Confirmation - HRMS',
                'content' => 'Dear {name},\n\nYour payment of ৳{amount} has been received successfully.\n\nInvoice: {invoice_number}\nPayment Method: {payment_method}\n\nThank you for using HRMS!\n\nBest regards,\nHRMS Team'
            ],
            'invoice_notification_email' => [
                'subject' => 'New Invoice Generated - HRMS',
                'content' => 'Dear {name},\n\nA new invoice has been generated for your account.\n\nInvoice: {invoice_number}\nAmount: ৳{amount}\nDue Date: {due_date}\n\nPlease make the payment before the due date.\n\nBest regards,\nHRMS Team'
            ],
            'subscription_reminder_email' => [
                'subject' => 'Subscription Reminder - HRMS',
                'content' => 'Dear {name},\n\nYour subscription will expire on {expiry_date}.\n\nPlease renew your subscription to continue using HRMS services.\n\nBest regards,\nHRMS Team'
            ],
            'welcome_email' => [
                'subject' => 'Welcome to HRMS!',
                'content' => 'Dear {name},\n\nWelcome to HRMS! Your account has been created successfully.\n\nYou can now access all our services.\n\nBest regards,\nHRMS Team'
            ],
            'password_reset_email' => [
                'subject' => 'Password Reset Request - HRMS',
                'content' => 'Dear {name},\n\nYou have requested to reset your password.\n\nReset Link: {reset_link}\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nHRMS Team'
            ],
            'maintenance_notification_email' => [
                'subject' => 'Maintenance Update - HRMS',
                'content' => 'Dear {name},\n\nMaintenance has been completed for your property.\n\nDetails: {maintenance_details}\n\nThank you for your patience.\n\nBest regards,\nHRMS Team'
            ],
            'rent_reminder_email' => [
                'subject' => 'Rent Reminder - HRMS',
                'content' => 'Dear {name},\n\nThis is a reminder that your rent is due on {due_date}.\n\nAmount: ৳{amount}\n\nPlease make the payment on time.\n\nBest regards,\nHRMS Team'
            ],
            'checkout_reminder_email' => [
                'subject' => 'Checkout Reminder - HRMS',
                'content' => 'Dear {name},\n\nYour checkout date is approaching on {checkout_date}.\n\nPlease ensure all dues are cleared before checkout.\n\nBest regards,\nHRMS Team'
            ],
            // SMS Templates for Owners
            'owner_payment_confirmation_sms' => [
                'content' => 'Dear {name}, your payment of ৳{amount} has been received. Invoice: {invoice_number}. Thank you! - HRMS'
            ],
            'owner_invoice_notification_sms' => [
                'content' => 'Dear {name}, new invoice generated. Amount: ৳{amount}, Due: {due_date}. Invoice: {invoice_number} - HRMS'
            ],
            'owner_subscription_reminder_sms' => [
                'content' => 'Dear {name}, your subscription expires on {expiry_date}. Please renew to continue services. - HRMS'
            ],
            'owner_subscription_activation_sms' => [
                'content' => 'Dear {name}, your subscription has been activated successfully. Welcome to HRMS!'
            ],
            'owner_welcome_sms' => [
                'content' => 'Welcome {name}! Your HRMS account has been created successfully. You can now access all services.'
            ],
            // SMS Templates for Tenants
            'tenant_payment_confirmation_sms' => [
                'content' => 'Dear {tenant_name}, your rent payment of ৳{amount} has been received. Property: {property_name}. Thank you! - HRMS'
            ],
            'tenant_invoice_notification_sms' => [
                'content' => 'Dear {tenant_name}, new rent invoice generated. Amount: ৳{amount}, Due: {due_date}. Property: {property_name} - HRMS'
            ],
            'tenant_subscription_reminder_sms' => [
                'content' => 'Dear {tenant_name}, your rent is due on {due_date}. Amount: ৳{amount}. Property: {property_name} - HRMS'
            ],
            'tenant_subscription_activation_sms' => [
                'content' => 'Dear {tenant_name}, your tenancy has been activated. Property: {property_name}. Welcome to HRMS!'
            ],
            'tenant_welcome_sms' => [
                'content' => 'Welcome {tenant_name}! Your tenancy at {property_name} has been registered. Welcome to HRMS!'
            ]
        ];

        $template = $defaultTemplates[$templateName] ?? [
            'subject' => 'HRMS Notification',
            'content' => 'Dear {name},\n\nThis is a notification from HRMS.\n\nBest regards,\nHRMS Team'
        ];

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    public function saveTemplate(Request $request)
    {
        // Temporarily disable super admin check for testing
        // $this->checkSuperAdmin();
        
        // Handle both GET and POST requests
        if ($request->isMethod('GET')) {
            $templateName = $request->get('template_name');
            $content = $request->get('content');
        } else {
            $request->validate([
                'template_name' => 'required|string',
                'content' => 'required|string',
            ]);
            $templateName = $request->input('template_name');
            $content = $request->input('content');
        }

        try {
            $templateData = [];
            
            // Check if it's an SMS template (contains '_sms' in name)
            if (str_contains($templateName, '_sms')) {
                $templateData = [
                    'content' => $content,
                ];
            } else {
                // Email template requires subject
                if ($request->isMethod('POST')) {
                    $request->validate([
                        'subject' => 'required|string|max:255',
                    ]);
                }
                
                $subject = $request->isMethod('GET') ? $request->get('subject') : $request->input('subject');
                
                $templateData = [
                    'subject' => $subject,
                    'content' => $content,
                ];
            }

            $result = SystemSetting::setValue('template_' . $templateName, json_encode($templateData));

            // Check if it's an AJAX request
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Template saved successfully!']);
            }

            // For form submissions, redirect back with success message
            return redirect()->back()->with('success', 'Template saved successfully!');
        } catch (\Exception $e) {
            Log::error('Template save failed: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to save template: ' . $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', 'Failed to save template: ' . $e->getMessage());
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
