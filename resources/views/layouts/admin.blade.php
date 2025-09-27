<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Dynamic Favicon -->
    @php
        $faviconUrl = \App\Helpers\SystemHelper::getCompanyFavicon();
        $faviconType = null;
        if ($faviconUrl) {
            $ext = strtolower(pathinfo(parse_url($faviconUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $faviconType = match ($ext) {
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                'gif' => 'image/gif',
                'jpg', 'jpeg' => 'image/jpeg',
                default => 'image/x-icon',
            };
        }
    @endphp
    @if($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}" type="{{ $faviconType }}">
        <link rel="shortcut icon" href="{{ $faviconUrl }}" type="{{ $faviconType }}">
    @endif
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { 
            background: #f4f6fa; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* Ensure proper container layout */
        .container-fluid {
            width: 100%;
            padding: 0;
            margin: 0;
        }
        
        /* Fix row layout */
        .row.flex-nowrap {
            margin: 0;
            width: 100%;
            display: flex;
            flex-wrap: nowrap;
        }
        
        /* Desktop sidebar styling */
        .sidebar {
            background: linear-gradient(135deg, #1a2942 0%, #2c3e50 100%) !important;
            min-height: 100vh;
            color: #fff !important;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            order: 1;
            margin: 0;
            padding-left: 0;
            padding-right: 0;
        }
        
        /* Ensure sidebar is visible on desktop */
        @media (min-width: 768px) {
            .sidebar {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                position: relative !important;
                left: 0 !important;
                transform: none !important;
                width: 25% !important;
                flex: 0 0 25% !important;
                max-width: 25% !important;
                order: 1;
                margin: 0;
                padding: 30px 0 0 0;
            }
            
            .main-content {
                flex: 0 0 75% !important;
                max-width: 75% !important;
                width: 75% !important;
                order: 2;
                margin: 0;
                padding: 20px;
            }
        }
        
        @media (min-width: 992px) {
            .sidebar {
                width: 16.666667% !important;
                flex: 0 0 16.666667% !important;
                max-width: 16.666667% !important;
            }
            
            .main-content {
                flex: 0 0 83.333333% !important;
                max-width: 83.333333% !important;
                width: 83.333333% !important;
            }
        }
        
        .sidebar h4 {
            background: linear-gradient(45deg, #3498db, #2980b9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: bold;
            color: #3498db !important;
            margin: 0;
            padding: 0 20px 20px 20px;
        }
        
        .sidebar .nav {
            padding: 0 20px;
        }
        
        .sidebar .nav-link {
            color: #b8c7ce !important;
            margin-bottom: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: block;
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
            color: #fff !important;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* Simple Modern Sub-menu Styling */
        .sidebar .submenu {
            background: rgba(255, 255, 255, 0.03);
            border-left: 2px solid rgba(52, 152, 219, 0.2);
            margin: 3px 0 0 15px;
            padding: 0;
            list-style: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease, opacity 0.2s ease;
            border-radius: 0 4px 4px 0;
            opacity: 0;
        }
        
        .sidebar .submenu.show {
            max-height: 500px;
            padding: 4px 0;
            opacity: 1;
        }
        
        .sidebar .submenu-item {
            margin: 0;
        }
        
        .sidebar .submenu-link {
            color: #a0aec0 !important;
            padding: 6px 12px 6px 20px;
            border-radius: 0;
            transition: all 0.2s ease;
            position: relative;
            text-decoration: none;
            display: block;
            font-size: 0.95rem;
            font-weight: 400;
            border-left: 2px solid transparent;
        }
        
        .sidebar .submenu-link:hover {
            color: #fff !important;
            background: rgba(52, 152, 219, 0.08);
            border-left-color: #3498db;
            transform: translateX(2px);
        }
        
        .sidebar .submenu-link.active {
            background: rgba(52, 152, 219, 0.12);
            color: #fff !important;
            border-left-color: #3498db;
            font-weight: 500;
        }
        
        /* Menu Toggle Arrow */
        .sidebar .menu-toggle::after {
            content: '▼';
            float: right;
            transition: transform 0.3s ease;
            font-size: 0.8rem;
            margin-top: 2px;
        }
        
        .sidebar .menu-toggle.open::after {
            transform: rotate(180deg);
        }
        
        /* Menu Item with Submenu */
        .sidebar .has-submenu .nav-link {
            position: relative;
        }
        
        .sidebar .has-submenu .nav-link.menu-toggle {
            cursor: pointer;
        }
        
        .sidebar .has-submenu.open .nav-link {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .sidebar .has-submenu.open .nav-link::after {
            transform: rotate(180deg);
        }
        
        .profile-img {
            width: 40px; 
            height: 40px; 
            border-radius: 50%;
            border: 2px solid #3498db;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e3e6f0;
        }
        
        .card-title { 
            font-size: 1.1rem; 
        }
        
        .btn-block {
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 15px;
            width: 100%;
            text-align: left;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle:hover {
            background: linear-gradient(135deg, #2980b9, #1a5276);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .mobile-menu-toggle:active {
            transform: translateY(0);
        }
        
        /* Sidebar Overlay - Only for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Responsive Design */
        @media (max-width: 767.98px) {
            .mobile-menu-toggle {
                display: block !important;
                margin: 10px 15px;
                width: calc(100% - 30px);
            }
            
            /* Mobile sidebar - fixed width, hidden by default */
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                z-index: 1050;
                min-height: 100vh;
                height: 100vh;
                padding-top: 20px;
                transition: left 0.3s ease;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                max-width: 280px !important;
                background: linear-gradient(135deg, #1a2942 0%, #2c3e50 100%) !important;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            /* Mobile overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            /* Mobile main content - full width, always visible */
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding: 20px !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                position: relative !important;
                left: 0 !important;
                background: #f4f6fa !important;
                min-height: 100vh;
            }
            
            /* Mobile row layout */
            .row.flex-nowrap {
                flex-direction: column !important;
                flex-wrap: wrap !important;
                margin: 0 !important;
            }
            
            /* Mobile column classes */
            .col-12, .col-lg-2, .col-lg-10, .col-md-3, .col-md-9 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Ensure main content is always visible on mobile */
            .col-lg-10, .col-md-9 {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                position: relative !important;
                left: 0 !important;
                width: 100% !important;
            }
            
            /* Hide sidebar column on mobile by default */
            .col-lg-2, .col-md-3 {
                display: none !important;
            }
            
            /* Show sidebar only when it has 'show' class */
            .sidebar.show {
                display: block !important;
                position: fixed !important;
                left: 0 !important;
            }
            
            .sidebar h4 {
                font-size: 1.2rem;
                margin-bottom: 20px;
            }
            
            .sidebar .nav-link {
                padding: 12px 15px;
                font-size: 0.95rem;
                margin-bottom: 5px;
            }
            
            .sidebar .submenu {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                background: rgba(52, 152, 219, 0.1);
                margin-top: 5px;
                margin-left: 20px;
                display: block;
                opacity: 1;
                visibility: visible;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease, padding 0.3s ease;
            }
            
            .sidebar .submenu.show {
                max-height: 500px !important;
                padding: 8px 0 !important;
                opacity: 1 !important;
                visibility: visible !important;
                transform: none !important;
                position: static !important;
            }
            
            .profile-section {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .profile-section .dropdown {
                margin-top: 10px;
            }
            
            /* Mobile-specific improvements */
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .h3 {
                font-size: 1.5rem;
            }
            
            .h5 {
                font-size: 1.1rem;
            }
            
            .text-xs {
                font-size: 0.7rem !important;
            }
            
            /* Improve touch targets */
            .nav-link, .dropdown-item {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
            
            /* Better spacing for mobile */
            .mb-4 {
                margin-bottom: 1rem !important;
            }
            
            .p-4 {
                padding: 1rem !important;
            }
            
            /* Responsive tables */
            .table-responsive {
                font-size: 0.85rem;
            }
            
            /* Chart responsiveness */
            .chart-area, .chart-pie {
                min-height: 200px;
            }
            
            /* Badge improvements */
            .badge {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
            
            /* Flex improvements */
            .d-flex {
                flex-wrap: wrap;
            }
            
            /* Gap improvements */
            .gap-2 {
                gap: 0.5rem !important;
            }
        }
        
        /* Desktop - Hide overlay completely */
        @media (min-width: 768px) {
            .sidebar-overlay {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                position: static !important;
            }
            
            .sidebar {
                position: relative !important;
                left: 0 !important;
                width: 16.666667% !important;
                flex: 0 0 16.666667% !important;
                max-width: 16.666667% !important;
            }
            
            .main-content {
                flex: 0 0 83.333333% !important;
                max-width: 83.333333% !important;
                width: 83.333333% !important;
            }
        }
        
        @media (max-width: 575.98px) {
            /* Extra small devices */
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
            }
            
            .d-flex.gap-2 .btn {
                width: 100%;
            }
            
            .h3 {
                font-size: 1.25rem;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
            .profile-img {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
<!-- Mobile Menu -->
<div class="offcanvas offcanvas-start d-block d-md-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title" id="mobileMenuLabel">HRMS Admin</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body bg-dark text-white">
    <div class="list-group list-group-flush">
      <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
      </a>
      
      <!-- Owners -->
      <div class="list-group-item bg-transparent border-0 p-0">
        <a class="list-group-item list-group-item-action bg-transparent text-white border-0 mobile-menu-toggle" data-bs-toggle="collapse" href="#ownersSubmenu" role="button" aria-expanded="false" aria-controls="ownersSubmenu">
          <i class="fas fa-users me-2"></i>Owners <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="ownersSubmenu">
          <div class="list-group list-group-flush ms-3">
            <a href="{{ route('admin.owners.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-list me-2"></i>Owner List
            </a>
            <a href="{{ route('admin.owners.create') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-plus me-2"></i>Add New Owner
            </a>
          </div>
        </div>
      </div>
      
      <!-- Subscriptions -->
      <div class="list-group-item bg-transparent border-0 p-0">
        <a class="list-group-item list-group-item-action bg-transparent text-white border-0" data-bs-toggle="collapse" href="#subscriptionSubmenu" role="button" aria-expanded="false" aria-controls="subscriptionSubmenu">
          <i class="fas fa-credit-card me-2"></i>Subscriptions <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="subscriptionSubmenu">
          <div class="list-group list-group-flush ms-3">
            <a href="{{ route('admin.subscriptions') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-list me-2"></i>Subscription List
            </a>
            <a href="{{ route('admin.plans.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-cube me-2"></i>Package Management
            </a>
            <a href="{{ route('admin.billing.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-file-invoice-dollar me-2"></i>Billing & Payments
            </a>
          </div>
        </div>
      </div>
      
      <!-- Reports -->
      <div class="list-group-item bg-transparent border-0 p-0">
        <a class="list-group-item list-group-item-action bg-transparent text-white border-0" data-bs-toggle="collapse" href="#reportsSubmenu" role="button" aria-expanded="false" aria-controls="reportsSubmenu">
          <i class="fas fa-chart-line me-2"></i>Reports <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="reportsSubmenu">
          <div class="list-group list-group-flush ms-3">
            <a href="{{ route('admin.reports.financial') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-chart-line me-2"></i>Financial Reports
            </a>
            <a href="{{ route('admin.analytics') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-chart-bar me-2"></i>Analytics Dashboard
            </a>
            <a href="{{ route('admin.login-logs.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-sign-in-alt me-2"></i>Login Logs
            </a>
          </div>
        </div>
      </div>
      
      <a href="{{ route('admin.backups.index') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0">
        <i class="fas fa-database me-2"></i>Backups
      </a>
      
      <!-- Ads Management -->
      <div class="list-group-item bg-transparent border-0 p-0">
        <a class="list-group-item list-group-item-action bg-transparent text-white border-0" data-bs-toggle="collapse" href="#adsSubmenu" role="button" aria-expanded="false" aria-controls="adsSubmenu">
          <i class="fas fa-ad me-2"></i>Ads Management <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="adsSubmenu">
          <div class="list-group list-group-flush ms-3">
            <a href="{{ route('admin.ads.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-list me-2"></i>All Ads
            </a>
            <a href="{{ route('admin.ads.create') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-plus me-2"></i>Create New Ad
            </a>
            <a href="{{ route('admin.ads.stats') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-chart-bar me-2"></i>Ads Statistics
            </a>
          </div>
        </div>
      </div>
      
      <!-- Settings -->
      <div class="list-group-item bg-transparent border-0 p-0">
        <a class="list-group-item list-group-item-action bg-transparent text-white border-0" data-bs-toggle="collapse" href="#settingsSubmenu" role="button" aria-expanded="false" aria-controls="settingsSubmenu">
          <i class="fas fa-cog me-2"></i>Settings <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="settingsSubmenu">
          <div class="list-group list-group-flush ms-3">
            <a href="{{ route('admin.settings.company') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-building me-2"></i>Company Info
            </a>
            <a href="{{ route('admin.settings.system') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-cogs me-2"></i>System Settings
            </a>
            <a href="{{ route('admin.settings.landing') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-globe me-2"></i>Landing Page
            </a>
            <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-cog me-2"></i>General Settings
            </a>
            <a href="{{ route('admin.otp-settings.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-mobile-alt me-2"></i>OTP Settings
            </a>
            <a href="{{ route('admin.settings.notifications') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-bell me-2"></i>Notification Settings
            </a>
            <a href="{{ route('admin.notifications.send') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-paper-plane me-2"></i>Send Push Notifications
            </a>
            <a href="{{ route('admin.settings.email-configuration') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-envelope me-2"></i>Email Config
            </a>
            <a href="{{ route('admin.templates.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-layer-group me-2"></i>Template Management
            </a>
            <a href="{{ route('admin.settings.seo') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-search me-2"></i>SEO Settings
            </a>
            <a href="{{ route('admin.settings.chat') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-comments me-2"></i>Chat Settings
            </a>
            <a href="{{ route('admin.settings.sms') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-sms me-2"></i>SMS Settings
            </a>
            <a href="{{ route('admin.settings.backup') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-database me-2"></i>Backup Settings
            </a>
            <a href="{{ route('admin.charges.index') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-money-bill-wave me-2"></i>Charges Setup
            </a>
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white-50 border-0">
              <i class="fas fa-comments me-2"></i>Chat Agent Dashboard
            </a>
          </div>
        </div>
      </div>
      
      <a href="{{ route('admin.tickets.index') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0">
        <i class="fas fa-ticket-alt me-2"></i>Support
      </a>
    </div>
  </div>
</div>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Mobile Menu Toggle -->
        
        
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <nav class="col-12 col-lg-2 col-md-3 sidebar mb-3 mb-md-0 d-none d-md-block" id="sidebar">
            <div class="d-flex justify-content-between align-items-center mb-4 px-3">
                <h4 class="mb-0">HRMS Admin</h4>
                <button class="btn-close btn-close-white d-md-none" id="closeSidebar"></button>
            </div>
            <ul class="nav flex-md-column flex-row">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>

                <!-- Owners Management -->
                <li class="nav-item has-submenu {{ request()->routeIs('admin.owners.*') ? 'open' : '' }}">
                    <a class="nav-link menu-toggle {{ request()->routeIs('admin.owners.*') ? 'active' : '' }}"
                       href="javascript:void(0);">
                        <i class="fas fa-users me-2"></i>Owners
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.owners.*') ? 'show' : '' }}">
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.owners.index') ? 'active' : '' }}"
                               href="{{ route('admin.owners.index') }}">
                                Owner List
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.owners.create') ? 'active' : '' }}"
                               href="{{ route('admin.owners.create') }}">
                                Add New Owner
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Tenants Management -->
                <li class="nav-item has-submenu {{ request()->routeIs('admin.tenants.*') ? 'open' : '' }}">
                    <a class="nav-link menu-toggle {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}"
                       href="javascript:void(0);">
                        <i class="fas fa-user-friends me-2"></i>Tenants
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.tenants.*') ? 'show' : '' }}">
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.tenants.index') ? 'active' : '' }}"
                               href="{{ route('admin.tenants.index') }}">
                                Tenant List
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Subscription Management -->
                <li class="nav-item has-submenu {{ request()->routeIs('admin.subscriptions') || request()->routeIs('admin.plans.*') || request()->routeIs('admin.billing.*') ? 'open' : '' }}">
                    <a class="nav-link menu-toggle {{ request()->routeIs('admin.subscriptions') || request()->routeIs('admin.plans.*') || request()->routeIs('admin.billing.*') ? 'active' : '' }}"
                       href="javascript:void(0);">
                        <i class="fas fa-credit-card me-2"></i>Subscription
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.subscriptions') || request()->routeIs('admin.plans.*') || request()->routeIs('admin.billing.*') ? 'show' : '' }}">
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}"
                               href="{{ route('admin.subscriptions') }}">
                                Subscription List
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                               href="{{ route('admin.plans.index') }}">
                                Package Management
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.billing.index') ? 'active' : '' }}"
                               href="{{ route('admin.billing.index') }}">
                                Billing & Payments
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reports -->
                <li class="nav-item has-submenu {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.analytics') || request()->routeIs('admin.login-logs.*') ? 'open' : '' }}">
                    <a class="nav-link menu-toggle {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.analytics') || request()->routeIs('admin.login-logs.*') ? 'active' : '' }}"
                       href="javascript:void(0);">
                        <i class="fas fa-chart-line me-2"></i>Reports
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.analytics') || request()->routeIs('admin.login-logs.*') ? 'show' : '' }}">
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}"
                               href="{{ route('admin.reports.financial') }}">
                                Financial Reports
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}"
                               href="{{ route('admin.analytics') }}">
                                Analytics Dashboard
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.login-logs.*') ? 'active' : '' }}"
                               href="{{ route('admin.login-logs.index') }}">
                                Login Logs
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Backup Management -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}"
                       href="{{ route('admin.backups.index') }}">
                        <i class="fas fa-database me-2"></i>Backup Management
                    </a>
                </li>

                <!-- Ads Management -->
                <li class="nav-item has-submenu {{ request()->routeIs('admin.ads.*') ? 'open' : '' }}">
                    <a class="nav-link menu-toggle {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}"
                       href="javascript:void(0);">
                        <i class="fas fa-ad me-2"></i>Ads Management
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.ads.*') ? 'show' : '' }}">
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.ads.index') ? 'active' : '' }}"
                               href="{{ route('admin.ads.index') }}">
                                All Ads
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.ads.create') ? 'active' : '' }}"
                               href="{{ route('admin.ads.create') }}">
                                Create New Ad
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.ads.stats') ? 'active' : '' }}"
                               href="{{ route('admin.ads.stats') }}">
                                Ads Statistics
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Settings -->
                <li class="nav-item has-submenu {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.otp-settings.*') || request()->routeIs('admin.notifications.*') || request()->routeIs('admin.charges.*') || request()->routeIs('admin.security.otp.*') || request()->routeIs('admin.chat.*') ? 'open' : '' }}">
                    <a class="nav-link menu-toggle {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.otp-settings.*') || request()->routeIs('admin.notifications.*') || request()->routeIs('admin.charges.*') || request()->routeIs('admin.security.otp.*') || request()->routeIs('admin.chat.*') ? 'active' : '' }}"
                       href="javascript:void(0);">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.otp-settings.*') || request()->routeIs('admin.notifications.*') || request()->routeIs('admin.charges.*') || request()->routeIs('admin.security.otp.*') || request()->routeIs('admin.chat.*') ? 'show' : '' }}">
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.company') ? 'active' : '' }}"
                               href="{{ route('admin.settings.company') }}">
                                Company Information
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.system') ? 'active' : '' }}"
                               href="{{ route('admin.settings.system') }}">
                                System Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.landing') ? 'active' : '' }}"
                               href="{{ route('admin.settings.landing') }}">
                                Landing Page
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.*') && !request()->routeIs('admin.settings.company') && !request()->routeIs('admin.settings.system') && !request()->routeIs('admin.settings.landing') && !request()->routeIs('admin.settings.notifications') && !request()->routeIs('admin.settings.email.templates') && !request()->routeIs('admin.settings.sms.templates') && !request()->routeIs('admin.settings.notification-logs') && !request()->routeIs('admin.settings.email-configuration') && !request()->routeIs('admin.settings.sms') && !request()->routeIs('admin.settings.seo') && !request()->routeIs('admin.settings.chat') && !request()->routeIs('admin.settings.backup') ? 'active' : '' }}"
                               href="{{ route('admin.settings.index') }}">
                                General Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.otp-settings.*') ? 'active' : '' }}"
                               href="{{ route('admin.otp-settings.index') }}">
                                OTP Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.notifications.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.notifications') }}">
                                Notification Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}"
                               href="{{ route('admin.templates.index') }}">
                                Template Management
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.notification-logs.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.notification-logs') }}">
                                Notification Logs
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
                               href="{{ route('admin.notifications.send') }}">
                                Send Push Notifications
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.email-configuration.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.email-configuration') }}">
                                Email Configuration
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.sms.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.sms') }}">
                                SMS Configuration
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.seo.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.seo') }}">
                                SEO Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.chat.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.chat') }}">
                                Chat Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.settings.backup.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.backup') }}">
                                Backup Settings
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.charges.*') ? 'active' : '' }}"
                               href="{{ route('admin.charges.index') }}">
                                Charges Setup
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.security.otp.*') ? 'active' : '' }}"
                               href="{{ route('admin.security.otp') }}">
                                OTP Security
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a class="submenu-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}"
                               href="{{ route('admin.dashboard') }}">
                                Chat Agent Dashboard
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
        
        <!-- Mobile Menu Button -->
        <div class="d-md-none w-100 p-3">
            <button class="mobile-menu-toggle" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                <i class="fas fa-bars"></i> Menu
            </button>
        </div>
        <!-- Main Content -->
        <main class="col-12 col-lg-10 col-md-9 p-4 main-content">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 profile-section">
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

<script>
// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const closeSidebar = document.getElementById('closeSidebar');
    
    function openSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            
    // Add focus trap for accessibility
    if (sidebar) sidebar.setAttribute('aria-hidden', 'false');
    if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'true');
        }
    }
    
    function closeSidebarMenu() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
        
    // Remove focus trap
    if (sidebar) sidebar.setAttribute('aria-hidden', 'true');
    if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', openSidebar);
        mobileMenuToggle.addEventListener('touchstart', openSidebar);
    }
    
    if (closeSidebar) {
        closeSidebar.addEventListener('click', closeSidebarMenu);
        closeSidebar.addEventListener('touchstart', closeSidebarMenu);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebarMenu);
        sidebarOverlay.addEventListener('touchstart', closeSidebarMenu);
    }
    
    // Close sidebar when clicking on a link (mobile)
    if (sidebar) {
        const sidebarLinks = sidebar.querySelectorAll('.nav-link, .dropdown-item');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebarMenu();
                }
            });
            link.addEventListener('touchstart', () => {
                if (window.innerWidth <= 768) {
                    closeSidebarMenu();
                }
            });
        });
    }
    
    // Ensure sidebar is properly hidden on mobile by default
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        if (sidebar) sidebar.setAttribute('aria-hidden', 'true');
        if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }
    
    // Submenu Toggle Functionality
    const menuToggles = document.querySelectorAll('.menu-toggle');
    if (menuToggles.length > 0) {
        menuToggles.forEach(toggle => {
            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const parentItem = this.closest('.has-submenu');
                    if (parentItem) {
                        const submenu = parentItem.querySelector('.submenu');
                        
                        if (submenu) {
                            // Close all other submenus
                            const allSubmenus = document.querySelectorAll('.has-submenu');
                            allSubmenus.forEach(item => {
                                if (item && item !== parentItem) {
                                    item.classList.remove('open');
                                    const otherSubmenu = item.querySelector('.submenu');
                                    if (otherSubmenu) {
                                        otherSubmenu.classList.remove('show');
                                    }
                                }
                            });
                            
                            // Toggle current submenu
                            parentItem.classList.toggle('open');
                            submenu.classList.toggle('show');
                        }
                    }
                });
            }
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Desktop: ensure sidebar is visible and overlay is hidden
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            sidebarOverlay.style.display = 'none';
            document.body.style.overflow = '';
            sidebar.setAttribute('aria-hidden', 'false');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            
            // Show sidebar column on desktop
            const sidebarCol = document.querySelector('.col-lg-2, .col-md-3');
            if (sidebarCol) {
                sidebarCol.style.display = 'block';
            }
        } else {
            // Mobile: ensure sidebar is hidden by default and main content is visible
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        if (sidebar) sidebar.setAttribute('aria-hidden', 'true');
        if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
            
            // Hide sidebar column on mobile
            const sidebarCol = document.querySelector('.col-lg-2, .col-md-3');
            if (sidebarCol) {
                sidebarCol.style.display = 'none';
            }
            
            // Ensure main content is visible on mobile
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.style.display = 'block';
                mainContent.style.visibility = 'visible';
                mainContent.style.opacity = '1';
                mainContent.style.width = '100%';
                mainContent.style.marginLeft = '0';
                mainContent.style.position = 'relative';
                mainContent.style.left = '0';
            }
        }
    });
    
    // Initialize overlay state based on screen size
    if (window.innerWidth > 768) {
        sidebarOverlay.style.display = 'none';
        sidebarOverlay.style.width = '0';
        sidebarOverlay.style.height = '0';
        if (sidebar) sidebar.setAttribute('aria-hidden', 'false');
        if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
        
        // Show sidebar column on desktop
        const sidebarCol = document.querySelector('.col-lg-2, .col-md-3');
        if (sidebarCol) {
            sidebarCol.style.display = 'block';
        }
    } else {
        // Mobile: ensure main content is properly displayed
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.style.display = 'block';
            mainContent.style.visibility = 'visible';
            mainContent.style.opacity = '1';
            mainContent.style.width = '100%';
            mainContent.style.marginLeft = '0';
            mainContent.style.position = 'relative';
            mainContent.style.left = '0';
        }
        
        // Hide sidebar column on mobile
        const sidebarCol = document.querySelector('.col-lg-2, .col-md-3');
        if (sidebarCol) {
            sidebarCol.style.display = 'none';
        }
        
        if (sidebar) sidebar.setAttribute('aria-hidden', 'true');
        if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }
    
    // Add keyboard support for accessibility
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth <= 768) {
            closeSidebarMenu();
        }
    });
});

