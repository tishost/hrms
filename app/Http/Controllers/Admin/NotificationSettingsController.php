<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Models\NotificationLog;
use App\Services\NotificationService;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        $notificationService = new NotificationService();

        // Get email settings
        $emailSettings = [
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_password' => config('mail.mailers.smtp.password'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'email_enabled' => Setting::getValue('email_enabled', true),
        ];

        // Get SMS settings
        $smsSettings = [
            'sms_provider' => Setting::getValue('sms_provider', 'bulksms'),
            'sms_api_key' => Setting::getValue('sms_api_key', ''),
            'sms_api_secret' => Setting::getValue('sms_api_secret', ''),
            'sms_sender_id' => Setting::getValue('sms_sender_id', 'HRMS'),
            'sms_enabled' => Setting::getValue('sms_enabled', true),
            'sms_monthly_limit' => Setting::getValue('sms_monthly_limit', 1000),
            'sms_monthly_count' => Setting::getValue('sms_monthly_count', 0),
        ];

        // Get SMS statistics
        $smsStats = $notificationService->getSmsStats();

        // Get notification logs (last 50)
        $notificationLogs = NotificationLog::orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.settings.notifications', compact('emailSettings', 'smsSettings', 'smsStats', 'notificationLogs'));
    }

    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
            'email_enabled' => 'boolean',
        ]);

        try {
            // Update .env file or database settings
            $this->updateEnvironmentFile([
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            ]);

            // Update email enabled setting
            Setting::setValue('email_enabled', $request->has('email_enabled'));

            return redirect()->back()->with('success', 'Email settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Email settings update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    public function updateSmsSettings(Request $request)
    {
        $request->validate([
            'sms_provider' => 'required|string',
            'sms_api_key' => 'required|string',
            'sms_api_secret' => 'required|string',
            'sms_sender_id' => 'required|string|max:11',
            'sms_enabled' => 'boolean',
            'sms_monthly_limit' => 'required|integer|min:1',
        ]);

        try {
            // Save SMS settings to database
            Setting::setValue('sms_provider', $request->sms_provider);
            Setting::setValue('sms_api_key', $request->sms_api_key);
            Setting::setValue('sms_api_secret', $request->sms_api_secret);
            Setting::setValue('sms_sender_id', $request->sms_sender_id);
            Setting::setValue('sms_enabled', $request->has('sms_enabled'));
            Setting::setValue('sms_monthly_limit', $request->sms_monthly_limit);

            return redirect()->back()->with('success', 'SMS settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('SMS settings update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update SMS settings: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        try {
            $notificationService = new NotificationService();
            $testEmail = $request->input('email', config('mail.from.address'));

            $result = $notificationService->testEmail($testEmail);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    public function testSms(Request $request)
    {
        try {
            $notificationService = new NotificationService();
            $testPhone = $request->input('phone', '01700000000');

            $result = $notificationService->testSms($testPhone);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Test SMS failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send test SMS: ' . $e->getMessage()]);
        }
    }

    public function resetSmsCount(Request $request)
    {
        try {
            $notificationService = new NotificationService();
            $notificationService->resetSmsCount();

            return response()->json(['success' => true, 'message' => 'SMS count reset successfully!']);
        } catch (\Exception $e) {
            Log::error('SMS count reset failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to reset SMS count: ' . $e->getMessage()]);
        }
    }

    public function getTemplate(Request $request)
    {
        $templateName = $request->get('template');

        // Get template from database or return default
        $template = Setting::where('key', 'template_' . $templateName)->first();

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
                'content' => 'Dear {name},\n\nYour subscription will expire soon.\n\nCurrent Plan: {plan_name}\nExpiry Date: {expiry_date}\n\nPlease renew your subscription to continue using our services.\n\nBest regards,\nHRMS Team'
            ],
            'subscription_activation_email' => [
                'subject' => 'Subscription Activated - HRMS',
                'content' => 'Dear {name},\n\nYour subscription has been activated successfully!\n\nPlan: {plan_name}\nExpiry Date: {expiry_date}\n\nYou can now access all features of your subscription.\n\nBest regards,\nHRMS Team'
            ],
            'welcome_email' => [
                'subject' => 'Welcome to HRMS!',
                'content' => 'Dear {name},\n\nWelcome to HRMS! Your account has been created successfully.\n\nEmail: {email}\n\nWe hope you enjoy using our property management system.\n\nBest regards,\nHRMS Team'
            ],
            'payment_confirmation_sms' => [
                'subject' => '',
                'content' => 'Payment of ৳{amount} received. Invoice: {invoice_number}. Thank you! - HRMS'
            ],
            'due_date_reminder_sms' => [
                'subject' => '',
                'content' => 'Reminder: Invoice {invoice_number} due on {due_date}. Amount: ৳{amount} - HRMS'
            ],
            'otp_verification_sms' => [
                'subject' => '',
                'content' => 'Your OTP is {otp}. Valid for 5 minutes. - HRMS'
            ],
            'subscription_activation_sms' => [
                'subject' => '',
                'content' => 'Your {plan_name} subscription is now active! Expires: {expiry_date}. - HRMS'
            ],
            'welcome_sms' => [
                'subject' => '',
                'content' => 'Welcome to HRMS! Your account has been created successfully. - HRMS'
            ],
        ];

        $template = $defaultTemplates[$templateName] ?? ['subject' => '', 'content' => ''];

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    public function saveTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'subject' => 'nullable|string',
            'content' => 'required|string',
        ]);

        try {
            $templateData = [
                'subject' => $request->subject,
                'content' => $request->content,
            ];

            Setting::updateOrCreate(
                ['key' => 'template_' . $request->template],
                ['value' => json_encode($templateData)]
            );

            return response()->json(['success' => true, 'message' => 'Template saved successfully!']);
        } catch (\Exception $e) {
            Log::error('Template save failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save template: ' . $e->getMessage()]);
        }
    }

    public function viewLog(Request $request)
    {
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
