@extends('layouts.admin')

@section('title', 'Notification Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i> Notification Settings
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Email Configuration Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-envelope"></i> Email Configuration
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.notifications.email.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_host">
                                                        <i class="fas fa-server"></i> SMTP Host
                                                    </label>
                                                    <input type="text" class="form-control" id="mail_host" name="mail_host"
                                                           value="{{ $emailSettings['mail_host'] ?? 'smtp.gmail.com' }}" required>
                                                    <small class="form-text text-muted">SMTP server hostname</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_port">
                                                        <i class="fas fa-network-wired"></i> SMTP Port
                                                    </label>
                                                    <input type="number" class="form-control" id="mail_port" name="mail_port"
                                                           value="{{ $emailSettings['mail_port'] ?? 587 }}" required>
                                                    <small class="form-text text-muted">SMTP server port</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_username">
                                                        <i class="fas fa-user"></i> SMTP Username
                                                    </label>
                                                    <input type="email" class="form-control" id="mail_username" name="mail_username"
                                                           value="{{ $emailSettings['mail_username'] ?? '' }}" required>
                                                    <small class="form-text text-muted">Email address for SMTP authentication</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_password">
                                                        <i class="fas fa-lock"></i> SMTP Password
                                                    </label>
                                                    <input type="password" class="form-control" id="mail_password" name="mail_password"
                                                           value="{{ $emailSettings['mail_password'] ?? '' }}" required>
                                                    <small class="form-text text-muted">Password or app password for SMTP</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_encryption">
                                                        <i class="fas fa-shield-alt"></i> Encryption
                                                    </label>
                                                    <select class="form-control" id="mail_encryption" name="mail_encryption">
                                                        <option value="tls" {{ ($emailSettings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                                        <option value="ssl" {{ ($emailSettings['mail_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                                        <option value="none" {{ ($emailSettings['mail_encryption'] ?? 'tls') === 'none' ? 'selected' : '' }}>None</option>
                                                    </select>
                                                    <small class="form-text text-muted">SMTP encryption method</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="email_enabled" name="email_enabled"
                                                               {{ ($emailSettings['email_enabled'] ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="email_enabled">
                                                            <i class="fas fa-toggle-on"></i> Enable Email Notifications
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">Enable or disable email notifications globally</small>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_from_address">
                                                        <i class="fas fa-at"></i> From Address
                                                    </label>
                                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address"
                                                           value="{{ $emailSettings['mail_from_address'] ?? 'noreply@hrms.com' }}" required>
                                                    <small class="form-text text-muted">Default sender email address</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Email Settings
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="testEmail()">
                                                    <i class="fas fa-paper-plane"></i> Test Email
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Configuration Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-sms"></i> SMS Configuration
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.notifications.sms.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sms_provider">
                                                        <i class="fas fa-mobile-alt"></i> SMS Provider
                                                    </label>
                                                    <select class="form-control" id="sms_provider" name="sms_provider">
                                                        <option value="bulksms" {{ ($smsSettings['sms_provider'] ?? 'bulksms') === 'bulksms' ? 'selected' : '' }}>Bulk SMS BD</option>
                                                        <option value="twilio" {{ ($smsSettings['sms_provider'] ?? 'bulksms') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                                        <option value="ssl" {{ ($smsSettings['sms_provider'] ?? 'bulksms') === 'ssl' ? 'selected' : '' }}>SSL Wireless</option>
                                                        <option value="greenweb" {{ ($smsSettings['sms_provider'] ?? 'bulksms') === 'greenweb' ? 'selected' : '' }}>GreenWeb</option>
                                                    </select>
                                                    <small class="form-text text-muted">Choose your SMS service provider</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sms_api_key">
                                                        <i class="fas fa-key"></i> API Key
                                                    </label>
                                                    <input type="text" class="form-control" id="sms_api_key" name="sms_api_key"
                                                           value="{{ $smsSettings['sms_api_key'] ?? '' }}" required>
                                                    <small class="form-text text-muted">SMS provider API key</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sms_api_secret">
                                                        <i class="fas fa-lock"></i> API Secret
                                                    </label>
                                                    <input type="password" class="form-control" id="sms_api_secret" name="sms_api_secret"
                                                           value="{{ $smsSettings['sms_api_secret'] ?? '' }}" required>
                                                    <small class="form-text text-muted">SMS provider API secret</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sms_sender_id">
                                                        <i class="fas fa-id-card"></i> Sender ID
                                                    </label>
                                                    <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id"
                                                           value="{{ $smsSettings['sms_sender_id'] ?? 'HRMS' }}" required>
                                                    <small class="form-text text-muted">Sender name for SMS (max 11 characters)</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sms_monthly_limit">
                                                        <i class="fas fa-chart-line"></i> Monthly SMS Limit
                                                    </label>
                                                    <input type="number" class="form-control" id="sms_monthly_limit" name="sms_monthly_limit"
                                                           value="{{ $smsSettings['sms_monthly_limit'] ?? 1000 }}" min="1" required>
                                                    <small class="form-text text-muted">Maximum SMS count per month</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="sms_enabled" name="sms_enabled"
                                                               {{ ($smsSettings['sms_enabled'] ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="sms_enabled">
                                                            <i class="fas fa-toggle-on"></i> Enable SMS Notifications
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">Enable or disable SMS notifications globally</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SMS Statistics -->
                                        @if(isset($smsStats))
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-chart-bar"></i> SMS Statistics</h6>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <strong>Status:</strong>
                                                            <span class="badge badge-{{ $smsStats['enabled'] ? 'success' : 'danger' }}">
                                                                {{ $smsStats['enabled'] ? 'Enabled' : 'Disabled' }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>Provider:</strong> {{ ucfirst($smsStats['provider']) }}
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>Used:</strong> {{ $smsStats['count'] }} / {{ $smsStats['limit'] }}
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>Remaining:</strong> {{ $smsStats['remaining'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save SMS Settings
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="testSMS()">
                                                    <i class="fas fa-paper-plane"></i> Test SMS
                                                </button>
                                                <button type="button" class="btn btn-warning" onclick="resetSmsCount()">
                                                    <i class="fas fa-redo"></i> Reset SMS Count
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Templates Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-alt"></i> Notification Templates
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">Email Templates</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="list-group">
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('payment_confirmation_email')">
                                                            <i class="fas fa-credit-card"></i> Payment Confirmation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('invoice_notification_email')">
                                                            <i class="fas fa-file-invoice"></i> Invoice Notification
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('subscription_reminder_email')">
                                                            <i class="fas fa-calendar-alt"></i> Subscription Reminder
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('subscription_activation_email')">
                                                            <i class="fas fa-check-circle"></i> Subscription Activation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('welcome_email')">
                                                            <i class="fas fa-handshake"></i> Welcome Email
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">SMS Templates</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="list-group">
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('payment_confirmation_sms')">
                                                            <i class="fas fa-credit-card"></i> Payment Confirmation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('due_date_reminder_sms')">
                                                            <i class="fas fa-clock"></i> Due Date Reminder
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('otp_verification_sms')">
                                                            <i class="fas fa-shield-alt"></i> OTP Verification
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('subscription_activation_sms')">
                                                            <i class="fas fa-check-circle"></i> Subscription Activation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('welcome_sms')">
                                                            <i class="fas fa-handshake"></i> Welcome SMS
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Notifications Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-vial"></i> Test Notifications
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="test_email">
                                                    <i class="fas fa-envelope"></i> Test Email Address
                                                </label>
                                                <input type="email" class="form-control" id="test_email" placeholder="Enter email address">
                                            </div>
                                            <button type="button" class="btn btn-info" onclick="sendTestEmail()">
                                                <i class="fas fa-paper-plane"></i> Send Test Email
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="test_sms">
                                                    <i class="fas fa-mobile-alt"></i> Test SMS Number
                                                </label>
                                                <input type="text" class="form-control" id="test_sms" placeholder="Enter phone number">
                                            </div>
                                            <button type="button" class="btn btn-info" onclick="sendTestSMS()">
                                                <i class="fas fa-paper-plane"></i> Send Test SMS
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Logs Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-history"></i> Notification Logs
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
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
                                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $log->type === 'email' ? 'info' : 'success' }}">
                                                            {{ ucfirst($log->type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $log->recipient }}</td>
                                                    <td>{{ Str::limit($log->content, 50) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $log->status === 'sent' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($log->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="viewLog({{ $log->id }})">
                                                            <i class="fas fa-eye"></i>
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

                    <!-- Back Button -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Template</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="templateForm">
                    <div class="form-group">
                        <label for="template_name">Template Name</label>
                        <input type="text" class="form-control" id="template_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="template_subject">Subject (Email only)</label>
                        <input type="text" class="form-control" id="template_subject">
                    </div>
                    <div class="form-group">
                        <label for="template_content">Content</label>
                        <textarea class="form-control" id="template_content" rows="10"></textarea>
                        <small class="form-text text-muted">
                            Available variables: {name}, {email}, {amount}, {invoice_number}, {due_date}, {payment_method}
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTemplate()">Save Template</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testEmail() {
    if (confirm('Send test email to verify configuration?')) {
        // AJAX call to test email
        fetch('{{ route("admin.notifications.email.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Test email sent successfully!');
            } else {
                alert('Failed to send test email: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending test email: ' + error);
        });
    }
}

function testSMS() {
    if (confirm('Send test SMS to verify configuration?')) {
        // AJAX call to test SMS
        fetch('{{ route("admin.notifications.sms.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Test SMS sent successfully!');
            } else {
                alert('Failed to send test SMS: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending test SMS: ' + error);
        });
    }
}

function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    if (!email) {
        alert('Please enter an email address');
        return;
    }

    fetch('{{ route("admin.notifications.email.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Test email sent successfully!');
        } else {
            alert('Failed to send test email: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending test email: ' + error.message);
    });
}

function sendTestSMS() {
    const phone = document.getElementById('test_sms').value;
    if (!phone) {
        alert('Please enter a phone number');
        return;
    }

    fetch('{{ route("admin.notifications.sms.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ phone: phone })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Test SMS sent successfully!');
        } else {
            alert('Failed to send test SMS: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending test SMS: ' + error.message);
    });
}

function editTemplate(templateName) {
    document.getElementById('template_name').value = templateName;
    document.getElementById('template_subject').value = '';
    document.getElementById('template_content').value = '';

    // Load template content via AJAX
    fetch(`{{ route("admin.notifications.template.get") }}?template=${templateName}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('template_subject').value = data.template.subject || '';
                document.getElementById('template_content').value = data.template.content || '';
            }
        })
        .catch(error => {
            console.error('Error loading template:', error);
            alert('Error loading template: ' + error.message);
        });

    $('#templateModal').modal('show');
}

function saveTemplate() {
    const templateName = document.getElementById('template_name').value;
    const subject = document.getElementById('template_subject').value;
    const content = document.getElementById('template_content').value;

    fetch('{{ route("admin.notifications.template.save") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            template: templateName,
            subject: subject,
            content: content
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Template saved successfully!');
            $('#templateModal').modal('hide');
        } else {
            alert('Failed to save template: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving template: ' + error.message);
    });
}

function viewLog(logId) {
    // AJAX call to view log details
    fetch(`{{ route("admin.notifications.log.view") }}?id=${logId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(`Log Details:\n\nDate: ${data.log.created_at}\nType: ${data.log.type}\nRecipient: ${data.log.recipient}\nContent: ${data.log.content}\nStatus: ${data.log.status}`);
            } else {
                alert('Failed to load log details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading log details: ' + error.message);
        });
}

function resetSmsCount() {
    if (confirm('Are you sure you want to reset the SMS count? This will reset the monthly SMS counter to 0.')) {
        fetch('{{ route("admin.notifications.sms.reset") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('SMS count reset successfully!');
                location.reload();
            } else {
                alert('Failed to reset SMS count: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error resetting SMS count: ' + error.message);
        });
    }
}
</script>
@endpush
