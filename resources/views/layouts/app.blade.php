<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="mahwiApp()"
    x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)"
    @resize.window.debounce.150ms="if (window.innerWidth >= 1024) sidebarOpen = false"
>
<head>
    {{-- Basic meta (charset must come first) --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16a34a" id="mahwi-theme-color">
    <meta name="color-scheme" content="light dark">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MahWi POS">
    <meta name="application-name" content="MahWi POS">
    <meta name="description" content="MahWi is a business management system for sales, inventory, purchases, expenses, income and business operations.">

    @include('partials.seo')
    @stack('head')

    <title>
        @hasSection('title')
            @yield('title') — MahWi
        @else
            MahWi
        @endif
    </title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.ico') }}">

    {{-- PWA --}}
    @if(file_exists(public_path('manifest.json')))
        <link rel="manifest" href="{{ asset('manifest.json') }}">
    @endif

    {{-- Apply theme + sidebar state BEFORE first paint (no flash) and define the Alpine root component --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            const root = document.documentElement;

            try {
                if (localStorage.getItem('sidebar-collapsed') === '1') {
                    root.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            // Theme: saved choice wins, otherwise light.
            let theme = 'light';
            try {
                theme = localStorage.getItem('mahwi-theme') === 'dark' ? 'dark' : 'light';
            } catch (e) {}

            root.classList.toggle('dark', theme === 'dark');
            root.dataset.theme = theme;

            const meta = document.getElementById('mahwi-theme-color');
            if (meta) meta.setAttribute('content', theme === 'dark' ? '#0b1120' : '#16a34a');
        })();

        // Root Alpine component. Defined here so it exists before Alpine starts.
        function mahwiApp() {
            return {
                sidebarOpen: false,
                dark: document.documentElement.classList.contains('dark'),
                _themeTimer: null,

                init() {
                    // Keep several open tabs in sync.
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'mahwi-theme') {
                            this.setTheme(e.newValue === 'dark', false);
                        }
                    });
                },

                setTheme(isDark, persist = true) {
                    const root = document.documentElement;

                    // Short-lived class so colours fade without slowing normal interactions.
                    root.classList.add('theme-switching');
                    root.classList.toggle('dark', isDark);
                    root.dataset.theme = isDark ? 'dark' : 'light';
                    this.dark = isDark;

                    const meta = document.getElementById('mahwi-theme-color');
                    if (meta) meta.setAttribute('content', isDark ? '#0b1120' : '#16a34a');

                    if (persist) {
                        try { localStorage.setItem('mahwi-theme', isDark ? 'dark' : 'light'); } catch (e) {}
                    }

                    clearTimeout(this._themeTimer);
                    this._themeTimer = setTimeout(() => root.classList.remove('theme-switching'), 250);
                },

                toggleTheme() {
                    this.setTheme(!this.dark);
                },
            };
        }
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* ------------------------------------------------------------------
           Design tokens. Light is the default; `html.dark` swaps them.
           The sidebar (layouts/navigation.blade.php) uses the same tokens.
        ------------------------------------------------------------------ */
        :root {
            --sidebar-w: 18rem;

            --bg-page:     #f1f5f9;
            --surface:     #ffffff;
            --surface-2:   #f8fafc;
            --hover:       #f1f5f9;
            --border:      #e2e8f0;
            --border-soft: #f1f5f9;
            --text:        #0f172a;
            --text-soft:   #475569;
            --text-muted:  #64748b;
            --accent:      #16a34a;
            --accent-soft: #ecfdf5;
            --accent-text: #047857;
            --danger-text: #dc2626;
            --danger-soft: #fef2f2;
            --shadow:      0 10px 30px -10px rgb(15 23 42 / .25);

            color-scheme: light;
        }

        html.dark {
            --bg-page:     #0b1120;
            --surface:     #111827;
            --surface-2:   #0f172a;
            --hover:       #1f2937;
            --border:      #2a3648;
            --border-soft: #1f2937;
            --text:        #f1f5f9;
            --text-soft:   #cbd5e1;
            --text-muted:  #94a3b8;
            --accent:      #22c55e;
            --accent-soft: rgb(16 185 129 / .14);
            --accent-text: #6ee7b7;
            --danger-text: #f87171;
            --danger-soft: rgb(239 68 68 / .14);
            --shadow:      0 10px 30px -10px rgb(0 0 0 / .7);

            color-scheme: dark;
        }

        html {
            min-height: 100%;
            background-color: var(--bg-page);
            color: var(--text);
        }

        body {
            min-height: 100vh;
            background-color: var(--bg-page);
            color: var(--text);
            font-family: 'IBM Plex Sans', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Space Grotesk', sans-serif; }

        /* Colour fade only while the theme is being switched */
        html.theme-switching *,
        html.theme-switching *::before,
        html.theme-switching *::after {
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, fill .2s ease !important;
        }

        /* ------------------------------------------------------------------
           Layout shell
        ------------------------------------------------------------------ */
        .app-shell {
            min-width: 0;
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

        .app-header {
            background-color: var(--surface);
            background-color: color-mix(in srgb, var(--surface) 92%, transparent);
            border-bottom: 1px solid var(--border);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            padding-top: env(safe-area-inset-top, 0px);
        }

        .app-main {
            min-width: 0;
            min-height: calc(100vh - 4rem);
            min-height: calc(100dvh - 4rem);
            background-color: var(--surface);
            color: var(--text);
        }

        /* Header buttons / menus */
        .app-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            height: 2.5rem;
            min-width: 2.5rem;
            padding: 0 .65rem;
            border: 1px solid var(--border);
            border-radius: .75rem;
            background-color: var(--surface);
            color: var(--text-soft);
            font-size: .875rem;
            font-weight: 500;
            line-height: 1;
            box-shadow: 0 1px 2px rgb(0 0 0 / .04);
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
            cursor: pointer;
        }
        .app-btn:hover { background-color: var(--hover); color: var(--text); }
        .app-btn:focus-visible,
        .app-menu-item:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

        @media (min-width: 640px) { .app-btn { padding: 0 .85rem; } }

        .app-btn-ghost { border-color: transparent; background: transparent; box-shadow: none; }

        .app-dropdown {
            position: absolute;
            right: 0;
            z-index: 50;
            margin-top: .5rem;
            max-width: calc(100vw - 2rem);
            border: 1px solid var(--border);
            border-radius: 1rem;
            background-color: var(--surface);
            color: var(--text);
            box-shadow: var(--shadow);
        }

        .app-menu-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            width: 100%;
            padding: .6rem .75rem;
            border-radius: .7rem;
            font-size: .875rem;
            color: var(--text-soft);
            text-align: left;
            transition: background-color .15s ease, color .15s ease;
        }
        .app-menu-item:hover { background-color: var(--hover); color: var(--text); }
        .app-menu-item-active { background-color: var(--accent-soft); color: var(--accent-text); font-weight: 600; }
        .app-menu-item-active:hover { background-color: var(--accent-soft); color: var(--accent-text); }
        .app-menu-item-danger { color: var(--danger-text); }
        .app-menu-item-danger:hover { background-color: var(--danger-soft); color: var(--danger-text); }

        .lang-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 1.5rem;
            border-radius: .4rem;
            background-color: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .03em;
        }
        .app-menu-item-active .lang-chip {
            background-color: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        /* Theme toggle: icons and labels switch with CSS only, so there is no flash on load */
        .theme-when-dark { display: none; }
        html.dark .theme-when-dark { display: inline-flex; }
        html.dark .theme-when-light { display: none; }

        /* ------------------------------------------------------------------
           Main content loading overlay
        ------------------------------------------------------------------ */
        .main-loading-overlay {
            position: fixed;
            top: calc(4rem + env(safe-area-inset-top, 0px));
            right: 0;
            bottom: 0;
            left: var(--sidebar-w, 18rem);
            z-index: 35;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgb(248 250 252 / .82);
            -webkit-backdrop-filter: blur(3px);
            backdrop-filter: blur(3px);
            transition: left .2s ease;
        }
        html.dark .main-loading-overlay { background: rgb(11 17 32 / .82); }
        .main-loading-overlay[hidden] { display: none !important; }

        @media (max-width: 1023px) {
            .main-loading-overlay { left: 0; }
        }

        .main-loading-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 24px;
        }

        .main-loading-spinner {
            width: 58px;
            height: 58px;
            border: 5px solid #dcfce7;
            border-top-color: #16a34a;
            border-right-color: #16a34a;
            border-radius: 50%;
            animation: mahwi-spin .75s linear infinite;
        }
        html.dark .main-loading-spinner {
            border-color: #14532d;
            border-top-color: #22c55e;
            border-right-color: #22c55e;
        }

        .main-loading-text {
            color: #166534;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .3px;
        }
        html.dark .main-loading-text { color: #86efac; }

        @keyframes mahwi-spin { to { transform: rotate(360deg); } }

        @media (prefers-reduced-motion: reduce) {
            .app-shell,
            .main-loading-overlay,
            .app-btn,
            .app-menu-item { transition: none; }
            .main-loading-spinner { animation-duration: 2s; }
            html.theme-switching *,
            html.theme-switching *::before,
            html.theme-switching *::after { transition: none !important; }
        }

        /* ------------------------------------------------------------------
           Dark mode for page content (views using Tailwind gray/slate utilities)
        ------------------------------------------------------------------ */
        html.dark .bg-white                          { background-color: var(--surface) !important; }
        html.dark .bg-gray-50,  html.dark .bg-slate-50  { background-color: var(--surface-2) !important; }
        html.dark .bg-gray-100, html.dark .bg-slate-100 { background-color: #1f2937 !important; }
        html.dark .bg-gray-200, html.dark .bg-slate-200 { background-color: #374151 !important; }
        html.dark .bg-gray-300, html.dark .bg-slate-300 { background-color: #4b5563 !important; }

        html.dark .text-gray-900, html.dark .text-gray-800,
        html.dark .text-slate-900, html.dark .text-slate-800 { color: #f8fafc !important; }
        html.dark .text-gray-700, html.dark .text-slate-700  { color: #e2e8f0 !important; }
        html.dark .text-gray-600, html.dark .text-slate-600  { color: #cbd5e1 !important; }
        html.dark .text-gray-500, html.dark .text-gray-400,
        html.dark .text-slate-500, html.dark .text-slate-400 { color: #94a3b8 !important; }

        html.dark .border-gray-100,  html.dark .border-gray-200,  html.dark .border-gray-300,
        html.dark .border-slate-100, html.dark .border-slate-200, html.dark .border-slate-300,
        html.dark .divide-gray-100 > :not(:first-child),
        html.dark .divide-gray-200 > :not(:first-child),
        html.dark .divide-slate-100 > :not(:first-child),
        html.dark .divide-slate-200 > :not(:first-child) { border-color: var(--border) !important; }

        html.dark hr { border-color: var(--border); }

        /* Status colours (alerts, badges) */
        html.dark .bg-green-50,  html.dark .bg-emerald-50 { background-color: rgb(34 197 94 / .12) !important; }
        html.dark .bg-red-50                              { background-color: rgb(239 68 68 / .12) !important; }
        html.dark .bg-amber-50,  html.dark .bg-yellow-50  { background-color: rgb(245 158 11 / .12) !important; }
        html.dark .bg-blue-50,   html.dark .bg-indigo-50  { background-color: rgb(59 130 246 / .12) !important; }
        html.dark .bg-green-100, html.dark .bg-emerald-100 { background-color: rgb(34 197 94 / .2) !important; }
        html.dark .bg-red-100                             { background-color: rgb(239 68 68 / .2) !important; }
        html.dark .bg-amber-100, html.dark .bg-yellow-100 { background-color: rgb(245 158 11 / .2) !important; }
        html.dark .bg-blue-100,  html.dark .bg-indigo-100 { background-color: rgb(59 130 246 / .2) !important; }

        html.dark .text-green-900, html.dark .text-green-800, html.dark .text-green-700,
        html.dark .text-emerald-800, html.dark .text-emerald-700 { color: #86efac !important; }
        html.dark .text-red-900, html.dark .text-red-800, html.dark .text-red-700 { color: #fca5a5 !important; }
        html.dark .text-amber-900, html.dark .text-amber-800, html.dark .text-amber-700,
        html.dark .text-yellow-800, html.dark .text-yellow-700 { color: #fcd34d !important; }
        html.dark .text-blue-800, html.dark .text-blue-700,
        html.dark .text-indigo-800, html.dark .text-indigo-700 { color: #93c5fd !important; }

        html.dark .border-green-200, html.dark .border-emerald-200 { border-color: rgb(34 197 94 / .35) !important; }
        html.dark .border-red-200                                  { border-color: rgb(239 68 68 / .35) !important; }
        html.dark .border-amber-200, html.dark .border-yellow-200  { border-color: rgb(245 158 11 / .35) !important; }

        /* Hover surfaces */
        html.dark .hover\:bg-gray-50:hover,  html.dark .hover\:bg-slate-50:hover  { background-color: #1f2937 !important; }
        html.dark .hover\:bg-gray-100:hover, html.dark .hover\:bg-slate-100:hover { background-color: #374151 !important; }
        html.dark .hover\:bg-gray-200:hover, html.dark .hover\:bg-slate-200:hover { background-color: #4b5563 !important; }

        /* Form controls */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        html.dark select,
        html.dark textarea {
            background-color: var(--surface) !important;
            color: #f3f4f6 !important;
            border-color: #4b5563 !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder { color: #9ca3af !important; }
        html.dark option { background-color: var(--surface); color: #f3f4f6; }

        /* Tables */
        html.dark table thead { background-color: #1f2937 !important; color: #d1d5db !important; }
        html.dark table tbody tr { border-color: var(--border) !important; }
        html.dark table tbody tr:hover { background-color: #1f2937; }

        html.dark .shadow, html.dark .shadow-sm, html.dark .shadow-md,
        html.dark .shadow-lg, html.dark .shadow-xl { --tw-shadow-color: rgb(0 0 0 / 35%); }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen antialiased">

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
        aria-hidden="true"
    ></div>


    {{-- Main application --}}
    <div class="app-shell min-h-screen">

        @php
            $authUser = Auth::user();

            // Profile photo saved as public/images/users/{id}.jpg|jpeg|png (see User::getProfilePhotoUrlAttribute)
            $photo = $authUser?->profile_photo_url;

            $initials = collect(preg_split('/\s+/', trim($authUser?->name ?? 'U')))
                ->filter()
                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                ->take(2)
                ->implode('');

            $roleLabel = match ($authUser?->role) {
                'system_admin'        => __('System administrator'),
                'admin', 'shop_admin' => __('Administrator'),
                'manager'             => __('Manager'),
                'seller'              => __('Seller'),
                'cashier'             => __('Cashier'),
                'waiter'              => __('Waiter'),
                'accountant'          => __('Accountant'),
                'owner'               => __('Owner'),
                default               => ucfirst(str_replace('_', ' ', $authUser?->role ?? 'User')),
            };

            // code => [badge, native name]
            // Badges are used instead of flag emoji: Windows does not render flag emoji (shows "RW", "GB"...).
            $languages = [
                'en' => ['EN', 'English'],
                'fr' => ['FR', 'Français'],
                'rw' => ['RW', 'Kinyarwanda'],
                'sw' => ['SW', 'Kiswahili'],
            ];
            $currentLocale = app()->getLocale();
            $currentLang   = $languages[$currentLocale] ?? [strtoupper($currentLocale), strtoupper($currentLocale)];
        @endphp

        {{-- Top bar --}}
        <header class="app-header sticky top-0 z-30">

            <div class="flex h-16 items-center gap-2 px-4 sm:px-6 lg:px-8">

                {{-- Mobile menu --}}
                <button
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="app-btn app-btn-ghost lg:hidden"
                    :aria-expanded="sidebarOpen"
                    aria-controls="app-sidebar"
                    aria-label="{{ __('Toggle navigation') }}"
                >
                    <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Page header (@section('header') style) --}}
                <div class="hidden min-w-0 flex-1 lg:block">
                    @hasSection('header')
                        <div class="truncate">@yield('header')</div>
                    @endif
                </div>

                {{-- Header actions --}}
                <div class="ml-auto flex min-w-0 items-center gap-1.5 sm:gap-2">

                    {{-- Theme switch (toggles the `dark` class on <html>) --}}
                    <button
                        type="button"
                        id="mahwi-theme-toggle"
                        @click="toggleTheme()"
                        :aria-pressed="dark"
                        :aria-label="dark ? @js(__('Switch to light mode')) : @js(__('Switch to dark mode'))"
                        :title="dark ? @js(__('Switch to light mode')) : @js(__('Switch to dark mode'))"
                        class="app-btn"
                    >
                        {{-- Moon (shown in light mode) --}}
                        <svg class="theme-when-light h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                        </svg>
                        {{-- Sun (shown in dark mode) --}}
                        <svg class="theme-when-dark h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>

                        <span class="hidden sm:inline">
                            <span class="theme-when-light">{{ __('Dark mode') }}</span>
                            <span class="theme-when-dark">{{ __('Light mode') }}</span>
                        </span>
                    </button>

                    {{-- Language --}}
                    <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            aria-haspopup="menu"
                            :aria-expanded="open"
                            aria-label="{{ __('Language') }}: {{ $currentLang[1] }}"
                            class="app-btn"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold">{{ $currentLang[0] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-3.5 w-3.5 shrink-0 opacity-60 transition-transform sm:block" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition.origin.top.right
                            role="menu"
                            aria-label="{{ __('Language') }}"
                            class="app-dropdown w-56 p-1.5"
                        >
                            <div class="px-3 pb-1.5 pt-1 text-xs font-semibold" style="color: var(--text-muted)">{{ __('Language') }}</div>

                            @foreach($languages as $code => [$badge, $name])
                                @php $isCurrent = $currentLocale === $code; @endphp
                                <a
                                    href="{{ route('language.switch', $code) }}"
                                    role="menuitem"
                                    lang="{{ $code }}"
                                    hreflang="{{ $code }}"
                                    @if($isCurrent) aria-current="true" @endif
                                    class="app-menu-item {{ $isCurrent ? 'app-menu-item-active' : '' }}"
                                >
                                    <span class="lang-chip">{{ $badge }}</span>
                                    <span class="flex-1 truncate">{{ $name }}</span>
                                    @if($isCurrent)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
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
                                aria-haspopup="menu"
                                :aria-expanded="open"
                                aria-label="{{ __('Account menu') }}"
                                class="app-btn app-btn-ghost !h-auto gap-2 !px-1.5 py-1.5 sm:gap-3 sm:!px-2"
                            >
                                @if($photo)
                                    <img src="{{ $photo }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover ring-1" style="--tw-ring-color: var(--border)">
                                @else
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold" style="background: var(--accent-soft); color: var(--accent-text)">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @endif

                                <div class="hidden max-w-[140px] text-left md:block">
                                    <p class="truncate text-sm font-semibold" style="color: var(--text)">{{ $authUser->name }}</p>
                                    <p class="truncate text-xs font-normal" style="color: var(--text-muted)">{{ $roleLabel }}</p>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 shrink-0 opacity-60 transition-transform sm:block" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition.origin.top.right
                                role="menu"
                                class="app-dropdown w-72 overflow-hidden"
                            >
                                <div class="flex items-center gap-3 p-4" style="border-bottom: 1px solid var(--border-soft)">
                                    @if($photo)
                                        <img src="{{ $photo }}" alt="" class="h-12 w-12 shrink-0 rounded-full object-cover">
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full font-semibold" style="background: var(--accent-soft); color: var(--accent-text)">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="truncate font-semibold" style="color: var(--text)">{{ $authUser->name }}</p>
                                        <p class="truncate text-xs" style="color: var(--text-muted)">{{ $authUser->email }}</p>
                                        <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold" style="background: var(--accent-soft); color: var(--accent-text)">{{ $roleLabel }}</span>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <a href="{{ route('profile.edit') }}" role="menuitem" class="app-menu-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('Profile Settings') }}
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" role="menuitem" class="app-menu-item app-menu-item-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
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
                <div class="px-4 py-3 lg:hidden" style="border-top: 1px solid var(--border-soft)">
                    @yield('header')
                </div>
            @endif
        </header>


        {{-- Page header (<x-slot name="header"> style).
             Lives outside the sticky bar so it scrolls away and the markup stays valid. --}}
        @isset($header)
            <div class="bg-white shadow">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </div>
        @endisset


        {{-- Main content --}}
        <main class="app-main p-4 sm:p-6 lg:p-8">

            @if(session('success'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    role="status"
                    class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    <span class="min-w-0 break-words">{{ session('success') }}</span>
                    <button type="button" @click="show = false" aria-label="{{ __('Close') }}" class="text-lg leading-none opacity-70 hover:opacity-100">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    role="alert"
                    class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                >
                    <span class="min-w-0 break-words">{{ session('error') }}</span>
                    <button type="button" @click="show = false" aria-label="{{ __('Close') }}" class="text-lg leading-none opacity-70 hover:opacity-100">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="mb-2 font-semibold">{{ __('Please correct the following:') }}</p>
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

    {{-- Main content loading indicator --}}
    <div
        id="main-loading-overlay"
        class="main-loading-overlay"
        hidden
        role="status"
        aria-live="polite"
        aria-label="{{ __('Loading page') }}"
    >
        <div class="main-loading-inner">
            <div class="main-loading-spinner"></div>
            <span class="main-loading-text">{{ __('Loading, please wait...') }}</span>
        </div>
    </div>


    <script nonce="{{ Vite::cspNonce() }}">
    (() => {
        const overlay = document.getElementById('main-loading-overlay');
        if (!overlay) return;

        let safetyTimer = null;

        function showMainLoader() {
            overlay.hidden = false;

            // Safety net: downloads (PDF/Excel exports) never unload the page,
            // so the loader must not stay on screen forever.
            clearTimeout(safetyTimer);
            safetyTimer = setTimeout(hideMainLoader, 20000);
        }

        function hideMainLoader() {
            overlay.hidden = true;
            clearTimeout(safetyTimer);
            safetyTimer = null;
        }

        // Links
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;

            if (
                event.defaultPrevented ||
                event.button !== 0 ||
                event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ||
                (link.target && link.target !== '_self') ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-loader') ||
                link.closest('[data-no-loader]')
            ) return;

            const rawHref = link.getAttribute('href') || '';
            if (rawHref === '' || rawHref.startsWith('#') || /^(javascript|mailto|tel):/i.test(rawHref)) return;

            const url = new URL(link.href, window.location.href);

            // External links, and same-page anchors
            if (url.origin !== window.location.origin) return;
            if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;

            showMainLoader();
        });

        // Normal form submissions (forms handled by JS call preventDefault and are skipped)
        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (
                event.defaultPrevented ||
                !(form instanceof HTMLFormElement) ||
                form.target === '_blank' ||
                form.closest('[data-no-loader]') ||
                (event.submitter && event.submitter.hasAttribute('data-no-loader'))
            ) return;

            showMainLoader();
        });

        // Back/forward cache restore
        window.addEventListener('pageshow', hideMainLoader);

        // Helpers for custom navigation
        window.mahwiShowLoader = showMainLoader;
        window.mahwiHideLoader = hideMainLoader;
    })();
    </script>

    @stack('scripts')
</body>
</html>