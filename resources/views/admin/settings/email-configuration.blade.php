@extends('layouts.admin')

@section('title', 'Email Configuration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope"></i> Email Configuration
                    </h3>
                    <p class="text-muted mb-0">Configure your email server settings and test email functionality</p>
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

                    <!-- Email Configuration Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cog"></i> SMTP Configuration
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.settings.email-configuration.update') }}" method="POST">
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
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input" id="email_enabled" name="email_enabled" value="1"
                                                               {{ ($emailSettings['email_enabled'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="email_enabled">
                                                            <i class="fas fa-toggle-on"></i> Enable Email Notifications
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">Enable or disable email notifications globally</small>
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
                                                <button type="button" class="btn btn-warning" onclick="debugEmailSettings()">
                                                    <i class="fas fa-bug"></i> Debug Settings
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="testEmailValidation()">
                                                    <i class="fas fa-vial"></i> Test Validation
                                                </button>
                                                <button type="button" class="btn btn-dark" onclick="testCsrf()">
                                                    <i class="fas fa-shield-alt"></i> Test CSRF
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Test Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-vial"></i> Email Test
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="test_email">
                                                    <i class="fas fa-envelope"></i> Test Email Address
                                                </label>
                                                <input type="email" class="form-control" id="test_email" 
                                                       value="{{ config('mail.from.address') }}" placeholder="Enter email address to test">
                                                <small class="form-text text-muted">Email address to send test email</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="button" class="btn btn-success" onclick="sendTestEmail()">
                                                        <i class="fas fa-paper-plane"></i> Send Test Email
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="test-result" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testEmail() {
    const testEmail = document.getElementById('test_email').value;
    
    fetch('{{ route("admin.settings.email-configuration.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            email: testEmail
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        const resultDiv = document.getElementById('test-result');
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Test email sent successfully!</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Failed to send test email: ' + data.message + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const resultDiv = document.getElementById('test-result');
        resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error sending test email: ' + error.message + '</div>';
    });
}

function sendTestEmail() {
    testEmail();
}

function debugEmailSettings() {
    fetch('{{ route("admin.settings.email-configuration.debug") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
            console.log('Email Settings Debug:', data);
            alert('Debug info logged to console. Check browser console for details.');
        } else {
            alert('Debug failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error debugging email settings: ' + error.message);
    });
}

function testEmailValidation() {
    // Test form validation
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Temporarily change submit button to test validation
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    submitButton.disabled = true;
    
    setTimeout(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        alert('Form validation test completed. Check browser console for validation details.');
    }, 2000);
}

function testCsrf() {
    // Test CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    alert('CSRF Token: ' + token.substring(0, 20) + '...');
}
</script>
@endsection 