{{--
    resources/views/welcome.blade.php
    Public landing page. Uses only guest-safe routes: login, register, contact, language.switch, dashboard (when signed in).
    Self-contained CSS, no Vite or Tailwind build needed.
--}}
@php
    $appName = config('app.name', 'Shop Manager');
    $locales = config('app.available_locales', [
        'en' => 'English',
        'fr' => 'Français',
        'rw' => 'Kinyarwanda',
    ]);
    $current = app()->getLocale();
    $hasLogin = Route::has('login');
    $hasRegister = Route::has('register');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $current) }}">
<head>
    @include('partials.seo')
    @stack('head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }}: sales, stock and money for every shop you run</title>
    <meta name="description" content="Point of sale, purchases, expenses, products and reports for one shop or many. Sellers, waiters, managers and accountants each get the screens they need.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style nonce="{{ $cspNonce ?? '' }}">
        :root {
            --counter: #E8EDF0;
            --slip: #FFFFFF;
            --ink: #0F2A3D;
            --ink-soft: #3E566A;
            --cobalt: #1F4FFF;
            --cobalt-dark: #1739C2;
            --mango: #FFB627;
            --basil: #1E7D52;
            --line: #C9D3DA;
            --display: "Bricolage Grotesque", "Segoe UI", system-ui, sans-serif;
            --body: "Instrument Sans", "Segoe UI", system-ui, sans-serif;
            --mono: "IBM Plex Mono", ui-monospace, Menlo, Consolas, monospace;
            --wrap: 1120px;
            color-scheme: light;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 84px; }
        body {
            margin: 0;
            font-family: var(--body);
            font-size: 1.0625rem;
            line-height: 1.6;
            color: var(--ink);
            background: var(--counter);
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3 { font-family: var(--display); line-height: 1.08; margin: 0; letter-spacing: -0.02em; }
        p { margin: 0; }
        a { color: inherit; }
        ul { margin: 0; padding: 0; list-style: none; }
        :focus-visible { outline: 3px solid var(--cobalt); outline-offset: 3px; border-radius: 4px; }

        .wrap { max-width: var(--wrap); margin: 0 auto; padding: 0 24px; }
        .skip {
            position: absolute; left: 16px; top: -60px; z-index: 100;
            background: var(--ink); color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none;
        }
        .skip:focus { top: 12px; }

        /* ---------- Buttons ---------- */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            font: 600 1rem/1 var(--body);
            padding: 15px 24px; border-radius: 10px; text-decoration: none;
            border: 2px solid transparent; cursor: pointer;
            transition: background-color .15s, color .15s, border-color .15s;
        }
        .btn-primary { background: var(--cobalt); color: #fff; }
        .btn-primary:hover { background: var(--cobalt-dark); }
        .btn-ghost { border-color: var(--ink); color: var(--ink); background: transparent; }
        .btn-ghost:hover { background: var(--ink); color: #fff; }
        .btn-small { padding: 10px 16px; font-size: .95rem; border-radius: 8px; }

        /* ---------- Header ---------- */
        .site-header {
            position: sticky; top: 0; z-index: 50;
            background: rgba(232, 237, 240, .94);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
        }
        .nav { display: flex; align-items: center; justify-content: space-between; height: 68px; gap: 24px; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; font: 800 1.25rem var(--display); letter-spacing: -0.02em; }
        .brand-mark {
            width: 30px; height: 30px; border-radius: 8px; background: var(--ink); position: relative; flex: none;
        }
        .brand-mark::before, .brand-mark::after { content: ""; position: absolute; left: 7px; right: 7px; height: 3px; background: var(--mango); border-radius: 2px; }
        .brand-mark::before { top: 9px; }
        .brand-mark::after { top: 16px; right: 12px; background: #fff; }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a.link { text-decoration: none; padding: 8px 12px; border-radius: 8px; font-weight: 500; color: var(--ink-soft); }
        .nav-links a.link:hover { color: var(--ink); background: rgba(15, 42, 61, .07); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .lang { position: relative; }
        .lang summary {
            list-style: none; cursor: pointer; padding: 8px 12px; border-radius: 8px; font-weight: 500;
            color: var(--ink-soft); display: flex; align-items: center; gap: 6px;
        }
        .lang summary::-webkit-details-marker { display: none; }
        .lang summary:hover { background: rgba(15, 42, 61, .07); color: var(--ink); }
        .lang summary::after { content: ""; width: 6px; height: 6px; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(45deg) translateY(-2px); }
        .lang ul {
            position: absolute; right: 0; top: calc(100% + 6px); min-width: 170px;
            background: var(--slip); border: 1px solid var(--line); border-radius: 10px; padding: 6px;
            box-shadow: 0 12px 30px rgba(15, 42, 61, .14);
        }
        .lang li a { display: block; padding: 9px 12px; border-radius: 6px; text-decoration: none; }
        .lang li a:hover { background: var(--counter); }
        .lang li a[aria-current="true"] { font-weight: 600; color: var(--cobalt); }

        .menu-toggle { display: none; background: none; border: 2px solid var(--ink); border-radius: 8px; width: 44px; height: 44px; cursor: pointer; color: var(--ink); }
        .menu-toggle span, .menu-toggle span::before, .menu-toggle span::after {
            display: block; width: 18px; height: 2px; background: currentColor; margin: 0 auto; position: relative;
        }
        .menu-toggle span::before, .menu-toggle span::after { content: ""; position: absolute; left: 0; }
        .menu-toggle span::before { top: -6px; }
        .menu-toggle span::after { top: 6px; }

        /* ---------- Hero ---------- */
        .hero { padding: 72px 0 96px; overflow: hidden; }
        .hero-grid { display: grid; grid-template-columns: 1.15fr .85fr; gap: 56px; align-items: center; }
        .hero h1 { font-size: clamp(2.6rem, 6vw, 4.6rem); font-weight: 800; max-width: 12ch; }
        .hero .lede { margin-top: 24px; max-width: 46ch; font-size: 1.2rem; color: var(--ink-soft); }
        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 36px; }
        .hero-note { margin-top: 20px; font-size: .95rem; color: var(--ink-soft); }

        /* The till slip: the one memorable element */
        .printer { max-width: 380px; margin-left: auto; width: 100%; }
        .printer-slot {
            height: 16px; background: var(--ink); border-radius: 10px 10px 0 0; position: relative; z-index: 2;
        }
        .printer-slot::after { content: ""; position: absolute; left: 14px; right: 14px; bottom: 4px; height: 3px; background: #000; border-radius: 2px; opacity: .55; }
        .printer-clip { overflow: hidden; padding: 0 14px 28px; margin: 0 -14px; }
        .slip-wrap { filter: drop-shadow(0 10px 14px rgba(15, 42, 61, .18)); animation: print 1.8s cubic-bezier(.3, .7, .2, 1) .25s both; }
        .slip {
            background: var(--slip);
            padding: 26px 24px 34px;
            font-family: var(--mono); font-size: .86rem; line-height: 1.55; color: var(--ink);
            -webkit-mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 16px 100%;
                    mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 16px 100%;
        }
        .slip-title { font: 700 1.1rem var(--display); letter-spacing: -0.01em; text-align: center; }
        .slip-sub { text-align: center; color: var(--ink-soft); font-size: .78rem; margin-bottom: 14px; }
        .slip hr { border: 0; border-top: 2px dashed var(--line); margin: 12px 0; }
        .slip .row { display: flex; justify-content: space-between; gap: 12px; }
        .slip .total { font-weight: 500; font-size: 1.02rem; }
        .slip .tag { display: inline-block; background: var(--mango); color: var(--ink); padding: 1px 8px; border-radius: 4px; font-weight: 500; }
        .slip .ok { color: var(--basil); font-weight: 500; }
        @keyframes print { from { transform: translateY(-100%); } to { transform: translateY(0); } }

        /* ---------- Sections ---------- */
        section { padding: 88px 0; }
        .section-head { max-width: 60ch; margin-bottom: 48px; }
        .section-head h2 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; }
        .section-head p { margin-top: 16px; color: var(--ink-soft); }

        .band { background: var(--ink); color: #fff; }
        .band .section-head p { color: #B9C8D4; }
        .paper { background: var(--slip); }

        /* Features: rows, not a card grid */
        .feature {
            display: grid; grid-template-columns: 5fr 7fr; gap: 40px;
            padding: 36px 0; border-top: 2px solid var(--ink);
        }
        .feature:last-child { border-bottom: 2px solid var(--ink); }
        .feature h3 { font-size: 1.7rem; font-weight: 700; }
        .feature .why { margin-top: 12px; color: var(--ink-soft); max-width: 36ch; }
        .caps { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px 32px; }
        .caps li { padding-left: 22px; position: relative; }
        .caps li::before { content: ""; position: absolute; left: 0; top: .62em; width: 10px; height: 10px; border-radius: 3px; background: var(--cobalt); }
        .caps strong { font-weight: 600; }
        .caps span { display: block; color: var(--ink-soft); font-size: .97rem; }

        /* Roles table */
        .roles { width: 100%; border-collapse: collapse; }
        .roles th, .roles td { text-align: left; padding: 20px 16px 20px 0; vertical-align: top; border-top: 1px solid rgba(255, 255, 255, .22); }
        .roles tr:last-child td, .roles tr:last-child th { border-bottom: 1px solid rgba(255, 255, 255, .22); }
        .roles th { font: 700 1.25rem var(--display); width: 220px; color: var(--mango); }
        .roles td { color: #E6EEF4; }
        .roles td small { display: block; margin-top: 4px; color: #9FB3C3; font-size: .92rem; }

        /* Steps: a real sequence, so numbered */
        .steps { counter-reset: step; display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
        .steps li { counter-increment: step; border-top: 4px solid var(--ink); padding-top: 18px; }
        .steps li::before { content: counter(step); display: block; font: 800 2.2rem var(--display); color: var(--cobalt); margin-bottom: 6px; }
        .steps h3 { font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em; }
        .steps p { margin-top: 8px; font-size: .97rem; color: var(--ink-soft); }

        /* About */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
        .about-grid h2 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; }
        .about-copy p + p { margin-top: 16px; }
        .about-copy { color: var(--ink-soft); }
        .about-copy strong { color: var(--ink); }
        .stat-line { margin-top: 28px; padding: 20px 22px; background: var(--counter); border-left: 6px solid var(--mango); border-radius: 0 10px 10px 0; color: var(--ink); }

        /* Policy */
        .policy-grid { display: grid; grid-template-columns: 4fr 8fr; gap: 56px; align-items: start; }
        .policy-side { position: sticky; top: 100px; }
        .policy-side h2 { font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 700; }
        .policy-side p { margin-top: 14px; color: var(--ink-soft); }
        .policy-side .updated { margin-top: 18px; font-size: .92rem; }
        .policy details { border-top: 1px solid var(--line); padding: 4px 0; }
        .policy details:last-child { border-bottom: 1px solid var(--line); }
        .policy summary {
            cursor: pointer; list-style: none; padding: 18px 40px 18px 0; position: relative;
            font: 700 1.2rem var(--display); letter-spacing: -0.01em;
        }
        .policy summary::-webkit-details-marker { display: none; }
        .policy summary::after { content: "+"; position: absolute; right: 6px; top: 50%; transform: translateY(-50%); font: 500 1.6rem var(--body); color: var(--cobalt); }
        .policy details[open] summary::after { content: "–"; }
        .policy .body { padding: 0 0 20px; color: var(--ink-soft); }
        .policy .body p + p, .policy .body ul { margin-top: 12px; }
        .policy .body li { padding-left: 18px; position: relative; margin-top: 6px; }
        .policy .body li::before { content: ""; position: absolute; left: 0; top: .7em; width: 6px; height: 6px; background: var(--ink); border-radius: 50%; }

        /* Final CTA */
        .cta { background: var(--cobalt); color: #fff; text-align: left; }
        .cta-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 32px; }
        .cta h2 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; max-width: 18ch; }
        .cta .btn-primary { background: var(--mango); color: var(--ink); }
        .cta .btn-primary:hover { background: #FFC85A; }
        .cta .btn-ghost { border-color: #fff; color: #fff; }
        .cta .btn-ghost:hover { background: #fff; color: var(--cobalt); }
        .cta :focus-visible { outline-color: #fff; }
        .cta-actions { margin: 0; }
        .slip .row.stamp { margin-top: 10px; }
        footer h3.foot-lang { margin-top: 20px; }

        /* Footer */
        footer { background: var(--ink); color: #B9C8D4; padding: 56px 0 32px; font-size: .97rem; }
        .foot-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 40px; }
        footer .brand { color: #fff; margin-bottom: 12px; }
        footer h3 { font: 700 1rem var(--display); color: #fff; margin-bottom: 12px; letter-spacing: 0; }
        footer li + li { margin-top: 8px; }
        footer a { text-decoration: none; }
        footer a:hover { color: #fff; text-decoration: underline; }
        .foot-bottom { margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, .16); font-size: .9rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px; }

        /* ---------- Responsive ---------- */
        @media (max-width: 960px) {
            .hero-grid, .about-grid, .policy-grid, .feature { grid-template-columns: 1fr; }
            .hero { padding-top: 48px; }
            .printer { margin: 0 auto; }
            .policy-side { position: static; }
            .steps { grid-template-columns: 1fr 1fr; }
            .foot-grid { grid-template-columns: 1fr 1fr; }
            .menu-toggle { display: block; }
            .nav-links {
                display: none; position: absolute; left: 0; right: 0; top: 68px;
                flex-direction: column; align-items: stretch; gap: 0;
                background: var(--counter); border-bottom: 1px solid var(--line); padding: 8px 24px 16px;
            }
            .nav-links.open { display: flex; }
            .nav-links a.link { padding: 14px 4px; border-radius: 0; border-bottom: 1px solid var(--line); }
            .nav-actions .btn-ghost { display: none; }
        }
        @media (max-width: 640px) {
            section { padding: 64px 0; }
            .caps { grid-template-columns: 1fr; }
            .steps { grid-template-columns: 1fr; }
            .roles th, .roles td { display: block; width: auto; padding: 0; border: 0; }
            .roles tr { display: block; padding: 18px 0; border-top: 1px solid rgba(255, 255, 255, .22); }
            .roles tr:last-child { border-bottom: 1px solid rgba(255, 255, 255, .22); }
            .roles tr:last-child td, .roles tr:last-child th { border-bottom: 0; }
            .roles th { margin-bottom: 4px; }
            .foot-grid { grid-template-columns: 1fr; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .slip-wrap { animation: none; }
            * { transition: none !important; }
        }
        .brand-logo {
    display: block;
    width: auto;
    height: 42px;
    max-width: 160px;
    object-fit: contain;
}
.brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

/* ---------- Page Loading Overlay ---------- */
.page-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(232, 237, 240, 0.94);
    backdrop-filter: blur(5px);
    opacity: 1;
    visibility: visible;
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

.page-loader[hidden] {
    display: none !important;
}

.page-loader-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.page-loader-spinner {
    width: 66px;
    height: 66px;
    border: 5px solid #cbd5e1;
    border-top-color: var(--cobalt);
    border-right-color: var(--cobalt);
    border-radius: 50%;
    animation: mahwi-page-spin 0.75s linear infinite;
}

.page-loader-brand {
    font-family: var(--display);
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -0.02em;
}

.page-loader-message {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--ink-soft);
}

@keyframes mahwi-page-spin {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .page-loader-spinner {
        animation-duration: 2s;
    }

    .page-loader {
        transition: none;
    }
}

    </style>
</head>
<body>
    
{{-- Full-page loading indicator --}}
<div
    id="page-loader"
    class="page-loader"
    role="status"
    aria-live="polite"
    aria-label="Loading page"
    hidden
>
    <div class="page-loader-content">
        <div class="page-loader-spinner" aria-hidden="true"></div>

        <div class="page-loader-brand">
            {{ $appName }}
        </div>

        <p class="page-loader-message">
            Loading, please wait...
        </p>
    </div>
</div>

<a class="skip" href="#main">{{ __('Skip to content') }}</a>

{{-- ================= HEADER / PUBLIC NAV ================= --}}
<header class="site-header">
    <div class="wrap nav">
        <a href="{{ url('/') }}" class="brand" aria-label="{{ $appName }} home">
    <img
        src="{{ asset('images/MAHWILOGO.png') }}"
        alt="{{ $appName }} logo"
        class="brand-logo"
    >
    <span>{{ $appName }}</span>
</a>

        <nav class="nav-links" id="site-nav" aria-label="{{ __('Main') }}">
            <a class="link" href="#features">{{ __('Features') }}</a>
            <a class="link" href="#roles">{{ __('Who uses it') }}</a>
            <a class="link" href="#how">{{ __('How it works') }}</a>
            <a class="link" href="#about">{{ __('About') }}</a>
            <a class="link" href="#policy">{{ __('Policy') }}</a>
            @if (Route::has('contact'))
                <a class="link" href="{{ route('contact') }}">{{ __('Contact') }}</a>
            @endif
        </nav>

        <div class="nav-actions">
            @if (Route::has('language.switch'))
                <details class="lang">
                    <summary aria-label="{{ __('Change language') }}">{{ strtoupper($current) }}</summary>
                    <ul>
                        @foreach ($locales as $code => $label)
                            <li>
                                <a href="{{ route('language.switch', $code) }}"
                                   @if ($code === $current) aria-current="true" @endif>{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif

            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-small">{{ __('Open dashboard') }}</a>
            @else
                @if ($hasLogin)
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-small">{{ __('Log in') }}</a>
                @endif
                @if ($hasRegister)
                    <a href="{{ route('register') }}" class="btn btn-primary btn-small">{{ __('Register your shop') }}</a>
                @endif
            @endauth

            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="{{ __('Menu') }}">
                <span></span>
            </button>
        </div>
    </div>
</header>

<main id="main">

    {{-- ================= HERO ================= --}}
    <div class="hero">
        <div class="wrap hero-grid">
            <div>
                <h1>Sales, stock and money for every shop you run.</h1>
                <p class="lede">
                    Sell at the till, record what you buy and spend, and see profit and loss by day, week, month or year.
                    Your sellers, waiters, managers and accountants each get the screens they need and nothing more.
                </p>
                <div class="hero-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Open dashboard') }}</a>
                    @else
                        @if ($hasRegister)
                            <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Register your shop') }}</a>
                        @endif
                        @if ($hasLogin)
                            <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('Log in') }}</a>
                        @endif
                    @endauth
                </div>
                <p class="hero-note">New shops are reviewed by a system admin before they go live.</p>
            </div>

            <div class="printer" aria-hidden="true">
                <div class="printer-slot"></div>
                <div class="printer-clip">
                    <div class="slip-wrap">
                        <div class="slip">
                            <div class="slip-title">{{ $appName }}</div>
                            <div class="slip-sub">Order 0142 · Table 6</div>
                            <hr>
                            <div class="row"><span>3 × Maize flour 2kg</span><span>7,500</span></div>
                            <div class="row"><span>2 × Cooking oil 1L</span><span>6,400</span></div>
                            <div class="row"><span>1 × Sugar 5kg</span><span>8,200</span></div>
                            <div class="row"><span>4 × Soda 300ml</span><span>2,200</span></div>
                            <hr>
                            <div class="row total"><span>Total</span><span>24,300</span></div>
                            <div class="row"><span>Paid</span><span>Cash</span></div>
                            <hr>
                            <div class="row"><span>Taken by</span><span>Waiter</span></div>
                            <div class="row"><span>Approved by</span><span>Seller</span></div>
                            <div class="row stamp"><span class="tag">Stock updated</span><span class="ok">Done</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= FEATURES ================= --}}
    <section id="features" class="paper">
        <div class="wrap">
            <div class="section-head">
                <h2>Everything a shop does in a day, in one place.</h2>
                <p>From the first sale in the morning to the profit and loss report at night.</p>
            </div>

            <div class="feature">
                <div>
                    <h3>Sell</h3>
                    <p class="why">A fast till for the counter, and an order flow for shops that serve tables.</p>
                </div>
                <ul class="caps">
                    <li><strong>Point of sale</strong><span>Ring up items and take payment at the counter.</span></li>
                    <li><strong>Sales records</strong><span>Edit, print or export any sale.</span></li>
                    <li><strong>Waiter orders</strong><span>Waiters take orders; sellers approve or reject them.</span></li>
                    <li><strong>Bills and printouts</strong><span>Download a bill or print an order for the kitchen.</span></li>
                </ul>
            </div>

            <div class="feature">
                <div>
                    <h3>Stock</h3>
                    <p class="why">Know what you have, who you bought it from, and what it cost.</p>
                </div>
                <ul class="caps">
                    <li><strong>Products and categories</strong><span>Organise your catalogue the way you shelve it.</span></li>
                    <li><strong>QR codes</strong><span>Generate a QR code for any product.</span></li>
                    <li><strong>Purchases</strong><span>Record stock coming in and download a PDF.</span></li>
                    <li><strong>Suppliers</strong><span>Keep supplier details next to what you buy from them.</span></li>
                    <li><strong>Bulk import and export</strong><span>Upload a spreadsheet, preview the rows, then confirm.</span></li>
                    <li><strong>Product templates</strong><span>Start from a ready-made import file.</span></li>
                </ul>
            </div>

            <div class="feature">
                <div>
                    <h3>Money</h3>
                    <p class="why">Every shilling in and out, with reports your accountant can use.</p>
                </div>
                <ul class="caps">
                    <li><strong>Expenses</strong><span>Sort by category, attach receipts, switch status on or off.</span></li>
                    <li><strong>Other income</strong><span>Track income that does not come from sales.</span></li>
                    <li><strong>Daily to yearly reports</strong><span>Sales, purchases, expenses and other income by period.</span></li>
                    <li><strong>Profit and loss</strong><span>One report that shows where you stand.</span></li>
                    <li><strong>Export anywhere</strong><span>Download any report as PDF, Excel or CSV.</span></li>
                    <li><strong>Shop statistics</strong><span>A summary view for the shop owner.</span></li>
                </ul>
            </div>

            <div class="feature">
                <div>
                    <h3>People and shops</h3>
                    <p class="why">Add your team, serve your customers and run more than one shop.</p>
                </div>
                <ul class="caps">
                    <li><strong>Staff accounts</strong><span>Give each person a role and the right access.</span></li>
                    <li><strong>Customers</strong><span>Keep a customer list for repeat sales.</span></li>
                    <li><strong>Multiple shops</strong><span>Each shop keeps its own sales, stock and staff.</span></li>
                    <li><strong>Your language</strong><span>Switch the interface language at any time.</span></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ================= ROLES ================= --}}
    <section id="roles" class="band">
        <div class="wrap">
            <div class="section-head">
                <h2>Everyone sees the screens they need.</h2>
                <p>Access is set by role, so a waiter never ends up in the books and an accountant never ends up at the till.</p>
            </div>

            <table class="roles">
                <tbody>
                <tr>
                    <th scope="row">Shop admin</th>
                    <td>Runs the shop: products, categories, suppliers, customers, staff, expenses and statistics.
                        <small>Can edit and delete records others can only view.</small></td>
                </tr>
                <tr>
                    <th scope="row">Seller</th>
                    <td>Works the counter: sales, purchases, bulk imports and approving waiter orders.
                        <small>Can view products and expenses.</small></td>
                </tr>
                <tr>
                    <th scope="row">Waiter</th>
                    <td>Takes orders at the table from a dashboard built for that one job.</td>
                </tr>
                <tr>
                    <th scope="row">Manager</th>
                    <td>Oversees day-to-day work from a manager dashboard.</td>
                </tr>
                <tr>
                    <th scope="row">Accountant</th>
                    <td>Follows the money from a finance dashboard and the full set of reports.</td>
                </tr>
                <tr>
                    <th scope="row">System admin</th>
                    <td>Approves or rejects new shops, manages users and sets platform settings.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section id="how" class="paper">
        <div class="wrap">
            <div class="section-head">
                <h2>From sign-up to first report.</h2>
            </div>
            <ol class="steps">
                <li>
                    <h3>Register your shop</h3>
                    <p>Create an account and tell us about your shop.</p>
                </li>
                <li>
                    <h3>Wait for approval</h3>
                    <p>A system admin reviews your shop. You see its status while you wait.</p>
                </li>
                <li>
                    <h3>Set up</h3>
                    <p>Add products, suppliers, customers and your staff.</p>
                </li>
                <li>
                    <h3>Trade</h3>
                    <p>Sell, buy and record expenses as the day goes.</p>
                </li>
                <li>
                    <h3>Read the reports</h3>
                    <p>Check profit and loss, then export it as PDF, Excel or CSV.</p>
                </li>
            </ol>
        </div>
    </section>

    {{-- ================= ABOUT ================= --}}
    <section id="about">
        <div class="wrap about-grid">
            <h2>Built for the people who actually run shops.</h2>
            <div class="about-copy">
                <p>
                    <strong>{{ $appName }}</strong> started with a simple question: why does a small shop need three notebooks,
                    a calculator and a spreadsheet to know whether it made money today?
                </p>
                <p>
                    So we put selling, buying, spending and reporting in one app. Owners get a clear picture of every shop.
                    Staff get a short list of screens that match their job. Nobody has to learn accounting to use it.
                </p>
                <p>
                    It works for a single counter, a restaurant with waiters, or an owner with several locations,
                    because each shop keeps its own records under one login.
                </p>
                <div class="stat-line">
                    Questions before you sign up?
                    @if (Route::has('contact'))
                        <a href="{{ route('contact') }}"><strong>Contact us</strong></a>
                    @else
                        Get in touch with our team.
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ================= POLICY ================= --}}
    <section id="policy" class="paper">
        <div class="wrap policy-grid">
            <div class="policy-side">
                <h2>Policy</h2>
                <p>Plain-language summaries of how we handle your data and what we expect from users.</p>
                <p class="updated">Last updated {{ now()->format('j F Y') }}</p>
            </div>

            <div class="policy">
                <details open>
                    <summary>Privacy</summary>
                    <div class="body">
                        <p>We collect only what the app needs to work: your account details, your shop details, and the records you enter, such as sales, purchases, expenses, products, customers and staff.</p>
                        <ul>
                            <li>We do not sell your data.</li>
                            <li>Your shop's records are visible only to people you give access to, and to system admins who support the platform.</li>
                            <li>You can ask us to correct or delete your account data at any time.</li>
                        </ul>
                    </div>
                </details>

                <details>
                    <summary>Your data and security</summary>
                    <div class="body">
                        <p>Each shop's records are kept separate from other shops. Access is controlled by role, and every person signs in with their own account.</p>
                        <ul>
                            <li>Use a strong password and do not share it.</li>
                            <li>Remove staff accounts when someone leaves.</li>
                            <li>Exports (PDF, Excel, CSV) are copies of your data. Keep them safe.</li>
                        </ul>
                    </div>
                </details>

                <details>
                    <summary>Roles and access</summary>
                    <div class="body">
                        <p>Shop admins decide who works in their shop and what role each person has. A role limits what someone can see and change. Only shop admins can edit or delete most records.</p>
                    </div>
                </details>

                <details>
                    <summary>Shop approval</summary>
                    <div class="body">
                        <p>New shops are reviewed by a system admin before they can trade on the platform. We may reject or suspend a shop that gives false information or breaks these terms.</p>
                    </div>
                </details>

                <details>
                    <summary>Terms of use</summary>
                    <div class="body">
                        <ul>
                            <li>Use the app only for lawful business.</li>
                            <li>You are responsible for the accuracy of the records you enter.</li>
                            <li>Do not try to access another shop's data or break the app's security.</li>
                            <li>We may update the app and these terms. We will publish changes on this page.</li>
                        </ul>
                    </div>
                </details>

                <details>
                    <summary>Questions about policy</summary>
                    <div class="body">
                        <p>
                            @if (Route::has('contact'))
                                Send us a message on the <a href="{{ route('contact') }}">contact page</a>
                            @else
                                Contact us
                            @endif
                            and we will reply as soon as we can.
                        </p>
                    </div>
                </details>
            </div>
        </div>
    </section>

    {{-- ================= FINAL CTA ================= --}}
    <section class="cta">
        <div class="wrap cta-inner">
            <h2>Close the notebooks. Open your shop.</h2>
            <div class="hero-cta cta-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Open dashboard') }}</a>
                @else
                    @if ($hasRegister)
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Register your shop') }}</a>
                    @endif
                    @if ($hasLogin)
                        <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('Log in') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </section>
</main>

{{-- ================= FOOTER ================= --}}
<footer>
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <a href="{{ url('/') }}" class="brand" aria-label="{{ $appName }} home">
    <img
        src="{{ asset('images/MAHWILOGO.png') }}"
        alt="{{ $appName }} logo"
        class="brand-logo"
    >
    <span>{{ $appName }}</span>
</a>
                <p>Sales, stock and money for every shop you run.</p>
            </div>
            <div>
                <h3>Product</h3>
                <ul>
                    <li><a href="#features">{{ __('Features') }}</a></li>
                    <li><a href="#roles">{{ __('Who uses it') }}</a></li>
                    <li><a href="#how">{{ __('How it works') }}</a></li>
                </ul>
            </div>
            <div>
                <h3>Company</h3>
                <ul>
                    <li><a href="#about">{{ __('About') }}</a></li>
                    <li><a href="/privacy">{{ __('Privacy Policy') }}</a></li>
                    @if (Route::has('contact'))
                        <li><a href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h3>Account</h3>
                <ul>
                    @auth
                        <li><a href="{{ route('dashboard') }}">{{ __('Open dashboard') }}</a></li>
                    @else
                        @if ($hasLogin)<li><a href="{{ route('login') }}">{{ __('Log in') }}</a></li>@endif
                        @if ($hasRegister)<li><a href="{{ route('register') }}">{{ __('Register your shop') }}</a></li>@endif
                    @endauth
                </ul>
                @if (Route::has('language.switch'))
                    <h3 class="foot-lang">Language</h3>
                    <ul>
                        @foreach ($locales as $code => $label)
                            <li><a href="{{ route('language.switch', $code) }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="foot-bottom">
            <span>© {{ date('Y') }} {{ $appName }}. All rights reserved.</span>
            <a href="/privacy">{{ __('Privacy Policy') }}</a>
        </div>
    </div>
</footer>


<script nonce="{{ $cspNonce ?? '' }}">
    (function () {
        const loader = document.getElementById('page-loader');

        function showLoader() {
            if (loader) {
                loader.hidden = false;
            }
        }

        function hideLoader() {
            if (loader) {
                loader.hidden = true;
            }
        }

        // Mobile navigation menu
        const btn = document.querySelector('.menu-toggle');
        const nav = document.getElementById('site-nav');

        if (btn && nav) {
            btn.addEventListener('click', function () {
                const open = nav.classList.toggle('open');
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

            nav.addEventListener('click', function (event) {
                const link = event.target.closest('a');

                if (link) {
                    nav.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Show loader for normal same-origin page navigation.
        document.addEventListener('click', function (event) {
            const link = event.target.closest('a[href]');

            if (!link || event.defaultPrevented) return;

            // Ignore modified clicks, middle clicks and downloads.
            if (
                event.button !== 0 ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey ||
                link.hasAttribute('download') ||
                link.target === '_blank'
            ) {
                return;
            }

            if (link.hasAttribute('data-no-loader')) return;

            const url = new URL(link.href, window.location.href);

            // Ignore external URLs and JavaScript links.
            if (
                url.origin !== window.location.origin ||
                url.protocol === 'javascript:'
            ) {
                return;
            }

            // In-page anchors should scroll without a loading overlay.
            if (
                url.pathname === window.location.pathname &&
                url.search === window.location.search &&
                url.hash
            ) {
                return;
            }

            // Avoid showing the loader for the current URL.
            if (url.href === window.location.href) return;

            showLoader();
        });

        // Show loader for normal form submissions.
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (
                form.hasAttribute('data-no-loader') ||
                form.target === '_blank'
            ) {
                return;
            }

            showLoader();
        });

        // Hide it when this page is loaded or restored from browser history.
        window.addEventListener('pageshow', hideLoader);

        // Allow other scripts to control the loader when needed.
        window.mahwiShowLoader = showLoader;
        window.mahwiHideLoader = hideLoader;
    })();
</script>
</body>
</html>

</body>
</html>