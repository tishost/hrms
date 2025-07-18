<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} | @yield('title', 'Welcome')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- For Laravel + Vite --}}
    <!-- Select2 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    <nav class="bg-white shadow p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">{{ config('app.name') }}</h1>
            <a href="{{ route('owner.register.form') }}" class="text-blue-600 hover:underline">Register as Owner</a>
        </div>
    </nav>

    <main class="py-10">
        @yield('content')
    </main>

    <footer class="text-center text-sm text-gray-500 py-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect("#country-select", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "Select your country"
        });
    });
</script>
</body>
</html>