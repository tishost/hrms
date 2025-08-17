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

    <!-- Reset Phone OTP Limit -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sync-alt"></i> Reset Phone OTP Limit
                    </h6>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="reset_phone" class="form-control" placeholder="Enter phone number (e.g., 01XXXXXXXXX)">
                        <button class="btn btn-warning" id="btn-reset-phone"><i class="fas fa-undo"></i> Reset</button>
                    </div>
                    <small class="text-muted">This will clear block and failed-attempt status for the phone so OTP can be tried again.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Phone Attempts Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table"></i> Phone Attempt Summary (Last {{ $hours }}h)
                    </h6>
                    <small class="text-muted">Total: {{ $phoneAttempts->count() }}</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Phone</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center text-danger">Failed</th>
                                    <th class="text-center text-success">Verified</th>
                                    <th class="text-center">Blocked</th>
                                    <th>Blocked Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phoneAttempts as $row)
                                    <tr>
                                        <td>{{ $row->phone }}</td>
                                        <td class="text-center fw-bold">{{ $row->total_attempts }}</td>
                                        <td class="text-center text-danger">{{ $row->failed_attempts }}</td>
                                        <td class="text-center text-success">{{ $row->verified_attempts }}</td>
                                        <td class="text-center">
                                            @if($row->is_blocked)
                                                <span class="badge badge-danger">Yes</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->blocked_until ? \Carbon\Carbon::parse($row->blocked_until)->format('M d, Y H:i') : '—' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-warning reset-phone" data-phone="{{ $row->phone }}">
                                                    <i class="fas fa-undo"></i> Reset
                                                </button>
                                                @if($row->is_blocked)
                                                <button class="btn btn-success unblock-phone" data-phone="{{ $row->phone }}">
                                                    <i class="fas fa-unlock"></i> Unblock
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No attempts found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    // Reset phone limit
    $('#btn-reset-phone').click(function() {
        const phone = $('#reset_phone').val().trim();
        console.log('Reset button clicked for phone:', phone);
        
        if (!phone) {
            alert('Please enter a phone number');
            return;
        }
        
        if (confirm(`Reset OTP limit for ${phone}?`)) {
            console.log('Sending reset request for phone:', phone);
            
            $.post('{{ route("admin.security.otp.reset-phone") }}', {
                phone: phone,
                _token: '{{ csrf_token() }}'
            })
            .done(function(resp) {
                console.log('Reset response received:', resp);
                if (resp.success) {
                    alert(resp.message || 'Reset successful');
                    location.reload();
                } else {
                    alert('Failed to reset: ' + (resp.message || 'Unknown error'));
                }
            })
            .fail(function(xhr){
                console.error('Reset request failed:', xhr);
                console.error('Response text:', xhr.responseText);
                console.error('Status:', xhr.status);
                alert('Request failed: ' + (xhr.responseJSON?.message || 'Server error') + ' (Status: ' + xhr.status + ')');
            });
        }
    });

    // Reset from table - Using event delegation for dynamic content
    $(document).on('click', '.reset-phone', function() {
        const phone = $(this).data('phone');
        console.log('Table reset button clicked for phone:', phone);
        
        if (confirm(`Reset OTP limit for ${phone}?`)) {
            console.log('Sending table reset request for phone:', phone);
            
            $.post('{{ route("admin.security.otp.reset-phone") }}', {
                phone: phone,
                _token: '{{ csrf_token() }}'
            })
            .done(function(resp) {
                console.log('Table reset response received:', resp);
                if (resp.success) {
                    alert(resp.message || 'Reset successful');
                    location.reload();
                } else {
                    alert('Failed to reset: ' + (resp.message || 'Unknown error'));
                }
            })
            .fail(function(xhr) {
                console.error('Table reset request failed:', xhr);
                console.error('Response text:', xhr.responseText);
                console.error('Status:', xhr.status);
                alert('Server error: ' + (xhr.responseJSON?.message || 'Unknown error') + ' (Status: ' + xhr.status + ')');
            });
        }
    });

    // Unblock IP - Using event delegation for dynamic content
    $(document).on('click', '.unblock-ip', function() {
        const ip = $(this).data('ip');
        console.log('Unblock IP button clicked for IP:', ip);
        
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

    // Unblock Phone - Using event delegation for dynamic content
    $(document).on('click', '.unblock-phone', function() {
        const phone = $(this).data('phone');
        console.log('Unblock phone button clicked for phone:', phone);
        
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