@extends('layouts.owner')
@section('title', 'Settings')

@section('content')
<style>
    /* Owner Tab Styling - Bootstrap 5 Compatible */
    .nav-tabs .nav-link {
        color: #2c3e50 !important;
        border: none !important;
        border-radius: 8px !important;
        margin: 0 4px !important;
        padding: 12px 20px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        background-color: transparent !important;
    }

    .nav-tabs .nav-link:hover {
        background-color: #ecf0f1 !important;
        color: #2c3e50 !important;
        border: none !important;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3) !important;
    }

    .nav-tabs .nav-link i {
        margin-right: 8px !important;
    }

    /* Ensure tab content is visible */
    .tab-content {
        padding-top: 20px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .tab-pane.show {
        display: block;
    }

    /* Override any conflicting Bootstrap styles */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
</style>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>⚙️ Settings</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Settings</li>
            </ul>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="true">
                        <i class="fas fa-bell"></i> Notification Settings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms" type="button" role="tab" aria-controls="sms" aria-selected="false">
                        <i class="fas fa-sms"></i> SMS Template
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab" aria-controls="email" aria-selected="false">
                        <i class="fas fa-envelope"></i> Email Template
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('owner.settings.update') }}">
                @csrf
                @method('PUT')
                
                <div class="tab-content" id="settingsTabContent">
                    <!-- Notification Settings Tab -->
                    <div class="tab-pane fade show active" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                        <div class="row">
                            <!-- Language Preference -->
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-language"></i> Language Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Notification Language</label>
                                                <select name="notification_language" class="form-control">
                                                    <option value="bangla" {{ ($allSettings['notification_language'] ?? 'bangla') == 'bangla' ? 'selected' : '' }}>
                                                        বাংলা (Bangla)
                                                    </option>
                                                    <option value="english" {{ ($allSettings['notification_language'] ?? 'bangla') == 'english' ? 'selected' : '' }}>
                                                        English
                                                    </option>
                                                </select>
                                                <small class="text-muted">
                                                    Choose the language for SMS and email notifications sent to tenants
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Toggles -->
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-bell"></i> Notification Preferences
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_rent_due" class="form-check-input" value="1" {{ ($allSettings['notify_rent_due'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify when rent is due</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_rent_paid" class="form-check-input" value="1" {{ ($allSettings['notify_rent_paid'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify when rent is paid</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_new_tenant" class="form-check-input" value="1" {{ ($allSettings['notify_new_tenant'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify when new tenant is added</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_checkout" class="form-check-input" value="1" {{ ($allSettings['notify_checkout'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify when tenant checks out</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_late_payment" class="form-check-input" value="1" {{ ($allSettings['notify_late_payment'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify for late payments</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="auto_send_reminders" class="form-check-input" value="1" {{ ($allSettings['auto_send_reminders'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Auto send reminders</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_maintenance" class="form-check-input" value="1" {{ ($allSettings['notify_maintenance'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify for maintenance updates</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="notify_lease_expiry" class="form-check-input" value="1" {{ ($allSettings['notify_lease_expiry'] ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Notify for lease expiry</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reminder Settings -->
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-clock"></i> Reminder Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Rent Due Reminder (Days)</label>
                                                <input type="number" name="rent_due_reminder_days" class="form-control" value="{{ $allSettings['rent_due_reminder_days'] ?? 7 }}" min="1" max="30">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Late Payment Reminder (Days)</label>
                                                <input type="number" name="late_payment_reminder_days" class="form-control" value="{{ $allSettings['late_payment_reminder_days'] ?? 3 }}" min="1" max="30">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Lease Expiry Reminder (Days)</label>
                                                <input type="number" name="lease_expiry_reminder_days" class="form-control" value="{{ $allSettings['lease_expiry_reminder_days'] ?? 30 }}" min="1" max="90">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Template Tab -->
                    <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
                        <!-- SMS Template Language Tabs -->
                        <ul class="nav nav-tabs mb-3" id="smsLanguageTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="sms-bangla-tab" data-bs-toggle="tab" data-bs-target="#sms-bangla" type="button" role="tab">
                                    <i class="fas fa-language"></i> বাংলা (Bangla)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sms-english-tab" data-bs-toggle="tab" data-bs-target="#sms-english" type="button" role="tab">
                                    <i class="fas fa-language"></i> English
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="smsLanguageTabContent">
                            <!-- Bangla SMS Templates -->
                            <div class="tab-pane fade show active" id="sms-bangla" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tenant Welcome SMS (বাংলা)</label>
                                        <textarea name="tenant_welcome_sms_template_bangla" class="form-control" rows="4" placeholder="স্বাগতম {tenant_name}! আপনার ইউনিট {unit_name} প্রস্তুত।">{{ $allSettings['tenant_welcome_sms_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {property_name}, {owner_phone}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Due SMS (বাংলা)</label>
                                        <textarea name="rent_due_sms_template_bangla" class="form-control" rows="4" placeholder="প্রিয় {tenant_name}, {month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি।">{{ $allSettings['rent_due_sms_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {due_date}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Paid SMS (বাংলা)</label>
                                        <textarea name="rent_paid_sms_template_bangla" class="form-control" rows="4" placeholder="ধন্যবাদ {tenant_name}! {month} মাসের ভাড়া ৳{amount} পাওয়া গেছে।">{{ $allSettings['rent_paid_sms_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {receipt_no}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Checkout SMS (বাংলা)</label>
                                        <textarea name="checkout_sms_template_bangla" class="form-control" rows="4" placeholder="প্রিয় {tenant_name}, {unit_name} এর চেকআউট সম্পন্ন।">{{ $allSettings['checkout_sms_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {deposit_amount}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- English SMS Templates -->
                            <div class="tab-pane fade" id="sms-english" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tenant Welcome SMS (English)</label>
                                        <textarea name="tenant_welcome_sms_template_english" class="form-control" rows="4" placeholder="Welcome {tenant_name}! Your unit {unit_name} at {property_name} is ready.">{{ $allSettings['tenant_welcome_sms_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {property_name}, {owner_phone}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Due SMS (English)</label>
                                        <textarea name="rent_due_sms_template_english" class="form-control" rows="4" placeholder="Dear {tenant_name}, rent of ৳{amount} for {month} is due on {due_date}.">{{ $allSettings['rent_due_sms_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {due_date}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Paid SMS (English)</label>
                                        <textarea name="rent_paid_sms_template_english" class="form-control" rows="4" placeholder="Thank you {tenant_name}! Rent payment of ৳{amount} for {month} received.">{{ $allSettings['rent_paid_sms_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {receipt_no}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Checkout SMS (English)</label>
                                        <textarea name="checkout_sms_template_english" class="form-control" rows="4" placeholder="Dear {tenant_name}, checkout process completed for {unit_name}.">{{ $allSettings['checkout_sms_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {deposit_amount}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Template Tab -->
                    <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                        <!-- Email Template Language Tabs -->
                        <ul class="nav nav-tabs mb-3" id="emailLanguageTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="email-bangla-tab" data-bs-toggle="tab" data-bs-target="#email-bangla" type="button" role="tab">
                                    <i class="fas fa-language"></i> বাংলা (Bangla)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="email-english-tab" data-bs-toggle="tab" data-bs-target="#email-english" type="button" role="tab">
                                    <i class="fas fa-language"></i> English
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="emailLanguageTabContent">
                            <!-- Bangla Email Templates -->
                            <div class="tab-pane fade show active" id="email-bangla" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tenant Welcome Email (বাংলা)</label>
                                        <textarea name="tenant_welcome_email_template_bangla" class="form-control" rows="4" placeholder="স্বাগতম {tenant_name}! আপনার ইউনিট {unit_name} প্রস্তুত।">{{ $allSettings['tenant_welcome_email_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {property_name}, {owner_email}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Due Email (বাংলা)</label>
                                        <textarea name="rent_due_email_template_bangla" class="form-control" rows="4" placeholder="প্রিয় {tenant_name}, {month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি।">{{ $allSettings['rent_due_email_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {due_date}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Paid Email (বাংলা)</label>
                                        <textarea name="rent_paid_email_template_bangla" class="form-control" rows="4" placeholder="ধন্যবাদ {tenant_name}! {month} মাসের ভাড়া ৳{amount} পাওয়া গেছে।">{{ $allSettings['rent_paid_email_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {receipt_no}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Checkout Email (বাংলা)</label>
                                        <textarea name="checkout_email_template_bangla" class="form-control" rows="4" placeholder="প্রিয় {tenant_name}, {unit_name} এর চেকআউট সম্পন্ন।">{{ $allSettings['checkout_email_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {deposit_amount}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lease Expiry Email (বাংলা)</label>
                                        <textarea name="lease_expiry_email_template_bangla" class="form-control" rows="4" placeholder="প্রিয় {tenant_name}, আপনার {unit_name} এর লিজ {expiry_date} তারিখে শেষ।">{{ $allSettings['lease_expiry_email_template_bangla'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {expiry_date}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- English Email Templates -->
                            <div class="tab-pane fade" id="email-english" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tenant Welcome Email (English)</label>
                                        <textarea name="tenant_welcome_email_template_english" class="form-control" rows="4" placeholder="Welcome {tenant_name}! Your unit {unit_name} at {property_name} is ready.">{{ $allSettings['tenant_welcome_email_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {property_name}, {owner_email}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Due Email (English)</label>
                                        <textarea name="rent_due_email_template_english" class="form-control" rows="4" placeholder="Dear {tenant_name}, rent of ৳{amount} for {month} is due on {due_date}.">{{ $allSettings['rent_due_email_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {due_date}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rent Paid Email (English)</label>
                                        <textarea name="rent_paid_email_template_english" class="form-control" rows="4" placeholder="Thank you {tenant_name}! Rent payment of ৳{amount} for {month} received.">{{ $allSettings['rent_paid_email_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {amount}, {month}, {receipt_no}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Checkout Email (English)</label>
                                        <textarea name="checkout_email_template_english" class="form-control" rows="4" placeholder="Dear {tenant_name}, checkout process completed for {unit_name}.">{{ $allSettings['checkout_email_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {deposit_amount}</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lease Expiry Email (English)</label>
                                        <textarea name="lease_expiry_email_template_english" class="form-control" rows="4" placeholder="Dear {tenant_name}, your lease for {unit_name} expires on {expiry_date}.">{{ $allSettings['lease_expiry_email_template_english'] ?? '' }}</textarea>
                                        <small class="text-muted">Available placeholders: {tenant_name}, {unit_name}, {expiry_date}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Initialize Bootstrap tabs
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
});
</script>
@endsection 