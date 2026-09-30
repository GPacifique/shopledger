<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="mahwiApp()"
    x-init="init()"
    x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)"
    @resize.window.debounce.150ms="if (window.innerWidth >= 1024) sidebarOpen = false"
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
    <script nonce="{{ Vite::cspNonce() }}">
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

        .app-shell {
            padding-left: env(safe-area-inset-left, 0px);
            padding-right: env(safe-area-inset-right, 0px);
        }

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

        @php
            $authUser = Auth::user();

            $photo = null;
            foreach (['profile_photo_path', 'profile_image', 'avatar'] as $field) {
                if (!empty($authUser?->{$field})) {
                    $photo = asset('storage/' . $authUser->{$field});
                    break;
                }
            }

            $initials = collect(preg_split('/\s+/', trim($authUser?->name ?? 'U')))
                ->filter()
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->implode('');

            $roleLabel = match ($authUser?->role) {
                'admin', 'shop_admin' => __('Administrator'),
                'seller'              => __('Seller'),
                'waiter'              => __('Waiter'),
                'accountant'          => __('Accountant'),
                'owner'               => __('Owner'),
                default               => ucfirst(str_replace('_', ' ', $authUser?->role ?? 'User')),
            };

            $languages = [
                'en' => ['🇬🇧', 'English'],
                'fr' => ['🇫🇷', 'Français'],
                'rw' => ['🇷🇼', 'Kinyarwanda'],
                'sw' => ['🇹🇿', 'Kiswahili'],
            ];
        @endphp

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900/95">

            <div class="flex h-16 items-center justify-between gap-2 px-4 sm:px-6 lg:px-8">

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
                <div class="ml-auto flex min-w-0 items-center gap-1.5 sm:gap-2">

                    {{-- Language --}}
                    <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            aria-haspopup="true"
                            :aria-expanded="open"
                            aria-label="{{ __('Language') }}"
                            class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100 sm:px-3 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ strtoupper(app()->getLocale()) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-3.5 w-3.5 text-slate-400 transition-transform sm:block" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition
                            class="absolute right-0 z-50 mt-2 w-48 max-w-[calc(100vw-2rem)] rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-800"
                        >
                            @foreach($languages as $code => [$flag, $name])
                                <a
                                    href="{{ route('language.switch', $code) }}"
                                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ app()->getLocale() === $code ? 'bg-green-50 font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700' }}"
                                >
                                    <span>{{ $flag }}</span>
                                    <span>{{ $name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Light / dark mode --}}
                    <button
                        type="button"
                        @click="toggleTheme()"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-yellow-300 dark:hover:bg-slate-700"
                        :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m12.728 0-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    {{-- Account --}}
                    @auth
                        <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                aria-haspopup="true"
                                :aria-expanded="open"
                                class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 dark:hover:bg-slate-800"
                            >
                                @if($photo)
                                    <img src="{{ $photo }}" alt="{{ $authUser->name }}" class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @endif

                                <div class="hidden max-w-[140px] text-left md:block">
                                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ $authUser->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $roleLabel }}</p>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                class="absolute right-0 z-50 mt-2 w-72 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800"
                            >
                                <div class="flex items-center gap-3 border-b border-slate-100 p-4 dark:border-slate-700">
                                    @if($photo)
                                        <img src="{{ $photo }}" alt="{{ $authUser->name }}" class="h-12 w-12 shrink-0 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-green-100 font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $authUser->name }}</p>
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $authUser->email }}</p>
                                        <span class="mt-1 inline-flex rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">{{ $roleLabel }}</span>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('Profile Settings') }}
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
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
        <main class="min-w-0 min-h-[calc(100vh-4rem)] bg-slate-100 p-4 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100 sm:p-6 lg:p-8">

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
    <script nonce="{{ Vite::cspNonce() }}">
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