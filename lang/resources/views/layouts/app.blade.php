<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mahwi|Business management system designed for modern enterprises|It tracks inventory, sales, and more') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 text-blue-900">

    {{--
        Shared layout state (used by navigation.blade.php too):
          sidebarOpen : mobile drawer open/closed
          collapsed   : compact icon-only rail on md+ (tablet defaults to compact,
                        desktop defaults to expanded, user choice is remembered)
    --}}
    <div
        x-data="{
            sidebarOpen: false,
            collapsed: (() => {
                try {
                    const saved = localStorage.getItem('mahwi.sidebar.collapsed');
                    return saved === null ? window.innerWidth < 1024 : saved === '1';
                } catch (e) {
                    return window.innerWidth < 1024;
                }
            })(),
            toggleCollapsed() {
                this.collapsed = !this.collapsed;
                try { localStorage.setItem('mahwi.sidebar.collapsed', this.collapsed ? '1' : '0'); } catch (e) {}
            }
        }"
        x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen && window.innerWidth < 768)"
        @keydown.escape.window="sidebarOpen = false"
        @resize.window="if (window.innerWidth >= 768) sidebarOpen = false"
        class="min-h-screen overflow-x-hidden"
    >

        @include('layouts.navigation')

        {{-- Main column: leaves room for the sidebar on md+ --}}
        <div
            class="flex min-h-screen min-w-0 flex-col transition-[padding] duration-200 ease-out"
            :class="collapsed ? 'md:pl-[76px]' : 'md:pl-[272px]'"
        >
            @isset($header)
                <header class="border-b border-green-200 bg-white">
                    <div class="px-4 py-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="min-w-0 flex-1">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>

    </div>

    @stack('scripts')
</body>
</html>