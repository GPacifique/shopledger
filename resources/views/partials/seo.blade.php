@php
    $siteName    = 'MahWi';
    $title       = trim($__env->yieldContent('title')) ?: 'MahWi: sales, stock and money for every shop you run';
    $description = trim($__env->yieldContent('description')) ?: 'Point of sale, purchases, expenses, products and reports for one shop or many. Built for shops in Rwanda.';
    $image       = trim($__env->yieldContent('og_image')) ?: asset('images/og-image.png');
    $canonical   = trim($__env->yieldContent('canonical')) ?: url()->current();
    $robots      = trim($__env->yieldContent('robots')) ?: 'index, follow, max-image-preview:large';
    $locale      = ['en' => 'en_US', 'fr' => 'fr_FR', 'rw' => 'rw_RW'][app()->getLocale()] ?? 'en_US';
@endphp
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#15803d">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Mahwi POS">
<link rel="apple-touch-icon" href="/icons/mahwi-192.png">
{{-- Basic --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<meta name="author" content="MahWi">
<meta name="keywords" content="shop management system, point of sale Rwanda, POS, inventory, stock management, sales tracking, expenses, profit and loss, multi-shop">
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph: WhatsApp, Facebook, LinkedIn, Telegram --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:locale" content="{{ $locale }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:secure_url" content="{{ $image }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $siteName }} logo and tagline">

{{-- Twitter / X --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
<meta name="twitter:image:alt" content="{{ $siteName }} logo and tagline">

{{-- Icons --}}
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">

{{-- PWA --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0f766e">
<meta name="application-name" content="{{ $siteName }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="format-detection" content="telephone=no">

{{-- Structured data for Google --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id'   => url('/') . '#org',
            'name'  => $siteName,
            'url'   => url('/'),
            'logo'  => asset('images/MAHWILOGO.png'),
        ],
        [
            '@type' => 'WebSite',
            'url'   => url('/'),
            'name'  => $siteName,
            'publisher' => ['@id' => url('/') . '#org'],
        ],
        [
            '@type' => 'SoftwareApplication',
            'name'  => $siteName,
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web, Android, iOS, Windows',
            'description' => $description,
            'url'   => url('/'),
            'image' => $image,
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>