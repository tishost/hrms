@extends('layouts.admin')

@section('title', 'User Login History')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history"></i> Login History for {{ $user->name }}
        </h1>
        <a href="{{ route('admin.login-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <!-- User Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $user->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>
                                        @php
                                            $lastLogin = $logs->where('status', 'success')->first();
                                        @endphp
                                        @if($lastLogin)
                                            {{ $lastLogin->login_at->format('M d, Y H:i:s') }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Logins:</strong></td>
                                    <td>{{ $logs->where('status', 'success')->count() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Login History (Last 50 Logins)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="userHistoryTable">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Platform</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Session Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->login_at->format('M d, Y H:i:s') }}</td>
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
                                            <br><small class="text-muted">{{ Str::limit($log->failure_reason, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->logout_at)
                                            {{ $log->session_duration }}
                                        @else
                                            <span class="text-muted">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.login-logs.show', $log) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Login Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ $logs->where('status', 'success')->count() }}</h4>
                                <p class="text-muted mb-0">Successful Logins</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">{{ $logs->where('status', 'failed')->count() }}</h4>
                                <p class="text-muted mb-0">Failed Logins</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $logs->where('device_type', 'web')->count() }}</h4>
                                <p class="text-muted mb-0">Web Logins</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $logs->where('device_type', 'mobile')->count() }}</h4>
                                <p class="text-muted mb-0">Mobile Logins</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 