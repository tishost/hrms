@extends('layouts.admin')

@section('title', 'Login Logs')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sign-in-alt"></i> Login Logs
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="exportLogs()">
                <i class="fas fa-download"></i> Export CSV
            </button>
            <a href="{{ route('admin.login-logs.active-sessions') }}" class="btn btn-info">
                <i class="fas fa-users"></i> Active Sessions
            </a>
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
                                Today's Logins
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['today']['total'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Successful Logins
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['today']['successful'] }}
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
                                Failed Logins
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['today']['failed'] }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Blocked IPs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['today']['blocked'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('admin.login-logs.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="device_type">Device Type</label>
                            <select name="device_type" id="device_type" class="form-control">
                                <option value="">All Devices</option>
                                <option value="web" {{ request('device_type') == 'web' ? 'selected' : '' }}>Web</option>
                                <option value="mobile" {{ request('device_type') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                <option value="tablet" {{ request('device_type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="platform">Platform</label>
                            <select name="platform" id="platform" class="form-control">
                                <option value="">All Platforms</option>
                                <option value="web" {{ request('platform') == 'web' ? 'selected' : '' }}>Web</option>
                                <option value="ios" {{ request('platform') == 'ios' ? 'selected' : '' }}>iOS</option>
                                <option value="android" {{ request('platform') == 'android' ? 'selected' : '' }}>Android</option>
                                <option value="desktop" {{ request('platform') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ip_address">IP Address</label>
                            <input type="text" name="ip_address" id="ip_address" class="form-control" 
                                   value="{{ request('ip_address') }}" placeholder="Enter IP">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="{{ request('email') }}" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.login-logs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Suspicious IPs Alert -->
    @if($suspiciousIps->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Warning:</strong> {{ $suspiciousIps->count() }} IP address(es) with suspicious activity detected.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Login Logs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Login Logs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="loginLogsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Platform</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Login At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->user)
                                    <a href="{{ route('admin.login-logs.user-history', $log->user) }}">
                                        {{ $log->user->name }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $log->email ?? 'N/A' }}</td>
                            <td>
                                <code>{{ $log->ip_address }}</code>
                                @if($log->is_suspicious)
                                    <span class="badge bg-warning">Suspicious</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $log->device_type_badge_class }}">
                                    {{ ucfirst($log->device_type) }}
                                </span>
                                @if($log->device_model)
                                    <br><small class="text-muted">{{ $log->device_model }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($log->platform) }}</span>
                                @if($log->browser)
                                    <br><small class="text-muted">{{ $log->browser }}</small>
                                @endif
                            </td>
                            <td>{{ $log->location_string }}</td>
                            <td>
                                <span class="badge {{ $log->status_badge_class }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                                @if($log->failure_reason)
                                    <br><small class="text-muted">{{ $log->failure_reason }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $log->login_at->format('M d, Y H:i:s') }}
                                @if($log->logout_at)
                                    <br><small class="text-muted">
                                        Duration: {{ $log->session_duration }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.login-logs.show', $log) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($log->status == 'failed' && $log->failed_attempts > 5)
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                onclick="blockIp('{{ $log->ip_address }}')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $logs->appends(request()->query())->links() }}
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
function exportLogs() {
    // Get current filters
    const url = new URL(window.location);
    const params = new URLSearchParams(url.search);
    params.append('export', '1');
    
    window.location.href = '{{ route("admin.login-logs.export") }}?' + params.toString();
}

function blockIp(ipAddress) {
    document.getElementById('block_ip_address').value = ipAddress;
    const modal = new bootstrap.Modal(document.getElementById('blockIpModal'));
    modal.show();
}

// Real-time updates
setInterval(function() {
    fetch('{{ route("admin.login-logs.real-time-stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update statistics if needed
            console.log('Real-time stats updated:', data);
        })
        .catch(error => {
            console.error('Error updating real-time stats:', error);
        });
}, 30000);
</script>
@endsection 