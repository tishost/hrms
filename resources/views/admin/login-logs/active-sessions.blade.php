@extends('layouts.admin')

@section('title', 'Active Sessions')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> Active Sessions
        </h1>
        <a href="{{ route('admin.login-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <!-- Active Sessions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Currently Active Sessions ({{ $sessions->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="activeSessionsTable">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                        <th>Platform</th>
                                        <th>Location</th>
                                        <th>Login Time</th>
                                        <th>Session Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    @if($session->user)
                                                        <div class="avatar-initial rounded-circle bg-primary">
                                                            {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                                        </div>
                                                    @else
                                                        <div class="avatar-initial rounded-circle bg-secondary">
                                                            ?
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($session->user)
                                                        <strong>{{ $session->user->name }}</strong>
                                                        <br><small class="text-muted">{{ $session->user->email }}</small>
                                                    @else
                                                        <span class="text-muted">Unknown User</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $session->ip_address }}</code>
                                            @if($session->is_suspicious)
                                                <span class="badge bg-warning">Suspicious</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $session->device_type_badge_class }}">
                                                {{ ucfirst($session->device_type) }}
                                            </span>
                                            @if($session->device_model)
                                                <br><small class="text-muted">{{ $session->device_model }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($session->platform) }}</span>
                                            @if($session->browser)
                                                <br><small class="text-muted">{{ $session->browser }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $session->location_string }}</td>
                                        <td>{{ $session->login_at->format('M d, Y H:i:s') }}</td>
                                        <td>
                                            <span class="text-success">
                                                {{ $session->session_duration }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.login-logs.show', $session) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($session->user)
                                                <a href="{{ route('admin.login-logs.user-history', $session->user) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                                @endif
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                        onclick="blockIp('{{ $session->ip_address }}')"
                                                        title="Block IP Address">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Active Sessions</h5>
                            <p class="text-muted">There are currently no active user sessions.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Session Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Session Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ $sessions->count() }}</h4>
                                <p class="text-muted mb-0">Active Sessions</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $sessions->where('device_type', 'web')->count() }}</h4>
                                <p class="text-muted mb-0">Web Sessions</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $sessions->where('device_type', 'mobile')->count() }}</h4>
                                <p class="text-muted mb-0">Mobile Sessions</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">{{ $sessions->where('is_suspicious', true)->count() }}</h4>
                                <p class="text-muted mb-0">Suspicious Sessions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Block IP Modal -->
<div class="modal fade" id="blockIpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block IP Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blockIpForm" method="POST" action="{{ route('admin.login-logs.block-ip') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="block_ip_address">IP Address</label>
                        <input type="text" name="ip_address" id="block_ip_address" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="block_reason">Reason (Optional)</label>
                        <textarea name="reason" id="block_reason" class="form-control" rows="3" 
                                  placeholder="Enter reason for blocking this IP"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Block IP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function blockIp(ipAddress) {
    document.getElementById('block_ip_address').value = ipAddress;
    const modal = new bootstrap.Modal(document.getElementById('blockIpModal'));
    modal.show();
}

// Auto-refresh every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection 