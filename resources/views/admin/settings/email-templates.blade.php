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
                                    <div class="mb-3">
                                        <label for="subject-tenant-welcome" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject-tenant-welcome" name="tenant_welcome_email_subject_bangla" 
                                               value="{{ $allSettings['tenant_welcome_email_subject_bangla'] ?? 'স্বাগতম! আপনার ইউনিট প্রস্তুত' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content-tenant-welcome" class="form-label">Email Content</label>
                                        <textarea class="form-control" id="content-tenant-welcome" name="tenant_welcome_email_content_bangla" rows="8" required>{{ $allSettings['tenant_welcome_email_content_bangla'] ?? 'স্বাগতম {tenant_name}!

আপনার ইউনিট {unit_name} প্রস্তুত। আপনি এখন আপনার নতুন বাড়িতে প্রবেশ করতে পারেন।

বিস্তারিত তথ্য:
- প্রপার্টি: {property_name}
- ইউনিট: {unit_name}
- মালিকের ইমেইল: {owner_email}

ধন্যবাদ,
{company_name}' }}</textarea>
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
                                    <div class="mb-3">
                                        <label for="subject-rent-due" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject-rent-due" name="rent_due_email_subject_bangla" 
                                               value="{{ $allSettings['rent_due_email_subject_bangla'] ?? 'ভাড়া বাকি - {month} মাস' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content-rent-due" class="form-label">Email Content</label>
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
                                    <div class="mb-3">
                                        <label for="subject-rent-paid" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject-rent-paid" name="rent_paid_email_subject_bangla" 
                                               value="{{ $allSettings['rent_paid_email_subject_bangla'] ?? 'ভাড়া পরিশোধ নিশ্চিতকরণ' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content-rent-paid" class="form-label">Email Content</label>
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
                                    <div class="mb-3">
                                        <label for="subject-payment-confirmation" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject-payment-confirmation" name="payment_confirmation_email_subject_bangla" 
                                               value="{{ $allSettings['payment_confirmation_email_subject_bangla'] ?? 'পেমেন্ট নিশ্চিতকরণ' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content-payment-confirmation" class="form-label">Email Content</label>
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
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {payment_type}, {amount}, {payment_date}, {transaction_id}, {company_name}
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
    
    // Show/hide language-specific fields
    const banglaFields = document.querySelectorAll('[name$="_bangla"]');
    const englishFields = document.querySelectorAll('[name$="_english"]');
    
    if (lang === 'bangla') {
        banglaFields.forEach(field => field.style.display = 'block');
        englishFields.forEach(field => field.style.display = 'none');
    } else {
        banglaFields.forEach(field => field.style.display = 'none');
        englishFields.forEach(field => field.style.display = 'block');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    switchLanguage('bangla');
});
</script>
@endsection
