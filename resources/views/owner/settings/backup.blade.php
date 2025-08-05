@extends('layouts.owner')

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
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
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

        <div class="col-xl-4 col-md-6 mb-4">
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

        <div class="col-xl-4 col-md-6 mb-4">
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
    </div>

    <!-- Backup Settings Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Backup Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.settings.backup.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Automatic Backup Settings -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="owner_backup_enabled" 
                                       name="owner_backup_enabled" 
                                       {{ ($settings['owner_backup_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="owner_backup_enabled">
                                    <strong>Enable Automatic Backups</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">Automatically create backups of your data</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_backup_frequency">Backup Frequency</label>
                                    <select name="owner_backup_frequency" id="owner_backup_frequency" class="form-control">
                                        <option value="daily" {{ ($settings['owner_backup_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ ($settings['owner_backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ ($settings['owner_backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_backup_retention_days">Retention Period (Days)</label>
                                    <input type="number" name="owner_backup_retention_days" id="owner_backup_retention_days" 
                                           class="form-control" value="{{ $settings['owner_backup_retention_days'] ?? 30 }}" 
                                           min="1" max="90">
                                    <small class="form-text text-muted">Maximum 90 days for owner backups</small>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="form-group">
                            <label for="owner_backup_notification_email">Notification Email</label>
                            <input type="email" name="owner_backup_notification_email" id="owner_backup_notification_email" 
                                   class="form-control" value="{{ $settings['owner_backup_notification_email'] ?? '' }}" 
                                   placeholder="your-email@example.com">
                            <small class="form-text text-muted">Email address for backup notifications (optional)</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
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
                        <button type="button" class="btn btn-success btn-block" onclick="testBackup()">
                            <i class="fas fa-play"></i> Test Backup
                        </button>
                        <a href="{{ route('owner.backups.index') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-list"></i> View My Backups
                        </a>
                    </div>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Backup Information</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> What gets backed up?</h6>
                        <ul class="mb-0">
                            <li>Your property data</li>
                            <li>Tenant information</li>
                            <li>Rent payment records</li>
                            <li>Invoice history</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Important Notes</h6>
                        <ul class="mb-0">
                            <li>Backups are stored securely</li>
                            <li>You can only access your own backups</li>
                            <li>Maximum retention: 90 days</li>
                            <li>Test backups before relying on them</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function testBackup() {
    $.post('{{ route("owner.settings.backup.test") }}', {
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

function refreshStats() {
    $.get('{{ route("owner.settings.backup.stats") }}').done(function(response) {
        if (response.success) {
            $('#totalBackups').text(response.stats.total);
            $('#completedBackups').text(response.stats.completed);
            $('#totalSize').text(response.stats.total_size);
        }
    });
}
</script>
@endpush 