@extends('layouts.admin')

@section('title', 'SMS Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-sms me-2"></i>
                        SMS Gateway Settings
                    </h4>
                    <p class="text-muted mb-0">Configure your SMS gateway and automated messaging system</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.sms.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Gateway Configuration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>
                                    Gateway Configuration
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_enabled" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Enable SMS System
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled" value="1" 
                                               {{ $smsSettings['sms_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_enabled">
                                            Enable SMS functionality
                                        </label>
                                    </div>
                                    <small class="text-muted">Turn this on to enable SMS features</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_api_token" class="form-label">
                                        <i class="fas fa-key me-1"></i>
                                        API Token
                                    </label>
                                    <input type="text" class="form-control" id="sms_api_token" name="sms_api_token" 
                                           value="{{ $smsSettings['sms_api_token'] }}" placeholder="Enter your SMS API token">
                                    <small class="text-muted">Your SMS gateway API token</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_sender_id" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Sender ID
                                    </label>
                                    <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id" 
                                           value="{{ $smsSettings['sms_sender_id'] }}" placeholder="BARI MANAGER" maxlength="11">
                                    <small class="text-muted">Sender name (max 11 characters)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_test_number" class="form-label">
                                        <i class="fas fa-phone me-1"></i>
                                        Test Number
                                    </label>
                                    <input type="text" class="form-control" id="sms_test_number" name="sms_test_number" 
                                           value="{{ $smsSettings['sms_test_number'] }}" placeholder="8801700000000">
                                    <small class="text-muted">Number for testing SMS functionality</small>
                                </div>
                            </div>
                        </div>

                        <!-- Automated SMS Features -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-robot me-2"></i>
                                    Automated SMS Features
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_rent_reminder_enabled" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>
                                        Rent Reminder SMS
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_rent_reminder_enabled" name="sms_rent_reminder_enabled" value="1" 
                                               {{ $smsSettings['sms_rent_reminder_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_rent_reminder_enabled">
                                            Send rent due reminders
                                        </label>
                                    </div>
                                    <small class="text-muted">Automatically send rent payment reminders</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_maintenance_update_enabled" class="form-label">
                                        <i class="fas fa-tools me-1"></i>
                                        Maintenance Update SMS
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_maintenance_update_enabled" name="sms_maintenance_update_enabled" value="1" 
                                               {{ $smsSettings['sms_maintenance_update_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_maintenance_update_enabled">
                                            Send maintenance status updates
                                        </label>
                                    </div>
                                    <small class="text-muted">Notify tenants about maintenance progress</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_welcome_message_enabled" class="form-label">
                                        <i class="fas fa-handshake me-1"></i>
                                        Welcome SMS
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_welcome_message_enabled" name="sms_welcome_message_enabled" value="1" 
                                               {{ $smsSettings['sms_welcome_message_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_welcome_message_enabled">
                                            Send welcome messages to new tenants
                                        </label>
                                    </div>
                                    <small class="text-muted">Welcome new tenants with SMS</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_payment_confirmation_enabled" class="form-label">
                                        <i class="fas fa-money-bill me-1"></i>
                                        Payment Confirmation SMS
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_payment_confirmation_enabled" name="sms_payment_confirmation_enabled" value="1" 
                                               {{ $smsSettings['sms_payment_confirmation_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_payment_confirmation_enabled">
                                            Send payment confirmations
                                        </label>
                                    </div>
                                    <small class="text-muted">Confirm payments via SMS</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_checkout_reminder_enabled" class="form-label">
                                        <i class="fas fa-sign-out-alt me-1"></i>
                                        Checkout Reminder SMS
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_checkout_reminder_enabled" name="sms_checkout_reminder_enabled" value="1" 
                                               {{ $smsSettings['sms_checkout_reminder_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_checkout_reminder_enabled">
                                            Send checkout reminders
                                        </label>
                                    </div>
                                    <small class="text-muted">Remind tenants about checkout process</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_reminder_days_before" class="form-label">
                                        <i class="fas fa-clock me-1"></i>
                                        Reminder Days Before
                                    </label>
                                    <input type="number" class="form-control" id="sms_reminder_days_before" name="sms_reminder_days_before" 
                                           value="{{ $smsSettings['sms_reminder_days_before'] }}" min="1" max="30">
                                    <small class="text-muted">Days before due date to send reminders</small>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours & Retry Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-clock me-2"></i>
                                    Working Hours & Retry Settings
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_working_hours_start" class="form-label">
                                        <i class="fas fa-sun me-1"></i>
                                        Working Hours Start
                                    </label>
                                    <input type="time" class="form-control" id="sms_working_hours_start" name="sms_working_hours_start" 
                                           value="{{ $smsSettings['sms_working_hours_start'] }}">
                                    <small class="text-muted">Start time for SMS sending (24-hour format)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_working_hours_end" class="form-label">
                                        <i class="fas fa-moon me-1"></i>
                                        Working Hours End
                                    </label>
                                    <input type="time" class="form-control" id="sms_working_hours_end" name="sms_working_hours_end" 
                                           value="{{ $smsSettings['sms_working_hours_end'] }}">
                                    <small class="text-muted">End time for SMS sending (24-hour format)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_max_retries" class="form-label">
                                        <i class="fas fa-redo me-1"></i>
                                        Max Retries
                                    </label>
                                    <input type="number" class="form-control" id="sms_max_retries" name="sms_max_retries" 
                                           value="{{ $smsSettings['sms_max_retries'] }}" min="1" max="10">
                                    <small class="text-muted">Maximum retry attempts for failed SMS</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sms_retry_delay" class="form-label">
                                        <i class="fas fa-hourglass-half me-1"></i>
                                        Retry Delay (minutes)
                                    </label>
                                    <input type="number" class="form-control" id="sms_retry_delay" name="sms_retry_delay" 
                                           value="{{ $smsSettings['sms_retry_delay'] }}" min="1" max="60">
                                    <small class="text-muted">Delay between retry attempts</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-info me-2" id="test-connection-btn">
                                            <i class="fas fa-plug me-2"></i>
                                            Test Connection
                                        </button>
                                        <button type="button" class="btn btn-warning me-2" id="test-sms-btn">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Test SMS
                                        </button>
                                        <button type="button" class="btn btn-success me-2" id="check-balance-btn">
                                            <i class="fas fa-wallet me-2"></i>
                                            Check Balance
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="bulk-sms-btn">
                                            <i class="fas fa-broadcast-tower me-2"></i>
                                            Bulk SMS
                                        </button>
                                    </div>
                                    
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test SMS Modal -->
<div class="modal fade" id="testSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane me-2"></i>
                    Test SMS
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="test_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="test_number" placeholder="8801700000000">
                </div>
                <div class="form-group mb-3">
                    <label for="test_message" class="form-label">Message</label>
                    <textarea class="form-control" id="test_message" rows="3" placeholder="Test message from Bari Manager"></textarea>
                    <small class="text-muted">Max 160 characters</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="send-test-sms">
                    <i class="fas fa-paper-plane me-2"></i>
                    Send Test SMS
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk SMS Modal -->
<div class="modal fade" id="bulkSmsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-broadcast-tower me-2"></i>
                    Send Bulk SMS
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="bulk_numbers" class="form-label">Phone Numbers</label>
                    <textarea class="form-control" id="bulk_numbers" rows="3" placeholder="8801700000000, 8801800000000, 8801900000000"></textarea>
                    <small class="text-muted">Separate numbers with commas or spaces</small>
                </div>
                <div class="form-group mb-3">
                    <label for="bulk_message" class="form-label">Message</label>
                    <textarea class="form-control" id="bulk_message" rows="3" placeholder="Your bulk message here"></textarea>
                    <small class="text-muted">Max 160 characters</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="send-bulk-sms">
                    <i class="fas fa-broadcast-tower me-2"></i>
                    Send Bulk SMS
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test connection
    document.getElementById('test-connection-btn').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
        
        fetch('{{ route("admin.settings.sms.test-connection") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('SMS gateway connection successful!', 'success');
                } else {
                    showAlert('SMS gateway connection failed: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Error testing connection: ' + error.message, 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-plug me-2"></i>Test Connection';
            });
    });
    
    // Check balance
    document.getElementById('check-balance-btn').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking...';
        
        fetch('{{ route("admin.settings.sms.balance") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const balance = data.balance || 0;
                    const currency = data.currency || 'BDT';
                    showAlert(`SMS Balance: ${balance} ${currency}`, 'success');
                } else {
                    showAlert('Balance check failed: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Error checking balance: ' + error.message, 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-wallet me-2"></i>Check Balance';
            });
    });
    
    // Test SMS modal
    document.getElementById('test-sms-btn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('testSmsModal'));
        modal.show();
    });
    
    // Send test SMS
    document.getElementById('send-test-sms').addEventListener('click', function() {
        const number = document.getElementById('test_number').value;
        const message = document.getElementById('test_message').value;
        
        if (!number || !message) {
            showAlert('Please fill in both number and message', 'error');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        
        fetch('{{ route("admin.settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                test_number: number,
                test_message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Test SMS sent successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('testSmsModal')).hide();
            } else {
                showAlert('Test SMS failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('Error sending test SMS: ' + error.message, 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Test SMS';
        });
    });
    
    // Bulk SMS modal
    document.getElementById('bulk-sms-btn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('bulkSmsModal'));
        modal.show();
    });
    
    // Send bulk SMS
    document.getElementById('send-bulk-sms').addEventListener('click', function() {
        const numbers = document.getElementById('bulk_numbers').value;
        const message = document.getElementById('bulk_message').value;
        
        if (!numbers || !message) {
            showAlert('Please fill in both numbers and message', 'error');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        
        fetch('{{ route("admin.settings.sms.bulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                phone_numbers: numbers,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Bulk SMS sent successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('bulkSmsModal')).hide();
            } else {
                showAlert('Bulk SMS failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('Error sending bulk SMS: ' + error.message, 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-broadcast-tower me-2"></i>Send Bulk SMS';
        });
    });
    
    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = 160;
        const counter = document.createElement('small');
        counter.className = 'text-muted float-end';
        counter.textContent = `${textarea.value.length}/${maxLength}`;
        textarea.parentNode.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            counter.textContent = `${this.value.length}/${maxLength}`;
            if (this.value.length > maxLength) {
                counter.className = 'text-danger float-end';
            } else {
                counter.className = 'text-muted float-end';
            }
        });
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush 