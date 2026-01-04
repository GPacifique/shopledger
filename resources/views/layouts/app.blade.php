<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shopledger') }}</title>

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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <!-- Motivational Footer -->
            <x-footer variant="light" />
        </div>
    </body>
</html>
