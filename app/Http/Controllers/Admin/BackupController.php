<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Models\Owner;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display backup management page
     */
    public function index(Request $request)
    {
        $query = Backup::with(['owner', 'creator', 'restorer']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $backups = $query->orderBy('created_at', 'desc')->paginate(15);
        $owners = Owner::all();
        $stats = $this->backupService->getBackupStats();

        return view('admin.backups.index', compact('backups', 'owners', 'stats'));
    }

    /**
     * Create a new backup
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,owner',
            'owner_id' => 'required_if:type,owner|exists:owners,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            if ($request->type === 'owner') {
                $backup = $this->backupService->createOwnerBackup(
                    $request->owner_id, 
                    Auth::id()
                );
                $message = 'Owner backup created successfully!';
            } else {
                $backup = $this->backupService->createFullBackup(Auth::id());
                $message = 'Full system backup created successfully!';
            }

            // Add notes if provided
            if ($request->filled('notes')) {
                $backup->update(['notes' => $request->notes]);
            }

            return redirect()->route('admin.backups.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Backup failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show backup details
     */
    public function show(Backup $backup)
    {
        $backup->load(['owner', 'creator', 'restorer']);
        
        return view('admin.backups.show', compact('backup'));
    }

    /**
     * Download backup file
     */
    public function download(Backup $backup)
    {
        try {
            return $this->backupService->downloadBackup($backup->id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore backup
     */
    public function restore(Backup $backup)
    {
        try {
            $this->backupService->restoreBackup($backup->id, Auth::id());
            
            return redirect()->route('admin.backups.index')
                ->with('success', 'Backup restored successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete backup
     */
    public function destroy(Backup $backup)
    {
        try {
            $this->backupService->deleteBackup($backup->id);
            
            return redirect()->route('admin.backups.index')
                ->with('success', 'Backup deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Clean old backups
     */
    public function cleanOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        try {
            $deletedCount = $this->backupService->cleanOldBackups($request->days);
            
            return redirect()->route('admin.backups.index')
                ->with('success', "{$deletedCount} old backups cleaned successfully!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Clean failed: ' . $e->getMessage());
        }
    }

    /**
     * Get backup statistics via AJAX
     */
    public function getStats()
    {
        $stats = $this->backupService->getBackupStats();
        
        return response()->json($stats);
    }

    /**
     * Get backup details via AJAX
     */
    public function getDetails(Backup $backup)
    {
        $backup->load(['owner', 'creator', 'restorer']);
        
        return response()->json([
            'id' => $backup->id,
            'filename' => $backup->filename,
            'type' => $backup->type_label,
            'size' => $backup->formatted_size,
            'status' => $backup->status,
            'created_at' => $backup->created_at->format('Y-m-d H:i:s'),
            'created_by' => $backup->creator->name ?? 'Unknown',
            'owner' => $backup->owner->name ?? 'N/A',
            'notes' => $backup->notes,
            'is_restored' => $backup->is_restored,
            'restored_at' => $backup->restored_at?->format('Y-m-d H:i:s'),
            'restored_by' => $backup->restorer->name ?? 'N/A',
            'file_exists' => $backup->file_exists,
        ]);
    }
} 