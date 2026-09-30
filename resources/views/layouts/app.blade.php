<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="mahwiApp()"
    x-init="init()"
    :class="{ 'dark': darkMode }"
    class="bg-slate-100 dark:bg-slate-950"
>
<head>

    {{-- Basic meta --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MahWi POS">
    <meta name="application-name" content="MahWi POS">
    <meta name="description" content="MahWi is a business management system for sales, inventory, purchases, expenses, income and business operations.">

    <title>
        @hasSection('title')
            @yield('title') — MahWi
        @else
            MahWi POS
        @endif
    </title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/MAHWILOGO.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/MAHWILOGO.png') }}">

    {{-- PWA --}}
    @if(file_exists(public_path('manifest.json')))
        <link rel="manifest" href="{{ asset('manifest.json') }}">
    @endif

    {{-- Apply saved theme and sidebar state before first paint (prevents flashes) --}}
    <script>
        (() => {
            try {
                const theme = localStorage.getItem('mahwi-theme');

                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }

                if (localStorage.getItem('sidebar-collapsed') === '1') {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        html { min-height: 100%; }

        body {
            min-height: 100vh;
            font-family: 'IBM Plex Sans', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Space Grotesk', sans-serif; }

        /* Sidebar width. The sidebar and the page content both follow this variable. */
        :root { --sidebar-w: 18rem; }

        @media (min-width: 1024px) {
            html.sidebar-collapsed { --sidebar-w: 4.5rem; }

            .app-shell {
                padding-left: var(--sidebar-w);
                transition: padding-left .2s ease;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .app-shell { transition: none; }
        }
    </style>

    @stack('styles')
</head>


<body class="min-h-screen bg-slate-100 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">

    {{-- Sidebar (fixed on desktop, slide-in drawer on mobile) --}}
    @include('layouts.navigation')

    {{-- Mobile drawer overlay --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
    ></div>


    {{-- Main application --}}
    <div class="app-shell min-h-screen">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900/95">

            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">

                {{-- Mobile menu --}}
                <button
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden"
                    aria-label="Toggle navigation"
                >
                    <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Page header (@section('header') style) --}}
                <div class="hidden flex-1 lg:block">
                    @hasSection('header')
                        <div class="truncate">@yield('header')</div>
                    @endif
                </div>

                {{-- Header actions --}}
                <div class="ml-auto flex items-center gap-2">

                    {{-- Light / dark mode --}}
                    <button
                        type="button"
                        @click="toggleTheme()"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-yellow-300 dark:hover:bg-slate-700"
                        :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m12.728 0-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    {{-- User --}}
                    @auth
                        <div class="hidden items-center gap-3 rounded-xl px-2 py-1.5 sm:flex">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>

                            <div class="hidden md:block">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
                            </div>
                        </div>
                    @endauth

                </div>
            </div>

            {{-- Mobile page header (@section('header') style) --}}
            @hasSection('header')
                <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-800 lg:hidden">
                    @yield('header')
                </div>
            @endif
        </header>


        {{-- Page header (<x-slot name="header"> style).
             Lives outside the sticky bar so it scrolls away and the markup stays valid. --}}
        @isset($header)
            <div class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </div>
        @endisset


        {{-- Main content --}}
        <main class="min-h-[calc(100vh-4rem)] bg-slate-100 p-4 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100 sm:p-6 lg:p-8">

            @if(session('success'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300"
                >
                    <span>{{ session('success') }}</span>
                    <button type="button" @click="show = false" class="text-lg opacity-70 hover:opacity-100">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
                >
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="show = false" class="text-lg opacity-70 hover:opacity-100">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                    <p class="mb-2 font-semibold">Please correct the following:</p>
                    <ul class="list-inside list-disc space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- @extends('layouts.app') support --}}
            @yield('content')

            {{-- <x-app-layout> support --}}
            @isset($slot)
                {{ $slot }}
            @endisset

        </main>
    </div>


    {{-- Alpine app state --}}
    <script>
        function mahwiApp() {
            return {
                darkMode: false,
                sidebarOpen: false,

                init() {
                    const savedTheme = localStorage.getItem('mahwi-theme');

                    if (savedTheme === 'dark') {
                        this.darkMode = true;
                    } else if (savedTheme === 'light') {
                        this.darkMode = false;
                    } else {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    this.applyTheme();
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('mahwi-theme', this.darkMode ? 'dark' : 'light');
                    this.applyTheme();
                },

                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.darkMode);
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>