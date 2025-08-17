<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OtpSecurityService;
use App\Models\OtpLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpSecurityController extends Controller
{
    /**
     * Show OTP security dashboard
     */
    public function index(Request $request): View
    {
        $hours = $request->get('hours', 24);
        
        $statistics = OtpSecurityService::getAbuseStatistics($hours);
        $securityReport = OtpSecurityService::getSecurityReport($hours);
        
        // Get recent suspicious activities
        $recentSuspicious = OtpLog::where('is_suspicious', true)
            ->where('created_at', '>', now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get blocked IPs and phones
        $blockedIps = OtpLog::where('status', 'blocked')
            ->where('blocked_until', '>', now())
            ->whereNotNull('ip_address')
            ->distinct()
            ->pluck('ip_address');

        $blockedPhones = OtpLog::where('status', 'blocked')
            ->where('blocked_until', '>', now())
            ->whereNotNull('phone')
            ->distinct()
            ->pluck('phone');

        // Phone attempt summary (last N hours)
        $phoneAttempts = OtpLog::select('phone')
            ->whereNotNull('phone')
            ->where('created_at', '>', now()->subHours($hours))
            ->where('status', '!=', 'blocked') // Exclude blocked logs from total count
            ->selectRaw('COUNT(*) as total_attempts')
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_attempts")
            ->selectRaw("SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified_attempts")
            ->selectRaw("MAX(CASE WHEN status = 'blocked' AND blocked_until > NOW() THEN 1 ELSE 0 END) as is_blocked")
            ->selectRaw('MAX(blocked_until) as blocked_until')
            ->groupBy('phone')
            ->orderByDesc('total_attempts')
            ->limit(200)
            ->get();

        return view('admin.security.otp', compact(
            'statistics',
            'securityReport',
            'recentSuspicious',
            'blockedIps',
            'blockedPhones',
            'hours',
            'phoneAttempts'
        ));
    }

    /**
     * Show detailed OTP logs
     */
    public function logs(Request $request): View
    {
        $query = OtpLog::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by IP
        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filter suspicious activities
        if ($request->boolean('suspicious_only')) {
            $query->where('is_suspicious', true);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.security.otp-logs', compact('logs'));
    }

    /**
     * Unblock IP address
     */
    public function unblockIp(Request $request)
    {
        $ip = $request->ip;
        
        OtpLog::where('ip_address', $ip)
            ->where('status', 'blocked')
            ->update([
                'status' => 'sent',
                'blocked_until' => null,
                'reason' => null
            ]);

        return response()->json([
            'success' => true,
            'message' => "IP address {$ip} has been unblocked."
        ]);
    }

    /**
     * Unblock phone number
     */
    public function unblockPhone(Request $request)
    {
        $phone = $request->phone;
        
        OtpLog::where('phone', $phone)
            ->where('status', 'blocked')
            ->update([
                'status' => 'sent',
                'blocked_until' => null,
                'reason' => null
            ]);

        return response()->json([
            'success' => true,
            'message' => "Phone number {$phone} has been unblocked."
        ]);
    }

    /**
     * Block IP address manually
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'duration' => 'required|integer|min:1|max:1440', // minutes
            'reason' => 'required|string|max:255'
        ]);

        OtpSecurityService::blockIp($request->ip, $request->duration, $request->reason);

        return response()->json([
            'success' => true,
            'message' => "IP address {$request->ip} has been blocked for {$request->duration} minutes."
        ]);
    }

    /**
     * Block phone number manually
     */
    public function blockPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'duration' => 'required|integer|min:1|max:1440', // minutes
            'reason' => 'required|string|max:255'
        ]);

        OtpSecurityService::blockPhone($request->phone, $request->duration, $request->reason);

        return response()->json([
            'success' => true,
            'message' => "Phone number {$request->phone} has been blocked for {$request->duration} minutes."
        ]);
    }

    /**
     * Get abuse statistics as JSON
     */
    public function getStatistics(Request $request)
    {
        $hours = $request->get('hours', 24);
        $statistics = OtpSecurityService::getAbuseStatistics($hours);
        
        return response()->json($statistics);
    }

    /**
     * Export OTP logs as CSV
     */
    public function exportLogs(Request $request)
    {
        $query = OtpLog::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'otp_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Phone', 'OTP', 'Type', 'IP Address', 'Status', 
                'Suspicious', 'Abuse Score', 'Device Info', 'Location',
                'Created At', 'Blocked Until', 'Reason'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->phone,
                    $log->otp,
                    $log->type,
                    $log->ip_address,
                    $log->status,
                    $log->is_suspicious ? 'Yes' : 'No',
                    $log->abuse_score,
                    $log->device_info,
                    $log->location,
                    $log->created_at,
                    $log->blocked_until,
                    $log->reason
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Reset OTP attempt/limit for a phone number
     */
    public function resetPhoneLimit(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);
        $phone = $request->phone;

        \Log::info('Resetting OTP limit for phone: ' . $phone);

        try {
            // Clear blocked status and failed attempts for this phone
            $updatedLogs = OtpLog::where('phone', $phone)
                ->whereIn('status', ['blocked', 'failed'])
                ->update([
                    'status' => 'sent',
                    'blocked_until' => null,
                    'reason' => null,
                    'abuse_score' => 0,
                ]);

            \Log::info("Updated {$updatedLogs} OTP log records for phone: {$phone}");

            // Also clear any existing OTP records for this phone to allow new OTP generation
            $deletedOtps = \App\Models\Otp::where('phone', $phone)
                ->where('is_used', false)
                ->delete();

            \Log::info("Deleted {$deletedOtps} existing OTP records for phone: {$phone}");

            return response()->json([
                'success' => true,
                'message' => "OTP limit/attempts for {$phone} have been reset. {$updatedLogs} log records updated, {$deletedOtps} OTP records cleared.",
            ]);

        } catch (\Exception $e) {
            \Log::error('Error resetting OTP limit for phone ' . $phone . ': ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset OTP limit: ' . $e->getMessage(),
            ], 500);
        }
    }
} 