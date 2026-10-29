@php
    $currentUser = Auth::user();
    $currentShop = $shop ?? $currentUser?->shop;

    $isAdmin  = $currentUser?->isAdmin();
    $isSeller = $currentUser?->isSeller();
    $isWaiter = $currentUser?->role === 'waiter';

    // Orders
    $ordersRoute = null;
    $pendingOrdersCount = 0;

    if ($currentShop) {
        $ordersRoute = route('shops.orders.index', ['shop' => $currentShop]);

        if (!$isWaiter) {
            $pendingOrdersCount = $currentShop->orders()->pending()->count();
        }
    }

    $shopLabel    = $currentShop ? ($currentShop->business_name ?: $currentShop->name) : '';
    $shopInitials = $shopLabel !== '' ? mb_strtoupper(mb_substr($shopLabel, 0, 2)) : '';

    // Icons (heroicons outline paths)
    $icons = [
        'home'     => 'M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6',
        'box'      => 'M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 0L4 7m8 4v10',
        'money'    => 'M9 14l6-6M8 8h.01M16 16h.01M19 5H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2z',
        'report'   => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h8l4 4v12a2 2 0 01-2 2z',
        'team'     => 'M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 110-8 4 4 0 010 8zm6 4a3 3 0 10-6 0 3 3 0 006 0z',
        'chart'    => 'M3 3v18h18M7 16l4-5 3 3 5-7',
        'cart'     => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 2h12m-9 4a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z',
        'expense'  => 'M12 8v8m-4-4h8M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z',
        'orders'   => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5h6',
        'pos'      => 'M3 3h18v18H3V3zm4 4h10M7 11h10M7 15h6',
        'logout'   => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
        'chevron'  => 'M19 9l-7 7-7-7',
        'collapse' => 'M15 19l-7-7 7-7',
        'close'    => 'M6 18L18 6M6 6l12 12',
    ];

    // Admin groups: [label, route, active pattern]
    $groups = $isAdmin ? [
        'inventory' => [
            'label' => __('Inventory'),
            'icon'  => 'box',
            'items' => [
                [__('Products'),   'products.index',   'products.*'],
                [__('Categories'), 'categories.index', 'categories.*'],
                [__('Suppliers'),  'suppliers.index',  'suppliers.*'],
                [__('Customers'),  'customers.index',  'customers.*'],
            ],
        ],
        'transactions' => [
            'label' => __('Transactions'),
            'icon'  => 'money',
            'items' => [
                [__('Sales'),              'sales.index',              'sales.*'],
                [__('Purchases'),          'purchases.index',          'purchases.*'],
                [__('Expenses'),           'expenses.index',           'expenses.*'],
                [__('Expense Categories'), 'expensecategories.index',  'expensecategories.*'],
                [__('Other Revenue'),      'other_incomes.index',      'other_incomes.*'],
                [__('Income Categories'),  'income_categories.index',  'income_categories.*'],
                [__('Import & Templates'), 'imports.index',            'imports.*'],
            ],
        ],
        'reports' => [
            'label' => __('Reports'),
            'icon'  => 'report',
            'items' => [
                [__('Overview'),       'reports.index',   'reports.index'],
                [__('Daily Report'),   'reports.daily',   'reports.daily'],
                [__('Weekly Report'),  'reports.weekly',  'reports.weekly'],
                [__('Monthly Report'), 'reports.monthly', 'reports.monthly'],
                [__('Yearly Report'),  'reports.yearly',  'reports.yearly'],
            ],
        ],
        'team' => [
            'label' => __('Team'),
            'icon'  => 'team',
            'items' => [
                [__('Staff'), 'staff.index', 'staff.*'],
            ],
        ],
    ] : [];

    $openGroups = collect($groups)
        ->map(fn ($g) => collect($g['items'])->contains(fn ($i) => request()->routeIs($i[2])))
        ->all();

    // Seller links: [label, route, active pattern, icon]
    $sellerLinks = $isSeller ? [
        [__('Sales'),     'sales.index',     'sales.*',     'chart'],
        [__('Products'),  'products.index',  'products.*',  'box'],
        [__('Purchases'), 'purchases.index', 'purchases.*', 'cart'],
        [__('Expenses'),  'expenses.index',  'expenses.*',  'expense'],
    ] : [];

    $dashboardActive = request()->routeIs('dashboard', 'shop.dashboard', 'admin.dashboard', 'seller.dashboard', 'accountant.dashboard');
@endphp

