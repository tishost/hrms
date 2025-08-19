<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run()
    {
        // Default building limit
        SystemSetting::updateOrCreate(['key' => 'default_building_limit'], ['value' => '1']);
        
        // Currency settings (system_* keys for admin panel)
        SystemSetting::updateOrCreate(['key' => 'system_currency'], ['value' => 'BDT']);
        SystemSetting::updateOrCreate(['key' => 'system_currency_symbol'], ['value' => '৳']);
        SystemSetting::updateOrCreate(['key' => 'system_currency_position'], ['value' => 'left']);
        SystemSetting::updateOrCreate(['key' => 'system_decimal_places'], ['value' => '2']);
        SystemSetting::updateOrCreate(['key' => 'system_thousand_separator'], ['value' => ',']);
        
        // Additional currency settings for better compatibility
        SystemSetting::updateOrCreate(['key' => 'currency_symbol'], ['value' => '৳']);
        SystemSetting::updateOrCreate(['key' => 'currency_code'], ['value' => 'BDT']);
        SystemSetting::updateOrCreate(['key' => 'currency_position'], ['value' => 'left']);
        SystemSetting::updateOrCreate(['key' => 'currency_decimals'], ['value' => '2']);
        
        // System settings
        SystemSetting::updateOrCreate(['key' => 'system_timezone'], ['value' => 'Asia/Dhaka']);
        SystemSetting::updateOrCreate(['key' => 'system_date_format'], ['value' => 'd/m/Y']);
        SystemSetting::updateOrCreate(['key' => 'system_time_format'], ['value' => 'H:i']);
        SystemSetting::updateOrCreate(['key' => 'system_datetime_format'], ['value' => 'd/m/Y H:i']);
        SystemSetting::updateOrCreate(['key' => 'system_week_start'], ['value' => 'sunday']);
        SystemSetting::updateOrCreate(['key' => 'system_language'], ['value' => 'en']);
        SystemSetting::updateOrCreate(['key' => 'system_pagination'], ['value' => '20']);
        
        // System modes
        SystemSetting::updateOrCreate(['key' => 'system_maintenance_mode'], ['value' => '0']);
        SystemSetting::updateOrCreate(['key' => 'system_debug_mode'], ['value' => '0']);
        
        // Notification settings
        SystemSetting::updateOrCreate(['key' => 'system_email_notifications'], ['value' => '1']);
        SystemSetting::updateOrCreate(['key' => 'system_sms_notifications'], ['value' => '1']);
        SystemSetting::updateOrCreate(['key' => 'system_push_notifications'], ['value' => '1']);
        SystemSetting::updateOrCreate(['key' => 'system_notification_sound'], ['value' => '1']);
        
        // HRMS specific settings
        SystemSetting::updateOrCreate(['key' => 'company_name'], ['value' => 'BariManager']);
        SystemSetting::updateOrCreate(['key' => 'company_address'], ['value' => 'Dhaka, Bangladesh']);
        SystemSetting::updateOrCreate(['key' => 'company_phone'], ['value' => '+880 9611 677170']);
        SystemSetting::updateOrCreate(['key' => 'company_email'], ['value' => 'info@barimanager.com']);
        SystemSetting::updateOrCreate(['key' => 'company_website'], ['value' => 'www.barimanager.com']);
        
        // OTP settings
        SystemSetting::updateOrCreate(['key' => 'otp_expiry_minutes'], ['value' => '10']);
        SystemSetting::updateOrCreate(['key' => 'otp_resend_cooldown_seconds'], ['value' => '60']);
        SystemSetting::updateOrCreate(['key' => 'otp_length'], ['value' => '6']);
        SystemSetting::updateOrCreate(['key' => 'otp_max_attempts'], ['value' => '3']);
        SystemSetting::updateOrCreate(['key' => 'otp_daily_limit'], ['value' => '5']);
        
        // Payment settings
        SystemSetting::updateOrCreate(['key' => 'payment_gateway'], ['value' => 'bkash']);
        SystemSetting::updateOrCreate(['key' => 'payment_currency'], ['value' => 'BDT']);
        SystemSetting::updateOrCreate(['key' => 'payment_min_amount'], ['value' => '10']);
        SystemSetting::updateOrCreate(['key' => 'payment_max_amount'], ['value' => '100000']);
        
        // Security settings
        SystemSetting::updateOrCreate(['key' => 'password_min_length'], ['value' => '8']);
        SystemSetting::updateOrCreate(['key' => 'password_require_special'], ['value' => '1']);
        SystemSetting::updateOrCreate(['key' => 'session_timeout_minutes'], ['value' => '120']);
        SystemSetting::updateOrCreate(['key' => 'max_login_attempts'], ['value' => '5']);
        
        // Ads settings
        SystemSetting::updateOrCreate(['key' => 'system_ads_enabled'], ['value' => '1']);
    }
}
