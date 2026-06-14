<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MahWi') }} - Multi-Shop Management System</title>

    <!-- SEO -->
    <meta name="description" content="MahWi is a cloud-based multi-shop management system for Rwanda. Manage inventory, sales, staff, and reports in real time.">
    <meta name="theme-color" content="#6366f1">

    <!-- Fonts & Vite -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans text-white">

<div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 relative overflow-hidden">

    <!-- BACKGROUND GRID -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full">
            <defs>
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>
    </div>

    <!-- FLOATING GLOW -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-40 right-20 w-48 h-48 bg-pink-400/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute top-1/3 right-1/4 w-24 h-24 bg-yellow-400/20 rounded-full blur-2xl animate-pulse"></div>

    <!-- NAV -->
    <nav class="relative z-10 px-6 py-5">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            <div class="flex items-center space-x-3">
                <div class="bg-white/20 p-3 rounded-xl backdrop-blur">
                    <span class="font-bold">M</span>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold">MahWi</h1>
                    <p class="text-xs text-indigo-200">Multi-Shop System</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-white/20 rounded-xl">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 bg-white text-indigo-900 rounded-xl font-bold">
                        Get Started
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    <!-- HERO -->
    <div class="relative z-10 px-6 lg:px-12 py-24">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">

            <!-- LEFT -->
            <div>
                <div class="inline-flex items-center px-4 py-2 bg-white/10 rounded-full text-sm mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    Trusted by shops in Rwanda
                </div>

                <h2 class="text-4xl lg:text-6xl font-extrabold leading-tight">
                    Manage Your Shops
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-pink-400">
                        Like a Pro
                    </span>
                </h2>

                <p class="text-indigo-200 mt-6 text-lg">
                    A powerful multi-shop system for inventory, sales, staff, and real-time reports.
                </p>

                <div class="mt-8 flex gap-4">
                    <a href="{{ route('register') }}"
                       class="px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-black font-bold rounded-xl">
                        Get Started Free
                    </a>

                    <a href="#features"
                       class="px-8 py-4 bg-white/10 rounded-xl">
                        Explore Features
                    </a>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="grid grid-cols-2 gap-4">

                <div class="bg-white/10 backdrop-blur p-6 rounded-2xl border border-white/20">
                    <h3 class="font-bold">Sales</h3>
                    <p class="text-sm text-indigo-200">Track revenue</p>
                </div>

                <div class="bg-white/10 backdrop-blur p-6 rounded-2xl border border-white/20">
                    <h3 class="font-bold">Inventory</h3>
                    <p class="text-sm text-indigo-200">Live stock</p>
                </div>

                <div class="bg-white/10 backdrop-blur p-6 rounded-2xl border border-white/20">
                    <h3 class="font-bold">Staff</h3>
                    <p class="text-sm text-indigo-200">Roles & access</p>
                </div>

                <div class="bg-white/10 backdrop-blur p-6 rounded-2xl border border-white/20">
                    <h3 class="font-bold">Reports</h3>
                    <p class="text-sm text-indigo-200">Analytics</p>
                </div>

            </div>

        </div>
    </div>

    <!-- FEATURES -->
    <div id="features" class="relative z-10 px-6 lg:px-12 py-20 bg-white/5 backdrop-blur">
        <div class="max-w-7xl mx-auto text-center mb-12">
            <h3 class="text-3xl font-extrabold">Everything You Need</h3>
            <p class="text-indigo-200 mt-2">Built for modern businesses</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-7xl mx-auto">

            @foreach(['Sales Management','Inventory Control','Multi-Shop System','Staff Roles','Reports','Profit Tracking'] as $f)
                <div class="bg-white/10 p-6 rounded-2xl border border-white/10 hover:scale-105 transition">
                    <h4 class="font-bold">{{ $f }}</h4>
                    <p class="text-sm text-indigo-200 mt-2">Powerful feature for your business</p>
                </div>
            @endforeach

        </div>
    </div>

    <!-- CTA -->
    <div class="relative z-10 py-24 text-center">
        <h2 class="text-4xl font-extrabold">Ready to grow your business?</h2>
        <p class="text-indigo-200 mt-4">Join hundreds of shop owners</p>

        <a href="{{ route('register') }}"
           class="mt-8 inline-block px-10 py-4 bg-white text-indigo-900 font-bold rounded-xl">
            Start Free Today
        </a>
    </div>

</div>

<!-- FOOTER -->
<x-footer variant="dark" />

</body>
</html>