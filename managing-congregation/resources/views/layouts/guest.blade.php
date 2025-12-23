<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Managing the Congregation') }}</title>

        <!-- Dynamic Favicon -->
        @php
            $faviconPath = \App\Models\SystemSetting::get('footer_logo_path');
        @endphp
        @if($faviconPath && file_exists(storage_path('app/public/' . $faviconPath)))
            <link rel="icon" type="image/webp" href="{{ asset('storage/' . $faviconPath) }}">
        @else
            <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
        @endif

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
                <div class="flex flex-col items-center mb-6">
                    <a href="/" class="flex items-center gap-3">
                        @php
                            $guestLogoPath = \App\Models\SystemSetting::get('footer_logo_path');
                        @endphp
                        
                        @if($guestLogoPath && file_exists(storage_path('app/public/' . $guestLogoPath)))
                            <img src="{{ asset('storage/' . $guestLogoPath) }}" 
                                 alt="Logo" 
                                 class="w-16 h-16 rounded-full object-cover ring-1 ring-stone-200/50">
                        @else
                            <img src="{{ asset('images/logo.webp') }}" 
                                 alt="Logo" 
                                 class="w-16 h-16 rounded-full object-cover ring-1 ring-stone-200/50">
                        @endif
                        
                        <span class="text-2xl font-heading font-bold text-slate-800">
                            {{ config('app.name') }}
                        </span>
                    </a>
                </div>
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
