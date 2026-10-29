{{--
    resources/views/welcome.blade.php
    Public landing page. Guest-safe routes only: login, register, contact, language.switch, dashboard (when signed in).
    Self-contained CSS, no Vite or Tailwind build needed.
    Light / dark aware, every visible word goes through __() (lang/fr.json, lang/rw.json).
--}}
@php
    $appName     = config('app.name', 'Shop Manager');
    $locales     = config('app.available_locales', [
        'en' => 'English',
        'fr' => 'Français',
        'rw' => 'Kinyarwanda',
        'sw' => 'Kiswahili',
    ]);
    $current     = app()->getLocale();
    $hasLogin    = Route::has('login');
    $hasRegister = Route::has('register');
    $hasContact  = Route::has('contact');

    // Change this date only when the policy text really changes.
    $policyUpdated = \Illuminate\Support\Carbon::create(2026, 10, 20)->locale($current)->translatedFormat('j F Y');

    // SEO values handed to partials.seo
    $seoTitle       = $appName . ': ' . __('sales, stock and money for every shop you run');
    $seoDescription = __('Point of sale, purchases, expenses, products and reports for one shop or many. Sellers, waiters, managers and accountants each get the screens they need.');

    $features = [
        [
            'title' => __('Sell'),
            'why'   => __('A fast till for the counter, and an order flow for shops that serve tables.'),
            'items' => [
                [__('Point of sale'),       __('Ring up items and take payment at the counter.')],
                [__('Sales records'),       __('Edit, print or export any sale.')],
                [__('Waiter orders'),       __('Waiters take orders; sellers approve or reject them.')],
                [__('Bills and printouts'), __('Download a bill or print an order for the kitchen.')],
            ],
        ],
        [
            'title' => __('Stock'),
            'why'   => __('Know what you have, who you bought it from, and what it cost.'),
            'items' => [
                [__('Products and categories'), __('Organise your catalogue the way you shelve it.')],
                [__('QR codes'),                __('Generate a QR code for any product.')],
                [__('Purchases'),               __('Record stock coming in and download a PDF.')],
                [__('Suppliers'),               __('Keep supplier details next to what you buy from them.')],
                [__('Bulk import and export'),  __('Upload a spreadsheet, preview the rows, then confirm.')],
                [__('Product templates'),       __('Start from a ready-made import file.')],
            ],
        ],
        [
            'title' => __('Money'),
            'why'   => __('Every franc in and out, with reports your accountant can use.'),
            'items' => [
                [__('Expenses'),               __('Sort by category, attach receipts, switch status on or off.')],
                [__('Other income'),           __('Track income that does not come from sales.')],
                [__('Daily to yearly reports'), __('Sales, purchases, expenses and other income by period.')],
                [__('Profit and loss'),        __('One report that shows where you stand.')],
                [__('Export anywhere'),        __('Download any report as PDF, Excel or CSV.')],
                [__('Shop statistics'),        __('A summary view for the shop owner.')],
            ],
        ],
        [
            'title' => __('People and shops'),
            'why'   => __('Add your team, serve your customers and run more than one shop.'),
            'items' => [
                [__('Staff accounts'),  __('Give each person a role and the right access.')],
                [__('Customers'),       __('Keep a customer list for repeat sales.')],
                [__('Multiple shops'),  __('Each shop keeps its own sales, stock and staff.')],
                [__('Your language'),   __('Switch the interface language at any time.')],
            ],
        ],
    ];

    $roles = [
        [__('Shop admin'),   __('Runs the shop: products, categories, suppliers, customers, staff, expenses and statistics.'), __('Can edit and delete records others can only view.')],
        [__('Seller'),       __('Works the counter: sales, purchases, bulk imports and approving waiter orders.'),             __('Can view products and expenses.')],
        [__('Waiter'),       __('Takes orders at the table from a dashboard built for that one job.'),                         null],
        [__('Manager'),      __('Oversees day-to-day work from a manager dashboard.'),                                         null],
        [__('Accountant'),   __('Follows the money from a finance dashboard and the full set of reports.'),                    null],
        [__('System admin'), __('Approves or rejects new shops, manages users and sets platform settings.'),                   null],
    ];

    $steps = [
        [__('Register your shop'),  __('Create an account and tell us about your shop.')],
        [__('Wait for approval'),   __('A system admin reviews your shop. You see its status while you wait.')],
        [__('Set up'),              __('Add products, suppliers, customers and your staff.')],
        [__('Trade'),               __('Sell, buy and record expenses as the day goes.')],
        [__('Read the reports'),    __('Check profit and loss, then export it as PDF, Excel or CSV.')],
    ];

    $policies = [
        [
            'title' => __('Privacy'),
            'open'  => true,
            'p'     => __('We collect only what the app needs to work: your account details, your shop details, and the records you enter, such as sales, purchases, expenses, products, customers and staff.'),
            'li'    => [
                __('We do not sell your data.'),
                __('The records of your shop are visible only to people you give access to, and to system admins who support the platform.'),
                __('You can ask us to correct or delete your account data at any time.'),
            ],
        ],
        [
            'title' => __('Your data and security'),
            'p'     => __('Each shop keeps its records separate from other shops. Access is controlled by role, and every person signs in with their own account.'),
            'li'    => [
                __('Use a strong password and do not share it.'),
                __('Remove staff accounts when someone leaves.'),
                __('Exports (PDF, Excel, CSV) are copies of your data. Keep them safe.'),
            ],
        ],
        [
            'title' => __('Roles and access'),
            'p'     => __('Shop admins decide who works in their shop and what role each person has. A role limits what someone can see and change. Only shop admins can edit or delete most records.'),
        ],
        [
            'title' => __('Shop approval'),
            'p'     => __('New shops are reviewed by a system admin before they can trade on the platform. We may reject or suspend a shop that gives false information or breaks these terms.'),
        ],
        [
            'title' => __('Terms of use'),
            'li'    => [
                __('Use the app only for lawful business.'),
                __('You are responsible for the accuracy of the records you enter.'),
                __('Do not try to access the data of another shop or break the security of the app.'),
                __('We may update the app and these terms. We will publish changes on this page.'),
            ],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $current) }}" data-theme="light">
