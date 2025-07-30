<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <script src="{{ asset('js/owner-dashboard.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Force CSS reload for subscription pages
        if (window.location.pathname.includes('subscription')) {
            const links = document.querySelectorAll('link[href*="owner-dashboard.css"]');
            links.forEach(link => {
                link.href = link.href.split('?')[0] + '?v=' + Date.now();
            });
        }

        // Debug submenu functionality
        console.log('Checking submenu functionality...');
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        console.log('Found submenu toggles:', submenuToggles.length);

        submenuToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Submenu toggle clicked');
                const parent = this.closest('.has-submenu');
                parent.classList.toggle('open');
                console.log('Submenu open state:', parent.classList.contains('open'));
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
