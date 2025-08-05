<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Backup;
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
     * Display owner's backups
     */
    public function index(Request $request)
    {
        $owner = Auth::user()->owner;
        
        $query = Backup::where('owner_id', $owner->id)
            ->with(['creator', 'restorer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $backups = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get owner-specific stats
        $stats = [
            'total' => Backup::where('owner_id', $owner->id)->count(),
            'completed' => Backup::where('owner_id', $owner->id)->where('status', 'completed')->count(),
            'recent' => Backup::where('owner_id', $owner->id)->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('owner.backups.index', compact('backups', 'stats'));
    }

    /**
     * Create a new backup for owner
     */
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $owner = Auth::user()->owner;

        try {
            $backup = $this->backupService->createOwnerBackup(
                $owner->id, 
                Auth::id()
            );

            // Add notes if provided
            if ($request->filled('notes')) {
                $backup->update(['notes' => $request->notes]);
            }

            return redirect()->route('owner.backups.index')
                ->with('success', 'Backup created successfully!');

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
        // Ensure owner can only view their own backups
        if ($backup->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized access to backup');
        }

        $backup->load(['creator', 'restorer']);
        
        return view('owner.backups.show', compact('backup'));
    }

    /**
     * Download backup file
     */
    public function download(Backup $backup)
    {
        // Ensure owner can only download their own backups
        if ($backup->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized access to backup');
        }

        try {
            return $this->backupService->downloadBackup($backup->id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore backup (owner can only restore their own backups)
     */
    public function restore(Backup $backup)
    {
        // Ensure owner can only restore their own backups
        if ($backup->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized access to backup');
        }

        try {
            $this->backupService->restoreBackup($backup->id, Auth::id());
            
            return redirect()->route('owner.backups.index')
                ->with('success', 'Backup restored successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Get backup statistics for owner
     */
    public function getStats()
    {
        $owner = Auth::user()->owner;
        
        $stats = [
            'total' => Backup::where('owner_id', $owner->id)->count(),
            'completed' => Backup::where('owner_id', $owner->id)->where('status', 'completed')->count(),
            'recent' => Backup::where('owner_id', $owner->id)->where('created_at', '>=', now()->subDays(7))->count(),
            'total_size' => $this->formatBytes(Backup::where('owner_id', $owner->id)->sum('size')),
        ];
        
        return response()->json($stats);
    }

    /**
     * Get backup details via AJAX
     */
    public function getDetails(Backup $backup)
    {
        // Ensure owner can only view their own backups
        if ($backup->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized access to backup');
        }

        $backup->load(['creator', 'restorer']);
        
        return response()->json([
            'id' => $backup->id,
            'filename' => $backup->filename,
            'type' => $backup->type_label,
            'size' => $backup->formatted_size,
            'status' => $backup->status,
            'created_at' => $backup->created_at->format('Y-m-d H:i:s'),
            'created_by' => $backup->creator->name ?? 'Unknown',
            'notes' => $backup->notes,
            'is_restored' => $backup->is_restored,
            'restored_at' => $backup->restored_at?->format('Y-m-d H:i:s'),
            'restored_by' => $backup->restorer->name ?? 'N/A',
            'file_exists' => $backup->file_exists,
        ]);
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