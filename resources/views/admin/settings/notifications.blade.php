@extends('layouts.admin')

@section('title', 'Notification Settings')

@section('content')
<style>
    /* Custom Pagination Styling */
    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }
    
    .page-link {
        color: #3498db !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.5rem 0.75rem !important;
        margin-left: 0 !important;
        transition: all 0.3s ease;
        border-radius: 5px !important;
        text-decoration: none !important;
    }
    
    .page-link:hover {
        color: #fff !important;
        background-color: #3498db !important;
        border-color: #3498db !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(52, 152, 219, 0.3);
        text-decoration: none !important;
    }
    
    .page-item.active .page-link {
        background-color: #3498db !important;
        border-color: #3498db !important;
        color: #fff !important;
        z-index: 3;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d !important;
        pointer-events: none;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
        opacity: 0.6;
    }
    
    .page-item:first-child .page-link {
        border-top-left-radius: 5px !important;
        border-bottom-left-radius: 5px !important;
    }
    
    .page-item:last-child .page-link {
        border-top-right-radius: 5px !important;
        border-bottom-right-radius: 5px !important;
    }
    
    /* Pagination Info Styling */
    .pagination-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
    }
</style>

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





                    <!-- SMS Notification Groups Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-sms"></i> SMS Notification Groups
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.notifications.sms-groups.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <!-- System Group -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="fas fa-cogs me-2"></i> System Notifications
                                                </h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="system_welcome_sms" name="system_welcome_sms" value="1" 
                                                           {{ ($smsGroupSettings['system_welcome_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="system_welcome_sms">
                                                        <i class="fas fa-handshake me-1"></i> Welcome SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send welcome SMS to new users</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="system_otp_sms" name="system_otp_sms" value="1" 
                                                           {{ ($smsGroupSettings['system_otp_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="system_otp_sms">
                                                        <i class="fas fa-key me-1"></i> OTP Verification SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send OTP for account verification</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="system_password_reset_sms" name="system_password_reset_sms" value="1" 
                                                           {{ ($smsGroupSettings['system_password_reset_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="system_password_reset_sms">
                                                        <i class="fas fa-lock me-1"></i> Password Reset SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send password reset codes</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="system_password_reset_otp_sms" name="system_password_reset_otp_sms" value="1" 
                                                           {{ ($smsGroupSettings['system_password_reset_otp_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="system_password_reset_otp_sms">
                                                        <i class="fas fa-key me-1"></i> Password Reset OTP SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send OTP for password reset</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="system_security_alert_sms" name="system_security_alert_sms" value="1" 
                                                           {{ ($smsGroupSettings['system_security_alert_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="system_security_alert_sms">
                                                        <i class="fas fa-shield-alt me-1"></i> Security Alert SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send security alerts</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Owner Group -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-success mb-3">
                                                    <i class="fas fa-user-tie me-2"></i> Owner Notifications
                                                </h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="owner_welcome_sms" name="owner_welcome_sms" value="1" 
                                                           {{ ($smsGroupSettings['owner_welcome_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="owner_welcome_sms">
                                                        <i class="fas fa-handshake me-1"></i> Owner Welcome SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send welcome SMS to new owners</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="owner_package_purchase_sms" name="owner_package_purchase_sms" value="1" 
                                                           {{ ($smsGroupSettings['owner_package_purchase_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="owner_package_purchase_sms">
                                                        <i class="fas fa-shopping-cart me-1"></i> Package Purchase SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send SMS when owner purchases package</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="owner_payment_confirmation_sms" name="owner_payment_confirmation_sms" value="1" 
                                                           {{ ($smsGroupSettings['owner_payment_confirmation_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="owner_payment_confirmation_sms">
                                                        <i class="fas fa-credit-card me-1"></i> Payment Confirmation SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send payment confirmation SMS</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="owner_invoice_reminder_sms" name="owner_invoice_reminder_sms" value="1" 
                                                           {{ ($smsGroupSettings['owner_invoice_reminder_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="owner_invoice_reminder_sms">
                                                        <i class="fas fa-file-invoice me-1"></i> Invoice Reminder SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send invoice reminder SMS</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="owner_subscription_expiry_sms" name="owner_subscription_expiry_sms" value="1" 
                                                           {{ ($smsGroupSettings['owner_subscription_expiry_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="owner_subscription_expiry_sms">
                                                        <i class="fas fa-calendar-times me-1"></i> Subscription Expiry SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send subscription expiry reminder</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="owner_subscription_renewal_sms" name="owner_subscription_renewal_sms" value="1" 
                                                           {{ ($smsGroupSettings['owner_subscription_renewal_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="owner_subscription_renewal_sms">
                                                        <i class="fas fa-sync me-1"></i> Subscription Renewal SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send subscription renewal confirmation</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tenant Group -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-info mb-3">
                                                    <i class="fas fa-users me-2"></i> Tenant Notifications
                                                </h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="tenant_welcome_sms" name="tenant_welcome_sms" value="1" 
                                                           {{ ($smsGroupSettings['tenant_welcome_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_welcome_sms">
                                                        <i class="fas fa-handshake me-1"></i> Tenant Welcome SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send welcome SMS to new tenants</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="tenant_rent_reminder_sms" name="tenant_rent_reminder_sms" value="1" 
                                                           {{ ($smsGroupSettings['tenant_rent_reminder_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_rent_reminder_sms">
                                                        <i class="fas fa-calendar-alt me-1"></i> Rent Reminder SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send rent reminder SMS</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="tenant_payment_confirmation_sms" name="tenant_payment_confirmation_sms" value="1" 
                                                           {{ ($smsGroupSettings['tenant_payment_confirmation_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_payment_confirmation_sms">
                                                        <i class="fas fa-credit-card me-1"></i> Payment Confirmation SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send payment confirmation SMS</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="tenant_maintenance_update_sms" name="tenant_maintenance_update_sms" value="1" 
                                                           {{ ($smsGroupSettings['tenant_maintenance_update_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_maintenance_update_sms">
                                                        <i class="fas fa-tools me-1"></i> Maintenance Update SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send maintenance status updates</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="tenant_checkout_reminder_sms" name="tenant_checkout_reminder_sms" value="1" 
                                                           {{ ($smsGroupSettings['tenant_checkout_reminder_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_checkout_reminder_sms">
                                                        <i class="fas fa-sign-out-alt me-1"></i> Checkout Reminder SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send checkout reminder SMS</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="tenant_lease_expiry_sms" name="tenant_lease_expiry_sms" value="1" 
                                                           {{ ($smsGroupSettings['tenant_lease_expiry_sms'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_lease_expiry_sms">
                                                        <i class="fas fa-calendar-times me-1"></i> Lease Expiry SMS
                                                    </label>
                                                    <small class="text-muted d-block">Send lease expiry reminder</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save SMS Group Settings
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
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editTemplate('password_reset_email')">
                                                            <i class="fas fa-lock"></i> Password Reset Email
                                                        </a>
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
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('password_reset_otp_sms')">
                                                            <i class="fas fa-key"></i> Password Reset OTP SMS
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('owner_payment_confirmation_sms')">
                                                            <i class="fas fa-credit-card"></i> Owner Payment Confirmation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('owner_invoice_notification_sms')">
                                                            <i class="fas fa-file-invoice"></i> Owner Invoice Notification
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('owner_subscription_reminder_sms')">
                                                            <i class="fas fa-calendar-alt"></i> Owner Subscription Reminder
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('owner_subscription_activation_sms')">
                                                            <i class="fas fa-check-circle"></i> Owner Subscription Activation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('owner_welcome_sms')">
                                                            <i class="fas fa-handshake"></i> Owner Welcome SMS
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('tenant_payment_confirmation_sms')">
                                                            <i class="fas fa-credit-card"></i> Tenant Payment Confirmation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('tenant_invoice_notification_sms')">
                                                            <i class="fas fa-file-invoice"></i> Tenant Invoice Notification
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('tenant_subscription_reminder_sms')">
                                                            <i class="fas fa-calendar-alt"></i> Tenant Subscription Reminder
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('tenant_subscription_activation_sms')">
                                                            <i class="fas fa-check-circle"></i> Tenant Subscription Activation
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action" onclick="editSmsTemplate('tenant_welcome_sms')">
                                                            <i class="fas fa-handshake"></i> Tenant Welcome SMS
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
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $notificationLogs->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                                    </div>
                                    
                                    <!-- Pagination Info -->
                                    <div class="text-center mt-3">
                                        <div class="pagination-info">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Showing {{ $notificationLogs->firstItem() ?? 0 }} to {{ $notificationLogs->lastItem() ?? 0 }} 
                                                of {{ $notificationLogs->total() }} notification logs
                                                <span class="badge badge-info ms-2">{{ $notificationLogs->perPage() }} per page</span>
                                            </small>
                                        </div>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="templateForm" method="GET" action="{{ route('admin.notifications.template.save') }}">
                    <div class="form-group">
                        <label for="template_name">Template Name</label>
                        <input type="text" class="form-control" id="template_name" name="template_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="template_subject">Subject (Email only)</label>
                        <input type="text" class="form-control" id="template_subject" name="subject">
                    </div>
                    <div class="form-group">
                        <label for="template_content">Content</label>
                        <textarea class="form-control" id="template_content" name="content" rows="10"></textarea>
                        <small class="form-text text-muted">
                            Available variables: {name}, {email}, {amount}, {invoice_number}, {due_date}, {payment_method}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeTemplateModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveEmailTemplateAjax()">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SMS Template Editor Modal -->
<div class="modal fade" id="smsTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit SMS Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="smsTemplateForm" method="GET" action="{{ route('admin.notifications.template.save') }}">
                    <div class="form-group">
                        <label for="sms_template_name">Template Name</label>
                        <input type="text" class="form-control" id="sms_template_name" name="template_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="sms_template_content">SMS Content</label>
                        <textarea class="form-control" id="sms_template_content" name="content" rows="6" maxlength="160"></textarea>
                        <small class="form-text text-muted">
                            Available variables: {name}, {phone}, {amount}, {invoice_number}, {due_date}, {payment_method}, {property_name}, {tenant_name}
                            <br><span id="sms_char_count">0/160 characters</span>
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeSmsTemplateModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveSmsTemplateAjax()">Save SMS Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global CSRF token handling
    function getCsrfToken() {
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // If token is not available, try to refresh it
        if (!token) {
            fetch('/csrf-token')
                .then(response => response.json())
                .then(data => {
                    token = data.token;
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
                })
                .catch(() => {
                    alert('Unable to get CSRF token. Please refresh the page.');
                    return null;
                });
        }
        
        return token;
    }

    // Enhanced fetch function with CSRF token
    function fetchWithCsrf(url, options = {}) {
        const token = getCsrfToken();
        
        // Set default headers
        options.headers = {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        };
        
        return fetch(url, options);
    }

    // Alternative fetch function that handles CSRF token more reliably
    function fetchWithCsrfReliable(url, options = {}) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Set default headers
        options.headers = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            ...options.headers
        };
        
        // Don't set Content-Type for FormData, let the browser set it
        if (!(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json';
        }
        
        return fetch(url, options);
    }

    // Simple fetch function with CSRF token
    function fetchWithCsrfSimple(url, options = {}) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Set headers
        options.headers = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            ...options.headers
        };
        
        return fetch(url, options);
    }

    // Direct CSRF token fetch function
    function fetchWithDirectCsrf(url, options = {}) {
        const token = '{{ csrf_token() }}';
        
        // Set headers
        options.headers = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            ...options.headers
        };
        
        return fetch(url, options);
    }

    // Alternative CSRF token fetch function with X-XSRF-TOKEN
    function fetchWithXSRF(url, options = {}) {
        const token = '{{ csrf_token() }}';
        
        // Set headers
        options.headers = {
            'X-XSRF-TOKEN': token,
            'Accept': 'application/json',
            ...options.headers
        };
        
        return fetch(url, options);
    }

    // Standard Laravel CSRF token fetch function
    function fetchWithLaravelCsrf(url, options = {}) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Set headers
        options.headers = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            ...options.headers
        };
        
        return fetch(url, options);
    }










function editTemplate(templateName) {
    document.getElementById('template_name').value = templateName;
    document.getElementById('template_subject').value = '';
    document.getElementById('template_content').value = '';

    // Load template from database
    fetch('{{ route("admin.notifications.template.get") }}?template=' + encodeURIComponent(templateName), {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Template load response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Template load response:', data);
        if (data.success && data.template && data.template.content) {
            // Use saved content from database
            document.getElementById('template_content').value = data.template.content;
        } else {
            // Set default content
            document.getElementById('template_content').value = 'Dear {name},\n\nThis is a notification from HRMS.\n\nBest regards,\nHRMS Team';
        }
    })
    .catch(error => {
        console.error('Error loading template:', error);
        document.getElementById('template_content').value = 'Dear {name},\n\nThis is a notification from HRMS.\n\nBest regards,\nHRMS Team';
    });

    const templateModal = new bootstrap.Modal(document.getElementById('templateModal'));
    templateModal.show();
}

function saveTemplate() {
    // This function is no longer needed as we're using form submission
    // The form will handle the submission automatically
}

function viewLog(logId) {
    // AJAX call to view log details
    fetchWithCsrf(`{{ route("admin.notifications.log.view") }}?id=${logId}`, { method: 'GET' })
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

function closeTemplateModal() {
    const templateModal = bootstrap.Modal.getInstance(document.getElementById('templateModal'));
    if (templateModal) {
        templateModal.hide();
    }
}

// SMS Template Functions
function editSmsTemplate(templateName) {
    document.getElementById('sms_template_name').value = templateName;
    document.getElementById('sms_template_content').value = '';

    // Load template from database
    fetch('{{ route("admin.notifications.template.get") }}?template=' + encodeURIComponent(templateName), {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('SMS template load response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('SMS template load response:', data);
        if (data.success && data.template && data.template.content) {
            // Use saved content from database
            document.getElementById('sms_template_content').value = data.template.content;
        } else {
            // Set default content
            document.getElementById('sms_template_content').value = 'Welcome to HRMS! Your notification has been sent.';
        }
        updateSmsCharCount();
    })
    .catch(error => {
        console.error('Error loading SMS template:', error);
        document.getElementById('sms_template_content').value = 'Welcome to HRMS! Your notification has been sent.';
        updateSmsCharCount();
    });

    const smsTemplateModal = new bootstrap.Modal(document.getElementById('smsTemplateModal'));
    smsTemplateModal.show();
}

function saveSmsTemplate() {
    // This function is no longer needed as we're using form submission
    // The form will handle the submission automatically
}

function validateSmsForm() {
    const content = document.getElementById('sms_template_content').value;
    const templateName = document.getElementById('sms_template_name').value;
    
    if (!content.trim()) {
        alert('Please enter SMS content');
        return false;
    }
    
    if (content.length > 160) {
        alert('SMS content cannot exceed 160 characters');
        return false;
    }
    
    return true;
}

function saveSmsTemplateAjax() {
    const templateName = document.getElementById('sms_template_name').value;
    const content = document.getElementById('sms_template_content').value;
    
    if (!validateSmsForm()) {
        return;
    }
    
    // Refresh CSRF token before saving
    if (typeof refreshCsrfToken === 'function') {
        refreshCsrfToken();
    }
    
    // Get CSRF token with fallback
    let csrfToken = '';
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        csrfToken = csrfMeta.getAttribute('content');
        console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
    } else {
        console.error('CSRF token meta tag not found');
    }
    
    // Use POST method with form data
    const formData = new FormData();
    formData.append('template_name', templateName);
    formData.append('content', content);
    formData.append('_token', csrfToken); // Add token to form data as well
    
    fetch('{{ route("admin.notifications.template.save") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Template save response:', data);
        if (data.success) {
            alert('Template saved successfully!');
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('smsTemplateModal'));
            if (modal) {
                modal.hide();
            }
            // Reload the page to show updated templates
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert('Failed to save template: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        alert('Error saving template: ' + error.message);
    });
}

function saveEmailTemplateAjax() {
    const templateName = document.getElementById('template_name').value;
    const subject = document.getElementById('template_subject').value;
    const content = document.getElementById('template_content').value;
    
    if (!templateName || !content.trim()) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Refresh CSRF token before saving
    if (typeof refreshCsrfToken === 'function') {
        refreshCsrfToken();
    }
    
    // Get CSRF token with fallback
    let csrfToken = '';
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        csrfToken = csrfMeta.getAttribute('content');
        console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
    } else {
        console.error('CSRF token meta tag not found');
    }
    
    // Use POST method with form data
    const formData = new FormData();
    formData.append('template_name', templateName);
    formData.append('subject', subject);
    formData.append('content', content);
    formData.append('_token', csrfToken); // Add token to form data as well
    
    fetch('{{ route("admin.notifications.template.save") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Template save response:', data);
        if (data.success) {
            alert('Template saved successfully!');
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('templateModal'));
            if (modal) {
                modal.hide();
            }
            // Reload the page to show updated templates
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert('Failed to save template: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        alert('Error saving template: ' + error.message);
    });
}

function closeSmsTemplateModal() {
    const smsTemplateModal = bootstrap.Modal.getInstance(document.getElementById('smsTemplateModal'));
    if (smsTemplateModal) {
        smsTemplateModal.hide();
    }
}

function updateSmsCharCount() {
    const content = document.getElementById('sms_template_content').value;
    const count = content.length;
    const charCount = document.getElementById('sms_char_count');
    
    charCount.textContent = `${count}/160 characters`;
    
    if (count > 160) {
        charCount.className = 'text-danger';
    } else if (count > 140) {
        charCount.className = 'text-warning';
    } else {
        charCount.className = 'text-muted';
    }
}

// Add character counter for SMS template
document.addEventListener('DOMContentLoaded', function() {
    const smsContent = document.getElementById('sms_template_content');
    if (smsContent) {
        smsContent.addEventListener('input', updateSmsCharCount);
    }
    
    // Refresh CSRF token before template operations
    if (typeof refreshCsrfToken === 'function') {
        refreshCsrfToken();
    }
});







</script>
@endpush
