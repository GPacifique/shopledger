{{--
    resources/views/auth/login.blade.php
    Light / dark aware, every visible word goes through __() (lang/en|fr|rw|sw.json).
    Encoding note: no flag emoji and no raw non-ASCII characters are used in this file.
    Accented names are written as \u{...} escapes, so a wrong editor encoding can never corrupt them again.
--}}
@php
    $appName = config('app.name', 'MahWi');
    $current = app()->getLocale();

    // Native language names. Escapes keep them safe even if the file is re-saved in another encoding.
    $names = [
        'en' => 'English',
        'fr' => "Fran\u{00E7}ais",
        'rw' => 'Kinyarwanda',
        'sw' => 'Kiswahili',
    ];
    $codes = array_keys(config('app.available_locales', $names));
    $host  = request()->getHost();

    $slides = [
        ['url' => 'dashboard', 'img' => 'MAHWI OVERVIEW.png',   'alt' => __('Dashboard overview showing sales, profit and stock at a glance'),                  'label' => __('Show overview slide')],
        ['url' => 'inventory', 'img' => 'mahwi-inventory.png',    'alt' => __('Inventory screen showing stock levels and low stock alerts'),                      'label' => __('Show inventory slide')],
        ['url' => 'sales',     'img' => 'mahwi-sales.png',        'alt' => __('Sales screen showing a new sale being recorded with profit calculated'),           'label' => __('Show sales slide')],
    ];

    $seoTitle       = $appName . ' - ' . __('Sign in to your account');
    $seoDescription = __('Sign in to your MahWi dashboard to manage sales, stock, purchases, expenses and staff for your shops.');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $current) }}" data-theme="light">
