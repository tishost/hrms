@extends('layouts.admin')

@section('title', 'Owner Details - ' . $owner->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user me-2"></i>Owner Details
        </h1>
        <div>
            <a href="{{ route('admin.owners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Owners
            </a>
            <button class="btn btn-primary" onclick="sendTestNotification()">
                <i class="fas fa-bell me-2"></i>Send Test Notification
            </button>
        </div>
    </div>

    <!-- Owner Basic Information -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Owner Information
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $owner->name }}</div>
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-envelope me-1"></i>{{ $owner->email }}<br>
                                <i class="fas fa-phone me-1"></i>{{ $owner->phone }}<br>
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $owner->address }}, {{ $owner->country }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Status -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Subscription Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $owner->subscription->plan->name ?? 'No Plan' }}
                            </div>
                            <div class="text-sm text-gray-600">
                                @if($owner->subscription)
                                <i class="fas fa-calendar me-1"></i>Expires: {{ $owner->subscription->end_date ? $owner->subscription->end_date->format('M d, Y') : 'N/A' }}<br>
                                <i class="fas fa-credit-card me-1"></i>Price: ৳{{ number_format($owner->subscription->plan->price ?? 0, 2) }}<br>
                                <i class="fas fa-sms me-1"></i>SMS Credits: {{ $owner->subscription->sms_credits ?? 0 }}
                                @else
                                <span class="text-warning">No active subscription</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crown fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS Statistics -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                SMS Statistics
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['total_sent'] }} Sent
                            </div>
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-check me-1"></i>{{ $smsStats['successful'] }} Successful<br>
                                <i class="fas fa-times me-1"></i>{{ $smsStats['failed'] }} Failed<br>
                                <i class="fas fa-percentage me-1"></i>{{ $smsStats['total_sent'] > 0 ? round(($smsStats['successful'] / $smsStats['total_sent']) * 100, 1) : 0 }}% Success Rate
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sms fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <ul class="nav nav-tabs card-header-tabs" id="ownerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties" type="button" role="tab">
                                <i class="fas fa-building me-2"></i>Properties ({{ $owner->properties->count() ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tenants-tab" data-bs-toggle="tab" data-bs-target="#tenants" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>Tenants ({{ $owner->tenants->count() ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subscription-tab" data-bs-toggle="tab" data-bs-target="#subscription" type="button" role="tab">
                                <i class="fas fa-crown me-2"></i>Subscription
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing" type="button" role="tab">
                                <i class="fas fa-file-invoice me-2"></i>Billing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="ownerTabsContent">
                        <!-- Properties Tab -->
                        <div class="tab-pane fade show active" id="properties" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Property Name</th>
                                            <th>Address</th>
                                            <th>Units</th>
                                            <th>Occupied Units</th>
                                            <th>Total Rent</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($owner->properties ?? [] as $property)
                                        <tr>
                                            <td>{{ $property->name }}</td>
                                            <td>{{ $property->address }}</td>
                                            <td>{{ $property->units->count() }}</td>
                                            <td>{{ $property->units->where('status', 'occupied')->count() }}</td>
                                            <td>৳{{ number_format($property->units->sum('rent'), 2) }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No properties found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tenants Tab -->
                        <div class="tab-pane fade" id="tenants" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tenant Name</th>
                                            <th>Property</th>
                                            <th>Unit</th>
                                            <th>Rent Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($owner->tenants ?? [] as $tenant)
                                        <tr>
                                            <td>{{ $tenant->name }}</td>
                                            <td>{{ $tenant->unit->property->name ?? 'N/A' }}</td>
                                            <td>{{ $tenant->unit->name ?? 'N/A' }}</td>
                                            <td>৳{{ number_format($tenant->unit->rent ?? 0, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($tenant->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No tenants found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Subscription Tab -->
                        <div class="tab-pane fade" id="subscription" role="tabpanel">
                            @if($owner->subscription)
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Current Plan</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>{{ $owner->subscription->plan->name }}</h6>
                                            <p class="text-muted">৳{{ number_format($owner->subscription->plan->price, 2) }}/year</p>
                                            <div class="mb-3">
                                                <strong>Features:</strong>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-building me-2"></i>{{ $owner->subscription->plan->properties_limit_text }}</li>
                                                    <li><i class="fas fa-home me-2"></i>{{ $owner->subscription->plan->units_limit_text }}</li>
                                                    <li><i class="fas fa-users me-2"></i>{{ $owner->subscription->plan->tenants_limit_text }}</li>
                                                    @if($owner->subscription->plan->sms_notification)
                                                    <li><i class="fas fa-sms me-2"></i>SMS Notifications</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Subscription Details</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <p><strong>Start Date:</strong> {{ $owner->subscription->start_date ? $owner->subscription->start_date->format('M d, Y') : 'N/A' }}</p>
                                            <p><strong>End Date:</strong> {{ $owner->subscription->end_date ? $owner->subscription->end_date->format('M d, Y') : 'N/A' }}</p>
                                            <p><strong>Status:</strong>
                                                <span class="badge bg-{{ $owner->subscription->status === 'active' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($owner->subscription->status) }}
                                                </span>
                                            </p>
                                            <p><strong>SMS Credits:</strong> {{ $owner->subscription->sms_credits }}</p>
                                            <p><strong>Auto Renew:</strong> {{ $owner->subscription->auto_renew ? 'Yes' : 'No' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No active subscription found for this owner.
                            </div>
                            @endif
                        </div>

                        <!-- Billing Tab -->
                        <div class="tab-pane fade" id="billing" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Payment Method</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($owner->billing ?? [] as $bill)
                                        <tr>
                                            <td>{{ $bill->invoice_number }}</td>
                                            <td>{{ $bill->created_at ? $bill->created_at->format('M d, Y') : 'N/A' }}</td>
                                            <td>৳{{ number_format($bill->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $bill->status === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($bill->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $bill->paymentMethod->name ?? 'N/A' }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No billing records found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Notifications Tab -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <button class="btn btn-success" onclick="sendTestEmail()">
                                        <i class="fas fa-envelope me-2"></i>Send Test Email
                                    </button>
                                    <button class="btn btn-info" onclick="sendTestSms()">
                                        <i class="fas fa-sms me-2"></i>Send Test SMS
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Recipient</th>
                                            <th>Status</th>
                                            <th>Content</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notificationLogs ?? [] as $log)
                                        <tr>
                                            <td>{{ $log->created_at ? $log->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $log->type === 'email' ? 'primary' : 'info' }}">
                                                    {{ strtoupper($log->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $log->recipient }}</td>
                                            <td>
                                                <span class="badge bg-{{ $log->status === 'sent' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($log->content, 50) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning resend-btn" title="Resend" data-log-id="{{ $log->id ?? 0 }}">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No notification logs found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Notification Modal -->
<div class="modal fade" id="testNotificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Test Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Notification Type</label>
                    <select class="form-select" id="notificationType">
                        <option value="welcome">Welcome Email</option>
                        <option value="subscription">Subscription Info</option>
                        <option value="payment">Payment Confirmation</option>
                        <option value="sms">Test SMS</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message (Optional)</label>
                    <textarea class="form-control" id="customMessage" rows="3" placeholder="Custom message..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendNotification()">Send</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle resend notification button clicks
        $('.resend-btn').on('click', function() {
            const logId = $(this).data('log-id');
            resendNotification(logId);
        });
    });

    function sendTestNotification() {
        $('#testNotificationModal').modal('show');
    }

    function sendNotification() {
        const type = $('#notificationType').val();
        const message = $('#customMessage').val();

        $.ajax({
            url: '{{ route("admin.owners.test-notification", $owner->id) }}',
            method: 'POST',
            data: {
                type: type,
                message: message,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Notification sent successfully!');
                    location.reload();
                } else {
                    alert('Failed to send notification: ' + response.message);
                }
            },
            error: function() {
                alert('Error sending notification');
            }
        });

        $('#testNotificationModal').modal('hide');
    }

    function sendTestEmail() {
        $.ajax({
            url: '{{ route("admin.owners.test-email", $owner->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Test email sent successfully!');
                } else {
                    alert('Failed to send test email: ' + response.message);
                }
            },
            error: function() {
                alert('Error sending test email');
            }
        });
    }

    function sendTestSms() {
        $.ajax({
            url: '{{ route("admin.owners.test-sms", $owner->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Test SMS sent successfully!');
                } else {
                    alert('Failed to send test SMS: ' + response.message);
                }
            },
            error: function() {
                alert('Error sending test SMS');
            }
        });
    }

    function resendNotification(logId) {
        if (confirm('Are you sure you want to resend this notification?')) {
            const baseUrl = '{{ route("admin.owners.resend-notification", ["id" => $owner->id, "log_id" => "PLACEHOLDER"]) }}';
            const url = baseUrl.replace('PLACEHOLDER', logId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Notification resent successfully!');
                        location.reload();
                    } else {
                        alert('Failed to resend notification: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error resending notification');
                }
            });
        }
    }
</script>
@endpush