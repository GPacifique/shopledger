<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mahwi POS') | Mahwi POS</title>

    <meta name="description"
          content="@yield('meta_description', 'Mahwi POS helps businesses manage sales, inventory, purchases, expenses, and financial reports.')">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
        }
    </style>

    @stack('styles')
</head>

<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">

    {{-- Public Navigation --}}
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">

            {{-- Logo --}}
            <a href="{{ url('/') }}"
               class="flex shrink-0 items-center gap-3"
               aria-label="Mahwi POS home">
                <x-application-logo
                    name="MAHWI POS"
                    name-class="text-lg font-extrabold tracking-tight text-slate-900"
                    class="h-10 max-w-[150px]"
                />
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden items-center gap-6 md:flex">
                <a href="{{ url('/') }}"
                   class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                    Home
                </a>

                <a href="{{ route('buyer.dashboard') }}"
                   class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                    Marketplace
                </a>

                <a href="{{ route('terms') }}"
                   class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                    Terms of Service
                </a>

                <a href="{{ route('privacy') }}"
                   class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                    Privacy Policy
                </a>
            </div>

            {{-- Account Actions --}}
            <div class="flex shrink-0 items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        Dashboard
                    </a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:inline-flex">
                            Log in
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Get Started
                        </a>
                    @endif
                @endauth
            </div>
        </nav>

        {{-- Mobile Navigation --}}
        <div class="flex flex-wrap justify-center gap-x-5 gap-y-2 border-t border-slate-100 px-4 py-3 md:hidden">
            <a href="{{ url('/') }}"
               class="text-xs font-medium text-slate-600 hover:text-indigo-600">
                Home
            </a>

            <a href="{{ route('buyer.dashboard') }}"
               class="text-xs font-medium text-slate-600 hover:text-indigo-600">
                Marketplace
            </a>

            <a href="{{ route('terms') }}"
               class="text-xs font-medium text-slate-600 hover:text-indigo-600">
                Terms
            </a>

            <a href="{{ route('privacy') }}"
               class="text-xs font-medium text-slate-600 hover:text-indigo-600">
                Privacy
            </a>

            @guest
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="text-xs font-medium text-slate-600 hover:text-indigo-600">
                        Log in
                    </a>
                @endif
            @endguest
        </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Public Footer --}}
    <footer class="mt-auto border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">

            <div class="flex items-center gap-3">
                <x-application-logo variant="mark" name="MAHWI POS" class="h-8" />

                <div>
                    <p class="font-bold text-slate-900">Mahwi POS</p>
                    <p class="text-xs text-slate-500">Your Business Backbone</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-x-5 gap-y-2">
                <a href="{{ route('terms') }}"
                   class="text-sm text-slate-500 transition hover:text-indigo-600">
                    Terms of Service
                </a>

                <a href="{{ route('privacy') }}"
                   class="text-sm text-slate-500 transition hover:text-indigo-600">
                    Privacy Policy
                </a>

                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="text-sm text-slate-500 transition hover:text-indigo-600">
                        Login
                    </a>
                @endif
            </div>

            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} Mahwi POS. All rights reserved.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>