<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - HRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .forgot-password-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .logo-section p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            z-index: 2;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px 12px 45px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-to-login a:hover {
            color: #764ba2;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .text-danger {
            color: #dc3545 !important;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .description {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .reset-options {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .reset-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .reset-option.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        }

        .reset-option:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .reset-option i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .reset-option h6 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }

        .reset-option p {
            margin: 5px 0 0 0;
            font-size: 0.8rem;
            color: #666;
        }

        .otp-section {
            display: none;
        }

        .otp-section.show {
            display: block;
        }

        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
        }

        .otp-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .resend-otp {
            text-align: center;
            margin-top: 15px;
        }

        .resend-otp a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .resend-otp a:hover {
            color: #764ba2;
        }

        .countdown {
            color: #666;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .forgot-password-container {
                margin: 10px;
                padding: 30px 20px;
            }
            
            .reset-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="logo-section">
            <h2><i class="fas fa-key"></i> Forgot Password</h2>
            <p>HRMS - Property Management System</p>
        </div>

        <div class="description">
            Choose how you want to reset your password. You can use either your email address or mobile number.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <!-- Reset Options -->
        <div class="reset-options">
            <div class="reset-option active" data-option="email">
                <i class="fas fa-envelope"></i>
                <h6>Email</h6>
                <p>Reset via Email</p>
            </div>
            <div class="reset-option" data-option="mobile">
                <i class="fas fa-mobile-alt"></i>
                <h6>Mobile</h6>
                <p>Reset via OTP</p>
            </div>
        </div>

        <!-- Email Reset Form -->
        <form method="POST" action="{{ route('password.email') }}" id="emailForm">
            @csrf
            <input type="hidden" name="reset_method" value="email">

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="Enter your email address">
                </div>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-reset">
                <i class="fas fa-paper-plane"></i> Send Password Reset Link
            </button>
        </form>

        <!-- Mobile OTP Form -->
        <form method="POST" action="{{ route('password.otp') }}" id="mobileForm" style="display: none;">
            @csrf
            <input type="hidden" name="reset_method" value="mobile">

            <div class="form-group">
                <label for="mobile" class="form-label">
                    <i class="fas fa-mobile-alt"></i> Mobile Number
                </label>
                <div class="input-group">
                    <i class="fas fa-mobile-alt input-icon"></i>
                    <input type="text"
                           class="form-control"
                           id="mobile"
                           name="mobile"
                           value="{{ old('mobile') }}"
                           required
                           placeholder="Enter your mobile number">
                </div>
                @error('mobile')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-reset">
                <i class="fas fa-paper-plane"></i> Send OTP
            </button>
        </form>

        <!-- OTP Verification Section -->
        <div class="otp-section" id="otpSection">
            <div class="description">
                Enter the 6-digit OTP sent to your mobile number.
            </div>

            <form method="POST" action="{{ route('password.verify.otp') }}" id="otpForm">
                @csrf
                <input type="hidden" name="mobile" id="otpMobile">
                <input type="hidden" name="reset_method" value="mobile">

                <div class="otp-inputs">
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" required>
                </div>

                <button type="submit" class="btn btn-reset">
                    <i class="fas fa-check"></i> Verify OTP
                </button>
            </form>

            <div class="resend-otp">
                <a href="#" id="resendOtp">
                    <i class="fas fa-redo"></i> Resend OTP
                </a>
                <div class="countdown" id="countdown"></div>
                <a href="#" id="backToMobile" style="margin-left: 15px; color: #666;">
                    <i class="fas fa-arrow-left"></i> Back to Mobile Form
                </a>
            </div>
        </div>

        <div class="back-to-login">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetOptions = document.querySelectorAll('.reset-option');
            const emailForm = document.getElementById('emailForm');
            const mobileForm = document.getElementById('mobileForm');
            const otpSection = document.getElementById('otpSection');
            const otpInputs = document.querySelectorAll('.otp-input');
            const resendOtp = document.getElementById('resendOtp');
            const countdown = document.getElementById('countdown');
            const backToMobile = document.getElementById('backToMobile');

            // Reset option switching
            resetOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const method = this.dataset.option;
                    
                    // Update active state
                    resetOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');

                    // Show/hide forms
                    if (method === 'email') {
                        emailForm.style.display = 'block';
                        mobileForm.style.display = 'none';
                        otpSection.classList.remove('show');
                    } else {
                        emailForm.style.display = 'none';
                        mobileForm.style.display = 'block';
                        otpSection.classList.remove('show');
                    }
                });
            });

            // OTP input handling
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });

            // Mobile form submission
            mobileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const mobile = document.getElementById('mobile').value;
                document.getElementById('otpMobile').value = mobile;
                
                // Get the submit button and disable it
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                // Submit form via AJAX
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show OTP section
                        otpSection.classList.add('show');
                        // Hide the mobile form
                        mobileForm.style.display = 'none';
                        // Start countdown
                        startCountdown();
                        alert('OTP sent successfully!');
                    } else {
                        // Re-enable button on error
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                        alert(data.message || 'Failed to send OTP');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Re-enable button on error
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                    alert('Failed to send OTP');
                });
            });

            // OTP form submission
            document.getElementById('otpForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to password reset page
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message || 'Invalid OTP');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to verify OTP');
                });
            });

            // Resend OTP
            resendOtp.addEventListener('click', function(e) {
                e.preventDefault();
                
                fetch('{{ route("password.resend.otp") }}', {
                    method: 'POST',
                    body: new FormData(mobileForm),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        startCountdown();
                        alert('OTP resent successfully');
                    } else {
                        alert(data.message || 'Failed to resend OTP');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to resend OTP');
                });
            });

            function startCountdown() {
                let timeLeft = 60;
                resendOtp.style.display = 'none';
                countdown.style.display = 'block';
                
                const timer = setInterval(() => {
                    timeLeft--;
                    countdown.textContent = `Resend available in ${timeLeft} seconds`;
                    
                    if (timeLeft <= 0) {
                        clearInterval(timer);
                        resendOtp.style.display = 'block';
                        countdown.style.display = 'none';
                    }
                }, 1000);
            }

            // Back to mobile form
            backToMobile.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Hide OTP section
                otpSection.classList.remove('show');
                // Show mobile form
                mobileForm.style.display = 'block';
                // Reset mobile form
                document.getElementById('mobile').value = '';
                // Reset OTP inputs
                otpInputs.forEach(input => input.value = '');
                // Reset submit button
                const submitButton = mobileForm.querySelector('button[type="submit"]');
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            });
        });
    </script>
</body>
</html>
