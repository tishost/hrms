@extends('layouts.admin')

@section('title', 'Add New Owner')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Owner</h1>
        <a href="{{ route('admin.owners.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Owner List
        </a>
    </div>

    <!-- Owner Creation Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Owner Information</h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.owners.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                                                <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Phone Number
                                    </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                    <label for="country" class="form-label">
                                        <i class="fas fa-globe me-2"></i>Country
                                    </label>
                                    <select class="form-control @error('country') is-invalid @enderror"
                                            id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars me-2"></i>Gender
                                    </label>
                                    <select class="form-control @error('gender') is-invalid @enderror"
                                            id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="plan_id" class="form-label">
                                        <i class="fas fa-cube me-2"></i>Subscription Plan
                                    </label>
                                    <select class="form-control @error('plan_id') is-invalid @enderror"
                                            id="plan_id" name="plan_id" required>
                                        <option value="">Select a plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} - ৳{{ number_format($plan->price) }}/year
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Address
                                    </label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password" required>
                                                                                <button type="button" class="btn btn-outline-secondary" id="generatePassword"
                                                title="Generate Strong Password">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Confirm Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control"
                                               id="password_confirmation" name="password_confirmation" required>
                                                                                <button type="button" class="btn btn-outline-secondary" id="copyPassword"
                                                title="Copy Password to Clipboard">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Owner
                                </button>
                                <a href="{{ route('admin.owners.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Plan Information Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Plans</h6>
                </div>
                <div class="card-body">
                    @foreach($plans as $plan)
                    <div class="border rounded p-3 mb-3">
                        <h6 class="font-weight-bold text-primary">{{ $plan->name }}</h6>
                        <div class="text-success font-weight-bold mb-2">৳{{ number_format($plan->price) }}/year</div>
                        <div class="small text-muted">
                            <div><i class="fas fa-building me-1"></i> {{ $plan->properties_limit_text }} Properties</div>
                            <div><i class="fas fa-home me-1"></i> {{ $plan->units_limit_text }} Units</div>
                            <div><i class="fas fa-users me-1"></i> {{ $plan->tenants_limit_text }} Tenants</div>
                            <div><i class="fas fa-sms me-1"></i> {{ $plan->sms_notification ? 'SMS Included' : 'No SMS' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password generation function
    function generatePassword(length = 12) {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        return password;
    }

    // Generate password button
    const generateBtn = document.getElementById('generatePassword');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');

    if (generateBtn) {
        generateBtn.addEventListener('click', function() {
            const newPassword = generatePassword();
            passwordField.value = newPassword;
            confirmPasswordField.value = newPassword;

            // Show success message
            showNotification('Password generated successfully!', 'success');
        });
    }

        // Copy password button
    const copyBtn = document.getElementById('copyPassword');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            console.log('Copy button clicked');

            // Try to copy from password field first, then confirm password field
            const textToCopy = passwordField.value || confirmPasswordField.value;
            console.log('Text to copy:', textToCopy ? 'Has text' : 'No text');

            if (textToCopy) {
                // Modern clipboard API
                if (navigator.clipboard && window.isSecureContext) {
                    console.log('Using modern clipboard API');
                    navigator.clipboard.writeText(textToCopy).then(function() {
                        console.log('Clipboard API success');
                        showNotification('Password copied to clipboard!', 'success');
                    }).catch(function(err) {
                        console.error('Clipboard API failed:', err);
                        // Fallback method
                        fallbackCopyTextToClipboard(textToCopy);
                    });
                } else {
                    console.log('Using fallback copy method');
                    // Fallback for older browsers or non-secure contexts
                    fallbackCopyTextToClipboard(textToCopy);
                }
            } else {
                console.log('No text to copy');
                showNotification('No password to copy!', 'error');
            }
        });
    }

        // Fallback copy function for older browsers
    function fallbackCopyTextToClipboard(text) {
        console.log('Using fallback copy method');
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            console.log('execCommand result:', successful);
            if (successful) {
                showNotification('Password copied to clipboard!', 'success');
            } else {
                showNotification('Failed to copy password!', 'error');
            }
        } catch (err) {
            console.error('Fallback copy failed:', err);
            showNotification('Failed to copy password!', 'error');
        }

        document.body.removeChild(textArea);
    }

    // Notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
});
</script>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Phone number formatting
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            value = '+' + value;
        }
        $(this).val(value);
    });
});
</script>
@endpush
