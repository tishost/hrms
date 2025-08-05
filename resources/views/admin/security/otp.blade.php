@extends('layouts.admin')

@section('title', 'OTP Security Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shield-alt"></i> OTP Security Dashboard
        </h1>
        <div>
            <a href="{{ route('admin.security.otp.logs') }}" class="btn btn-info btn-sm">
                <i class="fas fa-list"></i> View All Logs
            </a>
            <a href="{{ route('admin.security.otp.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Export Logs
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
                                Total OTP Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['total_otp_requests']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
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
                                Successful OTPs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['successful_otps']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Suspicious Activities
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['suspicious_activities']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Blocked Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['blocked_requests']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocked Entities -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-ban"></i> Currently Blocked IPs
                    </h6>
                </div>
                <div class="card-body">
                    @if($blockedIps->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blockedIps as $ip)
                                    <tr>
                                        <td>{{ $ip }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success unblock-ip" data-ip="{{ $ip }}">
                                                <i class="fas fa-unlock"></i> Unblock
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No IPs are currently blocked.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-phone"></i> Currently Blocked Phones
                    </h6>
                </div>
                <div class="card-body">
                    @if($blockedPhones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Phone Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blockedPhones as $phone)
                                    <tr>
                                        <td>{{ $phone }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success unblock-phone" data-phone="{{ $phone }}">
                                                <i class="fas fa-unlock"></i> Unblock
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No phones are currently blocked.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Suspicious Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle"></i> Recent Suspicious Activities
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentSuspicious->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Phone</th>
                                        <th>IP Address</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Abuse Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSuspicious as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                        <td>{{ $log->phone }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $log->type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $log->status == 'verified' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $log->abuse_score > 100 ? 'danger' : ($log->abuse_score > 50 ? 'warning' : 'info') }}">
                                                {{ $log->abuse_score }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $recentSuspicious->links() }}
                    @else
                        <p class="text-muted">No suspicious activities found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Unblock IP
    $('.unblock-ip').click(function() {
        const ip = $(this).data('ip');
        if (confirm(`Are you sure you want to unblock IP ${ip}?`)) {
            $.post('{{ route("admin.security.otp.unblock-ip") }}', {
                ip: ip,
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to unblock IP');
                }
            })
            .fail(function() {
                alert('Failed to unblock IP');
            });
        }
    });

    // Unblock Phone
    $('.unblock-phone').click(function() {
        const phone = $(this).data('phone');
        if (confirm(`Are you sure you want to unblock phone ${phone}?`)) {
            $.post('{{ route("admin.security.otp.unblock-phone") }}', {
                phone: phone,
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to unblock phone');
                }
            })
            .fail(function() {
                alert('Failed to unblock phone');
            });
        }
    });
});
</script>
@endsection 