<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Shopledger') }} - Multi-Shop Management System</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="Shopledger - Multi-shop management system for inventory, sales, purchases, and staff management. Simplify your business operations.">
        <meta name="keywords" content="shop management, inventory, sales, purchases, POS, Rwanda, RWF, business management">
        <meta name="author" content="Shopledger">
        <meta name="robots" content="index, follow">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:title" content="{{ config('app.name', 'Shopledger') }} - Multi-Shop Management System">
        <meta property="og:description" content="Simplify your shop management with Shopledger. Track inventory, sales, purchases, and staff all in one place.">
        <meta property="og:image" content="{{ asset('images/logo.svg') }}">
        <meta property="og:site_name" content="Shopledger">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url('/') }}">
        <meta name="twitter:title" content="{{ config('app.name', 'Shopledger') }} - Multi-Shop Management System">
        <meta name="twitter:description" content="Simplify your shop management with Shopledger. Track inventory, sales, purchases, and staff all in one place.">
        <meta name="twitter:image" content="{{ asset('images/logo.svg') }}">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">

        <!-- Theme Color -->
        <meta name="theme-color" content="#6366f1">
        <meta name="msapplication-TileColor" content="#6366f1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"/>
                </svg>
            </div>

            <!-- Floating Decorative Elements -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-40 right-20 w-48 h-48 bg-pink-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/3 right-1/4 w-24 h-24 bg-yellow-400/20 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 left-1/4 w-36 h-36 bg-indigo-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>

            <!-- Navigation -->
            <nav class="relative z-10 px-6 py-4 lg:px-12">
                <div class="flex items-center justify-between max-w-7xl mx-auto">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <svg class="w-10 h-10 text-white" viewBox="0 0 50 50" fill="currentColor">
                                <circle cx="12" cy="10" r="4"/>
                                <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
                                <path d="M9 26 L6 38 L8 38 L12 28" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M13 26 L17 38 L15 38 L11 28" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M14 18 L22 20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                                <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22" stroke="currentColor" stroke-width="2" fill="none"/>
                                <circle cx="30" cy="36" r="3"/>
                                <circle cx="40" cy="36" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-extrabold text-white tracking-tight">Shopledger</h1>
                            <p class="text-indigo-200 text-xs">Multi-Shop Management</p>
                        </div>
                    </div>

                    <!-- Auth Links -->
                    @if (Route::has('login'))
                        <div class="flex items-center space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-white/20 backdrop-blur-sm text-white rounded-xl font-semibold hover:bg-white/30 transition">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-6 py-2.5 text-white font-semibold hover:text-indigo-200 transition">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-white text-indigo-900 rounded-xl font-semibold hover:bg-indigo-100 transition transform hover:scale-105 shadow-lg">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative z-10 px-6 lg:px-12 py-20 lg:py-32">
                <div class="max-w-7xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <!-- Left Content -->
                        <div class="text-white">
                            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm mb-6">
                                <span class="h-2 w-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                                Trusted by 100+ shops in Rwanda
                            </div>
                            
                            <h2 class="text-4xl lg:text-6xl font-extrabold leading-tight mb-6">
                                Manage Your Shops
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-pink-400">
                                    Like a Pro
                                </span>
                            </h2>
                            
                            <p class="text-xl text-indigo-200 mb-8 leading-relaxed">
                                The complete multi-shop management system. Track sales, inventory, profits, and staff - all in one powerful platform built for Rwandan businesses.
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 mb-12">
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 rounded-xl font-bold text-lg hover:from-yellow-300 hover:to-orange-400 transition transform hover:scale-105 shadow-xl">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Get Started Free
                                </a>
                                <a href="#features" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-xl font-bold text-lg hover:bg-white/20 transition">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    See How It Works
                                </a>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-6">
                                <div class="text-center">
                                    <p class="text-3xl font-extrabold text-yellow-400">100+</p>
                                    <p class="text-indigo-200 text-sm">Active Shops</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-extrabold text-yellow-400">50K+</p>
                                    <p class="text-indigo-200 text-sm">Sales Tracked</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-extrabold text-yellow-400">24/7</p>
                                    <p class="text-indigo-200 text-sm">Support</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Content - Feature Cards -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition border border-white/20">
                                <div class="h-12 w-12 rounded-xl bg-green-500 flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold text-lg mb-2">Real-time Analytics</h3>
                                <p class="text-indigo-200 text-sm">Track sales and profits as they happen</p>
                            </div>

                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition border border-white/20 mt-8">
                                <div class="h-12 w-12 rounded-xl bg-blue-500 flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold text-lg mb-2">Multi-User Roles</h3>
                                <p class="text-indigo-200 text-sm">Admin, Sellers, Accountants</p>
                            </div>

                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition border border-white/20">
                                <div class="h-12 w-12 rounded-xl bg-yellow-500 flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold text-lg mb-2">Inventory Control</h3>
                                <p class="text-indigo-200 text-sm">Auto stock updates & alerts</p>
                            </div>

                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition border border-white/20 mt-8">
                                <div class="h-12 w-12 rounded-xl bg-pink-500 flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold text-lg mb-2">RWF Currency</h3>
                                <p class="text-indigo-200 text-sm">Built for Rwandan businesses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div id="features" class="relative z-10 px-6 lg:px-12 py-20 bg-white/5 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h3 class="text-3xl lg:text-4xl font-extrabold text-white mb-4">Everything You Need</h3>
                        <p class="text-indigo-200 text-lg max-w-2xl mx-auto">Powerful features designed specifically for multi-shop management in Rwanda</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-white/30 transition">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">Sales Management</h4>
                            <p class="text-indigo-200">Track every sale with profit calculation. Print beautiful receipts instantly.</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-white/30 transition">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">Inventory Tracking</h4>
                            <p class="text-indigo-200">Auto stock updates on purchases & sales. Low stock alerts.</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-white/30 transition">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-purple-400 to-pink-600 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">Staff Management</h4>
                            <p class="text-indigo-200">Assign roles: Sellers, Accountants. Track performance.</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-white/30 transition">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-600 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">Profit Tracking</h4>
                            <p class="text-indigo-200">See profit margins on every sale. Daily/weekly/monthly reports.</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-white/30 transition">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-teal-600 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">Multi-Shop Support</h4>
                            <p class="text-indigo-200">Manage multiple shops from one dashboard. System admin oversight.</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10 hover:border-white/30 transition">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-red-400 to-rose-600 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">Beautiful Reports</h4>
                            <p class="text-indigo-200">Charts, graphs, and printable reports. Export to PDF.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="relative z-10 px-6 lg:px-12 py-20">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-sm rounded-3xl p-12 border border-white/20">
                        <h3 class="text-3xl lg:text-4xl font-extrabold text-white mb-4">Ready to Transform Your Business?</h3>
                        <p class="text-xl text-indigo-200 mb-8">Join hundreds of shop owners who trust Shopledger to manage their business.</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 rounded-xl font-bold text-lg hover:from-yellow-300 hover:to-orange-400 transition transform hover:scale-105 shadow-xl">
                                Start Free Today
                            </a>
                            <a href="tel:0786163963" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 text-white rounded-xl font-bold text-lg hover:bg-white/20 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Call: 0786 163 963
                            </a>
                        </div>

                        <div class="flex items-center justify-center space-x-6 text-sm text-indigo-200">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Free Setup
                            </span>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Training Included
                            </span>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                24/7 Support
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Motivational Footer -->
        <x-footer variant="dark" />

        <!-- Floating WhatsApp Button -->
        <a href="https://wa.me/250786163963?text=Hello%2C%20I%20need%20help%20with%20Shopledger" target="_blank" class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 group">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span class="absolute right-full mr-3 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-sm px-3 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                Chat with us!
            </span>
        </a>
    </body>
</html>
