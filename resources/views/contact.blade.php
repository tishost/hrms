<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Contact Us - Bari Manager') }}</title>
    
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
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .contact-card {
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
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
                        <a href="{{ route('contact') }}" class="text-purple-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Contact') }}</a>
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

    <!-- Contact Header -->
    <section class="pt-24 pb-12 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">
                {{ __('Contact Us') }}
            </h1>
            <p class="text-xl text-purple-100">
                {{ __('Get in touch with our support team') }}
            </p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <!-- Address -->
                <div class="contact-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 text-center">
                    <div class="bg-purple-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-map-marker-alt text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Office Address') }}</h3>
                    <p class="text-gray-600">
                        {{ __('House #123, Road #4') }}<br>
                        {{ __('Block #A, Banani') }}<br>
                        {{ __('Dhaka-1213, Bangladesh') }}
                    </p>
                </div>

                <!-- Phone -->
                <div class="contact-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 text-center">
                    <div class="bg-green-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Phone Number') }}</h3>
                    <p class="text-gray-600">
                        <a href="tel:+8801712345678" class="hover:text-purple-600 transition-colors">
                            +880 1712-345-678
                        </a><br>
                        <a href="tel:+8801812345678" class="hover:text-purple-600 transition-colors">
                            +880 1812-345-678
                        </a>
                    </p>
                </div>

                <!-- Email -->
                <div class="contact-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 text-center">
                    <div class="bg-blue-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Email Address') }}</h3>
                    <p class="text-gray-600">
                        <a href="mailto:support@barimanager.com" class="hover:text-purple-600 transition-colors">
                            support@barimanager.com
                        </a><br>
                        <a href="mailto:info@barimanager.com" class="hover:text-purple-600 transition-colors">
                            info@barimanager.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                                 <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                     {{ __('Send us a Message') }}
                 </h2>
                 
                 <!-- Success Message -->
                 @if(session('success'))
                     <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                         <div class="flex items-center">
                             <div class="flex-shrink-0">
                                 <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                             </div>
                             <div class="ml-3">
                                 <h3 class="text-lg font-medium text-green-800">
                                     {{ __('Message Sent Successfully!') }}
                                 </h3>
                                 <div class="mt-2 text-green-700">
                                     <p>{{ session('success') }}</p>
                                     @if(session('ticket_number'))
                                         <p class="mt-2 font-semibold">
                                             {{ __('Ticket Number:') }} 
                                             <span class="bg-green-100 px-2 py-1 rounded text-green-800 font-mono">
                                                 {{ session('ticket_number') }}
                                             </span>
                                         </p>
                                         <p class="mt-2 text-sm">
                                             {{ __('Please save this ticket number for future reference.') }}
                                         </p>
                                     @endif
                                 </div>
                             </div>
                         </div>
                     </div>
                 @endif
                 
                 <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Full Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="{{ __('Enter your full name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Mobile Number') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="mobile" name="mobile" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="{{ __('Enter your mobile number') }}">
                        @error('mobile')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Email Address') }} <span class="text-gray-500">({{ __('Optional') }})</span>
                        </label>
                        <input type="email" id="email" name="email"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="{{ __('Enter your email address') }}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Details -->
                    <div>
                        <label for="details" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Message Details') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="details" name="details" rows="5" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                  placeholder="{{ __('Describe your inquiry or issue in detail') }}"></textarea>
                        @error('details')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Security Check -->
                    <div>
                        <label for="security_check" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Security Check') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <span class="text-lg font-bold text-gray-700">{{ $captcha ?? '5 + 3 = ?' }}</span>
                            </div>
                            <input type="number" id="security_check" name="security_check" required
                                   class="w-32 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="{{ __('Answer') }}">
                            <input type="hidden" name="expected_answer" value="{{ $expectedAnswer ?? 8 }}">
                        </div>
                        @error('security_check')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn-primary text-white px-8 py-3 rounded-lg font-semibold text-lg">
                            <i class="fas fa-paper-plane mr-2"></i>
                            {{ __('Send Message') }}
                        </button>
                    </div>
                </form>
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
        
        // Restore form values on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get old input values from PHP session
            @if(old('name'))
                document.getElementById('name').value = '{{ old("name") }}';
            @endif
            
            @if(old('mobile'))
                document.getElementById('mobile').value = '{{ old("mobile") }}';
            @endif
            
            @if(old('email'))
                document.getElementById('email').value = '{{ old("email") }}';
            @endif
            
            @if(old('details'))
                document.getElementById('details').value = '{{ old("details") }}';
            @endif
            
            @if(old('security_check'))
                document.getElementById('security_check').value = '{{ old("security_check") }}';
            @endif
        });
    </script>
</body>
</html> 