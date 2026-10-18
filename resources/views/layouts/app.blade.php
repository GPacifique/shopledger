<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="mahwiApp()"
    x-init="init()"
    x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)"
    @resize.window.debounce.150ms="if (window.innerWidth >= 1024) sidebarOpen = false"
    class="bg-slate-100 text-slate-900"
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
            MahWi
        @endif
    </title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/MAHWILOGO.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/MAHWILOGO.png') }}">

    {{-- PWA --}}
    @if(file_exists(public_path('manifest.json')))
        <link rel="manifest" href="{{ asset('manifest.json') }}">
    @endif

    {{-- Apply sidebar state before first paint --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            try {
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

        /* MahWi is light-only. Prevent OS dark-mode preferences from changing this layout. */
        html,
        body {
            color-scheme: light;
            background-color: #f1f5f9;
            color: #0f172a;
        }

        @media (max-width: 1023px) {
            html,
            body {
                color-scheme: light;
                background-color: #f1f5f9;
                color: #0f172a;
            }
        }

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
        /* Main content loading overlay */

/* Main content loading overlay */
.main-loading-overlay {
    position: fixed;
    top: 4rem;
    right: 0;
    bottom: 0;
    left: var(--sidebar-w, 18rem);
    z-index: 40;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(248, 250, 252, 0.82);
    backdrop-filter: blur(3px);
    transition: left 0.2s ease, opacity 0.2s ease;
}

.main-loading-overlay[hidden] {
    display: none !important;
}

/* Mobile: cover the full page below the top bar */
@media (max-width: 1023px) {
    .main-loading-overlay {
        left: 0;
    }
}

/* Respect reduced-motion preferences */
@media (prefers-reduced-motion: reduce) {
    .main-loading-overlay,
    .app-shell {
        transition: none;
    }

    .main-loading-spinner {
        animation-duration: 2s;
    }
}


.main-loading-spinner {
    width: 58px;
    height: 58px;
    border: 5px solid #dcfce7;
    border-top-color: #16a34a;
    border-right-color: #16a34a;
    border-radius: 50%;
    animation: mahwi-spin 0.75s linear infinite;
}

.main-loading-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    padding: 24px;
}

.main-loading-text {
    color: #166534;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

@keyframes mahwi-spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 1023px) {
    .main-loading-overlay {
        left: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .main-loading-spinner {
        animation-duration: 2s;
    }
}
    html {
        color-scheme: light;
    }

    html.dark {
        color-scheme: dark;
    }

    html.dark body {
        background-color: #0b1120 !important;
        color: #e5e7eb !important;
    }

    /* Main surfaces, cards, tables, dropdowns and forms */
    html.dark .bg-white {
        background-color: #111827 !important;
    }

    html.dark .bg-gray-50 {
        background-color: #0f172a !important;
    }

    html.dark .bg-gray-100 {
        background-color: #1f2937 !important;
    }

    html.dark .bg-gray-200 {
        background-color: #374151 !important;
    }

    html.dark .bg-gray-300 {
        background-color: #4b5563 !important;
    }

    /* Text */
    html.dark .text-gray-900,
    html.dark .text-gray-800 {
        color: #f9fafb !important;
    }

    html.dark .text-gray-700 {
        color: #e5e7eb !important;
    }

    html.dark .text-gray-600 {
        color: #d1d5db !important;
    }

    html.dark .text-gray-500 {
        color: #9ca3af !important;
    }

    html.dark .text-gray-400 {
        color: #9ca3af !important;
    }

    /* Borders and dividers */
    html.dark .border-gray-100,
    html.dark .border-gray-200,
    html.dark .border-gray-300,
    html.dark .divide-gray-100 > :not(:first-child),
    html.dark .divide-gray-200 > :not(:first-child) {
        border-color: #374151 !important;
    }

    html.dark hr {
        border-color: #374151;
    }

    /* Inputs and form controls */
    html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="range"]),
    html.dark select,
    html.dark textarea {
        background-color: #111827 !important;
        color: #f3f4f6 !important;
        border-color: #4b5563 !important;
    }

    html.dark input::placeholder,
    html.dark textarea::placeholder {
        color: #9ca3af !important;
    }

    html.dark option {
        background-color: #111827;
        color: #f3f4f6;
    }

    /* Tables */
    html.dark table thead {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    html.dark table tbody tr {
        border-color: #374151 !important;
    }

    html.dark table tbody tr:hover {
        background-color: #1f2937;
    }

    /* Common navigation and hover surfaces */
    html.dark .hover\:bg-gray-50:hover {
        background-color: #1f2937 !important;
    }

    html.dark .hover\:bg-gray-100:hover {
        background-color: #374151 !important;
    }

    html.dark .hover\:bg-gray-200:hover {
        background-color: #4b5563 !important;
    }

    html.dark .shadow,
    html.dark .shadow-sm,
    html.dark .shadow-md,
    html.dark .shadow-lg,
    html.dark .shadow-xl {
        --tw-shadow-color: rgb(0 0 0 / 25%);
    }

    /* Native browser controls */
    html.dark input[type="date"],
    html.dark input[type="time"],
    html.dark input[type="datetime-local"] {
        color-scheme: dark;
    }

    /* Smooth changes without interfering with layout */
    body,
    body * {
        transition-property: background-color, border-color, color;
        transition-duration: 150ms;
        transition-timing-function: ease-in-out;
    }

    @media (prefers-reduced-motion: reduce) {
        body,
        body * {
            transition-duration: 0ms !important;
        }
    }

    </style>

    @stack('styles')
    <script>
    (function () {
        const savedTheme = localStorage.getItem('mahwi-theme');
        const theme = savedTheme === 'dark' ? 'dark' : 'light';

        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.dataset.theme = theme;
    })();
</script>
</head>


<body class="min-h-screen bg-white-100 text-blue-900">

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
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">

            <div class="flex h-16 items-center justify-between gap-2 px-4 sm:px-6 lg:px-8">

                {{-- Mobile menu --}}
                <button
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-blue-600 transition hover:bg-slate-100 lg:hidden"
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
<button
    type="button"
    id="mahwi-theme-toggle"
    aria-label="Switch to dark mode"
    title="Switch theme"
    class="inline-flex items-center justify-center gap-2 rounded-lg
           border border-gray-200 bg-white px-3 py-2
           text-sm font-medium text-gray-700 shadow-sm
           transition hover:bg-gray-100
           focus:outline-none focus:ring-2 focus:ring-indigo-500
           dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
           dark:hover:bg-gray-700"
>
    <svg id="mahwi-sun-icon" class="hidden h-5 w-5" fill="none"
         stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="4" stroke-width="2"/>
        <path stroke-linecap="round" stroke-width="2"
              d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3
                 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42
                 m11.3-11.3 1.42-1.42"/>
    </svg>

    <svg id="mahwi-moon-icon" class="h-5 w-5" fill="none"
         stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2"
              d="M20.9 13A9 9 0 0 1 11 3.1
                 9 9 0 1 0 20.9 13Z"/>
    </svg>

    <span id="mahwi-theme-label">Dark mode</span>
</button>
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
                            class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100 sm:px-3"
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
                            class="absolute right-0 z-50 mt-2 w-48 max-w-[calc(100vw-2rem)] rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg"
                        >
                            @foreach($languages as $code => [$flag, $name])
                                <a
                                    href="{{ route('language.switch', $code) }}"
                                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ app()->getLocale() === $code ? 'bg-green-50 font-semibold text-green-700' : 'text-slate-600 hover:bg-slate-100' }}"
                                >
                                    <span>{{ $flag }}</span>
                                    <span>{{ $name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Account --}}
                    @auth
                        <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                aria-haspopup="true"
                                :aria-expanded="open"
                                class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500"
                            >
                                @if($photo)
                                    <img src="{{ $photo }}" alt="{{ $authUser->name }}" class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @endif

                                <div class="hidden max-w-[140px] text-left md:block">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $authUser->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $roleLabel }}</p>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                class="absolute right-0 z-50 mt-2 w-72 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
                            >
                                <div class="flex items-center gap-3 border-b border-slate-100 p-4">
                                    @if($photo)
                                        <img src="{{ $photo }}" alt="{{ $authUser->name }}" class="h-12 w-12 shrink-0 rounded-full object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-green-100 font-semibold text-green-700">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-900">{{ $authUser->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $authUser->email }}</p>
                                        <span class="mt-1 inline-flex rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-700">{{ $roleLabel }}</span>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('Profile Settings') }}
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-red-600 transition hover:bg-red-50">
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
                <div class="border-t border-slate-100 px-4 py-3 lg:hidden">
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
        <main class="min-w-0 min-h-[calc(100vh-4rem)] bg-white p-4 text-slate-900 transition-colors duration-300 sm:p-6 lg:p-8">

            @if(session('success'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
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
                    class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                >
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="show = false" class="text-lg opacity-70 hover:opacity-100">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
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
{{-- Main content loading indicator --}}
<div
    id="main-loading-overlay"
    class="main-loading-overlay"
    hidden
    role="status"
    aria-live="polite"
    aria-label="Loading page"
>
    <div class="main-loading-inner">
        <div class="main-loading-spinner"></div>
        <span class="main-loading-text">Loading, please wait...</span>
    </div>
</div>
        </main>
    </div>


    {{-- Alpine app state --}}
    <script nonce="{{ Vite::cspNonce() }}">
        function mahwiApp() {
            return {
                sidebarOpen: false,

                init() {
                    // MahWi is light-only.
                    document.documentElement.classList.remove('dark');
                    document.body.classList.remove('dark');
                }
            }
        }
    </script>
    <script nonce="{{ Vite::cspNonce() }}">
    (() => {
        const overlay = document.getElementById('main-loading-overlay');

        if (!overlay) return;

        let loadingTimer = null;

        function showMainLoader() {
            overlay.hidden = false;
        }

        function hideMainLoader() {
            overlay.hidden = true;

            if (loadingTimer) {
                clearTimeout(loadingTimer);
                loadingTimer = null;
            }
        }

        // Show the loader after the browser begins navigating away.
        document.addEventListener('click', function (event) {
            const link = event.target.closest('a[href]');

            if (!link) return;

            if (
                event.defaultPrevented ||
                event.button !== 0 ||
                event.metaKey ||
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey ||
                link.target === '_blank' ||
                link.hasAttribute('download')
            ) return;

            const url = new URL(link.href, window.location.href);

            // Skip same-page anchors, external links and JavaScript links.
            if (
                url.origin !== window.location.origin ||
                url.pathname === window.location.pathname &&
                url.search === window.location.search &&
                url.hash
            ) return;

            if (
                link.hasAttribute('data-no-loader') ||
                link.closest('[data-no-loader]')
            ) return;

            showMainLoader();
        });

        // Show for normal form submissions, including POST forms.
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (
                form.matches('[data-no-loader]') ||
                form.querySelector('[data-no-loader]') ||
                form.target === '_blank'
            ) return;

            showMainLoader();
        });

        // Restore the page if the browser returns through its back-forward cache.
        window.addEventListener('pageshow', hideMainLoader);

        // Handle browser navigation away from the page.
        window.addEventListener('beforeunload', showMainLoader);

        // Optional helper for forms or custom navigation.
        window.mahwiShowLoader = showMainLoader;
        window.mahwiHideLoader = hideMainLoader;
    })();
    
    (function () {
        const button = document.getElementById('mahwi-theme-toggle');
        const label = document.getElementById('mahwi-theme-label');
        const sun = document.getElementById('mahwi-sun-icon');
        const moon = document.getElementById('mahwi-moon-icon');

        if (!button || button.dataset.initialized === 'true') return;

        button.dataset.initialized = 'true';

        function updateThemeUI() {
            const isDark = document.documentElement.classList.contains('dark');

            button.setAttribute(
                'aria-label',
                isDark ? 'Switch to light mode' : 'Switch to dark mode'
            );

            if (label) {
                label.textContent = isDark ? 'Light mode' : 'Dark mode';
            }

            if (sun) sun.classList.toggle('hidden', !isDark);
            if (moon) moon.classList.toggle('hidden', isDark);
        }

        button.addEventListener('click', function () {
            const isDark = !document.documentElement.classList.contains('dark');

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

            try {
                localStorage.setItem('mahwi-theme', isDark ? 'dark' : 'light');
            } catch (error) {
                // Theme still works for this page if storage is unavailable.
            }

            updateThemeUI();
        });

        updateThemeUI();
    })();
</script>

    @stack('scripts')
</body>
</html>