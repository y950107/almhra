<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
   
</head>
<body class="flex items-center justify-center min-h-screen gradient-bg bg-cover bg-center" style="background-image: url('{{ asset('assets/images/background/1.jpg') }}');">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
       
            <div class="text-center mb-8">
                <a href="/">
                    <img class="w-40 h-40 mx-auto" src="{{ asset('assets/images/logorapport/mahara.png') }}" alt="شعار المنشأة">
                </a>
            </div>

            <main class="w-full">
                @yield('content')
            </main>
        
    </body>
</html>
