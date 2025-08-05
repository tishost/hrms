@extends('layouts.admin')

@section('title', 'Login Log Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sign-in-alt"></i> Login Log Details
        </h1>
        <a href="{{ route('admin.login-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <!-- Login Log Details -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Login Log #{{ $loginLog->id }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Basic Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>User:</strong></td>
                                    <td>
                                        @if($loginLog->user)
                                            <a href="{{ route('admin.login-logs.user-history', $loginLog->user) }}">
                                                {{ $loginLog->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $loginLog->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>IP Address:</strong></td>
                                    <td>
                                        <code>{{ $loginLog->ip_address }}</code>
                                        @if($loginLog->is_suspicious)
                                            <span class="badge bg-warning">Suspicious</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $loginLog->status_badge_class }}">
                                            {{ ucfirst($loginLog->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Login Method:</strong></td>
                                    <td>{{ ucfirst($loginLog->login_method) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Login At:</strong></td>
                                    <td>{{ $loginLog->login_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @if($loginLog->logout_at)
                                <tr>
                                    <td><strong>Logout At:</strong></td>
                                    <td>{{ $loginLog->logout_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Session Duration:</strong></td>
                                    <td>{{ $loginLog->session_duration }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Device Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Device Type:</strong></td>
                                    <td>
                                        <span class="badge {{ $loginLog->device_type_badge_class }}">
                                            {{ ucfirst($loginLog->device_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Platform:</strong></td>
                                    <td>{{ ucfirst($loginLog->platform) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Browser:</strong></td>
                                    <td>{{ $loginLog->browser ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Browser Version:</strong></td>
                                    <td>{{ $loginLog->browser_version ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Operating System:</strong></td>
                                    <td>{{ $loginLog->os ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>OS Version:</strong></td>
                                    <td>{{ $loginLog->os_version ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Device Model:</strong></td>
                                    <td>{{ $loginLog->device_model ?? 'Unknown' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($loginLog->location_string != 'Unknown Location')
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Location Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>{{ $loginLog->location_string }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $loginLog->city ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>State:</strong></td>
                                    <td>{{ $loginLog->state ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Country:</strong></td>
                                    <td>{{ $loginLog->country ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Timezone:</strong></td>
                                    <td>{{ $loginLog->timezone ?? 'Unknown' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($loginLog->failure_reason)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-danger">Failure Reason</h6>
                            <div class="alert alert-danger">
                                {{ $loginLog->failure_reason }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($loginLog->additional_data)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Additional Data</h6>
                            <pre class="bg-light p-3 rounded">{{ json_encode($loginLog->additional_data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif

                    @if($loginLog->user_agent)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold">User Agent</h6>
                            <code class="d-block bg-light p-3 rounded">{{ $loginLog->user_agent }}</code>
                        </div>
                    </div>
                    @endif

                    @if($loginLog->status == 'failed' && $loginLog->failed_attempts > 5)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle"></i> Suspicious Activity Detected
                                </h6>
                                <p class="mb-0">
                                    This IP address has {{ $loginLog->failed_attempts }} failed login attempts in the last hour.
                                    Consider blocking this IP address.
                                </p>
                                <hr>
                                <button type="button" class="btn btn-warning btn-sm" 
                                        onclick="blockIp('{{ $loginLog->ip_address }}')">
                                    <i class="fas fa-ban"></i> Block IP Address
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
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
</script>
@endsection 