@php
    /*
    |--------------------------------------------------------------------------
    | Context
    |--------------------------------------------------------------------------
    */
    $currentUser = Auth::user();
    $currentShop = $shop ?? $currentUser?->shop;

    $isAdmin  = $currentUser?->isAdmin();
    $isSeller = $currentUser?->isSeller();
    $isWaiter = $currentUser?->role === 'waiter';

    $ordersRoute = null;
    $pendingOrdersCount = 0;

    if ($currentShop) {
        $ordersRoute = route('shops.orders.index', ['shop' => $currentShop]);

        if (!$isWaiter) {
            $pendingOrdersCount = $currentShop->orders()->pending()->count();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Profile image / initials / role label
    |--------------------------------------------------------------------------
    */
    $profileImage = null;

    if (!empty($currentUser?->profile_photo_path)) {
        $profileImage = asset('storage/' . $currentUser->profile_photo_path);
    } elseif (!empty($currentUser?->profile_image)) {
        $profileImage = asset('storage/' . $currentUser->profile_image);
    } elseif (!empty($currentUser?->avatar)) {
        $profileImage = asset('storage/' . $currentUser->avatar);
    }

    $userInitials = collect(preg_split('/\s+/', trim($currentUser?->name ?? 'U')))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $userRole = match ($currentUser?->role) {
        'admin', 'shop_admin' => __('Administrator'),
        'seller'              => __('Seller'),
        'waiter'              => __('Waiter'),
        'accountant'          => __('Accountant'),
        'owner'               => __('Owner'),
        default               => ucfirst(str_replace('_', ' ', $currentUser?->role ?? 'User')),
    };

    /*
    |--------------------------------------------------------------------------
    | Icons (Heroicons outline, 24x24)
    |--------------------------------------------------------------------------
    */
    $iconPaths = [
        'home'      => 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        'pos'       => 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z',
        'bolt'      => 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
        'cube'      => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9',
        'cash'      => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
        'users'     => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'clipboard' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z',
        'user'      => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        'logout'    => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75',
        'chevron'   => 'M19.5 8.25l-7.5 7.5-7.5-7.5',
        'bars'      => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
        'close'     => 'M6 18L18 6M6 6l12 12',
        'collapse'  => 'M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5',
    ];

    $svg = fn (string $name, string $class = 'h-5 w-5 shrink-0') =>
        '<svg class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">'
        . '<path stroke-linecap="round" stroke-linejoin="round" d="' . ($iconPaths[$name] ?? '') . '"/></svg>';

    /*
    |--------------------------------------------------------------------------
    | Menu definition (same routes / route names / role rules as before)
    |--------------------------------------------------------------------------
    */
    $isActive = fn (array $patterns) => request()->routeIs(...$patterns);

    $link = fn (string $label, string $href, string $icon, array $patterns, int $badge = 0) => [
        'type'   => 'link',
        'label'  => $label,
        'href'   => $href,
        'icon'   => $icon,
        'active' => $isActive($patterns),
        'badge'  => $badge,
    ];

    // $items = [[label, route name, [active patterns]], ...]
    $group = function (string $key, string $label, string $icon, array $items) use ($isActive) {
        $items = array_map(fn ($i) => [
            'label'  => $i[0],
            'href'   => route($i[1]),
            'active' => $isActive($i[2]),
        ], $items);

        return [
            'type'   => 'group',
            'key'    => $key,
            'label'  => $label,
            'icon'   => $icon,
            'items'  => $items,
            'active' => collect($items)->contains('active', true),
        ];
    };

    $overview = [
        $link(__('Dashboard'), route('dashboard'), 'home', [
            'dashboard', 'shop.dashboard', 'admin.dashboard', 'seller.dashboard', 'accountant.dashboard',
        ]),
    ];

    $sales  = [];
    $manage = [];

    // ---------------- ADMIN ----------------
    if ($isAdmin) {
        $sales[] = $link(__('POS'), route('pos.index'), 'pos', ['pos.*']);
        $sales[] = $link(__('Quick Sale'), route('pos.index'), 'bolt', ['pos.*']);

        $manage[] = $group('admin-inventory', __('Inventory'), 'cube', [
            [__('Products'),   'products.index',   ['products.*']],
            [__('Categories'), 'categories.index', ['categories.*']],
            [__('Suppliers'),  'suppliers.index',  ['suppliers.*']],
            [__('Customers'),  'customers.index',  ['customers.*']],
        ]);

        $manage[] = $group('admin-transactions', __('Transactions'), 'cash', [
            [__('Purchases'),          'purchases.index',          ['purchases.*']],
            [__('Sales'),              'sales.index',              ['sales.*']],
            [__('Other Revenue'),      'other_incomes.index',      ['other_incomes.*']],
            [__('Income category'),    'income_categories.index',  ['income_categories.*']],
            [__('Expenses'),           'expenses.index',           ['expenses.*']],
            [__('Expense Categories'), 'expensecategories.index',  ['expensecategories.*']],
        ]);

        $manage[] = $group('admin-team', __('Team'), 'users', [
            [__('Staff'), 'staff.index', ['staff.*']],
        ]);
    }

    // ---------------- SELLER ----------------
    if ($isSeller) {
        $sales[] = $link(__('Quick Sale'), route('pos.index'), 'bolt', ['pos.*']);

        $manage[] = $group('seller-transactions', __('Transactions'), 'cash', [
            [__('Sales'),     'sales.index',     ['sales.*']],
            [__('Purchases'), 'purchases.index', ['purchases.*']],
            [__('Expenses'),  'expenses.index',  ['expenses.*']],
        ]);

        $manage[] = $group('seller-inventory', __('Inventory'), 'cube', [
            [__('Products'),   'products.index',   ['products.*']],
            [__('Categories'), 'categories.index', ['categories.*']],
        ]);
    }

    // ---------------- ORDERS (all roles with a shop, incl. waiter) ----------------
    if ($currentShop && $ordersRoute) {
        $sales[] = $link(
            $isWaiter ? __('Take Order') : __('Orders'),
            $ordersRoute,
            'clipboard',
            ['shops.orders.*'],
            $pendingOrdersCount
        );
    }

    $sections = array_values(array_filter([
        ['title' => __('Overview'),   'items' => $overview],
        ['title' => __('Sales'),      'items' => $sales],
        ['title' => __('Management'), 'items' => $manage],
    ], fn ($s) => count($s['items']) > 0));

    // Open the group that contains the current page
    $activeGroup = collect($sections)
        ->flatMap(fn ($s) => $s['items'])
        ->first(fn ($i) => $i['type'] === 'group' && $i['active'])['key'] ?? '';
@endphp

{{-- ====================================================================
     MOBILE HEADER (below md only)
===================================================================== --}}
<header class="sticky top-0 z-30 flex h-14 items-center gap-3 border-b border-gray-200 bg-white px-4 md:hidden">
    <button
        type="button"
        @click="sidebarOpen = true"
        class="-ml-2 inline-flex h-11 w-11 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
        aria-label="{{ __('Open navigation menu') }}"
        aria-controls="app-sidebar"
        :aria-expanded="sidebarOpen.toString()"
    >
        {!! $svg('bars', 'h-6 w-6') !!}
    </button>

    <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2">
        <x-application-logo class="block h-7 w-auto fill-current text-gray-800" />
        <span class="truncate text-base font-bold tracking-tight text-gray-900">MAHWI</span>
    </a>
</header>

{{-- ====================================================================
     OVERLAY (mobile drawer)
===================================================================== --}}
<div
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition-opacity ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-gray-900/50 md:hidden"
    aria-hidden="true"
></div>

{{-- ====================================================================
     SIDEBAR
     < md : off-canvas drawer (280px, never wider than 85% of viewport)
     md   : fixed rail (compact 76px or expanded 272px)
===================================================================== --}}
<aside
    id="app-sidebar"
    x-data="{
        openGroup: @js($activeGroup),
        toggleGroup(key) {
            if (collapsed && window.innerWidth >= 768) {
                collapsed = false;
                this.openGroup = key;
                return;
            }
            this.openGroup = this.openGroup === key ? null : key;
        }
    }"
    class="fixed inset-y-0 left-0 z-50 flex w-[280px] max-w-[85vw] -translate-x-full transform flex-col border-r border-gray-200 bg-white transition-[transform,width] duration-200 ease-out md:max-w-none md:translate-x-0"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        collapsed ? 'md:w-[76px]' : 'md:w-[272px]'
    ]"
    aria-label="{{ __('Main navigation') }}"
