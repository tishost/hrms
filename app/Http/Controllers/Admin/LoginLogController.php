<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use App\Services\LoginLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginLogController extends Controller
{
    protected $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

    /**
     * Display login logs with filters
     */
    public function index(Request $request)
    {
        $query = LoginLog::with('user');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('login_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('login_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $logs = $query->orderBy('login_at', 'desc')
            ->paginate(\App\Helpers\SystemHelper::getPaginationLimit());

        // Get statistics
        $stats = $this->getLoginStatistics();
        
        // Get suspicious IPs
        $suspiciousIps = $this->loginLogService->getSuspiciousLogins();

        return view('admin.login-logs.index', compact('logs', 'stats', 'suspiciousIps'));
    }

    /**
     * Show login log details
     */
    public function show(LoginLog $loginLog)
    {
        return view('admin.login-logs.show', compact('loginLog'));
    }

    /**
     * Get user login history
     */
    public function userHistory(User $user)
    {
        $logs = $this->loginLogService->getUserLoginHistory($user, 50);
        
        return view('admin.login-logs.user-history', compact('user', 'logs'));
    }

    /**
     * Get active sessions
     */
    public function activeSessions()
    {
        $sessions = $this->loginLogService->getActiveSessions();
        
        return view('admin.login-logs.active-sessions', compact('sessions'));
    }

    /**
     * Block IP address
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255'
        ]);

        $this->loginLogService->blockIpAddress(
            $request->ip_address, 
            $request->reason ?? 'Admin blocked'
        );

        return redirect()->back()->with('success', 'IP address blocked successfully.');
    }

    /**
     * Unblock IP address
     */
    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip'
        ]);

        // Remove recent blocks for this IP
        LoginLog::where('ip_address', $request->ip_address)
            ->where('status', 'blocked')
            ->where('login_at', '>=', Carbon::now()->subHour())
            ->delete();

        return redirect()->back()->with('success', 'IP address unblocked successfully.');
    }

    /**
     * Export login logs
     */
    public function export(Request $request)
    {
        $query = LoginLog::with('user');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('date_from')) {
            $query->where('login_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('login_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $logs = $query->orderBy('login_at', 'desc')->get();

        // Generate CSV
        $filename = 'login_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'User', 'Email', 'IP Address', 'Device Type', 'Platform', 
                'Browser', 'OS', 'Location', 'Status', 'Login Method', 'Login At', 
                'Logout At', 'Session Duration'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'N/A',
                    $log->email,
                    $log->ip_address,
                    $log->device_type,
                    $log->platform,
                    $log->browser,
                    $log->os,
                    $log->location_string,
                    $log->status,
                    $log->login_method,
                    $log->login_at->format('Y-m-d H:i:s'),
                    $log->logout_at ? $log->logout_at->format('Y-m-d H:i:s') : 'N/A',
                    $log->session_duration
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get login statistics
     */
    private function getLoginStatistics()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today' => [
                'total' => LoginLog::whereDate('login_at', $today)->count(),
                'successful' => LoginLog::whereDate('login_at', $today)->successful()->count(),
                'failed' => LoginLog::whereDate('login_at', $today)->failed()->count(),
                'blocked' => LoginLog::whereDate('login_at', $today)->blocked()->count(),
            ],
            'yesterday' => [
                'total' => LoginLog::whereDate('login_at', $yesterday)->count(),
                'successful' => LoginLog::whereDate('login_at', $yesterday)->successful()->count(),
                'failed' => LoginLog::whereDate('login_at', $yesterday)->failed()->count(),
                'blocked' => LoginLog::whereDate('login_at', $yesterday)->blocked()->count(),
            ],
            'this_week' => [
                'total' => LoginLog::where('login_at', '>=', $thisWeek)->count(),
                'successful' => LoginLog::where('login_at', '>=', $thisWeek)->successful()->count(),
                'failed' => LoginLog::where('login_at', '>=', $thisWeek)->failed()->count(),
                'blocked' => LoginLog::where('login_at', '>=', $thisWeek)->blocked()->count(),
            ],
            'this_month' => [
                'total' => LoginLog::where('login_at', '>=', $thisMonth)->count(),
                'successful' => LoginLog::where('login_at', '>=', $thisMonth)->successful()->count(),
                'failed' => LoginLog::where('login_at', '>=', $thisMonth)->failed()->count(),
                'blocked' => LoginLog::where('login_at', '>=', $thisMonth)->blocked()->count(),
            ],
            'device_types' => [
                'web' => LoginLog::web()->count(),
                'mobile' => LoginLog::mobile()->count(),
                'tablet' => LoginLog::tablet()->count(),
            ],
            'platforms' => [
                'web' => LoginLog::where('platform', 'web')->count(),
                'ios' => LoginLog::where('platform', 'ios')->count(),
                'android' => LoginLog::where('platform', 'android')->count(),
                'desktop' => LoginLog::where('platform', 'desktop')->count(),
            ]
        ];
    }

    /**
     * Get real-time login statistics
     */
    public function getRealTimeStats()
    {
        $lastHour = Carbon::now()->subHour();
        
        $stats = [
            'last_hour' => [
                'total' => LoginLog::where('login_at', '>=', $lastHour)->count(),
                'successful' => LoginLog::where('login_at', '>=', $lastHour)->successful()->count(),
                'failed' => LoginLog::where('login_at', '>=', $lastHour)->failed()->count(),
            ],
            'active_sessions' => LoginLog::where('status', 'success')
                ->whereNull('logout_at')
                ->where('login_at', '>=', Carbon::now()->subDay())
                ->count(),
            'suspicious_ips' => $this->loginLogService->getSuspiciousLogins(1)->count()
        ];

        return response()->json($stats);
    }
} 