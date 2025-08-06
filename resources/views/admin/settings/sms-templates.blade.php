@extends('layouts.admin')

@section('title', 'SMS Templates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-sms"></i> SMS Templates
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
                        
                        <!-- Welcome SMS -->
                        <div class="template-section mb-4" id="welcome-sms">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-plus"></i> Welcome SMS (<span id="lang-indicator-bangla">বাংলা</span>)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content-welcome-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-welcome-sms" name="welcome_sms_content_bangla" rows="4" maxlength="160" required>{{ $allSettings['welcome_sms_content_bangla'] ?? 'স্বাগতম {tenant_name}! আপনার ইউনিট {unit_name} প্রস্তুত। {company_name}' }}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-welcome-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {unit_name}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rent Due SMS -->
                        <div class="template-section mb-4" id="rent-due-sms">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-times"></i> Rent Due SMS (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content-rent-due-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-rent-due-sms" name="rent_due_sms_content_bangla" rows="4" maxlength="160" required>{{ $allSettings['rent_due_sms_content_bangla'] ?? 'প্রিয় {tenant_name}, {month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি। {company_name}' }}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-rent-due-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {amount}, {month}, {due_date}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rent Paid SMS -->
                        <div class="template-section mb-4" id="rent-paid-sms">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle"></i> Rent Paid SMS (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content-rent-paid-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-rent-paid-sms" name="rent_paid_sms_content_bangla" rows="4" maxlength="160" required>{{ $allSettings['rent_paid_sms_content_bangla'] ?? 'প্রিয় {tenant_name}, আপনার {month} মাসের ভাড়া ৳{amount} সফলভাবে পরিশোধ হয়েছে। ধন্যবাদ! {company_name}' }}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-rent-paid-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {amount}, {month}, {payment_date}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Confirmation SMS -->
                        <div class="template-section mb-4" id="payment-confirmation-sms">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-credit-card"></i> Payment Confirmation SMS (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content-payment-confirmation-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-payment-confirmation-sms" name="payment_confirmation_sms_content_bangla" rows="4" maxlength="160" required>{{ $allSettings['payment_confirmation_sms_content_bangla'] ?? 'প্রিয় {tenant_name}, আপনার পেমেন্ট ৳{amount} সফলভাবে সম্পন্ন হয়েছে। ট্রানজেকশন: {transaction_id} {company_name}' }}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-payment-confirmation-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {payment_type}, {amount}, {transaction_id}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Reset OTP SMS -->
                        <div class="template-section mb-4" id="password-reset-otp-sms">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-key"></i> Password Reset OTP SMS (বাংলা)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content-password-reset-otp-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-password-reset-otp-sms" name="password_reset_otp_sms_content_bangla" rows="4" maxlength="160" required>{{ $allSettings['password_reset_otp_sms_content_bangla'] ?? 'আপনার পাসওয়ার্ড রিসেট OTP: {otp}। এই OTP 10 মিনিটের জন্য বৈধ। {company_name}' }}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-password-reset-otp-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {otp}, {company_name}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save SMS Templates
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

.text-danger {
    color: #dc3545 !important;
}

.text-warning {
    color: #ffc107 !important;
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

function updateCharCount(templateName) {
    const textarea = document.getElementById(`content-${templateName}`);
    const charCount = document.getElementById(`char-count-${templateName}`);
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    switchLanguage('bangla');
    
    // Add character counters for all SMS textareas
    const smsTextareas = document.querySelectorAll('textarea[maxlength="160"]');
    smsTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const templateName = this.id.replace('content-', '');
            updateCharCount(templateName);
        });
        
        // Initialize character count
        const templateName = textarea.id.replace('content-', '');
        updateCharCount(templateName);
    });
});
</script>
@endsection
