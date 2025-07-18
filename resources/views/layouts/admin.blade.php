<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- মোবাইল রেসপনসিভ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #22304a;
            color: #fff;
            border-radius: 8px;
        }
        .profile-img {
            width: 40px; height: 40px; border-radius: 50%;
        }
        .card { border-radius: 15px; }
        .card-title { font-size: 1.1rem; }
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
            <h4 class="mb-4 text-center">DASHTRAP</h4>
            <ul class="nav flex-md-column flex-row">
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.owners.index') }}">Owners</a></li>
                <!-- আরও মেনু -->
            </ul>
        </nav>
        <!-- Main Content -->
        <main class="col-12 col-md-10 p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <h3 class="mb-2 mb-md-0">Dashboard</h3>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="profile-img me-2" alt="Profile">
                        <span>{{ Auth::user()->name ?? 'Profile' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @yield('content')
        </main>
    </div>
</div>
  @stack('scripts')
</body>
</html>