{{--
    The drawer's open/closed state is `sidebarOpen`, owned by the root <html x-data="mahwiApp()">
    in layouts/app.blade.php. It is NOT redeclared here, so the header's menu button, the overlay
    and this sidebar all share the same value.
--}}
<aside
    id="app-sidebar"
    x-data="{
        collapsed: document.documentElement.classList.contains('sidebar-collapsed'),
        desktop: window.matchMedia('(min-width: 1024px)').matches,
        canHover: window.matchMedia('(hover: hover)').matches,
        open: @js($openGroups),
        flyout: null,
        flyoutTop: 0,
        tip: '',
        tipTop: 0,
        timer: null,

        get rail() {
            return this.collapsed && this.desktop;
        },

        init() {
            const media = window.matchMedia('(min-width: 1024px)');

            media.addEventListener('change', e => {
                this.desktop = e.matches;
                this.flyout = null;
                this.tip = '';
                if (e.matches) this.sidebarOpen = false;
            });
        },

        toggle() {
            this.collapsed = !this.collapsed;

            document.documentElement.classList.toggle('sidebar-collapsed', this.collapsed);

            try {
                localStorage.setItem('sidebar-collapsed', this.collapsed ? '1' : '0');
            } catch (e) {}

            this.flyout = null;
            this.tip = '';
        },

        hold() {
            clearTimeout(this.timer);
        },

        release() {
            this.timer = setTimeout(() => {
                this.flyout = null;
                this.tip = '';
            }, 120);
        },

        hoverFlyout(key, el, height) {
            if (this.canHover) this.showFlyout(key, el, height);
        },

        hoverTip(label, el) {
            if (this.canHover) this.showTip(label, el);
        },

        showFlyout(key, el, height) {
            if (!this.rail) return;

            this.hold();
            this.tip = '';

            this.flyoutTop = Math.max(
                8,
                Math.min(el.getBoundingClientRect().top, window.innerHeight - height - 8)
            );

            this.flyout = key;
        },

        showTip(label, el) {
            if (!this.rail) return;

            this.hold();
            this.flyout = null;

            const r = el.getBoundingClientRect();
            this.tipTop = r.top + r.height / 2;
            this.tip = label;
        },

        groupClick(key, el, height) {
            if (!this.rail) {
                this.open[key] = !this.open[key];
                return;
            }

            this.flyout === key ? this.flyout = null : this.showFlyout(key, el, height);
        }
    }"
    :class="{ 'is-open': sidebarOpen }"
    @click.outside="flyout = null; tip = ''"
    @keydown.escape.window="flyout = null; tip = ''; if (!desktop) sidebarOpen = false"
    @click="if (!desktop && $event.target.closest('a[href]')) sidebarOpen = false"
    class="sidebar-panel fixed inset-y-0 left-0 z-50 flex flex-col"
    aria-label="{{ __('Main navigation') }}"
