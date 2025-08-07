@extends('layouts.admin')

@section('title', 'Notification Logs')

@section('content')
<style>
    /* Custom styling for notification logs */
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 500;
    }
    
    .badge-info {
        background-color: #17a2b8 !important;
        color: #fff !important;
    }
    
    .badge-success {
        background-color: #28a745 !important;
        color: #fff !important;
    }
    
    .badge-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
    }
    
    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    
    /* Table styling */
    .table th {
        background-color: #343a40;
        color: #fff;
        border-color: #454d55;
    }
    
    .table td {
        vertical-align: middle;
        color: #333 !important;
        background-color: #fff !important;
    }
    
    /* Force text color for all table cells */
    .table tbody tr td {
        color: #333 !important;
        background-color: #fff !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    
    /* Ensure text visibility */
    .table td * {
        color: inherit !important;
    }
    
    /* Specific styling for Type column */
    .table td:nth-child(2) {
        color: #333 !important;
    }
    
    .table td:nth-child(2) * {
        color: #333 !important;
    }
    
    .table td:nth-child(2) .badge {
        color: #fff !important;
    }
    
    /* Text color fixes */
    .text-break {
        color: #333 !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    /* Button styling */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
    }
    
    /* Modal styling */
    .modal-content {
        border-radius: 0.5rem;
    }
    
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                        <li class="breadcrumb-item active">Notification Logs</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="fas fa-history"></i> Notification Logs
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list"></i> Notification History
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-success" onclick="location.reload()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="filter-type" class="form-label">Type</label>
                            <select class="form-select" id="filter-type">
                                <option value="">All Types</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-status" class="form-label">Status</label>
                            <select class="form-select" id="filter-status">
                                <option value="">All Status</option>
                                <option value="sent">Sent</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="filter-date">
                        </div>
                        <div class="col-md-3">
                            <label for="search-recipient" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search-recipient" placeholder="Email or phone...">
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Recipient</th>
                                    <th>Subject/Content</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notificationLogs ?? [] as $log)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $log->created_at->format('M d, Y') }}</span>
                                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td style="color: #333 !important;">
                                        <span class="badge badge-{{ $log->type === 'email' ? 'info' : 'success' }}" style="display: inline-block; min-width: 80px; text-align: center; color: #fff !important;">
                                            <i class="fas fa-{{ $log->type === 'email' ? 'envelope' : 'mobile-alt' }} me-1"></i>
                                            {{ ucfirst($log->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-{{ $log->type === 'email' ? 'envelope' : 'mobile-alt' }} me-2 text-muted"></i>
                                            <span class="text-break">{{ $log->recipient }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-break">
                                            @if($log->type === 'email')
                                                <strong>{{ $log->subject ?? 'No Subject' }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($log->content, 100) }}</small>
                                            @else
                                                {{ Str::limit($log->content, 150) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $log->status === 'sent' ? 'success' : 'danger' }}" style="display: inline-block; min-width: 70px; text-align: center;">
                                            <i class="fas fa-{{ $log->status === 'sent' ? 'check' : 'times' }} me-1"></i>
                                            {{ ucfirst($log->status) }}
                                        </span>
                                        @if($log->status === 'failed' && $log->error_message)
                                            <br><small class="text-danger" style="font-size: 0.75rem;">{{ Str::limit($log->error_message, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info" onclick="viewLogDetails({{ $log->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($log->status === 'failed')
                                                <button class="btn btn-sm btn-warning" onclick="retryNotification({{ $log->id }})" title="Retry">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5>No notification logs found</h5>
                                            <p>No notifications have been sent yet.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($notificationLogs) && $notificationLogs->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notificationLogs->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailsModalLabel">
                    <i class="fas fa-info-circle"></i> Notification Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // View log details
    function viewLogDetails(logId) {
        // For now, just show a simple message
        document.getElementById('logDetailsContent').innerHTML = `
            <div class="text-center">
                <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                <h5>Notification Details</h5>
                <p>Detailed view for notification log ID: ${logId}</p>
                <p class="text-muted">This feature will be implemented to show full notification details.</p>
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
        modal.show();
    }

    // Retry notification
    function retryNotification(logId) {
        if (confirm('Are you sure you want to retry this notification?')) {
            // For now, just show a message
            alert('Retry functionality will be implemented to resend failed notifications.');
        }
    }
</script>
@endpush
