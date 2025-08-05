<?php

namespace App\Http\Controllers\Admin;

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
     * Display backup settings page
     */
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key');
        $backupStats = $this->backupService->getBackupStats();
        
        return view('admin.settings.backup', compact('settings', 'backupStats'));
    }

    /**
     * Update backup settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'backup_auto_enabled' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'backup_include_files' => 'boolean',
            'backup_include_database' => 'boolean',
            'backup_compression' => 'boolean',
            'backup_notification_email' => 'nullable|email',
            'backup_max_size_mb' => 'required|integer|min:1|max:1000',
        ]);

        $settings = [
            'backup_auto_enabled' => $request->boolean('backup_auto_enabled'),
            'backup_frequency' => $request->backup_frequency,
            'backup_retention_days' => $request->backup_retention_days,
            'backup_include_files' => $request->boolean('backup_include_files'),
            'backup_include_database' => $request->boolean('backup_include_database'),
            'backup_compression' => $request->boolean('backup_compression'),
            'backup_notification_email' => $request->backup_notification_email,
            'backup_max_size_mb' => $request->backup_max_size_mb,
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
     * Test backup functionality
     */
    public function testBackup()
    {
        try {
            $backup = $this->backupService->createFullBackup(Auth::id());
            
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
     * Clean old backups
     */
    public function cleanOldBackups(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        try {
            $deletedCount = $this->backupService->cleanOldBackups($request->days);
            
            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} old backups cleaned successfully!",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Clean failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup statistics
     */
    public function getStats()
    {
        try {
            $stats = $this->backupService->getBackupStats();
            
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
     * Schedule automatic backup
     */
    public function scheduleBackup(Request $request)
    {
        $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'enabled' => 'boolean'
        ]);

        try {
            // Update schedule settings
            SystemSetting::updateOrCreate(
                ['key' => 'backup_schedule_frequency'],
                ['value' => $request->frequency]
            );
            
            SystemSetting::updateOrCreate(
                ['key' => 'backup_schedule_time'],
                ['value' => $request->time]
            );
            
            SystemSetting::updateOrCreate(
                ['key' => 'backup_schedule_enabled'],
                ['value' => $request->boolean('enabled')]
            );

            return response()->json([
                'success' => true,
                'message' => 'Backup schedule updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule: ' . $e->getMessage()
            ], 500);
        }
    }
} 