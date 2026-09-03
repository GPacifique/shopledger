<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MahWi') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800"
        rel="stylesheet"
    />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen relative overflow-hidden">

        {{-- Background Decoration --}}
        <div
            class="absolute inset-0 bg-gradient-to-br
                   from-indigo-50 via-white to-purple-50"
        ></div>

        {{-- Decorative Blobs --}}
        <div
            class="absolute -top-32 -left-32 w-96 h-96
                   bg-indigo-200/30 rounded-full blur-3xl"
        ></div>

        <div
            class="absolute -bottom-32 -right-32 w-96 h-96
                   bg-purple-200/30 rounded-full blur-3xl"
        ></div>

        {{-- Main Content --}}
        <main class="relative min-h-screen">
            {{ $slot }}
        </main>

    </div>

</body>
</html>