>

    {{-- Brand --}}
    <div
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-gray-100 px-4"
        :class="collapsed ? 'md:justify-center md:px-0' : ''"
    >
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
            <x-application-logo class="block h-8 w-auto shrink-0 fill-current text-gray-800" />
            <span class="truncate text-lg font-bold tracking-tight text-gray-900" :class="collapsed ? 'md:hidden' : ''">MAHWI</span>
        </a>

        {{-- Close (mobile only) --}}
        <button
            type="button"
            @click="sidebarOpen = false"
            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 md:hidden"
            aria-label="{{ __('Close navigation menu') }}"
        >
            {!! $svg('close', 'h-6 w-6') !!}
        </button>
    </div>

    {{-- Navigation (scrolls independently, never horizontally) --}}
    <nav
        class="flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 py-4"
        @click="if ($event.target.closest('a')) sidebarOpen = false"
    >
        @foreach ($sections as $section)
            <div class="space-y-1">

                <p
                    class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500"
                    :class="collapsed ? 'md:hidden' : ''"
                >
                    {{ $section['title'] }}
                </p>

                {{-- Divider replaces the section title in compact mode --}}
                @unless ($loop->first)
                    <hr class="hidden border-gray-200 md:mb-2" :class="collapsed ? 'md:block' : ''" aria-hidden="true">
                @endunless

                @foreach ($section['items'] as $item)

                    {{-- ======================= SINGLE LINK ======================= --}}
                    @if ($item['type'] === 'link')
                        <a
                            href="{{ $item['href'] }}"
                            title="{{ $item['label'] }}"
                            @if ($item['active']) aria-current="page" @endif
                            class="group relative flex min-h-[44px] items-center gap-3 rounded-lg px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                                {{ $item['active']
                                    ? 'bg-indigo-50 font-semibold text-indigo-700'
                                    : 'font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                            :class="collapsed ? 'md:justify-center md:px-0' : ''"
                        >
                            @if ($item['active'])
                                <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-indigo-600" aria-hidden="true"></span>
                            @endif

                            <span class="relative">
                                {!! $svg($item['icon']) !!}

                                @if ($item['badge'] > 0)
                                    <span
                                        x-show="collapsed"
                                        class="absolute -right-1 -top-1 hidden h-2.5 w-2.5 rounded-full bg-amber-500 ring-2 ring-white md:block"
                                        aria-hidden="true"
                                    ></span>
                                @endif
                            </span>

                            <span class="min-w-0 flex-1 truncate" :class="collapsed ? 'md:hidden' : ''">
                                {{ $item['label'] }}
                            </span>

                            @if ($item['badge'] > 0)
                                <span
                                    class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-amber-100 px-1.5 text-[11px] font-bold text-amber-800"
                                    :class="collapsed ? 'md:hidden' : ''"
                                >
                                    {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                </span>
                            @endif
                        </a>

                    {{-- ======================= DROPDOWN GROUP ======================= --}}
                    @else
                        <div>
                            <button
                                type="button"
                                title="{{ $item['label'] }}"
                                @click="toggleGroup('{{ $item['key'] }}')"
                                :aria-expanded="(openGroup === '{{ $item['key'] }}' && !collapsed).toString()"
                                aria-controls="submenu-{{ $item['key'] }}"
                                class="group relative flex min-h-[44px] w-full items-center gap-3 rounded-lg px-3 text-left text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                                    {{ $item['active']
                                        ? 'font-semibold text-indigo-700'
                                        : 'font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                                :class="collapsed ? 'md:justify-center md:px-0' : ''"
                            >
                                @if ($item['active'])
                                    <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-indigo-600" aria-hidden="true"></span>
                                @endif

                                {!! $svg($item['icon']) !!}

                                <span class="min-w-0 flex-1 truncate" :class="collapsed ? 'md:hidden' : ''">
                                    {{ $item['label'] }}
                                </span>

                                <span
                                    class="transition-transform duration-200"
                                    :class="[collapsed ? 'md:hidden' : '', openGroup === '{{ $item['key'] }}' ? 'rotate-180' : '']"
                                >
                                    {!! $svg('chevron', 'h-4 w-4 shrink-0 text-gray-400') !!}
                                </span>
                            </button>

                            <div
                                id="submenu-{{ $item['key'] }}"
                                x-show="openGroup === '{{ $item['key'] }}'"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="ml-5 mt-1 space-y-1 border-l border-gray-200 pl-3"
                                :class="collapsed ? 'md:hidden' : ''"
                            >
                                @foreach ($item['items'] as $sub)
                                    <a
                                        href="{{ $sub['href'] }}"
                                        @if ($sub['active']) aria-current="page" @endif
                                        class="flex min-h-[40px] items-center gap-2 rounded-lg px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                                            {{ $sub['active']
                                                ? 'bg-indigo-50 font-semibold text-indigo-700'
                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                                    >
                                        @if ($sub['active'])
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600" aria-hidden="true"></span>
                                        @endif
                                        <span class="truncate">{{ $sub['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @endforeach
            </div>
        @endforeach
    </nav>

    {{-- ================================================================
         FOOTER: language, collapse toggle, profile, logout
    ================================================================= --}}
    <div class="shrink-0 space-y-2 border-t border-gray-100 p-3">

        {{-- Language switcher --}}
        <div class="px-1" :class="collapsed ? 'md:hidden' : ''">
            <p class="mb-1.5 px-2 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Language') }}</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach (['en' => '🇬🇧', 'fr' => '🇫🇷', 'rw' => '🇷🇼', 'sw' => '🇹🇿'] as $code => $flag)
                    <a
                        href="{{ route('language.switch', $code) }}"
                        @if (app()->getLocale() === $code) aria-current="true" @endif
                        class="inline-flex min-h-[36px] items-center gap-1 rounded-lg px-2.5 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                            {{ app()->getLocale() === $code
                                ? 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        <span aria-hidden="true">{{ $flag }}</span>
                        {{ strtoupper($code) }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Collapse / expand (md and up) --}}
        <button
            type="button"
            @click="toggleCollapsed()"
            :aria-label="collapsed ? '{{ __('Expand sidebar') }}' : '{{ __('Collapse sidebar') }}'"
            :aria-expanded="(!collapsed).toString()"
            aria-controls="app-sidebar"
            class="hidden min-h-[44px] w-full items-center gap-3 rounded-lg px-3 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 md:flex"
            :class="collapsed ? 'md:justify-center md:px-0' : ''"
        >
            <span class="transition-transform duration-200" :class="collapsed ? 'rotate-180' : ''">
                {!! $svg('collapse') !!}
            </span>
            <span :class="collapsed ? 'md:hidden' : ''">{{ __('Collapse') }}</span>
        </button>

        {{-- Profile + logout --}}
        <div
            class="flex items-center gap-1 rounded-xl border border-gray-200 p-1.5"
            :class="collapsed ? 'md:flex-col md:border-0 md:p-0' : ''"
        >
            <a
                href="{{ route('profile.edit') }}"
                title="{{ __('Profile Settings') }}"
                class="flex min-h-[44px] min-w-0 flex-1 items-center gap-3 rounded-lg px-1.5 transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                :class="collapsed ? 'md:flex-none md:justify-center md:px-0' : ''"
            >
                <span class="relative shrink-0">
                    @if ($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $currentUser->name }}" class="h-9 w-9 rounded-full object-cover ring-1 ring-gray-200">
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
                            {{ $userInitials ?: 'U' }}
                        </span>
                    @endif
                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white" aria-hidden="true"></span>
                </span>

                <span class="min-w-0 flex-1" :class="collapsed ? 'md:hidden' : ''">
                    <span class="block truncate text-sm font-semibold text-gray-900">{{ $currentUser->name }}</span>
                    <span class="block truncate text-xs text-gray-500">{{ $userRole }}</span>
                </span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button
                    type="submit"
                    title="{{ __('Log Out') }}"
                    aria-label="{{ __('Log Out') }}"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-red-600 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                >
                    {!! $svg('logout') !!}
                </button>
            </form>
        </div>
    </div>
</aside>