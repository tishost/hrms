<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OwnerSetting;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $owner = Auth::user()->owner;
        
        // Get templates with admin defaults
        $templates = OwnerSetting::getTemplatesForOwner($owner->id);
        
        // Get other settings
        $settings = OwnerSetting::getSettings($owner->id);
        
        // Merge templates with other settings
        $allSettings = array_merge($settings, $templates);
        
        // Initialize default non-template settings if not exists
        if (empty($settings)) {
            OwnerSetting::initializeDefaults($owner->id);
            $settings = OwnerSetting::getSettings($owner->id);
            $allSettings = array_merge($settings, $templates);
        }

        return view('owner.settings.index', compact('allSettings'));
    }

    public function update(Request $request)
    {
        $owner = Auth::user()->owner;

        $request->validate([
            // SMS Templates - Bangla
            'tenant_welcome_sms_template_bangla' => 'nullable|string',
            'rent_due_sms_template_bangla' => 'nullable|string',
            'rent_paid_sms_template_bangla' => 'nullable|string',
            'checkout_sms_template_bangla' => 'nullable|string',
            
            // SMS Templates - English
            'tenant_welcome_sms_template_english' => 'nullable|string',
            'rent_due_sms_template_english' => 'nullable|string',
            'rent_paid_sms_template_english' => 'nullable|string',
            'checkout_sms_template_english' => 'nullable|string',
            
            // Email Templates - Bangla
            'tenant_welcome_email_template_bangla' => 'nullable|string',
            'rent_due_email_template_bangla' => 'nullable|string',
            'rent_paid_email_template_bangla' => 'nullable|string',
            'checkout_email_template_bangla' => 'nullable|string',
            'lease_expiry_email_template_bangla' => 'nullable|string',
            
            // Email Templates - English
            'tenant_welcome_email_template_english' => 'nullable|string',
            'rent_due_email_template_english' => 'nullable|string',
            'rent_paid_email_template_english' => 'nullable|string',
            'checkout_email_template_english' => 'nullable|string',
            'lease_expiry_email_template_english' => 'nullable|string',
            
            // Settings
            'notification_language' => 'required|in:bangla,english',
            'notify_rent_due' => 'boolean',
            'notify_rent_paid' => 'boolean',
            'notify_new_tenant' => 'boolean',
            'notify_checkout' => 'boolean',
            'notify_late_payment' => 'boolean',
            'notify_maintenance' => 'boolean',
            'notify_lease_expiry' => 'boolean',
            'auto_send_reminders' => 'boolean',
            'rent_due_reminder_days' => 'integer|min:1|max:30',
            'late_payment_reminder_days' => 'integer|min:1|max:30',
            'lease_expiry_reminder_days' => 'integer|min:1|max:90',
        ]);

        try {
            // Update settings
            foreach ($request->all() as $key => $value) {
                if ($key !== '_token' && $key !== '_method') {
                    OwnerSetting::setValue($owner->id, $key, $value);
                }
            }

            return redirect()->back()->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:160',
        ]);

        $owner = Auth::user()->owner;

        try {
            // Use the existing SMS service
            $smsService = app(\App\Services\SmsService::class);
            $result = $smsService->sendSms($request->phone, $request->message);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test SMS sent successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS. Please check system settings.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ]);
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $owner = Auth::user()->owner;

        try {
            // Use the existing notification service
            $notificationService = app(\App\Services\NotificationService::class);
            $result = $notificationService->sendEmail($request->email, $request->subject, $request->message);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test email sent successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email. Please check system settings.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ]);
        }
    }

    public function resetTemplates()
    {
        $owner = Auth::user()->owner;
        $defaults = OwnerSetting::getDefaultSettings();

        // Reset only template settings
        $templateKeys = [
            'tenant_welcome_email_template',
            'rent_due_email_template',
            'rent_paid_email_template',
            'checkout_email_template',
            'tenant_welcome_sms_template',
            'rent_due_sms_template',
            'rent_paid_sms_template',
            'checkout_sms_template',
            'otp_sms_template',
        ];

        foreach ($templateKeys as $key) {
            if (isset($defaults[$key])) {
                OwnerSetting::setValue($owner->id, $key, $defaults[$key]);
            }
        }

        return redirect()->route('owner.settings.index')
            ->with('success', 'Templates reset to default successfully!');
    }
}
