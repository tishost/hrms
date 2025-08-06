@extends('layouts.admin')

@section('title', 'Email Templates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-envelope"></i> Email Templates
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Language Selection -->
                    <div class="mb-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success active" onclick="switchLanguage('bangla')">
                                <i class="fas fa-language"></i> বাংলা (Bangla)
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="switchLanguage('english')">
                                <i class="fas fa-language"></i> English
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.notifications.template.save') }}">
                        @csrf
                        <input type="hidden" name="current_language" id="current-language" value="bangla">
                        
                        <!-- Tenant Welcome Email -->
                        <div class="template-section mb-4" id="tenant-welcome-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-plus"></i> Tenant Welcome Email (<span id="lang-indicator-bangla">বাংলা</span>)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-tenant-welcome" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-tenant-welcome" name="tenant_welcome_email_subject_bangla" 
                                                   value="{{ $allSettings['tenant_welcome_email_subject_bangla'] ?? 'স্বাগতম! আপনার ইউনিট প্রস্তুত' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-welcome" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-tenant-welcome" name="tenant_welcome_email_content_bangla" rows="8" required>{{ $allSettings['tenant_welcome_email_content_bangla'] ?? 'স্বাগতম {tenant_name}!

আপনার ইউনিট {unit_name} প্রস্তুত। আপনি এখন আপনার নতুন বাড়িতে প্রবেশ করতে পারেন।

বিস্তারিত তথ্য:
- প্রপার্টি: {property_name}
- ইউনিট: {unit_name}
- মালিকের ইমেইল: {owner_email}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-tenant-welcome-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-tenant-welcome-en" name="tenant_welcome_email_subject_english" 
                                                   value="{{ $allSettings['tenant_welcome_email_subject_english'] ?? 'Welcome! Your unit is ready' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-welcome-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-tenant-welcome-en" name="tenant_welcome_email_content_english" rows="8" required>{{ $allSettings['tenant_welcome_email_content_english'] ?? 'Welcome {tenant_name}!

Your unit {unit_name} is ready. You can now move into your new home.

Details:
- Property: {property_name}
- Unit: {unit_name}
- Owner Email: {owner_email}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {unit_name}, {property_name}, {owner_email}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rent Due Email -->
                        <div class="template-section mb-4" id="rent-due-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-times"></i> Rent Due Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-rent-due" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-rent-due" name="rent_due_email_subject_bangla" 
                                                   value="{{ $allSettings['rent_due_email_subject_bangla'] ?? 'ভাড়া বাকি - {month} মাস' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-rent-due" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-rent-due" name="rent_due_email_content_bangla" rows="8" required>{{ $allSettings['rent_due_email_content_bangla'] ?? 'প্রিয় {tenant_name},

{month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি। অনুগ্রহ করে সময়মতো পরিশোধ করুন।

বিস্তারিত:
- ইউনিট: {unit_name}
- প্রপার্টি: {property_name}
- বাকি ভাড়া: ৳{amount}
- শেষ তারিখ: {due_date}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-rent-due-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-rent-due-en" name="rent_due_email_subject_english" 
                                                   value="{{ $allSettings['rent_due_email_subject_english'] ?? 'Rent Due - {month}' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-rent-due-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-rent-due-en" name="rent_due_email_content_english" rows="8" required>{{ $allSettings['rent_due_email_content_english'] ?? 'Dear {tenant_name},

Rent of ৳{amount} for {month} is due on {due_date}. Please pay on time.

Details:
- Unit: {unit_name}
- Property: {property_name}
- Due Amount: ৳{amount}
- Due Date: {due_date}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {amount}, {month}, {due_date}, {unit_name}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rent Paid Email -->
                        <div class="template-section mb-4" id="rent-paid-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle"></i> Rent Paid Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-rent-paid" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-rent-paid" name="rent_paid_email_subject_bangla" 
                                                   value="{{ $allSettings['rent_paid_email_subject_bangla'] ?? 'ভাড়া পরিশোধ নিশ্চিতকরণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-rent-paid" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-rent-paid" name="rent_paid_email_content_bangla" rows="8" required>{{ $allSettings['rent_paid_email_content_bangla'] ?? 'প্রিয় {tenant_name},

আপনার {month} মাসের ভাড়া ৳{amount} সফলভাবে পরিশোধ হয়েছে। ধন্যবাদ!

বিস্তারিত:
- ইউনিট: {unit_name}
- প্রপার্টি: {property_name}
- পরিশোধিত ভাড়া: ৳{amount}
- পরিশোধের তারিখ: {payment_date}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-rent-paid-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-rent-paid-en" name="rent_paid_email_subject_english" 
                                                   value="{{ $allSettings['rent_paid_email_subject_english'] ?? 'Rent Payment Confirmation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-rent-paid-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-rent-paid-en" name="rent_paid_email_content_english" rows="8" required>{{ $allSettings['rent_paid_email_content_english'] ?? 'Dear {tenant_name},

Your rent payment of ৳{amount} for {month} has been successfully received. Thank you!

Details:
- Unit: {unit_name}
- Property: {property_name}
- Paid Amount: ৳{amount}
- Payment Date: {payment_date}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {amount}, {month}, {payment_date}, {unit_name}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Confirmation Email -->
                        <div class="template-section mb-4" id="payment-confirmation-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-credit-card"></i> Payment Confirmation Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-payment-confirmation" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-payment-confirmation" name="payment_confirmation_email_subject_bangla" 
                                                   value="{{ $allSettings['payment_confirmation_email_subject_bangla'] ?? 'পেমেন্ট নিশ্চিতকরণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-payment-confirmation" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-payment-confirmation" name="payment_confirmation_email_content_bangla" rows="8" required>{{ $allSettings['payment_confirmation_email_content_bangla'] ?? 'প্রিয় {tenant_name},

আপনার পেমেন্ট সফলভাবে সম্পন্ন হয়েছে।

বিস্তারিত:
- পেমেন্টের ধরন: {payment_type}
- পরিমাণ: ৳{amount}
- তারিখ: {payment_date}
- ট্রানজেকশন আইডি: {transaction_id}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-payment-confirmation-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-payment-confirmation-en" name="payment_confirmation_email_subject_english" 
                                                   value="{{ $allSettings['payment_confirmation_email_subject_english'] ?? 'Payment Confirmation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-payment-confirmation-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-payment-confirmation-en" name="payment_confirmation_email_content_english" rows="8" required>{{ $allSettings['payment_confirmation_email_content_english'] ?? 'Dear {tenant_name},

Your payment has been successfully completed.

Details:
- Payment Type: {payment_type}
- Amount: ৳{amount}
- Date: {payment_date}
- Transaction ID: {transaction_id}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {payment_type}, {amount}, {payment_date}, {transaction_id}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Reset Email -->
                        <div class="template-section mb-4" id="password-reset-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-key"></i> Password Reset Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-password-reset" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-password-reset" name="password_reset_email_subject_bangla" 
                                                   value="{{ $allSettings['password_reset_email_subject_bangla'] ?? 'পাসওয়ার্ড রিসেট অনুরোধ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-password-reset" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-password-reset" name="password_reset_email_content_bangla" rows="8" required>{{ $allSettings['password_reset_email_content_bangla'] ?? 'প্রিয় {user_name},

আপনার পাসওয়ার্ড রিসেট করার অনুরোধ পাওয়া গেছে।

আপনার OTP: {otp}

এই OTP 10 মিনিটের জন্য বৈধ। যদি আপনি এই অনুরোধ করেননি, তাহলে এই ইমেইল উপেক্ষা করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-password-reset-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-password-reset-en" name="password_reset_email_subject_english" 
                                                   value="{{ $allSettings['password_reset_email_subject_english'] ?? 'Password Reset Request' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-password-reset-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-password-reset-en" name="password_reset_email_content_english" rows="8" required>{{ $allSettings['password_reset_email_content_english'] ?? 'Dear {user_name},

A password reset request has been received for your account.

Your OTP: {otp}

This OTP is valid for 10 minutes. If you did not request this, please ignore this email.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {user_name}, {otp}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Payment Confirmation Email -->
                        <div class="template-section mb-4" id="owner-payment-confirmation-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-money-bill-wave"></i> Owner Payment Confirmation Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-owner-payment-confirmation" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-owner-payment-confirmation" name="owner_payment_confirmation_email_subject_bangla" 
                                                   value="{{ $allSettings['owner_payment_confirmation_email_subject_bangla'] ?? 'মালিক পেমেন্ট নিশ্চিতকরণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-payment-confirmation" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-owner-payment-confirmation" name="owner_payment_confirmation_email_content_bangla" rows="8" required>{{ $allSettings['owner_payment_confirmation_email_content_bangla'] ?? 'প্রিয় {owner_name},

আপনার পেমেন্ট সফলভাবে সম্পন্ন হয়েছে।

বিস্তারিত:
- পেমেন্টের ধরন: {payment_type}
- পরিমাণ: ৳{amount}
- তারিখ: {payment_date}
- ট্রানজেকশন আইডি: {transaction_id}
- প্রপার্টি: {property_name}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-owner-payment-confirmation-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-owner-payment-confirmation-en" name="owner_payment_confirmation_email_subject_english" 
                                                   value="{{ $allSettings['owner_payment_confirmation_email_subject_english'] ?? 'Owner Payment Confirmation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-payment-confirmation-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-owner-payment-confirmation-en" name="owner_payment_confirmation_email_content_english" rows="8" required>{{ $allSettings['owner_payment_confirmation_email_content_english'] ?? 'Dear {owner_name},

Your payment has been successfully completed.

Details:
- Payment Type: {payment_type}
- Amount: ৳{amount}
- Date: {payment_date}
- Transaction ID: {transaction_id}
- Property: {property_name}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {owner_name}, {payment_type}, {amount}, {payment_date}, {transaction_id}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Invoice Notification Email -->
                        <div class="template-section mb-4" id="owner-invoice-notification-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-invoice"></i> Owner Invoice Notification Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-owner-invoice-notification" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-owner-invoice-notification" name="owner_invoice_notification_email_subject_bangla" 
                                                   value="{{ $allSettings['owner_invoice_notification_email_subject_bangla'] ?? 'নতুন ইনভয়েস - {month}' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-invoice-notification" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-owner-invoice-notification" name="owner_invoice_notification_email_content_bangla" rows="8" required>{{ $allSettings['owner_invoice_notification_email_content_bangla'] ?? 'প্রিয় {owner_name},

আপনার জন্য একটি নতুন ইনভয়েস তৈরি হয়েছে।

বিস্তারিত:
- মাস: {month}
- পরিমাণ: ৳{amount}
- প্রপার্টি: {property_name}
- ইউনিট: {unit_name}
- টেন্যান্ট: {tenant_name}

অনুগ্রহ করে সময়মতো পরিশোধ করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-owner-invoice-notification-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-owner-invoice-notification-en" name="owner_invoice_notification_email_subject_english" 
                                                   value="{{ $allSettings['owner_invoice_notification_email_subject_english'] ?? 'New Invoice - {month}' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-invoice-notification-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-owner-invoice-notification-en" name="owner_invoice_notification_email_content_english" rows="8" required>{{ $allSettings['owner_invoice_notification_email_content_english'] ?? 'Dear {owner_name},

A new invoice has been generated for you.

Details:
- Month: {month}
- Amount: ৳{amount}
- Property: {property_name}
- Unit: {unit_name}
- Tenant: {tenant_name}

Please pay on time.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {owner_name}, {month}, {amount}, {property_name}, {unit_name}, {tenant_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Subscription Reminder Email -->
                        <div class="template-section mb-4" id="owner-subscription-reminder-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bell"></i> Owner Subscription Reminder Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-owner-subscription-reminder" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-owner-subscription-reminder" name="owner_subscription_reminder_email_subject_bangla" 
                                                   value="{{ $allSettings['owner_subscription_reminder_email_subject_bangla'] ?? 'সাবস্ক্রিপশন রিমাইন্ডার' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-subscription-reminder" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-owner-subscription-reminder" name="owner_subscription_reminder_email_content_bangla" rows="8" required>{{ $allSettings['owner_subscription_reminder_email_content_bangla'] ?? 'প্রিয় {owner_name},

আপনার সাবস্ক্রিপশন {expiry_date} তারিখে শেষ হবে।

বিস্তারিত:
- সাবস্ক্রিপশন প্ল্যান: {subscription_plan}
- বর্তমান স্ট্যাটাস: {current_status}
- শেষ তারিখ: {expiry_date}
- পরবর্তী বিলিং: ৳{next_billing_amount}

অনুগ্রহ করে সময়মতো রিনিউ করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-owner-subscription-reminder-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-owner-subscription-reminder-en" name="owner_subscription_reminder_email_subject_english" 
                                                   value="{{ $allSettings['owner_subscription_reminder_email_subject_english'] ?? 'Subscription Reminder' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-subscription-reminder-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-owner-subscription-reminder-en" name="owner_subscription_reminder_email_content_english" rows="8" required>{{ $allSettings['owner_subscription_reminder_email_content_english'] ?? 'Dear {owner_name},

Your subscription will expire on {expiry_date}.

Details:
- Subscription Plan: {subscription_plan}
- Current Status: {current_status}
- Expiry Date: {expiry_date}
- Next Billing: ৳{next_billing_amount}

Please renew on time.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {owner_name}, {subscription_plan}, {current_status}, {expiry_date}, {next_billing_amount}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Subscription Activation Email -->
                        <div class="template-section mb-4" id="owner-subscription-activation-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle"></i> Owner Subscription Activation Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-owner-subscription-activation" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-owner-subscription-activation" name="owner_subscription_activation_email_subject_bangla" 
                                                   value="{{ $allSettings['owner_subscription_activation_email_subject_bangla'] ?? 'সাবস্ক্রিপশন সক্রিয়করণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-subscription-activation" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-owner-subscription-activation" name="owner_subscription_activation_email_content_bangla" rows="8" required>{{ $allSettings['owner_subscription_activation_email_content_bangla'] ?? 'প্রিয় {owner_name},

আপনার সাবস্ক্রিপশন সফলভাবে সক্রিয় হয়েছে!

বিস্তারিত:
- সাবস্ক্রিপশন প্ল্যান: {subscription_plan}
- সক্রিয়করণ তারিখ: {activation_date}
- শেষ তারিখ: {expiry_date}
- মাসিক চার্জ: ৳{monthly_charge}

আপনার অ্যাকাউন্টে সব ফিচার এখন ব্যবহার করতে পারবেন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-owner-subscription-activation-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-owner-subscription-activation-en" name="owner_subscription_activation_email_subject_english" 
                                                   value="{{ $allSettings['owner_subscription_activation_email_subject_english'] ?? 'Subscription Activation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-subscription-activation-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-owner-subscription-activation-en" name="owner_subscription_activation_email_content_english" rows="8" required>{{ $allSettings['owner_subscription_activation_email_content_english'] ?? 'Dear {owner_name},

Your subscription has been successfully activated!

Details:
- Subscription Plan: {subscription_plan}
- Activation Date: {activation_date}
- Expiry Date: {expiry_date}
- Monthly Charge: ৳{monthly_charge}

You can now use all features in your account.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {owner_name}, {subscription_plan}, {activation_date}, {expiry_date}, {monthly_charge}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Welcome Email -->
                        <div class="template-section mb-4" id="owner-welcome-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-home"></i> Owner Welcome Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-owner-welcome" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-owner-welcome" name="owner_welcome_email_subject_bangla" 
                                                   value="{{ $allSettings['owner_welcome_email_subject_bangla'] ?? 'স্বাগতম! আপনার অ্যাকাউন্ট প্রস্তুত' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-welcome" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-owner-welcome" name="owner_welcome_email_content_bangla" rows="8" required>{{ $allSettings['owner_welcome_email_content_bangla'] ?? 'স্বাগতম {owner_name}!

আপনার অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে। আপনি এখন আপনার প্রপার্টি ব্যবস্থাপনা শুরু করতে পারেন।

বিস্তারিত:
- অ্যাকাউন্ট আইডি: {account_id}
- প্রপার্টি: {property_name}
- ইউনিট: {unit_name}

আপনার ড্যাশবোর্ডে প্রবেশ করতে লগইন করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-owner-welcome-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-owner-welcome-en" name="owner_welcome_email_subject_english" 
                                                   value="{{ $allSettings['owner_welcome_email_subject_english'] ?? 'Welcome! Your account is ready' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-welcome-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-owner-welcome-en" name="owner_welcome_email_content_english" rows="8" required>{{ $allSettings['owner_welcome_email_content_english'] ?? 'Welcome {owner_name}!

Your account has been successfully created. You can now start managing your properties.

Details:
- Account ID: {account_id}
- Property: {property_name}
- Unit: {unit_name}

Login to access your dashboard.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {owner_name}, {account_id}, {property_name}, {unit_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Payment Confirmation Email -->
                        <div class="template-section mb-4" id="tenant-payment-confirmation-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-money-bill-wave"></i> Tenant Payment Confirmation Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-tenant-payment-confirmation" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-tenant-payment-confirmation" name="tenant_payment_confirmation_email_subject_bangla" 
                                                   value="{{ $allSettings['tenant_payment_confirmation_email_subject_bangla'] ?? 'টেন্যান্ট পেমেন্ট নিশ্চিতকরণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-payment-confirmation" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-tenant-payment-confirmation" name="tenant_payment_confirmation_email_content_bangla" rows="8" required>{{ $allSettings['tenant_payment_confirmation_email_content_bangla'] ?? 'প্রিয় {tenant_name},

আপনার পেমেন্ট সফলভাবে সম্পন্ন হয়েছে।

বিস্তারিত:
- পেমেন্টের ধরন: {payment_type}
- পরিমাণ: ৳{amount}
- তারিখ: {payment_date}
- ট্রানজেকশন আইডি: {transaction_id}
- ইউনিট: {unit_name}
- প্রপার্টি: {property_name}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-tenant-payment-confirmation-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-tenant-payment-confirmation-en" name="tenant_payment_confirmation_email_subject_english" 
                                                   value="{{ $allSettings['tenant_payment_confirmation_email_subject_english'] ?? 'Tenant Payment Confirmation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-payment-confirmation-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-tenant-payment-confirmation-en" name="tenant_payment_confirmation_email_content_english" rows="8" required>{{ $allSettings['tenant_payment_confirmation_email_content_english'] ?? 'Dear {tenant_name},

Your payment has been successfully completed.

Details:
- Payment Type: {payment_type}
- Amount: ৳{amount}
- Date: {payment_date}
- Transaction ID: {transaction_id}
- Unit: {unit_name}
- Property: {property_name}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {payment_type}, {amount}, {payment_date}, {transaction_id}, {unit_name}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Invoice Notification Email -->
                        <div class="template-section mb-4" id="tenant-invoice-notification-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-invoice"></i> Tenant Invoice Notification Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-tenant-invoice-notification" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-tenant-invoice-notification" name="tenant_invoice_notification_email_subject_bangla" 
                                                   value="{{ $allSettings['tenant_invoice_notification_email_subject_bangla'] ?? 'নতুন ইনভয়েস - {month}' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-invoice-notification" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-tenant-invoice-notification" name="tenant_invoice_notification_email_content_bangla" rows="8" required>{{ $allSettings['tenant_invoice_notification_email_content_bangla'] ?? 'প্রিয় {tenant_name},

আপনার জন্য একটি নতুন ইনভয়েস তৈরি হয়েছে।

বিস্তারিত:
- মাস: {month}
- পরিমাণ: ৳{amount}
- ইউনিট: {unit_name}
- প্রপার্টি: {property_name}
- মালিক: {owner_name}

অনুগ্রহ করে সময়মতো পরিশোধ করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-tenant-invoice-notification-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-tenant-invoice-notification-en" name="tenant_invoice_notification_email_subject_english" 
                                                   value="{{ $allSettings['tenant_invoice_notification_email_subject_english'] ?? 'New Invoice - {month}' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-invoice-notification-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-tenant-invoice-notification-en" name="tenant_invoice_notification_email_content_english" rows="8" required>{{ $allSettings['tenant_invoice_notification_email_content_english'] ?? 'Dear {tenant_name},

A new invoice has been generated for you.

Details:
- Month: {month}
- Amount: ৳{amount}
- Unit: {unit_name}
- Property: {property_name}
- Owner: {owner_name}

Please pay on time.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {month}, {amount}, {unit_name}, {property_name}, {owner_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Subscription Reminder Email -->
                        <div class="template-section mb-4" id="tenant-subscription-reminder-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bell"></i> Tenant Subscription Reminder Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-tenant-subscription-reminder" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-tenant-subscription-reminder" name="tenant_subscription_reminder_email_subject_bangla" 
                                                   value="{{ $allSettings['tenant_subscription_reminder_email_subject_bangla'] ?? 'সাবস্ক্রিপশন রিমাইন্ডার' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-subscription-reminder" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-tenant-subscription-reminder" name="tenant_subscription_reminder_email_content_bangla" rows="8" required>{{ $allSettings['tenant_subscription_reminder_email_content_bangla'] ?? 'প্রিয় {tenant_name},

আপনার সাবস্ক্রিপশন {expiry_date} তারিখে শেষ হবে।

বিস্তারিত:
- সাবস্ক্রিপশন প্ল্যান: {subscription_plan}
- বর্তমান স্ট্যাটাস: {current_status}
- শেষ তারিখ: {expiry_date}
- পরবর্তী বিলিং: ৳{next_billing_amount}
- ইউনিট: {unit_name}

অনুগ্রহ করে সময়মতো রিনিউ করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-tenant-subscription-reminder-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-tenant-subscription-reminder-en" name="tenant_subscription_reminder_email_subject_english" 
                                                   value="{{ $allSettings['tenant_subscription_reminder_email_subject_english'] ?? 'Subscription Reminder' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-subscription-reminder-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-tenant-subscription-reminder-en" name="tenant_subscription_reminder_email_content_english" rows="8" required>{{ $allSettings['tenant_subscription_reminder_email_content_english'] ?? 'Dear {tenant_name},

Your subscription will expire on {expiry_date}.

Details:
- Subscription Plan: {subscription_plan}
- Current Status: {current_status}
- Expiry Date: {expiry_date}
- Next Billing: ৳{next_billing_amount}
- Unit: {unit_name}

Please renew on time.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {subscription_plan}, {current_status}, {expiry_date}, {next_billing_amount}, {unit_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Subscription Activation Email -->
                        <div class="template-section mb-4" id="tenant-subscription-activation-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle"></i> Tenant Subscription Activation Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-tenant-subscription-activation" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-tenant-subscription-activation" name="tenant_subscription_activation_email_subject_bangla" 
                                                   value="{{ $allSettings['tenant_subscription_activation_email_subject_bangla'] ?? 'সাবস্ক্রিপশন সক্রিয়করণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-subscription-activation" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-tenant-subscription-activation" name="tenant_subscription_activation_email_content_bangla" rows="8" required>{{ $allSettings['tenant_subscription_activation_email_content_bangla'] ?? 'প্রিয় {tenant_name},

আপনার সাবস্ক্রিপশন সফলভাবে সক্রিয় হয়েছে!

বিস্তারিত:
- সাবস্ক্রিপশন প্ল্যান: {subscription_plan}
- সক্রিয়করণ তারিখ: {activation_date}
- শেষ তারিখ: {expiry_date}
- মাসিক চার্জ: ৳{monthly_charge}
- ইউনিট: {unit_name}

আপনার অ্যাকাউন্টে সব ফিচার এখন ব্যবহার করতে পারবেন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-tenant-subscription-activation-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-tenant-subscription-activation-en" name="tenant_subscription_activation_email_subject_english" 
                                                   value="{{ $allSettings['tenant_subscription_activation_email_subject_english'] ?? 'Subscription Activation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-tenant-subscription-activation-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-tenant-subscription-activation-en" name="tenant_subscription_activation_email_content_english" rows="8" required>{{ $allSettings['tenant_subscription_activation_email_content_english'] ?? 'Dear {tenant_name},

Your subscription has been successfully activated!

Details:
- Subscription Plan: {subscription_plan}
- Activation Date: {activation_date}
- Expiry Date: {expiry_date}
- Monthly Charge: ৳{monthly_charge}
- Unit: {unit_name}

You can now use all features in your account.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {subscription_plan}, {activation_date}, {expiry_date}, {monthly_charge}, {unit_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Reset Email -->
                        <div class="template-section mb-4" id="password-reset-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-key"></i> Password Reset Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-password-reset" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-password-reset" name="password_reset_email_subject_bangla" 
                                                   value="{{ $allSettings['password_reset_email_subject_bangla'] ?? 'পাসওয়ার্ড রিসেট অনুরোধ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-password-reset" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-password-reset" name="password_reset_email_content_bangla" rows="8" required>{{ $allSettings['password_reset_email_content_bangla'] ?? 'প্রিয় {user_name},

আপনার পাসওয়ার্ড রিসেট করার অনুরোধ পাওয়া গেছে।

আপনার OTP: {otp}

এই OTP 10 মিনিটের জন্য বৈধ। যদি আপনি এই অনুরোধ করেননি, তাহলে এই ইমেইল উপেক্ষা করুন।

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-password-reset-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-password-reset-en" name="password_reset_email_subject_english" 
                                                   value="{{ $allSettings['password_reset_email_subject_english'] ?? 'Password Reset Request' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-password-reset-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-password-reset-en" name="password_reset_email_content_english" rows="8" required>{{ $allSettings['password_reset_email_content_english'] ?? 'Dear {user_name},

A password reset request has been received for your account.

Your OTP: {otp}

This OTP is valid for 10 minutes. If you did not request this, please ignore this email.

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {user_name}, {otp}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Payment Confirmation Email -->
                        <div class="template-section mb-4" id="owner-payment-confirmation-email">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-money-bill-wave"></i> Owner Payment Confirmation Email (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bangla Content -->
                                    <div class="bangla-content">
                                        <div class="mb-3">
                                            <label for="subject-owner-payment-confirmation" class="form-label">Subject (বাংলা)</label>
                                            <input type="text" class="form-control" id="subject-owner-payment-confirmation" name="owner_payment_confirmation_email_subject_bangla" 
                                                   value="{{ $allSettings['owner_payment_confirmation_email_subject_bangla'] ?? 'মালিক পেমেন্ট নিশ্চিতকরণ' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-payment-confirmation" class="form-label">Email Content (বাংলা)</label>
                                            <textarea class="form-control" id="content-owner-payment-confirmation" name="owner_payment_confirmation_email_content_bangla" rows="8" required>{{ $allSettings['owner_payment_confirmation_email_content_bangla'] ?? 'প্রিয় {owner_name},

আপনার পেমেন্ট সফলভাবে সম্পন্ন হয়েছে।

বিস্তারিত:
- পেমেন্টের ধরন: {payment_type}
- পরিমাণ: ৳{amount}
- তারিখ: {payment_date}
- ট্রানজেকশন আইডি: {transaction_id}
- প্রপার্টি: {property_name}

ধন্যবাদ,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- English Content -->
                                    <div class="english-content" style="display: none;">
                                        <div class="mb-3">
                                            <label for="subject-owner-payment-confirmation-en" class="form-label">Subject (English)</label>
                                            <input type="text" class="form-control" id="subject-owner-payment-confirmation-en" name="owner_payment_confirmation_email_subject_english" 
                                                   value="{{ $allSettings['owner_payment_confirmation_email_subject_english'] ?? 'Owner Payment Confirmation' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content-owner-payment-confirmation-en" class="form-label">Email Content (English)</label>
                                            <textarea class="form-control" id="content-owner-payment-confirmation-en" name="owner_payment_confirmation_email_content_english" rows="8" required>{{ $allSettings['owner_payment_confirmation_email_content_english'] ?? 'Dear {owner_name},

Your payment has been successfully completed.

Details:
- Payment Type: {payment_type}
- Amount: ৳{amount}
- Date: {payment_date}
- Transaction ID: {transaction_id}
- Property: {property_name}

Thank you,
{company_name}' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {owner_name}, {payment_type}, {amount}, {payment_date}, {transaction_id}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Email Templates
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.template-section {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    background: #fff;
}

.template-section .card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.template-section .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
}

.template-section .card-header h5 {
    margin: 0;
    font-weight: 600;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.btn-group .btn {
    border-radius: 0.35rem;
    font-weight: 600;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}
</style>

<script>
let currentLanguage = 'bangla';

function switchLanguage(lang) {
    currentLanguage = lang;
    
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-success', 'active');
        btn.classList.add('btn-outline-secondary');
    });
    
    event.target.classList.remove('btn-outline-secondary');
    event.target.classList.add('btn-success', 'active');
    
    // Update language indicators
    const langIndicators = document.querySelectorAll('[id^="lang-indicator-"]');
    langIndicators.forEach(indicator => {
        indicator.textContent = lang === 'bangla' ? 'বাংলা' : 'English';
    });
    
    // Update template titles
    const templateTitles = document.querySelectorAll('.template-section .card-header h5');
    templateTitles.forEach(title => {
        const icon = title.querySelector('i');
        const baseText = title.textContent.replace(/\(বাংলা\)|\(English\)/, '');
        title.innerHTML = icon.outerHTML + ' ' + baseText + ` (${lang === 'bangla' ? 'বাংলা' : 'English'})`;
    });
    
    // Update hidden input
    document.getElementById('current-language').value = lang;
    
    // Show/hide language-specific content
    const banglaContents = document.querySelectorAll('.bangla-content');
    const englishContents = document.querySelectorAll('.english-content');
    
    if (lang === 'bangla') {
        banglaContents.forEach(content => content.style.display = 'block');
        englishContents.forEach(content => content.style.display = 'none');
    } else {
        banglaContents.forEach(content => content.style.display = 'none');
        englishContents.forEach(content => content.style.display = 'block');
    }
    
    // Dispatch event for any additional updates
    document.dispatchEvent(new Event('languageChanged'));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    switchLanguage('bangla');
});
</script>
@endsection
