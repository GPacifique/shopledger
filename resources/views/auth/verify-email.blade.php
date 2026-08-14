<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg" href="{{ asset('images/MAHWILOGO.png') }}">
    <title>{{ config('app.name', 'Mahwi') }} - Verify your email</title>

    <meta name="description" content="Mahwi - Rwanda's leading multi-shop management system for inventory, sales, purchases, and staff management. Verify your email to continue.">
    <meta name="robots" content="noindex, nofollow">
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
      a{ color:inherit; }

      .wrap{
        min-height:100vh;
        display:grid;
        grid-template-columns: 1.35fr 1fr;
      }
      @media (max-width: 920px){
        .wrap{ grid-template-columns: 1fr; }
      }

      /* ===================== LEFT: SHOWCASE ===================== */
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

      .lang-switch{ position:relative; z-index:5; flex-shrink:0; }
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

      .slides{ position:relative; aspect-ratio: 16 / 10; background:#0F1826; }
      .slide{
        position:absolute; inset:0;
        opacity:0; transform: translateX(14px);
        transition: opacity .55s ease, transform .55s ease;
      }
      .slide.is-active{ opacity:1; transform:translateX(0); z-index:2; }
      .slide img{ width:100%; height:100%; object-fit:cover; object-position: top; display:block; }

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

      /* ===================== RIGHT: VERIFY-EMAIL TICKET ===================== */
      .login-pane{
        background: var(--paper);
        background-image: radial-gradient(rgba(22,35,58,0.035) 1px, transparent 1px);
        background-size: 3px 3px;
        display:flex; align-items:center; justify-content:center;
        padding: 56px 40px;
        position:relative;
      }

      .ticket{ width:100%; max-width: 380px; position:relative; }

      .ticket-stub{
        background:#fff;
        border-radius: 10px;
        padding: 34px 32px 30px;
        box-shadow: 0 18px 50px -22px rgba(22,35,58,0.35);
        position:relative;
        text-align:center;
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

      .envelope{
        width:64px; height:64px; margin:0 auto 20px;
        border-radius:50%;
        background: rgba(31,122,77,0.08);
        border:1px solid rgba(31,122,77,0.25);
        display:flex; align-items:center; justify-content:center;
      }
      .envelope svg{ width:28px; height:28px; }

      .ticket-eyebrow{
        font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.16em; text-transform:uppercase;
        color: var(--green); margin:0 0 8px;
      }
      .ticket h2{
        font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:23px; margin:0 0 10px; letter-spacing:-0.01em;
      }
      .ticket-sub{ color:var(--ink-soft); font-size:14px; margin:0 0 26px; line-height:1.6; }
      .ticket-sub strong{ color:var(--ink); }

      .status-msg{
        background: rgba(31,122,77,0.08); border:1px solid rgba(31,122,77,0.3);
        color: var(--green); border-radius:7px; padding:11px 13px;
        font-size:13px; margin-bottom:20px; text-align:left; line-height:1.5;
      }

      .btn-submit{
        width:100%; padding:13px; border:none; border-radius:7px; cursor:pointer;
        background: var(--ink); color: var(--paper);
        font-family:'Space Grotesk',sans-serif; font-weight:600; font-size:15px;
        transition: background .15s ease, transform .1s ease;
      }
      .btn-submit:hover{ background: var(--ink-soft); }
      .btn-submit:active{ transform: translateY(1px); }
      .btn-submit:focus-visible{ outline:2px solid var(--green); outline-offset:3px; }

      .btn-ghost{
        width:100%; padding:12px; margin-top:10px;
        border:1.5px solid var(--rule); border-radius:7px; cursor:pointer;
        background: transparent; color: var(--ink-soft);
        font-family:'IBM Plex Sans',sans-serif; font-weight:500; font-size:14px;
        transition: border-color .15s ease, color .15s ease;
      }
      .btn-ghost:hover{ border-color: var(--ink-soft); color: var(--ink); }
      .btn-ghost:focus-visible{ outline:2px solid var(--green); outline-offset:3px; }

      .stamp{
        position:absolute; top:-14px; right:14px;
        transform: rotate(-8deg);
        border:2px solid var(--amber); color:var(--amber);
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
      .ticket-foot a{ color:var(--green); font-weight:600; text-decoration:none; background:none; border:none; cursor:pointer; font-family:inherit; font-size:inherit; padding:0; }
      .ticket-foot a:hover{ text-decoration:underline; }

      @media (prefers-reduced-motion: reduce){ .slide{ transition:none; } }
    </style>
</head>
<body>

<div class="wrap">

  <!-- ===================== LEFT: SHOWCASE ===================== -->
  <div class="showcase">

    <div class="showcase-topbar">
      <div class="brand-row">
        <div class="brand-mark">
          <img src="{{ asset('images/MAHWILOGO.png') }}" alt="{{ config('app.name', 'MahWi') }} logo">
        </div>
        <div>
          <div class="brand-name">{{ config('app.name', 'MahWi') }}</div>
          <div class="brand-tag">Shop&#8209;Manager</div>
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
        <p class="showcase-eyebrow">{{ __('One last step') }}</p>
        <h1>{{ __('Almost in.') }}</h1>
        <p>{{ __("Confirm it's really you, and we'll take you straight to the dashboard your team opens every morning.") }}</p>
      </div>

      <div class="frame">
        <div class="frame-bar">
          <span class="frame-dot" style="background:#A23A2E99;"></span>
          <span class="frame-dot" style="background:#C6862B99;"></span>
          <span class="frame-dot" style="background:#1F7A4D99;"></span>
          <span class="frame-url" id="frame-url">mahwi.sharptechlearners.com/dashboard</span>
        </div>

        <div class="slides" id="slides" role="region" aria-roledescription="carousel" aria-label="{{ __('Product screenshots') }}">
          <div class="slide is-active" data-url="mahwi.sharptechlearners.com/dashboard" aria-hidden="false">
            <img src="{{ asset('images/MAHWI OVERVIEW.png') }}" alt="{{ __('MahWi dashboard overview showing sales, profit and stock at a glance') }}">
          </div>
          <div class="slide" data-url="mahwi.sharptechlearners.com/inventory" aria-hidden="true">
            <img src="{{ asset('images/mahwi-inventory.png') }}" alt="{{ __('MahWi inventory screen showing stock levels and low-stock alerts') }}">
          </div>
          <div class="slide" data-url="mahwi.sharptechlearners.com/sales" aria-hidden="true">
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

  <!-- ===================== RIGHT: VERIFY EMAIL ===================== -->
  <div class="login-pane">
    <div class="ticket">
      <div class="ticket-stub">
        <div class="stamp">{{ __('Pending') }}<br>{{ __('review') }}</div>

        <div class="envelope">
          <svg viewBox="0 0 24 24" fill="none" stroke="#1F7A4D" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
            <path d="M3 7l9 6 9-6"></path>
          </svg>
        </div>

        <p class="ticket-eyebrow">{{ __('Confirm your email') }}</p>
        <h2>{{ __('Check your inbox') }}</h2>
        <p class="ticket-sub">
          {{ __("We've sent a verification link to your email address. Click it to activate your account before signing in.") }}
        </p>

        @if (session('status') == 'verification-link-sent')
          <div class="status-msg">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
          </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn-submit">{{ __('Resend verification email') }}</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn-ghost">{{ __('Log out') }}</button>
        </form>
      </div>

      <div class="ticket-tear" aria-hidden="true"></div>
      <div class="ticket-foot">
        {{ __('Wrong email address?') }}
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
          @csrf
          <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Sign out and try again') }}</a>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
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

  document.addEventListener('click', function(e){
    var details = document.querySelector('.lang-switch');
    if (details && details.open && !details.contains(e.target)) {
      details.open = false;
    }
  });
</script>

</body>
</html>F