@extends('layouts.admin')

@section('title', 'Template Groups')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-layer-group"></i> Email & SMS Template Groups
                    </h4>
                    <p class="card-subtitle text-muted">Organized template management by categories</p>
                </div>
                <div class="card-body">
                    
                    <!-- System Templates Group -->
                    <div class="template-group mb-5">
                        <div class="group-header">
                            <h5 class="group-title">
                                <i class="fas fa-cog text-primary"></i> System Templates
                                <span class="badge badge-primary ml-2">System-wide notifications</span>
                            </h5>
                            <p class="group-description">Core system notifications for authentication, security, and general operations</p>
                        </div>
                        
                        <div class="row">
                            <!-- System Email Templates -->
                            <div class="col-md-6">
                                <div class="template-category">
                                    <h6 class="category-title">
                                        <i class="fas fa-envelope text-info"></i> Email Templates
                                    </h6>
                                    <div class="template-list">
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Password Reset Email</h6>
                                                <p class="template-description">Sent when user requests password reset</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {user_name}, {otp}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#password-reset-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Account Verification Email</h6>
                                                <p class="template-description">Sent for email verification</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {user_name}, {verification_url}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#account-verification-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- System SMS Templates -->
                            <div class="col-md-6">
                                <div class="template-category">
                                    <h6 class="category-title">
                                        <i class="fas fa-sms text-success"></i> SMS Templates
                                    </h6>
                                    <div class="template-list">
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Password Reset OTP SMS</h6>
                                                <p class="template-description">OTP for password reset via SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {otp}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#password-reset-otp-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">System Welcome SMS</h6>
                                                <p class="template-description">Welcome message for new system users</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {user_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#system-welcome-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Templates Group -->
                    <div class="template-group mb-5">
                        <div class="group-header">
                            <h5 class="group-title">
                                <i class="fas fa-home text-warning"></i> Owner Templates
                                <span class="badge badge-warning ml-2">Property owner notifications</span>
                            </h5>
                            <p class="group-description">Notifications for property owners including welcome, payments, subscriptions, and property management</p>
                        </div>
                        
                        <div class="row">
                            <!-- Owner Email Templates -->
                            <div class="col-md-6">
                                <div class="template-category">
                                    <h6 class="category-title">
                                        <i class="fas fa-envelope text-info"></i> Email Templates
                                    </h6>
                                    <div class="template-list">
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Welcome Email</h6>
                                                <p class="template-description">Welcome message for new property owners</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {account_id}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#owner-welcome-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Payment Confirmation Email</h6>
                                                <p class="template-description">Payment confirmation for owners</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {payment_type}, {amount}, {transaction_id}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#owner-payment-confirmation-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Invoice Notification Email</h6>
                                                <p class="template-description">Invoice notifications for owners</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {month}, {amount}, {property_name}, {tenant_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#owner-invoice-notification-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Subscription Reminder Email</h6>
                                                <p class="template-description">Subscription expiry reminders</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {subscription_plan}, {expiry_date}, {next_billing_amount}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#owner-subscription-reminder-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Subscription Activation Email</h6>
                                                <p class="template-description">Subscription activation confirmation</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {subscription_plan}, {activation_date}, {expiry_date}, {monthly_charge}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#owner-subscription-activation-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Owner SMS Templates -->
                            <div class="col-md-6">
                                <div class="template-category">
                                    <h6 class="category-title">
                                        <i class="fas fa-sms text-success"></i> SMS Templates
                                    </h6>
                                    <div class="template-list">
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Welcome SMS</h6>
                                                <p class="template-description">Welcome SMS for new property owners</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#owner-welcome-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Payment Confirmation SMS</h6>
                                                <p class="template-description">Payment confirmation SMS for owners</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {payment_type}, {amount}, {transaction_id}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#owner-payment-confirmation-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Invoice Notification SMS</h6>
                                                <p class="template-description">Invoice notification SMS for owners</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {month}, {amount}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#owner-invoice-notification-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Subscription Reminder SMS</h6>
                                                <p class="template-description">Subscription expiry reminder SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {subscription_plan}, {expiry_date}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#owner-subscription-reminder-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Owner Subscription Activation SMS</h6>
                                                <p class="template-description">Subscription activation confirmation SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {owner_name}, {subscription_plan}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#owner-subscription-activation-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tenant Templates Group -->
                    <div class="template-group mb-5">
                        <div class="group-header">
                            <h5 class="group-title">
                                <i class="fas fa-users text-info"></i> Tenant Templates
                                <span class="badge badge-info ml-2">Tenant notifications</span>
                            </h5>
                            <p class="group-description">Notifications for tenants including welcome, rent reminders, payment confirmations, and lease management</p>
                        </div>
                        
                        <div class="row">
                            <!-- Tenant Email Templates -->
                            <div class="col-md-6">
                                <div class="template-category">
                                    <h6 class="category-title">
                                        <i class="fas fa-envelope text-info"></i> Email Templates
                                    </h6>
                                    <div class="template-list">
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Welcome Email</h6>
                                                <p class="template-description">Welcome message for new tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {unit_name}, {property_name}, {owner_email}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#tenant-welcome-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Rent Due Email</h6>
                                                <p class="template-description">Rent due reminder emails</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {amount}, {month}, {due_date}, {unit_name}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#rent-due-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Rent Paid Email</h6>
                                                <p class="template-description">Rent payment confirmation emails</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {amount}, {month}, {payment_date}, {unit_name}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#rent-paid-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Payment Confirmation Email</h6>
                                                <p class="template-description">General payment confirmation for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {payment_type}, {amount}, {payment_date}, {transaction_id}, {unit_name}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#tenant-payment-confirmation-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Invoice Notification Email</h6>
                                                <p class="template-description">Invoice notifications for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {month}, {amount}, {unit_name}, {property_name}, {owner_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#tenant-invoice-notification-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Subscription Reminder Email</h6>
                                                <p class="template-description">Subscription expiry reminders for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {subscription_plan}, {expiry_date}, {next_billing_amount}, {unit_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#tenant-subscription-reminder-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Subscription Activation Email</h6>
                                                <p class="template-description">Subscription activation confirmation for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {subscription_plan}, {activation_date}, {expiry_date}, {monthly_charge}, {unit_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.email.templates') }}#tenant-subscription-activation-email" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tenant SMS Templates -->
                            <div class="col-md-6">
                                <div class="template-category">
                                    <h6 class="category-title">
                                        <i class="fas fa-sms text-success"></i> SMS Templates
                                    </h6>
                                    <div class="template-list">
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Welcome SMS</h6>
                                                <p class="template-description">Welcome SMS for new tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {unit_name}, {property_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#welcome-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Rent Due SMS</h6>
                                                <p class="template-description">Rent due reminder SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {amount}, {month}, {due_date}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#rent-due-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Rent Paid SMS</h6>
                                                <p class="template-description">Rent payment confirmation SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {amount}, {month}, {payment_date}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#rent-paid-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Payment Confirmation SMS</h6>
                                                <p class="template-description">General payment confirmation SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {payment_type}, {amount}, {transaction_id}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#payment-confirmation-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Payment Confirmation SMS</h6>
                                                <p class="template-description">Tenant-specific payment confirmation SMS</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {payment_type}, {amount}, {unit_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#tenant-payment-confirmation-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Invoice Notification SMS</h6>
                                                <p class="template-description">Invoice notification SMS for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {month}, {amount}, {unit_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#tenant-invoice-notification-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Subscription Reminder SMS</h6>
                                                <p class="template-description">Subscription expiry reminder SMS for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {subscription_plan}, {expiry_date}, {unit_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#tenant-subscription-reminder-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="template-item">
                                            <div class="template-info">
                                                <h6 class="template-name">Tenant Subscription Activation SMS</h6>
                                                <p class="template-description">Subscription activation confirmation SMS for tenants</p>
                                                <div class="template-meta">
                                                    <span class="badge badge-success">Active</span>
                                                    <span class="template-placeholders">
                                                        <i class="fas fa-tags"></i> {tenant_name}, {subscription_plan}, {unit_name}, {company_name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="template-actions">
                                                <a href="{{ route('admin.settings.sms.templates') }}#tenant-subscription-activation-sms" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </div>
                                    <div class="action-content">
                                        <h6>Email Templates</h6>
                                        <p>Manage all email templates</p>
                                        <a href="{{ route('admin.settings.email.templates') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit Email Templates
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-sms text-success"></i>
                                    </div>
                                    <div class="action-content">
                                        <h6>SMS Templates</h6>
                                        <p>Manage all SMS templates</p>
                                        <a href="{{ route('admin.settings.sms.templates') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-edit"></i> Edit SMS Templates
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-cog text-warning"></i>
                                    </div>
                                    <div class="action-content">
                                        <h6>Notification Settings</h6>
                                        <p>Configure notification preferences</p>
                                        <a href="{{ route('admin.settings.notifications') }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-cog"></i> Notification Settings
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
</div>

<style>
.template-group {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    background: #fff;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.group-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fc;
}

.group-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.group-title i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

.group-description {
    color: #858796;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.template-category {
    margin-bottom: 1.5rem;
}

.category-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 1rem;
}

.category-title i {
    margin-right: 0.5rem;
    font-size: 1rem;
}

.template-list {
    space-y: 0.75rem;
}

.template-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    background: #f8f9fc;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.template-item:hover {
    background: #fff;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    transform: translateY(-1px);
}

.template-info {
    flex: 1;
}

.template-name {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.template-description {
    color: #858796;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.template-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.template-placeholders {
    color: #6c757d;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
}

.template-placeholders i {
    margin-right: 0.25rem;
}

.template-actions {
    margin-left: 1rem;
}

.quick-actions {
    background: #f8f9fc;
    padding: 1.5rem;
    border-radius: 0.5rem;
    border: 1px solid #e3e6f0;
}

.action-card {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #fff;
    border-radius: 0.35rem;
    border: 1px solid #e3e6f0;
    height: 100%;
    transition: all 0.3s ease;
}

.action-card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    transform: translateY(-2px);
}

.action-icon {
    margin-right: 1rem;
    font-size: 2rem;
}

.action-content {
    flex: 1;
}

.action-content h6 {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.action-content p {
    color: #858796;
    font-size: 0.85rem;
    margin-bottom: 0.75rem;
}

.badge {
    font-size: 0.7rem;
    font-weight: 500;
}

.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
}

.btn-outline-primary {
    border-color: #4e73df;
    color: #4e73df;
}

.btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
    color: #fff;
}
</style>
@endsection
