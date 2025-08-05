<?php

namespace App\Services;

use App\Models\Backup;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class BackupService
{
    /**
     * Create a full system backup
     */
    public function createFullBackup($userId = null)
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_full_{$timestamp}.sql";
            $backupPath = storage_path("app/backups/{$filename}");
            
            // Create backups directory if it doesn't exist
            $this->ensureBackupDirectory();
            
            // Get database configuration
            $config = $this->getDatabaseConfig();
            
            // Create mysqldump command
            $command = $this->buildMysqldumpCommand($config, $backupPath);
            
            // Execute backup command
            $this->executeBackupCommand($command);
            
            // Create backup record
            $backup = $this->createBackupRecord($filename, 'full', null, filesize($backupPath), $userId);
            
            return $backup;
        } catch (\Exception $e) {
            \Log::error('Full backup failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create an owner-specific backup
     */
    public function createOwnerBackup($ownerId, $userId = null)
    {
        try {
            $owner = Owner::find($ownerId);
            if (!$owner) {
                throw new \Exception('Owner not found');
            }
            
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_owner_{$ownerId}_{$timestamp}.sql";
            $backupPath = storage_path("app/backups/{$filename}");
            
            // Create backups directory if it doesn't exist
            $this->ensureBackupDirectory();
            
            // Get database configuration
            $config = $this->getDatabaseConfig();
            
            // Create mysqldump command for owner-specific data
            $command = $this->buildMysqldumpCommand($config, $backupPath, $ownerId);
            
            // Execute backup command
            $this->executeBackupCommand($command);
            
            // Create backup record
            $backup = $this->createBackupRecord($filename, 'owner', $ownerId, filesize($backupPath), $userId);
            
            return $backup;
        } catch (\Exception $e) {
            \Log::error('Owner backup failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Restore a backup
     */
    public function restoreBackup($backupId, $userId = null)
    {
        try {
            $backup = Backup::find($backupId);
            if (!$backup) {
                throw new \Exception('Backup not found');
            }
            
            if (!$backup->file_exists) {
                throw new \Exception('Backup file not found');
            }
            
            // Get database configuration
            $config = $this->getDatabaseConfig();
            
            // Create mysql restore command
            $command = $this->buildMysqlRestoreCommand($config, $backup->file_path);
            
            // Execute restore command
            $this->executeRestoreCommand($command);
            
            // Update backup record
            $backup->update([
                'restored_at' => Carbon::now(),
                'restored_by' => $userId,
            ]);
            
            return $backup;
        } catch (\Exception $e) {
            \Log::error('Backup restore failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Download backup file
     */
    public function downloadBackup($backupId)
    {
        $backup = Backup::find($backupId);
        if (!$backup || !$backup->file_exists) {
            throw new \Exception('Backup file not found');
        }
        
        return response()->download($backup->file_path, $backup->filename);
    }
    
    /**
     * Delete backup
     */
    public function deleteBackup($backupId)
    {
        $backup = Backup::find($backupId);
        if (!$backup) {
            throw new \Exception('Backup not found');
        }
        
        // Delete file if exists
        if ($backup->file_exists) {
            unlink($backup->file_path);
        }
        
        // Delete record
        $backup->delete();
        
        return true;
    }
    
    /**
     * Get backup statistics
     */
    public function getBackupStats()
    {
        $totalBackups = Backup::count();
        $completedBackups = Backup::completed()->count();
        $failedBackups = Backup::where('status', 'failed')->count();
        $totalSize = Backup::sum('size');
        
        $recentBackups = Backup::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $fullBackups = Backup::where('type', 'full')->count();
        $ownerBackups = Backup::where('type', 'owner')->count();
        
        return [
            'total' => $totalBackups,
            'completed' => $completedBackups,
            'failed' => $failedBackups,
            'total_size' => $this->formatBytes($totalSize),
            'recent' => $recentBackups,
            'full' => $fullBackups,
            'owner' => $ownerBackups,
        ];
    }
    
    /**
     * Clean old backups
     */
    public function cleanOldBackups($days = 30)
    {
        $oldBackups = Backup::where('created_at', '<', Carbon::now()->subDays($days))->get();
        
        foreach ($oldBackups as $backup) {
            $this->deleteBackup($backup->id);
        }
        
        return $oldBackups->count();
    }
    
    /**
     * Ensure backup directory exists
     */
    private function ensureBackupDirectory()
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
    }
    
    /**
     * Get database configuration
     */
    private function getDatabaseConfig()
    {
        return [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];
    }
    
    /**
     * Build mysqldump command
     */
    private function buildMysqldumpCommand($config, $backupPath, $ownerId = null)
    {
        $command = "mysqldump -h {$config['host']} -P {$config['port']} -u {$config['username']}";
        
        if ($config['password']) {
            $command .= " -p{$config['password']}";
        }
        
        $command .= " {$config['database']}";
        
        if ($ownerId) {
            $command .= " --where=\"owner_id={$ownerId}\"";
        }
        
        $command .= " > {$backupPath}";
        
        return $command;
    }
    
    /**
     * Build mysql restore command
     */
    private function buildMysqlRestoreCommand($config, $backupPath)
    {
        $command = "mysql -h {$config['host']} -P {$config['port']} -u {$config['username']}";
        
        if ($config['password']) {
            $command .= " -p{$config['password']}";
        }
        
        $command .= " {$config['database']} < {$backupPath}";
        
        return $command;
    }
    
    /**
     * Execute backup command
     */
    private function executeBackupCommand($command)
    {
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Database backup command failed');
        }
    }
    
    /**
     * Execute restore command
     */
    private function executeRestoreCommand($command)
    {
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Database restore command failed');
        }
    }
    
    /**
     * Create backup record
     */
    private function createBackupRecord($filename, $type, $ownerId = null, $size = 0, $userId = null)
    {
        return Backup::create([
            'filename' => $filename,
            'type' => $type,
            'owner_id' => $ownerId,
            'size' => $size,
            'status' => 'completed',
            'created_by' => $userId ?? auth()->id() ?? 1,
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