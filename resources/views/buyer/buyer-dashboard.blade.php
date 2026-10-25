<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="truncate text-xl font-bold leading-tight text-gray-900 dark:text-gray-100 sm:text-2xl">
                    {{ __('Marketplace') }}
                </h2>
                <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Products from every verified seller in one place') }}
                </p>
            </div>
            <p class="hidden shrink-0 text-sm font-semibold text-gray-800 dark:text-gray-200 sm:block">{{ auth()->user()->name }}</p>
        </div>
    </x-slot>

    @php
        $hasFilters = collect(['q', 'min_price', 'max_price', 'city', 'seller', 'in_stock'])->contains(fn ($k) => filled(request($k)));
        $sort = request('sort', 'newest');

        $stockNote = function ($stock) {
            if ($stock === null) return null;
            if ($stock <= 0)     return __('Out of stock');
            if ($stock <= 5)     return __('Only :n left', ['n' => $stock]);
            return null;
        };

        $hue = fn ($id) => (($id * 47) % 360);
    @endphp

    <div class="bm py-6"
         x-data="buyerMarket(@js($cards), @js(route('buyer.dashboard')))"
         x-on:keydown.escape.window="close()"
         x-effect="document.body.classList.toggle('overflow-hidden', !!current)">

        <form method="GET" action="{{ route('buyer.dashboard') }}" x-ref="form" class="space-y-6">

            {{-- ================= Search ================= --}}
            <div class="bm-search-band">
                <label class="bm-search-main">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" maxlength="100"
                           placeholder="{{ __('Search products or sellers') }}"
                           aria-label="{{ __('Search products or sellers') }}">
                    <button type="submit" class="bm-btn bm-btn-pri">{{ __('Search') }}</button>
                </label>

                <p class="mt-2 text-sm bm-soft">
                    {{ trans_choice(':count product|:count products', $products->total(), ['count' => number_format($products->total())]) }}
                    {{ __('from') }}
                    {{ trans_choice(':count seller|:count sellers', $sellerOptions->count(), ['count' => $sellerOptions->count()]) }}
                </p>
            </div>


            {{-- ================= Seller banner (when browsing one shop) ================= --}}
            @if($activeSeller)
                @php $s = $activeSeller; @endphp
                <section class="bm-banner" aria-label="{{ __('Seller') }}">
                    <div class="bm-flagbar" aria-hidden="true"></div>
                    <div class="flex flex-wrap items-start gap-4 p-5">
                        @if($s['logo'])
                            <img src="{{ $s['logo'] }}" alt="" class="bm-avatar bm-avatar-lg object-cover">
                        @else
                            <span class="bm-avatar bm-avatar-lg" style="background: hsl({{ $hue($s['id']) }} 45% 38%)">{{ mb_strtoupper(mb_substr($s['name'], 0, 2)) }}</span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xl font-bold bm-ink">{{ $s['name'] }}</h3>
                                <span class="bm-chip bm-chip-ok">{{ __('Verified seller') }}</span>
                                @if($s['registered'])<span class="bm-chip">{{ __('Registered business') }}</span>@endif
                            </div>

                            <dl class="mt-3 grid gap-x-8 gap-y-1 text-sm sm:grid-cols-2">
                                @if($s['type'])    <div class="flex gap-2"><dt class="bm-muted">{{ __('Business type') }}</dt><dd class="bm-ink">{{ $s['type'] }}</dd></div>@endif
                                @if($s['owner'])   <div class="flex gap-2"><dt class="bm-muted">{{ __('Owner') }}</dt><dd class="bm-ink">{{ $s['owner'] }}</dd></div>@endif
                                @if($s['city'] || $s['country'])
                                    <div class="flex gap-2"><dt class="bm-muted">{{ __('Location') }}</dt><dd class="bm-ink">{{ collect([$s['address'], $s['city'], $s['country']])->filter()->map(fn ($v) => ucfirst($v))->implode(', ') }}</dd></div>
                                @endif
                                @if($s['since'])   <div class="flex gap-2"><dt class="bm-muted">{{ __('On the platform since') }}</dt><dd class="bm-ink">{{ $s['since'] }}</dd></div>@endif
                                <div class="flex gap-2"><dt class="bm-muted">{{ __('Products listed') }}</dt><dd class="bm-ink">{{ $s['products'] }}</dd></div>
                            </dl>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($s['phone'])    <a href="tel:{{ preg_replace('/[^\d+]/', '', $s['phone']) }}" class="bm-btn bm-btn-pri bm-btn-sm">{{ __('Call') }} {{ $s['phone'] }}</a>@endif
                                @if($s['whatsapp']) <a href="{{ $s['whatsapp'] }}" target="_blank" rel="noopener" class="bm-btn bm-btn-ghost bm-btn-sm">WhatsApp</a>@endif
                                @if($s['email'])    <a href="mailto:{{ $s['email'] }}" class="bm-btn bm-btn-ghost bm-btn-sm">{{ __('Email') }}</a>@endif
                                <a href="{{ route('buyer.dashboard') }}" class="bm-btn bm-btn-ghost bm-btn-sm">{{ __('Back to all sellers') }}</a>
                            </div>
                        </div>
                    </div>
                </section>
            @endif


            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[16.5rem_minmax(0,1fr)]">

                {{-- ================= Filters ================= --}}
                <aside class="lg:sticky lg:top-4 lg:self-start" aria-label="{{ __('Filters') }}">
                    <button type="button" class="bm-btn bm-btn-ghost w-full lg:hidden" x-on:click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen">
                        {{ __('Filters') }}
                        @if($hasFilters)<span class="bm-dot"></span>@endif
                    </button>

                    <div class="bm-panel mt-3 space-y-4 p-4 lg:mt-0" :class="filtersOpen ? '' : 'hidden lg:block'">
                        <label class="bm-field">
                            <span>{{ __('Seller') }}</span>
                            <select name="seller" class="bm-input" x-on:change="$refs.form.requestSubmit()">
                                <option value="">{{ __('All sellers') }}</option>
                                @foreach($sellerOptions as $opt)
                                    <option value="{{ $opt->id }}" @selected((int) request('seller') === $opt->id)>
                                        {{ $opt->business_name }}@if($opt->city) ({{ ucfirst($opt->city) }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="bm-field">
                            <span>{{ __('Location') }}</span>
                            <select name="city" class="bm-input" x-on:change="$refs.form.requestSubmit()">
                                <option value="">{{ __('Anywhere') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" @selected(strcasecmp((string) request('city'), $city) === 0)>{{ ucfirst($city) }}</option>
                                @endforeach
                            </select>
                        </label>

                        <fieldset>
                            <legend class="bm-legend">{{ __('Price (RWF)') }}</legend>
                            <div class="mt-1 grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" min="0" step="any" value="{{ request('min_price') }}" placeholder="{{ __('Min') }}" class="bm-input" aria-label="{{ __('Minimum price') }}">
                                <input type="number" name="max_price" min="0" step="any" value="{{ request('max_price') }}" placeholder="{{ __('Max') }}" class="bm-input" aria-label="{{ __('Maximum price') }}">
                            </div>
                        </fieldset>

                        <label class="bm-check">
                            <input type="checkbox" name="in_stock" value="1" @checked(request('in_stock')) x-on:change="$refs.form.requestSubmit()">
                            <span>{{ __('In stock only') }}</span>
                        </label>

                        <div class="flex items-center gap-2 pt-1">
                            <button type="submit" class="bm-btn bm-btn-pri flex-1">{{ __('Apply') }}</button>
                            @if($hasFilters || request('sort'))
                                <a href="{{ route('buyer.dashboard') }}" class="bm-btn bm-btn-ghost">{{ __('Reset') }}</a>
                            @endif
                        </div>
                    </div>
                </aside>


                {{-- ================= Products ================= --}}
                <section class="min-w-0" aria-label="{{ __('Products') }}">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <p class="text-sm bm-soft">
                            {{ __('Showing') }} {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} {{ __('of') }} {{ number_format($products->total()) }}
                        </p>

                        <label class="flex items-center gap-2 text-sm bm-soft">
                            <span>{{ __('Sort by') }}</span>
                            <select name="sort" class="bm-input !w-auto" x-on:change="$refs.form.requestSubmit()">
                                <option value="newest"     @selected($sort === 'newest')>{{ __('Newest') }}</option>
                                <option value="price_asc"  @selected($sort === 'price_asc')>{{ __('Price: low to high') }}</option>
                                <option value="price_desc" @selected($sort === 'price_desc')>{{ __('Price: high to low') }}</option>
                                <option value="name"       @selected($sort === 'name')>{{ __('Name') }}</option>
                            </select>
                        </label>
                    </div>

                    @if(count($cards))
                        <div class="grid grid-cols-1 gap-4 min-[480px]:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                            @foreach($cards as $i => $card)
                                @php
                                    $shop = $card['shop'];
                                    $out  = $card['stock'] !== null && $card['stock'] <= 0;
                                    $note = $stockNote($card['stock']);
                                @endphp

                                <article class="bm-card {{ $out ? 'is-out' : '' }}">
                                    <button type="button" class="bm-media" x-on:click="open({{ $i }})" aria-label="{{ __('View details for :name', ['name' => $card['name']]) }}">
                                        <span class="bm-media-fallback">{{ mb_strtoupper(mb_substr($card['name'], 0, 1)) }}</span>
                                        @if($card['image'])
                                            <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}" loading="lazy" decoding="async" x-on:error="$el.remove()">
                                        @endif
                                        @if($note)<span class="bm-flag {{ $out ? '' : 'bm-flag-low' }}">{{ $note }}</span>@endif
                                    </button>

                                    <div class="bm-card-body">
                                        <button type="button" class="bm-name" x-on:click="open({{ $i }})">{{ $card['name'] }}</button>
                                        <p class="bm-price">{{ rwf($card['price']) }}</p>
                                    </div>

                                    @if($shop)
                                        <a href="{{ route('buyer.dashboard', ['seller' => $shop['id']]) }}" class="bm-plate" title="{{ __('See all products from :name', ['name' => $shop['name']]) }}">
                                            <span class="bm-flagbar" aria-hidden="true"></span>

                                            <span class="flex items-center gap-3">
                                                @if($shop['logo'])
                                                    <img src="{{ $shop['logo'] }}" alt="" class="bm-avatar object-cover">
                                                @else
                                                    <span class="bm-avatar" style="background: hsl({{ $hue($shop['id']) }} 45% 38%)">{{ mb_strtoupper(mb_substr($shop['name'], 0, 2)) }}</span>
                                                @endif

                                                <span class="min-w-0">
                                                    <span class="flex items-center gap-1 font-semibold bm-ink">
                                                        <span class="truncate">{{ $shop['name'] }}</span>
                                                        <svg class="h-4 w-4 shrink-0 bm-ok" viewBox="0 0 20 20" fill="currentColor" aria-label="{{ __('Verified seller') }}"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.8 3.8 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                                                    </span>
                                                    @if($shop['city'] || $shop['country'])
                                                        <span class="block truncate text-xs bm-muted">{{ collect([$shop['city'], $shop['country']])->filter()->map(fn ($v) => ucfirst($v))->implode(', ') }}</span>
                                                    @endif
                                                </span>
                                            </span>

                                            <span class="mt-2 flex flex-wrap gap-1.5">
                                                @if($shop['type'])<span class="bm-chip">{{ $shop['type'] }}</span>@endif
                                                <span class="bm-chip">{{ trans_choice(':count product|:count products', $shop['products'], ['count' => $shop['products']]) }}</span>
                                                @if($shop['since'])<span class="bm-chip">{{ __('Since') }} {{ $shop['since'] }}</span>@endif
                                            </span>
                                        </a>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-6">{{ $products->links() }}</div>
                    @else
                        <div class="bm-empty">
                            <p class="font-semibold bm-ink">{{ __('No products match these filters') }}</p>
                            <p class="mt-1 text-sm">{{ __('Try a shorter search, a wider price range, or all sellers.') }}</p>
                            @if($hasFilters)
                                <a href="{{ route('buyer.dashboard') }}" class="bm-btn bm-btn-pri mt-4">{{ __('Reset filters') }}</a>
                            @endif
                        </div>
                    @endif
                </section>
            </div>
        </form>


        {{-- ================= Quick view ================= --}}
        <div x-show="current" x-cloak class="bm-modal" role="dialog" aria-modal="true" aria-labelledby="bm-modal-title"
             x-transition.opacity.duration.120ms>
            <div class="bm-modal-backdrop" x-on:click="close()"></div>

            <div class="bm-modal-box" x-show="current" x-transition.duration.150ms>
                <template x-if="current">
                    <div>
                        <button type="button" class="bm-modal-close" x-ref="closeBtn" x-on:click="close()" aria-label="{{ __('Close') }}">&times;</button>

                        <div class="grid gap-0 md:grid-cols-2">
                            {{-- Product --}}
                            <div class="p-5">
                                <div class="bm-media bm-media-static">
                                    <span class="bm-media-fallback" x-text="current.name.charAt(0).toUpperCase()"></span>
                                    <template x-if="current.image">
                                        <img :src="current.image" :alt="current.name" x-on:error="$el.remove()">
                                    </template>
                                </div>

                                <h3 id="bm-modal-title" class="mt-4 text-xl font-bold bm-ink" x-text="current.name"></h3>
                                <p class="mt-1 text-2xl font-extrabold bm-ink" x-text="fmt(current.price)"></p>
                                <p class="mt-1 text-sm font-semibold" :class="current.stock !== null && current.stock <= 0 ? 'bm-bad' : 'bm-ok'" x-text="stockText(current)"></p>
                                <p class="mt-3 text-sm leading-relaxed bm-soft" x-show="current.description" x-text="current.description"></p>
                            </div>

                            {{-- Seller --}}
                            <div class="bm-seller-panel p-5" x-show="current.shop">
                                <template x-if="current.shop">
                                    <div>
                                        <p class="text-sm font-semibold bm-soft">{{ __('Sold by') }}</p>

                                        <div class="mt-2 flex items-center gap-3">
                                            <template x-if="current.shop.logo">
                                                <img :src="current.shop.logo" alt="" class="bm-avatar bm-avatar-lg object-cover">
                                            </template>
                                            <template x-if="!current.shop.logo">
                                                <span class="bm-avatar bm-avatar-lg" :style="'background: hsl(' + hue(current.shop.id) + ' 45% 38%)'" x-text="current.shop.name.substring(0, 2).toUpperCase()"></span>
                                            </template>

                                            <div class="min-w-0">
                                                <p class="truncate text-lg font-bold bm-ink" x-text="current.shop.name"></p>
                                                <div class="mt-1 flex flex-wrap gap-1.5">
                                                    <span class="bm-chip bm-chip-ok">{{ __('Verified seller') }}</span>
                                                    <span class="bm-chip" x-show="current.shop.registered">{{ __('Registered business') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <dl class="mt-4 space-y-2 text-sm">
                                            <div class="flex gap-3" x-show="current.shop.owner"><dt class="w-28 shrink-0 bm-muted">{{ __('Owner') }}</dt><dd class="bm-ink" x-text="current.shop.owner"></dd></div>
                                            <div class="flex gap-3" x-show="current.shop.type"><dt class="w-28 shrink-0 bm-muted">{{ __('Business type') }}</dt><dd class="bm-ink" x-text="current.shop.type"></dd></div>
                                            <div class="flex gap-3" x-show="location(current.shop)"><dt class="w-28 shrink-0 bm-muted">{{ __('Location') }}</dt><dd class="bm-ink" x-text="location(current.shop)"></dd></div>
                                            <div class="flex gap-3" x-show="current.shop.since"><dt class="w-28 shrink-0 bm-muted">{{ __('Member since') }}</dt><dd class="bm-ink" x-text="current.shop.since"></dd></div>
                                            <div class="flex gap-3"><dt class="w-28 shrink-0 bm-muted">{{ __('Products listed') }}</dt><dd class="bm-ink" x-text="current.shop.products"></dd></div>
                                        </dl>

                                        <div class="mt-5 flex flex-wrap gap-2">
                                            <a class="bm-btn bm-btn-pri" x-show="current.shop.phone" :href="'tel:' + (current.shop.phone || '').replace(/[^\d+]/g, '')">{{ __('Call seller') }}</a>
                                            <a class="bm-btn bm-btn-ghost" x-show="current.shop.whatsapp" :href="current.shop.whatsapp" target="_blank" rel="noopener">WhatsApp</a>
                                            <a class="bm-btn bm-btn-ghost" x-show="current.shop.email" :href="'mailto:' + current.shop.email">{{ __('Email') }}</a>
                                        </div>

                                        <a class="bm-btn bm-btn-ghost mt-3 w-full" :href="sellerUrl(current)">{{ __('See all products from this seller') }}</a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>


    <style>
        [x-cloak] { display: none !important; }

        .bm {
            --bm-card: #ffffff;  --bm-card2: #f2f7fb;
            --bm-ink: #0f2238;   --bm-soft: #4a6078;   --bm-muted: #75899e;
            --bm-line: #dfe8f1;
            --bm-pri: #0b5fa5;   --bm-pri-h: #094a82;  --bm-on-pri: #ffffff;
            --bm-blue: #00a1de;  --bm-sun: #fad201;    --bm-green: #20603d;
            --bm-ok: #1d7a3e;    --bm-ok-bg: #def3e5;  --bm-bad: #b3261e;
            --bm-focus: #0b5fa5;
        }
        html.dark .bm {
            --bm-card: #101c2b;  --bm-card2: #16263a;
            --bm-ink: #e6eef7;   --bm-soft: #a7bad0;   --bm-muted: #86a0b8;
            --bm-line: #213349;
            --bm-pri: #5aaeea;   --bm-pri-h: #82c3f0;  --bm-on-pri: #04223a;
            --bm-ok: #5fd38a;    --bm-ok-bg: rgb(95 211 138 / .14); --bm-bad: #f2736a;
            --bm-focus: #82c3f0;
        }

        .bm-ink   { color: var(--bm-ink); }
        .bm-soft  { color: var(--bm-soft); }
        .bm-muted { color: var(--bm-muted); }
        .bm-ok    { color: var(--bm-ok); }
        .bm-bad   { color: var(--bm-bad); }
        .bm :focus-visible { outline: 2px solid var(--bm-focus); outline-offset: 2px; }

        /* Rwandan flag colours as a thin stripe: the one recurring motif */
        .bm-flagbar { display: block; height: 3px; background: linear-gradient(90deg, var(--bm-blue) 0 50%, var(--bm-sun) 50% 75%, var(--bm-green) 75% 100%); }

        /* Search */
        .bm-search-band { padding: 1.25rem; border-radius: 1.125rem; background: var(--bm-card); border: 1px solid var(--bm-line); }
        .bm-search-main {
            display: flex; align-items: center; gap: .75rem; padding: .375rem .375rem .375rem 1rem;
            border: 2px solid var(--bm-line); border-radius: .875rem; background: var(--bm-card2); color: var(--bm-muted);
        }
        .bm-search-main:focus-within { border-color: var(--bm-pri); }
        .bm-search-main input { flex: 1; min-width: 0; border: 0; background: transparent; padding: .5rem 0; color: var(--bm-ink); font-size: 1rem; }
        .bm-search-main input:focus { outline: none; box-shadow: none; }

        /* Panels, fields */
        .bm-panel { background: var(--bm-card); border: 1px solid var(--bm-line); border-radius: 1rem; }
        .bm-field { display: flex; flex-direction: column; gap: .25rem; font-size: .8125rem; font-weight: 600; color: var(--bm-soft); }
        .bm-legend { font-size: .8125rem; font-weight: 600; color: var(--bm-soft); }
        .bm-input {
            width: 100%; padding: .5rem .625rem; font-size: .9rem; font-weight: 400;
            background: var(--bm-card2); color: var(--bm-ink); border: 1px solid var(--bm-line); border-radius: .625rem;
        }
        .bm-input:focus { border-color: var(--bm-pri); box-shadow: 0 0 0 2px rgb(11 95 165 / .2); outline: none; }
        .bm-check { display: inline-flex; align-items: center; gap: .5rem; font-size: .875rem; color: var(--bm-soft); cursor: pointer; }
        .bm-check input { accent-color: var(--bm-pri); width: 1rem; height: 1rem; }
        .bm-dot { display: inline-block; width: .5rem; height: .5rem; margin-left: .5rem; border-radius: 9999px; background: var(--bm-sun); }

        /* Buttons */
        .bm-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
            padding: .5rem .9rem; border-radius: .75rem; border: 1px solid transparent;
            font-size: .875rem; font-weight: 700; text-align: center; transition: background-color .12s ease;
        }
        .bm-btn-sm { padding: .375rem .75rem; font-size: .8125rem; }
        .bm-btn-pri { background: var(--bm-pri); color: var(--bm-on-pri); }
        .bm-btn-pri:hover { background: var(--bm-pri-h); }
        .bm-btn-ghost { background: transparent; color: var(--bm-ink); border-color: var(--bm-line); }
        .bm-btn-ghost:hover { background: var(--bm-card2); }

        /* Chips */
        .bm-chip { display: inline-flex; align-items: center; padding: .125rem .5rem; border-radius: 9999px; font-size: .6875rem; font-weight: 600; background: var(--bm-card2); color: var(--bm-soft); border: 1px solid var(--bm-line); }
        .bm-chip-ok { background: var(--bm-ok-bg); color: var(--bm-ok); border-color: transparent; }

        /* Avatar */
        .bm-avatar { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; flex-shrink: 0; border-radius: 9999px; color: #fff; font-size: .8125rem; font-weight: 800; }
        .bm-avatar-lg { width: 3.5rem; height: 3.5rem; font-size: 1.05rem; }

        /* Seller banner */
        .bm-banner { overflow: hidden; border-radius: 1.125rem; background: var(--bm-card); border: 1px solid var(--bm-line); }

        /* Product card */
        .bm-card { display: flex; flex-direction: column; overflow: hidden; background: var(--bm-card); border: 1px solid var(--bm-line); border-radius: 1.125rem; transition: border-color .12s ease, box-shadow .12s ease; }
        .bm-card:hover { border-color: var(--bm-pri); box-shadow: 0 12px 28px -18px rgb(11 95 165 / .6); }
        .bm-card.is-out .bm-media img { filter: grayscale(1); opacity: .7; }

        .bm-media { position: relative; display: block; width: 100%; aspect-ratio: 4 / 3; overflow: hidden; background: var(--bm-card2); }
        .bm-media-static { border-radius: .875rem; }
        .bm-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .bm-media-fallback {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            font-size: 2.75rem; font-weight: 800; color: var(--bm-pri); opacity: .3;
            background: repeating-linear-gradient(135deg, transparent 0 14px, rgb(11 95 165 / .07) 14px 28px);
        }
        .bm-flag { position: absolute; left: .5rem; bottom: .5rem; padding: .125rem .5rem; border-radius: .5rem; background: rgb(15 34 56 / .88); color: #fff; font-size: .75rem; font-weight: 600; }
        .bm-flag-low { background: var(--bm-sun); color: #3b3000; }

        .bm-card-body { padding: .75rem 1rem .5rem; }
        .bm-name { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5em; width: 100%; text-align: left; font-weight: 600; line-height: 1.25; color: var(--bm-ink); }
        .bm-name:hover { color: var(--bm-pri); }
        .bm-price { margin-top: .25rem; font-size: 1.2rem; font-weight: 800; color: var(--bm-ink); }

        /* The seller plate: who you are buying from */
        .bm-plate { position: relative; display: block; margin-top: auto; padding: .875rem 1rem 1rem; background: var(--bm-card2); border-top: 1px solid var(--bm-line); }
        .bm-plate .bm-flagbar { position: absolute; top: 0; left: 0; right: 0; }
        .bm-plate:hover { background: var(--bm-card); }
        .bm-plate:hover .truncate:first-child { text-decoration: underline; }

        .bm-empty { padding: 3rem 1rem; text-align: center; color: var(--bm-muted); background: var(--bm-card); border: 1px dashed var(--bm-line); border-radius: 1rem; }

        /* Modal */
        .bm-modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .bm-modal-backdrop { position: absolute; inset: 0; background: rgb(8 18 30 / .6); }
        .bm-modal-box { position: relative; width: 100%; max-width: 56rem; max-height: calc(100vh - 2rem); overflow-y: auto; border-radius: 1.25rem; background: var(--bm-card); border: 1px solid var(--bm-line); box-shadow: 0 30px 60px -20px rgb(0 0 0 / .6); }
        .bm-modal-close { position: absolute; top: .75rem; right: .75rem; z-index: 2; width: 2.25rem; height: 2.25rem; border-radius: 9999px; font-size: 1.5rem; line-height: 1; background: var(--bm-card2); color: var(--bm-ink); border: 1px solid var(--bm-line); }
        .bm-modal-close:hover { background: var(--bm-line); }
        .bm-seller-panel { background: var(--bm-card2); border-top: 1px solid var(--bm-line); }
        @media (min-width: 768px) { .bm-seller-panel { border-top: 0; border-left: 1px solid var(--bm-line); } }

        @media (prefers-reduced-motion: reduce) { .bm-card, .bm-btn { transition: none; } }
    </style>


    <script nonce="{{ Vite::cspNonce() }}">
    document.addEventListener('alpine:init', function () {

        Alpine.data('buyerMarket', function (cards, baseUrl) {
            return {
                cards: cards,
                baseUrl: baseUrl,
                filtersOpen: false,
                current: null,

                open(i) {
                    this.current = this.cards[i] || null;
                    this.$nextTick(() => { if (this.$refs.closeBtn) this.$refs.closeBtn.focus(); });
                },

                close() { this.current = null; },

                fmt(value) {
                    return 'RWF ' + Number(value).toLocaleString('en-US', { maximumFractionDigits: 0 });
                },

                stockText(c) {
                    if (c.stock === null) return 'Available';
                    if (c.stock <= 0) return 'Out of stock';
                    if (c.stock <= 5) return 'Only ' + c.stock + ' left';
                    return c.stock + ' in stock';
                },

                location(shop) {
                    return [shop.address, shop.city, shop.country]
                        .filter(Boolean)
                        .map((v) => v.charAt(0).toUpperCase() + v.slice(1))
                        .join(', ');
                },

                hue(id) { return (id * 47) % 360; },

                sellerUrl(c) { return this.baseUrl + '?seller=' + c.shop.id; },
            };
        });
    });
    </script>
</x-app-layout>