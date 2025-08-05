<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--type=full} {--owner=}';
    protected $description = 'Create database backup for admin or specific owner';

    public function handle()
    {
        $type = $this->option('type');
        $ownerId = $this->option('owner');
        
        $this->info('Starting database backup...');
        
        try {
            if ($type === 'owner' && $ownerId) {
                $this->createOwnerBackup($ownerId);
            } else {
                $this->createFullBackup();
            }
            
            $this->info('Backup completed successfully!');
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function createFullBackup()
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_full_{$timestamp}.sql";
        $backupPath = storage_path("app/backups/{$filename}");
        
        // Create backups directory if it doesn't exist
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Get database configuration
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        // Create mysqldump command
        $command = "mysqldump -h {$host} -P {$port} -u {$username}";
        if ($password) {
            $command .= " -p{$password}";
        }
        $command .= " {$database} > {$backupPath}";
        
        // Execute backup command
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Database backup command failed');
        }
        
        // Create backup record
        $this->createBackupRecord($filename, 'full', null, filesize($backupPath));
        
        $this->info("Full backup created: {$filename}");
    }
    
    private function createOwnerBackup($ownerId)
    {
        $owner = \App\Models\Owner::find($ownerId);
        if (!$owner) {
            throw new \Exception('Owner not found');
        }
        
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_owner_{$ownerId}_{$timestamp}.sql";
        $backupPath = storage_path("app/backups/{$filename}");
        
        // Create backups directory if it doesn't exist
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Get database configuration
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        // Create mysqldump command for owner-specific data
        $command = "mysqldump -h {$host} -P {$port} -u {$username}";
        if ($password) {
            $command .= " -p{$password}";
        }
        $command .= " {$database} --where=\"owner_id={$ownerId}\" > {$backupPath}";
        
        // Execute backup command
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Owner backup command failed');
        }
        
        // Create backup record
        $this->createBackupRecord($filename, 'owner', $ownerId, filesize($backupPath));
        
        $this->info("Owner backup created: {$filename}");
    }
    
    private function createBackupRecord($filename, $type, $ownerId = null, $size = 0)
    {
        \App\Models\Backup::create([
            'filename' => $filename,
            'type' => $type,
            'owner_id' => $ownerId,
            'size' => $size,
            'status' => 'completed',
            'created_by' => auth()->id() ?? 1,
        ]);
    }
} 