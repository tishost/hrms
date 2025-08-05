<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\OwnerSubscription;
use App\Models\Tenant;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;

class OwnerSmsService
{
    private $smsService;
    private $notificationService;

    public function __construct()
    {
        $this->smsService = new SmsService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Send SMS for Owner (deducts from owner's credits)
     */
    public function sendOwnerSms($ownerId, $phoneNumber, $message, $templateName = null, $variables = [])
    {
        try {
            $owner = Owner::find($ownerId);
            if (!$owner) {
                return ['success' => false, 'message' => 'Owner not found'];
            }

            $subscription = $owner->subscription;
            if (!$subscription) {
                return ['success' => false, 'message' => 'No active subscription found'];
            }

            // Check if owner can send SMS
            if (!$subscription->canSendSms()) {
                return ['success' => false, 'message' => 'Insufficient SMS credits or SMS not enabled'];
            }

            // Use template if provided
            if ($templateName) {
                $result = $this->notificationService->sendTemplateNotification('sms', $phoneNumber, $templateName, $variables);
            } else {
                $result = $this->smsService->sendSms($phoneNumber, $message);
            }

            // If SMS sent successfully, deduct credit
            if ($result['success']) {
                $subscription->deductSmsCredit();
                
                // Log the SMS
                $this->logSms($ownerId, $phoneNumber, $message, 'sent', $templateName);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Owner SMS sending failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'SMS sending failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send SMS for Tenant (uses owner's SMS settings and credits)
     */
    public function sendTenantSms($tenantId, $phoneNumber, $message, $templateName = null, $variables = [])
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return ['success' => false, 'message' => 'Tenant not found'];
            }

            // Get the property owner
            $property = $tenant->property;
            if (!$property) {
                return ['success' => false, 'message' => 'Tenant property not found'];
            }

            $owner = $property->owner;
            if (!$owner) {
                return ['success' => false, 'message' => 'Property owner not found'];
            }

            // Use owner's SMS service and credits
            return $this->sendOwnerSms($owner->id, $phoneNumber, $message, $templateName, $variables);

        } catch (\Exception $e) {
            Log::error('Tenant SMS sending failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'SMS sending failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send SMS using default system (when owner has no credits)
     */
    public function sendSystemSms($phoneNumber, $message, $templateName = null, $variables = [])
    {
        try {
            // Use default system SMS service
            if ($templateName) {
                $result = $this->notificationService->sendTemplateNotification('sms', $phoneNumber, $templateName, $variables);
            } else {
                $result = $this->smsService->sendSms($phoneNumber, $message);
            }

            // Log the SMS
            $this->logSms(null, $phoneNumber, $message, 'sent', $templateName, 'system');

            return $result;

        } catch (\Exception $e) {
            Log::error('System SMS sending failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'SMS sending failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send SMS with automatic routing (Owner -> Tenant -> System)
     */
    public function sendSmartSms($phoneNumber, $message, $ownerId = null, $tenantId = null, $templateName = null, $variables = [])
    {
        // Priority 1: Try owner SMS if ownerId provided
        if ($ownerId) {
            $result = $this->sendOwnerSms($ownerId, $phoneNumber, $message, $templateName, $variables);
            if ($result['success']) {
                return $result;
            }
        }

        // Priority 2: Try tenant SMS if tenantId provided
        if ($tenantId) {
            $result = $this->sendTenantSms($tenantId, $phoneNumber, $message, $templateName, $variables);
            if ($result['success']) {
                return $result;
            }
        }

        // Priority 3: Use default system SMS
        return $this->sendSystemSms($phoneNumber, $message, $templateName, $variables);
    }

    /**
     * Get SMS usage statistics for an owner
     */
    public function getOwnerSmsStats($ownerId)
    {
        try {
            $owner = Owner::find($ownerId);
            if (!$owner) {
                return ['success' => false, 'message' => 'Owner not found'];
            }

            $subscription = $owner->subscription;
            if (!$subscription) {
                return ['success' => false, 'message' => 'No active subscription found'];
            }

            // Get SMS logs for this owner
            $smsLogs = NotificationLog::where('type', 'sms')
                ->where('owner_id', $ownerId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get();

            return [
                'success' => true,
                'stats' => [
                    'subscription' => $subscription->getSmsUsageStats(),
                    'monthly_sent' => $smsLogs->where('status', 'sent')->count(),
                    'monthly_failed' => $smsLogs->where('status', 'failed')->count(),
                    'total_sent' => $smsLogs->where('status', 'sent')->count(),
                    'total_failed' => $smsLogs->where('status', 'failed')->count(),
                ]
            ];

        } catch (\Exception $e) {
            Log::error('SMS stats failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get SMS stats'];
        }
    }

    /**
     * Add SMS credits to owner
     */
    public function addOwnerSmsCredits($ownerId, $credits)
    {
        try {
            $owner = Owner::find($ownerId);
            if (!$owner) {
                return ['success' => false, 'message' => 'Owner not found'];
            }

            $subscription = $owner->subscription;
            if (!$subscription) {
                return ['success' => false, 'message' => 'No active subscription found'];
            }

            $newBalance = $subscription->addSmsCredits($credits);

            return [
                'success' => true,
                'message' => "Added {$credits} SMS credits",
                'new_balance' => $newBalance
            ];

        } catch (\Exception $e) {
            Log::error('Add SMS credits failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add SMS credits'];
        }
    }

    /**
     * Log SMS activity
     */
    private function logSms($ownerId, $phoneNumber, $message, $status, $templateName = null, $source = 'owner')
    {
        try {
            NotificationLog::create([
                'type' => 'sms',
                'recipient' => $phoneNumber,
                'content' => $message,
                'status' => $status,
                'owner_id' => $ownerId,
                'template_name' => $templateName,
                'source' => $source,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('SMS logging failed: ' . $e->getMessage());
        }
    }
} 