<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - HRMS</title>
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

        .reset-password-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            width: 100%;
            max-width: 450px;
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

        .password-requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            color: #666;
        }

        .password-requirements h6 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .password-requirements ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .reset-password-container {
                margin: 10px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <div class="logo-section">
            <h2><i class="fas fa-lock"></i> Reset Password</h2>
            <p>HRMS - Property Management System</p>
        </div>

        <div class="description">
            Enter your email address and new password to reset your account password.
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

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                           value="{{ old('email', $request->email) }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="Enter your email address">
                </div>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           placeholder="Enter your new password">
                </div>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock"></i> Confirm New Password
                </label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password"
                           class="form-control"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           placeholder="Confirm your new password">
                </div>
                @error('password_confirmation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="password-requirements">
                <h6><i class="fas fa-info-circle"></i> Password Requirements:</h6>
                <ul>
                    <li>At least 6 characters long</li>
                    <li>Contains at least one uppercase letter</li>
                    <li>Contains at least one lowercase letter</li>
                    <li>Contains at least one number</li>
                    <li>Contains at least one special character</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-reset">
                <i class="fas fa-key"></i> Reset Password
            </button>
        </form>

        <div class="back-to-login">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
