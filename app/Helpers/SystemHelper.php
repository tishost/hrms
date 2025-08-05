<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class SystemHelper
{
    /**
     * Get system setting value with fallback
     */
    public static function getSetting($key, $default = null)
    {
        return SystemSetting::getValue($key, $default);
    }

    /**
     * Format currency based on system settings
     */
    public static function formatCurrency($amount)
    {
        $currency = self::getSetting('system_currency', 'BDT');
        $symbol = self::getSetting('system_currency_symbol', '৳');
        $position = self::getSetting('system_currency_position', 'left');
        $decimalPlaces = (int) self::getSetting('system_decimal_places', '2');
        $thousandSeparator = self::getSetting('system_thousand_separator', ',');

        // Format the number
        $formattedAmount = number_format($amount, $decimalPlaces, '.', $thousandSeparator);

        // Apply currency position
        switch ($position) {
            case 'left':
                return $symbol . $formattedAmount;
            case 'right':
                return $formattedAmount . $symbol;
            case 'left_space':
                return $symbol . ' ' . $formattedAmount;
            case 'right_space':
                return $formattedAmount . ' ' . $symbol;
            default:
                return $symbol . $formattedAmount;
        }
    }

    /**
     * Format date based on system settings
     */
    public static function formatDate($date)
    {
        $format = self::getSetting('system_date_format', 'Y-m-d');
        return date($format, strtotime($date));
    }

    /**
     * Format time based on system settings
     */
    public static function formatTime($time)
    {
        $format = self::getSetting('system_time_format', 'H:i');
        return date($format, strtotime($time));
    }

    /**
     * Format datetime based on system settings
     */
    public static function formatDateTime($datetime)
    {
        $format = self::getSetting('system_datetime_format', 'Y-m-d H:i');
        return date($format, strtotime($datetime));
    }

    /**
     * Get company name
     */
    public static function getCompanyName()
    {
        return self::getSetting('company_name', 'HRMS');
    }

    /**
     * Get company logo URL
     */
    public static function getCompanyLogo()
    {
        $logo = self::getSetting('company_logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    /**
     * Get company favicon URL
     */
    public static function getCompanyFavicon()
    {
        $favicon = self::getSetting('company_favicon');
        return $favicon ? asset('storage/' . $favicon) : null;
    }

    /**
     * Get pagination limit
     */
    public static function getPaginationLimit()
    {
        return (int) self::getSetting('system_pagination', '20');
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode()
    {
        return (bool) self::getSetting('system_maintenance_mode', '0');
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebugMode()
    {
        return (bool) self::getSetting('system_debug_mode', '0');
    }

    /**
     * Check if email notifications are enabled
     */
    public static function isEmailNotificationsEnabled()
    {
        return (bool) self::getSetting('system_email_notifications', '1');
    }

    /**
     * Check if SMS notifications are enabled
     */
    public static function isSmsNotificationsEnabled()
    {
        return (bool) self::getSetting('system_sms_notifications', '1');
    }

    /**
     * Get system timezone
     */
    public static function getTimezone()
    {
        return self::getSetting('system_timezone', 'Asia/Dhaka');
    }

    /**
     * Get system language
     */
    public static function getLanguage()
    {
        return self::getSetting('system_language', 'en');
    }

    /**
     * Get week start day
     */
    public static function getWeekStart()
    {
        return self::getSetting('system_week_start', 'monday');
    }
} 