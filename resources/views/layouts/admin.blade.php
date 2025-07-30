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
            background: #1a2942;
            min-height: 100vh;
            color: #fff;
            padding-top: 30px;
        }
        .sidebar .nav-link {
            color: #b8c7ce;
            margin-bottom: 10px;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #22304a;
            color: #fff;
            transform: translateX(5px);
        }
        .profile-img {
            width: 40px; height: 40px; border-radius: 50%;
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
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
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
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.subscriptions') }}">
                                <i class="fas fa-credit-card me-2"></i>View Subscriptions
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                       href="{{ route('admin.plans.index') }}">
                        <i class="fas fa-cube me-2"></i>Subscription Plans
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}"
                       href="{{ route('admin.subscriptions') }}">
                        <i class="fas fa-credit-card me-2"></i>Subscriptions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.billing.index') ? 'active' : '' }}"
                       href="{{ route('admin.billing.index') }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Billing
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                       href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog me-2"></i>System Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.otp-settings.*') ? 'active' : '' }}"
                       href="{{ route('admin.otp-settings.index') }}">
                        <i class="fas fa-mobile-alt me-2"></i>SMS Settings
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
</body>
</html>