<head>
    @include('partials.seo', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription])
    @stack('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">

    {{-- Applies the saved or system theme before first paint (no flash) --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (function () {
            var t = null;
            try { t = localStorage.getItem('mahwi-theme'); } catch (e) {}
            if (t !== 'light' && t !== 'dark') {
                t = window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', t);
            var m = document.querySelector('meta[name="theme-color"]');
            if (m) m.setAttribute('content', t === 'dark' ? '#0A111C' : '#16233A');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|ibm-plex-sans:400,500,600|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style nonce="{{ Vite::cspNonce() }}">
      :root{
        --paper:#F5F0E4;
        --paper-dim:#EAE2D0;
        --ink:#16233A;
        --ink-soft:#3C4A63;
        --rule:#C9BEA3;
        --green:#1F7A4D;
        --stamp:#A23A2E;
        --card:#FFFFFF;
        --field-bg:#F5F0E4;
        --placeholder:#9B937D;
        --dots:rgba(22,35,58,0.035);
        --btn-bg:#16233A;
        --btn-bg-hover:#3C4A63;
        --btn-text:#F5F0E4;
        --err-bg:rgba(162,58,46,0.08);
        --err-line:rgba(162,58,46,0.3);
        --ok-bg:rgba(31,122,77,0.08);
        --ok-line:rgba(31,122,77,0.3);
        --shadow:0 18px 50px -22px rgba(22,35,58,0.35);
        --show-bg:#16233A;
        --on-dark:#F5F0E4;
        color-scheme:light;
      }
      :root[data-theme="dark"]{
        --paper:#0B1420;
        --paper-dim:#1B2A40;
        --ink:#E9EEF5;
        --ink-soft:#A4B2C6;
        --rule:#2C3E57;
        --green:#43C58B;
        --stamp:#EE8B7F;
        --card:#121F33;
        --field-bg:#0B1420;
        --placeholder:#6F7F96;
        --dots:rgba(255,255,255,0.03);
        --btn-bg:#1F7A4D;
        --btn-bg-hover:#26935D;
        --btn-text:#FFFFFF;
        --err-bg:rgba(238,139,127,0.12);
        --err-line:rgba(238,139,127,0.4);
        --ok-bg:rgba(67,197,139,0.12);
        --ok-line:rgba(67,197,139,0.4);
        --shadow:0 18px 50px -22px rgba(0,0,0,0.7);
        --show-bg:#0A111C;
        color-scheme:dark;
      }
      *{ box-sizing:border-box; }
      html,body{ height:100%; }
      body{
        margin:0;
        font-family:'IBM Plex Sans', ui-sans-serif, sans-serif;
        background:var(--paper);
        color:var(--ink);
        transition:background-color .2s, color .2s;
      }
      .font-display{ font-family:'Space Grotesk', ui-sans-serif, sans-serif; }
      .font-mono{ font-family:'IBM Plex Mono', ui-monospace, monospace; }
      a{ color:inherit; }
      :focus-visible{ outline:2px solid var(--green); outline-offset:2px; border-radius:4px; }

      .skip{
        position:absolute; left:12px; top:-60px; z-index:100;
        background:var(--on-dark); color:#16233A; padding:10px 14px; border-radius:6px; text-decoration:none;
        font-size:14px;
      }
      .skip:focus{ top:12px; }

      .wrap{ min-height:100vh; display:grid; grid-template-columns:1.35fr 1fr; }
      @media (max-width:920px){ .wrap{ grid-template-columns:1fr; } }

      /* ===================== LEFT: SCREEN SHOWCASE (dark in both themes) ===================== */
      .showcase{
        position:relative;
        background:
          radial-gradient(1200px 600px at 20% -10%, rgba(31,122,77,0.16), transparent 60%),
          radial-gradient(900px 500px at 100% 110%, rgba(198,134,43,0.14), transparent 55%),
          var(--show-bg);
        overflow:hidden;
        display:flex; flex-direction:column;
        padding:36px 64px 64px;
        min-height:480px;
        color:var(--on-dark);
      }
      @media (max-width:920px){ .showcase{ padding:24px 28px 32px; min-height:auto; } }

      .showcase-topbar{ display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:28px; }
      @media (max-width:920px){ .showcase-topbar{ margin-bottom:20px; } }
      .topbar-actions{ display:flex; align-items:center; gap:8px; flex-shrink:0; }

      .showcase-body{ flex:1; display:flex; flex-direction:column; justify-content:center; min-height:0; }

      .brand-row{ display:flex; align-items:center; gap:12px; color:var(--on-dark); text-decoration:none; }
      .brand-mark{
        width:44px; height:44px; border-radius:10px; background:var(--on-dark);
        display:flex; align-items:center; justify-content:center; flex-shrink:0; padding:4px;
      }
      .brand-mark img{ width:100%; height:100%; object-fit:contain; display:block; border-radius:7px; }
      .brand-name{ font-weight:700; font-size:18px; letter-spacing:-0.01em; font-family:'Space Grotesk',sans-serif; }
      .brand-tag{ font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.14em; text-transform:uppercase; color:rgba(245,240,228,0.55); }

      .icon-btn, .lang-switch summary{
        display:inline-flex; align-items:center; gap:7px; height:36px; padding:0 11px; border-radius:7px;
        border:1px solid rgba(245,240,228,0.18); background:transparent; color:var(--on-dark);
        font-family:'IBM Plex Mono',monospace; font-size:11px; text-transform:uppercase; letter-spacing:.06em;
        cursor:pointer; transition:background-color .15s;
      }
      .icon-btn{ width:36px; padding:0; justify-content:center; }
      .icon-btn:hover, .lang-switch summary:hover{ background:rgba(245,240,228,0.10); }
      .icon-btn svg, .lang-switch summary svg{ width:16px; height:16px; flex:none; }
      .icon-btn .i-sun{ display:none; }
      :root[data-theme="dark"] .icon-btn .i-sun{ display:block; }
      :root[data-theme="dark"] .icon-btn .i-moon{ display:none; }

      .lang-switch{ position:relative; z-index:5; }
      .lang-switch summary{ list-style:none; }
      .lang-switch summary::-webkit-details-marker{ display:none; }
      .lang-switch[open] summary{ background:rgba(245,240,228,0.10); }
      .lang-menu{
        position:absolute; right:0; margin-top:6px; width:190px;
        background:var(--card); color:var(--ink); border:1px solid var(--rule);
        border-radius:9px; overflow:hidden; box-shadow:0 18px 40px -18px rgba(0,0,0,0.5);
      }
      .lang-menu a{
        display:flex; align-items:center; gap:10px; padding:10px 14px; font-size:14px; color:var(--ink); text-decoration:none;
      }
      .lang-menu a:hover{ background:var(--paper-dim); }
      .lang-menu a.is-active{ background:var(--paper-dim); font-weight:600; }
      .lang-code{
        font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.06em;
        min-width:28px; text-align:center; padding:3px 5px; border-radius:5px;
        border:1px solid var(--rule); color:var(--ink-soft);
      }
      .lang-menu a.is-active .lang-code{ border-color:var(--green); color:var(--green); }

      .showcase-copy{ max-width:460px; margin-bottom:40px; }
      .showcase-eyebrow{
        font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.18em; text-transform:uppercase;
        color:#7fd6ab; margin:0 0 14px;
      }
      .showcase-copy h1{
        font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:clamp(26px,3vw,34px);
        line-height:1.15; letter-spacing:-0.01em; margin:0 0 12px;
      }
      .showcase-copy p{ color:rgba(245,240,228,0.68); font-size:15px; line-height:1.55; margin:0; }

      .frame{
        position:relative; border-radius:12px; background:#0F1826;
        border:1px solid rgba(245,240,228,0.10); box-shadow:0 30px 70px -20px rgba(0,0,0,0.6);
        overflow:hidden; max-width:640px;
      }
      .frame-bar{ display:flex; align-items:center; gap:8px; padding:11px 14px; background:rgba(245,240,228,0.04); border-bottom:1px solid rgba(245,240,228,0.08); }
      .frame-dot{ width:8px; height:8px; border-radius:50%; }
      .frame-url{ margin-left:8px; font-family:'IBM Plex Mono',monospace; font-size:10.5px; color:rgba(245,240,228,0.45); }

      .slides{ position:relative; aspect-ratio:16 / 10; background:#0F1826; }
      .slide{ position:absolute; inset:0; opacity:0; transform:translateX(14px); transition:opacity .55s ease, transform .55s ease; }
      .slide.is-active{ opacity:1; transform:translateX(0); z-index:2; }
      .slide img{ width:100%; height:100%; object-fit:cover; object-position:top; display:block; }

      .dots{ position:absolute; bottom:14px; left:0; right:0; display:flex; justify-content:center; gap:7px; z-index:3; }
      .dot{ width:20px; height:4px; border-radius:2px; border:none; cursor:pointer; background:rgba(245,240,228,0.35); transition:background .25s ease, width .25s ease; padding:0; }
      .dot.is-active{ background:var(--on-dark); width:28px; }
      .dot:focus-visible{ outline:2px solid #7fd6ab; outline-offset:2px; }

      .showcase-foot{ margin-top:20px; display:flex; gap:22px; flex-wrap:wrap; font-family:'IBM Plex Mono',monospace; font-size:11px; color:rgba(245,240,228,0.55); }
      .showcase-foot span{ display:flex; align-items:center; gap:6px; }
      .showcase-foot .sw{ width:6px; height:6px; border-radius:50%; background:#7fd6ab; }

      /* ===================== RIGHT: LOGIN TICKET ===================== */
      .login-pane{
        background:var(--paper);
        background-image:radial-gradient(var(--dots) 1px, transparent 1px);
        background-size:3px 3px;
        display:flex; align-items:center; justify-content:center;
        padding:56px 40px; position:relative;
      }
      .ticket{ width:100%; max-width:380px; position:relative; }
      .ticket-stub{
        background:var(--card); border-radius:10px; padding:34px 32px 30px;
        box-shadow:var(--shadow); position:relative; border:1px solid transparent;
      }
      :root[data-theme="dark"] .ticket-stub, :root[data-theme="dark"] .ticket-foot{ border-color:var(--rule); }
      .ticket-stub::before, .ticket-stub::after{
        content:""; position:absolute; top:50%; transform:translateY(-50%);
        width:22px; height:22px; border-radius:50%; background:var(--paper);
      }
      .ticket-stub::before{ left:-11px; }
      .ticket-stub::after{ right:-11px; }

      .ticket-eyebrow{ font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.16em; text-transform:uppercase; color:var(--green); margin:0 0 8px; }
      .ticket h2{ font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:25px; margin:0 0 6px; letter-spacing:-0.01em; }
      .ticket-sub{ color:var(--ink-soft); font-size:14px; margin:0 0 22px; line-height:1.5; }

      .alert-error{ background:var(--err-bg); border:1px solid var(--err-line); color:var(--stamp); border-radius:7px; padding:11px 13px; font-size:13px; margin-bottom:18px; line-height:1.5; }
      .alert-error ul{ margin:0; padding-left:18px; }
      .status-msg{ background:var(--ok-bg); border:1px solid var(--ok-line); color:var(--green); border-radius:7px; padding:11px 13px; font-size:13px; margin-bottom:18px; }

      .field{ margin-bottom:16px; }
      .field label{ display:block; font-family:'IBM Plex Mono',monospace; font-size:10.5px; text-transform:uppercase; letter-spacing:.08em; color:var(--ink-soft); margin-bottom:6px; }
      .field input{
        width:100%; padding:11px 13px; font-size:14.5px; font-family:'IBM Plex Sans',sans-serif;
        border:1.5px solid var(--rule); border-radius:7px; background:var(--field-bg); color:var(--ink);
        transition:border-color .15s ease, background .15s ease;
      }
      .field input.has-error{ border-color:var(--stamp); }
      .field input::placeholder{ color:var(--placeholder); }
      .field input:focus{ outline:none; border-color:var(--green); background:var(--card); }
      .field-error{ color:var(--stamp); font-size:12px; margin-top:5px; }

      .field-pass-wrap{ position:relative; }
      .field-pass-wrap input{ padding-right:68px; }
      .toggle-pass{
        position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer;
        font-family:'IBM Plex Mono',monospace; font-size:10.5px; text-transform:uppercase; letter-spacing:.06em; color:var(--ink-soft); padding:4px 6px;
      }
      .toggle-pass:hover{ color:var(--ink); }

      .row-between{ display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin:4px 0 22px; }
      .remember{ display:flex; align-items:center; gap:7px; font-size:13px; color:var(--ink-soft); }
      .remember input{ width:14px; height:14px; accent-color:var(--green); }
      .forgot{ font-size:13px; color:var(--green); text-decoration:none; font-weight:500; }
      .forgot:hover{ text-decoration:underline; }

      .btn-submit{
        width:100%; padding:13px; border:none; border-radius:7px; cursor:pointer;
        background:var(--btn-bg); color:var(--btn-text);
        font-family:'Space Grotesk',sans-serif; font-weight:600; font-size:15px;
        transition:background .15s ease, transform .1s ease, opacity .15s ease;
      }
      .btn-submit:hover{ background:var(--btn-bg-hover); }
      .btn-submit:active{ transform:translateY(1px); }
      .btn-submit[disabled]{ opacity:.7; cursor:progress; }

      .stamp{
        position:absolute; top:-16px; right:12px; transform:rotate(-8deg);
        border:2px solid var(--stamp); color:var(--stamp); border-radius:50%;
        width:64px; height:64px; padding:6px;
        display:flex; align-items:center; justify-content:center;
        font-family:'IBM Plex Mono',monospace; font-size:8.5px; font-weight:600; text-transform:uppercase;
        text-align:center; line-height:1.15; background:var(--paper); overflow-wrap:anywhere;
      }

      .ticket-tear{
        height:14px;
        background-image:radial-gradient(circle, var(--paper) 5.5px, transparent 5.6px);
        background-size:15px 15px; background-position:-4px -8px; background-repeat:repeat-x;
      }
      .ticket-foot{
        background:var(--card); border-radius:0 0 10px 10px; padding:14px 32px 18px; text-align:center;
        font-size:13.5px; color:var(--ink-soft); box-shadow:var(--shadow); border:1px solid transparent;
      }
      .ticket-foot a{ color:var(--green); font-weight:600; text-decoration:none; }
      .ticket-foot a:hover{ text-decoration:underline; }

      @media (prefers-reduced-motion:reduce){
        .slide{ transition:none; }
        *{ transition:none !important; }
      }
    </style>
</head>
<body>

<a class="skip" href="#login-form">{{ __('Skip to the sign in form') }}</a>

<div class="wrap">

  <!-- ===================== LEFT: SHOWCASE ===================== -->
  <div class="showcase">

    <div class="showcase-topbar">
      <a href="{{ url('/') }}" class="brand-row" aria-label="{{ __(':app home', ['app' => $appName]) }}">
        <div class="brand-mark">
          <img src="{{ asset('icons/icon-192.png') }}" alt="" width="36" height="36">
        </div>
        <div>
          <div class="brand-name">{{ $appName }}</div>
          <div class="brand-tag">{{ __('Shop manager') }}</div>
        </div>
      </a>

      <div class="topbar-actions">
        <button type="button" id="theme-toggle" class="icon-btn"
                data-to-dark="{{ __('Switch to dark mode') }}"
                data-to-light="{{ __('Switch to light mode') }}"
                aria-label="{{ __('Switch to dark mode') }}">
          <svg class="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
          <svg class="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>

        <details class="lang-switch">
          <summary aria-label="{{ __('Change language') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/></svg>
            <span>{{ strtoupper($current) }}</span>
          </summary>
          <div class="lang-menu">
            @foreach ($codes as $code)
              <a href="{{ route('language.switch', $code) }}"
                 class="{{ $current === $code ? 'is-active' : '' }}"
                 @if ($current === $code) aria-current="true" @endif
                 lang="{{ $code }}">
                <span class="lang-code">{{ strtoupper($code) }}</span>{{ $names[$code] ?? strtoupper($code) }}
              </a>
            @endforeach
          </div>
        </details>
      </div>
    </div>

    <div class="showcase-body">
      <div class="showcase-copy">
        <p class="showcase-eyebrow">{{ __('What you will see inside') }}</p>
        <h1>{{ __('Every business, on one screen.') }}</h1>
        <p>{{ __('A quick look at the dashboard your team opens every morning: stock, sales and profit, always current.') }}</p>
      </div>

      <div class="frame">
        <div class="frame-bar">
          <span class="frame-dot" style="background:#A23A2E99;"></span>
          <span class="frame-dot" style="background:#C6862B99;"></span>
          <span class="frame-dot" style="background:#1F7A4D99;"></span>
          <span class="frame-url" id="frame-url">{{ $host }}/{{ $slides[0]['url'] }}</span>
        </div>

        <div class="slides" id="slides" role="region" aria-roledescription="carousel" aria-label="{{ __('Product screenshots') }}">
          @foreach ($slides as $i => $slide)
            <div class="slide {{ $i === 0 ? 'is-active' : '' }}" data-url="{{ $host }}/{{ $slide['url'] }}" aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
              <img src="{{ asset('images/' . rawurlencode($slide['img'])) }}" alt="{{ $slide['alt'] }}" @if ($i > 0) loading="lazy" @endif>
            </div>
          @endforeach
        </div>

        <div class="dots" id="dots">
          @foreach ($slides as $i => $slide)
            <button type="button" class="dot {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}"
                    aria-label="{{ $slide['label'] }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}"></button>
          @endforeach
        </div>
      </div>

      <div class="showcase-foot">
        <span><i class="sw"></i>{{ __('100+ shops live') }}</span>
        <span><i class="sw"></i>{{ __('50K+ sales logged') }}</span>
        <span><i class="sw"></i>{{ __('24/7 support') }}</span>
      </div>
    </div>
  </div>

  <!-- ===================== RIGHT: LOGIN ===================== -->
  <main class="login-pane">
    <div class="ticket">
      <div class="ticket-stub">
        <div class="stamp" aria-hidden="true">{{ __('Access pass') }}</div>
        <p class="ticket-eyebrow">{{ __('Account access') }}</p>
        <h2>{{ __('Welcome back') }}</h2>
        <p class="ticket-sub">{{ __('Sign in to see how your shops are doing today.') }}</p>

        @if (session('status'))
          <div class="status-msg" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert-error" role="alert">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
          @csrf

          <div class="field">
            <label for="email">{{ __('Email or phone') }}</label>
            <input id="email" name="email" type="text" class="{{ $errors->has('email') ? 'has-error' : '' }}"
                   placeholder="you@example.com" value="{{ old('email') }}"
                   autocomplete="username" required autofocus>
            @error('email')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <div class="field-pass-wrap">
              <input id="password" name="password" type="password" class="{{ $errors->has('password') ? 'has-error' : '' }}"
                     placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password" required>
              <button type="button" class="toggle-pass" id="toggle-pass" aria-pressed="false" aria-controls="password"
                      data-show="{{ __('Show') }}" data-hide="{{ __('Hide') }}">{{ __('Show') }}</button>
            </div>
            @error('password')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="row-between">
            <label class="remember">
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              {{ __('Keep me signed in') }}
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="forgot">{{ __('Forgot password?') }}</a>
            @endif
          </div>

          <button type="submit" class="btn-submit" id="submit-btn" data-busy="{{ __('Signing in...') }}">{{ __('Sign in') }}</button>
        </form>
      </div>

      <div class="ticket-tear" aria-hidden="true"></div>
      <div class="ticket-foot">
        {{ __('New to :app?', ['app' => $appName]) }}
        @if (Route::has('register'))
          <a href="{{ route('register') }}">{{ __('Sign up') }}</a>
        @endif
      </div>
    </div>
  </main>

</div>

<script nonce="{{ Vite::cspNonce() }}">
  (function () {
    var root = document.documentElement;

    /* ---- Theme toggle ---- */
    var toggle = document.getElementById('theme-toggle');
    function applyTheme(t) {
      root.setAttribute('data-theme', t);
      var meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', t === 'dark' ? '#0A111C' : '#16233A');
      if (toggle) {
        toggle.setAttribute('aria-label', t === 'dark' ? toggle.dataset.toLight : toggle.dataset.toDark);
        toggle.setAttribute('aria-pressed', t === 'dark' ? 'true' : 'false');
      }
    }
    applyTheme(root.getAttribute('data-theme') || 'light');
    if (toggle) {
      toggle.addEventListener('click', function () {
        var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        try { localStorage.setItem('mahwi-theme', next); } catch (e) {}
        applyTheme(next);
      });
    }
    if (window.matchMedia) {
      var mq = matchMedia('(prefers-color-scheme: dark)');
      if (mq.addEventListener) mq.addEventListener('change', function (e) {
        var saved = null;
        try { saved = localStorage.getItem('mahwi-theme'); } catch (err) {}
        if (!saved) applyTheme(e.matches ? 'dark' : 'light');
      });
    }

    /* ---- Auto-sliding screenshot carousel ---- */
    var slides = Array.prototype.slice.call(document.querySelectorAll('.slide'));
    var dots = Array.prototype.slice.call(document.querySelectorAll('.dot'));
    var urlEl = document.getElementById('frame-url');
    var frame = document.querySelector('.frame');
    var current = 0, timer = null, INTERVAL = 4200;
    var reduceMotion = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

    function goTo(index) {
      slides[current].classList.remove('is-active');
      slides[current].setAttribute('aria-hidden', 'true');
      dots[current].classList.remove('is-active');
      dots[current].setAttribute('aria-current', 'false');
      current = (index + slides.length) % slides.length;
      slides[current].classList.add('is-active');
      slides[current].setAttribute('aria-hidden', 'false');
      dots[current].classList.add('is-active');
      dots[current].setAttribute('aria-current', 'true');
      urlEl.textContent = slides[current].getAttribute('data-url');
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }
    function start() { if (reduceMotion || slides.length < 2) return; stop(); timer = setInterval(function () { goTo(current + 1); }, INTERVAL); }

    dots.forEach(function (dot) {
      dot.addEventListener('click', function () { goTo(parseInt(dot.getAttribute('data-index'), 10)); start(); });
    });
    if (frame) {
      frame.addEventListener('mouseenter', stop);
      frame.addEventListener('mouseleave', start);
      frame.addEventListener('focusin', stop);
      frame.addEventListener('focusout', start);
    }
    start();

    /* ---- Show / hide password ---- */
    var passBtn = document.getElementById('toggle-pass');
    var passInput = document.getElementById('password');
    if (passBtn && passInput) {
      passBtn.addEventListener('click', function () {
        var hidden = passInput.type === 'password';
        passInput.type = hidden ? 'text' : 'password';
        passBtn.textContent = hidden ? passBtn.dataset.hide : passBtn.dataset.show;
        passBtn.setAttribute('aria-pressed', hidden ? 'true' : 'false');
      });
    }

    /* ---- Submit feedback (prevents double clicks) ---- */
    var form = document.getElementById('login-form');
    var submitBtn = document.getElementById('submit-btn');
    if (form && submitBtn) {
      form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.textContent = submitBtn.dataset.busy;
      });
      window.addEventListener('pageshow', function (e) {
        if (e.persisted) { submitBtn.disabled = false; submitBtn.textContent = form.dataset.label || submitBtn.textContent; }
      });
    }

    /* ---- Close language dropdown on outside click / Escape ---- */
    var details = document.querySelector('.lang-switch');
    if (details) {
      document.addEventListener('click', function (e) { if (details.open && !details.contains(e.target)) details.open = false; });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') details.open = false; });
    }
  })();
</script>

</body>
</html>