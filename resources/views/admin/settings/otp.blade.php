@extends('layouts.admin')

@section('title', 'OTP Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">OTP System Settings</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.otp-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- OTP System Toggle -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1">OTP System Status</h5>
                                                <p class="mb-0 text-muted">Enable or disable the entire OTP system</p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled"
                                                       value="1" {{ $settings->is_enabled ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_enabled">
                                                    {{ $settings->is_enabled ? 'Enabled' : 'Disabled' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- OTP Configuration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="otp_length">OTP Length</label>
                                    <input type="number" class="form-control" id="otp_length" name="otp_length"
                                           value="{{ $settings->otp_length }}" min="4" max="8" required>
                                    <small class="form-text text-muted">Number of digits in OTP (4-8)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="otp_expiry_minutes">OTP Expiry (Minutes)</label>
                                    <input type="number" class="form-control" id="otp_expiry_minutes" name="otp_expiry_minutes"
                                           value="{{ $settings->otp_expiry_minutes }}" min="1" max="60" required>
                                    <small class="form-text text-muted">How long OTP remains valid</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_attempts">Maximum Attempts</label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                           value="{{ $settings->max_attempts }}" min="1" max="10" required>
                                    <small class="form-text text-muted">Maximum failed attempts allowed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="resend_cooldown_seconds">Resend Cooldown (Seconds)</label>
                                    <input type="number" class="form-control" id="resend_cooldown_seconds" name="resend_cooldown_seconds"
                                           value="{{ $settings->resend_cooldown_seconds }}" min="30" max="300" required>
                                    <small class="form-text text-muted">Wait time before resending OTP</small>
                                </div>
                            </div>
                        </div>

                        <!-- OTP Requirements -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>OTP Requirements</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require_otp_for_registration"
                                                   name="require_otp_for_registration" value="1"
                                                   {{ $settings->require_otp_for_registration ? 'checked' : '' }}>
                                            <label class="form-check-label" for="require_otp_for_registration">
                                                Registration
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require_otp_for_login"
                                                   name="require_otp_for_login" value="1"
                                                   {{ $settings->require_otp_for_login ? 'checked' : '' }}>
                                            <label class="form-check-label" for="require_otp_for_login">
                                                Login
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require_otp_for_password_reset"
                                                   name="require_otp_for_password_reset" value="1"
                                                   {{ $settings->require_otp_for_password_reset ? 'checked' : '' }}>
                                            <label class="form-check-label" for="require_otp_for_password_reset">
                                                Password Reset
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Template -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="otp_message_template">SMS Message Template</label>
                                    <textarea class="form-control" id="otp_message_template" name="otp_message_template"
                                              rows="3" maxlength="500">{{ $settings->otp_message_template }}</textarea>
                                    <small class="form-text text-muted">
                                        Use {otp} for OTP code and {minutes} for expiry time. Max 500 characters.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update toggle label when changed
    $('#is_enabled').change(function() {
        const label = $(this).next('label');
        const isChecked = $(this).is(':checked');
        label.text(isChecked ? 'Enabled' : 'Disabled');
        console.log('OTP System Status changed to:', isChecked ? 'Enabled' : 'Disabled');
    });

    // Update other toggle labels
    $('#require_otp_for_registration').change(function() {
        const isChecked = $(this).is(':checked');
        console.log('OTP Registration requirement changed to:', isChecked);
    });

    $('#require_otp_for_login').change(function() {
        const isChecked = $(this).is(':checked');
        console.log('OTP Login requirement changed to:', isChecked);
    });

    $('#require_otp_for_password_reset').change(function() {
        const isChecked = $(this).is(':checked');
        console.log('OTP Password Reset requirement changed to:', isChecked);
    });

    // Form submission with debugging
    $('form').submit(function(e) {
        console.log('Form submitted');
        console.log('Form data:', $(this).serialize());

        const otpLength = parseInt($('#otp_length').val());
        const expiryMinutes = parseInt($('#otp_expiry_minutes').val());
        const maxAttempts = parseInt($('#max_attempts').val());
        const cooldown = parseInt($('#resend_cooldown_seconds').val());

        // Log form values
        console.log('OTP Length:', otpLength);
        console.log('Expiry Minutes:', expiryMinutes);
        console.log('Max Attempts:', maxAttempts);
        console.log('Resend Cooldown:', cooldown);
        console.log('Is Enabled:', $('#is_enabled').is(':checked'));
        console.log('Require Registration:', $('#require_otp_for_registration').is(':checked'));
        console.log('Require Login:', $('#require_otp_for_login').is(':checked'));
        console.log('Require Password Reset:', $('#require_otp_for_password_reset').is(':checked'));

        if (otpLength < 4 || otpLength > 8) {
            alert('OTP Length must be between 4 and 8');
            e.preventDefault();
            return false;
        }

        if (expiryMinutes < 1 || expiryMinutes > 60) {
            alert('OTP Expiry must be between 1 and 60 minutes');
            e.preventDefault();
            return false;
        }

        if (maxAttempts < 1 || maxAttempts > 10) {
            alert('Maximum attempts must be between 1 and 10');
            e.preventDefault();
            return false;
        }

        if (cooldown < 30 || cooldown > 300) {
            alert('Resend cooldown must be between 30 and 300 seconds');
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        submitBtn.prop('disabled', true);

        // Re-enable button after 3 seconds (in case of error)
        setTimeout(function() {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }, 3000);
    });

    // Log initial state
    console.log('Initial OTP Settings:');
    console.log('Is Enabled:', $('#is_enabled').is(':checked'));
    console.log('Require Registration:', $('#require_otp_for_registration').is(':checked'));
    console.log('Require Login:', $('#require_otp_for_login').is(':checked'));
    console.log('Require Password Reset:', $('#require_otp_for_password_reset').is(':checked'));
});
</script>
@endpush
