<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shopledger') }} | @yield('title', 'Welcome')</title>
        <meta name="application-name" content="Shopledger">
        <meta name="apple-mobile-web-app-title" content="Shopledger">
<!-- SEO Meta Tags -->
        <meta name="description" content="Shopledger is a modern multi-shop management system for inventory, sales, purchases, expenses, staff, and analytics for growing businesses.">
        <meta name="keywords" content="shop management, inventory system, sales tracking, purchase management, POS, Rwanda, stock alerts, expense tracking, business analytics">
        <meta name="author" content="Shopledger">
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ config('app.name', 'Shopledger') }} - Multi-Shop Management System">
        <meta property="og:description" content="Manage inventory, sales, purchases, staff, and analytics from one smart dashboard with Shopledger.">
        <meta property="og:image" content="{{ asset('images/og-logo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="Shopledger">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ config('app.name', 'Shopledger') }} - Multi-Shop Management System">
        <meta name="twitter:description" content="Manage inventory, sales, purchases, staff, and analytics from one smart dashboard with Shopledger.">
        <meta name="twitter:image" content="{{ asset('images/og-logo.png') }}">

        <!-- Favicon / Logo -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/og-logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/og-logo.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/og-logo.png') }}">

        <!-- Theme Color -->
        <meta name="theme-color" content="#6366f1">
        <meta name="msapplication-TileColor" content="#6366f1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
        </script>
        <!-- SEO Meta Tags -->
        <meta name="description" content="MahWi - Multi-shop management system for inventory, sales, purchases, and staff management. Simplify your business operations.">
        <meta name="keywords" content="shop management, inventory, sales, purchases, POS, Rwanda, RWF, business management">
        <meta name="author" content="MahWi Team">
        <meta name="robots" content="index, follow">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ config('app.name', 'MahWi') }} - Multi-Shop Management System">
        <meta property="og:description" content="Simplify your shop management with MahWi. Track inventory, sales, purchases, and staff all in one place.">
        <meta property="og:image" content="{{ asset('images/og-image.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="MahWi">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ config('app.name', 'MahWi') }} - Multi-Shop Management System">
        <meta name="twitter:description" content="Simplify your shop management with MahWi. Track inventory, sales, purchases, and staff all in one place.">
        <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">

        <!-- Theme Color -->
        <meta name="theme-color" content="#6366f1">
        <meta name="msapplication-TileColor" content="#6366f1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Left Side - Promotional Flyer -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 relative overflow-hidden">
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

                <!-- Floating Elements -->
                <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
                <div class="absolute bottom-32 right-20 w-32 h-32 bg-pink-400/20 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-yellow-400/20 rounded-full blur-xl animate-pulse" style="animation-delay: 2s;"></div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                    <!-- Logo & Tagline -->
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
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
                                <h1 class="text-3xl font-extrabold tracking-tight">MahWi</h1>
                                <p class="text-indigo-200 text-sm">Multi-Shop Management System</p>
                            </div>
                        </div>

                        <!-- Motivational Quote -->
                        @php
                            $quotes = [
                                "Success is not final, failure is not fatal: it is the courage to continue that counts.",
                                "The only way to do great work is to love what you do.",
                                "Dream big. Start small. Act now.",
                                "Every expert was once a beginner.",
                                "Your limitation—it's only your imagination.",
                                "Great things never come from comfort zones.",
                                "Success doesn't just find you. You have to go out and get it.",
                                "The harder you work, the greater you'll feel when you achieve it.",
                                "Wake up with determination. Go to bed with satisfaction.",
                                "Don't wait for opportunity. Create it.",
                            ];
                            $randomQuote = $quotes[array_rand($quotes)];
                        @endphp
                        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-4 border-l-4 border-yellow-400">
                            <p class="text-white/90 italic text-sm">"{{ $randomQuote }}"</p>
                            <p class="text-indigo-300 text-xs mt-2">— Daily motivation for entrepreneurs 💪</p>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold mb-6">Why Choose MahWi?</h2>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transform hover:scale-105 transition">
                                <div class="bg-green-400 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Real-time Analytics</h3>
                                    <p class="text-indigo-200 text-sm">Track sales, profits, and inventory in real-time</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transform hover:scale-105 transition">
                                <div class="bg-blue-400 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Multi-User Roles</h3>
                                    <p class="text-indigo-200 text-sm">Admin, Sellers, Accountants - All managed easily</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transform hover:scale-105 transition">
                                <div class="bg-yellow-400 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Inventory Management</h3>
                                    <p class="text-indigo-200 text-sm">Auto stock updates, low stock alerts & more</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 transform hover:scale-105 transition">
                                <div class="bg-pink-400 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Profit Tracking</h3>
                                    <p class="text-indigo-200 text-sm">See profit margins on every sale instantly</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & CTA -->
                    <div class="space-y-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                            <p class="text-indigo-200 text-sm uppercase tracking-wider mb-2">Need Help? Contact Admin</p>
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-500 p-3 rounded-full animate-pulse">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold">0786 163 963</p>
                                    <p class="text-indigo-200 text-sm">Available 24/7</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 text-sm text-indigo-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Free Setup
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Training Included
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                RWF Currency
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 bg-gray-50">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-8 text-center">
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <div class="bg-indigo-600 p-2 rounded-xl">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 50 50" fill="currentColor">
                                <circle cx="12" cy="10" r="4"/>
                                <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
                                <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22" stroke="currentColor" stroke-width="2" fill="none"/>
                                <circle cx="30" cy="36" r="3"/>
                                <circle cx="40" cy="36" r="3"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800">MahWi</h1>
                    </div>
                    <p class="text-gray-500 text-sm">Multi-Shop Management System</p>
                </div>

                <div class="w-full max-w-md">
                    <div class="bg-white shadow-xl rounded-2xl p-8">
                        {{ $slot }}
                    </div>

                    <!-- Mobile Contact -->
                    <div class="lg:hidden mt-6 text-center">
                        <p class="text-gray-500 text-sm">Need help? Call</p>
                        <a href="tel:0786163963" class="text-indigo-600 font-bold text-lg">0786 163 963</a>
                    </div>
                </div>
            </div>
        </div>

       
        <x-whatsapp-float />
    </body>
</html>
