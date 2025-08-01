<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Terms & Conditions - Bari Manager') }}</title>
    
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
        
        .language-switch {
            transition: all 0.3s ease;
        }
        
        .language-switch:hover {
            transform: scale(1.1);
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
                        <a href="{{ route('home') }}" class="cursor-pointer">
                            <img src="{{ asset('images/bari-manager-logo.svg') }}" alt="Bari Manager Logo" class="h-12 w-auto hover:opacity-80 transition-opacity">
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ __('Home') }}</a>
                        <a href="{{ route('contact') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ __('Contact') }}</a>
                        <a href="{{ route('terms') }}" class="text-purple-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Terms') }}</a>
                        <a href="{{ route('privacy') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ __('Privacy') }}</a>
                        
                        <!-- Language Switcher -->
                        <div class="flex items-center space-x-2 ml-4">
                            <button onclick="changeLanguage('en')" class="language-switch px-2 py-1 rounded text-xs font-medium {{ app()->getLocale() == 'en' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">EN</button>
                            <button onclick="changeLanguage('bn')" class="language-switch px-2 py-1 rounded text-xs font-medium {{ app()->getLocale() == 'bn' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">বাং</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Terms Header -->
    <section class="pt-24 pb-12 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">
                {{ __('Terms & Conditions') }}
            </h1>
            <p class="text-xl text-purple-100">
                {{ __('Please read these terms carefully before using our service') }}
            </p>
        </div>
    </section>

    <!-- Terms Content -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg max-w-none">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('1. Acceptance of Terms') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('By accessing and using Bari Manager, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('2. Use License') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Permission is granted to temporarily download one copy of Bari Manager for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Modify or copy the materials') }}</li>
                        <li>{{ __('Use the materials for any commercial purpose or for any public display') }}</li>
                        <li>{{ __('Attempt to reverse engineer any software contained in Bari Manager') }}</li>
                        <li>{{ __('Remove any copyright or other proprietary notations from the materials') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('3. Service Description') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Bari Manager is a house rent management system that provides property owners with tools to manage their rental properties, tenants, and financial transactions. Our services include:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Property and tenant management') }}</li>
                        <li>{{ __('Rent collection and tracking') }}</li>
                        <li>{{ __('Financial reporting and analytics') }}</li>
                        <li>{{ __('Mobile application access') }}</li>
                        <li>{{ __('Customer support services') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('4. User Responsibilities') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('As a user of Bari Manager, you agree to:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Provide accurate and complete information') }}</li>
                        <li>{{ __('Maintain the security of your account credentials') }}</li>
                        <li>{{ __('Comply with all applicable laws and regulations') }}</li>
                        <li>{{ __('Not use the service for any illegal or unauthorized purpose') }}</li>
                        <li>{{ __('Report any security concerns immediately') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('5. Payment Terms') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Subscription fees are billed in advance on a monthly or annual basis. All payments are non-refundable except as specified in our refund policy. We reserve the right to modify our pricing with 30 days notice.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('6. Privacy and Data Protection') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the service, to understand our practices regarding the collection and use of your personal information.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('7. Intellectual Property') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('The service and its original content, features, and functionality are and will remain the exclusive property of Bari Manager and its licensors. The service is protected by copyright, trademark, and other laws.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('8. Limitation of Liability') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('In no event shall Bari Manager, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('9. Termination') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We may terminate or suspend your account and bar access to the service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever and without limitation, including but not limited to a breach of the Terms.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('10. Changes to Terms') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('11. Contact Information') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('If you have any questions about these Terms and Conditions, please contact us at:') }}
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">
                            <strong>{{ __('Email:') }}</strong> legal@barimanager.com<br>
                            <strong>{{ __('Phone:') }}</strong> +880 1712-345-678<br>
                            <strong>{{ __('Address:') }}</strong> House #123, Road #4, Block #A, Banani, Dhaka-1213, Bangladesh
                        </p>
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <p class="text-blue-800 text-sm">
                            <strong>{{ __('Last Updated:') }}</strong> {{ __('July 30, 2024') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="{{ route('home') }}" class="cursor-pointer">
                        <img src="{{ asset('images/bari-manager-logo.svg') }}" alt="Bari Manager Logo" class="h-10 w-auto mb-4 hover:opacity-80 transition-opacity">
                    </a>
                    <p class="text-gray-400 mb-4">
                        {{ __('The complete house rent management solution for property owners.') }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('Product') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Features') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Pricing') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Mobile App') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('API') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('Support') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Help Center') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('Contact Us') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Documentation') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Status') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('Company') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('About') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Blog') }}</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">{{ __('Careers') }}</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('Privacy') }}</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">
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
    </script>
</body>
</html> 