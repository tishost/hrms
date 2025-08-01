<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Bari Manager - House Rent Management System') }}</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {!! \App\Services\SeoService::renderMetaTags('landing') !!}

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', 'Noto Sans Bengali', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .package-card {
            transition: all 0.3s ease;
        }

        .package-card:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }

        @keyframes pulse-glow {
            from { box-shadow: 0 0 20px rgba(102, 126, 234, 0.5); }
            to { box-shadow: 0 0 30px rgba(102, 126, 234, 0.8); }
        }

        .language-switch {
            transition: all 0.3s ease;
        }

        .language-switch:hover {
            transform: scale(1.1);
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
        }

        .nav-link.active {
            color: #9333ea !important;
            background-color: rgba(147, 51, 234, 0.1);
            border-bottom: 2px solid #9333ea;
        }

        .nav-link.active:hover {
            color: #7c3aed !important;
        }

        /* Mobile menu transitions */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
        }

        #mobile-menu.hidden {
            opacity: 0;
            transform: translateY(-10px);
        }

        #mobile-menu:not(.hidden) {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="#home" class="cursor-pointer">
                            <img src="{{ asset('images/bari-manager-logo.svg') }}" alt="Bari Manager Logo" class="h-8 sm:h-12 w-auto hover:opacity-80 transition-opacity">
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#home" class="nav-link text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors" data-section="home">{{ __('Home') }}</a>
                        <a href="#features" class="nav-link text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors" data-section="features">{{ __('Features') }}</a>
                        <a href="#packages" class="nav-link text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors" data-section="packages">{{ __('Packages') }}</a>
                        <a href="#support" class="nav-link text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors" data-section="support">{{ __('Support') }}</a>
                        <a href="{{ route('login') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">{{ __('Login') }}</a>

                        <!-- Language Switcher -->
                        <div class="flex items-center space-x-2 ml-4">
                            <button onclick="changeLanguage('en')" class="language-switch px-2 py-1 rounded text-xs font-medium {{ app()->getLocale() == 'en' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">EN</button>
                            <button onclick="changeLanguage('bn')" class="language-switch px-2 py-1 rounded text-xs font-medium {{ app()->getLocale() == 'bn' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">বাং</button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-purple-600 focus:outline-none focus:text-purple-600 p-3 touch-manipulation">
                        <i class="fas fa-bars text-lg lg:text-xl"></i>
                    </button>
                </div>
            </div>

                        <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden">
                <div class="px-4 pt-4 pb-6 space-y-2 bg-white border-t border-gray-200">
                    <a href="#home" class="nav-link block text-gray-700 hover:text-purple-600 px-4 py-3 rounded-md text-base font-medium transition-colors" data-section="home">{{ __('Home') }}</a>
                    <a href="#features" class="nav-link block text-gray-700 hover:text-purple-600 px-4 py-3 rounded-md text-base font-medium transition-colors" data-section="features">{{ __('Features') }}</a>
                    <a href="#packages" class="nav-link block text-gray-700 hover:text-purple-600 px-4 py-3 rounded-md text-base font-medium transition-colors" data-section="packages">{{ __('Packages') }}</a>
                    <a href="#support" class="nav-link block text-gray-700 hover:text-purple-600 px-4 py-3 rounded-md text-base font-medium transition-colors" data-section="support">{{ __('Support') }}</a>
                    <a href="{{ route('login') }}" class="block bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-md text-base font-medium transition-colors text-center">{{ __('Login') }}</a>

                    <!-- Mobile Language Switcher -->
                    <div class="flex items-center justify-center space-x-3 pt-4">
                        <button onclick="changeLanguage('en')" class="language-switch px-4 py-2 rounded text-sm font-medium {{ app()->getLocale() == 'en' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">EN</button>
                        <button onclick="changeLanguage('bn')" class="language-switch px-4 py-2 rounded text-sm font-medium {{ app()->getLocale() == 'bn' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">বাং</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    {!! \App\Services\SeoService::renderBreadcrumbs() !!}

    <!-- Hero Section -->
    <section id="home" class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-white text-center lg:text-left">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-bold mb-4 lg:mb-6">
                        {{ __('Bari Manager') }}
                    </h1>
                    <p class="text-lg sm:text-xl lg:text-2xl mb-6 lg:mb-8 text-gray-100">
                        {{ __('Streamline your property management with our comprehensive Bari Manager solution. Manage tenants, track rent, and grow your business effortlessly.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#register" class="btn-primary text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold inline-block text-center">
                            <i class="fas fa-rocket mr-2"></i>
                            {{ __('Get Started Free') }}
                        </a>
                        <a href="#features" class="bg-white bg-opacity-20 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold inline-block text-center hover:bg-opacity-30 transition-all">
                            <i class="fas fa-play mr-2"></i>
                            {{ __('Watch Demo') }}
                        </a>
                    </div>
                </div>
                <div class="floating hidden lg:block">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                         alt="Modern House" class="rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Apps Section -->
    <section class="py-12 lg:py-20 bg-gradient-to-br from-purple-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-center lg:text-left">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 lg:mb-6">
                        {{ __('Get Our Mobile App') }}
                    </h2>
                    <p class="text-base lg:text-lg text-gray-600 mb-6 lg:mb-8">
                        {{ __('Download Bari Manager mobile app and manage your properties on the go. Available for both Android and iOS devices with full feature access.') }}
                    </p>

                    <div class="space-y-4 lg:space-y-6">
                        <div class="flex items-center justify-center lg:justify-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-mobile-alt text-green-600 text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ __('Mobile Management') }}</h4>
                                <p class="text-xs lg:text-sm text-gray-600">{{ __('Manage properties from anywhere') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center lg:justify-start">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <i class="fas fa-bell text-blue-600 text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ __('Real-time Notifications') }}</h4>
                                <p class="text-xs lg:text-sm text-gray-600">{{ __('Get instant updates and alerts') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center lg:justify-start">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <i class="fas fa-chart-line text-purple-600 text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ __('Offline Access') }}</h4>
                                <p class="text-xs lg:text-sm text-gray-600">{{ __('Work without internet connection') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 lg:mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#" class="flex items-center justify-center bg-black text-white px-4 lg:px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                            <i class="fab fa-apple text-xl lg:text-2xl mr-3"></i>
                            <div class="text-left">
                                <div class="text-xs">{{ __('Download on the') }}</div>
                                <div class="text-sm font-bold">{{ __('App Store') }}</div>
                            </div>
                        </a>

                        <a href="#" class="flex items-center justify-center bg-green-600 text-white px-4 lg:px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            <i class="fab fa-google-play text-xl lg:text-2xl mr-3"></i>
                            <div class="text-left">
                                <div class="text-xs">{{ __('Get it on') }}</div>
                                <div class="text-sm font-bold">{{ __('Google Play') }}</div>
                            </div>
                        </a>
                    </div>

                    <div class="mt-4 lg:mt-6 text-center lg:text-left">
                        <p class="text-xs lg:text-sm text-gray-500">
                            {{ __('Free download • No hidden charges • Full feature access') }}
                        </p>
                    </div>
                </div>

                <div class="relative hidden lg:block">
                    <div class="relative z-10">
                        <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                             alt="Mobile App Screenshot" class="rounded-2xl shadow-2xl">
                    </div>

                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 bg-white p-4 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <i class="fas fa-star text-green-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">4.8/5</div>
                                <div class="text-xs text-gray-600">{{ __('App Store Rating') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -bottom-4 -left-4 bg-white p-4 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-2 rounded-full mr-3">
                                <i class="fas fa-download text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">10K+</div>
                                <div class="text-xs text-gray-600">{{ __('Downloads') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-12 lg:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-8">
                <div class="stats-card text-white p-4 lg:p-6 rounded-2xl text-center">
                    <div class="text-2xl lg:text-3xl font-bold mb-2">{{ number_format($totalOwners) }}+</div>
                    <div class="text-xs lg:text-sm opacity-90">{{ __('Happy Customers') }}</div>
                </div>
                <div class="stats-card text-white p-4 lg:p-6 rounded-2xl text-center">
                    <div class="text-2xl lg:text-3xl font-bold mb-2">{{ number_format($totalProperties) }}+</div>
                    <div class="text-xs lg:text-sm opacity-90">{{ __('Properties Managed') }}</div>
                </div>
                <div class="stats-card text-white p-4 lg:p-6 rounded-2xl text-center">
                    <div class="text-2xl lg:text-3xl font-bold mb-2">{{ number_format($totalTenants) }}+</div>
                    <div class="text-xs lg:text-sm opacity-90">{{ __('Tenants Served') }}</div>
                </div>
                <div class="stats-card text-white p-4 lg:p-6 rounded-2xl text-center">
                    <div class="text-2xl lg:text-3xl font-bold mb-2">24/7</div>
                    <div class="text-xs lg:text-sm opacity-90">{{ __('Support Available') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HRMS Details Section -->
    <section class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-center lg:text-left">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 lg:mb-6">
                        {{ __('What is') }} <span class="gradient-text">{{ __('Bari Manager') }}</span>?
                    </h2>
                    <p class="text-base lg:text-lg text-gray-600 mb-4 lg:mb-6">
                        {{ __('Bari Manager is a comprehensive digital solution designed to revolutionize property management. Our platform combines powerful web applications with intuitive mobile apps to provide property owners with complete control over their rental business.') }}
                    </p>
                    <div class="space-y-3 lg:space-y-4">
                        <div class="flex items-center justify-center lg:justify-start">
                            <i class="fas fa-check-circle text-green-500 text-lg lg:text-xl mr-3"></i>
                            <span class="text-gray-700 text-sm lg:text-base">{{ __('Complete tenant management system') }}</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start">
                            <i class="fas fa-check-circle text-green-500 text-lg lg:text-xl mr-3"></i>
                            <span class="text-gray-700 text-sm lg:text-base">{{ __('Automated rent collection and tracking') }}</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start">
                            <i class="fas fa-check-circle text-green-500 text-lg lg:text-xl mr-3"></i>
                            <span class="text-gray-700 text-sm lg:text-base">{{ __('Maintenance request management') }}</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start">
                            <i class="fas fa-check-circle text-green-500 text-lg lg:text-xl mr-3"></i>
                            <span class="text-gray-700 text-sm lg:text-base">{{ __('Financial reporting and analytics') }}</span>
                        </div>
                    </div>
                    <div class="mt-6 lg:mt-8 text-center lg:text-left">
                        <a href="#register" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold inline-block">
                            <i class="fas fa-user-plus mr-2"></i>
                            {{ __('Free Registration') }}
                        </a>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                         alt="Property Management" class="rounded-2xl shadow-xl">
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ __('Growth Analytics') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Track your rental income') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('Powerful Features') }}
                </h2>
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('Everything you need to manage your rental properties efficiently and grow your business with Bari Manager') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="bg-purple-100 p-3 lg:p-4 rounded-full w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center mb-4 lg:mb-6">
                        <i class="fas fa-home text-purple-600 text-lg lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Property Management') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Easily manage multiple properties, units, and their details in one centralized platform.') }}</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="bg-blue-100 p-3 lg:p-4 rounded-full w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center mb-4 lg:mb-6">
                        <i class="fas fa-users text-blue-600 text-lg lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Tenant Management') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Complete tenant profiles, rent history, and communication tools all in one place.') }}</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="bg-green-100 p-3 lg:p-4 rounded-full w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center mb-4 lg:mb-6">
                        <i class="fas fa-money-bill-wave text-green-600 text-lg lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Rent Collection') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Automated rent tracking, payment reminders, and financial reporting.') }}</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="bg-yellow-100 p-3 lg:p-4 rounded-full w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center mb-4 lg:mb-6">
                        <i class="fas fa-tools text-yellow-600 text-lg lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Maintenance') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Track maintenance requests, assign tasks, and monitor completion status.') }}</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="bg-red-100 p-3 lg:p-4 rounded-full w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center mb-4 lg:mb-6">
                        <i class="fas fa-chart-bar text-red-600 text-lg lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Analytics') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Comprehensive reports and analytics to track your rental business performance.') }}</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="bg-indigo-100 p-3 lg:p-4 rounded-full w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center mb-4 lg:mb-6">
                        <i class="fas fa-mobile-alt text-indigo-600 text-lg lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Mobile App') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Access your property management system anywhere with our mobile application.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('How It Works') }}
                </h2>
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('Get started with Bari Manager in just 3 simple steps') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <div class="text-center">
                    <div class="bg-purple-100 p-4 lg:p-6 rounded-full w-16 h-16 lg:w-20 lg:h-20 flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <span class="text-2xl lg:text-3xl font-bold text-purple-600">1</span>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Sign Up') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Create your free account and set up your profile in minutes.') }}</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 p-4 lg:p-6 rounded-full w-16 h-16 lg:w-20 lg:h-20 flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <span class="text-2xl lg:text-3xl font-bold text-blue-600">2</span>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Add Properties') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Add your properties and units to start managing them.') }}</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 p-4 lg:p-6 rounded-full w-16 h-16 lg:w-20 lg:h-20 flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <span class="text-2xl lg:text-3xl font-bold text-green-600">3</span>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Start Managing') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Begin managing tenants, collecting rent, and growing your business.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section id="packages" class="py-12 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('Choose Your Plan') }}
                </h2>
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('Start with our free plan and upgrade as your business grows with Bari Manager') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-{{ count($plans) > 3 ? '4' : count($plans) }} gap-6 lg:gap-8">
                @foreach($plans as $plan)
                <div class="package-card bg-white p-6 lg:p-8 rounded-2xl shadow-lg border-2 {{ $plan->is_popular ? 'border-purple-500 relative' : 'border-gray-200' }}">
                    @if($plan->is_popular)
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-purple-600 text-white px-3 lg:px-4 py-1 lg:py-2 rounded-full text-xs lg:text-sm font-semibold">{{ __('Most Popular') }}</span>
                    </div>
                    @endif
                    <div class="text-center mb-6 lg:mb-8">
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <div class="text-3xl lg:text-4xl font-bold text-purple-600 mb-2">
                            @if($plan->price == 0)
                                ৳0
                            @else
                                ৳{{ number_format($plan->price) }}
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm lg:text-base">
                            @if($plan->price == 0)
                                {{ __('Perfect for getting started') }}
                            @else
                                {{ __('Per month') }}
                            @endif
                        </p>
                    </div>
                    <ul class="space-y-3 lg:space-y-4 mb-6 lg:mb-8">
                        @if($plan->properties_limit > 0)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Up to :limit properties/buildings', ['limit' => $plan->properties_limit]) }}</span>
                        </li>
                        @else
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Unlimited properties/buildings') }}</span>
                        </li>
                        @endif

                        @if($plan->tenants_limit > 0)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Up to :limit tenants', ['limit' => $plan->tenants_limit]) }}</span>
                        </li>
                        @else
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Unlimited tenants') }}</span>
                        </li>
                        @endif

                        @if($plan->units_limit > 0)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Up to :limit units/flats', ['limit' => $plan->units_limit]) }}</span>
                        </li>
                        @else
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Unlimited units/flats') }}</span>
                        </li>
                        @endif

                        @if($plan->features)
                        @foreach($plan->features as $index => $feature)
                        <li class="flex items-center">
                                                                                    @if(isset($plan->features_css[$index]))
                                @php
                                    $cssString = $plan->features_css[$index];
                                    $iconClass = 'mr-3 text-sm lg:text-base ';
                                    $textClass = 'text-sm lg:text-base ';

                                    // Simple logic: if contains 'fa-times', it's unavailable
                                    if(str_contains($cssString, 'fa-times')) {
                                        $iconClass .= 'fas fa-times text-red-500';
                                        $textClass .= 'text-gray-500';
                                    } else {
                                        $iconClass .= 'fas fa-check text-green-500';
                                        $textClass .= 'text-gray-900';
                                    }
                                @endphp
                                <i class="{{ $iconClass }}"></i>
                                <span class="{{ $textClass }}">{{ $feature }}</span>
                            @else
                                <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                                <span class="text-gray-900 text-sm lg:text-base">{{ $feature }}</span>
                            @endif
                        </li>
                        @endforeach
                        @endif

                        @if($plan->price == 0)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Email support') }}</span>
                        </li>
                        @elseif($plan->price >= 99)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('24/7 support') }}</span>
                        </li>
                        @else
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">{{ __('Priority support') }}</span>
                        </li>
                        @endif
                    </ul>
                    <a href="{{ $plan->price >= 99 ? route('owner.register.with.plan.subscribe', $plan->id) : route('owner.register.form') }}" class="w-full {{ $plan->is_popular ? 'btn-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} py-3 rounded-lg font-semibold text-center block transition-colors text-sm lg:text-base">
                        @if($plan->price == 0)
                            {{ __('Get Started Free') }}
                        @elseif($plan->price >= 99)
                            {{ __('Contact Sales') }}
                        @else
                            {{ __('Start :plan Trial', ['plan' => $plan->name]) }}
                        @endif
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('What Our Customers Say') }}
                </h2>
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('Join thousands of satisfied property owners') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 text-lg lg:text-xl">★★★★★</div>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm lg:text-base">{{ __('"Bari Manager has completely transformed how I manage my properties. The automated rent collection feature alone has saved me hours every month."') }}</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-purple-600 font-semibold text-sm lg:text-base">AM</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ __('Ahmed Mahmud') }}</h4>
                            <p class="text-xs lg:text-sm text-gray-600">{{ __('Property Owner') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 text-lg lg:text-xl">★★★★★</div>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm lg:text-base">{{ __('"The mobile app is fantastic! I can check my rental income and manage tenants from anywhere. Highly recommended!"') }}</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-600 font-semibold text-sm lg:text-base">SK</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ __('Sara Khan') }}</h4>
                            <p class="text-xs lg:text-sm text-gray-600">{{ __('Real Estate Investor') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 text-lg lg:text-xl">★★★★★</div>
                    </div>
                    <p class="text-gray-600 mb-6 text-sm lg:text-base">{{ __('"Excellent customer support and easy to use interface. My rental business has grown 200% since using Bari Manager."') }}</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-semibold text-sm lg:text-base">MR</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ __('Mohammad Rahman') }}</h4>
                            <p class="text-xs lg:text-sm text-gray-600">{{ __('Property Manager') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login/Registration Section -->
    <section id="register" class="py-12 lg:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 lg:mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('Get Started Today') }}
                </h2>
                <p class="text-lg lg:text-xl text-gray-600">
                    {{ __('Join thousands of property owners who trust Bari Manager') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                <!-- Registration -->
                <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4 lg:mb-6">{{ __('Create Account') }}</h3>
                    <form class="space-y-3 lg:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Full Name') }}</label>
                            <input type="text" class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm lg:text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                            <input type="email" class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm lg:text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                            <input type="password" class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm lg:text-base">
                        </div>
                        <button type="submit" class="w-full btn-primary text-white py-2 lg:py-3 rounded-lg font-semibold text-sm lg:text-base">
                            {{ __('Create Free Account') }}
                        </button>
                    </form>
                </div>

                <!-- Login -->
                <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4 lg:mb-6">{{ __('Sign In') }}</h3>
                    <form class="space-y-3 lg:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                            <input type="email" class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm lg:text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                            <input type="password" class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm lg:text-base">
                        </div>
                        <button type="submit" class="w-full btn-primary text-white py-2 lg:py-3 rounded-lg font-semibold text-sm lg:text-base">
                            {{ __('Sign In') }}
                        </button>
                    </form>
                    <div class="mt-4 lg:mt-6 text-center">
                        <a href="#" class="text-purple-600 hover:text-purple-700 font-medium text-sm lg:text-base">{{ __('Forgot password?') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <section id="support" class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('We\'re Here to Help') }}
                </h2>
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('Get the support you need to succeed with Bari Manager') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <div class="text-center">
                    <div class="bg-purple-100 p-4 lg:p-6 rounded-full w-16 h-16 lg:w-20 lg:h-20 flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <i class="fas fa-headset text-purple-600 text-2xl lg:text-3xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('24/7 Support') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Our support team is available around the clock to help you with any questions.') }}</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 p-4 lg:p-6 rounded-full w-16 h-16 lg:w-20 lg:h-20 flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <i class="fas fa-book text-blue-600 text-2xl lg:text-3xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Documentation') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Comprehensive guides and tutorials to help you get the most out of Bari Manager.') }}</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 p-4 lg:p-6 rounded-full w-16 h-16 lg:w-20 lg:h-20 flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <i class="fas fa-video text-green-600 text-2xl lg:text-3xl"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 lg:mb-4">{{ __('Video Tutorials') }}</h3>
                    <p class="text-gray-600 text-sm lg:text-base">{{ __('Step-by-step video guides to help you master every feature of Bari Manager.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center sm:text-left">
                    <a href="#home" class="cursor-pointer">
                        <img src="{{ asset('images/bari-manager-logo.svg') }}" alt="Bari Manager Logo" class="h-8 lg:h-10 w-auto mb-4 hover:opacity-80 transition-opacity mx-auto sm:mx-0">
                    </a>
                    <p class="text-gray-400 mb-4 text-sm lg:text-base">
                        {{ __('The complete house rent management solution for property owners.') }}
                    </p>
                    <div class="flex space-x-4 justify-center sm:justify-start">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-lg lg:text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-lg lg:text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin text-lg lg:text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-lg lg:text-xl"></i>
                        </a>
                    </div>
                </div>

                <div class="text-center sm:text-left">
                    <h4 class="text-base lg:text-lg font-semibold mb-4">{{ __('Product') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Features') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Pricing') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Mobile App') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('API') }}</a></li>
                    </ul>
                </div>

                <div class="text-center sm:text-left">
                    <h4 class="text-base lg:text-lg font-semibold mb-4">{{ __('Support') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Help Center') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Contact Us') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Documentation') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Status') }}</a></li>
                    </ul>
                </div>

                <div class="text-center sm:text-left">
                    <h4 class="text-base lg:text-lg font-semibold mb-4">{{ __('Legal') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('terms') }}" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Terms & Conditions') }}</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('refund') }}" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('Refund Policy') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm lg:text-base">{{ __('About') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 lg:mt-12 pt-6 lg:pt-8 text-center">
                <p class="text-gray-400 text-sm lg:text-base">
                    © 2024 {{ __('Bari Manager') }}. {{ __('All rights reserved.') }} | {{ __('House Rent Management System') }}
                </p>
            </div>
        </div>
    </footer>

    <!-- Language Switcher Script -->
    <script>
        function changeLanguage(lang) {
            window.location.href = '{{ route("language.switch") }}?lang=' + lang;
        }

        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });

                // Close mobile menu when clicking on a link
                const mobileMenuLinks = mobileMenu.querySelectorAll('a');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                    });
                });
            }
        });

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Active Navigation Highlighting
        function updateActiveNav() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');

            let current = '';

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.pageYOffset >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === current) {
                    link.classList.add('active');
                }
            });
        }

        // Update active nav on scroll
        window.addEventListener('scroll', updateActiveNav);

        // Update active nav on page load
        document.addEventListener('DOMContentLoaded', updateActiveNav);

        // Update active nav when clicking nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                this.classList.add('active');
            });
        });
    </script>

    <!-- Live Chat Widget -->
    <div id="live-chat-widget" class="fixed bottom-4 right-4 z-50">
        <!-- Chat Button -->
        <div id="chat-button" class="bg-purple-600 hover:bg-purple-700 text-white rounded-full p-4 shadow-lg cursor-pointer transition-all duration-300 transform hover:scale-110">
            <i class="fas fa-comments text-xl"></i>
        </div>
        
        <!-- Chat Window -->
        <div id="chat-window" class="hidden bg-white rounded-lg shadow-2xl w-80 h-96 mb-4 border border-gray-200">
            <!-- Chat Header -->
            <div class="bg-purple-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                    <span class="font-semibold">Bari Manager Support</span>
                </div>
                <button id="close-chat" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-3">
                <!-- Welcome Message -->
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-headset text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                        <p class="text-sm text-gray-800">Hello! 👋 Welcome to Bari Manager. How can I help you today?</p>
                    </div>
                </div>
                
                <!-- Quick Options -->
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-headset text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                        <p class="text-sm text-gray-800 mb-2">Quick options:</p>
                        <div class="space-y-1">
                            <button class="quick-option text-xs bg-white px-2 py-1 rounded border hover:bg-gray-50" data-option="pricing">💰 Pricing & Plans</button>
                            <button class="quick-option text-xs bg-white px-2 py-1 rounded border hover:bg-gray-50" data-option="demo">🎥 Request Demo</button>
                            <button class="quick-option text-xs bg-white px-2 py-1 rounded border hover:bg-gray-50" data-option="support">🛠️ Technical Support</button>
                            <button class="quick-option text-xs bg-white px-2 py-1 rounded border hover:bg-gray-50" data-option="features">✨ Features</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex space-x-2">
                    <input type="text" id="chat-input" placeholder="Type your message..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <button id="send-message" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Chat JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatButton = document.getElementById('chat-button');
            const chatWindow = document.getElementById('chat-window');
            const closeChat = document.getElementById('close-chat');
            const chatMessages = document.getElementById('chat-messages');
            const chatInput = document.getElementById('chat-input');
            const sendMessage = document.getElementById('send-message');
            
            // Generate session ID
            const sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            // Chat responses
            const responses = {
                'pricing': {
                    message: 'Our pricing plans start from free! We offer:\n\n🆓 Free Plan: Up to 5 properties\n💰 Basic Plan: $9/month - 20 properties\n🚀 Pro Plan: $19/month - Unlimited properties\n\nWould you like to see detailed features?',
                    intent: 'pricing'
                },
                'demo': {
                    message: 'Great! I\'d be happy to arrange a demo for you. Please provide your email and preferred time, or you can schedule directly at: https://barimanager.com/demo',
                    intent: 'demo'
                },
                'support': {
                    message: 'For technical support, you can:\n\n📧 Email: support@barimanager.com\n📞 Phone: +880-1234-567890\n🕒 Hours: 9 AM - 6 PM (GMT+6)\n\nWhat specific issue are you facing?',
                    intent: 'support'
                },
                'features': {
                    message: 'Bari Manager includes:\n\n🏠 Property Management\n👥 Tenant Management\n💰 Rent Collection\n🛠️ Maintenance Tracking\n📊 Analytics & Reports\n📱 Mobile App\n\nWhich feature interests you most?',
                    intent: 'features'
                }
            };
            
            // Store message in database
            async function storeMessage(message, messageType, intent = null) {
                try {
                    const response = await fetch('{{ route("chat.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            session_id: sessionId,
                            message: message,
                            message_type: messageType,
                            intent: intent
                        })
                    });
                    
                    if (!response.ok) {
                        console.error('Failed to store chat message');
                    }
                } catch (error) {
                    console.error('Error storing chat message:', error);
                }
            }
            
            // Toggle chat window
            chatButton.addEventListener('click', function() {
                chatWindow.classList.toggle('hidden');
                chatButton.classList.toggle('hidden');
            });
            
            closeChat.addEventListener('click', function() {
                chatWindow.classList.add('hidden');
                chatButton.classList.remove('hidden');
            });
            
            // Send message
            function sendUserMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex items-start space-x-2 justify-end';
                messageDiv.innerHTML = `
                    <div class="bg-purple-600 text-white rounded-lg p-3 max-w-xs">
                        <p class="text-sm">${message}</p>
                    </div>
                    <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                `;
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Store user message
                storeMessage(message, 'user');
            }
            
            function sendBotMessage(message, intent = null) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex items-start space-x-2';
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-headset text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                        <p class="text-sm text-gray-800 whitespace-pre-line">${message}</p>
                    </div>
                `;
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Store bot message
                storeMessage(message, 'bot', intent);
            }
            
            // Handle send button
            sendMessage.addEventListener('click', function() {
                const message = chatInput.value.trim();
                if (message) {
                    sendUserMessage(message);
                    chatInput.value = '';
                    
                    // Simulate bot response
                    setTimeout(() => {
                        const botResponse = getBotResponse(message.toLowerCase());
                        sendBotMessage(botResponse.message, botResponse.intent);
                        
                        // Handle agent transfer
                        if (botResponse.transfer) {
                            requestAgentTransfer();
                        }
                    }, 1000);
                }
            });
            
            // Handle enter key
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage.click();
                }
            });
            
            // Quick option buttons
            document.querySelectorAll('.quick-option').forEach(button => {
                button.addEventListener('click', function() {
                    const option = this.dataset.option;
                    const response = responses[option];
                    if (response) {
                        sendUserMessage(this.textContent);
                        setTimeout(() => {
                            sendBotMessage(response.message, response.intent);
                        }, 500);
                    }
                });
            });
            
            // Get bot response based on user message
            function getBotResponse(message) {
                if (message.includes('price') || message.includes('cost') || message.includes('plan')) {
                    return responses.pricing;
                } else if (message.includes('demo') || message.includes('show') || message.includes('tour')) {
                    return responses.demo;
                } else if (message.includes('support') || message.includes('help') || message.includes('issue')) {
                    return responses.support;
                } else if (message.includes('feature') || message.includes('what') || message.includes('can')) {
                    return responses.features;
                } else if (message.includes('agent') || message.includes('human') || message.includes('person')) {
                    return {
                        message: 'I\'m transferring you to a human agent. Please wait a moment...',
                        intent: 'agent_transfer',
                        transfer: true
                    };
                } else {
                    return {
                        message: 'Thank you for your message! Our team will get back to you soon. In the meantime, you can check our pricing plans or request a demo. How else can I help you?',
                        intent: 'general'
                    };
                }
            }
            
            // Request agent transfer
            async function requestAgentTransfer() {
                try {
                    const response = await fetch('{{ route("chat.request-agent") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            session_id: sessionId,
                            reason: 'User requested human agent'
                        })
                    });
                    
                    if (response.ok) {
                        sendBotMessage('I\'m transferring you to a human agent. Please wait a moment...', 'agent_transfer');
                        
                        // Add a button to check agent availability
                        const checkAgentDiv = document.createElement('div');
                        checkAgentDiv.className = 'flex items-start space-x-2';
                        checkAgentDiv.innerHTML = `
                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-headset text-white text-sm"></i>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                <p class="text-sm text-gray-800 mb-2">Checking agent availability...</p>
                                <button id="check-agent-btn" class="text-xs bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">
                                    Check Status
                                </button>
                            </div>
                        `;
                        chatMessages.appendChild(checkAgentDiv);
                        
                        // Add event listener to check agent button
                        document.getElementById('check-agent-btn').addEventListener('click', checkAgentStatus);
                    }
                } catch (error) {
                    console.error('Error requesting agent transfer:', error);
                }
            }
            
            // Check agent status
            async function checkAgentStatus() {
                try {
                    const response = await fetch('{{ route("chat.agent-availability") }}');
                    const data = await response.json();
                    
                    if (data.success) {
                        if (data.data.agent_available) {
                            sendBotMessage('Great! An agent is available and will join the conversation shortly.', 'agent_status');
                        } else {
                            sendBotMessage('Agents are currently busy. Please wait or try again later.', 'agent_status');
                        }
                    }
                } catch (error) {
                    console.error('Error checking agent status:', error);
                }
            }
            
            // Auto-hide chat after 30 seconds of inactivity
            let chatTimeout;
            function resetChatTimeout() {
                clearTimeout(chatTimeout);
                chatTimeout = setTimeout(() => {
                    if (!chatWindow.classList.contains('hidden')) {
                        chatWindow.classList.add('hidden');
                        chatButton.classList.remove('hidden');
                    }
                }, 30000);
            }
            
            // Reset timeout on any interaction
            chatButton.addEventListener('click', resetChatTimeout);
            chatInput.addEventListener('input', resetChatTimeout);
            sendMessage.addEventListener('click', resetChatTimeout);
        });
    </script>

</body>
</html>
