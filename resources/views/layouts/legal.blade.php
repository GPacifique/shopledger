<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Privacy Policy') | Mahwi POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
    <main>
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            @include('partials.back-button', ['fallbackUrl' => auth()->check() ? route('dashboard') : url('/')])
        </div>
        @yield('content')
    </main>
</body>
</html>