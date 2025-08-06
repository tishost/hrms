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

                                         <!-- Welcome SMS -->
                     <div class="template-section mb-4" id="welcome-sms">
                         <div class="card">
                             <div class="card-header">
                                 <h5 class="mb-0">
                                     <i class="fas fa-user-plus"></i> Welcome SMS (<span id="lang-indicator-bangla">বাংলা</span>)
                                 </h5>
                             </div>
                             <div class="card-body">
                                 <form id="welcome-sms-form" onsubmit="saveSmsTemplate('welcome_sms', currentLanguage, event)">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content-welcome-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-welcome-sms" name="content" rows="4" maxlength="160" required>স্বাগতম {tenant_name}! আপনার ইউনিট {unit_name} প্রস্তুত। {company_name}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-welcome-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {unit_name}, {property_name}, {company_name}
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Template
                                    </button>
                                </form>
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
                                <form id="rent-due-sms-form" onsubmit="saveSmsTemplate('rent_due_sms', 'bangla', event)">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content-rent-due-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-rent-due-sms" name="content" rows="4" maxlength="160" required>প্রিয় {tenant_name}, {month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি। {company_name}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-rent-due-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {amount}, {month}, {due_date}, {company_name}
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Template
                                    </button>
                                </form>
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
                                <form id="rent-paid-sms-form" onsubmit="saveSmsTemplate('rent_paid_sms', 'bangla', event)">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content-rent-paid-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-rent-paid-sms" name="content" rows="4" maxlength="160" required>প্রিয় {tenant_name}, আপনার {month} মাসের ভাড়া ৳{amount} সফলভাবে পরিশোধ হয়েছে। ধন্যবাদ! {company_name}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-rent-paid-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {amount}, {month}, {payment_date}, {company_name}
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Template
                                    </button>
                                </form>
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
                                <form id="payment-confirmation-sms-form" onsubmit="saveSmsTemplate('payment_confirmation_sms', 'bangla', event)">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content-payment-confirmation-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-payment-confirmation-sms" name="content" rows="4" maxlength="160" required>প্রিয় {tenant_name}, আপনার পেমেন্ট ৳{amount} সফলভাবে সম্পন্ন হয়েছে। ট্রানজেকশন: {transaction_id} {company_name}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-payment-confirmation-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {tenant_name}, {payment_type}, {amount}, {transaction_id}, {company_name}
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Template
                                    </button>
                                </form>
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
                                <form id="password-reset-otp-sms-form" onsubmit="saveSmsTemplate('password_reset_otp_sms', 'bangla', event)">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content-password-reset-otp-sms" class="form-label">SMS Content</label>
                                        <textarea class="form-control" id="content-password-reset-otp-sms" name="content" rows="4" maxlength="160" required>আপনার পাসওয়ার্ড রিসেট OTP: {otp}। এই OTP 10 মিনিটের জন্য বৈধ। {company_name}</textarea>
                                        <div class="form-text">
                                            <span id="char-count-password-reset-otp-sms" class="text-muted">0/160</span> characters
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Available placeholders:</strong> {otp}, {company_name}
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Template
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

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
    
    // Load templates for selected language
    loadTemplatesForLanguage(lang);
}

function loadTemplatesForLanguage(lang) {
    // Load saved templates for the selected language
    const templates = [
        'welcome_sms',
        'rent_due_sms', 
        'rent_paid_sms',
        'payment_confirmation_sms',
        'password_reset_otp_sms'
    ];
    
    templates.forEach(template => {
        loadSmsTemplate(template, lang);
    });
}

function loadSmsTemplate(templateName, lang) {
    fetch(`/admin/settings/notifications/template?template=${templateName}_${lang}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.template) {
            const formId = templateName.replace(/_/g, '-') + '-form';
            const form = document.getElementById(formId);
            if (form) {
                const contentTextarea = form.querySelector('textarea[name="content"]');
                if (contentTextarea) {
                    contentTextarea.value = data.template.content || '';
                    updateCharCount(templateName.replace(/_/g, '-'));
                }
            }
        }
    })
    .catch(error => {
        console.error('Error loading template:', error);
    });
}

function saveSmsTemplate(templateName, lang, event) {
    event.preventDefault();
    
    const form = event.target;
    const content = form.querySelector('textarea[name="content"]').value;
    
    if (!content.trim()) {
        alert('Please enter SMS content');
        return;
    }
    
    if (content.length > 160) {
        alert('SMS content cannot exceed 160 characters');
        return;
    }
    
    const formData = new FormData(form);
    formData.append('template_name', `${templateName}_${lang}`);
    
    fetch('/admin/settings/notifications/template', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle"></i> Template saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.parentNode.insertBefore(alertDiv, form);
            
            // Auto-remove alert after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        } else {
            alert('Failed to save template: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        alert('Error saving template: ' + error.message);
    });
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

// Load templates on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTemplatesForLanguage(currentLanguage);
    
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
