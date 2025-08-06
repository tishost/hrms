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
                    <div class="mt-3">
                        <a href="{{ route('admin.settings.email.templates') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-envelope"></i> Email Templates
                        </a>
                        <a href="{{ route('admin.settings.sms.templates') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-sms"></i> SMS Templates
                        </a>
                    </div>
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
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-lock"></i> Password Reset Email
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('password_reset_email', 'email')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-password_reset_email" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Subject:</label>
                                                                    <input type="text" class="form-control" id="subject-password_reset_email" placeholder="Email subject">
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-password_reset_email" rows="4" placeholder="Email content with {otp} variable"></textarea>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('password_reset_email', 'email')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('password_reset_email')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-credit-card"></i> Payment Confirmation
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('payment_confirmation_email', 'email')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-payment_confirmation_email" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Subject:</label>
                                                                    <input type="text" class="form-control" id="subject-payment_confirmation_email" placeholder="Email subject">
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-payment_confirmation_email" rows="4" placeholder="Email content"></textarea>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('payment_confirmation_email', 'email')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('payment_confirmation_email')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-file-invoice"></i> Invoice Notification
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('invoice_notification_email', 'email')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-invoice_notification_email" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Subject:</label>
                                                                    <input type="text" class="form-control" id="subject-invoice_notification_email" placeholder="Email subject">
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-invoice_notification_email" rows="4" placeholder="Email content"></textarea>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('invoice_notification_email', 'email')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('invoice_notification_email')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-calendar-alt"></i> Subscription Reminder
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('subscription_reminder_email', 'email')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-subscription_reminder_email" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Subject:</label>
                                                                    <input type="text" class="form-control" id="subject-subscription_reminder_email" placeholder="Email subject">
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-subscription_reminder_email" rows="4" placeholder="Email content"></textarea>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('subscription_reminder_email', 'email')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('subscription_reminder_email')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-check-circle"></i> Subscription Activation
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('subscription_activation_email', 'email')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-subscription_activation_email" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Subject:</label>
                                                                    <input type="text" class="form-control" id="subject-subscription_activation_email" placeholder="Email subject">
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-subscription_activation_email" rows="4" placeholder="Email content"></textarea>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('subscription_activation_email', 'email')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('subscription_activation_email')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-handshake"></i> Welcome Email
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('welcome_email', 'email')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-welcome_email" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Subject:</label>
                                                                    <input type="text" class="form-control" id="subject-welcome_email" placeholder="Email subject">
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-welcome_email" rows="4" placeholder="Email content"></textarea>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('welcome_email', 'email')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('welcome_email')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-key"></i> Password Reset OTP SMS
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('password_reset_otp_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-password_reset_otp_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-password_reset_otp_sms" rows="3" placeholder="SMS content with {otp} variable" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-password_reset_otp_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('password_reset_otp_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('password_reset_otp_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-credit-card"></i> Owner Payment Confirmation
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('owner_payment_confirmation_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-owner_payment_confirmation_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-owner_payment_confirmation_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-owner_payment_confirmation_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('owner_payment_confirmation_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('owner_payment_confirmation_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-file-invoice"></i> Owner Invoice Notification
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('owner_invoice_notification_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-owner_invoice_notification_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-owner_invoice_notification_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-owner_invoice_notification_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('owner_invoice_notification_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('owner_invoice_notification_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-calendar-alt"></i> Owner Subscription Reminder
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('owner_subscription_reminder_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-owner_subscription_reminder_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-owner_subscription_reminder_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-owner_subscription_reminder_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('owner_subscription_reminder_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('owner_subscription_reminder_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-check-circle"></i> Owner Subscription Activation
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('owner_subscription_activation_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-owner_subscription_activation_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-owner_subscription_activation_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-owner_subscription_activation_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('owner_subscription_activation_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('owner_subscription_activation_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-handshake"></i> Owner Welcome SMS
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('owner_welcome_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-owner_welcome_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-owner_welcome_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-owner_welcome_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('owner_welcome_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('owner_welcome_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-credit-card"></i> Tenant Payment Confirmation
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('tenant_payment_confirmation_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-tenant_payment_confirmation_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-tenant_payment_confirmation_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-tenant_payment_confirmation_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('tenant_payment_confirmation_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('tenant_payment_confirmation_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-file-invoice"></i> Tenant Invoice Notification
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('tenant_invoice_notification_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-tenant_invoice_notification_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-tenant_invoice_notification_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-tenant_invoice_notification_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('tenant_invoice_notification_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('tenant_invoice_notification_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-calendar-alt"></i> Tenant Subscription Reminder
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('tenant_subscription_reminder_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-tenant_subscription_reminder_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-tenant_subscription_reminder_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-tenant_subscription_reminder_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('tenant_subscription_reminder_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('tenant_subscription_reminder_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-check-circle"></i> Tenant Subscription Activation
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('tenant_subscription_activation_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-tenant_subscription_activation_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-tenant_subscription_activation_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-tenant_subscription_activation_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('tenant_subscription_activation_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('tenant_subscription_activation_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-handshake"></i> Tenant Welcome SMS
                                                                </div>
                                                                <button class="btn btn-sm btn-primary" onclick="toggleInlineEdit('tenant_welcome_sms', 'sms')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                            </div>
                                                            <div id="edit-tenant_welcome_sms" class="mt-3" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Content:</label>
                                                                    <textarea class="form-control" id="content-tenant_welcome_sms" rows="3" placeholder="SMS content" maxlength="160"></textarea>
                                                                    <small class="text-muted">Character count: <span id="char-count-tenant_welcome_sms">0/160</span></small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-success" onclick="saveInlineTemplate('tenant_welcome_sms', 'sms')">
                                                                        <i class="fas fa-save"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit('tenant_welcome_sms')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
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







// Inline Template Editing Functions
function toggleInlineEdit(templateName, type) {
    const editDiv = document.getElementById('edit-' + templateName);
    if (editDiv.style.display === 'none') {
        // Show edit form and load template
        editDiv.style.display = 'block';
        loadInlineTemplate(templateName, type);
    } else {
        // Hide edit form
        editDiv.style.display = 'none';
    }
}

function loadInlineTemplate(templateName, type) {
    // Load template from database using simplified CSRF handling
    fetchWithCsrf('{{ route("admin.notifications.template.get") }}?template=' + encodeURIComponent(templateName), {
        method: 'GET'
    })
    .then(response => {
        console.log('Load template response status:', response.status);
        if (handleSessionExpired(response)) return;
        
        if (response.status === 401) {
            console.log('Authentication required for template load');
            alert('Session expired. Please log in again.');
            window.location.href = '/login';
            return;
        }
        if (response.status === 403) {
            console.log('Access denied for template load');
            alert('Access denied. You do not have permission to perform this action.');
            return;
        }
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (!data) return;
        
        if (data.success && data.template && data.template.content) {
            // Load content into form
            if (type === 'email') {
                document.getElementById('subject-' + templateName).value = data.template.subject || '';
                document.getElementById('content-' + templateName).value = data.template.content;
            } else {
                document.getElementById('content-' + templateName).value = data.template.content;
                updateCharCount(templateName);
            }
        } else {
            // Set default content
            if (type === 'email') {
                document.getElementById('subject-' + templateName).value = 'HRMS Notification';
                document.getElementById('content-' + templateName).value = 'Dear {name},\n\nThis is a notification from HRMS.\n\nBest regards,\nHRMS Team';
            } else {
                document.getElementById('content-' + templateName).value = 'Welcome to HRMS! Your notification has been sent.';
                updateCharCount(templateName);
            }
        }
    })
    .catch(error => {
        console.error('Error loading template:', error);
        if (type === 'email') {
            document.getElementById('subject-' + templateName).value = 'HRMS Notification';
            document.getElementById('content-' + templateName).value = 'Dear {name},\n\nThis is a notification from HRMS.\n\nBest regards,\nHRMS Team';
        } else {
            document.getElementById('content-' + templateName).value = 'Welcome to HRMS! Your notification has been sent.';
            updateCharCount(templateName);
        }
    });
}

function saveInlineTemplate(templateName, type) {
    let content = document.getElementById('content-' + templateName).value;
    let subject = '';
    
    if (type === 'email') {
        subject = document.getElementById('subject-' + templateName).value;
        if (!subject.trim() || !content.trim()) {
            alert('Please fill in both subject and content');
            return;
        }
    } else {
        if (!content.trim()) {
            alert('Please enter SMS content');
            return;
        }
        if (content.length > 160) {
            alert('SMS content cannot exceed 160 characters');
            return;
        }
    }
    
    // Prepare data
    const data = {
        template_name: templateName,
        content: content,
        _token: document.querySelector('meta[name="csrf-token"]').content
    };
    
    if (type === 'email') {
        data.subject = subject;
    }
    
    // Save template with retry mechanism
    saveTemplateWithRetry(templateName, type, data, 0);
}

function saveTemplateWithRetry(templateName, type, data, retryCount) {
    console.log(`Attempting to save template (attempt ${retryCount + 1})...`);
    
    fetchWithCsrf('{{ route("admin.notifications.template.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Save response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (response.status === 419 && retryCount < 2) {
            // CSRF token mismatch, refresh and retry
            console.log('CSRF token mismatch, refreshing and retrying...');
            return fetch('/refresh-csrf')
                .then(res => {
                    console.log('Refresh CSRF response status:', res.status);
                    return res.json();
                })
                .then(tokenData => {
                    console.log('Refresh CSRF response:', tokenData);
                    if (tokenData.token) {
                        // Update meta tag and token
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            metaTag.content = tokenData.token;
                        }
                        data._token = tokenData.token;
                        console.log('✅ CSRF token refreshed, retrying...');
                        // Retry the save
                        return saveTemplateWithRetry(templateName, type, data, retryCount + 1);
                    } else {
                        throw new Error('Failed to refresh CSRF token');
                    }
                })
                .catch(refreshError => {
                    console.error('Error refreshing CSRF token:', refreshError);
                    throw new Error('Failed to refresh CSRF token: ' + refreshError.message);
                });
        }
        
        if (response.status === 401) {
            console.log('Authentication required');
            alert('Session expired. Please log in again.');
            window.location.href = '/login';
            return;
        }
        if (response.status === 403) {
            console.log('Access denied');
            alert('Access denied. You do not have permission to perform this action.');
            return;
        }
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (!data) return;
        
        console.log('Save response:', data);
        if (data.success) {
            alert('Template saved successfully!');
            // Hide edit form
            document.getElementById('edit-' + templateName).style.display = 'none';
        } else {
            alert('Failed to save template: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        if (retryCount >= 2) {
            alert('Failed to save template after multiple attempts. Please refresh the page and try again.');
        } else {
            alert('Error saving template: ' + error.message);
        }
    });
}



function cancelInlineEdit(templateName) {
    document.getElementById('edit-' + templateName).style.display = 'none';
}

function updateCharCount(templateName) {
    const textarea = document.getElementById('content-' + templateName);
    const charCount = document.getElementById('char-count-' + templateName);
    if (textarea && charCount) {
        const count = textarea.value.length;
        charCount.textContent = count + '/160';
        
        if (count > 160) {
            charCount.className = 'text-danger';
        } else if (count > 140) {
            charCount.className = 'text-warning';
        } else {
            charCount.className = 'text-muted';
        }
    }
}

// Simplified CSRF handling function
function fetchWithCsrf(url, options = {}) {
    // Get token from meta tag with fallback
    let token = '';
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag && metaTag.content) {
        token = metaTag.content;
    } else {
        console.error('CSRF token not found in meta tag');
        // Try to get from form
        const tokenInput = document.querySelector('input[name="_token"]');
        if (tokenInput) {
            token = tokenInput.value;
        }
    }
    
    if (!token) {
        console.error('No CSRF token available');
        return Promise.reject(new Error('CSRF token not available'));
    }
    
    // Set default headers if none exist
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = token;
    options.headers['Accept'] = 'application/json';
    options.headers['X-Requested-With'] = 'XMLHttpRequest';
    
    console.log('Making request with CSRF token:', token.substring(0, 20) + '...');
    
    return fetch(url, options);
}

// Handle session expiration gracefully
function handleSessionExpired(response) {
    if (response.status === 419) {
        console.log('Session expired, attempting to refresh CSRF token...');
        
        // Try to refresh the CSRF token
        fetch('/refresh-csrf')
            .then(res => res.json())
            .then(data => {
                if (data.token) {
                    // Update meta tag
                    document.querySelector('meta[name="csrf-token"]').content = data.token;
                    console.log('✅ CSRF token refreshed successfully');
                    // Don't show alert, just log the refresh
                    console.log('CSRF token refreshed, user can retry');
                } else {
                    // Redirect to login
                    console.log('❌ Failed to refresh CSRF token, redirecting to login');
                    window.location.href = '/login';
                }
            })
            .catch(error => {
                console.error('Error refreshing CSRF token:', error);
                window.location.href = '/login';
            });
        return true;
    }
    return false;
}

// Test CSRF function
function testCsrfToken() {
    console.log('Testing CSRF token...');
    
    const data = {
        test: 'data',
        _token: document.querySelector('meta[name="csrf-token"]').content
    };
    
    fetchWithCsrf('/test-csrf-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('CSRF test response status:', response.status);
        if (handleSessionExpired(response)) return;
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            console.log('✅ CSRF token working correctly');
            console.log('CSRF Token:', data.csrf_token);
            console.log('Session Token:', data.session_token);
            console.log('Tokens Match:', data.tokens_match);
            console.log('Session ID:', data.session_id);
            console.log('User Authenticated:', data.user_authenticated);
        } else {
            console.log('❌ CSRF token test failed');
            console.log('Response:', data);
        }
    })
    .catch(error => {
        console.error('CSRF test error:', error);
    });
}

// Session refresh function
function refreshSession() {
    console.log('Refreshing session...');
    
    fetchWithCsrf('/admin/settings/notifications', {
        method: 'GET'
    })
    .then(response => {
        console.log('Session refresh response:', response.status);
        if (response.ok) {
            console.log('✅ Session refreshed successfully');
        } else {
            console.log('❌ Session refresh failed');
        }
    })
    .catch(error => {
        console.error('Session refresh error:', error);
    });
}

// Add character counter for SMS template
document.addEventListener('DOMContentLoaded', function() {
    const smsContent = document.getElementById('sms_template_content');
    if (smsContent) {
        smsContent.addEventListener('input', updateSmsCharCount);
    }
    
    // Add character counters for inline SMS templates
    const smsTextareas = document.querySelectorAll('textarea[id^="content-"][maxlength="160"]');
    smsTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const templateName = this.id.replace('content-', '');
            updateCharCount(templateName);
        });
    });
    
    // Refresh session and CSRF token before template operations
    if (typeof refreshSession === 'function') {
        refreshSession();
    }
    
    if (typeof refreshCsrfToken === 'function') {
        refreshCsrfToken();
    }
    
    // Test CSRF token on page load
    setTimeout(() => {
        if (typeof testCsrfToken === 'function') {
            console.log('🔍 Testing CSRF token on page load...');
            testCsrfToken();
        }
    }, 1000);
    
    // Refresh session every 5 minutes to keep it alive
    setInterval(function() {
        if (typeof refreshSession === 'function') {
            refreshSession();
        }
    }, 5 * 60 * 1000); // 5 minutes
    
    // Test CSRF token every 2 minutes
    setInterval(function() {
        if (typeof testCsrfToken === 'function') {
            console.log('🔍 Periodic CSRF token test...');
            testCsrfToken();
        }
    }, 2 * 60 * 1000); // 2 minutes
});







</script>
@endpush
