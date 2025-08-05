<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SystemHelper;

class ApplySystemSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Set timezone
        $timezone = SystemHelper::getTimezone();
        date_default_timezone_set($timezone);
        
        // Set locale
        $language = SystemHelper::getLanguage();
        app()->setLocale($language);
        
        // Share system settings with all views
        view()->share('systemSettings', [
            'company_name' => SystemHelper::getCompanyName(),
            'company_logo' => SystemHelper::getCompanyLogo(),
            'company_favicon' => SystemHelper::getCompanyFavicon(),
            'currency_symbol' => SystemHelper::getSetting('system_currency_symbol', '৳'),
            'date_format' => SystemHelper::getSetting('system_date_format', 'Y-m-d'),
            'time_format' => SystemHelper::getSetting('system_time_format', 'H:i'),
            'pagination_limit' => SystemHelper::getPaginationLimit(),
            'maintenance_mode' => SystemHelper::isMaintenanceMode(),
            'debug_mode' => SystemHelper::isDebugMode(),
            'email_notifications' => SystemHelper::isEmailNotificationsEnabled(),
            'sms_notifications' => SystemHelper::isSmsNotificationsEnabled(),
        ]);

        // Check maintenance mode
        if (SystemHelper::isMaintenanceMode() && !auth()->check()) {
            return response()->view('maintenance');
        }

        return $next($request);
    }
} 