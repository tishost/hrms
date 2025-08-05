@extends('layouts.admin')

@section('title', 'OTP Logs')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list"></i> OTP Logs
        </h1>
        <div>
            <a href="{{ route('admin.security.otp') }}" class="btn btn-info btn-sm">
                <i class="fas fa-shield-alt"></i> Security Dashboard
            </a>
            <a href="{{ route('admin.security.otp.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Export Logs
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="password_reset" {{ request('type') == 'password_reset' ? 'selected' : '' }}>Password Reset</option>
                        <option value="registration" {{ request('type') == 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>Login</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">IP Address</label>
                    <input type="text" name="ip" class="form-control" value="{{ request('ip') }}" placeholder="Enter IP">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ request('phone') }}" placeholder="Enter phone">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-check">
                        <input type="checkbox" name="suspicious_only" class="form-check-input" id="suspiciousOnly" value="1" {{ request('suspicious_only') ? 'checked' : '' }}>
                        <label class="form-check-label" for="suspiciousOnly">
                            Show only suspicious activities
                        </label>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.security.otp.logs') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">OTP Logs</h6>
        </div>
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Time</th>
                                <th>Phone</th>
                                <th>OTP</th>
                                <th>Type</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Suspicious</th>
                                <th>Abuse Score</th>
                                <th>Device Info</th>
                                <th>Location</th>
                                <th>Blocked Until</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td>{{ $log->phone }}</td>
                                <td>{{ $log->otp }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $log->type }}</span>
                                </td>
                                <td>{{ $log->ip_address }}</td>
                                <td>
                                    <span class="badge badge-{{ $log->status == 'verified' ? 'success' : ($log->status == 'failed' ? 'danger' : ($log->status == 'blocked' ? 'dark' : 'warning')) }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->is_suspicious)
                                        <span class="badge badge-danger">Yes</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $log->abuse_score > 100 ? 'danger' : ($log->abuse_score > 50 ? 'warning' : 'info') }}">
                                        {{ $log->abuse_score }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->device_info)
                                        @php $deviceInfo = json_decode($log->device_info, true); @endphp
                                        <small>
                                            {{ $deviceInfo['browser'] ?? 'Unknown' }} on {{ $deviceInfo['os'] ?? 'Unknown' }}
                                            @if($deviceInfo['is_mobile'] ?? false)
                                                <span class="badge badge-secondary">Mobile</span>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Unknown</small>
                                    @endif
                                </td>
                                <td>{{ $log->location ?? 'Unknown' }}</td>
                                <td>
                                    @if($log->blocked_until)
                                        {{ $log->blocked_until->format('M d, Y H:i:s') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->reason)
                                        <small>{{ $log->reason }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No OTP logs found</h5>
                    <p class="text-gray-400">No logs match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 