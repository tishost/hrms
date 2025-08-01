<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Privacy Policy - Bari Manager') }}</title>
    
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
                        <a href="{{ route('privacy') }}" class="text-purple-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Privacy') }}</a>
                        
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

    <!-- Privacy Header -->
    <section class="pt-24 pb-12 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">
                {{ __('Privacy Policy') }}
            </h1>
            <p class="text-xl text-purple-100">
                {{ __('How we collect, use, and protect your information') }}
            </p>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg max-w-none">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('1. Information We Collect') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support. This may include:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Personal identification information (name, email address, phone number)') }}</li>
                        <li>{{ __('Property and tenant information') }}</li>
                        <li>{{ __('Financial and payment information') }}</li>
                        <li>{{ __('Communication preferences and support history') }}</li>
                        <li>{{ __('Usage data and analytics') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('2. How We Use Your Information') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We use the information we collect to:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Provide, maintain, and improve our services') }}</li>
                        <li>{{ __('Process transactions and send related information') }}</li>
                        <li>{{ __('Send technical notices, updates, security alerts, and support messages') }}</li>
                        <li>{{ __('Respond to your comments, questions, and customer service requests') }}</li>
                        <li>{{ __('Monitor and analyze trends, usage, and activities in connection with our services') }}</li>
                        <li>{{ __('Detect, investigate, and prevent fraudulent transactions and other illegal activities') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('3. Information Sharing and Disclosure') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except in the following circumstances:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('With your explicit consent') }}</li>
                        <li>{{ __('To comply with legal obligations or court orders') }}</li>
                        <li>{{ __('To protect our rights, property, or safety') }}</li>
                        <li>{{ __('In connection with a business transfer or merger') }}</li>
                        <li>{{ __('With service providers who assist us in operating our services') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('4. Data Security') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Encryption of data in transit and at rest') }}</li>
                        <li>{{ __('Regular security assessments and updates') }}</li>
                        <li>{{ __('Access controls and authentication measures') }}</li>
                        <li>{{ __('Employee training on data protection practices') }}</li>
                        <li>{{ __('Incident response and breach notification procedures') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('5. Data Retention') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We retain your personal information for as long as necessary to provide our services and fulfill the purposes outlined in this policy, unless a longer retention period is required or permitted by law.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('6. Your Rights and Choices') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('You have certain rights regarding your personal information, including:') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>{{ __('Access to your personal information') }}</li>
                        <li>{{ __('Correction of inaccurate or incomplete information') }}</li>
                        <li>{{ __('Deletion of your personal information') }}</li>
                        <li>{{ __('Restriction of processing') }}</li>
                        <li>{{ __('Data portability') }}</li>
                        <li>{{ __('Objection to processing') }}</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('7. Cookies and Tracking Technologies') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We use cookies and similar tracking technologies to enhance your experience, analyze usage, and provide personalized content. You can control cookie settings through your browser preferences.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('8. Third-Party Services') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Our services may contain links to third-party websites or services. We are not responsible for the privacy practices of these third parties. We encourage you to review their privacy policies.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('9. Children\'s Privacy') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Our services are not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you believe we have collected such information, please contact us immediately.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('10. International Data Transfers') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('Your information may be transferred to and processed in countries other than your own. We ensure that such transfers comply with applicable data protection laws and implement appropriate safeguards.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('11. Changes to This Policy') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.') }}
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('12. Contact Us') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('If you have any questions about this Privacy Policy or our data practices, please contact us at:') }}
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">
                            <strong>{{ __('Email:') }}</strong> privacy@barimanager.com<br>
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