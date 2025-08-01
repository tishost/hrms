<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background: #f4f6fa; }
        .sidebar {
            background: linear-gradient(135deg, #1a2942 0%, #2c3e50 100%);
            min-height: 100vh;
            color: #fff;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar h4 {
            background: linear-gradient(45deg, #3498db, #2980b9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: bold;
        }
        .sidebar .nav-link {
            color: #b8c7ce;
            margin-bottom: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .sidebar .nav-link:hover::before {
            left: 100%;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* Enhanced Dropdown Styling */
        .sidebar .dropdown-menu {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            margin-top: 5px;
            padding: 8px;
            min-width: 220px;
            backdrop-filter: blur(10px);
        }
        .sidebar .dropdown-item {
            color: #ecf0f1;
            padding: 12px 16px;
            border-radius: 10px;
            margin: 2px 0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .sidebar .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #3498db, #2980b9);
            transition: width 0.3s ease;
            z-index: -1;
        }
        .sidebar .dropdown-item:hover {
            color: #fff;
            background: transparent;
            transform: translateX(5px);
        }
        .sidebar .dropdown-item:hover::before {
            width: 100%;
        }
        .sidebar .dropdown-item.active {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        .sidebar .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        .sidebar .dropdown-item:hover i {
            transform: scale(1.2);
        }
        
        /* Dropdown Animation */
        .sidebar .dropdown-menu {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Dropdown Toggle Arrow */
        .sidebar .dropdown-toggle::after {
            transition: transform 0.3s ease;
            margin-left: 8px;
        }
        .sidebar .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        .profile-img {
            width: 40px; height: 40px; border-radius: 50%;
            border: 2px solid #3498db;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-title { font-size: 1.1rem; }
        .btn-block {
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Responsive Design */
        @media (max-width: 767.98px) {
            .sidebar {
                min-height: auto;
                padding-top: 10px;
            }
            .sidebar h4 {
                font-size: 1.2rem;
            }
            .sidebar ul {
                flex-direction: row !important;
                overflow-x: auto;
                white-space: nowrap;
            }
            .sidebar .nav-item {
                display: inline-block;
                margin-right: 10px;
            }
            .sidebar .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.95rem;
            }
            .sidebar .dropdown-menu {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                background: rgba(52, 152, 219, 0.1);
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar -->
        <nav class="col-12 col-md-2 sidebar mb-3 mb-md-0">
            <h4 class="mb-4 text-center">HRMS Admin</h4>
            <ul class="nav flex-md-column flex-row">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>

                <!-- Owners Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.owners.*') ? 'active' : '' }}"
                       href="#" id="ownersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-2"></i>Owners
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="ownersDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.owners.index') ? 'active' : '' }}"
                               href="{{ route('admin.owners.index') }}">
                                <i class="fas fa-list me-2"></i>Owner List
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.owners.create') ? 'active' : '' }}"
                               href="{{ route('admin.owners.create') }}">
                                <i class="fas fa-plus me-2"></i>Add New Owner
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Subscription Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.subscriptions') || request()->routeIs('admin.plans.*') || request()->routeIs('admin.billing.*') ? 'active' : '' }}"
                       href="#" id="subscriptionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-credit-card me-2"></i>Subscription
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="subscriptionDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}"
                               href="{{ route('admin.subscriptions') }}">
                                <i class="fas fa-list me-2"></i>Subscription List
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                               href="{{ route('admin.plans.index') }}">
                                <i class="fas fa-cube me-2"></i>Package Management
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.billing.index') ? 'active' : '' }}"
                               href="{{ route('admin.billing.index') }}">
                                <i class="fas fa-file-invoice-dollar me-2"></i>Billing & Payments
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- System Settings -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.otp-settings.*') ? 'active' : '' }}"
                       href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.index') }}">
                                <i class="fas fa-cog me-2"></i>System Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.otp-settings.*') ? 'active' : '' }}"
                               href="{{ route('admin.otp-settings.index') }}">
                                <i class="fas fa-mobile-alt me-2"></i>SMS Settings
                            </a>
                        </li>
                        <li>
                                                            <a class="dropdown-item {{ request()->routeIs('admin.settings.seo.*') ? 'active' : '' }}"
                                   href="{{ route('admin.settings.seo') }}">
                                    <i class="fas fa-search me-2"></i>SEO Settings
                                </a>
                                <a class="dropdown-item {{ request()->routeIs('admin.settings.chat.*') ? 'active' : '' }}"
                                   href="{{ route('admin.settings.chat') }}">
                                    <i class="fas fa-comments me-2"></i>Chat Settings
                                </a>
                                <a class="dropdown-item {{ request()->routeIs('admin.settings.sms.*') ? 'active' : '' }}"
                                   href="{{ route('admin.settings.sms') }}">
                                    <i class="fas fa-sms me-2"></i>SMS Settings
                                </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}"
                               href="{{ route('admin.chat.dashboard') }}">
                                <i class="fas fa-comments me-2"></i>Chat Agent Dashboard
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Support -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}"
                       href="{{ route('admin.tickets.index') }}">
                        <i class="fas fa-ticket-alt me-2"></i>Contact Tickets
                    </a>
                </li>
            </ul>
                </nav>
        <!-- Main Content -->
        <main class="col-12 col-md-10 p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <h3 class="mb-2 mb-md-0">@yield('title', 'Dashboard')</h3>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="profile-img me-2" alt="Profile">
                        <span>{{ Auth::user()->name ?? 'Profile' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')

<script>
// CSRF Token Refresh Function
function refreshCsrfToken() {
    fetch('/csrf-token', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update all CSRF tokens in forms
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = data.token;
        });
        
        // Update meta tag
        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
    })
    .catch(error => {
        console.error('Error refreshing CSRF token:', error);
    });
}

// Refresh CSRF token every 30 minutes
setInterval(refreshCsrfToken, 30 * 60 * 1000);

// Refresh CSRF token before form submission
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('submit', function(e) {
        // Refresh token before form submission
        refreshCsrfToken();
    });
    
    // Enhanced dropdown interactions
    const dropdowns = document.querySelectorAll('.sidebar .dropdown');
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Add hover effect for desktop
        if (window.innerWidth > 768) {
            dropdown.addEventListener('mouseenter', function() {
                this.classList.add('show');
                menu.style.display = 'block';
            });
            
            dropdown.addEventListener('mouseleave', function() {
                this.classList.remove('show');
                menu.style.display = 'none';
            });
        }
        
        // Add click effect for mobile
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const isOpen = dropdown.classList.contains('show');
                
                // Close all other dropdowns
                dropdowns.forEach(d => d.classList.remove('show'));
                
                // Toggle current dropdown
                if (!isOpen) {
                    dropdown.classList.add('show');
                }
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar .dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
});
</script>
</body>
</html>
