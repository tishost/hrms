<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Refund Policy - Bari Manager') }}</title>
    
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
                        <a href="{{ route('terms') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ __('Terms') }}</a>
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

    <!-- Refund Header -->
    <section class="pt-24 pb-12 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">
                {{ __('Refund Policy') }}
            </h1>
            <p class="text-xl text-purple-100">
                {{ __('Our commitment to customer satisfaction') }}
            </p>
        </div>
    </section>

    <!-- Refund Content -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg max-w-none">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('1. Overview') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('At Bari Manager, we strive to provide the best possible service to our customers. We understand that sometimes our service may not meet your expectations, and we want to ensure you have a clear understanding of our refund policy.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('2. Free Plan') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Our free plan is available at no cost and does not require any payment. Therefore, no refund is applicable for the free plan.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('3. Paid Subscriptions') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('For paid subscriptions, we offer the following refund terms:') }}
                    </p>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-green-800 mb-4">{{ __('30-Day Money-Back Guarantee') }}</h3>
                        <p class="text-green-700 mb-4">
                            {{ __('We offer a 30-day money-back guarantee for all new paid subscriptions. If you are not satisfied with our service within the first 30 days of your subscription, you can request a full refund.') }}
                        </p>
                        <ul class="list-disc list-inside text-green-700 space-y-2">
                            <li>{{ __('Full refund of your subscription payment') }}</li>
                            <li>{{ __('No questions asked within the first 30 days') }}</li>
                            <li>{{ __('Available for all paid plans (Pro and Enterprise)') }}</li>
                            <li>{{ __('Refund processed within 5-7 business days') }}</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('4. Refund Eligibility') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('To be eligible for a refund, you must meet the following criteria:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Request made within 30 days of initial subscription') }}</li>
                        <li>{{ __('Valid payment method and transaction') }}</li>
                        <li>{{ __('No violation of our Terms of Service') }}</li>
                        <li>{{ __('Account in good standing') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('5. Non-Refundable Items') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('The following items are not eligible for refunds:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Subscriptions after the 30-day guarantee period') }}</li>
                        <li>{{ __('Add-on services and custom integrations') }}</li>
                        <li>{{ __('Training and consultation services') }}</li>
                        <li>{{ __('Accounts suspended for Terms of Service violations') }}</li>
                        <li>{{ __('Partial refunds for unused periods') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('6. How to Request a Refund') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('To request a refund, please follow these steps:') }}
                    </p>
                    <ol class="list-decimal list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Contact our support team at support@barimanager.com') }}</li>
                        <li>{{ __('Include your account email and subscription details') }}</li>
                        <li>{{ __('Provide a brief reason for your refund request') }}</li>
                        <li>{{ __('Our team will review and process your request within 2 business days') }}</li>
                    </ol>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('7. Refund Processing') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Once your refund is approved:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Refund will be processed within 5-7 business days') }}</li>
                        <li>{{ __('You will receive an email confirmation') }}</li>
                        <li>{{ __('Your account access will be suspended immediately') }}</li>
                        <li>{{ __('All data will be permanently deleted after 30 days') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('8. Cancellation Policy') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('You can cancel your subscription at any time:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Cancel through your account dashboard') }}</li>
                        <li>{{ __('Contact our support team') }}</li>
                        <li>{{ __('No cancellation fees') }}</li>
                        <li>{{ __('Access continues until the end of your billing period') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('9. Dispute Resolution') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('If you disagree with our refund decision, you may:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Request a review by our management team') }}</li>
                        <li>{{ __('Provide additional documentation or evidence') }}</li>
                        <li>{{ __('Escalate to our customer relations department') }}</li>
                        <li>{{ __('Contact us through multiple channels for resolution') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('10. Contact Information') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('For refund requests and questions about this policy, please contact us:') }}
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">
                            <strong>{{ __('Email:') }}</strong> refunds@barimanager.com<br>
                            <strong>{{ __('Phone:') }}</strong> +880 1712-345-678<br>
                            <strong>{{ __('Support Hours:') }}</strong> {{ __('Monday - Friday, 9:00 AM - 6:00 PM (GMT+6)') }}<br>
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