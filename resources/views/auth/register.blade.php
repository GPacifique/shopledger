<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg" href="{{ asset('images/favicon.ico') }}">
    <title>{{ config('app.name', 'Mahwi') }} - Create your account</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Mahwi - Rwanda's leading Business management system for Incomes inventory, sales, purchases, and staff management. Create your free account.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#16233A">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|ibm-plex-sans:400,500,600|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      :root{
        --paper:#F5F0E4;
        --paper-dim:#EAE2D0;
        --ink:#16233A;
        --ink-soft:#3C4A63;
        --rule:#C9BEA3;
        --green:#1F7A4D;
        --amber:#C6862B;
        --stamp:#A23A2E;
      }
      *{ box-sizing:border-box; }
      html,body{ height:100%; }
      body{
        margin:0;
        font-family:'IBM Plex Sans', ui-sans-serif, sans-serif;
        background:var(--ink);
        color:var(--ink);
      }
      .font-display{ font-family:'Space Grotesk', ui-sans-serif, sans-serif; }
      .font-mono{ font-family:'IBM Plex Mono', ui-monospace, monospace; }

      a{ color:inherit; }

      .wrap{
        min-height:100vh;
        display:grid;
        grid-template-columns: 1.35fr 1fr;
      }
      @media (max-width: 920px){
        .wrap{ grid-template-columns: 1fr; }
      }

      /* ===================== LEFT: SCREEN SHOWCASE ===================== */
      .showcase{
        position:relative;
        background:
          radial-gradient(1200px 600px at 20% -10%, rgba(31,122,77,0.16), transparent 60%),
          radial-gradient(900px 500px at 100% 110%, rgba(198,134,43,0.14), transparent 55%),
          var(--ink);
        overflow:hidden;
        display:flex;
        flex-direction:column;
        padding: 36px 64px 64px;
        min-height: 480px;
      }
      @media (max-width: 920px){
        .showcase{ padding: 24px 28px 32px; min-height: auto; }
      }

      .showcase-topbar{
        display:flex; align-items:flex-start; justify-content:space-between; gap:16px;
        margin-bottom: 28px;
      }
      @media (max-width: 920px){ .showcase-topbar{ margin-bottom:20px; } }

      .showcase-body{
        flex:1;
        display:flex;
        flex-direction:column;
        justify-content:center;
        min-height:0;
      }

      .brand-row{
        display:flex; align-items:center; gap:12px;
        color:var(--paper);
      }
      .brand-mark{
        width:44px; height:44px; border-radius:9px;
        background:var(--paper);
        display:flex; align-items:center; justify-content:center;
        flex-shrink:0;
        padding:5px;
      }
      .brand-mark img{
        width:100%; height:100%;
        object-fit:contain;
        display:block;
      }
      .brand-name{ font-weight:700; font-size:18px; letter-spacing:-0.01em; }
      .brand-tag{ font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.14em; text-transform:uppercase; color:rgba(245,240,228,0.55); }

      .lang-switch{
        position:relative; z-index:5; flex-shrink:0;
      }
      .lang-switch summary{
        list-style:none; cursor:pointer;
        display:inline-flex; align-items:center; gap:6px;
        padding:8px 12px; border-radius:7px;
        border:1px solid rgba(245,240,228,0.18);
        color: var(--paper);
        font-family:'IBM Plex Mono',monospace; font-size:11px; text-transform:uppercase; letter-spacing:.06em;
      }
      .lang-switch summary::-webkit-details-marker{ display:none; }
      .lang-switch[open] summary{ background: rgba(245,240,228,0.08); }
      .lang-menu{
        position:absolute; right:0; margin-top:6px; width:170px;
        background:#fff; border-radius:8px; overflow:hidden;
        box-shadow: 0 18px 40px -18px rgba(0,0,0,0.5);
      }
      .lang-menu a{
        display:flex; align-items:center; gap:8px;
        padding:10px 14px; font-size:13.5px; color:var(--ink);
        font-family:'IBM Plex Mono',monospace;
      }
      .lang-menu a:hover{ background: var(--paper-dim); }
      .lang-menu a.is-active{ background: var(--paper-dim); font-weight:600; }

      .showcase-copy{
        max-width: 460px;
        margin-bottom: 40px;
        color: var(--paper);
      }
      .showcase-eyebrow{
        font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.18em; text-transform:uppercase;
        color: #7fd6ab; margin: 0 0 14px;
      }
      .showcase-copy h1{
        font-family:'Space Grotesk',sans-serif;
        font-weight:700; font-size: clamp(26px, 3vw, 34px);
        line-height:1.15; letter-spacing:-0.01em; margin:0 0 12px;
      }
      .showcase-copy p{
        color: rgba(245,240,228,0.68); font-size:15px; line-height:1.55; margin:0;
      }

      .frame{
        position:relative;
        border-radius: 12px;
        background:#0F1826;
        border: 1px solid rgba(245,240,228,0.10);
        box-shadow: 0 30px 70px -20px rgba(0,0,0,0.6);
        overflow:hidden;
        max-width: 640px;
      }
      .frame-bar{
        display:flex; align-items:center; gap:8px;
        padding: 11px 14px;
        background: rgba(245,240,228,0.04);
        border-bottom: 1px solid rgba(245,240,228,0.08);
      }
      .frame-dot{ width:8px; height:8px; border-radius:50%; }
      .frame-url{
        margin-left:8px;
        font-family:'IBM Plex Mono',monospace; font-size:10.5px;
        color: rgba(245,240,228,0.45);
      }

      .slides{
        position:relative;
        aspect-ratio: 16 / 10;
        background:#0F1826;
      }
      .slide{
        position:absolute; inset:0;
        opacity:0; transform: translateX(14px);
        transition: opacity .55s ease, transform .55s ease;
      }
      .slide.is-active{ opacity:1; transform:translateX(0); z-index:2; }
      .slide img{
        width:100%; height:100%; object-fit:cover; object-position: top;
        display:block;
      }

      .dots{
        position:absolute; bottom:14px; left:0; right:0;
        display:flex; justify-content:center; gap:7px; z-index:3;
      }
      .dot{
        width:20px; height:4px; border-radius:2px; border:none; cursor:pointer;
        background: rgba(245,240,228,0.35);
        transition: background .25s ease, width .25s ease;
        padding:0;
      }
      .dot.is-active{ background: var(--paper); width:28px; }
      .dot:focus-visible{ outline:2px solid #7fd6ab; outline-offset:2px; }

      .showcase-foot{
        margin-top:20px; display:flex; gap:22px; flex-wrap:wrap;
        font-family:'IBM Plex Mono',monospace; font-size:11px; color: rgba(245,240,228,0.45);
      }
      .showcase-foot span{ display:flex; align-items:center; gap:6px; }
      .showcase-foot .sw{ width:6px;height:6px;border-radius:50%; background:#7fd6ab; }

      /* Sign-up guide (left side) */
      .guide{
        max-width: 460px;
        margin-bottom: 36px;
        color: var(--paper);
      }
      .guide-title{
        font-family:'Space Grotesk',sans-serif; font-weight:600; font-size:16px;
        margin:0 0 14px;
      }
      .guide-steps{
        list-style:none; margin:0 0 26px; padding:0;
        display:grid; gap:14px;
      }
      .guide-steps li{
        display:grid; grid-template-columns: 28px 1fr; gap:12px; align-items:start;
      }
      .guide-num{
        width:28px; height:28px; border-radius:50%;
        border:1.5px solid rgba(127,214,171,0.55); color:#7fd6ab;
        display:flex; align-items:center; justify-content:center;
        font-family:'IBM Plex Mono',monospace; font-size:12px; font-weight:600;
        line-height:1; padding:0;
      }
      .guide-steps strong{
        display:block; font-size:14.5px; font-weight:600; margin-bottom:2px;
      }
      .guide-steps div > span{
        display:block; font-size:13.5px; line-height:1.5; color: rgba(245,240,228,0.66);
      }
      .guide-ready{
        list-style:none; margin:0; padding:0;
        display:grid; gap:9px;
      }
      .guide-ready li{
        display:grid; grid-template-columns: 16px 1fr; gap:10px; align-items:start;
        font-size:13.5px; line-height:1.5; color: rgba(245,240,228,0.78);
      }
      .guide-ready li::before{
        content:""; width:7px; height:7px; border-radius:50%; margin-top:7px; margin-left:4px;
        background:#7fd6ab;
      }
      .guide-ready em{ font-style:normal; color: rgba(245,240,228,0.5); }
      @media (max-width: 920px){
        .guide{ display:none; }
      }

      /* ===================== RIGHT: REGISTER TICKET ===================== */
      .login-pane{
        background: var(--paper);
        background-image: radial-gradient(rgba(22,35,58,0.035) 1px, transparent 1px);
        background-size: 3px 3px;
        display:flex; align-items:center; justify-content:center;
        padding: 48px 40px;
        position:relative;
      }

      .ticket{
        width:100%; max-width: 400px;
        position:relative;
      }

      .ticket-stub{
        background:#fff;
        border-radius: 10px;
        padding: 34px 32px 30px;
        box-shadow: 0 18px 50px -22px rgba(22,35,58,0.35);
        position:relative;
      }
      .ticket-stub::before,
      .ticket-stub::after{
        content:"";
        position:absolute; top:50%; transform:translateY(-50%);
        width:22px; height:22px; border-radius:50%;
        background: var(--paper);
      }
      .ticket-stub::before{ left:-11px; }
      .ticket-stub::after{ right:-11px; }

      .ticket-eyebrow{
        font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.16em; text-transform:uppercase;
        color: var(--green); margin:0 0 8px;
      }
      .ticket h2{
        font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:25px; margin:0 0 6px; letter-spacing:-0.01em;
      }
      .ticket-sub{ color:var(--ink-soft); font-size:14px; margin:0 0 22px; line-height:1.5; }

      .alert-error{
        background: rgba(162,58,46,0.08); border:1px solid rgba(162,58,46,0.3);
        color: var(--stamp); border-radius:7px; padding:11px 13px;
        font-size:13px; margin-bottom:18px; line-height:1.5;
      }
      .alert-error ul{ margin:0; padding-left:18px; }
      .status-msg{
        background: rgba(31,122,77,0.08); border:1px solid rgba(31,122,77,0.3);
        color: var(--green); border-radius:7px; padding:11px 13px;
        font-size:13px; margin-bottom:18px;
      }


      .field{ margin-bottom:16px; }
      .field-row{
        display:grid; grid-template-columns: 1fr 1fr; gap:12px;
      }
      @media (max-width: 400px){
        .field-row{ grid-template-columns: 1fr; }
      }
      .field label{
        display:block; font-family:'IBM Plex Mono',monospace; font-size:10.5px; text-transform:uppercase; letter-spacing:.08em;
        color: var(--ink-soft); margin-bottom:6px;
      }
      .field label .opt{ text-transform:none; letter-spacing:0; color:#9b937d; }
      .field input[type="text"],
      .field input[type="email"],
      .field input[type="tel"],
      .field input[type="password"],
      .field textarea{
        width:100%; padding:11px 13px; font-size:14.5px; font-family:'IBM Plex Sans',sans-serif;
        border:1.5px solid var(--rule); border-radius:7px; background: var(--paper);
        color:var(--ink); transition: border-color .15s ease, background .15s ease;
      }
      .field textarea{ resize: vertical; min-height: 76px; line-height:1.45; }
      .field input.has-error,
      .field textarea.has-error{ border-color: var(--stamp); }
      .field input::placeholder,
      .field textarea::placeholder{ color:#9b937d; }
      .field input:focus,
      .field textarea:focus{
        outline:none; border-color: var(--green); background:#fff;
      }
      .field input[type="file"]{
        width:100%; font-size:13px; font-family:'IBM Plex Sans',sans-serif; color: var(--ink-soft);
        padding:8px; border:1.5px dashed var(--rule); border-radius:7px; background: var(--paper);
      }
      .field input[type="file"]::file-selector-button{
        margin-right:10px; padding:6px 12px; border:none; border-radius:5px; cursor:pointer;
        background: var(--ink); color: var(--paper); font-family:'IBM Plex Mono',monospace; font-size:11px;
      }
      .field-error{ color: var(--stamp); font-size:12px; margin-top:5px; }
      .field-hint{ color: var(--ink-soft); font-size:11.5px; margin-top:5px; }

      .field-pass-wrap{ position:relative; }
      .toggle-pass{
        position:absolute; right:10px; top:50%; transform:translateY(-50%);
        background:none; border:none; cursor:pointer;
        font-family:'IBM Plex Mono',monospace; font-size:10.5px; text-transform:uppercase; letter-spacing:.06em;
        color: var(--ink-soft); padding:4px 6px;
      }
      .toggle-pass:hover{ color:var(--ink); }
      .toggle-pass:focus-visible{ outline:2px solid var(--green); outline-offset:2px; border-radius:4px; }

      .terms{
        display:flex; align-items:flex-start; gap:8px;
        font-size:13px; color:var(--ink-soft); line-height:1.5;
        margin: 4px 0 22px;
      }
      .terms input{ width:14px; height:14px; margin-top:3px; accent-color: var(--green); flex-shrink:0; }
      .terms a{ color: var(--green); font-weight:500; text-decoration:none; }
      .terms a:hover{ text-decoration:underline; }

      .btn-submit{
        width:100%; padding:13px; border:none; border-radius:7px; cursor:pointer;
        background: var(--ink); color: var(--paper);
        font-family:'Space Grotesk',sans-serif; font-weight:600; font-size:15px;
        transition: background .15s ease, transform .1s ease;
      }
      .btn-submit:hover{ background: var(--ink-soft); }
      .btn-submit:active{ transform: translateY(1px); }
      .btn-submit:focus-visible{ outline:2px solid var(--green); outline-offset:3px; }

      .stamp{
        position:absolute; top:-14px; right:14px;
        transform: rotate(-8deg);
        border:2px solid var(--green); color:var(--green);
        border-radius:50%; width:58px; height:58px;
        display:flex; align-items:center; justify-content:center;
        font-family:'IBM Plex Mono',monospace; font-size:8.5px; font-weight:600; text-transform:uppercase;
        text-align:center; line-height:1.15; background: var(--paper);
      }

      .ticket-tear{
        height:14px;
        background-image: radial-gradient(circle, var(--paper) 5.5px, transparent 5.6px);
        background-size: 15px 15px;
        background-position: -4px -8px;
        background-repeat: repeat-x;
      }
      .ticket-foot{
        background:#fff;
        border-radius: 0 0 10px 10px;
        padding: 14px 32px 18px;
        text-align:center;
        font-size:13.5px; color:var(--ink-soft);
        box-shadow: 0 18px 50px -22px rgba(22,35,58,0.35);
      }
      .ticket-foot a{ color:var(--green); font-weight:600; text-decoration:none; }
      .ticket-foot a:hover{ text-decoration:underline; }

      @media (prefers-reduced-motion: reduce){
        .slide{ transition:none; }
      }
    </style>
</head>
<body>

<div class="wrap">

  <!-- ===================== LEFT: SHOWCASE ===================== -->
  <div class="showcase">

    <div class="showcase-topbar">
      <div class="brand-row">
        <div class="brand-mark">
          <img src="{{ asset('images/favicon.ico') }}" alt="{{ config('app.name', 'MahWi') }} logo">
        </div>
        <div>
          <div class="brand-name">{{ config('app.name', 'MahWi') }}</div>
          <div class="brand-tag">Business&#8209;Manager</div>
        </div>
      </div>

      <details class="lang-switch">
        <summary>
          @php
            $flags = ['en' => '🇬🇧', 'fr' => '🇫🇷', 'rw' => '🇷🇼', 'sw' => '🇹🇿'];
          @endphp
          <span>{{ $flags[app()->getLocale()] ?? '🇬🇧' }}</span>
          <span>{{ strtoupper(app()->getLocale()) }}</span>
        </summary>
        <div class="lang-menu">
          @foreach(['en' => ['English', '🇬🇧'], 'fr' => ['Français', '🇫🇷'], 'rw' => ['Kinyarwanda', '🇷🇼'], 'sw' => ['Kiswahili', '🇹🇿']] as $code => $lang)
            <a href="{{ route('language.switch', $code) }}" class="{{ app()->getLocale() === $code ? 'is-active' : '' }}">
              <span>{{ $lang[1] }}</span>{{ $lang[0] }}
            </a>
          @endforeach
        </div>
      </details>
    </div>

    <div class="showcase-body">
      <div class="showcase-copy">
        <p class="showcase-eyebrow">{{ __('Get set up in minutes') }}</p>
        <h1>{{ __('Bring every Business online.') }}</h1>
        <p>{{ __('Create your account and start tracking stock, sales, and profit across all your shops from day one.') }}</p>
      </div>

      <div class="guide">
        <h2 class="guide-title">{{ __('How sign-up works') }}</h2>
        <ol class="guide-steps">
          <li>
            <span class="guide-num">1</span>
            <div>
              <strong>{{ __('Fill in the form') }}</strong>
              <span>{{ __('Add your details and your business information. It takes about two minutes.') }}</span>
            </div>
          </li>
          <li>
            <span class="guide-num">2</span>
            <div>
              <strong>{{ __('Verify your email') }}</strong>
              <span>{{ __('We send a link to the email you enter. Open it to confirm your account before you can sign in to the dashboard.') }}</span>
            </div>
          </li>
          <li>
            <span class="guide-num">3</span>
            <div>
              <strong>{{ __('We review your shop') }}</strong>
              <span>{{ __('Your shop is checked and approved before it goes live. You will see its status once you are signed in.') }}</span>
            </div>
          </li>
          <li>
            <span class="guide-num">4</span>
            <div>
              <strong>{{ __('Start managing your business') }}</strong>
              <span>{{ __('Add products, record sales and watch stock and profit from one place.') }}</span>
            </div>
          </li>
        </ol>

        <h2 class="guide-title">{{ __('Have these ready') }}</h2>
        <ul class="guide-ready">
          <li><span>{{ __('A working email address you can open now, for the verification link.') }}</span></li>
          <li><span>{{ __('A phone number with exactly 10 digits, for example 07XXXXXXXX.') }}</span></li>
          <li><span>{{ __('Your business name, type and address.') }}</span></li>
          <li><span>{{ __('Your registration and TIN numbers') }} <em>({{ __('optional, we can generate a registration number for you') }})</em></span></li>
          <li><span>{{ __('Your shop logo') }} <em>({{ __('optional, PNG or JPG up to 2 MB') }})</em></span></li>
        </ul>
      </div>

      <div class="frame">
        <div class="frame-bar">
          <span class="frame-dot" style="background:#A23A2E99;"></span>
          <span class="frame-dot" style="background:#C6862B99;"></span>
          <span class="frame-dot" style="background:#1F7A4D99;"></span>
          <span class="frame-url" id="frame-url">mahwi.com/dashboard</span>
        </div>

        <div class="slides" id="slides" role="region" aria-roledescription="carousel" aria-label="{{ __('Product screenshots') }}">

          <div class="slide is-active" data-url="mahwi.com/dashboard" aria-hidden="false">
            <img src="{{ asset('images/MAHWI OVERVIEW.png') }}" alt="{{ __('MahWi dashboard overview showing sales, profit and stock at a glance') }}">
          </div>

          <div class="slide" data-url="mahwi.com/inventory" aria-hidden="true">
            <img src="{{ asset('images/mahwi-inventory.png') }}" alt="{{ __('MahWi inventory screen showing stock levels and low-stock alerts') }}">
          </div>

          <div class="slide" data-url="mahwi.com/sales" aria-hidden="true">
            <img src="{{ asset('images/mahwi-sales.png') }}" alt="{{ __('MahWi sales screen showing a new sale being recorded with profit calculated') }}">
          </div>

        </div>

        <div class="dots" id="dots">
          <button class="dot is-active" data-index="0" aria-label="{{ __('Show overview slide') }}" aria-current="true"></button>
          <button class="dot" data-index="1" aria-label="{{ __('Show inventory slide') }}" aria-current="false"></button>
          <button class="dot" data-index="2" aria-label="{{ __('Show sales slide') }}" aria-current="false"></button>
        </div>
      </div>

      <div class="showcase-foot">
        <span><i class="sw"></i>{{ __('100+ shops live') }}</span>
        <span><i class="sw"></i>{{ __('50K+ sales logged') }}</span>
        <span><i class="sw"></i>{{ __('24/7 support') }}</span>
      </div>
    </div>
  </div>

  <!-- ===================== RIGHT: REGISTER ===================== -->
  <div class="login-pane">
    <div class="ticket">
      <div class="ticket-stub">
        <div class="stamp">{{ __('New') }}<br>{{ __('member') }}</div>
        <p class="ticket-eyebrow">{{ __('Create account') }}</p>
        <h2>{{ __('Join MahWi') }}</h2>
        <p class="ticket-sub">{{ __('Set up your workspace and start managing your shops today.') }}</p>

        @if (session('status'))
          <div class="status-msg">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert-error">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
          @csrf


          <div class="field">
            <label for="name">{{ __('Full name') }}</label>
            <input id="name" name="name" type="text" class="{{ $errors->has('name') ? 'has-error' : '' }}"
                   placeholder="{{ __('Jane Uwase') }}" value="{{ old('name') }}"
                   autocomplete="name" required autofocus>
            @error('name')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="{{ $errors->has('email') ? 'has-error' : '' }}"
                   placeholder="you@example.com" value="{{ old('email') }}"
                   autocomplete="email" required>
            @error('email')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label for="phone">{{ __('Phone number') }}</label>
            <input id="phone" name="phone" type="tel" class="{{ $errors->has('phone') ? 'has-error' : '' }}"
                   placeholder="07XXXXXXXX" value="{{ old('phone') }}"
                   inputmode="numeric" maxlength="10" minlength="10" pattern="[0-9]{10}"
                   autocomplete="tel-national" required>
            <p class="field-hint">{{ __('10 digits, numbers only.') }}</p>
            @error('phone')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field-row">
            <div class="field">
              <label for="password">{{ __('Password') }}</label>
              <div class="field-pass-wrap">
                <input id="password" name="password" type="password" class="{{ $errors->has('password') ? 'has-error' : '' }}"
                       placeholder="••••••••" autocomplete="new-password" required>
                <button type="button" class="toggle-pass" id="toggle-pass" aria-pressed="false">{{ __('Show') }}</button>
              </div>
              @error('password')
                <p class="field-error">{{ $message }}</p>
              @enderror
            </div>

            <div class="field">
              <label for="password_confirmation">{{ __('Confirm') }}</label>
              <div class="field-pass-wrap">
                <input id="password_confirmation" name="password_confirmation" type="password"
                       placeholder="••••••••" autocomplete="new-password" required>
                <button type="button" class="toggle-pass" id="toggle-pass-confirm" aria-pressed="false">{{ __('Show') }}</button>
              </div>
            </div>
          </div>
          <p class="field-hint" style="margin-bottom:16px;">{{ __('At least 8 characters.') }}</p>


          <div class="field">
            <label for="business_name">{{ __('Business name') }}</label>
            <input id="business_name" name="business_name" type="text" class="{{ $errors->has('business_name') ? 'has-error' : '' }}"
                   placeholder="{{ __('Kigali General Store') }}" value="{{ old('business_name') }}"
                   autocomplete="organization" required>
            @error('business_name')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label for="business_type">{{ __('Business type') }}</label>
            <input id="business_type" name="business_type" type="text" class="{{ $errors->has('business_type') ? 'has-error' : '' }}"
                   placeholder="{{ __('e.g. Retail, Restaurant, Pharmacy') }}" value="{{ old('business_type') }}" required>
            @error('business_type')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field-row">
            <div class="field">
              <label for="registration_number">{{ __('Registration no.') }} <span class="opt">({{ __('optional') }})</span></label>
              <input id="registration_number" name="registration_number" type="text" class="{{ $errors->has('registration_number') ? 'has-error' : '' }}"
                     placeholder="{{ __('Auto-generated if blank') }}" value="{{ old('registration_number') }}">
              @error('registration_number')
                <p class="field-error">{{ $message }}</p>
              @enderror
            </div>

            <div class="field">
              <label for="tin_number">{{ __('TIN number') }} <span class="opt">({{ __('optional') }})</span></label>
              <input id="tin_number" name="tin_number" type="text" class="{{ $errors->has('tin_number') ? 'has-error' : '' }}"
                     placeholder="{{ __('Tax ID') }}" value="{{ old('tin_number') }}">
              @error('tin_number')
                <p class="field-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="country">{{ __('Country') }}</label>
              <input id="country" name="country" type="text" class="{{ $errors->has('country') ? 'has-error' : '' }}"
                     placeholder="{{ __('Country') }}" value="{{ old('country', 'Rwanda') }}"
                     autocomplete="country-name" required>
              @error('country')
                <p class="field-error">{{ $message }}</p>
              @enderror
            </div>

            <div class="field">
              <label for="city">{{ __('City') }}</label>
              <input id="city" name="city" type="text" class="{{ $errors->has('city') ? 'has-error' : '' }}"
                     placeholder="{{ __('Kigali') }}" value="{{ old('city') }}"
                     autocomplete="address-level2" required>
              @error('city')
                <p class="field-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="field">
            <label for="address">{{ __('Address') }}</label>
            <textarea id="address" name="address" rows="3" class="{{ $errors->has('address') ? 'has-error' : '' }}"
                      placeholder="{{ __('Street, sector, landmark') }}"
                      autocomplete="street-address" required>{{ old('address') }}</textarea>
            @error('address')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label for="logo">{{ __('Shop logo') }} <span class="opt">({{ __('optional') }})</span></label>
            <input id="logo" name="logo" type="file" accept="image/png,image/jpeg">
            <p class="field-hint">{{ __('PNG or JPG, up to 2 MB.') }}</p>
            @error('logo')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="terms" style="margin-top:16px;">
            <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
            <label for="terms">
              {{ __('I agree to the') }}
              <a href="{{ Route::has('terms') ? route('terms') : '#' }}">{{ __('Terms of Service') }}</a>
{{ __('and') }}
<a href="{{ Route::has('privacy') ? route('privacy') : '#' }}">{{ __('Privacy Policy') }}</a>.
            </label>
          </div>
          @error('terms')
            <p class="field-error" style="margin-top:-16px; margin-bottom:16px;">{{ $message }}</p>
          @enderror

          <button type="submit" class="btn-submit">{{ __('Create account') }}</button>
        </form>
      </div>

      <div class="ticket-tear" aria-hidden="true"></div>
      <div class="ticket-foot">
        {{ __('Already have an account?') }}
        @if (Route::has('login'))
          <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
        @endif
      </div>
    </div>
  </div>

</div>

<script nonce="{{ Vite::cspNonce() }}">
  // ---- Auto-sliding screenshot carousel ----
  (function(){
    var slides = Array.prototype.slice.call(document.querySelectorAll('.slide'));
    var dots = Array.prototype.slice.call(document.querySelectorAll('.dot'));
    var urlEl = document.getElementById('frame-url');
    var frame = document.querySelector('.frame');
    var current = 0;
    var timer = null;
    var INTERVAL = 4200;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function goTo(index){
      slides[current].classList.remove('is-active');
      slides[current].setAttribute('aria-hidden','true');
      dots[current].classList.remove('is-active');
      dots[current].setAttribute('aria-current','false');

      current = (index + slides.length) % slides.length;

      slides[current].classList.add('is-active');
      slides[current].setAttribute('aria-hidden','false');
      dots[current].classList.add('is-active');
      dots[current].setAttribute('aria-current','true');
      urlEl.textContent = slides[current].getAttribute('data-url');
    }

    function next(){ goTo(current + 1); }

    function start(){
      if (reduceMotion) return;
      stop();
      timer = setInterval(next, INTERVAL);
    }
    function stop(){
      if (timer) { clearInterval(timer); timer = null; }
    }

    dots.forEach(function(dot){
      dot.addEventListener('click', function(){
        goTo(parseInt(dot.getAttribute('data-index'), 10));
        start();
      });
    });

    frame.addEventListener('mouseenter', stop);
    frame.addEventListener('mouseleave', start);
    frame.addEventListener('focusin', stop);
    frame.addEventListener('focusout', start);

    start();
  })();

  // ---- Show/hide password (both fields) ----
  (function(){
    function wireToggle(btnId, inputId){
      var btn = document.getElementById(btnId);
      var input = document.getElementById(inputId);
      if (!btn || !input) return;
      btn.addEventListener('click', function(){
        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.textContent = isPassword ? '{{ __('Hide') }}' : '{{ __('Show') }}';
        btn.setAttribute('aria-pressed', String(isPassword));
      });
    }
    wireToggle('toggle-pass', 'password');
    wireToggle('toggle-pass-confirm', 'password_confirmation');
  })();

  // ---- Phone: digits only, max 10 ----
  (function(){
    var phone = document.getElementById('phone');
    if (!phone) return;
    phone.addEventListener('input', function(){
      phone.value = phone.value.replace(/\D/g, '').slice(0, 10);
    });
  })();

  // ---- Logo: warn early if the file is over 2 MB ----
  (function(){
    var logo = document.getElementById('logo');
    if (!logo) return;
    logo.addEventListener('change', function(){
      if (logo.files[0] && logo.files[0].size > 2 * 1024 * 1024) {
        alert('{{ __('The logo must be 2 MB or smaller.') }}');
        logo.value = '';
      }
    });
  })();

  // ---- Close language dropdown on outside click ----
  document.addEventListener('click', function(e){
    var details = document.querySelector('.lang-switch');
    if (details && details.open && !details.contains(e.target)) {
      details.open = false;
    }
  });
</script>

</body>
</html>