>

    {{-- Collapse toggle (desktop only) --}}
    <button
        type="button"
        @click="toggle()"
        :aria-expanded="!collapsed"
        :aria-label="collapsed ? @js(__('Expand sidebar')) : @js(__('Collapse sidebar'))"
        class="sidebar-collapse-btn absolute -right-3 top-5 z-10 hidden h-6 w-6 items-center justify-center rounded-full lg:flex"
    >
        <svg class="h-3.5 w-3.5 transition-transform duration-200" :class="{ 'rotate-180': collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $icons['collapse'] }}"/>
        </svg>
    </button>

    {{-- Brand --}}
    <div class="sidebar-brand sidebar-row flex h-16 shrink-0 items-center justify-between gap-2 overflow-hidden px-5">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3" aria-label="{{ __('Dashboard') }}">
            <x-application-logo class="sidebar-logo block h-9 w-auto max-w-full shrink-0 fill-current" />
        </a>

        {{-- Close button (mobile drawer only) --}}
        <button
            type="button"
            @click="sidebarOpen = false"
            class="sidebar-close-btn inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg lg:hidden"
            aria-label="{{ __('Close menu') }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['close'] }}"/>
            </svg>
        </button>
    </div>

    {{-- Shop --}}
    @if($currentShop)
        <div
            class="sidebar-shop mx-4 mt-4 flex items-center gap-3 px-3 py-2.5"
            @mouseenter="hoverTip(@js($shopLabel), $el)"
            @mouseleave="release()"
        >
            <div class="sidebar-shop-avatar flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold">
                {{ $shopInitials }}
            </div>
            <div class="sb-label min-w-0">
                <div class="sidebar-muted text-xs">{{ __('Business name') }}</div>
                <div class="sidebar-shop-name truncate text-sm font-semibold">{{ $shopLabel }}</div>
            </div>
        </div>
    @endif

    {{-- Navigation --}}
    <nav class="flex flex-1 flex-col space-y-1 overflow-y-auto overflow-x-hidden overscroll-y-contain whitespace-nowrap px-3 py-5">

        {{-- Dashboard --}}
        <a
            href="{{ route('dashboard') }}"
            class="sidebar-link {{ $dashboardActive ? 'sidebar-link-active' : '' }}"
            @if($dashboardActive) aria-current="page" @endif
            @mouseenter="hoverTip(@js(__('Dashboard')), $el)"
            @mouseleave="release()"
        >
            <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['home'] }}"/></svg>
            <span class="sb-label">{{ __('Dashboard') }}</span>
        </a>

        {{-- Admin groups --}}
        @foreach($groups as $key => $group)
            @php
                $groupActive = $openGroups[$key];
                $panelHeight = count($group['items']) * 36 + 48;
            @endphp

            <div>
                <button
                    type="button"
                    @click="groupClick('{{ $key }}', $el, {{ $panelHeight }})"
                    @mouseenter="hoverFlyout('{{ $key }}', $el, {{ $panelHeight }})"
                    @mouseleave="release()"
                    :aria-expanded="rail ? flyout === '{{ $key }}' : open.{{ $key }}"
                    class="sidebar-group-button {{ $groupActive ? 'sidebar-group-active' : '' }}"
                >
                    <span class="flex items-center gap-3">
                        <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$group['icon']] }}"/></svg>
                        <span class="sb-label">{{ $group['label'] }}</span>
                    </span>

                    <svg class="sb-hide h-4 w-4 transition-transform" :class="{ 'rotate-180': open.{{ $key }} }" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['chevron'] }}"/>
                    </svg>
                </button>

                {{-- Inline submenu (expanded mode and mobile drawer) --}}
                <div x-show="!rail && open.{{ $key }}" x-collapse x-cloak class="sidebar-submenu">
                    @foreach($group['items'] as [$label, $routeName, $pattern])
                        <a href="{{ route($routeName) }}" class="sidebar-sub-link {{ request()->routeIs($pattern) ? 'sidebar-sub-link-active' : '' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Seller links --}}
        @if($isSeller)
            <div class="sidebar-section-title sb-label">{{ __('Sales') }}</div>

            @foreach($sellerLinks as [$label, $routeName, $pattern, $icon])
                <a
                    href="{{ route($routeName) }}"
                    class="sidebar-link {{ request()->routeIs($pattern) ? 'sidebar-link-active' : '' }}"
                    @if(request()->routeIs($pattern)) aria-current="page" @endif
                    @mouseenter="hoverTip(@js($label), $el)"
                    @mouseleave="release()"
                >
                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$icon] }}"/></svg>
                    <span class="sb-label">{{ $label }}</span>
                </a>
            @endforeach
        @endif

        {{-- Quick Sale --}}
        @php $posActive = request()->routeIs('pos.*'); @endphp
        <a
            href="{{ route('sales.create') }}"
            class="sidebar-link {{ $posActive ? 'sidebar-link-active' : '' }}"
            @if($posActive) aria-current="page" @endif
            @mouseenter="hoverTip(@js(__('Quick Sale')), $el)"
            @mouseleave="release()"
        >
            <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['pos'] }}"/></svg>
            <span class="sb-label">{{ __('Quick Sale') }}</span>
        </a>

        {{-- Orders --}}
        @if($currentShop && $ordersRoute)
            @php $ordersLabel = $isWaiter ? __('Take Order') : __('Orders'); @endphp

            <div class="sidebar-divider mt-4 border-t pt-4">
                <a
                    href="{{ $ordersRoute }}"
                    class="sidebar-link {{ request()->routeIs('shops.orders.*') ? 'sidebar-link-active' : '' }}"
                    @if(request()->routeIs('shops.orders.*')) aria-current="page" @endif
                    @mouseenter="hoverTip(@js($ordersLabel), $el)"
                    @mouseleave="release()"
                >
                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['orders'] }}"/></svg>
                    <span class="sb-label flex-1">{{ $ordersLabel }}</span>

                    @if($pendingOrdersCount > 0)
                        <span class="sb-label sidebar-badge">
                            {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                        </span>
                        <span class="sb-dot" aria-hidden="true"></span>
                    @endif
                </a>
            </div>
        @endif

        {{-- Pushes Log Out to the bottom when the list is short; collapses when the list is long --}}
        <div class="flex-1" style="min-height: 1rem"></div>

        {{-- Log out --}}
        <form method="POST" action="{{ route('logout') }}" class="sidebar-divider border-t pt-3">
            @csrf
            <button
                type="submit"
                class="sidebar-link sidebar-link-danger"
                @mouseenter="hoverTip(@js(__('Log Out')), $el)"
                @mouseleave="release()"
            >
                <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['logout'] }}"/></svg>
                <span class="sb-label">{{ __('Log Out') }}</span>
            </button>
        </form>
    </nav>

    {{-- Flyouts (collapsed desktop rail). Fixed-position so the nav's overflow can't clip them. --}}
    @foreach($groups as $key => $group)
        <div
            x-show="rail && flyout === '{{ $key }}'"
            x-cloak
            x-transition.opacity.duration.100ms
            @mouseenter="hold()"
            @mouseleave="release()"
            :style="`top:${flyoutTop}px`"
            class="fixed left-[4.5rem] z-[60] pl-2"
        >
            <div class="sidebar-flyout w-56 p-1.5">
                <div class="sidebar-muted px-2.5 pb-1 pt-1.5 text-xs font-semibold">{{ $group['label'] }}</div>
                @foreach($group['items'] as [$label, $routeName, $pattern])
                    <a href="{{ route($routeName) }}" class="sidebar-sub-link {{ request()->routeIs($pattern) ? 'sidebar-sub-link-active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Tooltip for single links (collapsed desktop rail) --}}
    <div
        x-show="rail && tip"
        x-cloak
        :style="`top:${tipTop}px`"
        x-text="tip"
        class="sidebar-tip pointer-events-none fixed left-[4.5rem] z-[60] ml-2 -translate-y-1/2 whitespace-nowrap rounded-md px-2.5 py-1.5 text-xs font-medium"
    ></div>
