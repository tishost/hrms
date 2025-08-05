<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - {{ \App\Helpers\SystemHelper::getCompanyName() }}</title>
    
    <!-- Dynamic Favicon -->
    @if(\App\Helpers\SystemHelper::getCompanyFavicon())
        <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SystemHelper::getCompanyFavicon() }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ \App\Helpers\SystemHelper::getCompanyFavicon() }}">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .maintenance-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="maintenance-gradient min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto text-center text-white p-8">
        <div class="mb-8">
            @if(\App\Helpers\SystemHelper::getCompanyLogo())
                <img src="{{ \App\Helpers\SystemHelper::getCompanyLogo() }}" 
                     alt="{{ \App\Helpers\SystemHelper::getCompanyName() }}" 
                     class="mx-auto mb-6" style="max-height: 80px;">
            @else
                <i class="fas fa-tools text-6xl mb-6"></i>
            @endif
        </div>
        
        <h1 class="text-3xl font-bold mb-4">
            <i class="fas fa-wrench mr-3"></i>
            Under Maintenance
        </h1>
        
        <p class="text-lg mb-6 opacity-90">
            We're currently performing scheduled maintenance to improve our services. 
            Please check back soon.
        </p>
        
        <div class="bg-white bg-opacity-10 rounded-lg p-6 mb-6">
            <h3 class="font-semibold mb-2">
                <i class="fas fa-clock mr-2"></i>
                Expected Duration
            </h3>
            <p class="text-sm opacity-80">
                We expect to be back online within a few hours. 
                Thank you for your patience.
            </p>
        </div>
        
        <div class="space-y-4">
            <div class="flex items-center justify-center space-x-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-cog fa-spin text-xl"></i>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-server text-xl"></i>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-database text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-sm opacity-70">
            <p>{{ \App\Helpers\SystemHelper::getCompanyName() }}</p>
            <p class="mt-2">
                <i class="fas fa-envelope mr-2"></i>
                {{ \App\Helpers\SystemHelper::getSetting('company_support_email', 'support@hrms.com') }}
            </p>
        </div>
    </div>
</body>
</html> 