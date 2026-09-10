```blade
@props(['variant' => 'light'])

@php
    /*
    |--------------------------------------------------------------------------
    | MahWi Footer
    |--------------------------------------------------------------------------
    | Supports:
    | - dark variant for landing/public pages
    | - light variant for dashboard/application pages
    | - safe route fallbacks when some named routes don't exist yet
    |--------------------------------------------------------------------------
    */

    $quotes = [
        "Success is not final, failure is not fatal: it is the courage to continue that counts.",
        "The only way to do great work is to love what you do.",
        "Dream big. Start small. Act now.",
        "Every expert was once a beginner.",
        "Your limitation—it's only your imagination.",
        "Push yourself, because no one else is going to do it for you.",
        "Great things never come from comfort zones.",
        "Success doesn't just find you. You have to go out and get it.",
        "The harder you work for something, the greater you'll feel when you achieve it.",
        "Don't stop when you're tired. Stop when you're done.",
        "Wake up with determination. Go to bed with satisfaction.",
        "Little things make big days.",
        "It's going to be hard, but hard does not mean impossible.",
        "Don't wait for opportunity. Create it.",
        "Sometimes we're tested not to show our weaknesses, but to discover our strengths.",
        "The key to success is to focus on goals, not obstacles.",
        "Dream it. Believe it. Build it.",
        "Work hard in silence, let success make the noise.",
        "Stay positive, work hard, make it happen.",
        "Your business, your rules, your success.",
    ];

    $randomQuote = $quotes[array_rand($quotes)];

    /*
    |--------------------------------------------------------------------------
    | Safe route helper
    |--------------------------------------------------------------------------
    */
    $link = function ($route, $fallback) {
        return \Illuminate\Support\Facades\Route::has($route)
            ? route($route)
            : url($fallback);
    };

    /*
    |--------------------------------------------------------------------------
    | Public links
    |--------------------------------------------------------------------------
    */
    $links = [
        'about' => $link('about', '/about'),
        'features' => $link('features', '/#features'),
        'pricing' => $link('pricing', '/pricing'),
        'contact' => $link('contact', '/contact'),
        'faq' => $link('faq', '/faq'),
        'help' => $link('help', '/help'),
        'documentation' => $link('documentation', '/documentation'),
        'blog' => $link('blog', '/blog'),

        'privacy' => $link('privacy.policy', '/privacy-policy'),
        'terms' => $link('terms', '/terms'),
        'cookies' => $link('cookie.policy', '/cookie-policy'),
        'refund' => $link('refund.policy', '/refund-policy'),
        'subscription' => $link('subscription.policy', '/subscription-policy'),

        'login' => $link('login', '/login'),
        'register' => $link('register', '/register'),
        'dashboard' => $link('dashboard', '/dashboard'),
    ];
@endphp


{{-- ================================================================
     DARK FOOTER — PUBLIC / LANDING PAGES
================================================================ --}}
@if($variant === 'dark')

<footer class="relative overflow-hidden bg-slate-950 text-white">

    {{-- Background decoration --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-indigo-600/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-purple-600/10 blur-3xl"></div>
    </div>


    {{-- ============================================================
         CTA SECTION
    ============================================================ --}}
    <section class="relative border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-14">

            <div class="relative overflow-hidden rounded-3xl
                        bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700
                        px-8 py-10 lg:px-12">

                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border-[40px] border-white"></div>
                    <div class="absolute -bottom-24 -left-10 h-48 w-48 rounded-full border-[30px] border-white"></div>
                </div>

                <div class="relative flex flex-col lg:flex-row
                            items-start lg:items-center justify-between gap-8">

                    <div class="max-w-2xl">
                        <span class="inline-flex items-center rounded-full
                                     bg-white/10 px-3 py-1 text-xs font-semibold
                                     uppercase tracking-wider text-indigo-100 mb-4">
                            Built for growing businesses
                        </span>

                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight">
                            Run your business smarter with MahWi.
                        </h2>

                        <p class="mt-4 text-indigo-100 text-base md:text-lg leading-relaxed">
                            Manage sales, purchases, inventory, expenses, income,
                            staff and multiple business locations from one powerful platform.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 shrink-0">

                        <a href="{{ $links['register'] }}"
                           class="inline-flex items-center justify-center gap-2
                                  rounded-xl bg-white px-6 py-3.5
                                  text-sm font-bold text-indigo-700
                                  shadow-lg shadow-indigo-950/20
                                  transition-all duration-200
                                  hover:-translate-y-0.5 hover:bg-indigo-50">

                            Get Started

                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>

                        <a href="{{ $links['features'] }}"
                           class="inline-flex items-center justify-center
                                  rounded-xl border border-white/30
                                  bg-white/10 px-6 py-3.5
                                  text-sm font-semibold text-white
                                  backdrop-blur-sm
                                  transition hover:bg-white/20">

                            Explore MahWi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================
         MOTIVATIONAL QUOTE
    ============================================================ --}}
    <section class="relative border-b border-white/10">
        <div class="max-w-4xl mx-auto px-6 py-10 text-center">

            <div class="mx-auto mb-5 flex h-11 w-11 items-center justify-center
                        rounded-full bg-white/10 ring-1 ring-white/10">

                <svg class="h-5 w-5 text-indigo-300"
                     fill="currentColor"
                     viewBox="0 0 24 24">
                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151
                    c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391
                    c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638
                    3.996-5.849h-3.983v-10h9.983z"/>
                </svg>
            </div>

            <p class="text-lg md:text-xl font-medium italic leading-relaxed text-white/90">
                "{{ $randomQuote }}"
            </p>

            <p class="mt-3 text-xs font-medium uppercase tracking-wider text-indigo-300">
                Daily inspiration for MahWi entrepreneurs
            </p>
        </div>
    </section>


    {{-- ============================================================
         MAIN FOOTER
    ============================================================ --}}
    <section class="relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-14">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-10">


                {{-- BRAND --}}
                <div class="sm:col-span-2 lg:col-span-2">

                    <a href="{{ url('/') }}"
                       class="inline-flex items-center gap-3 group">

                        <div class="flex h-11 w-11 items-center justify-center
                                    rounded-xl bg-gradient-to-br
                                    from-indigo-500 to-purple-600
                                    shadow-lg shadow-indigo-950/30
                                    transition group-hover:scale-105">

                            <svg class="w-7 h-7 text-white"
                                 viewBox="0 0 50 50"
                                 fill="currentColor">

                                <circle cx="12" cy="10" r="4"/>
                                <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
                                <path d="M9 26 L6 38 L8 38 L12 28"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      fill="none"/>
                                <path d="M13 26 L17 38 L15 38 L11 28"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      fill="none"/>
                                <path d="M14 18 L22 20"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      fill="none"
                                      stroke-linecap="round"/>
                                <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      fill="none"/>
                                <circle cx="30" cy="36" r="3"/>
                                <circle cx="40" cy="36" r="3"/>
                            </svg>
                        </div>

                        <div>
                            <span class="block text-xl font-black tracking-tight">
                                MahWi
                            </span>

                            <span class="block text-[10px] font-semibold
                                         uppercase tracking-[0.2em] text-indigo-300">
                                Business Management
                            </span>
                        </div>
                    </a>


                    <p class="mt-5 max-w-sm text-sm leading-7 text-slate-400">
                        MahWi helps businesses manage their operations, sales,
                        stock, expenses, income, staff and multiple business
                        locations from one platform.
                    </p>


                    {{-- Trust --}}
                    <div class="mt-6 flex flex-wrap gap-2">

                        <span class="inline-flex items-center gap-1.5 rounded-full
                                     border border-white/10 bg-white/5
                                     px-3 py-1.5 text-xs text-slate-300">

                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Secure platform
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full
                                     border border-white/10 bg-white/5
                                     px-3 py-1.5 text-xs text-slate-300">

                            <svg class="w-3.5 h-3.5 text-indigo-300"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0
                                      00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4
                                      4 0 00-8 0v2h8z"/>
                            </svg>

                            Business ready
                        </span>
                    </div>


                    {{-- Social links --}}
                    <div class="mt-7 flex items-center gap-2">

                        <a href="#"
                           aria-label="Facebook"
                           class="flex h-9 w-9 items-center justify-center rounded-lg
                                  bg-white/5 text-slate-400 ring-1 ring-white/10
                                  transition hover:bg-white/10 hover:text-white">

                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5
                                1.6-1.5h1.7V4c-.3 0-1.4-.1-2.6-.1-2.6
                                0-4.3 1.6-4.3 4.4V10H7v3h2.8v8h3.7z"/>
                            </svg>
                        </a>

                        <a href="#"
                           aria-label="Instagram"
                           class="flex h-9 w-9 items-center justify-center rounded-lg
                                  bg-white/5 text-slate-400 ring-1 ring-white/10
                                  transition hover:bg-white/10 hover:text-white">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="5"
                                      stroke-width="2"/>
                                <circle cx="12" cy="12" r="4"
                                        stroke-width="2"/>
                                <circle cx="17.5" cy="6.5" r="1"
                                        fill="currentColor"
                                        stroke="none"/>
                            </svg>
                        </a>

                        <a href="#"
                           aria-label="LinkedIn"
                           class="flex h-9 w-9 items-center justify-center rounded-lg
                                  bg-white/5 text-slate-400 ring-1 ring-white/10
                                  transition hover:bg-white/10 hover:text-white">

                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.5 8.5H3V21h3.5V8.5zM4.75
                                3A2.05 2.05 0 102 5.05 2.05 2.05 0
                                004.75 3zM21 13.9c0-3.76-2-5.51-4.67-5.51
                                -2.15 0-3.11 1.18-3.65 2.01V8.5H9.2V21h3.48v-6.19
                                c0-1.63.31-3.21 2.33-3.21 1.99 0 2.02
                                1.86 2.02 3.32V21H21v-7.1z"/>
                            </svg>
                        </a>

                        <a href="#"
                           aria-label="X"
                           class="flex h-9 w-9 items-center justify-center rounded-lg
                                  bg-white/5 text-slate-400 ring-1 ring-white/10
                                  transition hover:bg-white/10 hover:text-white">

                            <span class="text-sm font-bold">X</span>
                        </a>
                    </div>
                </div>


                {{-- PLATFORM --}}
                <div>
                    <h3 class="mb-5 text-sm font-bold text-white">
                        Platform
                    </h3>

                    <ul class="space-y-3 text-sm">

                        @foreach([
                            ['Dashboard', $links['dashboard']],
                            ['Products', url('/products')],
                            ['Categories', url('/categories')],
                            ['Sales', url('/sales')],
                            ['Purchases', url('/purchases')],
                            ['Inventory', url('/inventory')],
                            ['Expenses', url('/expenses')],
                            ['Income', url('/income')],
                            ['Reports', url('/reports')],
                        ] as [$label, $url])

                            <li>
                                <a href="{{ $url }}"
                                   class="text-slate-400 transition
                                          hover:text-white">
                                    {{ $label }}
                                </a>
                            </li>

                        @endforeach

                    </ul>
                </div>


                {{-- BUSINESS --}}
                <div>
                    <h3 class="mb-5 text-sm font-bold text-white">
                        Business
                    </h3>

                    <ul class="space-y-3 text-sm">

                        @foreach([
                            ['Suppliers', url('/suppliers')],
                            ['Customers', url('/customers')],
                            ['Staff', url('/staff')],
                            ['Shops & Locations', url('/shops')],
                            ['Stock Management', url('/stock')],
                            ['Orders', url('/orders')],
                            ['Payments', url('/payments')],
                            ['Subscriptions', url('/subscriptions')],
                        ] as [$label, $url])

                            <li>
                                <a href="{{ $url }}"
                                   class="text-slate-400 transition
                                          hover:text-white">
                                    {{ $label }}
                                </a>
                            </li>

                        @endforeach

                    </ul>
                </div>


                {{-- SOLUTIONS --}}
                <div>
                    <h3 class="mb-5 text-sm font-bold text-white">
                        Solutions
                    </h3>

                    <ul class="space-y-3 text-sm">

                        @foreach([
                            'Retail Shops',
                            'Supermarkets',
                            'Restaurants',
                            'Bars',
                            'Boutiques',
                            'Pharmacies',
                            'Hardware Stores',
                            'Electronics Shops',
                            'Wholesale Businesses',
                        ] as $solution)

                            <li>
                                <a href="{{ $links['features'] }}"
                                   class="text-slate-400 transition
                                          hover:text-white">
                                    {{ $solution }}
                                </a>
                            </li>

                        @endforeach

                    </ul>
                </div>


                {{-- COMPANY --}}
                <div>
                    <h3 class="mb-5 text-sm font-bold text-white">
                        Company
                    </h3>

                    <ul class="space-y-3 text-sm">

                        <li>
                            <a href="{{ $links['about'] }}"
                               class="text-slate-400 hover:text-white transition">
                                About MahWi
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['pricing'] }}"
                               class="text-slate-400 hover:text-white transition">
                                Pricing
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['blog'] }}"
                               class="text-slate-400 hover:text-white transition">
                                Blog
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['faq'] }}"
                               class="text-slate-400 hover:text-white transition">
                                FAQs
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['contact'] }}"
                               class="text-slate-400 hover:text-white transition">
                                Contact Us
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['help'] }}"
                               class="text-slate-400 hover:text-white transition">
                                Support
                            </a>
                        </li>

                    </ul>
                </div>

            </div>


            {{-- ========================================================
                 CONTACT STRIP
            ======================================================== --}}
            <div class="mt-14 grid grid-cols-1 md:grid-cols-3
                        overflow-hidden rounded-2xl
                        border border-white/10 bg-white/[0.03]">

                {{-- Phone --}}
                <a href="tel:0786163963"
                   class="group flex items-center gap-4 p-5
                          transition hover:bg-white/[0.05]">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                rounded-xl bg-indigo-500/10 text-indigo-300">

                        <svg class="h-5 w-5" fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a2 2 0
                                  011.897 1.368l1.498 4.493a2 2 0
                                  01-1.004 2.42l-2.257 1.13a11.04
                                  11.04 0 005.516 5.516l1.13-2.257a2
                                  2 0 012.42-1.004l4.493 1.498A2
                                  2 0 0121 12.72V19a2 2 0 01-2
                                  2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Call us</p>
                        <p class="mt-0.5 text-sm font-semibold text-white">
                            0786 163 963
                        </p>
                    </div>
                </a>


                {{-- WhatsApp --}}
                <a href="https://wa.me/250786163963"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="group flex items-center gap-4 border-y
                          border-white/10 p-5 transition
                          hover:bg-white/[0.05] md:border-x md:border-y-0">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                rounded-xl bg-emerald-500/10 text-emerald-300">

                        <svg class="h-5 w-5" fill="currentColor"
                             viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967
                            -.273-.099-.471-.148-.67.15-.197.297-.767.966-.94
                            1.164-.173.199-.347.223-.644.075-.297-.15-1.255
                            -.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059
                            -.173-.297-.018-.458.13-.606.134-.133.298-.347.446
                            -.52.149-.174.198-.298.298-.497.099-.198.05-.371
                            -.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579
                            -.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198
                            0-.52.074-.792.372-.272.297-1.04 1.016-1.04
                            2.479 0 1.462 1.065 2.875 1.213 3.074.149.198
                            2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625
                            .712.227 1.36.195 1.871.118.571-.085 1.758-.719
                            2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124
                            -.272-.198-.57-.347"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Chat with us</p>
                        <p class="mt-0.5 text-sm font-semibold text-white">
                            WhatsApp Support
                        </p>
                    </div>
                </a>


                {{-- Location --}}
                <div class="flex items-center gap-4 p-5">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                rounded-xl bg-purple-500/10 text-purple-300">

                        <svg class="h-5 w-5" fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998
                                  1.998 0 01-2.827 0l-4.244-4.243a8
                                  8 0 1111.314 0z"/>
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0
                                  016 0z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Based in</p>
                        <p class="mt-0.5 text-sm font-semibold text-white">
                            Kigali, Rwanda
                        </p>
                    </div>
                </div>

            </div>


            {{-- ========================================================
                 LEGAL LINKS
            ======================================================== --}}
            <div class="mt-12 pt-8 border-t border-white/10">

                <div class="flex flex-wrap gap-x-6 gap-y-3 text-xs">

                    <a href="{{ $links['terms'] }}"
                       class="text-slate-500 hover:text-white transition">
                        Terms & Conditions
                    </a>

                    <a href="{{ $links['privacy'] }}"
                       class="text-slate-500 hover:text-white transition">
                        Privacy Policy
                    </a>

                    <a href="{{ $links['cookies'] }}"
                       class="text-slate-500 hover:text-white transition">
                        Cookie Policy
                    </a>

                    <a href="{{ $links['refund'] }}"
                       class="text-slate-500 hover:text-white transition">
                        Refund Policy
                    </a>

                    <a href="{{ $links['subscription'] }}"
                       class="text-slate-500 hover:text-white transition">
                        Subscription Policy
                    </a>

                    <a href="{{ url('/acceptable-use-policy') }}"
                       class="text-slate-500 hover:text-white transition">
                        Acceptable Use
                    </a>

                    <a href="{{ url('/data-protection') }}"
                       class="text-slate-500 hover:text-white transition">
                        Data Protection
                    </a>

                </div>
            </div>


            {{-- ========================================================
                 BOTTOM BAR
            ======================================================== --}}
            <div class="mt-7 flex flex-col gap-5
                        md:flex-row md:items-center md:justify-between">

                <p class="text-xs leading-5 text-slate-500">
                    © {{ date('Y') }} MahWi. All rights reserved.
                    Built with care in Rwanda.
                </p>

                <div class="flex flex-wrap items-center gap-5">

                    <span class="inline-flex items-center gap-2 text-xs text-slate-500">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Platform operational
                    </span>

                    <span class="hidden sm:inline text-slate-700">•</span>

                    <span class="text-xs text-slate-500">
                        RWF · Rwanda
                    </span>

                    <span class="hidden sm:inline text-slate-700">•</span>

                    <span class="text-xs text-slate-500">
                        MahWi Business Management
                    </span>

                </div>

            </div>

        </div>
    </section>

</footer>


{{-- ================================================================
     LIGHT FOOTER — APPLICATION / DASHBOARD
================================================================ --}}
@else

<footer class="mt-auto border-t border-gray-200 bg-white">

    {{-- Quote --}}
    <div class="bg-gradient-to-r from-indigo-50
                via-purple-50 to-pink-50">

        <div class="max-w-4xl mx-auto px-6 py-8 text-center">

            <div class="mx-auto mb-3 flex h-9 w-9 items-center justify-center
                        rounded-full bg-indigo-600 shadow-sm">

                <svg class="h-4 w-4 text-white"
                     fill="currentColor"
                     viewBox="0 0 24 24">
                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983
                    10.609l-.995-2.151c2.432-.917 3.995-3.638
                    3.995-5.849h-4v-10h9.983zm14.017 0v7.391
                    c0 5.704-3.748 9.571-9 10.609l-.996-2.151
                    c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                </svg>

            </div>

            <p class="text-base md:text-lg font-medium italic text-gray-700">
                "{{ $randomQuote }}"
            </p>

            <p class="mt-2 text-xs font-medium uppercase
                      tracking-wider text-indigo-500">
                Keep building. Keep growing.
            </p>

        </div>
    </div>


    {{-- Main --}}
    <div class="px-6 py-10 lg:px-12">

        <div class="max-w-7xl mx-auto">

            <div class="grid grid-cols-1 sm:grid-cols-2
                        lg:grid-cols-5 gap-8">


                {{-- Brand --}}
                <div class="sm:col-span-2">

                    <a href="{{ url('/') }}"
                       class="inline-flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center
                                    rounded-xl bg-indigo-100 text-indigo-600">

                            <svg class="h-6 w-6"
                                 viewBox="0 0 50 50"
                                 fill="currentColor">

                                <circle cx="12" cy="10" r="4"/>
                                <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
                                <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      fill="none"/>
                                <circle cx="30" cy="36" r="3"/>
                                <circle cx="40" cy="36" r="3"/>

                            </svg>
                        </div>

                        <div>
                            <span class="block font-bold text-gray-900">
                                MahWi
                            </span>

                            <span class="block text-[10px]
                                         uppercase tracking-wider
                                         text-gray-400">
                                Business Management
                            </span>
                        </div>

                    </a>

                    <p class="mt-4 max-w-sm text-sm leading-6 text-gray-500">
                        Everything your business needs to manage sales,
                        stock, purchases, expenses, income and growth.
                    </p>

                    <div class="mt-5 flex items-center gap-2">

                        <span class="inline-flex items-center gap-1.5 rounded-full
                                     bg-emerald-50 px-3 py-1.5
                                     text-xs font-medium text-emerald-700">

                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Secure
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full
                                     bg-indigo-50 px-3 py-1.5
                                     text-xs font-medium text-indigo-700">

                            <svg class="h-3.5 w-3.5" fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>

                            Business ready
                        </span>

                    </div>
                </div>


                {{-- Platform --}}
                <div>
                    <h3 class="mb-4 text-sm font-bold text-gray-900">
                        Platform
                    </h3>

                    <ul class="space-y-2.5 text-sm">

                        <li>
                            <a href="{{ $links['dashboard'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Dashboard
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/products') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Products
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/sales') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Sales
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/purchases') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Purchases
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/inventory') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Inventory
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/reports') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Reports
                            </a>
                        </li>

                    </ul>
                </div>


                {{-- Business --}}
                <div>
                    <h3 class="mb-4 text-sm font-bold text-gray-900">
                        Business
                    </h3>

                    <ul class="space-y-2.5 text-sm">

                        <li>
                            <a href="{{ url('/shops') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Shops & Locations
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/suppliers') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Suppliers
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/customers') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Customers
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/staff') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Staff
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/expenses') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Expenses
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/income') }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Income
                            </a>
                        </li>

                    </ul>
                </div>


                {{-- Help --}}
                <div>
                    <h3 class="mb-4 text-sm font-bold text-gray-900">
                        Help & Company
                    </h3>

                    <ul class="space-y-2.5 text-sm">

                        <li>
                            <a href="{{ $links['about'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                About MahWi
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['pricing'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Pricing
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['help'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Help Center
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['documentation'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Documentation
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['faq'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                FAQs
                            </a>
                        </li>

                        <li>
                            <a href="{{ $links['contact'] }}"
                               class="text-gray-500 hover:text-indigo-600 transition">
                                Contact Support
                            </a>
                        </li>

                    </ul>
                </div>

            </div>


            {{-- Contact --}}
            <div class="mt-10 flex flex-col gap-4
                        rounded-2xl border border-gray-200
                        bg-gray-50 p-5
                        sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-sm font-semibold text-gray-900">
                        Need help with MahWi?
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Our support team is ready to help you keep your business moving.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">

                    <a href="tel:0786163963"
                       class="inline-flex items-center gap-2 rounded-lg
                              bg-white px-4 py-2.5 text-xs font-semibold
                              text-gray-700 shadow-sm ring-1 ring-gray-200
                              hover:text-indigo-600 transition">

                        <svg class="h-4 w-4" fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a2
                                  2 0 011.897 1.368l1.498 4.493a2
                                  2 0 01-1.004 2.42l-2.257
                                  1.13a11.04 11.04 0 005.516
                                  5.516l1.13-2.257a2 2 0
                                  012.42-1.004l4.493 1.498A2
                                  2 0 0121 12.72V19a2 2
                                  0 01-2 2h-1C9.716 21
                                  3 14.284 3 6V5z"/>
                        </svg>

                        0786 163 963
                    </a>

                    <a href="https://wa.me/250786163963"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-lg
                              bg-emerald-600 px-4 py-2.5 text-xs
                              font-semibold text-white
                              hover:bg-emerald-700 transition">

                        WhatsApp Support
                    </a>

                </div>
            </div>


            {{-- Legal --}}
            <div class="mt-8 pt-6 border-t border-gray-200">

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">

                    <a href="{{ $links['privacy'] }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Privacy
                    </a>

                    <a href="{{ $links['terms'] }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Terms
                    </a>

                    <a href="{{ $links['cookies'] }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Cookies
                    </a>

                    <a href="{{ $links['refund'] }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Refund Policy
                    </a>

                    <a href="{{ $links['subscription'] }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Subscription Policy
                    </a>

                    <a href="{{ url('/acceptable-use-policy') }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Acceptable Use
                    </a>

                    <a href="{{ url('/data-protection') }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        Data Protection
                    </a>

                </div>
            </div>


            {{-- Bottom --}}
            <div class="mt-6 flex flex-col gap-3
                        sm:flex-row sm:items-center
                        sm:justify-between">

                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} MahWi. All rights reserved.
                </p>

                <div class="flex flex-wrap items-center gap-4">

                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Platform operational
                    </span>

                    <span class="text-gray-300">•</span>

                    <span class="text-xs text-gray-400">
                        Made in Rwanda 🇷🇼
                    </span>

                </div>

            </div>

        </div>
    </div>

</footer>

@endif