<head>
    @include('partials.seo', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription])
    @stack('head')

    {{-- Runs before first paint: applies the saved/system theme, and catches the install event early --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (function () {
            var t = null;
            try { t = localStorage.getItem('mahwi-theme'); } catch (e) {}
            if (t !== 'light' && t !== 'dark') {
                t = window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', t);
            var m = document.querySelector('meta[name="theme-color"]');
            if (m) m.setAttribute('content', t === 'dark' ? '#0B1620' : '#E8EDF0');

            window.addEventListener('beforeinstallprompt', function (e) {
                e.preventDefault();
                window.__mahwiInstallEvent = e;
                document.dispatchEvent(new Event('mahwi:installable'));
            });
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style nonce="{{ Vite::cspNonce() }}">
        :root {
            --counter: #E8EDF0;
            --slip: #FFFFFF;
            --ink: #0F2A3D;
            --ink-soft: #3E566A;
            --accent: #1F4FFF;
            --btn: #1F4FFF;
            --btn-hover: #1739C2;
            --mango: #FFB627;
            --basil: #1E7D52;
            --line: #C9D3DA;
            --header-bg: rgba(232, 237, 240, .94);
            --hover-tint: rgba(15, 42, 61, .07);
            --band: #0F2A3D;
            --device: #0F2A3D;
            --cta-bg: #1F4FFF;
            --loader-bg: rgba(232, 237, 240, .94);
            --shadow: 0 12px 30px rgba(15, 42, 61, .14);
            --receipt-bg: #FFFFFF;
            --receipt-ink: #0F2A3D;
            --receipt-soft: #3E566A;
            --receipt-line: #C9D3DA;
            --display: "Bricolage Grotesque", "Segoe UI", system-ui, sans-serif;
            --body: "Instrument Sans", "Segoe UI", system-ui, sans-serif;
            --mono: "IBM Plex Mono", ui-monospace, Menlo, Consolas, monospace;
            --wrap: 1120px;
            color-scheme: light;
        }
        :root[data-theme="dark"] {
            --counter: #0B1620;
            --slip: #101F2C;
            --ink: #E8EEF3;
            --ink-soft: #A3B6C6;
            --accent: #7C9BFF;
            --btn: #3B66FF;
            --btn-hover: #2F55E0;
            --basil: #3FBF8A;
            --line: #243746;
            --header-bg: rgba(11, 22, 32, .92);
            --hover-tint: rgba(232, 238, 243, .08);
            --band: #07111A;
            --device: #26405A;
            --cta-bg: #1A3FC4;
            --loader-bg: rgba(11, 22, 32, .94);
            --shadow: 0 12px 30px rgba(0, 0, 0, .5);
            color-scheme: dark;
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
            transition: background-color .2s, color .2s;
        }
        h1, h2, h3 { font-family: var(--display); line-height: 1.08; margin: 0; letter-spacing: -0.02em; }
        p { margin: 0; }
        a { color: inherit; }
        ul, ol { margin: 0; padding: 0; list-style: none; }
        :focus-visible { outline: 3px solid var(--accent); outline-offset: 3px; border-radius: 4px; }

        .wrap { max-width: var(--wrap); margin: 0 auto; padding: 0 24px; }
        .skip {
            position: absolute; left: 16px; top: -60px; z-index: 100;
            background: var(--band); color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none;
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
        .btn-primary { background: var(--btn); color: #fff; }
        .btn-primary:hover { background: var(--btn-hover); }
        .btn-ghost { border-color: var(--ink); color: var(--ink); background: transparent; }
        .btn-ghost:hover { background: var(--ink); color: var(--counter); }
        .btn-small { padding: 10px 16px; font-size: .95rem; border-radius: 8px; }

        /* ---------- Header ---------- */
        .site-header {
            position: sticky; top: 0; z-index: 50;
            background: var(--header-bg);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
        }
        .nav { display: flex; align-items: center; justify-content: space-between; height: 68px; gap: 24px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; font: 800 1.25rem var(--display); letter-spacing: -0.02em; }
        .brand-logo { width: 36px; height: 36px; border-radius: 9px; flex: none; display: block; }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a.link { text-decoration: none; padding: 8px 12px; border-radius: 8px; font-weight: 500; color: var(--ink-soft); }
        .nav-links a.link:hover { color: var(--ink); background: var(--hover-tint); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .lang { position: relative; }
        .lang summary {
            list-style: none; cursor: pointer; padding: 8px 12px; border-radius: 8px; font-weight: 500;
            color: var(--ink-soft); display: flex; align-items: center; gap: 6px;
        }
        .lang summary::-webkit-details-marker { display: none; }
        .lang summary:hover { background: var(--hover-tint); color: var(--ink); }
        .lang summary::after { content: ""; width: 6px; height: 6px; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(45deg) translateY(-2px); }
        .lang ul {
            position: absolute; right: 0; top: calc(100% + 6px); min-width: 170px;
            background: var(--slip); border: 1px solid var(--line); border-radius: 10px; padding: 6px;
            box-shadow: var(--shadow);
        }
        .lang li a { display: block; padding: 9px 12px; border-radius: 6px; text-decoration: none; }
        .lang li a:hover { background: var(--counter); }
        .lang li a[aria-current="true"] { font-weight: 600; color: var(--accent); }

        .theme-toggle {
            width: 40px; height: 40px; border-radius: 8px; border: 1px solid var(--line);
            background: transparent; color: var(--ink-soft); cursor: pointer;
            display: grid; place-items: center; transition: background-color .15s, color .15s;
        }
        .theme-toggle:hover { background: var(--hover-tint); color: var(--ink); }
        .theme-toggle svg { width: 20px; height: 20px; }
        .theme-toggle .i-sun { display: none; }
        :root[data-theme="dark"] .theme-toggle .i-sun { display: block; }
        :root[data-theme="dark"] .theme-toggle .i-moon { display: none; }

        .menu-toggle { display: none; background: none; border: 2px solid var(--ink); border-radius: 8px; width: 44px; height: 44px; cursor: pointer; color: var(--ink); }
        .menu-toggle span, .menu-toggle span::before, .menu-toggle span::after {
            display: block; width: 18px; height: 2px; background: currentColor; margin: 0 auto; position: relative;
        }
        .menu-toggle span::before, .menu-toggle span::after { content: ""; position: absolute; left: 0; }
        .menu-toggle span::before { top: -6px; }
        .menu-toggle span::after { top: 6px; }

        /* ---------- Floating install card (right, under the header) ---------- */
        .install {
            position: fixed; top: 80px; right: 16px; z-index: 45;
            display: flex; flex-direction: column; align-items: flex-end; gap: 10px;
            max-width: calc(100vw - 32px);
            animation: install-in .55s cubic-bezier(.2, .8, .2, 1) both;
        }
        .install[hidden] { display: none; }
        .install-card {
            display: flex; align-items: center; gap: 4px;
            background: var(--slip); color: var(--ink);
            border: 1px solid var(--line); border-radius: 18px;
            padding: 8px 6px 8px 8px;
            box-shadow: 0 16px 38px rgba(8, 20, 32, .22), 0 2px 8px rgba(8, 20, 32, .12);
        }
        .install-btn {
            display: flex; align-items: center; gap: 12px;
            background: none; border: 0; padding: 2px 6px 2px 2px; margin: 0;
            font: inherit; color: inherit; text-align: left; cursor: pointer; border-radius: 12px;
        }
        .install-icon { position: relative; width: 44px; height: 44px; flex: none; }
        .install-icon img { width: 100%; height: 100%; border-radius: 12px; display: block; box-shadow: 0 4px 12px rgba(31, 79, 255, .35); }
        .install-icon::after {
            content: ""; position: absolute; inset: 0; border-radius: 12px;
            border: 2px solid var(--accent); opacity: 0; animation: install-pulse 2.6s ease-out 1.2s infinite;
        }
        .install-text { min-width: 0; }
        .install-text strong { display: block; font: 700 .98rem/1.2 var(--display); letter-spacing: -0.01em; }
        .install-text small { display: block; margin-top: 2px; color: var(--ink-soft); font-size: .8rem; line-height: 1.3; max-width: 21ch; }
        .install-pill {
            flex: none; background: var(--btn); color: #fff; font: 600 .88rem/1 var(--body);
            padding: 10px 14px; border-radius: 999px; transition: background-color .15s;
        }
        .install-btn:hover .install-pill { background: var(--btn-hover); }
        .install-close {
            flex: none; width: 30px; height: 30px; border-radius: 50%; border: 0; background: transparent;
            color: var(--ink-soft); font-size: 1.25rem; line-height: 1; cursor: pointer;
        }
        .install-close:hover { background: var(--hover-tint); color: var(--ink); }
        .install-ios {
            max-width: 290px; padding: 14px 16px; border-radius: 14px; font-size: .92rem; color: var(--ink-soft);
            background: var(--slip); border: 1px solid var(--line); box-shadow: var(--shadow);
        }
        .install-ios[hidden] { display: none; }
        .install-ios strong { display: block; color: var(--ink); margin-bottom: 4px; }
        .install-ios button {
            margin-top: 10px; background: var(--btn); color: #fff; border: 0; border-radius: 8px;
            padding: 8px 14px; font: 600 .88rem var(--body); cursor: pointer;
        }
        @keyframes install-in { from { opacity: 0; transform: translateX(24px); } to { opacity: 1; transform: none; } }
        @keyframes install-pulse { 0% { opacity: .55; transform: scale(1); } 70%, 100% { opacity: 0; transform: scale(1.35); } }

        /* ---------- Hero ---------- */
        .hero { padding: 72px 0 96px; overflow: hidden; }
        .hero-grid { display: grid; grid-template-columns: 1.15fr .85fr; gap: 56px; align-items: center; }
        .hero h1 { font-size: clamp(2.6rem, 6vw, 4.6rem); font-weight: 800; max-width: 12ch; }
        .hero .lede { margin-top: 24px; max-width: 46ch; font-size: 1.2rem; color: var(--ink-soft); }
        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 36px; }
        .hero-note { margin-top: 20px; font-size: .95rem; color: var(--ink-soft); }

        /* The till slip: the one memorable element (stays paper-white in both themes) */
        .printer { max-width: 380px; margin-left: auto; width: 100%; }
        .printer-slot { height: 16px; background: var(--device); border-radius: 10px 10px 0 0; position: relative; z-index: 2; }
        .printer-slot::after { content: ""; position: absolute; left: 14px; right: 14px; bottom: 4px; height: 3px; background: #000; border-radius: 2px; opacity: .55; }
        .printer-clip { overflow: hidden; padding: 0 14px 28px; margin: 0 -14px; }
        .slip-wrap { filter: drop-shadow(0 10px 14px rgba(0, 0, 0, .22)); animation: print 1.8s cubic-bezier(.3, .7, .2, 1) .25s both; }
        .slip {
            background: var(--receipt-bg);
            padding: 26px 24px 34px;
            font-family: var(--mono); font-size: .86rem; line-height: 1.55; color: var(--receipt-ink);
            -webkit-mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 16px 100%;
                    mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 16px 100%;
        }
        .slip-title { font: 700 1.1rem var(--display); letter-spacing: -0.01em; text-align: center; }
        .slip-sub { text-align: center; color: var(--receipt-soft); font-size: .78rem; margin-bottom: 14px; }
        .slip hr { border: 0; border-top: 2px dashed var(--receipt-line); margin: 12px 0; }
        .slip .row { display: flex; justify-content: space-between; gap: 12px; }
        .slip .row.stamp { margin-top: 10px; }
        .slip .total { font-weight: 500; font-size: 1.02rem; }
        .slip .tag { display: inline-block; background: var(--mango); color: #0F2A3D; padding: 1px 8px; border-radius: 4px; font-weight: 500; }
        .slip .ok { color: #1E7D52; font-weight: 500; }
        @keyframes print { from { transform: translateY(-100%); } to { transform: translateY(0); } }

        /* ---------- Sections ---------- */
        section { padding: 88px 0; }
        .section-head { max-width: 60ch; margin-bottom: 48px; }
        .section-head h2 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; }
        .section-head p { margin-top: 16px; color: var(--ink-soft); }

        .band { background: var(--band); color: #fff; }
        .band .section-head p { color: #B9C8D4; }
        .paper { background: var(--slip); }

        .feature { display: grid; grid-template-columns: 5fr 7fr; gap: 40px; padding: 36px 0; border-top: 2px solid var(--ink); }
        .feature:last-child { border-bottom: 2px solid var(--ink); }
        .feature h3 { font-size: 1.7rem; font-weight: 700; }
        .feature .why { margin-top: 12px; color: var(--ink-soft); max-width: 36ch; }
        .caps { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px 32px; }
        .caps li { padding-left: 22px; position: relative; }
        .caps li::before { content: ""; position: absolute; left: 0; top: .62em; width: 10px; height: 10px; border-radius: 3px; background: var(--accent); }
        .caps strong { font-weight: 600; }
        .caps span { display: block; color: var(--ink-soft); font-size: .97rem; }

        .roles { width: 100%; border-collapse: collapse; }
        .roles th, .roles td { text-align: left; padding: 20px 16px 20px 0; vertical-align: top; border-top: 1px solid rgba(255, 255, 255, .22); }
        .roles tr:last-child td, .roles tr:last-child th { border-bottom: 1px solid rgba(255, 255, 255, .22); }
        .roles th { font: 700 1.25rem var(--display); width: 220px; color: var(--mango); }
        .roles td { color: #E6EEF4; }
        .roles td small { display: block; margin-top: 4px; color: #9FB3C3; font-size: .92rem; }

        .steps { counter-reset: step; display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
        .steps li { counter-increment: step; border-top: 4px solid var(--ink); padding-top: 18px; }
        .steps li::before { content: counter(step); display: block; font: 800 2.2rem var(--display); color: var(--accent); margin-bottom: 6px; }
        .steps h3 { font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em; }
        .steps p { margin-top: 8px; font-size: .97rem; color: var(--ink-soft); }

        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
        .about-grid h2 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; }
        .about-copy p + p { margin-top: 16px; }
        .about-copy { color: var(--ink-soft); }
        .about-copy strong { color: var(--ink); }
        .stat-line { margin-top: 28px; padding: 20px 22px; background: var(--slip); border-left: 6px solid var(--mango); border-radius: 0 10px 10px 0; color: var(--ink); }
        .stat-line a { color: var(--accent); }

        .policy-grid { display: grid; grid-template-columns: 4fr 8fr; gap: 56px; align-items: start; }
        .policy-side { position: sticky; top: 100px; }
        .policy-side h2 { font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 700; }
        .policy-side p { margin-top: 14px; color: var(--ink-soft); }
        .policy-side .updated { margin-top: 18px; font-size: .92rem; }
        .policy details { border-top: 1px solid var(--line); padding: 4px 0; }
        .policy details:last-child { border-bottom: 1px solid var(--line); }
        .policy summary { cursor: pointer; list-style: none; padding: 18px 40px 18px 0; position: relative; font: 700 1.2rem var(--display); letter-spacing: -0.01em; }
        .policy summary::-webkit-details-marker { display: none; }
        .policy summary::after { content: "+"; position: absolute; right: 6px; top: 50%; transform: translateY(-50%); font: 500 1.6rem var(--body); color: var(--accent); }
        .policy details[open] summary::after { content: "–"; }
        .policy .body { padding: 0 0 20px; color: var(--ink-soft); }
        .policy .body p + p, .policy .body ul { margin-top: 12px; }
        .policy .body a { color: var(--accent); }
        .policy .body li { padding-left: 18px; position: relative; margin-top: 6px; }
        .policy .body li::before { content: ""; position: absolute; left: 0; top: .7em; width: 6px; height: 6px; background: var(--ink); border-radius: 50%; }

        .cta { background: var(--cta-bg); color: #fff; text-align: left; }
        .cta-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 32px; }
        .cta h2 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; max-width: 18ch; }
        .cta .btn-primary { background: var(--mango); color: #0F2A3D; }
        .cta .btn-primary:hover { background: #FFC85A; }
        .cta .btn-ghost { border-color: #fff; color: #fff; }
        .cta .btn-ghost:hover { background: #fff; color: #1739C2; }
        .cta :focus-visible { outline-color: #fff; }
        .cta-actions { margin: 0; }

        footer { background: var(--band); color: #B9C8D4; padding: 56px 0 32px; font-size: .97rem; }
        .foot-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 40px; }
        footer .brand { color: #fff; margin-bottom: 12px; }
        footer h3 { font: 700 1rem var(--display); color: #fff; margin-bottom: 12px; letter-spacing: 0; }
        footer h3.foot-lang { margin-top: 20px; }
        footer li + li { margin-top: 8px; }
        footer a { text-decoration: none; }
        footer a:hover { color: #fff; text-decoration: underline; }
        .foot-bottom { margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, .16); font-size: .9rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px; }

        /* ---------- Page loading overlay ---------- */
        .page-loader {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            background: var(--loader-bg); backdrop-filter: blur(5px);
        }
        .page-loader[hidden] { display: none !important; }
        .page-loader-content { display: flex; flex-direction: column; align-items: center; gap: 20px; }
        .page-loader-spinner {
            width: 66px; height: 66px; border: 5px solid var(--line);
            border-top-color: var(--accent); border-right-color: var(--accent);
            border-radius: 50%; animation: mahwi-page-spin .75s linear infinite;
        }
        .page-loader-brand { font-family: var(--display); font-size: 1.15rem; font-weight: 800; color: var(--ink); letter-spacing: -0.02em; }
        .page-loader-message { font-size: .9rem; font-weight: 500; color: var(--ink-soft); }
        @keyframes mahwi-page-spin { to { transform: rotate(360deg); } }

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
            .nav-actions .btn-primary { display: none; }
            .install { top: 76px; right: 10px; }
            .install-text small { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .slip-wrap, .install, .install-icon::after { animation: none; }
            .page-loader-spinner { animation-duration: 2s; }
            * { transition: none !important; }
        }
    </style>
</head>
<body>

{{-- Full-page loading indicator --}}
<div id="page-loader" class="page-loader" role="status" aria-live="polite" aria-label="{{ __('Loading page') }}" hidden>
    <div class="page-loader-content">
        <div class="page-loader-spinner" aria-hidden="true"></div>
        <div class="page-loader-brand">{{ $appName }}</div>
        <p class="page-loader-message">{{ __('Loading, please wait...') }}</p>
    </div>
</div>

<a class="skip" href="#main">{{ __('Skip to content') }}</a>

{{-- ================= HEADER / PUBLIC NAV ================= --}}
<header class="site-header">
    <div class="wrap nav">
        <a href="{{ url('/') }}" class="brand" aria-label="{{ __(':app home', ['app' => $appName]) }}">
            <img src="{{ asset('icons/icon-192.png') }}" alt="" width="36" height="36" class="brand-logo">
            <span>{{ $appName }}</span>
        </a>

        <nav class="nav-links" id="site-nav" aria-label="{{ __('Main') }}">
            <a class="link" href="#features">{{ __('Features') }}</a>
            <a class="link" href="#roles">{{ __('Who uses it') }}</a>
            <a class="link" href="#how">{{ __('How it works') }}</a>
            <a class="link" href="#about">{{ __('About') }}</a>
            <a class="link" href="#policy">{{ __('Policy') }}</a>
            @if ($hasContact)
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

            <button type="button" id="theme-toggle" class="theme-toggle"
                    data-to-dark="{{ __('Switch to dark mode') }}"
                    data-to-light="{{ __('Switch to light mode') }}"
                    aria-label="{{ __('Switch to dark mode') }}">
                <svg class="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                <svg class="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
            </button>

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

{{-- Floating install card. Kept OUTSIDE the header on purpose: the header uses backdrop-filter,
     which would make a fixed child position itself inside the header instead of the screen. --}}
<div class="install" id="install-widget" hidden>
    <div class="install-card">
        <button type="button" class="install-btn" id="install-btn" aria-label="{{ __('Install :app', ['app' => $appName]) }}">
            <span class="install-icon"><img src="{{ asset('icons/icon-192.png') }}" alt="" width="44" height="44"></span>
            <span class="install-text">
                <strong>{{ __('Install app') }}</strong>
                <small>{{ __('Quick access from your desktop or home screen') }}</small>
            </span>
            <span class="install-pill" aria-hidden="true">{{ __('Install') }}</span>
        </button>
        <button type="button" class="install-close" id="install-close" aria-label="{{ __('Dismiss') }}">&times;</button>
    </div>
    <div class="install-ios" id="install-ios" role="dialog" aria-label="{{ __('Install app') }}" hidden>
        <strong>{{ __('To install on iPhone or iPad:') }}</strong>
        {{ __('Tap the Share button, then choose Add to Home Screen.') }}
        <br><button type="button" id="install-ios-ok">{{ __('Got it') }}</button>
    </div>
</div>

<main id="main">

    {{-- ================= HERO ================= --}}
    <div class="hero">
        <div class="wrap hero-grid">
            <div>
                <h1>{{ __('Sales, stock and money for every shop you run.') }}</h1>
                <p class="lede">
                    {{ __('Sell at the till, record what you buy and spend, and see profit and loss by day, week, month or year. Your sellers, waiters, managers and accountants each get the screens they need and nothing more.') }}
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
                <p class="hero-note">{{ __('New shops are reviewed by a system admin before they go live.') }}</p>
            </div>

            <div class="printer" aria-hidden="true">
                <div class="printer-slot"></div>
                <div class="printer-clip">
                    <div class="slip-wrap">
                        <div class="slip">
                            <div class="slip-title">{{ $appName }}</div>
                            <div class="slip-sub">{{ __('Order :order · Table :table', ['order' => '0142', 'table' => '6']) }}</div>
                            <hr>
                            <div class="row"><span>3 × {{ __('Maize flour 2kg') }}</span><span>7,500</span></div>
                            <div class="row"><span>2 × {{ __('Cooking oil 1L') }}</span><span>6,400</span></div>
                            <div class="row"><span>1 × {{ __('Sugar 5kg') }}</span><span>8,200</span></div>
                            <div class="row"><span>4 × {{ __('Soda 300ml') }}</span><span>2,200</span></div>
                            <hr>
                            <div class="row total"><span>{{ __('Total') }}</span><span>24,300</span></div>
                            <div class="row"><span>{{ __('Paid') }}</span><span>{{ __('Cash') }}</span></div>
                            <hr>
                            <div class="row"><span>{{ __('Taken by') }}</span><span>{{ __('Waiter') }}</span></div>
                            <div class="row"><span>{{ __('Approved by') }}</span><span>{{ __('Seller') }}</span></div>
                            <div class="row stamp"><span class="tag">{{ __('Stock updated') }}</span><span class="ok">{{ __('Done') }}</span></div>
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
                <h2>{{ __('Everything a shop does in a day, in one place.') }}</h2>
                <p>{{ __('From the first sale in the morning to the profit and loss report at night.') }}</p>
            </div>

            @foreach ($features as $feature)
                <div class="feature">
                    <div>
                        <h3>{{ $feature['title'] }}</h3>
                        <p class="why">{{ $feature['why'] }}</p>
                    </div>
                    <ul class="caps">
                        @foreach ($feature['items'] as [$name, $text])
                            <li><strong>{{ $name }}</strong><span>{{ $text }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= ROLES ================= --}}
    <section id="roles" class="band">
        <div class="wrap">
            <div class="section-head">
                <h2>{{ __('Everyone sees the screens they need.') }}</h2>
                <p>{{ __('Access is set by role, so a waiter never ends up in the books and an accountant never ends up at the till.') }}</p>
            </div>

            <table class="roles">
                <tbody>
                @foreach ($roles as [$role, $what, $note])
                    <tr>
                        <th scope="row">{{ $role }}</th>
                        <td>{{ $what }}@if ($note)<small>{{ $note }}</small>@endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section id="how" class="paper">
        <div class="wrap">
            <div class="section-head">
                <h2>{{ __('From sign-up to first report.') }}</h2>
            </div>
            <ol class="steps">
                @foreach ($steps as [$stepTitle, $stepText])
                    <li>
                        <h3>{{ $stepTitle }}</h3>
                        <p>{{ $stepText }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ================= ABOUT ================= --}}
    <section id="about">
        <div class="wrap about-grid">
            <h2>{{ __('Built for the people who actually run shops.') }}</h2>
            <div class="about-copy">
                <p>{!! __(':app started with a simple question: why does a small shop need three notebooks, a calculator and a spreadsheet to know whether it made money today?', ['app' => '<strong>' . e($appName) . '</strong>']) !!}</p>
                <p>{{ __('So we put selling, buying, spending and reporting in one app. Owners get a clear picture of every shop. Staff get a short list of screens that match their job. Nobody has to learn accounting to use it.') }}</p>
                <p>{{ __('It works for a single counter, a restaurant with waiters, or an owner with several locations, because each shop keeps its own records under one login.') }}</p>
                <div class="stat-line">
                    {{ __('Questions before you sign up?') }}
                    @if ($hasContact)
                        <a href="{{ route('contact') }}"><strong>{{ __('Contact us') }}</strong></a>
                    @else
                        {{ __('Get in touch with our team.') }}
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ================= POLICY ================= --}}
    <section id="policy" class="paper">
        <div class="wrap policy-grid">
            <div class="policy-side">
                <h2>{{ __('Policy') }}</h2>
                <p>{{ __('Plain-language summaries of how we handle your data and what we expect from users.') }}</p>
                <p class="updated">{{ __('Last updated :date', ['date' => $policyUpdated]) }}</p>
            </div>

            <div class="policy">
                @foreach ($policies as $policy)
                    <details @if (!empty($policy['open'])) open @endif>
                        <summary>{{ $policy['title'] }}</summary>
                        <div class="body">
                            @isset($policy['p'])<p>{{ $policy['p'] }}</p>@endisset
                            @isset($policy['li'])
                                <ul>
                                    @foreach ($policy['li'] as $point)
                                        <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            @endisset
                        </div>
                    </details>
                @endforeach

                <details>
                    <summary>{{ __('Questions about policy') }}</summary>
                    <div class="body">
                        <p>
                            @if ($hasContact)
                                {!! __('Send us a message on the :link and we will reply as soon as we can.', ['link' => '<a href="' . e(route('contact')) . '">' . e(__('contact page')) . '</a>']) !!}
                            @else
                                {{ __('Contact us and we will reply as soon as we can.') }}
                            @endif
                        </p>
                    </div>
                </details>
            </div>
        </div>
    </section>

    {{-- ================= FINAL CTA ================= --}}
    <section class="cta">
        <div class="wrap cta-inner">
            <h2>{{ __('Close the notebooks. Open your shop.') }}</h2>
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
                <a href="{{ url('/') }}" class="brand" aria-label="{{ __(':app home', ['app' => $appName]) }}">
                    <img src="{{ asset('icons/icon-192.png') }}" alt="" width="36" height="36" class="brand-logo">
                    <span>{{ $appName }}</span>
                </a>
                <p>{{ __('Sales, stock and money for every shop you run.') }}</p>
            </div>
            <div>
                <h3>{{ __('Product') }}</h3>
                <ul>
                    <li><a href="#features">{{ __('Features') }}</a></li>
                    <li><a href="#roles">{{ __('Who uses it') }}</a></li>
                    <li><a href="#how">{{ __('How it works') }}</a></li>
                </ul>
            </div>
            <div>
                <h3>{{ __('Company') }}</h3>
                <ul>
                    <li><a href="#about">{{ __('About') }}</a></li>
                    <li><a href="/privacy">{{ __('Privacy Policy') }}</a></li>
                    @if ($hasContact)
                        <li><a href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h3>{{ __('Account') }}</h3>
                <ul>
                    @auth
                        <li><a href="{{ route('dashboard') }}">{{ __('Open dashboard') }}</a></li>
                    @else
                        @if ($hasLogin)<li><a href="{{ route('login') }}">{{ __('Log in') }}</a></li>@endif
                        @if ($hasRegister)<li><a href="{{ route('register') }}">{{ __('Register your shop') }}</a></li>@endif
                    @endauth
                </ul>
                @if (Route::has('language.switch'))
                    <h3 class="foot-lang">{{ __('Language') }}</h3>
                    <ul>
                        @foreach ($locales as $code => $label)
                            <li><a href="{{ route('language.switch', $code) }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="foot-bottom">
            <span>{{ __('© :year :app. All rights reserved.', ['year' => date('Y'), 'app' => $appName]) }}</span>
            <a href="/privacy">{{ __('Privacy Policy') }}</a>
        </div>
    </div>
</footer>

<script nonce="{{ Vite::cspNonce() }}">
    (function () {
        var root = document.documentElement;

        /* ---------- Loader ---------- */
        var loader = document.getElementById('page-loader');
        function showLoader() { if (loader) loader.hidden = false; }
        function hideLoader() { if (loader) loader.hidden = true; }

        /* ---------- Mobile menu ---------- */
        var btn = document.querySelector('.menu-toggle');
        var nav = document.getElementById('site-nav');
        if (btn && nav) {
            btn.addEventListener('click', function () {
                var open = nav.classList.toggle('open');
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            nav.addEventListener('click', function (event) {
                if (event.target.closest('a')) {
                    nav.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        /* ---------- Language menu closes on outside click / Escape ---------- */
        var lang = document.querySelector('.lang');
        if (lang) {
            document.addEventListener('click', function (e) { if (!lang.contains(e.target)) lang.removeAttribute('open'); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') lang.removeAttribute('open'); });
        }

        /* ---------- Theme toggle ---------- */
        var toggle = document.getElementById('theme-toggle');
        function applyTheme(t) {
            root.setAttribute('data-theme', t);
            var meta = document.querySelector('meta[name="theme-color"]');
            if (meta) meta.setAttribute('content', t === 'dark' ? '#0B1620' : '#E8EDF0');
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
            var follow = function (e) {
                var saved = null;
                try { saved = localStorage.getItem('mahwi-theme'); } catch (err) {}
                if (!saved) applyTheme(e.matches ? 'dark' : 'light');
            };
            if (mq.addEventListener) mq.addEventListener('change', follow);
        }

        /* ---------- Floating install card ---------- */
        var widget = document.getElementById('install-widget');
        var installBtn = document.getElementById('install-btn');
        var closeBtn = document.getElementById('install-close');
        var iosBox = document.getElementById('install-ios');
        var iosOk = document.getElementById('install-ios-ok');
        var DISMISS_KEY = 'mahwi-install-dismissed';
        var WEEK = 7 * 24 * 60 * 60 * 1000;

        var standalone = (window.matchMedia && matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;
        var isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        function recentlyDismissed() {
            try {
                var at = parseInt(localStorage.getItem(DISMISS_KEY) || '0', 10);
                return at && (Date.now() - at) < WEEK;
            } catch (e) { return false; }
        }
        function showInstall() {
            if (!widget || standalone || recentlyDismissed()) return;
            widget.hidden = false;
        }
        function hideInstall(remember) {
            if (widget) widget.hidden = true;
            if (iosBox) iosBox.hidden = true;
            if (remember) { try { localStorage.setItem(DISMISS_KEY, String(Date.now())); } catch (e) {} }
        }

        if (widget && !standalone) {
            if (window.__mahwiInstallEvent) showInstall();
            document.addEventListener('mahwi:installable', showInstall);
            // iPhone/iPad Safari never fires the install event: show the card with manual steps instead.
            if (isIOS) showInstall();

            installBtn.addEventListener('click', function () {
                var evt = window.__mahwiInstallEvent;
                if (evt) {
                    evt.prompt();
                    evt.userChoice.then(function () {
                        window.__mahwiInstallEvent = null;
                        hideInstall(false);
                    });
                } else if (isIOS && iosBox) {
                    iosBox.hidden = !iosBox.hidden;
                }
            });
            closeBtn.addEventListener('click', function () { hideInstall(true); });
            if (iosOk) iosOk.addEventListener('click', function () { hideInstall(true); });
            window.addEventListener('appinstalled', function () { hideInstall(false); });
        }

        /* ---------- Loader on normal same-origin navigation ---------- */
        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[href]');
            if (!link || event.defaultPrevented) return;
            if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ||
                link.hasAttribute('download') || link.target === '_blank') return;
            if (link.hasAttribute('data-no-loader')) return;

            var url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin || url.protocol === 'javascript:') return;
            if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
            if (url.href === window.location.href) return;
            showLoader();
        });
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (form.hasAttribute('data-no-loader') || form.target === '_blank') return;
            showLoader();
        });
        window.addEventListener('pageshow', hideLoader);
        window.mahwiShowLoader = showLoader;
        window.mahwiHideLoader = hideLoader;
    })();

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/service-worker.js').catch(function (error) {
                console.error('Mahwi service worker registration failed:', error);
            });
        });
    }
</script>
</body>
</html>