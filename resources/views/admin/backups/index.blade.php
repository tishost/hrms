@extends('layouts.admin')

@section('title', 'Backup Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-database"></i> Backup Management
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createBackupModal">
                <i class="fas fa-plus"></i> Create Backup
            </button>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#cleanOldModal">
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total'] }}
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['completed'] }}
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Failed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['failed'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_size'] }}
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

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.backups.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="full" {{ request('type') == 'full' ? 'selected' : '' }}>Full System</option>
                                <option value="owner" {{ request('type') == 'owner' ? 'selected' : '' }}>Owner Data</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System Files</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="owner_id">Owner</label>
                            <select name="owner_id" id="owner_id" class="form-control">
                                <option value="">All Owners</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.backups.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Backups</h6>
        </div>
        <div class="card-body">
            @if($backups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="backupsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Filename</th>
                                <th>Type</th>
                                <th>Owner</th>
                                <th>Size</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>{{ $backup->id }}</td>
                                    <td>{{ $backup->filename }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $backup->type_label }}</span>
                                    </td>
                                    <td>{{ $backup->owner->name ?? 'N/A' }}</td>
                                    <td>{{ $backup->formatted_size }}</td>
                                    <td>
                                        <span class="badge {{ $backup->status_badge_class }}">
                                            {{ ucfirst($backup->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $backup->creator->name ?? 'Unknown' }}</td>
                                    <td>{{ $backup->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewBackup({{ $backup->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($backup->file_exists)
                                                <a href="{{ route('admin.backups.download', $backup) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="restoreBackup({{ $backup->id }})">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteBackup({{ $backup->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $backups->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-database fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No backups found</h5>
                    <p class="text-gray-400">Create your first backup to get started.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Backup</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.backups.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="backup_type">Backup Type</label>
                        <select name="type" id="backup_type" class="form-control" required>
                            <option value="full">Full System Backup</option>
                            <option value="owner">Owner Data Backup</option>
                        </select>
                    </div>
                    <div class="form-group" id="owner_select_group" style="display: none;">
                        <label for="owner_id">Select Owner</label>
                        <select name="owner_id" id="owner_id_select" class="form-control">
                            <option value="">Select Owner</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any notes about this backup..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Backup</button>
                </div>
            </form>
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
            <form action="{{ route('admin.backups.clean-old') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This will permanently delete backups older than the specified number of days.
                    </div>
                    <div class="form-group">
                        <label for="days">Delete backups older than (days)</label>
                        <input type="number" name="days" id="days" class="form-control" value="30" min="1" max="365" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Clean Old Backups</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Backup Details Modal -->
<div class="modal fade" id="backupDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Backup Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="backupDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#backupsTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]]
    });

    // Show/hide owner select based on backup type
    $('#backup_type').change(function() {
        if ($(this).val() === 'owner') {
            $('#owner_select_group').show();
            $('#owner_id_select').prop('required', true);
        } else {
            $('#owner_select_group').hide();
            $('#owner_id_select').prop('required', false);
        }
    });
});

function viewBackup(backupId) {
    $.get(`/admin/backups/${backupId}/details`, function(data) {
        let content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Basic Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                        <tr><td><strong>Filename:</strong></td><td>${data.filename}</td></tr>
                        <tr><td><strong>Type:</strong></td><td>${data.type}</td></tr>
                        <tr><td><strong>Size:</strong></td><td>${data.size}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>${data.status}</td></tr>
                        <tr><td><strong>Created At:</strong></td><td>${data.created_at}</td></tr>
                        <tr><td><strong>Created By:</strong></td><td>${data.created_by}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Additional Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Owner:</strong></td><td>${data.owner}</td></tr>
                        <tr><td><strong>Notes:</strong></td><td>${data.notes || 'N/A'}</td></tr>
                        <tr><td><strong>File Exists:</strong></td><td>${data.file_exists ? 'Yes' : 'No'}</td></tr>
                        <tr><td><strong>Is Restored:</strong></td><td>${data.is_restored ? 'Yes' : 'No'}</td></tr>
                        ${data.is_restored ? `<tr><td><strong>Restored At:</strong></td><td>${data.restored_at}</td></tr>` : ''}
                        ${data.is_restored ? `<tr><td><strong>Restored By:</strong></td><td>${data.restored_by}</td></tr>` : ''}
                    </table>
                </div>
            </div>
        `;
        $('#backupDetailsContent').html(content);
        $('#backupDetailsModal').modal('show');
    });
}

function restoreBackup(backupId) {
    if (confirm('Are you sure you want to restore this backup? This will overwrite current data.')) {
        $.post(`/admin/backups/${backupId}/restore`, {
            _token: '{{ csrf_token() }}'
        }).done(function() {
            location.reload();
        }).fail(function(xhr) {
            alert('Restore failed: ' + xhr.responseJSON?.message || 'Unknown error');
        });
    }
}

function deleteBackup(backupId) {
    if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/backups/${backupId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            }
        }).done(function() {
            location.reload();
        }).fail(function(xhr) {
            alert('Delete failed: ' + xhr.responseJSON?.message || 'Unknown error');
        });
    }
}
</script>
@endpush 