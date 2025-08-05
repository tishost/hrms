<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/owner-dashboard.css') }}?v=1.1">
    @yield('styles')
    @yield('head')
</head>
<body>
    @include('layouts.partials.owner_sidebar')

    {{-- Toast notifications for backend messages --}}
    @if(session('error'))
        <x-toast type="danger" :message="session('error')" />
    @endif
    @if(session('success'))
        <x-toast type="success" :message="session('success')" />
    @endif
    @if($errors->any())
        <x-toast type="danger" :message="$errors->first()" />
    @endif

    <div class="main-content">
        @include('layouts.partials.owner_topbar')
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/owner-dashboard.js') }}"></script>
    <style>
    /* Responsive Layout Styles */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 280px !important;
            height: 100vh !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s ease !important;
            z-index: 1000 !important;
            background: #2c3e50 !important;
            overflow-y: auto !important;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important;
        }
        
        .sidebar.mobile-open {
            transform: translateX(0) !important;
            width: 280px !important;
            max-width: 280px !important;
            min-width: 280px !important;
            overflow: visible !important;
            box-shadow: 2px 0 20px rgba(0,0,0,0.3) !important;
        }
        
        /* Force sidebar to be hidden by default on mobile */
        .sidebar {
            display: block !important;
            visibility: visible !important;
        }
        
        .content-wrapper {
            padding: 15px !important;
            margin-left: 0 !important;
        }
        
        .topbar {
            padding: 10px 15px !important;
            margin-left: 0 !important;
        }
        
        .topbar-content {
            flex-direction: column !important;
            gap: 10px !important;
        }
        
        .user-menu {
            align-self: flex-end !important;
        }
        
        /* Sidebar menu styles for mobile */
        .sidebar-menu {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        }
        
        .sidebar-menu li a {
            padding: 15px 20px !important;
            color: #ecf0f1 !important;
            display: flex !important;
            align-items: center !important;
            text-decoration: none !important;
            transition: background-color 0.3s ease !important;
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: unset !important;
        }
        
        .sidebar-menu li a span {
            display: inline-block !important;
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: unset !important;
        }
        
        .sidebar-menu li a:hover {
            background-color: rgba(255,255,255,0.1) !important;
        }
        
        .sidebar-menu li.active a {
            background-color: #4361ee !important;
            color: white !important;
        }
        
        .submenu {
            background-color: rgba(0,0,0,0.1) !important;
            padding-left: 0 !important;
        }
        
        .submenu li a {
            padding: 12px 20px 12px 40px !important;
            font-size: 0.9rem !important;
        }
        
        .submenu-arrow {
            margin-left: auto !important;
            transition: transform 0.3s ease !important;
        }
        
        .has-submenu.open .submenu-arrow {
            transform: rotate(180deg) !important;
        }
        
        .sidebar-header {
            padding: 20px !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        }
        
        .sidebar-brand {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            color: white !important;
            font-size: 1.2rem !important;
            font-weight: bold !important;
        }
    }
    
    @media (max-width: 480px) {
        .content-wrapper {
            padding: 10px;
        }
        
        .topbar {
            padding: 8px 10px;
        }
        
        .sidebar-brand span {
            font-size: 1rem;
        }
        
        .sidebar-menu li a {
            padding: 12px 15px;
            font-size: 0.9rem;
        }
        
        .submenu {
            padding-left: 20px;
        }
        
        .submenu li a {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
    }
    
    /* Mobile menu toggle button */
    .mobile-menu-toggle {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1001;
        background: #4361ee;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 8px 12px;
        font-size: 1.2rem;
        cursor: pointer;
    }
    
    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: block !important;
            position: fixed !important;
            top: 15px !important;
            left: 15px !important;
            z-index: 1001 !important;
            background: #4361ee !important;
            color: white !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 10px 15px !important;
            font-size: 1.2rem !important;
            cursor: pointer !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2) !important;
        }
        
        .sidebar-overlay {
            display: none !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0,0,0,0.5) !important;
            z-index: 999 !important;
            backdrop-filter: blur(2px) !important;
        }
        
        .sidebar-overlay.active {
            display: block !important;
        }
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Force CSS reload for subscription pages
        if (window.location.pathname.includes('subscription')) {
            const links = document.querySelectorAll('link[href*="owner-dashboard.css"]');
            links.forEach(link => {
                link.href = link.href.split('?')[0] + '?v=' + Date.now();
            });
        }

        // Mobile menu functionality
        const mobileMenuToggle = document.createElement('button');
        mobileMenuToggle.className = 'mobile-menu-toggle';
        mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(mobileMenuToggle);

        const sidebar = document.querySelector('.sidebar');
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        // Debug: Check if sidebar exists
        console.log('Sidebar found:', sidebar);
        console.log('Mobile menu toggle created');

        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile menu toggle clicked');
            
            if (sidebar) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
                console.log('Sidebar classes:', sidebar.className);
                console.log('Overlay classes:', overlay.className);
            } else {
                console.error('Sidebar not found!');
            }
        });

        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Overlay clicked - closing menu');
            
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });

        // Submenu functionality
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.has-submenu');
                parent.classList.toggle('open');
            });
        });

        document.querySelectorAll('.form-input').forEach(function(input) {
            input.addEventListener('input', function() {
                var errorDiv = this.parentElement.querySelector('.input-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        });
        
        // Toast auto-hide logic
        let toast = document.querySelector('.toast');
        if (toast) {
            setTimeout(function () {
                toast.style.opacity = '0';
                setTimeout(function () {
                    toast.style.display = 'none';
                }, 500);
            }, 4000); // 4 second por hide hobe
        }
    });
    </script>
    @yield('scripts')
</body>
</html>
