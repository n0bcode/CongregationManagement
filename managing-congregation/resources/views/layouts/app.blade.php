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

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-stone-50">
        <!-- Skip to main content link for keyboard navigation -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-6 focus:py-3 focus:bg-amber-600 focus:text-white focus:rounded-lg focus:shadow-lg">
            Skip to main content
        </a>

        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-stone-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            <!-- Flash Messages -->
            <x-ui.flash-message />

            <!-- Page Content -->
            <main id="main-content" class="py-6 flex-1" role="main">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-stone-200 mt-12">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Brand & Contact -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                @php
                                    $footerLogoPath = \App\Models\SystemSetting::get('footer_logo_path');
                                @endphp
                                
                                @if($footerLogoPath && file_exists(storage_path('app/public/' . $footerLogoPath)))
                                    <img src="{{ asset('storage/' . $footerLogoPath) }}" alt="{{ config('app.name') }}" class="block h-7 w-auto object-contain">
                                @else
                                    <x-application-logo class="block h-7 w-auto fill-current text-amber-600" />
                                @endif
                                
                                <span class="text-xl font-serif font-bold text-stone-800">{{ config('app.name') }}</span>
                            </div>
                            <p class="text-stone-500 text-sm leading-relaxed">
                                {!! \App\Models\SystemSetting::get('footer_description', 'Supporting our community with grace and efficiency. Managing member records, events, and reports for the congregation.') !!}
                            </p>
                            <div class="pt-2 flex flex-col gap-1 text-sm text-stone-500">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ \App\Models\SystemSetting::get('footer_address', '123 Congregation Ave, City, Country') }}
                                </span>
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ \App\Models\SystemSetting::get('footer_email', 'contact@congregation.org') }}
                                </span>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div>
                            <h3 class="text-sm font-semibold text-stone-900 tracking-wider uppercase mb-4">{{ __('Quick Links') }}</h3>
                            <ul class="space-y-3">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('members.index') }}" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Member Directory') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('communities.index') }}" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Communities') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.demographic') }}" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Reports') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Legal & Support -->
                        <div>
                            <h3 class="text-sm font-semibold text-stone-900 tracking-wider uppercase mb-4">{{ __('Resources') }}</h3>
                            <ul class="space-y-3">
                                <li>
                                    <a href="#" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Help Center') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Privacy Policy') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-stone-500 hover:text-amber-600 transition-colors">
                                        {{ __('Terms of Service') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-12 border-t border-stone-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-stone-400 text-sm text-center md:text-left">
                            {!! \App\Models\SystemSetting::get('footer_copyright', '&copy; ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.') !!}
                        </p>
                        <p class="text-stone-400 text-xs">
                            v{{ config('app.version', '1.0.0') }}
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        @livewireScripts
        @stack('scripts')
        <x-layout.mobile-navigation />
        <x-features.command-palette />
    </body>
</html>