// CSRF Token Refresh Function
function refreshCsrfToken() {
    console.log('Refreshing CSRF token...');
    fetch('/csrf-token', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('CSRF token refreshed successfully');
        
        // Update all CSRF tokens in forms
        const tokenInputs = document.querySelectorAll('input[name="_token"]');
        if (tokenInputs.length > 0) {
            tokenInputs.forEach(input => {
                if (input) input.value = data.token;
            });
        }
        
        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', data.token);
        }
        
        // Update any other CSRF token elements
        const csrfElements = document.querySelectorAll('[data-csrf-token]');
        if (csrfElements.length > 0) {
            csrfElements.forEach(element => {
                if (element) element.setAttribute('data-csrf-token', data.token);
            });
        }
        
        console.log('All CSRF tokens updated');
    })
    .catch(error => {
        console.error('Error refreshing CSRF token:', error);
        // Try to get token from meta tag as fallback
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            console.log('Using existing CSRF token from meta tag');
        }
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
    
    // Note: Menu toggle functionality is handled above in the main menu section
    
    // Close submenus when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar .has-submenu')) {
            const allSubmenus = document.querySelectorAll('.sidebar .has-submenu');
            if (allSubmenus.length > 0) {
                allSubmenus.forEach(item => {
                    if (item) {
                        item.classList.remove('open');
                        const submenu = item.querySelector('.submenu');
                        if (submenu) {
                            submenu.classList.remove('show');
                        }
                    }
                });
            }
        }
    });
    
    // Close submenus when touching outside
    document.addEventListener('touchstart', function(e) {
        if (!e.target.closest('.sidebar .has-submenu')) {
            const allSubmenus = document.querySelectorAll('.sidebar .has-submenu');
            if (allSubmenus.length > 0) {
                allSubmenus.forEach(item => {
                    if (item) {
                        item.classList.remove('open');
                        const submenu = item.querySelector('.submenu');
                        if (submenu) {
                            submenu.classList.remove('show');
                        }
                    }
                });
            }
        }
    });
});
</script>

@stack('scripts')
</body>
</html>
