@extends('layouts.admin')

@section('title', 'Notification Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Notification Log
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.notifications') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Recipient</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                            <td>
                                                @if($log->type === 'email')
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-envelope"></i> Email
                                                    </span>
                                                @elseif($log->type === 'sms')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-sms"></i> SMS
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-bell"></i> {{ ucfirst($log->type) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $log->recipient }}</td>
                                            <td>
                                                @if($log->status === 'success')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Success
                                                    </span>
                                                @elseif($log->status === 'failed')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times"></i> Failed
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewLogDetails({{ $log->id }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $logs->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No notification logs found</h5>
                            <p class="text-muted">Notification logs will appear here once notifications are sent.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailsModalLabel">Notification Details</h5>
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
function viewLogDetails(logId) {
    // Show loading
    $('#logDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#logDetailsModal').modal('show');
    
    // Fetch log details via AJAX
    $.ajax({
        url: '{{ route("admin.notifications.log.details") }}',
        method: 'GET',
        data: { id: logId },
        success: function(response) {
            if (response.success) {
                const log = response.log;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Date & Time:</strong><br>
                            ${log.created_at}
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong><br>
                            ${log.type}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Recipient:</strong><br>
                            ${log.recipient}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge ${log.status === 'success' ? 'bg-success' : log.status === 'failed' ? 'bg-danger' : 'bg-warning'}">
                                ${log.status}
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <strong>Content:</strong><br>
                            <div class="mt-2 p-3 bg-light rounded">
                                <pre style="white-space: pre-wrap; word-wrap: break-word;">${log.content}</pre>
                            </div>
                        </div>
                    </div>
                `;
                $('#logDetailsContent').html(content);
            } else {
                $('#logDetailsContent').html('<div class="alert alert-danger">Failed to load log details.</div>');
            }
        },
        error: function() {
            $('#logDetailsContent').html('<div class="alert alert-danger">Failed to load log details.</div>');
        }
    });
}
</script>
@endpush 