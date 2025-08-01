<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    private $smsProvider;
    private $smsApiKey;
    private $smsApiSecret;
    private $smsSenderId;
    private $emailSettings;
    private $smsLimit;
    private $smsCount;
    private $isSmsEnabled;
    private $isEmailEnabled;

    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Load all notification settings from database
     */
    private function loadSettings()
    {
        // Load SMS settings
        $this->smsProvider = Setting::getValue('sms_provider', 'bulksms');
        $this->smsApiKey = Setting::getValue('sms_api_key', '');
        $this->smsApiSecret = Setting::getValue('sms_api_secret', '');
        $this->smsSenderId = Setting::getValue('sms_sender_id', 'HRMS');
        $this->isSmsEnabled = Setting::getValue('sms_enabled', true);
        $this->smsLimit = Setting::getValue('sms_monthly_limit', 1000);
        $this->smsCount = Setting::getValue('sms_monthly_count', 0);

        // Load Email settings
        $this->isEmailEnabled = Setting::getValue('email_enabled', true);
        $this->emailSettings = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
        ];
    }

    /**
     * Send SMS notification
     */
    public function sendSms($to, $message, $template = null, $variables = [])
    {
        // Check if SMS is enabled
        if (!$this->isSmsEnabled) {
            $this->logNotification('sms', $to, $message, 'disabled', 'SMS notifications are disabled');
            return ['success' => false, 'message' => 'SMS notifications are disabled'];
        }

        // Check SMS limit
        if ($this->smsCount >= $this->smsLimit) {
            $this->logNotification('sms', $to, $message, 'failed', 'SMS monthly limit exceeded');
            return ['success' => false, 'message' => 'SMS monthly limit exceeded'];
        }

        // Replace variables in message
        $message = $this->replaceVariables($message, $variables);

        try {
            $result = $this->sendSmsViaProvider($to, $message);

            if ($result['success']) {
                // Increment SMS count
                $this->incrementSmsCount();
                $this->logNotification('sms', $to, $message, 'sent');
            } else {
                $this->logNotification('sms', $to, $message, 'failed', $result['message']);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logNotification('sms', $to, $message, 'failed', $e->getMessage());
            return ['success' => false, 'message' => 'SMS sending failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send Email notification
     */
    public function sendEmail($to, $subject, $content, $template = null, $variables = [])
    {
        // Check if Email is enabled
        if (!$this->isEmailEnabled) {
            $this->logNotification('email', $to, $content, 'disabled', 'Email notifications are disabled');
            return ['success' => false, 'message' => 'Email notifications are disabled'];
        }

        // Replace variables in subject and content
        $subject = $this->replaceVariables($subject, $variables);
        $content = $this->replaceVariables($content, $variables);

        try {
            Mail::raw($content, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });

            $this->logNotification('email', $to, $content, 'sent');
            return ['success' => true, 'message' => 'Email sent successfully'];
        } catch (\Exception $e) {
            $this->logNotification('email', $to, $content, 'failed', $e->getMessage());
            return ['success' => false, 'message' => 'Email sending failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send notification using template
     */
    public function sendTemplateNotification($type, $to, $templateName, $variables = [])
    {
        $template = Setting::where('key', 'template_' . $templateName)->first();

        if (!$template) {
            return ['success' => false, 'message' => 'Template not found'];
        }

        $templateData = json_decode($template->value, true);
        $subject = $templateData['subject'] ?? '';
        $content = $templateData['content'] ?? '';

        if ($type === 'email') {
            return $this->sendEmail($to, $subject, $content, $templateName, $variables);
        } elseif ($type === 'sms') {
            return $this->sendSms($to, $content, $templateName, $variables);
        }

        return ['success' => false, 'message' => 'Invalid notification type'];
    }

    /**
     * Send multiple notifications
     */
    public function sendMultiple($notifications)
    {
        $results = [];

        foreach ($notifications as $notification) {
            $type = $notification['type'] ?? 'email';
            $to = $notification['to'];
            $subject = $notification['subject'] ?? '';
            $content = $notification['content'] ?? '';
            $template = $notification['template'] ?? null;
            $variables = $notification['variables'] ?? [];

            if ($type === 'email') {
                $results[] = $this->sendEmail($to, $subject, $content, $template, $variables);
            } elseif ($type === 'sms') {
                $results[] = $this->sendSms($to, $content, $template, $variables);
            }
        }

        return $results;
    }

    /**
     * Send SMS via different providers
     */
    private function sendSmsViaProvider($to, $message)
    {
        switch ($this->smsProvider) {
            case 'bulksms':
                return $this->sendViaBulkSms($to, $message);
            case 'twilio':
                return $this->sendViaTwilio($to, $message);
            case 'nexmo':
                return $this->sendViaNexmo($to, $message);
            default:
                return $this->sendViaBulkSms($to, $message);
        }
    }

    /**
     * Send SMS via Bulk SMS BD
     */
    private function sendViaBulkSms($to, $message)
    {
        try {
            $response = Http::post('https://bulksms.com.bd/api/v1/send', [
                'api_key' => $this->smsApiKey,
                'api_secret' => $this->smsApiSecret,
                'to' => $to,
                'message' => $message,
                'sender_id' => $this->smsSenderId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return ['success' => true, 'message' => 'SMS sent successfully'];
                } else {
                    return ['success' => false, 'message' => $data['message'] ?? 'SMS sending failed'];
                }
            } else {
                return ['success' => false, 'message' => 'SMS API request failed'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'SMS sending error: ' . $e->getMessage()];
        }
    }

    /**
     * Send SMS via Twilio
     */
    private function sendViaTwilio($to, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->smsApiKey . ':' . $this->smsApiSecret),
            ])->post('https://api.twilio.com/2010-04-01/Accounts/' . $this->smsApiKey . '/Messages.json', [
                'To' => $to,
                'From' => $this->smsSenderId,
                'Body' => $message,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'SMS sent successfully'];
            } else {
                return ['success' => false, 'message' => 'SMS sending failed'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'SMS sending error: ' . $e->getMessage()];
        }
    }

    /**
     * Send SMS via Nexmo
     */
    private function sendViaNexmo($to, $message)
    {
        try {
            $response = Http::post('https://rest.nexmo.com/sms/json', [
                'api_key' => $this->smsApiKey,
                'api_secret' => $this->smsApiSecret,
                'to' => $to,
                'from' => $this->smsSenderId,
                'text' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['messages'][0]['status'] === '0') {
                    return ['success' => true, 'message' => 'SMS sent successfully'];
                } else {
                    return ['success' => false, 'message' => 'SMS sending failed'];
                }
            } else {
                return ['success' => false, 'message' => 'SMS API request failed'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'SMS sending error: ' . $e->getMessage()];
        }
    }

    /**
     * Replace variables in message
     */
    private function replaceVariables($message, $variables)
    {
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        return $message;
    }

    /**
     * Log notification
     */
    private function logNotification($type, $recipient, $content, $status, $error = null)
    {
        try {
            NotificationLog::create([
                'user_id' => auth()->id() ?? 1,
                'type' => $type,
                'recipient' => $recipient,
                'content' => $content,
                'status' => $status,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log notification: ' . $e->getMessage());
        }
    }

    /**
     * Increment SMS count
     */
    private function incrementSmsCount()
    {
        $currentCount = Setting::getValue('sms_monthly_count', 0);
        Setting::setValue('sms_monthly_count', $currentCount + 1);
    }

    /**
     * Reset SMS count (call this monthly)
     */
    public function resetSmsCount()
    {
        Setting::setValue('sms_monthly_count', 0);
    }

    /**
     * Get SMS statistics
     */
    public function getSmsStats()
    {
        return [
            'enabled' => $this->isSmsEnabled,
            'limit' => $this->smsLimit,
            'count' => $this->smsCount,
            'remaining' => $this->smsLimit - $this->smsCount,
            'provider' => $this->smsProvider,
        ];
    }

    /**
     * Enable/Disable SMS
     */
    public function setSmsEnabled($enabled)
    {
        Setting::setValue('sms_enabled', $enabled);
        $this->isSmsEnabled = $enabled;
    }

    /**
     * Enable/Disable Email
     */
    public function setEmailEnabled($enabled)
    {
        Setting::setValue('email_enabled', $enabled);
        $this->isEmailEnabled = $enabled;
    }

    /**
     * Set SMS monthly limit
     */
    public function setSmsLimit($limit)
    {
        Setting::setValue('sms_monthly_limit', $limit);
        $this->smsLimit = $limit;
    }

    /**
     * Test SMS sending
     */
    public function testSms($phone)
    {
        return $this->sendSms($phone, 'This is a test SMS from HRMS notification system.');
    }

    /**
     * Test Email sending
     */
    public function testEmail($email)
    {
        return $this->sendEmail($email, 'HRMS Test Email', 'This is a test email from HRMS notification system.');
    }
}
