@extends('layouts.public')

@section('title', __('Marketplace'))
@section('meta_description', __('Browse products from verified sellers and contact businesses directly.'))

@section('content')

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

    <div class="bm bg-slate-50 py-8 sm:py-10"
         x-data="buyerMarket(@js($cards), @js(route('buyer.dashboard')), @js($heroSlides))"
         x-on:keydown.escape.window="close()"
         x-effect="document.body.classList.toggle('overflow-hidden', !!current)">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <header class="bm-hero mb-7 sm:mb-9">
                <div class="bm-hero-slideshow" x-show="heroSlide" aria-label="{{ __('Featured product images') }}"
                     role="region" aria-roledescription="{{ __('carousel') }}"
                     x-on:mouseenter="heroPaused = true" x-on:mouseleave="heroPaused = false"
                     x-on:focusin="heroPaused = true" x-on:focusout="heroPaused = false">
                    <template x-if="heroSlide">
                        <img class="bm-hero-slide-image" :src="heroSlide.image" :alt="heroSlide.name" x-on:error="$el.remove()">
                    </template>
                    <div class="bm-hero-image-shade" aria-hidden="true"></div>
                    <div class="bm-hero-slide-caption" x-show="heroSlide">
                        <span>{{ __('Featured product') }}</span>
                        <strong x-text="heroSlide ? heroSlide.name : ''"></strong>
                        <small x-text="heroSlide ? fmt(heroSlide.price) : ''"></small>
                    </div>
                    <div class="bm-hero-slide-controls" x-show="heroSlides.length > 1">
                        <span class="bm-slide-count" aria-live="polite" x-text="(heroIndex + 1) + ' / ' + heroSlides.length"></span>
                        <button type="button" x-on:click="previousHero()" aria-label="{{ __('Previous featured product') }}">&#8249;</button>
                        <button type="button" x-on:click="nextHero()" aria-label="{{ __('Next featured product') }}">&#8250;</button>
                    </div>
                </div>
                <div class="bm-hero-copy">
                    <span class="bm-eyebrow">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m10 1.8 2.45 4.97 5.48.8-3.96 3.86.94 5.46L10 14.31l-4.9 2.58.94-5.46L2.08 7.57l5.47-.8L10 1.8Z"/></svg>
                        {{ __('The Mahwi marketplace') }}
                    </span>
                    <h1>{{ __('Discover something good.') }}</h1>
                    <p>{{ __('Shop featured finds from trusted local businesses, all in one place.') }}</p>
                    <a href="#marketplace-products" class="bm-hero-link">
                        {{ __('Explore featured products') }}
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12m-5-5 5 5-5 5"/></svg>
                    </a>
                </div>
                <div class="bm-hero-stats" aria-label="{{ __('Marketplace overview') }}">
                    <div class="bm-hero-stat">
                        <span class="bm-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>
                        </span>
                        <span><strong>{{ number_format($products->total()) }}</strong><small>{{ __('featured products') }}</small></span>
                    </div>
                    <div class="bm-hero-stat">
                        <span class="bm-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01m8-5v.01m0 3v.01"/></svg>
                        </span>
                        <span><strong>{{ number_format($sellerOptions->count()) }}</strong><small>{{ __('verified shops') }}</small></span>
                    </div>
                </div>
            </header>

        <form method="GET" action="{{ route('buyer.dashboard') }}" x-ref="form" class="space-y-6">

            {{-- ================= Search ================= --}}
            <div class="bm-search-band">
                <div class="bm-search-main">
                    <span class="bm-search-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="10.8" cy="10.8" r="6.8"/><path stroke-linecap="round" d="m16 16 4.5 4.5"/></svg>
                    </span>
                    <label class="bm-search-field">
                        <span class="bm-search-label">{{ __('Find your next favorite') }}</span>
                        <input type="search" name="q" value="{{ request('q') }}" maxlength="100"
                               placeholder="{{ __('Search products or sellers') }}"
                               aria-label="{{ __('Search products or sellers') }}">
                    </label>
                    <button type="submit" class="bm-btn bm-btn-pri">
                        {{ __('Search') }}
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5"/><path stroke-linecap="round" d="m12.5 12.5 4 4"/></svg>
                    </button>
                </div>
                <div class="bm-search-meta">
                    <span>{{ __('Curated products from approved local shops') }}</span>
                    <span class="bm-search-count">
                        <span class="bm-live-dot"></span>
                        {{ trans_choice(':count product|:count products', $products->total(), ['count' => number_format($products->total())]) }}
                    </span>
                </div>
            </div>


            {{-- ================= Seller banner (when browsing one shop) ================= --}}
            @if($activeSeller)
                @php $s = $activeSeller; @endphp
                <section class="bm-banner" aria-label="{{ __('Seller') }}">
                    <div class="bm-flagbar" aria-hidden="true"></div>
                    <div class="flex flex-wrap items-start gap-4 p-5 sm:p-6">
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
                <aside class="lg:sticky lg:top-6 lg:self-start" aria-label="{{ __('Filters') }}">
                    <button type="button" class="bm-btn bm-btn-ghost w-full lg:hidden" x-on:click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen">
                        {{ __('Filters') }}
                        @if($hasFilters)<span class="bm-dot"></span>@endif
                    </button>

                    <div class="bm-panel mt-3 space-y-5 p-4 sm:p-5 lg:mt-0" :class="filtersOpen ? '' : 'hidden lg:block'">
                        <div class="bm-filter-heading">
                            <div>
                                <h2>{{ __('Refine your search') }}</h2>
                                <p>{{ __('Find just the right thing') }}</p>
                            </div>
                            @if($hasFilters)
                                <a href="{{ route('buyer.dashboard') }}" class="bm-clear-link">{{ __('Clear') }}</a>
                            @endif
                        </div>
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
                            @if($hasFilters || ($sort !== 'newest'))
                                <a href="{{ route('buyer.dashboard') }}" class="bm-btn bm-btn-ghost">{{ __('Reset') }}</a>
                            @endif
                        </div>
                    </div>
                </aside>


                {{-- ================= Products ================= --}}
                <section id="marketplace-products" class="min-w-0 scroll-mt-24" aria-label="{{ __('Products') }}">
                    <div class="bm-results-toolbar">
                        <div>
                            <span class="bm-section-kicker">{{ __('Handpicked for you') }}</span>
                            <h2>{{ __('Featured products') }}</h2>
                            <p>{{ __('Showing') }} <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong> {{ __('of') }} <strong>{{ number_format($products->total()) }}</strong></p>
                        </div>

                        <label class="bm-sort">
                            <span>{{ __('Sort by') }}</span>
                            <select name="sort" class="bm-input" x-on:change="$refs.form.requestSubmit()">
                                <option value="newest"     @selected($sort === 'newest')>{{ __('Newest') }}</option>
                                <option value="price_asc"  @selected($sort === 'price_asc')>{{ __('Price: low to high') }}</option>
                                <option value="price_desc" @selected($sort === 'price_desc')>{{ __('Price: high to low') }}</option>
                                <option value="name"       @selected($sort === 'name')>{{ __('Name') }}</option>
                            </select>
                        </label>
                    </div>

                    @if(count($cards))
                        <div class="bm-product-grid">
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
                                        <span class="bm-featured-tag">
                                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m10 1.8 2.45 4.97 5.48.8-3.96 3.86.94 5.46L10 14.31l-4.9 2.58.94-5.46L2.08 7.57l5.47-.8L10 1.8Z"/></svg>
                                            {{ __('Featured') }}
                                        </span>
                                        @if($note)<span class="bm-flag {{ $out ? '' : 'bm-flag-low' }}">{{ $note }}</span>@endif
                                        <span class="bm-view-product">{{ __('Quick view') }} <span aria-hidden="true">↗</span></span>
                                    </button>

                                    <div class="bm-card-body">
                                        <div class="bm-card-title-row">
                                            <button type="button" class="bm-name" x-on:click="open({{ $i }})">{{ $card['name'] }}</button>
                                            <span class="bm-stock-indicator {{ $out ? 'is-out' : '' }}">
                                                <span></span>{{ $out ? __('Out of stock') : __('Available') }}
                                            </span>
                                        </div>
                                        <p class="bm-price">{{ rwf($card['price']) }}</p>
                                    </div>

                                    @if($shop)
                                        <a href="{{ route('buyer.dashboard', ['seller' => $shop['id']]) }}" class="bm-plate" title="{{ __('See all products from :name', ['name' => $shop['name']]) }}">
                                            <span class="bm-flagbar" aria-hidden="true"></span>

                                            <span class="flex min-w-0 items-center gap-3">
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

                                            <span class="bm-shop-action">{{ __('Visit shop') }} <span aria-hidden="true">→</span></span>
                                        </a>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        <div class="bm-pagination">{{ $products->links() }}</div>
                    @else
                        <div class="bm-empty">
                            <span class="bm-empty-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10.5" cy="10.5" r="6.5"/><path stroke-linecap="round" d="m15.5 15.5 5 5M8 10.5h5"/></svg>
                            </span>
                            <p class="font-semibold bm-ink">{{ __('No featured products found') }}</p>
                            <p class="mt-1 text-sm">{{ __('Try adjusting your filters or search for something else.') }}</p>
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
                --bm-card: #fff;
                --bm-card2: #f4f7f5;
                --bm-ink: #17241d;
                --bm-soft: #58675e;
                --bm-muted: #7d8b82;
                --bm-line: #e2e9e4;
                --bm-pri: #16704c;
                --bm-pri-h: #10583b;
                --bm-on-pri: #fff;
                --bm-ok: #16704c;
                --bm-ok-bg: #e8f4ec;
                --bm-bad: #b42318;
                --bm-focus: #16704c;
                color: var(--bm-ink);
            }

            html.dark .bm {
                --bm-card: #15221b;
                --bm-card2: #1c2b22;
                --bm-ink: #edf5ef;
                --bm-soft: #b1c2b6;
                --bm-muted: #8da296;
                --bm-line: #2b3d31;
                --bm-pri: #75c69a;
                --bm-pri-h: #99dbb5;
                --bm-on-pri: #11251a;
                --bm-ok: #75c69a;
                --bm-ok-bg: rgb(117 198 154 / .14);
                --bm-bad: #ff8c82;
                --bm-focus: #99dbb5;
            }

            .bm-ink { color: var(--bm-ink); }
            .bm-soft { color: var(--bm-soft); }
            .bm-muted { color: var(--bm-muted); }
            .bm-ok { color: var(--bm-ok); }
            .bm-bad { color: var(--bm-bad); }
            .bm :focus-visible { outline: 3px solid color-mix(in srgb, var(--bm-focus) 45%, transparent); outline-offset: 3px; }

            .bm-hero {
                position: relative;
                display: flex;
                min-height: 270px;
                align-items: center;
                justify-content: space-between;
                gap: 2.5rem;
                overflow: hidden;
                padding: clamp(1.5rem, 5vw, 3.5rem);
                border: 1px solid #244e38;
                border-radius: 2rem;
                color: #f4fff7;
                background:
                    radial-gradient(ellipse at 86% 16%, rgb(140 205 151 / .24), transparent 32%),
                    radial-gradient(ellipse at 73% 110%, rgb(90 152 108 / .34), transparent 40%),
                    linear-gradient(116deg, #102b1d 0%, #17462e 55%, #1d5537 100%);
                box-shadow: 0 24px 60px -34px rgb(18 52 33 / .7);
            }
            .bm-hero-slideshow {
                position: absolute;
                inset: 0 0 0 auto;
                z-index: 0;
                width: 58%;
                overflow: hidden;
            }
            .bm-hero-slide-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; animation: bm-slide-in .65s ease both; }
            .bm-hero-image-shade {
                position: absolute;
                inset: 0;
                background: linear-gradient(90deg, #102b1d 0%, rgb(16 43 29 / .72) 24%, rgb(16 43 29 / .12) 100%), linear-gradient(0deg, rgb(9 28 18 / .7), transparent 55%);
            }
            .bm-hero-slide-caption { position: absolute; right: 7rem; bottom: 1.6rem; left: 2rem; display: grid; gap: .2rem; color: #fff; text-shadow: 0 1px 8px rgb(0 0 0 / .35); }
            .bm-hero-slide-caption > span { color: #d3ead9; font-size: .68rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
            .bm-hero-slide-caption strong { overflow: hidden; font-size: 1.1rem; text-overflow: ellipsis; white-space: nowrap; }
            .bm-hero-slide-caption small { font-size: .82rem; font-weight: 700; }
            .bm-hero-slide-controls { position: absolute; right: 1.25rem; bottom: 1.25rem; display: flex; align-items: center; gap: .4rem; }
            .bm-slide-count { margin-right: .25rem; color: #fff; font-size: .7rem; font-weight: 750; text-shadow: 0 1px 6px rgb(0 0 0 / .45); }
            .bm-hero-slide-controls button { display: grid; width: 2.2rem; height: 2.2rem; place-items: center; border: 1px solid rgb(255 255 255 / .45); border-radius: 50%; color: #fff; background: rgb(12 30 20 / .48); font-size: 1.6rem; line-height: 1; cursor: pointer; backdrop-filter: blur(8px); }
            .bm-hero-slide-controls button:hover { background: rgb(12 30 20 / .8); }
            @keyframes bm-slide-in { from { opacity: .35; transform: scale(1.025); } to { opacity: 1; transform: scale(1); } }
            .bm-hero::after {
                position: absolute;
                right: -4rem;
                bottom: -12rem;
                width: 31rem;
                height: 31rem;
                border: 1px solid rgb(255 255 255 / .12);
                border-radius: 50%;
                box-shadow: 0 0 0 2.5rem rgb(255 255 255 / .035), 0 0 0 5rem rgb(255 255 255 / .025);
                content: "";
                pointer-events: none;
            }
            .bm-hero-copy, .bm-hero-stats { position: relative; z-index: 1; }
            .bm-hero-copy { max-width: 39rem; }
            .bm-hero-stats { align-self: flex-start; margin-top: 1.2rem; }
            .bm-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: .45rem;
                color: #c5e7d0;
                font-size: .73rem;
                font-weight: 800;
                letter-spacing: .14em;
                text-transform: uppercase;
            }
            .bm-eyebrow svg { width: 1rem; height: 1rem; color: #f1c96a; }
            .bm-hero h1 {
                max-width: 12ch;
                margin: .8rem 0 0;
                color: #fff;
                font-size: clamp(2.35rem, 5vw, 4.25rem);
                font-weight: 800;
                letter-spacing: -.065em;
                line-height: .99;
            }
            .bm-hero-copy > p { max-width: 34rem; margin: 1rem 0 0; color: #d2e3d6; font-size: 1.02rem; line-height: 1.65; }
            .bm-hero-link {
                display: inline-flex;
                align-items: center;
                gap: .65rem;
                margin-top: 1.45rem;
                padding: .8rem 1rem;
                border: 1px solid rgb(255 255 255 / .25);
                border-radius: .85rem;
                color: #fff;
                background: rgb(255 255 255 / .1);
                font-size: .88rem;
                font-weight: 750;
                text-decoration: none;
                backdrop-filter: blur(10px);
            }
            .bm-hero-link:hover { background: rgb(255 255 255 / .18); transform: translateY(-1px); }
            .bm-hero-link svg { width: 1.1rem; height: 1.1rem; }
            .bm-hero-stats { display: grid; width: min(100%, 15rem); gap: .7rem; flex-shrink: 0; }
            .bm-hero-stat {
                display: flex;
                align-items: center;
                gap: .85rem;
                padding: .9rem 1rem;
                border: 1px solid rgb(255 255 255 / .16);
                border-radius: 1rem;
                background: rgb(255 255 255 / .09);
                backdrop-filter: blur(12px);
            }
            .bm-stat-icon {
                display: grid;
                width: 2.5rem;
                height: 2.5rem;
                place-items: center;
                border-radius: .8rem;
                color: #f2d783;
                background: rgb(255 255 255 / .1);
            }
            .bm-stat-icon svg { width: 1.35rem; height: 1.35rem; }
            .bm-hero-stat strong, .bm-hero-stat small { display: block; }
            .bm-hero-stat strong { color: #fff; font-size: 1.15rem; line-height: 1.15; }
            .bm-hero-stat small { margin-top: .2rem; color: #c5d9ca; font-size: .73rem; }

            .bm-search-band {
                padding: 1rem;
                border: 1px solid var(--bm-line);
                border-radius: 1.25rem;
                background: var(--bm-card);
                box-shadow: 0 12px 34px -28px rgb(21 45 30 / .35);
            }
            .bm-search-main {
                display: flex;
                min-height: 4.25rem;
                align-items: center;
                gap: .9rem;
                padding: .45rem .45rem .45rem 1rem;
                border: 1px solid var(--bm-line);
                border-radius: .95rem;
                background: var(--bm-card2);
                color: var(--bm-muted);
            }
            .bm-search-main:focus-within { border-color: var(--bm-pri); box-shadow: 0 0 0 3px color-mix(in srgb, var(--bm-pri) 13%, transparent); }
            .bm-search-icon { display: grid; width: 2.7rem; height: 2.7rem; flex: 0 0 auto; place-items: center; border-radius: .8rem; color: var(--bm-pri); background: var(--bm-card); }
            .bm-search-icon svg { width: 1.35rem; height: 1.35rem; }
            .bm-search-field { display: block; min-width: 0; flex: 1; }
            .bm-search-label { display: block; color: var(--bm-muted); font-size: .67rem; font-weight: 750; letter-spacing: .08em; text-transform: uppercase; }
            .bm-search-main input { display: block; width: 100%; min-width: 0; border: 0; padding: .15rem 0 0; color: var(--bm-ink); background: transparent; font-size: .97rem; }
            .bm-search-main input:focus { outline: none; box-shadow: none; }
            .bm-search-main input::placeholder { color: var(--bm-muted); opacity: .8; }
            .bm-search-main .bm-btn { min-height: 3.3rem; padding-inline: 1.25rem; }
            .bm-search-main .bm-btn svg { width: 1.1rem; height: 1.1rem; }
            .bm-search-meta { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .8rem .35rem 0; color: var(--bm-muted); font-size: .76rem; }
            .bm-search-count { display: inline-flex; align-items: center; gap: .45rem; color: var(--bm-soft); font-weight: 700; }
            .bm-live-dot { width: .45rem; height: .45rem; border-radius: 50%; background: #41a96b; box-shadow: 0 0 0 3px rgb(65 169 107 / .13); }

            .bm-banner { overflow: hidden; border: 1px solid var(--bm-line); border-radius: 1.25rem; background: var(--bm-card); box-shadow: 0 10px 32px -28px rgb(21 45 30 / .4); }
            .bm-flagbar { display: block; height: 3px; background: linear-gradient(90deg, #00a1de 0 50%, #fad201 50% 75%, #20603d 75% 100%); }
            .bm-avatar { display: inline-flex; width: 2.5rem; height: 2.5rem; flex-shrink: 0; align-items: center; justify-content: center; border: 2px solid var(--bm-card); border-radius: 50%; color: #fff; font-size: .78rem; font-weight: 800; box-shadow: 0 0 0 1px var(--bm-line); }
            .bm-avatar-lg { width: 3.5rem; height: 3.5rem; font-size: 1.05rem; }
            .bm-chip { display: inline-flex; align-items: center; padding: .2rem .55rem; border: 1px solid var(--bm-line); border-radius: 999px; color: var(--bm-soft); background: var(--bm-card2); font-size: .68rem; font-weight: 650; }
            .bm-chip-ok { border-color: transparent; color: var(--bm-ok); background: var(--bm-ok-bg); }

            .bm-panel { border: 1px solid var(--bm-line); border-radius: 1.2rem; background: var(--bm-card); box-shadow: 0 12px 34px -28px rgb(21 45 30 / .35); }
            .bm-filter-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: .6rem; padding-bottom: .95rem; border-bottom: 1px solid var(--bm-line); }
            .bm-filter-heading h2 { margin: 0; color: var(--bm-ink); font-size: .96rem; font-weight: 800; letter-spacing: -.02em; }
            .bm-filter-heading p { margin: .2rem 0 0; color: var(--bm-muted); font-size: .73rem; }
            .bm-clear-link { color: var(--bm-pri); font-size: .75rem; font-weight: 750; text-decoration: none; }
            .bm-clear-link:hover { text-decoration: underline; }
            .bm-field { display: flex; flex-direction: column; gap: .4rem; color: var(--bm-soft); font-size: .76rem; font-weight: 700; }
            .bm-legend { color: var(--bm-soft); font-size: .76rem; font-weight: 700; }
            .bm-input {
                width: 100%;
                min-height: 2.65rem;
                padding: .55rem .7rem;
                border: 1px solid var(--bm-line);
                border-radius: .7rem;
                color: var(--bm-ink);
                background: var(--bm-card2);
                font-size: .83rem;
                font-weight: 500;
            }
            .bm-input:focus { border-color: var(--bm-pri); box-shadow: 0 0 0 3px color-mix(in srgb, var(--bm-pri) 14%, transparent); outline: none; }
            .bm-check { display: inline-flex; align-items: center; gap: .6rem; color: var(--bm-soft); font-size: .82rem; cursor: pointer; }
            .bm-check input { width: 1rem; height: 1rem; accent-color: var(--bm-pri); }
            .bm-dot { display: inline-block; width: .48rem; height: .48rem; margin-left: .4rem; border-radius: 50%; background: #e6a23c; }
            .bm-btn {
                display: inline-flex;
                min-height: 2.65rem;
                align-items: center;
                justify-content: center;
                gap: .45rem;
                padding: .55rem .9rem;
                border: 1px solid transparent;
                border-radius: .75rem;
                font-size: .82rem;
                font-weight: 750;
                text-align: center;
                text-decoration: none;
                transition: color .18s ease, background-color .18s ease, border-color .18s ease, transform .18s ease, box-shadow .18s ease;
            }
            .bm-btn-sm { min-height: 2.3rem; padding: .4rem .75rem; font-size: .77rem; }
            .bm-btn-pri { color: var(--bm-on-pri); background: var(--bm-pri); box-shadow: 0 5px 14px -9px var(--bm-pri); }
            .bm-btn-pri:hover { color: var(--bm-on-pri); background: var(--bm-pri-h); transform: translateY(-1px); box-shadow: 0 8px 18px -10px var(--bm-pri); }
            .bm-btn-ghost { border-color: var(--bm-line); color: var(--bm-ink); background: var(--bm-card); }
            .bm-btn-ghost:hover { border-color: var(--bm-pri); color: var(--bm-pri); background: var(--bm-card2); }
            .bm-results-toolbar { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin: 1.8rem 0 1rem; }
            .bm-section-kicker { color: var(--bm-pri); font-size: .67rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
            .bm-results-toolbar h2 { margin: .2rem 0 0; color: var(--bm-ink); font-size: clamp(1.35rem, 2vw, 1.8rem); font-weight: 800; letter-spacing: -.045em; }
            .bm-results-toolbar p { margin: .25rem 0 0; color: var(--bm-muted); font-size: .77rem; }
            .bm-results-toolbar p strong { color: var(--bm-soft); font-weight: 750; }
            .bm-sort { display: flex; align-items: center; gap: .55rem; color: var(--bm-muted); font-size: .75rem; font-weight: 650; }
            .bm-sort .bm-input { min-width: 10.5rem; width: auto; }
            .bm-product-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
            .bm-card {
                display: flex;
                min-width: 0;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid var(--bm-line);
                border-radius: 1.2rem;
                background: var(--bm-card);
                box-shadow: 0 8px 24px -22px rgb(20 45 30 / .45);
                transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
            }
            .bm-card:hover { transform: translateY(-4px); border-color: color-mix(in srgb, var(--bm-pri) 55%, var(--bm-line)); box-shadow: 0 22px 42px -30px rgb(18 48 30 / .55); }
            .bm-card.is-out .bm-media img { filter: grayscale(.85); opacity: .7; }
            .bm-media {
                position: relative;
                display: block;
                width: 100%;
                aspect-ratio: 1.2 / 1;
                overflow: hidden;
                border: 0;
                padding: 0;
                background: var(--bm-card2);
                cursor: pointer;
                text-align: left;
            }
            .bm-media-static { border-radius: 1rem; }
            .bm-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .45s cubic-bezier(.2,.7,.2,1); }
            .bm-card:hover .bm-media img { transform: scale(1.055); }
            .bm-media-fallback {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--bm-pri);
                background: radial-gradient(circle at 50% 40%, color-mix(in srgb, var(--bm-pri) 12%, transparent), transparent 42%), repeating-linear-gradient(135deg, transparent 0 14px, color-mix(in srgb, var(--bm-pri) 5%, transparent) 14px 28px);
                font-size: 3rem;
                font-weight: 800;
                opacity: .72;
            }
            .bm-featured-tag, .bm-flag, .bm-view-product { position: absolute; z-index: 1; display: inline-flex; align-items: center; gap: .35rem; border-radius: 999px; font-size: .68rem; font-weight: 750; }
            .bm-featured-tag { top: .75rem; left: .75rem; padding: .38rem .62rem; color: #173a28; background: rgb(255 255 255 / .92); box-shadow: 0 4px 12px rgb(0 0 0 / .08); backdrop-filter: blur(8px); }
            .bm-featured-tag svg { width: .85rem; height: .85rem; color: #cf9f24; }
            .bm-flag { right: .75rem; bottom: .75rem; padding: .38rem .65rem; color: #fff; background: rgb(26 39 30 / .88); backdrop-filter: blur(8px); }
            .bm-flag-low { color: #513800; background: #f8d978; }
            .bm-view-product { right: .75rem; top: .75rem; padding: .4rem .65rem; color: #fff; background: rgb(18 31 22 / .48); opacity: 0; transform: translateY(-3px); transition: opacity .2s ease, transform .2s ease; backdrop-filter: blur(8px); }
            .bm-view-product span { font-size: .9rem; }
            .bm-card:hover .bm-view-product, .bm-media:focus-visible .bm-view-product { opacity: 1; transform: translateY(0); }
            .bm-card-body { padding: .95rem 1rem .9rem; }
            .bm-card-title-row { display: flex; min-height: 2.7rem; align-items: flex-start; justify-content: space-between; gap: .5rem; }
            .bm-name { display: -webkit-box; min-width: 0; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 2; color: var(--bm-ink); font-size: .93rem; font-weight: 750; line-height: 1.4; text-align: left; }
            .bm-name:hover { color: var(--bm-pri); }
            .bm-stock-indicator { display: inline-flex; flex: 0 0 auto; align-items: center; gap: .3rem; padding-top: .15rem; color: var(--bm-ok); font-size: .62rem; font-weight: 700; white-space: nowrap; }
            .bm-stock-indicator > span { width: .38rem; height: .38rem; border-radius: 50%; background: currentColor; }
            .bm-stock-indicator.is-out { color: var(--bm-bad); }
            .bm-price { margin: .45rem 0 0; color: var(--bm-ink); font-size: 1.2rem; font-weight: 850; letter-spacing: -.035em; }
            .bm-plate { position: relative; display: block; margin-top: auto; padding: .85rem 1rem; border-top: 1px solid var(--bm-line); color: inherit; background: var(--bm-card2); text-decoration: none; transition: background-color .2s ease; }
            .bm-plate .bm-flagbar { position: absolute; top: 0; right: 0; left: 0; height: 2px; opacity: .8; }
            .bm-plate:hover { background: color-mix(in srgb, var(--bm-pri) 7%, var(--bm-card)); }
            .bm-plate:hover .truncate:first-child { text-decoration: underline; text-underline-offset: 2px; }
            .bm-shop-action { display: flex; align-items: center; justify-content: space-between; margin: .65rem 0 0 3.25rem; color: var(--bm-pri); font-size: .72rem; font-weight: 750; }
            .bm-empty { padding: 4rem 1.25rem; border: 1px dashed var(--bm-line); border-radius: 1.2rem; color: var(--bm-muted); background: var(--bm-card); text-align: center; }
            .bm-empty-icon { display: grid; width: 3.5rem; height: 3.5rem; margin: 0 auto 1rem; place-items: center; border-radius: 1.1rem; color: var(--bm-pri); background: var(--bm-card2); }
            .bm-empty-icon svg { width: 1.7rem; height: 1.7rem; }
            .bm-pagination { margin-top: 1.5rem; }
            .bm-pagination nav { display: flex; justify-content: center; }
            .bm-pagination > * { max-width: 100%; }
            .bm-modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 1rem; }
            .bm-modal-backdrop { position: absolute; inset: 0; background: rgb(8 18 30 / .66); backdrop-filter: blur(5px); }
            .bm-modal-box { position: relative; width: 100%; max-width: 58rem; max-height: calc(100vh - 2rem); overflow-y: auto; border: 1px solid var(--bm-line); border-radius: 1.5rem; background: var(--bm-card); box-shadow: 0 30px 80px -24px rgb(0 0 0 / .5); }
            .bm-modal-close { position: absolute; top: .85rem; right: .85rem; z-index: 2; display: grid; width: 2.5rem; height: 2.5rem; place-items: center; border: 1px solid var(--bm-line); border-radius: 50%; color: var(--bm-ink); background: var(--bm-card); font-size: 1.45rem; line-height: 1; box-shadow: 0 5px 18px rgb(0 0 0 / .12); }
            .bm-modal-close:hover { color: var(--bm-pri); background: var(--bm-card2); }
            .bm-seller-panel { border-top: 1px solid var(--bm-line); background: var(--bm-card2); }
            @media (min-width: 768px) { .bm-seller-panel { border-top: 0; border-left: 1px solid var(--bm-line); } }
            @media (max-width: 1100px) { .bm-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (max-width: 760px) {
                .bm-hero { min-height: 0; flex-direction: column; align-items: stretch; gap: 1.5rem; border-radius: 1.5rem; }
                .bm-hero-slideshow { width: 100%; }
                .bm-hero-image-shade { background: linear-gradient(90deg, rgb(16 43 29 / .9), rgb(16 43 29 / .55)), linear-gradient(0deg, rgb(9 28 18 / .65), transparent); }
                .bm-hero-slide-caption { display: none; }
                .bm-hero-slide-controls { top: 1rem; right: 1rem; bottom: auto; }
                .bm-hero h1 { max-width: 14ch; }
                .bm-hero-stats { width: 100%; grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .bm-hero-stat { padding: .75rem; }
                .bm-results-toolbar { align-items: flex-start; }
            }
            @media (max-width: 540px) {
                .bm-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .7rem; }
                .bm-card { border-radius: .95rem; }
                .bm-media { aspect-ratio: .95 / 1; }
                .bm-card-body { padding: .7rem; }
                .bm-card-title-row { min-height: 3rem; flex-direction: column; gap: .2rem; }
                .bm-name { font-size: .83rem; }
                .bm-stock-indicator { font-size: .59rem; }
                .bm-price { font-size: 1.05rem; }
                .bm-plate { padding: .7rem; }
                .bm-plate .bm-avatar { width: 2.1rem; height: 2.1rem; font-size: .67rem; }
                .bm-plate .bm-shop-action { margin-left: 2.7rem; }
                .bm-view-product { display: none; }
                .bm-search-band { padding: .7rem; }
                .bm-search-main { gap: .55rem; padding-left: .55rem; }
                .bm-search-icon { width: 2.25rem; height: 2.25rem; }
                .bm-search-main .bm-btn { min-height: 2.8rem; padding-inline: .75rem; }
                .bm-search-main .bm-btn svg { display: none; }
                .bm-search-label { font-size: .58rem; }
                .bm-search-main input { font-size: .85rem; }
                .bm-search-meta { align-items: flex-start; flex-direction: column; gap: .35rem; }
                .bm-results-toolbar { align-items: flex-start; flex-direction: column; }
                .bm-sort { width: 100%; justify-content: space-between; }
                .bm-sort .bm-input { min-width: 0; }
            }
            @media (max-width: 370px) {
                .bm-product-grid { grid-template-columns: 1fr; }
                .bm-media { aspect-ratio: 1.25 / 1; }
                .bm-hero-stats { grid-template-columns: 1fr; }
            }
            @media (prefers-reduced-motion: reduce) {
                .bm-card, .bm-btn, .bm-hero-link, .bm-media img, .bm-view-product, .bm-hero-slide-image { transition: none; animation: none; }
                .bm-card:hover, .bm-hero-link:hover, .bm-btn-pri:hover { transform: none; }
            }
        </style>


    <script nonce="{{ Vite::cspNonce() }}">
    document.addEventListener('alpine:init', function () {

        Alpine.data('buyerMarket', function (cards, baseUrl, heroSlides) {
            return {
                cards: cards,
                baseUrl: baseUrl,
                heroSlides: heroSlides,
                heroIndex: 0,
                heroTimer: null,
                heroPaused: false,
                filtersOpen: false,
                current: null,

                init() {
                    if (this.heroSlides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        this.heroTimer = window.setInterval(() => {
                            if (!this.heroPaused) this.nextHero();
                        }, 5000);
                    }
                },

                destroy() {
                    if (this.heroTimer) window.clearInterval(this.heroTimer);
                },

                get heroSlide() { return this.heroSlides[this.heroIndex] || null; },

                nextHero() {
                    if (this.heroSlides.length > 1) this.heroIndex = (this.heroIndex + 1) % this.heroSlides.length;
                },

                previousHero() {
                    if (this.heroSlides.length > 1) this.heroIndex = (this.heroIndex - 1 + this.heroSlides.length) % this.heroSlides.length;
                },

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
@endsection