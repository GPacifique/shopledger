<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-lg font-bold text-white shadow-lg">
                    {{ mb_strtoupper(mb_substr($shop->business_name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-xl font-bold leading-tight text-gray-900 dark:text-gray-100 sm:text-2xl">
                        {{ $shop->business_name }}
                    </h2>
                    <p class="truncate text-sm text-gray-500 dark:text-gray-400">{{ __('Take orders, print receipts, send bills') }}</p>
                </div>
            </div>
            <div class="hidden shrink-0 text-right sm:block">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Waiter') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $statusMeta = [
            'pending'   => ['Pending',   'st-pending'],
            'approved'  => ['Approved',  'st-approved'],
            'completed' => ['Completed', 'st-completed'],
            'cancelled' => ['Cancelled', 'st-cancelled'],
        ];

        $tiles = [
            ['all',       __('All orders'), $pendingOrders + $approvedOrders + $completedOrders + $cancelledOrders],
            ['pending',   __('Pending'),    $pendingOrders],
            ['approved',  __('Approved'),   $approvedOrders],
            ['completed', __('Completed'),  $completedOrders],
            ['cancelled', __('Cancelled'),  $cancelledOrders],
        ];

        $lineTotal = fn ($item) => (float) $item->quantity * (float) $item->price;
    @endphp

    <div class="wp py-6"
         x-data="waiterPos(@js($products), 'waiter-cart-{{ $shop->id }}', @js((bool) session('new_order')))">
        <div class="mx-auto w-full max-w-[1500px] space-y-6 px-0">

            {{-- ================= Flash messages ================= --}}
            @if(session('success'))
                <div class="wp-flash wp-flash-ok" role="status">
                    <p class="font-semibold">{{ session('success') }}</p>

                    @if(session('new_order'))
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a href="{{ route('waiter.orders.receipt', [$shop, session('new_order')]) }}" target="_blank" rel="noopener" class="wp-btn wp-btn-pri wp-btn-sm">
                                {{ __('Print receipt') }}
                            </a>
                            <a href="{{ route('waiter.orders.bill', [$shop, session('new_order')]) }}" class="wp-btn wp-btn-ghost wp-btn-sm">
                                {{ __('Download bill') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if($errors->any())
                <div class="wp-flash wp-flash-err" role="alert">
                    <p class="font-semibold">{{ __('The order was not saved') }}</p>
                    <ul class="mt-1 list-disc space-y-0.5 pl-5 text-sm">
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- ================= Status tiles (also filter the order list) ================= --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach($tiles as [$key, $label, $count])
                    <button type="button"
                            x-on:click="statusFilter = '{{ $key }}'; $nextTick(() => document.getElementById('orders').scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                            :class="statusFilter === '{{ $key }}' ? 'is-on' : ''"
                            class="wp-tile {{ $key !== 'all' ? $statusMeta[$key][1] : '' }}">
                        <span class="wp-tile-count">{{ $count }}</span>
                        <span class="wp-tile-label">{{ $label }}</span>
                    </button>
                @endforeach
            </div>


            {{-- ================= Products + ticket ================= --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_25rem]">

                {{-- ---------- Products ---------- --}}
                <section class="min-w-0" aria-labelledby="products-title">
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <h3 id="products-title" class="mr-auto text-xl font-bold wp-ink">{{ __('Menu') }}</h3>

                        <label class="wp-search">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                            </svg>
                            <input type="search" x-model.debounce.150ms="q" placeholder="{{ __('Search products') }}" aria-label="{{ __('Search products') }}">
                        </label>

                        <label class="wp-check">
                            <input type="checkbox" x-model="inStockOnly">
                            <span>{{ __('In stock only') }}</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 xl:grid-cols-4">
                        <template x-for="p in filtered" :key="p.id">
                            <button type="button" class="wp-prod"
                                    :class="{ 'is-out': soldOut(p), 'is-in': cart[p.id] }"
                                    :disabled="soldOut(p)"
                                    x-on:click="add(p)"
                                    :aria-label="'Add ' + p.name + ' to order'">
                                <span class="wp-prod-media">
                                    <span class="wp-prod-fallback" x-text="p.name.charAt(0).toUpperCase()"></span>
                                    <template x-if="p.image">
                                        <img :src="p.image" :alt="p.name" loading="lazy" decoding="async"
                                             x-on:error="$el.remove()">
                                    </template>

                                    <span class="wp-prod-qty" x-show="cart[p.id]" x-text="cart[p.id]"></span>

                                    <span class="wp-prod-flag" x-show="soldOut(p)">{{ __('Sold out') }}</span>
                                    <span class="wp-prod-flag wp-prod-flag-low" x-show="!soldOut(p) && p.stock !== null && p.stock <= 5"
                                          x-text="p.stock + ' left'"></span>
                                </span>

                                <span class="wp-prod-body">
                                    <span class="wp-prod-name" x-text="p.name"></span>
                                    <span class="wp-prod-price" x-text="fmt(p.price)"></span>
                                </span>
                            </button>
                        </template>
                    </div>

                    <p x-show="!products.length" class="wp-empty">
                        {{ __('No products in this shop yet. Ask the owner to add products and images.') }}
                    </p>
                    <p x-show="products.length && !filtered.length" x-cloak class="wp-empty">
                        {{ __('No product matches your search.') }}
                    </p>
                </section>


                {{-- ---------- Order ticket ---------- --}}
                <aside id="ticket" class="lg:sticky lg:top-4 lg:self-start" aria-labelledby="ticket-title">
                    <form method="POST" action="{{ route('waiter.orders.store', $shop) }}"
                          x-on:submit="if (!lines.length) { $event.preventDefault(); }"
                          class="wp-ticket">
                        @csrf

                        <div class="wp-ticket-top">
                            <h3 id="ticket-title" class="text-lg font-bold wp-ink">{{ __('Current order') }}</h3>
                            <button type="button" class="wp-link" x-show="lines.length" x-on:click="clear()">{{ __('Clear') }}</button>
                        </div>

                        <div class="grid grid-cols-2 gap-3 px-5 pb-3">
                            <label class="wp-field">
                                <span>{{ __('Table') }}</span>
                                <input type="text" name="table_number" maxlength="20" value="{{ old('table_number') }}" placeholder="{{ __('e.g. 4') }}" class="wp-input">
                            </label>

                            <label class="wp-field">
                                <span>{{ __('Customer') }}</span>
                                <select name="customer_id" class="wp-input">
                                    <option value="">{{ __('Walk-in') }}</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="wp-lines" role="list">
                            <template x-for="(l, i) in lines" :key="l.p.id">
                                <div class="wp-line" role="listitem">
                                    <input type="hidden" :name="'items[' + i + '][product_id]'" :value="l.p.id">
                                    <input type="hidden" :name="'items[' + i + '][quantity]'" :value="l.qty">

                                    <span class="wp-thumb">
                                        <span x-text="l.p.name.charAt(0).toUpperCase()"></span>
                                        <template x-if="l.p.image">
                                            <img :src="l.p.image" alt="" loading="lazy" x-on:error="$el.remove()">
                                        </template>
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold wp-ink" x-text="l.p.name"></span>
                                        <span class="block text-xs wp-muted" x-text="fmt(l.p.price)"></span>
                                    </span>

                                    <span class="wp-step">
                                        <button type="button" x-on:click="dec(l.p)" aria-label="{{ __('Remove one') }}">&minus;</button>
                                        <input type="number" inputmode="numeric" min="1" max="999"
                                               :value="l.qty" x-on:change="setQty(l.p, $event.target.value); $event.target.value = cart[l.p.id] || 1"
                                               aria-label="{{ __('Quantity') }}">
                                        <button type="button" x-on:click="add(l.p)" :disabled="l.p.stock !== null && l.qty >= l.p.stock" aria-label="{{ __('Add one') }}">+</button>
                                    </span>

                                    <span class="wp-line-total" x-text="fmt(l.p.price * l.qty)"></span>
                                </div>
                            </template>

                            <p x-show="!lines.length" class="wp-ticket-empty">
                                {{ __('Tap a product to start an order.') }}
                            </p>
                        </div>

                        <div class="px-5 pb-3 pt-2" x-show="lines.length">
                            <label class="wp-field">
                                <span>{{ __('Note for the kitchen') }}</span>
                                <textarea name="notes" rows="2" maxlength="500" class="wp-input" placeholder="{{ __('No onions, extra sauce...') }}">{{ old('notes') }}</textarea>
                            </label>
                        </div>

                        <div class="wp-ticket-foot">
                            <div class="flex items-baseline justify-between">
                                <span class="text-sm wp-soft"><span x-text="count"></span> {{ __('items') }}</span>
                                <span class="wp-total" x-text="fmt(total)"></span>
                            </div>

                            <button type="submit" class="wp-btn wp-btn-pri wp-btn-lg w-full" :disabled="!lines.length">
                                {{ __('Place order') }}
                            </button>
                        </div>
                    </form>
                </aside>
            </div>


            {{-- ================= My orders ================= --}}
            <section id="orders" class="scroll-mt-4" aria-labelledby="orders-title">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h3 id="orders-title" class="text-xl font-bold wp-ink">{{ __('My orders') }}</h3>

                    <div class="wp-seg" role="tablist" aria-label="{{ __('Filter orders by status') }}">
                        <button type="button" role="tab" :aria-selected="statusFilter === 'all'" :class="statusFilter === 'all' ? 'is-on' : ''" x-on:click="statusFilter = 'all'">{{ __('All') }}</button>
                        @foreach($statusMeta as $key => [$label, $cls])
                            <button type="button" role="tab" :aria-selected="statusFilter === '{{ $key }}'" :class="statusFilter === '{{ $key }}' ? 'is-on' : ''" x-on:click="statusFilter = '{{ $key }}'">{{ __($label) }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($orders as $order)
                        @php
                            [$statusLabel, $statusClass] = $statusMeta[$order->status] ?? [ucfirst($order->status), 'st-pending'];
                            $orderTotal = (float) ($order->total_amount ?: $order->items->sum($lineTotal));
                        @endphp

                        <article class="wp-order" x-data="{ open: {{ session('new_order') == $order->id ? 'true' : 'false' }} }"
                                 x-show="statusFilter === 'all' || statusFilter === '{{ $order->status }}'">

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 sm:px-5">
                                <button type="button" class="flex min-w-0 flex-1 items-center gap-3 text-left" x-on:click="open = !open" :aria-expanded="open">
                                    <svg class="h-4 w-4 shrink-0 transition-transform wp-muted" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>

                                    <span class="min-w-0">
                                        <span class="block font-bold wp-ink">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="block truncate text-xs wp-muted">
                                            {{ $order->created_at->format('d M, H:i') }}
                                            @if($order->table_number) &nbsp;|&nbsp; {{ __('Table') }} {{ $order->table_number }} @endif
                                            @if($order->customer) &nbsp;|&nbsp; {{ $order->customer->name }} @endif
                                        </span>
                                    </span>
                                </button>

                                <span class="wp-badge {{ $statusClass }}">{{ __($statusLabel) }}</span>
                                <span class="w-28 text-right font-bold wp-ink">{{ rwf($orderTotal) }}</span>

                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('waiter.orders.receipt', [$shop, $order]) }}" target="_blank" rel="noopener" class="wp-btn wp-btn-ghost wp-btn-sm">{{ __('Receipt') }}</a>
                                    <a href="{{ route('waiter.orders.bill', [$shop, $order]) }}" class="wp-btn wp-btn-ghost wp-btn-sm">{{ __('Bill') }}</a>

                                    @if($order->status === 'pending')
                                        <form method="POST" action="{{ route('waiter.orders.status', [$shop, $order]) }}"
                                              x-on:submit="if (!confirm('{{ __('Cancel this order?') }}')) $event.preventDefault()">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button class="wp-btn wp-btn-danger wp-btn-sm">{{ __('Cancel') }}</button>
                                        </form>
                                    @elseif($order->status === 'approved')
                                        <form method="POST" action="{{ route('waiter.orders.status', [$shop, $order]) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button class="wp-btn wp-btn-pri wp-btn-sm">{{ __('Mark completed') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div x-show="open" x-collapse x-cloak class="wp-order-body">
                                <ul class="divide-y divide-[var(--wp-line)]">
                                    @foreach($order->items as $item)
                                        <li class="flex items-center justify-between gap-3 py-2 text-sm">
                                            <span class="min-w-0 truncate wp-ink">
                                                {{ $item->product->name ?? __('Removed product') }}
                                                <span class="wp-muted">x {{ $item->quantity }}</span>
                                            </span>
                                            <span class="shrink-0 font-medium wp-soft">{{ rwf($lineTotal($item)) }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                @if($order->notes)
                                    <p class="mt-3 rounded-lg px-3 py-2 text-sm wp-note">{{ $order->notes }}</p>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="wp-empty">{{ __('No orders yet. Pick products above to place your first one.') }}</p>
                    @endforelse
                </div>

                <div class="mt-5">{{ $orders->links() }}</div>
            </section>
        </div>

        {{-- Mobile shortcut to the ticket --}}
        <a href="#ticket" class="wp-fab lg:hidden" x-show="count > 0" x-cloak>
            <span><span x-text="count"></span> {{ __('items') }}</span>
            <strong x-text="fmt(total)"></strong>
        </a>
    </div>


    <style>
        [x-cloak] { display: none !important; }

        .wp {
            --wp-card: #ffffff;   --wp-card2: #f3f8f6;
            --wp-ink: #12241f;    --wp-soft: #46605a;   --wp-muted: #7a8f88;
            --wp-line: #e1ebe7;
            --wp-pri: #0f6b57;    --wp-pri-h: #0b5846;  --wp-on-pri: #ffffff;
            --wp-acc: #f0a81c;    --wp-danger: #c8372d;
            --wp-focus: #0f6b57;
        }
        html.dark .wp {
            --wp-card: #13211d;   --wp-card2: #192b26;
            --wp-ink: #e8f2ee;    --wp-soft: #a9bfb8;   --wp-muted: #86998f;
            --wp-line: #243a33;
            --wp-pri: #34c2a2;    --wp-pri-h: #5fd6ba;  --wp-on-pri: #03231b;
            --wp-acc: #f5b942;    --wp-danger: #f2736a;
            --wp-focus: #5fd6ba;
        }

        .wp-ink   { color: var(--wp-ink); }
        .wp-soft  { color: var(--wp-soft); }
        .wp-muted { color: var(--wp-muted); }
        .wp :focus-visible { outline: 2px solid var(--wp-focus); outline-offset: 2px; }

        /* Status tones */
        .st-pending   { --s-bg: #fff3d1; --s-fg: #8a5a00; --s-bd: #f3dc9a; }
        .st-approved  { --s-bg: #dcecff; --s-fg: #1b4f9c; --s-bd: #b9d6fb; }
        .st-completed { --s-bg: #d8f3e6; --s-fg: #11663f; --s-bd: #aee0c7; }
        .st-cancelled { --s-bg: #fde3e1; --s-fg: #a3281f; --s-bd: #f5bcb7; }
        html.dark .st-pending   { --s-bg: rgb(245 185 66 / .14);  --s-fg: #f8cf7a; --s-bd: rgb(245 185 66 / .35); }
        html.dark .st-approved  { --s-bg: rgb(96 165 250 / .14);  --s-fg: #9fc6fb; --s-bd: rgb(96 165 250 / .35); }
        html.dark .st-completed { --s-bg: rgb(52 194 162 / .14);  --s-fg: #7fe0c6; --s-bd: rgb(52 194 162 / .35); }
        html.dark .st-cancelled { --s-bg: rgb(242 115 106 / .14); --s-fg: #f6a39d; --s-bd: rgb(242 115 106 / .35); }

        /* Flash */
        .wp-flash { border-radius: .875rem; padding: .875rem 1rem; border: 1px solid; }
        .wp-flash-ok  { background: #e6f6ee; border-color: #aee0c7; color: #11663f; }
        .wp-flash-err { background: #fde8e6; border-color: #f5bcb7; color: #a3281f; }
        html.dark .wp-flash-ok  { background: rgb(52 194 162 / .12);  border-color: rgb(52 194 162 / .35);  color: #7fe0c6; }
        html.dark .wp-flash-err { background: rgb(242 115 106 / .12); border-color: rgb(242 115 106 / .35); color: #f6a39d; }

        /* Tiles */
        .wp-tile {
            display: flex; flex-direction: column; align-items: flex-start; gap: .125rem;
            padding: .75rem 1rem; text-align: left;
            background: var(--s-bg, var(--wp-card2)); color: var(--s-fg, var(--wp-ink));
            border: 1px solid var(--s-bd, var(--wp-line)); border-radius: .875rem;
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .wp-tile:hover { transform: translateY(-1px); }
        .wp-tile.is-on { box-shadow: 0 0 0 2px var(--s-fg, var(--wp-pri)); }
        .wp-tile-count { font-size: 1.75rem; font-weight: 800; line-height: 1.1; }
        .wp-tile-label { font-size: .8125rem; font-weight: 600; }

        /* Search + checkbox */
        .wp-search {
            display: flex; align-items: center; gap: .5rem; min-width: 14rem;
            padding: .5rem .75rem; border: 1px solid var(--wp-line); border-radius: .75rem;
            background: var(--wp-card); color: var(--wp-muted);
        }
        .wp-search input { flex: 1; min-width: 0; border: 0; background: transparent; padding: 0; color: var(--wp-ink); font-size: .9rem; }
        .wp-search input:focus { outline: none; box-shadow: none; }
        .wp-search:focus-within { border-color: var(--wp-pri); }
        .wp-check { display: inline-flex; align-items: center; gap: .5rem; font-size: .875rem; color: var(--wp-soft); cursor: pointer; }
        .wp-check input { accent-color: var(--wp-pri); width: 1rem; height: 1rem; }

        /* Product tiles: the photo is the hero */
        .wp-prod {
            display: flex; flex-direction: column; text-align: left; overflow: hidden;
            background: var(--wp-card); border: 1px solid var(--wp-line); border-radius: 1.125rem;
            transition: transform .12s ease, border-color .12s ease, box-shadow .12s ease;
        }
        .wp-prod:hover:not(:disabled) { transform: translateY(-2px); border-color: var(--wp-pri); box-shadow: 0 10px 24px -14px rgb(15 107 87 / .55); }
        .wp-prod:active:not(:disabled) { transform: scale(.98); }
        .wp-prod.is-in { border-color: var(--wp-pri); box-shadow: 0 0 0 1px var(--wp-pri); }
        .wp-prod.is-out { opacity: .6; cursor: not-allowed; }
        .wp-prod.is-out img { filter: grayscale(1); }

        .wp-prod-media { position: relative; display: block; aspect-ratio: 4 / 3; background: var(--wp-card2); overflow: hidden; }
        .wp-prod-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .wp-prod-fallback {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            font-size: 2.75rem; font-weight: 800; color: var(--wp-pri); opacity: .35;
            background: repeating-linear-gradient(135deg, transparent 0 14px, rgb(15 107 87 / .06) 14px 28px);
        }
        .wp-prod-qty {
            position: absolute; top: .5rem; right: .5rem; min-width: 1.75rem; height: 1.75rem; padding: 0 .4rem;
            display: flex; align-items: center; justify-content: center; border-radius: 9999px;
            background: var(--wp-pri); color: var(--wp-on-pri); font-weight: 800; font-size: .875rem;
            box-shadow: 0 2px 8px rgb(0 0 0 / .3);
        }
        .wp-prod-flag {
            position: absolute; left: .5rem; bottom: .5rem; padding: .125rem .5rem; border-radius: .5rem;
            background: rgb(18 36 31 / .85); color: #fff; font-size: .75rem; font-weight: 600;
        }
        .wp-prod-flag-low { background: var(--wp-acc); color: #3a2800; }
        .wp-prod-body { display: flex; flex-direction: column; gap: .125rem; padding: .625rem .75rem .75rem; }
        .wp-prod-name { font-weight: 600; font-size: .9rem; line-height: 1.25; color: var(--wp-ink); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.25em; }
        .wp-prod-price { font-weight: 800; color: var(--wp-pri); }

        /* Ticket: a paper order slip with notches above the total */
        .wp-ticket {
            --n: calc(100% - 8.25rem);
            display: flex; flex-direction: column;
            background: var(--wp-card); border: 1px solid var(--wp-line); border-radius: 1.125rem;
            -webkit-mask: radial-gradient(circle .55rem at 0 var(--n), #0000 97%, #000) left / 51% 100% no-repeat,
                          radial-gradient(circle .55rem at 100% var(--n), #0000 97%, #000) right / 51% 100% no-repeat;
                    mask: radial-gradient(circle .55rem at 0 var(--n), #0000 97%, #000) left / 51% 100% no-repeat,
                          radial-gradient(circle .55rem at 100% var(--n), #0000 97%, #000) right / 51% 100% no-repeat;
        }
        .wp-ticket-top { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem .75rem; }
        .wp-lines { max-height: 38vh; min-height: 5rem; overflow-y: auto; padding: 0 1.25rem; border-top: 1px dashed var(--wp-line); }
        .wp-line { display: flex; align-items: center; gap: .625rem; padding: .625rem 0; border-bottom: 1px dashed var(--wp-line); flex-wrap: wrap; }
        .wp-line-total { width: 100%; text-align: right; font-size: .8125rem; font-weight: 700; color: var(--wp-soft); margin-top: -.25rem; }
        .wp-ticket-empty { padding: 1.5rem 0; text-align: center; font-size: .9rem; color: var(--wp-muted); }
        .wp-ticket-foot {
            display: flex; flex-direction: column; justify-content: center; gap: .75rem;
            height: 8.25rem; padding: 0 1.25rem; border-top: 1px dashed var(--wp-line); margin-top: auto;
        }
        .wp-total { font-size: 1.625rem; font-weight: 800; color: var(--wp-ink); }

        .wp-thumb { position: relative; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; flex-shrink: 0; overflow: hidden; border-radius: .625rem; background: var(--wp-card2); color: var(--wp-pri); font-weight: 800; }
        .wp-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }

        .wp-step { display: inline-flex; align-items: center; border: 1px solid var(--wp-line); border-radius: .625rem; overflow: hidden; background: var(--wp-card2); }
        .wp-step button { width: 1.875rem; height: 1.875rem; font-size: 1.125rem; line-height: 1; color: var(--wp-ink); }
        .wp-step button:hover:not(:disabled) { background: var(--wp-line); }
        .wp-step button:disabled { opacity: .35; cursor: not-allowed; }
        .wp-step input { width: 2.25rem; height: 1.875rem; padding: 0; text-align: center; border: 0; background: transparent; color: var(--wp-ink); font-weight: 700; -moz-appearance: textfield; }
        .wp-step input::-webkit-outer-spin-button, .wp-step input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        /* Fields */
        .wp-field { display: flex; flex-direction: column; gap: .25rem; font-size: .8125rem; font-weight: 600; color: var(--wp-soft); }
        .wp-input {
            width: 100%; padding: .5rem .625rem; font-size: .9rem; font-weight: 400;
            background: var(--wp-card2); color: var(--wp-ink);
            border: 1px solid var(--wp-line); border-radius: .625rem;
        }
        .wp-input:focus { border-color: var(--wp-pri); box-shadow: 0 0 0 2px rgb(15 107 87 / .2); outline: none; }

        /* Buttons */
        .wp-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
            padding: .5rem .875rem; border-radius: .75rem; border: 1px solid transparent;
            font-size: .875rem; font-weight: 700; transition: background-color .12s ease, transform .12s ease;
        }
        .wp-btn:active:not(:disabled) { transform: scale(.97); }
        .wp-btn:disabled { opacity: .45; cursor: not-allowed; }
        .wp-btn-sm { padding: .375rem .75rem; font-size: .8125rem; }
        .wp-btn-lg { padding: .75rem 1rem; font-size: 1rem; }
        .wp-btn-pri { background: var(--wp-pri); color: var(--wp-on-pri); }
        .wp-btn-pri:hover:not(:disabled) { background: var(--wp-pri-h); }
        .wp-btn-ghost { background: transparent; color: var(--wp-ink); border-color: var(--wp-line); }
        .wp-btn-ghost:hover { background: var(--wp-card2); }
        .wp-btn-danger { background: transparent; color: var(--wp-danger); border-color: var(--wp-danger); }
        .wp-btn-danger:hover { background: var(--wp-danger); color: #fff; }
        .wp-link { font-size: .8125rem; font-weight: 600; color: var(--wp-danger); }
        .wp-link:hover { text-decoration: underline; }

        /* Orders */
        .wp-seg { display: inline-flex; flex-wrap: wrap; gap: .25rem; padding: .25rem; border-radius: .875rem; background: var(--wp-card2); border: 1px solid var(--wp-line); }
        .wp-seg button { padding: .375rem .75rem; border-radius: .625rem; font-size: .8125rem; font-weight: 600; color: var(--wp-soft); }
        .wp-seg button:hover { color: var(--wp-ink); }
        .wp-seg button.is-on { background: var(--wp-pri); color: var(--wp-on-pri); }

        .wp-order { background: var(--wp-card); border: 1px solid var(--wp-line); border-radius: 1rem; overflow: hidden; }
        .wp-order-body { padding: .25rem 1.25rem 1rem 2.75rem; border-top: 1px dashed var(--wp-line); }
        .wp-badge { display: inline-flex; align-items: center; padding: .25rem .75rem; border-radius: 9999px; font-size: .75rem; font-weight: 700; background: var(--s-bg); color: var(--s-fg); border: 1px solid var(--s-bd); }
        .wp-note { background: var(--wp-card2); color: var(--wp-soft); }
        .wp-empty { padding: 2.5rem 1rem; text-align: center; color: var(--wp-muted); background: var(--wp-card); border: 1px dashed var(--wp-line); border-radius: 1rem; }

        /* Mobile bar */
        .wp-fab {
            position: fixed; left: 1rem; right: 1rem; bottom: calc(1rem + env(safe-area-inset-bottom, 0px)); z-index: 40;
            display: flex; align-items: center; justify-content: space-between;
            padding: .875rem 1.25rem; border-radius: 1rem; background: var(--wp-pri); color: var(--wp-on-pri);
            box-shadow: 0 12px 30px -8px rgb(0 0 0 / .45); font-weight: 600;
        }

        @media (prefers-reduced-motion: reduce) {
            .wp-tile, .wp-prod, .wp-btn { transition: none; }
            .wp-tile:hover, .wp-prod:hover:not(:disabled) { transform: none; }
        }
    </style>


    <script nonce="{{ Vite::cspNonce() }}">
    document.addEventListener('alpine:init', function () {

        Alpine.data('waiterPos', function (products, storageKey, justOrdered) {
            return {
                products: products,
                storageKey: storageKey,
                q: '',
                inStockOnly: false,
                statusFilter: 'all',
                cart: {},

                init() {
                    if (justOrdered) {
                        try { localStorage.removeItem(this.storageKey); } catch (e) {}
                    } else {
                        // Restore an unfinished order after an accidental refresh
                        try {
                            const saved = JSON.parse(localStorage.getItem(this.storageKey) || '{}');
                            Object.keys(saved).forEach((id) => {
                                const p = this.find(id);
                                if (p && !this.soldOut(p) && saved[id] > 0) this.cart[id] = this.clamp(p, saved[id]);
                            });
                        } catch (e) {}
                    }

                    this.$watch('cart', (value) => {
                        try { localStorage.setItem(this.storageKey, JSON.stringify(value)); } catch (e) {}
                    });
                },

                find(id) { return this.products.find((p) => String(p.id) === String(id)); },

                soldOut(p) { return p.stock !== null && p.stock <= 0; },

                clamp(p, qty) {
                    let n = Math.max(1, Math.floor(Number(qty) || 1));
                    if (p.stock !== null) n = Math.min(n, p.stock);
                    return Math.min(n, 999);
                },

                add(p) {
                    if (this.soldOut(p)) return;
                    this.cart[p.id] = this.clamp(p, (this.cart[p.id] || 0) + 1);
                },

                dec(p) {
                    const n = (this.cart[p.id] || 0) - 1;
                    if (n <= 0) { delete this.cart[p.id]; } else { this.cart[p.id] = n; }
                },

                setQty(p, value) {
                    const n = Math.floor(Number(value));
                    if (!n || n <= 0) { delete this.cart[p.id]; return; }
                    this.cart[p.id] = this.clamp(p, n);
                },

                clear() { this.cart = {}; },

                get lines() {
                    return Object.keys(this.cart)
                        .map((id) => ({ p: this.find(id), qty: this.cart[id] }))
                        .filter((l) => l.p && l.qty > 0);
                },

                get count() { return this.lines.reduce((sum, l) => sum + l.qty, 0); },

                get total() { return this.lines.reduce((sum, l) => sum + l.p.price * l.qty, 0); },

                get filtered() {
                    const term = this.q.trim().toLowerCase();
                    return this.products.filter((p) => {
                        if (this.inStockOnly && this.soldOut(p)) return false;
                        return !term || p.name.toLowerCase().includes(term);
                    });
                },

                fmt(value) {
                    return 'RWF ' + Number(value).toLocaleString('en-US', { maximumFractionDigits: 0 });
                },
            };
        });
    });
    </script>
</x-app-layout>