<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\SystemHelper;
use App\Models\SystemSetting;

class SystemController extends Controller
{
    /**
     * Return system status info for mobile/web clients.
     */
    public function status(Request $request)
    {
        $maintenance = SystemHelper::isMaintenanceMode();

        $details = [
            'company_name' => SystemHelper::getCompanyName(),
            'maintenance_mode' => $maintenance,
            'message' => SystemSetting::getValue('system_maintenance_message', $maintenance ? 'The system is under maintenance' : null),
            'description' => SystemSetting::getValue('system_maintenance_description', null),
            'until' => SystemSetting::getValue('system_maintenance_until', null),
        ];

        return response()->json([
            'success' => true,
            'maintenance' => $maintenance,
            'details' => $details,
        ]);
    }
}


