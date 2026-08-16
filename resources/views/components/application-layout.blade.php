<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'MahwiPOS') }}
        | @yield('title', 'All-in-one business management system for shops')
    </title>

    <meta name="application-name" content="MahwiPOS">
    <meta name="apple-mobile-web-app-title" content="MahwiPOS">

    {{-- SEO --}}
    <meta name="description"
          content="MahwiPOS is a modern multi-shop management system for inventory, sales, purchases, orders, expenses, staff, and analytics for growing businesses in Rwanda and beyond.">

    <meta name="keywords"
          content="MahwiPOS, shop management, inventory system, POS, sales, purchases, orders, Rwanda, stock management, business analytics">

    <meta name="author" content="MahwiPOS Team">
    <meta name="robots" content="index, follow">

    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title"
          content="{{ config('app.name', 'MahwiPOS') }} - Multi-Shop Management System">

    <meta property="og:description"
          content="Manage inventory, sales, purchases, orders, expenses, staff, and analytics with MahwiPOS.">

    <meta property="og:image" content="{{ asset('images/og-logo.png') }}">
    <meta property="og:site_name" content="MahwiPOS">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
          content="{{ config('app.name', 'MahwiPOS') }} - Multi-Shop Management System">

    <meta name="twitter:description"
          content="Manage inventory, sales, purchases, orders, expenses, staff, and analytics with MahwiPOS.">

    <meta name="twitter:image" content="{{ asset('images/og-logo.png') }}">

    {{-- Favicon --}}
    <link rel="icon"
          type="image/png"
          href="{{ asset('images/MAHWILOGO.png') }}">

    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ asset('images/MAHWILOGO.png') }}">

    {{-- Theme --}}
    <meta name="theme-color" content="#6366f1">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap"
        rel="stylesheet"
    >

    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    {{-- Vite --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Alpine.js --}}
    <script
        defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>

    {{-- Extra page styles --}}
    @stack('styles')

    <style>
        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
        }

        /*
         * Responsive table wrapper.
         * Prevents tables from breaking the entire mobile page.
         */
        .responsive-table {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .responsive-table table {
            min-width: 640px;
        }

        /*
         * Prevent long values such as order numbers,
         * emails and product names from breaking layouts.
         */
        .break-anywhere {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /*
         * Better touch behavior on phones/tablets.
         */
        button,
        a,
        input,
        select,
        textarea {
            touch-action: manipulation;
        }
    </style>
</head>


<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen min-h-[100dvh] flex flex-col">

        {{-- Navigation --}}
        @include('layouts.navigation')


        {{-- Page Heading --}}
        @isset($header)

            <header class="bg-white shadow-sm border-b border-gray-200">

                <div class="
                    w-full
                    max-w-7xl
                    mx-auto
                    px-3
                    sm:px-5
                    md:px-6
                    lg:px-8
                    py-4
                    sm:py-5
                    lg:py-6
                ">

                    {{ $header }}

                </div>

            </header>

        @endisset


        {{-- Main Application Content --}}
        <main class="flex-grow w-full">

            <div class="
                w-full
                py-4
                sm:py-5
                md:py-6
                lg:py-8
            ">

                <div class="
                    w-full
                    max-w-7xl
                    mx-auto
                    px-3
                    sm:px-5
                    md:px-6
                    lg:px-8
                ">

                    {{-- Success Message --}}
                    @if(session('success'))

                        <div
                            class="
                                mb-4
                                sm:mb-6
                                rounded-lg
                                border
                                border-green-200
                                bg-green-50
                                px-3
                                sm:px-4
                                py-3
                                text-sm
                                text-green-800
                            "
                            role="alert"
                        >

                            <div class="flex items-start gap-2 sm:gap-3">

                                <i class="
                                    fa-solid
                                    fa-circle-check
                                    mt-0.5
                                    shrink-0
                                "></i>

                                <span class="break-anywhere">
                                    {{ session('success') }}
                                </span>

                            </div>

                        </div>

                    @endif


                    {{-- Error Message --}}
                    @if(session('error'))

                        <div
                            class="
                                mb-4
                                sm:mb-6
                                rounded-lg
                                border
                                border-red-200
                                bg-red-50
                                px-3
                                sm:px-4
                                py-3
                                text-sm
                                text-red-800
                            "
                            role="alert"
                        >

                            <div class="flex items-start gap-2 sm:gap-3">

                                <i class="
                                    fa-solid
                                    fa-circle-exclamation
                                    mt-0.5
                                    shrink-0
                                "></i>

                                <span class="break-anywhere">
                                    {{ session('error') }}
                                </span>

                            </div>

                        </div>

                    @endif


                    {{-- Warning Message --}}
                    @if(session('warning'))

                        <div
                            class="
                                mb-4
                                sm:mb-6
                                rounded-lg
                                border
                                border-yellow-200
                                bg-yellow-50
                                px-3
                                sm:px-4
                                py-3
                                text-sm
                                text-yellow-800
                            "
                            role="alert"
                        >

                            <div class="flex items-start gap-2 sm:gap-3">

                                <i class="
                                    fa-solid
                                    fa-triangle-exclamation
                                    mt-0.5
                                    shrink-0
                                "></i>

                                <span class="break-anywhere">
                                    {{ session('warning') }}
                                </span>

                            </div>

                        </div>

                    @endif


                    {{-- Information Message --}}
                    @if(session('info'))

                        <div
                            class="
                                mb-4
                                sm:mb-6
                                rounded-lg
                                border
                                border-blue-200
                                bg-blue-50
                                px-3
                                sm:px-4
                                py-3
                                text-sm
                                text-blue-800
                            "
                            role="alert"
                        >

                            <div class="flex items-start gap-2 sm:gap-3">

                                <i class="
                                    fa-solid
                                    fa-circle-info
                                    mt-0.5
                                    shrink-0
                                "></i>

                                <span class="break-anywhere">
                                    {{ session('info') }}
                                </span>

                            </div>

                        </div>

                    @endif


                    {{-- =====================================================
                         ACTUAL PAGE CONTENT
                         ===================================================== --}}
                    <div class="w-full min-w-0">

                        {{ $slot }}

                    </div>

                </div>

            </div>

        </main>


        {{-- WhatsApp Floating Button --}}
        <x-whatsapp-float />


        {{-- Footer --}}
        <div class="w-full">
            <x-footer variant="light" />
        </div>

    </div>


    {{-- Page-specific scripts --}}
    @stack('scripts')

</body>

</html>