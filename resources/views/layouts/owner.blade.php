<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/owner-dashboard.css') }}">
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
