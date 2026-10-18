<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Sign-in pages should never appear in search results or be sent as a referrer. --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="same-origin">

    <title>{{ $title ?? config('app.name', 'MAHWI') }}</title>

    {{-- System fonts only: no third-party font requests, so nothing leaks and nothing is blocked by CSP. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">

    {{-- Maintenance notice (set in Admin > Settings; null when off) --}}
    @if (!empty($maintenanceNotice))
        <div class="bg-amber-100 px-4 py-2 text-center text-sm font-medium text-amber-900" role="status">
            {{ $maintenanceNotice }}
        </div>
    @endif

    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

        {{-- Brand --}}
        <a href="{{ url('/') }}"
           class="mb-6 flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
            <x-application-logo class="h-12 w-auto fill-current text-gray-800" />
            <span class="text-2xl font-bold tracking-tight text-gray-900">MAHWI</span>
        </a>

        {{-- Form card --}}
        <main class="w-full max-w-md rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm sm:px-8">
            {{ $slot }}
        </main>

        {{-- Language switcher --}}
        <nav class="mt-6 flex flex-wrap justify-center gap-1.5" aria-label="{{ __('Language') }}">
            @foreach (['en' => 'EN', 'fr' => 'FR', 'rw' => 'RW', 'sw' => 'SW'] as $code => $label)
                @if (Route::has('language.switch'))
                    <a href="{{ route('language.switch', $code) }}"
                       @if (app()->getLocale() === $code) aria-current="true" @endif
                       class="inline-flex min-h-[36px] min-w-[44px] items-center justify-center rounded-lg px-2.5 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                              {{ app()->getLocale() === $code
                                  ? 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200'
                                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </nav>

        <p class="mt-4 text-xs text-gray-500">&copy; {{ date('Y') }} MAHWI</p>
    </div>
</body>
</html>