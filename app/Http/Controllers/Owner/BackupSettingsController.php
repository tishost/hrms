<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupSettingsController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display owner backup settings page
     */
    public function index()
    {
        $owner = Auth::user()->owner;
        $settings = SystemSetting::pluck('value', 'key');
        
        // Get owner-specific backup stats
        $backupStats = [
            'total' => \App\Models\Backup::where('owner_id', $owner->id)->count(),
            'completed' => \App\Models\Backup::where('owner_id', $owner->id)->where('status', 'completed')->count(),
            'recent' => \App\Models\Backup::where('owner_id', $owner->id)->where('created_at', '>=', now()->subDays(7))->count(),
            'total_size' => $this->formatBytes(\App\Models\Backup::where('owner_id', $owner->id)->sum('size')),
        ];
        
        return view('owner.settings.backup', compact('settings', 'backupStats'));
    }

    /**
     * Update owner backup settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'owner_backup_enabled' => 'boolean',
            'owner_backup_frequency' => 'required|in:daily,weekly,monthly',
            'owner_backup_retention_days' => 'required|integer|min:1|max:90',
            'owner_backup_notification_email' => 'nullable|email',
        ]);

        $owner = Auth::user()->owner;
        
        $settings = [
            'owner_backup_enabled' => $request->boolean('owner_backup_enabled'),
            'owner_backup_frequency' => $request->owner_backup_frequency,
            'owner_backup_retention_days' => $request->owner_backup_retention_days,
            'owner_backup_notification_email' => $request->owner_backup_notification_email,
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Backup settings updated successfully!');
    }

    /**
     * Test owner backup functionality
     */
    public function testBackup()
    {
        try {
            $owner = Auth::user()->owner;
            $backup = $this->backupService->createOwnerBackup($owner->id, Auth::id());
            
            return response()->json([
                'success' => true,
                'message' => 'Test backup created successfully!',
                'backup_id' => $backup->id,
                'filename' => $backup->filename,
                'size' => $backup->formatted_size
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get owner backup statistics
     */
    public function getStats()
    {
        try {
            $owner = Auth::user()->owner;
            
            $stats = [
                'total' => \App\Models\Backup::where('owner_id', $owner->id)->count(),
                'completed' => \App\Models\Backup::where('owner_id', $owner->id)->where('status', 'completed')->count(),
                'recent' => \App\Models\Backup::where('owner_id', $owner->id)->where('created_at', '>=', now()->subDays(7))->count(),
                'total_size' => $this->formatBytes(\App\Models\Backup::where('owner_id', $owner->id)->sum('size')),
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
} 