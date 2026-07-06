<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <title>{{ config('app.name', 'MahwiPOS') }} | @yield('title', 'Saas shop management system made for shops around the globe')</title>
        <meta name="application-name" content="Shopledger">
        <meta name="apple-mobile-web-app-title" content="Shopledger">

        <!-- SEO Meta Tags -->
        <meta name="description" content="Shopledger is a modern multi-shop management system for inventory, sales, purchases, expenses, staff, and analytics for growing businesses in Rwanda and beyond.">
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
        <meta property="og:site_name" content="Multi-Shop Management System">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ config('app.name', 'Shopledger') }} - Multi-Shop Management System">
        <meta name="twitter:description" content="Manage inventory, sales, purchases, staff, and analytics from one smart dashboard with Shopledger.">
        <meta name="twitter:image" content="{{ asset('images/og-logo.png') }}">

        <!-- Favicon / Logo -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}">

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
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>
<x-whatsapp-float />
            <!-- Motivational Footer -->
            <x-footer variant="light" />
        </div>
    </body>
</html>
