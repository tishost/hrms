<?php

namespace App\Services;

use App\Models\PackageLimit;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;

class PackageLimitService
{
    /**
     * Initialize limits for an owner based on their subscription plan
     */
    public function initializeLimits(Owner $owner)
    {
        $subscription = $owner->activeSubscription;
        if (!$subscription) return;

        $plan = $subscription->plan;

        $limits = [
            'properties' => [
                'max_limit' => $plan->properties_limit,
                'reset_frequency' => 'never'
            ],
            'units' => [
                'max_limit' => $plan->units_limit,
                'reset_frequency' => 'never'
            ],
            'tenants' => [
                'max_limit' => $plan->tenants_limit,
                'reset_frequency' => 'never'
            ],
            'sms' => [
                'max_limit' => $plan->sms_notification ? 1000 : 0,
                'reset_frequency' => 'monthly'
            ],
            'emails' => [
                'max_limit' => 10000,
                'reset_frequency' => 'monthly'
            ],
            'notifications' => [
                'max_limit' => 5000,
                'reset_frequency' => 'monthly'
            ]
        ];

        foreach ($limits as $type => $config) {
            PackageLimit::updateOrCreate(
                [
                    'owner_id' => $owner->id,
                    'limit_type' => $type
                ],
                [
                    'max_limit' => $config['max_limit'],
                    'reset_frequency' => $config['reset_frequency'],
                    'reset_date' => $this->calculateResetDate($config['reset_frequency']),
                    'is_active' => true
                ]
            );
        }
    }

    /**
     * Check if owner can perform an action
     */
    public function canPerformAction(Owner $owner, string $action, int $amount = 1)
    {
        $limit = PackageLimit::where('owner_id', $owner->id)
            ->where('limit_type', $action)
            ->where('is_active', true)
            ->first();

        if (!$limit) return true; // No limit set

        // Check if reset is due
        if ($limit->isResetDue()) {
            $limit->resetUsage();
        }

        return $limit->getRemaining() >= $amount;
    }

    /**
     * Increment usage for an action
     */
    public function incrementUsage(Owner $owner, string $action, int $amount = 1)
    {
        $limit = PackageLimit::where('owner_id', $owner->id)
            ->where('limit_type', $action)
            ->where('is_active', true)
            ->first();

        if ($limit) {
            $limit->incrementUsage($amount);
        }
    }

    /**
     * Decrement usage for an action
     */
    public function decrementUsage(Owner $owner, string $action, int $amount = 1)
    {
        $limit = PackageLimit::where('owner_id', $owner->id)
            ->where('limit_type', $action)
            ->where('is_active', true)
            ->first();

        if ($limit) {
            $limit->decrementUsage($amount);
        }
    }

    /**
     * Get all limits for an owner
     */
    public function getOwnerLimits(Owner $owner)
    {
        return PackageLimit::where('owner_id', $owner->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('limit_type');
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats(Owner $owner)
    {
        $limits = $this->getOwnerLimits($owner);
        $stats = [];

        foreach ($limits as $type => $limit) {
            if ($limit->isResetDue()) {
                $limit->resetUsage();
            }

            $stats[$type] = [
                'current' => $limit->current_usage,
                'max' => $limit->max_limit,
                'remaining' => $limit->getRemaining(),
                'percentage' => $limit->getUsagePercentage(),
                'color' => $limit->getStatusColor(),
                'exceeded' => $limit->isExceeded(),
                'reset_date' => $limit->reset_date
            ];
        }

        return $stats;
    }

    /**
     * Check and reset overdue limits
     */
    public function checkAndResetLimits()
    {
        $overdueLimits = PackageLimit::where('reset_date', '<', now())
            ->where('is_active', true)
            ->get();

        foreach ($overdueLimits as $limit) {
            $limit->resetUsage();
        }
    }

    /**
     * Calculate reset date based on frequency
     */
    private function calculateResetDate($frequency)
    {
        switch ($frequency) {
            case 'monthly':
                return now()->addMonth();
            case 'yearly':
                return now()->addYear();
            case 'never':
                return now()->addYears(100); // Far future
            default:
                return now()->addMonth();
        }
    }

    /**
     * Get limit suggestions for upgrade
     */
    public function getUpgradeSuggestions(Owner $owner)
    {
        $stats = $this->getUsageStats($owner);
        $suggestions = [];

        foreach ($stats as $type => $stat) {
            if ($stat['percentage'] >= 80) {
                $suggestions[] = [
                    'type' => $type,
                    'current' => $stat['current'],
                    'max' => $stat['max'],
                    'percentage' => $stat['percentage']
                ];
            }
        }

        return $suggestions;
    }
}
