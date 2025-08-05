<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Backup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'filename',
        'type', // full, owner, system
        'owner_id',
        'size',
        'status', // pending, completed, failed
        'created_by',
        'notes',
        'restored_at',
        'restored_by'
    ];

    protected $casts = [
        'restored_at' => 'datetime',
    ];

    /**
     * Get the owner that owns the backup
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the user who created the backup
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who restored the backup
     */
    public function restorer()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get backup type label
     */
    public function getTypeLabelAttribute()
    {
        switch ($this->type) {
            case 'full':
                return 'Full System';
            case 'owner':
                return 'Owner Data';
            case 'system':
                return 'System Files';
            default:
                return ucfirst($this->type);
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return 'badge-success';
            case 'pending':
                return 'badge-warning';
            case 'failed':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Check if backup is restored
     */
    public function getIsRestoredAttribute()
    {
        return !is_null($this->restored_at);
    }

    /**
     * Get backup file path
     */
    public function getFilePathAttribute()
    {
        return storage_path("app/backups/{$this->filename}");
    }

    /**
     * Check if backup file exists
     */
    public function getFileExistsAttribute()
    {
        return file_exists($this->file_path);
    }

    /**
     * Scope for full backups
     */
    public function scopeFull($query)
    {
        return $query->where('type', 'full');
    }

    /**
     * Scope for owner backups
     */
    public function scopeOwner($query)
    {
        return $query->where('type', 'owner');
    }

    /**
     * Scope for completed backups
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for recent backups
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }
} 