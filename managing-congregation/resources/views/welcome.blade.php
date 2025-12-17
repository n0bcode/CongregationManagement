<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} - Congregation Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-50 font-sans antialiased text-slate-800">
        <!-- Navigation -->
        <nav class="bg-white border-b border-stone-200 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.webp') }}" 
                             alt="Logo" 
                             class="w-10 h-10 object-contain">
                        <h1 class="text-xl font-heading font-bold text-slate-800 hidden sm:block">{{ config('app.name') }}</h1>
                    </div>
                    
                    @if (Route::has('login'))
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-slate-700 font-medium hover:text-amber-600 transition-colors px-4 py-2">
                                    Log in
                                </a>
                                
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-primary shadow-sm hover:shadow-md">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative bg-stone-50 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-40">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-100 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
                <div class="absolute top-0 -right-4 w-96 h-96 bg-stone-200 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-96 h-96 bg-amber-50 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 sm:py-40">
                <div class="text-center max-w-4xl mx-auto">
                    <h2 class="text-5xl sm:text-7xl font-heading font-bold text-slate-900 mb-8 leading-tight tracking-tight animate-fade-in-up">
                        Congregation Management
                        <span class="text-amber-600 relative inline-block">
                            System
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-amber-200 -z-10" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="8" fill="none" />
                            </svg>
                        </span>
                    </h2>
                    <p class="text-xl sm:text-2xl text-slate-600 mb-12 max-w-2xl mx-auto leading-relaxed animate-fade-in-up animation-delay-200">
                        Comprehensive, professional, and secure solution for managing congregation members, finances, and activities.
                    </p>
                    
                    @guest
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fade-in-up animation-delay-400">
                            <a href="{{ route('register') }}" class="btn btn-primary text-lg px-8 py-4 h-auto shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                Get Started
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-secondary text-lg px-8 py-4 h-auto shadow-sm hover:shadow-md transition-all duration-300">
                                Log in
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-24 bg-white relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 pt-2">
                    <h3 class="text-3xl sm:text-4xl font-heading font-bold text-slate-800 mb-4">
                        Key Features
                    </h3>
                    <p class="text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                        Designed specifically to meet all congregation management needs
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1 -->
                    <div class="card group hover:border-amber-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-100 transition-colors shrink-0">
                            <svg class="w-7 h-7 text-amber-600" width="28" height="28" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-heading font-semibold text-slate-800 mb-3">Member Management</h4>
                        <p class="text-slate-600 leading-relaxed">Track detailed information, sacramental history, and activity progress of each member.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="card group hover:border-amber-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-100 transition-colors shrink-0">
                            <svg class="w-7 h-7 text-amber-600" width="28" height="28" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-heading font-semibold text-slate-800 mb-3">Financial Management</h4>
                        <p class="text-slate-600 leading-relaxed">Transparent income and expense tracking, automated financial reporting, and effective budget management.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="card group hover:border-amber-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-100 transition-colors shrink-0">
                            <svg class="w-7 h-7 text-amber-600" width="28" height="28" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-heading font-semibold text-slate-800 mb-3">Training & Education</h4>
                        <p class="text-slate-600 leading-relaxed">Organize catechism classes, manage learning progress, and issue certificates to students.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="card group hover:border-amber-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-100 transition-colors shrink-0">
                            <svg class="w-7 h-7 text-amber-600" width="28" height="28" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-heading font-semibold text-slate-800 mb-3">Document Storage</h4>
                        <p class="text-slate-600 leading-relaxed">Secure digital archive for all documents, encyclicals, and important congregation records.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="py-24 bg-stone-50 mt-5 py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h3 class="text-3xl sm:text-4xl font-heading font-bold text-slate-800 mb-8">
                            Why Choose Our System?
                        </h3>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xl font-heading font-semibold text-slate-800 mb-2">Simple & Easy to Use</h4>
                                    <p class="text-slate-600 leading-relaxed">Intuitive, user-friendly interface suitable for all ages and technical skill levels.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xl font-heading font-semibold text-slate-800 mb-2">Safe & Secure</h4>
                                    <p class="text-slate-600 leading-relaxed">Data is encrypted and strictly protected. Detailed permission system ensures privacy.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xl font-heading font-semibold text-slate-800 mb-2">Smart Reporting</h4>
                                    <p class="text-slate-600 leading-relaxed">Visual reporting system helps the board grasp the situation and make accurate decisions.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 bg-amber-200 rounded-3xl transform rotate-3 opacity-30"></div>
                        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 shadow-2xl relative">
                            <!-- Mock Dashboard UI -->
                            <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                                <div class="border-b border-stone-200 p-4 flex items-center justify-between bg-stone-50">
                                    <div class="flex gap-2">
                                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                    </div>
                                    <div class="h-2 w-20 bg-stone-200 rounded-full"></div>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div class="flex gap-4">
                                        <div class="w-1/3 h-24 bg-amber-50 rounded-lg border border-amber-100 p-4">
                                            <div class="h-2 w-12 bg-amber-200 rounded mb-2"></div>
                                            <div class="h-8 w-16 bg-amber-600 rounded opacity-20"></div>
                                        </div>
                                        <div class="w-1/3 h-24 bg-emerald-50 rounded-lg border border-emerald-100 p-4">
                                            <div class="h-2 w-12 bg-emerald-200 rounded mb-2"></div>
                                            <div class="h-8 w-16 bg-emerald-600 rounded opacity-20"></div>
                                        </div>
                                        <div class="w-1/3 h-24 bg-blue-50 rounded-lg border border-blue-100 p-4">
                                            <div class="h-2 w-12 bg-blue-200 rounded mb-2"></div>
                                            <div class="h-8 w-16 bg-blue-600 rounded opacity-20"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="h-4 bg-stone-100 rounded w-3/4"></div>
                                        <div class="h-4 bg-stone-100 rounded w-full"></div>
                                        <div class="h-4 bg-stone-100 rounded w-5/6"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        @guest
            <div class="py-24 bg-slate-900 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                    </svg>
                </div>
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <h3 class="text-3xl sm:text-4xl font-heading font-bold text-white mb-6 leading-tight">
                        Ready to Improve Management Efficiency?
                    </h3>
                    <p class="text-xl text-slate-300 mb-10 leading-relaxed max-w-2xl mx-auto">
                        Join hundreds of other congregations using our system to better manage their community.
                    </p>
                    <a href="{{ route('register') }}" class="btn bg-amber-600 text-white hover:bg-amber-500 text-lg px-8 py-4 h-auto shadow-lg hover:shadow-amber-500/20 border-none">
                        Register for Free
                        <svg class="ml-2 w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endguest

        <!-- Footer -->
        <footer class="bg-white border-t border-stone-200 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.webp') }}" 
                             alt="Logo" 
                             class="w-8 h-8 object-contain">
                        <span class="text-lg font-heading font-bold text-slate-800">{{ config('app.name') }}</span>
                    </div>
                    <div class="text-slate-500 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
