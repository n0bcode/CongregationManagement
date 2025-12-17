<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Managing the Congregation') }}</title>

        <!-- Preload fonts for performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-stone-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center">
                    <div class="mb-3">
                        <img src="{{ asset('images/logo.webp') }}" 
                             alt="AFE Salesian Province Logo" 
                             class="w-20 h-20 object-contain">
                    </div>
                    <span class="font-heading text-2xl text-slate-800">Managing the Congregation</span>
                    <span class="text-sm text-slate-600 mt-1">Pastoral Administration System</span>
                </a>
            </div>

            <!-- Content Card -->
            <div class="w-full sm:max-w-md">
                <div class="card">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-600">
                    &copy; {{ date('Y') }} {{ config('app.name') }}
                </p>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
