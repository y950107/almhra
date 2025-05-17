<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap">
        </noscript>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      *,html,body{
                font-family: "Noto Kufi Arabic", sans-serif !important;
                font-optical-sizing: auto;
                font-style: normal;
        }
    .quran-level-label {
        display: block;
        font-size: 0.875rem; /* text-sm */
        font-weight: 500; /* medium */
        color: #374151; /* text-gray-700 */
        margin: 0.5rem auto;
    }

    .quran-level-select {
        width: 100%;
        padding: 0.5rem 1.75rem; /* py-2 px-3 */
        font-size: 0.875rem; /* text-sm */
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        background-color: white;
        color: #374151; /* text-gray-700 */
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); /* shadow-sm */
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .quran-level-select:focus {
        outline: none;
        border-color: #3b82f6; /* focus:border-blue-500 */
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); /* focus:ring-2 focus:ring-blue-500 */
    }

    .quran-level-select option {
        padding: 0.25rem;
    }

    .quran-level-select option:hover {
        background-color: #eff6ff; /* hover:bg-blue-50 */
    }
        .file-upload-section {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background-color: #f9fafb;
        direction: rtl;
    }

    .file-upload-legend {
        font-size: 1.125rem;
        font-weight: 600;
        color: #7c3aed;
        padding: 0 0.5rem;
        background-color: white;
    }

    .file-upload-group {
        margin-bottom: 1.25rem;
    }

    .file-upload-label {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-weight: 500;
        color: #374151;
    }

    .file-upload-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .file-upload-input {
        width: 100%;
        padding: 0.75rem;
        padding-left: 6rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: white;
        color: #6b7280;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-upload-input:hover {
        border-color: #9ca3af;
    }

    .file-upload-input:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .file-upload-button {
          position: absolute;
            right: 0.253rem;
            padding: 0.5rem 1.5rem;
            background-color: #7c3aed;
            color: white;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            pointer-events: none;
    }

    /* For audio input specifically */
    #audio_recitation::file-selector-button {
        display: none;
    }
</style>
</head>
<body class="flex items-center justify-center min-h-screen gradient-bg bg-cover bg-center" style="background-image: url('{{ asset('assets/images/background/1.jpg') }}');">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 w-full pb-6">

            <div class="text-center">
                <a href="/">
                    <img class="w-40 h-40 mx-auto" src="{{ asset('assets/logo.png') }}" alt="شعار المنشأة">
                </a>
            </div>

            <main class="w-full">
                @yield('content')
            </main>

    </div>
    <!-- تذييل الصفحة (Footer) -->
    <footer class="w-full bg-gray-800 text-white py-2 mt-16 fixed bottom-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center"> مقرأة المهرة
                بجامع والدة الأمير بندر بن عبد العزيز - بحي الندى . جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
        </div>
    </footer>
    </body>
</html>