</aside>


<style>
    [x-cloak] { display: none !important; }

    /* Colour tokens (--surface, --border, ...) and --sidebar-w come from layouts/app.blade.php.
       Fallbacks keep the sidebar usable if this file is ever included on its own. */

    #app-sidebar {
        width: var(--sidebar-w, 18rem);
        height: 100vh;
        height: 100dvh;
        background-color: var(--surface, #fff);
        color: var(--text, #0f172a);
        border-right: 1px solid var(--border, #e2e8f0);
        box-shadow: var(--shadow, 0 10px 30px -10px rgb(15 23 42 / .25));
        transition: width .2s ease, transform .25s ease, visibility 0s;
    }

    /* ---------- Phones & tablets: off-canvas drawer ---------- */
    @media (max-width: 1023px) {
        #app-sidebar {
            width: min(18rem, 85vw);
            transform: translateX(-100%);
            visibility: hidden; /* closed drawer is not focusable / readable by screen readers */
            padding-left: env(safe-area-inset-left, 0px);
            transition: transform .25s ease, visibility 0s linear .25s;
        }
        #app-sidebar.is-open {
            transform: none;
            visibility: visible;
            transition: transform .25s ease, visibility 0s;
        }
        #app-sidebar nav { padding-bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px)); }
    }

    /* ---------- Desktop: fixed sidebar, optional icon rail ---------- */
    @media (min-width: 1024px) {
        #app-sidebar { transform: none; visibility: visible; box-shadow: 0 1px 2px rgb(0 0 0 / .05); }

        html.sidebar-collapsed #app-sidebar .sb-label,
        html.sidebar-collapsed #app-sidebar .sb-hide { display: none; }

        html.sidebar-collapsed #app-sidebar .sb-dot { display: block; }

        html.sidebar-collapsed #app-sidebar .sidebar-link,
        html.sidebar-collapsed #app-sidebar .sidebar-group-button {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        html.sidebar-collapsed #app-sidebar .sidebar-row  { justify-content: center; padding-left: 0; padding-right: 0; }
        html.sidebar-collapsed #app-sidebar .sidebar-shop { justify-content: center; padding-left: 0; padding-right: 0; margin-left: .75rem; margin-right: .75rem; }
    }

    .sb-dot {
        display: none;
        position: absolute;
        top: .55rem;
        right: 1.05rem;
        width: .5rem;
        height: .5rem;
        border-radius: 9999px;
        background-color: #f59e0b;
        box-shadow: 0 0 0 2px var(--surface, #fff);
    }

    /* ---------- Parts ---------- */
    .sidebar-brand,
    .sidebar-divider { border-color: var(--border, #e2e8f0); }
    .sidebar-brand   { border-bottom: 1px solid var(--border, #e2e8f0); }

    .sidebar-logo  { color: var(--text, #1f2937); }
    .sidebar-muted { color: var(--text-muted, #64748b); }

    .sidebar-collapse-btn,
    .sidebar-close-btn {
        color: var(--text-muted, #64748b);
        transition: color .15s ease, background-color .15s ease;
    }
    .sidebar-collapse-btn {
        background-color: var(--surface, #fff);
        border: 1px solid var(--border, #e2e8f0);
        box-shadow: 0 1px 2px rgb(0 0 0 / .08);
    }
    .sidebar-collapse-btn:hover { color: var(--accent-text, #047857); }
    .sidebar-close-btn:hover    { background-color: var(--hover, #f1f5f9); color: var(--text, #0f172a); }
    .sidebar-collapse-btn:focus-visible,
    .sidebar-close-btn:focus-visible { outline: 2px solid #10b981; outline-offset: 2px; }

    .sidebar-shop {
        border-radius: .75rem;
        background-color: var(--surface-2, #f8fafc);
        border: 1px solid var(--border, #e2e8f0);
    }
    .sidebar-shop-avatar { background-color: #059669; color: #fff; }
    .sidebar-shop-name   { color: var(--accent-text, #166534); }

    /* Links */
    .sidebar-link,
    .sidebar-group-button {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        border-radius: .7rem;
        font-size: .885rem;
        font-weight: 500;
        color: var(--text-soft, #475569);
        transition: background-color .15s ease, color .15s ease;
    }

    .sidebar-link         { gap: .75rem; padding: .7rem .8rem; }
    .sidebar-group-button { justify-content: space-between; padding: .7rem .8rem; }

    .sidebar-link:hover,
    .sidebar-group-button:hover { background-color: var(--hover, #f1f5f9); color: var(--text, #0f172a); }

    .sidebar-link:focus-visible,
    .sidebar-group-button:focus-visible,
    .sidebar-sub-link:focus-visible { outline: 2px solid #10b981; outline-offset: -2px; }

    .sidebar-link.sidebar-link-danger       { color: var(--danger-text, #dc2626); }
    .sidebar-link.sidebar-link-danger:hover { background-color: var(--danger-soft, #fef2f2); color: var(--danger-text, #b91c1c); }

    .sidebar-link-active       { background-color: var(--accent-soft, #ecfdf5); color: var(--accent-text, #047857); font-weight: 600; }
    .sidebar-link-active:hover { background-color: var(--accent-soft, #d1fae5); color: var(--accent-text, #047857); }

    /* Group that contains the current page */
    .sidebar-group-active { color: var(--accent-text, #047857); font-weight: 600; }

    .sidebar-icon { width: 1.15rem; height: 1.15rem; flex-shrink: 0; color: currentColor; }

    .sidebar-submenu {
        margin: .2rem 0 .3rem 1.1rem;
        padding-left: .8rem;
        border-left: 1px solid var(--border, #e2e8f0);
        display: flex;
        flex-direction: column;
        gap: .1rem;
    }

    .sidebar-sub-link {
        display: block;
        padding: .55rem .7rem;
        border-radius: .55rem;
        font-size: .8125rem;
        color: var(--text-muted, #64748b);
        transition: background-color .15s ease, color .15s ease;
    }
    .sidebar-sub-link:hover        { background-color: var(--hover, #f1f5f9); color: var(--text, #0f172a); }
    .sidebar-sub-link-active       { background-color: var(--accent-soft, #f0fdf4); color: var(--accent-text, #047857); font-weight: 600; }
    .sidebar-sub-link-active:hover { background-color: var(--accent-soft, #f0fdf4); color: var(--accent-text, #047857); }

    .sidebar-section-title {
        padding: .9rem .8rem .35rem;
        font-size: .75rem;
        font-weight: 600;
        color: var(--text-muted, #94a3b8);
    }

    .sidebar-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 20px;
        padding: 0 .3rem;
        border-radius: 9999px;
        background-color: #fef3c7;
        color: #92400e;
        font-size: 11px;
        font-weight: 700;
    }
    html.dark .sidebar-badge { background-color: rgb(245 158 11 / .2); color: #fcd34d; }

    /* Flyout + tooltip (collapsed rail) */
    .sidebar-flyout {
        border-radius: .75rem;
        background-color: var(--surface, #fff);
        border: 1px solid var(--border, #e2e8f0);
        box-shadow: var(--shadow, 0 10px 30px -10px rgb(15 23 42 / .25));
    }
    .sidebar-tip { background-color: #1e293b; color: #fff; box-shadow: 0 4px 12px rgb(0 0 0 / .25); }
    html.dark .sidebar-tip { background-color: #e2e8f0; color: #0f172a; }

    @media (prefers-reduced-motion: reduce) {
        #app-sidebar,
        #app-sidebar.is-open { transition: none; }
        .sidebar-link, .sidebar-group-button, .sidebar-sub-link { transition: none; }
    }
</style>