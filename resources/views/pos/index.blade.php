{{-- resources/views/pos/index.blade.php --}}
<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-300 sm:text-2xl">
                    {{ __('Point of Sale') }}
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                    {{ __('Tap a product card or scan a barcode to add it to the sale.') }}
                </p>
            </div>

            <a href="#pos-cart"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm lg:hidden">
                {{ __('Cart') }}
                <span id="cart-count-mobile"
                      class="rounded-full bg-white/20 px-2 py-0.5 text-xs tabular-nums">0</span>
            </a>
        </div>
    </x-slot>

    {{-- =========================================================
         3D PRODUCT CARD STYLES
    ========================================================== --}}
    <style nonce="{{ Vite::cspNonce() }}">
        /* Scene: gives every card its own perspective so tilt looks real */
        .pos-scene {
            perspective: 900px;
            perspective-origin: 50% 40%;
        }

        /* Card = square. Top part shows the full image, bottom strip holds the text.
           Colours follow Tailwind's `.dark` class. */
        .pos-card {
            --rx: 0deg;
            --ry: 0deg;
            --lift: 0px;
            --mx: 50%;
            --my: 0%;
            --hue: 230;
            --info-h: 36%;
            --radius: 1.15rem;

            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: var(--radius);
            cursor: pointer;
            outline: none;
            user-select: none;
            -webkit-tap-highlight-color: transparent;

            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, .1);

            transform-style: preserve-3d;
            transform: rotateX(var(--rx)) rotateY(var(--ry)) translateZ(var(--lift));
            transition: transform .45s cubic-bezier(.2, .8, .2, 1), box-shadow .45s cubic-bezier(.2, .8, .2, 1);
            will-change: transform;

            box-shadow:
                0 1px 2px rgba(15, 23, 42, .12),
                0 10px 18px -8px rgba(15, 23, 42, .28),
                0 22px 34px -16px rgba(79, 70, 229, .25);
        }

        .dark .pos-card {
            background: #1f2937;
            border-color: rgba(255, 255, 255, .1);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, .5),
                0 12px 20px -8px rgba(0, 0, 0, .7),
                0 24px 36px -16px rgba(99, 102, 241, .3);
        }

        .pos-card.is-tilting {
            transition: transform .08s linear, box-shadow .3s ease;
        }

        @media (hover: hover) {
            .pos-card:not(.is-out):hover {
                --lift: 18px;
                box-shadow:
                    0 2px 4px rgba(15, 23, 42, .15),
                    0 18px 28px -10px rgba(15, 23, 42, .4),
                    0 38px 56px -22px rgba(79, 70, 229, .45);
            }

            .dark .pos-card:not(.is-out):hover {
                box-shadow:
                    0 2px 4px rgba(0, 0, 0, .55),
                    0 20px 30px -10px rgba(0, 0, 0, .8),
                    0 40px 60px -22px rgba(99, 102, 241, .5);
            }
        }

        .pos-card:not(.is-out):active {
            --lift: 4px;
            transition-duration: .12s;
        }

        .pos-card:focus-visible {
            box-shadow: 0 0 0 3px #fff, 0 0 0 6px #6366f1, 0 18px 30px -10px rgba(60, 40, 180, .5);
        }

        .dark .pos-card:focus-visible {
            box-shadow: 0 0 0 3px #111827, 0 0 0 6px #818cf8, 0 18px 30px -10px rgba(0, 0, 0, .7);
        }

        .pos-card.is-out {
            cursor: not-allowed;
        }

        .pos-card.is-out .pos-media {
            filter: grayscale(1) brightness(.8);
        }

        .pos-card.is-out .pos-info {
            opacity: .65;
        }

        /* Image area: shown clean, no overlay darkening it */
        .pos-media {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: var(--info-h);
            overflow: hidden;
            border-radius: calc(var(--radius) - 1px) calc(var(--radius) - 1px) 0 0;
            background: linear-gradient(135deg,
                hsl(var(--hue) 70% 48%),
                hsl(calc(var(--hue) + 45) 75% 28%));
            transform: translateZ(0);
        }

        .pos-media img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .7s cubic-bezier(.2, .8, .2, 1);
        }

        @media (hover: hover) {
            .pos-card:not(.is-out):hover .pos-media img {
                transform: scale(1.08);
            }
        }

        /* Initial letter shown when a product has no image */
        .pos-initial {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1;
            color: rgba(255, 255, 255, .3);
            text-shadow: 0 6px 24px rgba(0, 0, 0, .25);
        }

        /* Light that follows the pointer (subtle so the photo stays true) */
        .pos-shine {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .3s ease;
            background: radial-gradient(circle at var(--mx) var(--my), rgba(255, 255, 255, .28), transparent 55%);
            pointer-events: none;
        }

        .pos-card.is-tilting .pos-shine {
            opacity: 1;
        }

        /* Floating layers sit at different depths above the card */
        .pos-float {
            position: absolute;
            transform: translateZ(var(--z, 40px));
        }

        .pos-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .22rem .6rem;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            color: #fff;
            background: rgba(14, 14, 32, .62);
            -webkit-backdrop-filter: blur(10px) saturate(150%);
            backdrop-filter: blur(10px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, .25);
            box-shadow: 0 8px 16px -6px rgba(0, 0, 0, .55);
        }

        .pos-dot {
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
        }

        .pos-dot.ok   { background: #4ade80; box-shadow: 0 0 8px #4ade80; }
        .pos-dot.low  { background: #fbbf24; box-shadow: 0 0 8px #fbbf24; }
        .pos-dot.none { background: #f87171; box-shadow: 0 0 8px #f87171; }

        /* Quantity stepper shown once a product is in the cart */
        .pos-stepper {
            display: inline-flex;
            align-items: center;
            gap: .1rem;
            padding: .15rem;
            border-radius: 999px;
            color: #fff;
            background: rgba(79, 70, 229, .9);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, .3);
            box-shadow: 0 10px 18px -6px rgba(30, 20, 120, .7);
        }

        .pos-step-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.65rem;
            height: 1.65rem;
            border-radius: 999px;
            font-size: .95rem;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            background: rgba(255, 255, 255, .16);
            transition: background .15s ease;
        }

        .pos-step-btn:hover {
            background: rgba(255, 255, 255, .32);
        }

        .pos-step-count {
            min-width: 1.4rem;
            text-align: center;
            font-size: .8rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        /* Price tag floats over the seam between image and text strip */
        .pos-price {
            --z: 70px;
            position: absolute;
            right: .6rem;
            bottom: calc(var(--info-h) - .95rem);
            padding: .28rem .7rem;
            border-radius: .65rem;
            font-size: .85rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: .01em;
            color: #fff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: 1px solid rgba(255, 255, 255, .4);
            box-shadow: 0 12px 20px -8px rgba(60, 40, 200, .85), inset 0 1px 0 rgba(255, 255, 255, .35);
            transform: translateZ(var(--z)) rotateZ(-2deg);
        }

        /* Text strip below the image: name + SKU, theme aware */
        .pos-info {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: var(--info-h);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: .45rem .75rem;
            overflow: hidden;
            border-radius: 0 0 calc(var(--radius) - 1px) calc(var(--radius) - 1px);
            border-top: 1px solid rgba(15, 23, 42, .08);
            color: #111827;
        }

        .dark .pos-info {
            border-top-color: rgba(255, 255, 255, .08);
            color: #f3f4f6;
        }

        .pos-name {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: .8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .pos-sku {
            margin-top: .15rem;
            font-size: 11px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .pos-sku {
            color: #9ca3af;
        }

        /* Feedback when a product is added */
        @keyframes pos-pop {
            0%   { transform: rotateX(0) rotateY(0) translateZ(0) scale(1); }
            40%  { transform: rotateX(-6deg) rotateY(0) translateZ(34px) scale(1.05); }
            100% { transform: rotateX(0) rotateY(0) translateZ(0) scale(1); }
        }

        .pos-card.pos-pop {
            animation: pos-pop .45s cubic-bezier(.2, .8, .2, 1);
        }

        @media (prefers-reduced-motion: reduce) {
            .pos-card,
            .pos-media img {
                transition: none;
            }

            .pos-card.pos-pop {
                animation: none;
            }
        }
    </style>

    <div class="py-4 sm:py-6">
        <div class="mx-auto max-w-screen-2xl px-3 sm:px-6 lg:px-8">
            <div id="checkout-success" class="mb-4 hidden rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200" role="alert" aria-live="polite">
                <p id="checkout-success-message" class="font-semibold"></p>
                <div class="mt-2 flex gap-3 text-sm">
                    <a id="checkout-success-print" class="font-medium underline hover:no-underline" target="_blank" rel="noopener">{{ __('Print receipt') }}</a>
                    <a id="checkout-success-pdf" class="font-medium underline hover:no-underline">{{ __('Download PDF receipt') }}</a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">

                {{-- =====================================================
                     PRODUCTS
                ====================================================== --}}
                <section class="lg:col-span-8 xl:col-span-9">

                    {{-- Toolbar --}}
                    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-4">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center">

                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4"/>
                                </svg>

                                <input
                                    id="pos-search"
                                    type="search"
                                    autocomplete="off"
                                    autofocus
                                    placeholder="{{ __('Search or scan name, SKU, barcode… (press Enter)') }}"
                                    class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                            </div>

                            {{-- Auto-select toggle --}}
                            <label class="flex shrink-0 cursor-pointer select-none items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-600">
                                <input id="pos-auto" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                    {{ __('Auto-select items') }}
                                </span>
                            </label>

                            <label class="flex shrink-0 cursor-pointer select-none items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-600">
                                <input id="pos-hide-out" type="checkbox" checked
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                    {{ __('Hide out of stock') }}
                                </span>
                            </label>
                        </div>

                        <p id="pos-auto-hint" class="mt-2 hidden text-xs text-indigo-600 dark:text-indigo-300">
                            {{ __('Auto-select is ON: pressing Enter (or scanning) adds the exact barcode/SKU match, or the only product left in the results.') }}
                        </p>

                        {{-- Category chips --}}
                        <div id="pos-categories" class="mt-3 flex gap-2 overflow-x-auto pb-1"></div>
                    </div>

                    {{-- Cards grid (padding leaves room for the 3D lift and shadows) --}}
                    <div id="pos-grid"
                         class="grid grid-cols-2 gap-4 p-1 sm:grid-cols-3 sm:gap-5 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6"></div>

                    <p id="pos-empty"
                       class="hidden rounded-xl border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                        {{ __('No products match your search.') }}
                    </p>

                    <p id="pos-more" class="mt-3 hidden text-center text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Showing the first products only — refine your search to see more.') }}
                    </p>
                </section>


                {{-- =====================================================
                     CART
                ====================================================== --}}
                <aside id="pos-cart" class="lg:col-span-4 xl:col-span-3">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:sticky lg:top-4">

                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ __('Current sale') }}
                                <span id="cart-count" class="ml-1 text-sm font-normal text-gray-500 dark:text-gray-400">(0)</span>
                            </h3>

                            <button type="button" id="cart-clear"
                                    class="text-xs font-medium text-red-600 hover:text-red-700 disabled:opacity-40"
                                    disabled>
                                {{ __('Clear') }}
                            </button>
                        </div>

                        <div id="cart-items" class="max-h-[45vh] divide-y divide-gray-100 overflow-y-auto dark:divide-gray-700"></div>

                        <p id="cart-empty" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No items yet.') }}
                        </p>

                        <div class="space-y-3 border-t border-gray-100 px-4 py-4 dark:border-gray-700">

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Total') }}</span>
                                <span id="cart-total" class="text-xl font-bold tabular-nums text-gray-900 dark:text-white">RWF 0</span>
                            </div>

                            <div>
                                <label for="pay-method" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('Payment method') }}
                                </label>
                                <select id="pay-method"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    <option value="cash">{{ __('Cash') }}</option>
                                    <option value="mobile_money">{{ __('Mobile Money') }}</option>
                                    <option value="card">{{ __('Card') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="pay-received" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('Amount received') }}
                                </label>
                                <input id="pay-received" type="number" min="0" step="0.01" inputmode="decimal"
                                       placeholder="{{ __('Leave empty = exact amount') }}"
                                       class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <span id="change-label" class="text-gray-600 dark:text-gray-300">{{ __('Change') }}</span>
                                <span id="change-value" class="font-semibold tabular-nums text-gray-900 dark:text-white">RWF 0</span>
                            </div>

                            <p id="checkout-error" class="hidden text-sm text-red-600" role="alert"></p>

                            <button type="button" id="checkout-btn"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled>
                                {{ __('Complete sale') }}
                            </button>
                        </div>
                    </div>
                </aside>

            </div>

            <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" aria-labelledby="recent-sales-heading">
                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                    <h3 id="recent-sales-heading" class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent sales') }}</h3>
                </div>
                <ul id="recent-sales-list" class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($recentSales as $sale)
                        <li class="flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between" data-sale-id="{{ $sale->id }}">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('Sale #:id', ['id' => $sale->id]) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $sale->sale_date->format('M d, Y') }} · {{ rwf($sale->total_amount) }} · {{ $sale->payment_status ?? __('Unpaid') }}</p>
                            </div>
                            <div class="flex gap-3 text-sm">
                                <a href="{{ route('pos.sales.print', ['sale' => $sale, 'print' => 1]) }}" target="_blank" rel="noopener" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">{{ __('Print') }}</a>
                                <a href="{{ route('pos.sales.export', $sale) }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">{{ __('PDF receipt') }}</a>
                            </div>
                        </li>
                    @empty
                        <li id="recent-sales-empty" class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No sales recorded yet.') }}</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>

    {{-- Toast --}}
    <div id="pos-toast"
         class="pointer-events-none fixed bottom-5 left-1/2 z-50 hidden -translate-x-1/2 rounded-lg bg-gray-900 px-4 py-2 text-sm text-white shadow-lg"
         role="status" aria-live="polite"></div>


    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function () {

            /* ---------------------------------------------------------
             | Data (comes from PosController@index)
             --------------------------------------------------------- */
            const products   = @json($products);
            const categories = @json($categories);
            const CHECKOUT_URL = @json(route('pos.checkout'));
            const CSRF = @json(csrf_token());
            const MAX_CARDS = 80;

            const byId = new Map(products.map(p => [p.id, p]));
            const cart = new Map();            // product id -> quantity
            let activeCategory = 'all';
            let lastAddedId = null;            // drives the "pop" animation

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* ---------------------------------------------------------
             | Elements
             --------------------------------------------------------- */
            const $ = id => document.getElementById(id);

            const searchEl   = $('pos-search');
            const autoEl     = $('pos-auto');
            const hideOutEl  = $('pos-hide-out');
            const autoHint   = $('pos-auto-hint');
            const catsEl     = $('pos-categories');
            const gridEl     = $('pos-grid');
            const emptyEl    = $('pos-empty');
            const moreEl     = $('pos-more');
            const cartItems  = $('cart-items');
            const cartEmpty  = $('cart-empty');
            const cartTotal  = $('cart-total');
            const cartCount  = $('cart-count');
            const cartCountM = $('cart-count-mobile');
            const clearBtn   = $('cart-clear');
            const payMethod  = $('pay-method');
            const payRecv    = $('pay-received');
            const changeLbl  = $('change-label');
            const changeVal  = $('change-value');
            const checkoutBt = $('checkout-btn');
            const checkoutEr = $('checkout-error');
            const successAlert = $('checkout-success');
            const successMessage = $('checkout-success-message');
            const successPrint = $('checkout-success-print');
            const successPdf = $('checkout-success-pdf');
            const recentSalesList = $('recent-sales-list');
            const toastEl    = $('pos-toast');

            /* ---------------------------------------------------------
             | Helpers
             --------------------------------------------------------- */
            function money(n) {
                return 'RWF ' + (Number(n) || 0).toLocaleString('en-RW', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            function addRecentSale(data) {
                $('recent-sales-empty')?.remove();

                const item = el('li', 'flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between');
                item.dataset.saleId = data.sale_id;

                const details = el('div');
                details.append(
                    el('p', 'font-medium text-gray-900 dark:text-gray-100', @json(__('Sale #')) + data.sale_id),
                    el('p', 'text-sm text-gray-500 dark:text-gray-400', data.sale_date + ' · ' + money(data.total_amount) + ' · ' + (data.payment_status || @json(__('Unpaid'))))
                );

                const actions = el('div', 'flex gap-3 text-sm');
                const printLink = el('a', 'font-medium text-indigo-600 hover:underline dark:text-indigo-400', @json(__('Print')));
                printLink.href = data.print_url;
                printLink.target = '_blank';
                printLink.rel = 'noopener';

                const pdfLink = el('a', 'font-medium text-indigo-600 hover:underline dark:text-indigo-400', @json(__('PDF receipt')));
                pdfLink.href = data.pdf_url;
                actions.append(printLink, pdfLink);
                item.append(details, actions);
                recentSalesList.prepend(item);

                while (recentSalesList.children.length > 5) {
                    recentSalesList.lastElementChild.remove();
                }
            }

            function el(tag, className, text) {
                const node = document.createElement(tag);
                if (className) node.className = className;
                if (text !== undefined) node.textContent = text;
                return node;
            }

            const PLACEHOLDER_SVG =
                '<svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">' +
                '<rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.6"/>' +
                '<circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.6"/>' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m21 15-5-5L5 21"/></svg>';

            let toastTimer = null;
            function toast(message, isError) {
                toastEl.textContent = message;
                toastEl.classList.remove('hidden', 'bg-gray-900', 'bg-red-600');
                toastEl.classList.add(isError ? 'bg-red-600' : 'bg-gray-900');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toastEl.classList.add('hidden'), 1800);
            }

            function store(key, value) {
                try { localStorage.setItem(key, value); } catch (e) { /* ignore */ }
            }
            function read(key) {
                try { return localStorage.getItem(key); } catch (e) { return null; }
            }

            /* ---------------------------------------------------------
             | Filtering
             --------------------------------------------------------- */
            function filtered() {
                const q = searchEl.value.trim().toLowerCase();
                const hideOut = hideOutEl.checked;

                return products.filter(p => {
                    if (hideOut && p.stock <= 0) return false;
                    if (activeCategory !== 'all' && String(p.category_id) !== activeCategory) return false;
                    if (!q) return true;

                    return p.name.toLowerCase().includes(q)
                        || (p.sku && p.sku.toLowerCase().includes(q))
                        || (p.barcode && p.barcode.toLowerCase().includes(q));
                });
            }

            /* ---------------------------------------------------------
             | Categories
             --------------------------------------------------------- */
            function renderCategories() {
                catsEl.replaceChildren();

                const items = [{ id: 'all', name: @json(__('All')) }].concat(
                    categories.map(c => ({ id: String(c.id), name: c.name }))
                );

                items.forEach(c => {
                    const active = activeCategory === c.id;

                    const btn = el(
                        'button',
                        'shrink-0 rounded-full border px-3 py-1.5 text-xs font-medium transition ' + (active
                            ? 'border-indigo-600 bg-indigo-600 text-white'
                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'),
                        c.name
                    );
                    btn.type = 'button';
                    btn.dataset.cat = c.id;
                    catsEl.appendChild(btn);
                });
            }

            /* ---------------------------------------------------------
             | Product cards (square, clean image on top, text strip below, 3D)
             --------------------------------------------------------- */
            function buildCard(p) {
                const out = p.stock <= 0;
                const inCart = cart.get(p.id) || 0;
                const low = !out && p.minimum_stock > 0 && p.stock <= p.minimum_stock;

                const scene = el('div', 'pos-scene');

                const card = el(
                    'div',
                    'pos-card' + (out ? ' is-out' : '') + (p.id === lastAddedId ? ' pos-pop' : '')
                );
                card.setAttribute('role', 'button');
                card.setAttribute('aria-disabled', out ? 'true' : 'false');
                card.setAttribute('aria-label', p.name + ', ' + money(p.price));
                card.tabIndex = out ? -1 : 0;
                card.dataset.id = p.id;
                card.style.setProperty('--hue', String((Number(p.id) * 47) % 360));

                /* Layer 0: image fills the whole card */
                const media = el('div', 'pos-media');

                if (p.image) {
                    const img = document.createElement('img');
                    img.src = p.image;
                    img.alt = '';
                    img.loading = 'lazy';
                    img.decoding = 'async';
                    img.draggable = false;
                    img.addEventListener('error', () => {
                        img.remove();
                        media.prepend(el('span', 'pos-initial', (p.name || '?').charAt(0).toUpperCase()));
                    });
                    media.appendChild(img);
                } else {
                    media.appendChild(el('span', 'pos-initial', (p.name || '?').charAt(0).toUpperCase()));
                }

                media.appendChild(el('div', 'pos-shine'));
                card.appendChild(media);

                /* Floating: stock chip (top-left) */
                const chip = el('span', 'pos-chip pos-float');
                chip.style.setProperty('--z', '45px');
                chip.style.left = '.6rem';
                chip.style.top = '.6rem';
                chip.appendChild(el('span', 'pos-dot ' + (out ? 'none' : low ? 'low' : 'ok')));
                chip.appendChild(document.createTextNode(
                    out
                        ? @json(__('Out of stock'))
                        : Number(p.stock).toLocaleString('en-RW', { maximumFractionDigits: 2 }) + ' ' + @json(__('left'))
                ));
                card.appendChild(chip);

                /* Floating: quantity stepper (top-right) once in cart */
                if (inCart > 0) {
                    const stepper = el('div', 'pos-stepper pos-float');
                    stepper.style.setProperty('--z', '70px');
                    stepper.style.right = '.5rem';
                    stepper.style.top = '.5rem';

                    const minus = el('button', 'pos-step-btn', '−');
                    minus.type = 'button';
                    minus.dataset.cardAct = 'dec';
                    minus.setAttribute('aria-label', @json(__('Remove one')));

                    const count = el('span', 'pos-step-count', String(inCart));

                    const plus = el('button', 'pos-step-btn', '+');
                    plus.type = 'button';
                    plus.dataset.cardAct = 'inc';
                    plus.setAttribute('aria-label', @json(__('Add one')));

                    stepper.append(minus, count, plus);
                    card.appendChild(stepper);
                }

                /* Price tag floats on the seam between image and text strip */
                card.appendChild(el('div', 'pos-price', money(p.price)));

                /* Text strip below the image: name + SKU */
                const info = el('div', 'pos-info');
                info.appendChild(el('p', 'pos-name', p.name));
                if (p.sku) {
                    info.appendChild(el('p', 'pos-sku', p.sku));
                }
                card.appendChild(info);

                scene.appendChild(card);
                return scene;
            }

            function renderGrid() {
                const list = filtered();
                const shown = list.slice(0, MAX_CARDS);

                gridEl.replaceChildren();

                const frag = document.createDocumentFragment();
                shown.forEach(p => frag.appendChild(buildCard(p)));
                gridEl.appendChild(frag);

                lastAddedId = null;

                emptyEl.classList.toggle('hidden', list.length > 0);
                moreEl.classList.toggle('hidden', list.length <= MAX_CARDS);
            }

            /* ---------------------------------------------------------
             | Cart
             --------------------------------------------------------- */
            function cartTotalValue() {
                let total = 0;
                cart.forEach((qty, id) => {
                    const p = byId.get(id);
                    if (p) total += p.price * qty;
                });
                return Math.round(total * 100) / 100;
            }

            function addToCart(p, qty) {
                qty = qty || 1;

                if (p.stock <= 0) {
                    toast(p.name + ': ' + @json(__('out of stock')), true);
                    return false;
                }

                const next = (cart.get(p.id) || 0) + qty;

                if (next > p.stock) {
                    toast(@json(__('Only')) + ' ' + p.stock + ' ' + @json(__('in stock')) + ' — ' + p.name, true);
                    return false;
                }

                cart.set(p.id, next);
                lastAddedId = p.id;
                renderCart();
                renderGrid();
                toast('+ ' + p.name);
                return true;
            }

            function setQty(id, qty) {
                const p = byId.get(id);
                if (!p) return;

                if (!(qty > 0)) {
                    cart.delete(id);
                } else if (qty > p.stock) {
                    cart.set(id, p.stock);
                    toast(@json(__('Only')) + ' ' + p.stock + ' ' + @json(__('in stock')), true);
                } else {
                    cart.set(id, qty);
                }

                renderCart();
                renderGrid();
            }

            function renderCart() {
                cartItems.replaceChildren();

                let lines = 0;

                cart.forEach((qty, id) => {
                    const p = byId.get(id);
                    if (!p) return;
                    lines += 1;

                    const row = el('div', 'flex gap-3 px-4 py-3');

                    // thumb
                    const thumb = el('div', 'flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-50 dark:bg-gray-900');
                    if (p.image) {
                        const img = document.createElement('img');
                        img.src = p.image;
                        img.alt = '';
                        img.className = 'h-full w-full object-cover';
                        thumb.appendChild(img);
                    } else {
                        thumb.innerHTML = PLACEHOLDER_SVG.replace('h-10 w-10', 'h-6 w-6');
                    }
                    row.appendChild(thumb);

                    // details
                    const body = el('div', 'min-w-0 flex-1');
                    body.appendChild(el('p', 'truncate text-sm font-medium text-gray-900 dark:text-gray-100', p.name));
                    body.appendChild(el('p', 'text-xs text-gray-500 dark:text-gray-400', money(p.price)));

                    const controls = el('div', 'mt-1.5 flex items-center gap-1.5');

                    const minus = el('button', 'h-7 w-7 rounded-md border border-gray-300 text-sm font-bold text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700', '−');
                    minus.type = 'button'; minus.dataset.act = 'dec'; minus.dataset.id = id;

                    const input = el('input', 'h-7 w-14 rounded-md border-gray-300 px-1 text-center text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100');
                    input.type = 'number'; input.min = '0'; input.step = '1'; input.value = qty;
                    input.dataset.act = 'set'; input.dataset.id = id;

                    const plus = el('button', 'h-7 w-7 rounded-md border border-gray-300 text-sm font-bold text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700', '+');
                    plus.type = 'button'; plus.dataset.act = 'inc'; plus.dataset.id = id;

                    controls.append(minus, input, plus);
                    body.appendChild(controls);
                    row.appendChild(body);

                    // line total + remove
                    const side = el('div', 'flex shrink-0 flex-col items-end justify-between');
                    side.appendChild(el('span', 'text-sm font-semibold tabular-nums text-gray-900 dark:text-white', money(p.price * qty)));

                    const rm = el('button', 'text-xs text-red-600 hover:text-red-700', @json(__('Remove')));
                    rm.type = 'button'; rm.dataset.act = 'rm'; rm.dataset.id = id;
                    side.appendChild(rm);

                    row.appendChild(side);
                    cartItems.appendChild(row);
                });

                const total = cartTotalValue();
                const totalUnits = Array.from(cart.values()).reduce((a, b) => a + b, 0);

                cartEmpty.classList.toggle('hidden', lines > 0);
                cartTotal.textContent = money(total);
                cartCount.textContent = '(' + totalUnits + ')';
                cartCountM.textContent = String(totalUnits);
                clearBtn.disabled = lines === 0;
                checkoutBt.disabled = lines === 0;

                updateChange();
            }

            function updateChange() {
                const total = cartTotalValue();
                const raw = payRecv.value.trim();
                const received = raw === '' ? total : (parseFloat(raw) || 0);
                const diff = received - total;

                if (diff >= 0) {
                    changeLbl.textContent = @json(__('Change'));
                    changeVal.textContent = money(diff);
                    changeVal.classList.remove('text-red-600');
                } else {
                    changeLbl.textContent = @json(__('Balance due'));
                    changeVal.textContent = money(-diff);
                    changeVal.classList.add('text-red-600');
                }
            }

            /* ---------------------------------------------------------
             | Events
             --------------------------------------------------------- */

            // Category chips
            catsEl.addEventListener('click', function (e) {
                const btn = e.target.closest('button[data-cat]');
                if (!btn) return;
                activeCategory = btn.dataset.cat;
                renderCategories();
                renderGrid();
            });

            // Product cards: stepper buttons first, then the card itself
            gridEl.addEventListener('click', function (e) {
                const stepBtn = e.target.closest('[data-card-act]');

                if (stepBtn) {
                    const card = stepBtn.closest('.pos-card');
                    if (!card) return;
                    const id = Number(card.dataset.id);
                    const qty = cart.get(id) || 0;
                    setQty(id, stepBtn.dataset.cardAct === 'inc' ? qty + 1 : qty - 1);
                    return;
                }

                const card = e.target.closest('.pos-card');
                if (!card || card.getAttribute('aria-disabled') === 'true') return;

                const p = byId.get(Number(card.dataset.id));
                if (p) addToCart(p, 1);
            });

            // Keyboard: Enter / Space on a focused card adds it
            gridEl.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' && e.key !== ' ') return;

                const card = e.target.closest('.pos-card');
                if (!card || e.target !== card) return;

                e.preventDefault();
                if (card.getAttribute('aria-disabled') === 'true') return;

                const p = byId.get(Number(card.dataset.id));
                if (p) addToCart(p, 1);
            });

            // 3D tilt that follows the mouse (skipped for touch and reduced motion)
            if (!reduceMotion) {
                const MAX_TILT = 14;

                gridEl.addEventListener('pointermove', function (e) {
                    if (e.pointerType !== 'mouse') return;

                    const scene = e.target.closest('.pos-scene');
                    if (!scene) return;

                    const card = scene.firstElementChild;
                    if (!card || card.classList.contains('is-out')) return;

                    // Measure the un-tilted scene so the maths stays stable
                    const rect = scene.getBoundingClientRect();
                    const px = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
                    const py = Math.min(1, Math.max(0, (e.clientY - rect.top) / rect.height));

                    card.classList.add('is-tilting');
                    card.style.setProperty('--ry', ((px - 0.5) * 2 * MAX_TILT).toFixed(2) + 'deg');
                    card.style.setProperty('--rx', ((0.5 - py) * 2 * MAX_TILT).toFixed(2) + 'deg');
                    card.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
                    card.style.setProperty('--my', (py * 100).toFixed(1) + '%');
                });

                gridEl.addEventListener('pointerout', function (e) {
                    const scene = e.target.closest('.pos-scene');
                    if (!scene || scene.contains(e.relatedTarget)) return;

                    const card = scene.firstElementChild;
                    if (!card) return;

                    card.classList.remove('is-tilting');
                    card.style.setProperty('--rx', '0deg');
                    card.style.setProperty('--ry', '0deg');
                });
            }

            // Live search
            searchEl.addEventListener('input', renderGrid);
            hideOutEl.addEventListener('change', renderGrid);

            // Auto-select: Enter / barcode scanner
            searchEl.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();

                if (!autoEl.checked) return;

                const q = searchEl.value.trim().toLowerCase();
                if (!q) return;

                // 1) exact barcode / SKU match
                let match = products.find(p =>
                    (p.barcode && p.barcode.toLowerCase() === q) ||
                    (p.sku && p.sku.toLowerCase() === q)
                );

                // 2) otherwise the only product left in the results
                if (!match) {
                    const list = filtered();
                    if (list.length === 1) match = list[0];
                }

                if (!match) {
                    toast(@json(__('No single matching product found.')), true);
                    return;
                }

                if (addToCart(match, 1)) {
                    searchEl.value = '';
                    renderGrid();
                }
            });

            autoEl.addEventListener('change', function () {
                autoHint.classList.toggle('hidden', !autoEl.checked);
                store('pos_auto_select', autoEl.checked ? '1' : '0');
                searchEl.focus();
            });

            // Cart controls (delegated)
            cartItems.addEventListener('click', function (e) {
                const btn = e.target.closest('button[data-act]');
                if (!btn) return;

                const id = Number(btn.dataset.id);
                const qty = cart.get(id) || 0;

                if (btn.dataset.act === 'inc') setQty(id, qty + 1);
                if (btn.dataset.act === 'dec') setQty(id, qty - 1);
                if (btn.dataset.act === 'rm')  setQty(id, 0);
            });

            cartItems.addEventListener('change', function (e) {
                const input = e.target.closest('input[data-act="set"]');
                if (!input) return;
                setQty(Number(input.dataset.id), parseInt(input.value, 10) || 0);
            });

            clearBtn.addEventListener('click', function () {
                cart.clear();
                payRecv.value = '';
                renderCart();
                renderGrid();
            });

            payRecv.addEventListener('input', updateChange);

            // Checkout
            checkoutBt.addEventListener('click', async function () {
                if (cart.size === 0) return;

                checkoutEr.classList.add('hidden');
                successAlert.classList.add('hidden');
                checkoutBt.disabled = true;

                const total = cartTotalValue();
                const raw = payRecv.value.trim();

                const payload = {
                    items: Array.from(cart.entries()).map(([id, qty]) => ({
                        product_id: id,
                        quantity: qty
                    })),
                    payment_method: payMethod.value,
                    amount_paid: raw === '' ? total : parseFloat(raw)
                };

                try {
                    const res = await fetch(CHECKOUT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || data.success === false) {
                        let message = data.message || @json(__('The sale could not be completed.'));

                        if (data.errors) {
                            message = Object.values(data.errors).flat().join(' ');
                        }

                        checkoutEr.textContent = message;
                        checkoutEr.classList.remove('hidden');
                        checkoutBt.disabled = false;
                        return;
                    }

                    // Update local stock so cards stay accurate
                    cart.forEach((qty, id) => {
                        const p = byId.get(id);
                        if (p) p.stock = Math.max(0, p.stock - qty);
                    });

                    cart.clear();
                    payRecv.value = '';
                    renderCart();
                    renderGrid();
                    successMessage.textContent = data.message || @json(__('Sale completed.'));
                    successPrint.href = data.print_url;
                    successPdf.href = data.pdf_url;
                    successAlert.classList.remove('hidden');
                    addRecentSale(data);
                    toast(data.message || @json(__('Sale completed.')));
                    searchEl.focus();

                } catch (err) {
                    checkoutEr.textContent = @json(__('Network error. Please try again.'));
                    checkoutEr.classList.remove('hidden');
                    checkoutBt.disabled = false;
                }
            });

            /* ---------------------------------------------------------
             | Init
             --------------------------------------------------------- */
            autoEl.checked = read('pos_auto_select') === '1';
            autoHint.classList.toggle('hidden', !autoEl.checked);

            renderCategories();
            renderGrid();
            renderCart();
        });
    </script>

</x-app-layout>