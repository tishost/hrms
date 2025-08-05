@extends('layouts.admin')

@section('title', 'Backup Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog"></i> Backup Settings
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="testBackup()">
                <i class="fas fa-play"></i> Test Backup
            </button>
            <button type="button" class="btn btn-warning" onclick="cleanOldBackups()">
                <i class="fas fa-broom"></i> Clean Old
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Backups
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBackups">
                                {{ $backupStats['total'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedBackups">
                                {{ $backupStats['completed'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Size
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSize">
                                {{ $backupStats['total_size'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Auto Backup
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="autoBackupStatus">
                                {{ $settings['backup_auto_enabled'] ?? false ? 'Enabled' : 'Disabled' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Settings Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Backup Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.backup.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Automatic Backup Settings -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="backup_auto_enabled" name="backup_auto_enabled" 
                                       {{ ($settings['backup_auto_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="backup_auto_enabled">
                                    <strong>Enable Automatic Backups</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">Automatically create backups based on schedule</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="backup_frequency">Backup Frequency</label>
                                    <select name="backup_frequency" id="backup_frequency" class="form-control">
                                        <option value="daily" {{ ($settings['backup_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ ($settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ ($settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="backup_retention_days">Retention Period (Days)</label>
                                    <input type="number" name="backup_retention_days" id="backup_retention_days" 
                                           class="form-control" value="{{ $settings['backup_retention_days'] ?? 30 }}" 
                                           min="1" max="365">
                                </div>
                            </div>
                        </div>

                        <!-- Backup Content Settings -->
                        <div class="form-group">
                            <label><strong>Backup Content</strong></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="backup_include_database" 
                                               name="backup_include_database" 
                                               {{ ($settings['backup_include_database'] ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="backup_include_database">
                                            Include Database
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="backup_include_files" 
                                               name="backup_include_files" 
                                               {{ ($settings['backup_include_files'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="backup_include_files">
                                            Include Files
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Backup Options -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="backup_compression" 
                                               name="backup_compression" 
                                               {{ ($settings['backup_compression'] ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="backup_compression">
                                            Enable Compression
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Compress backup files to save space</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="backup_max_size_mb">Maximum Backup Size (MB)</label>
                                    <input type="number" name="backup_max_size_mb" id="backup_max_size_mb" 
                                           class="form-control" value="{{ $settings['backup_max_size_mb'] ?? 100 }}" 
                                           min="1" max="1000">
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="form-group">
                            <label for="backup_notification_email">Notification Email</label>
                            <input type="email" name="backup_notification_email" id="backup_notification_email" 
                                   class="form-control" value="{{ $settings['backup_notification_email'] ?? '' }}" 
                                   placeholder="admin@example.com">
                            <small class="form-text text-muted">Email address for backup notifications</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Schedule Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Backup Schedule</h6>
                </div>
                <div class="card-body">
                    <form id="scheduleForm">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="schedule_enabled" 
                                       {{ ($settings['backup_schedule_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="schedule_enabled">
                                    Enable Scheduled Backups
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="schedule_frequency">Schedule Frequency</label>
                            <select id="schedule_frequency" class="form-control">
                                <option value="daily" {{ ($settings['backup_schedule_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ ($settings['backup_schedule_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ ($settings['backup_schedule_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="schedule_time">Schedule Time</label>
                            <input type="time" id="schedule_time" class="form-control" 
                                   value="{{ $settings['backup_schedule_time'] ?? '02:00' }}">
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-clock"></i> Update Schedule
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-info btn-block" onclick="refreshStats()">
                            <i class="fas fa-sync"></i> Refresh Stats
                        </button>
                        <button type="button" class="btn btn-warning btn-block" onclick="cleanOldBackups()">
                            <i class="fas fa-broom"></i> Clean Old Backups
                        </button>
                        <button type="button" class="btn btn-success btn-block" onclick="testBackup()">
                            <i class="fas fa-play"></i> Test Backup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clean Old Backups Modal -->
<div class="modal fade" id="cleanOldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clean Old Backups</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will permanently delete backups older than the specified number of days.
                </div>
                <div class="form-group">
                    <label for="cleanDays">Delete backups older than (days)</label>
                    <input type="number" id="cleanDays" class="form-control" value="30" min="1" max="365">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="confirmCleanOld()">Clean Old Backups</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle schedule form submission
    $('#scheduleForm').submit(function(e) {
        e.preventDefault();
        updateSchedule();
    });
});

function testBackup() {
    $.post('{{ route("admin.settings.backup.test") }}', {
        _token: '{{ csrf_token() }}'
    }).done(function(response) {
        if (response.success) {
            alert('Test backup created successfully!\nBackup ID: ' + response.backup_id + '\nFilename: ' + response.filename + '\nSize: ' + response.size);
            refreshStats();
        } else {
            alert('Test backup failed: ' + response.message);
        }
    }).fail(function(xhr) {
        alert('Test backup failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function cleanOldBackups() {
    $('#cleanOldModal').modal('show');
}

function confirmCleanOld() {
    const days = $('#cleanDays').val();
    
    $.post('{{ route("admin.settings.backup.clean") }}', {
        _token: '{{ csrf_token() }}',
        days: days
    }).done(function(response) {
        if (response.success) {
            alert(response.message);
            refreshStats();
        } else {
            alert('Clean failed: ' + response.message);
        }
    }).fail(function(xhr) {
        alert('Clean failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
    
    $('#cleanOldModal').modal('hide');
}

function refreshStats() {
    $.get('{{ route("admin.settings.backup.stats") }}').done(function(response) {
        if (response.success) {
            $('#totalBackups').text(response.stats.total);
            $('#completedBackups').text(response.stats.completed);
            $('#totalSize').text(response.stats.total_size);
        }
    });
}

function updateSchedule() {
    const data = {
        _token: '{{ csrf_token() }}',
        frequency: $('#schedule_frequency').val(),
        time: $('#schedule_time').val(),
        enabled: $('#schedule_enabled').is(':checked')
    };
    
    $.post('{{ route("admin.settings.backup.schedule") }}', data).done(function(response) {
        if (response.success) {
            alert('Schedule updated successfully!');
        } else {
            alert('Failed to update schedule: ' + response.message);
        }
    }).fail(function(xhr) {
        alert('Failed to update schedule: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}
</script>
@endpush 