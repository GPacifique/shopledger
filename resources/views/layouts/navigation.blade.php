<nav
    x-data="{ open: false, openGroupMobile: null }"
    class="bg-white border-b border-gray-100 shadow-sm relative z-50"
>
    @php
        $currentUser = Auth::user();

        // Current shop
        $currentShop = $shop ?? $currentUser?->shop;

        // Roles
        $isAdmin = $currentUser?->isAdmin();
        $isSeller = $currentUser?->isSeller();
        $isWaiter = $currentUser?->role === 'waiter';

        // Orders
        $ordersRoute = null;
        $pendingOrdersCount = 0;

        if ($currentShop) {
            $ordersRoute = route('shops.orders.index', [
                'shop' => $currentShop
            ]);

            if (!$isWaiter) {
                $pendingOrdersCount = $currentShop
                    ->orders()
                    ->pending()
                    ->count();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Profile Image
        |--------------------------------------------------------------------------
        | Supports:
        | 1. profile_photo_path
        | 2. profile_image
        | 3. avatar
        |
        | Change the priority below if your users table uses another field.
        */
        $profileImage = null;

        if (!empty($currentUser?->profile_photo_path)) {
            $profileImage = asset('storage/' . $currentUser->profile_photo_path);
        } elseif (!empty($currentUser?->profile_image)) {
            $profileImage = asset('storage/' . $currentUser->profile_image);
        } elseif (!empty($currentUser?->avatar)) {
            $profileImage = asset('storage/' . $currentUser->avatar);
        }

        $userInitials = collect(
            preg_split('/\s+/', trim($currentUser?->name ?? 'U'))
        )
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');

        $userRole = match ($currentUser?->role) {
            'admin', 'shop_admin' => __('Administrator'),
            'seller' => __('Seller'),
            'waiter' => __('Waiter'),
            'accountant' => __('Accountant'),
            'owner' => __('Owner'),
            default => ucfirst(str_replace('_', ' ', $currentUser?->role ?? 'User')),
        };
    @endphp

    <!-- ============================================================
         DESKTOP / TABLET NAVIGATION
    ============================================================= -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="min-h-[72px] flex items-center justify-between gap-4">

            <!-- LEFT SIDE -->
            <div class="flex items-center min-w-0">

                <!-- Logo -->
                <div class="shrink-0">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center"
                    >
                        <x-application-logo
                            class="block h-9 w-auto fill-current text-gray-800"
                        />
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center ml-8 gap-2">

                    <!-- Dashboard -->
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="
                            request()->routeIs('dashboard') ||
                            request()->routeIs('shop.dashboard') ||
                            request()->routeIs('admin.dashboard') ||
                            request()->routeIs('seller.dashboard') ||
                            request()->routeIs('accountant.dashboard')
                        "
                    >
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- ==================================================
                         ADMIN
                    =================================================== --}}
                    @if($isAdmin)

                        <!-- Inventory -->
                        <div x-data="{ open: false }" class="relative">

                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                type="button"
                                class="nav-menu-button"
                            >
                                {{ __('Inventory') }}

                                <svg
                                    :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                @click.outside="open = false"
                                x-transition
                                class="dropdown-menu w-52"
                            >
                                <a href="{{ route('products.index') }}" class="dropdown-item">
                                    {{ __('Products') }}
                                </a>

                                <a href="{{ route('categories.index') }}" class="dropdown-item">
                                    {{ __('Categories') }}
                                </a>

                                <a href="{{ route('suppliers.index') }}" class="dropdown-item">
                                    {{ __('Suppliers') }}
                                </a>

                                <a href="{{ route('customers.index') }}" class="dropdown-item">
                                    {{ __('Customers') }}
                                </a>
                            </div>
                        </div>

                        <!-- Transactions -->
                        <div x-data="{ open: false }" class="relative">

                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                type="button"
                                class="nav-menu-button"
                            >
                                {{ __('Transactions') }}

                                <svg
                                    :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                @click.outside="open = false"
                                x-transition
                                class="dropdown-menu w-56"
                            >
                                <a href="{{ route('purchases.index') }}" class="dropdown-item">
                                    {{ __('Purchases') }}
                                </a>

                                <a href="{{ route('sales.index') }}" class="dropdown-item">
                                    {{ __('Sales') }}
                                </a>
                                 <a href="{{ route('other_incomes.index') }}" class="dropdown-item">
                                    {{ __('Other Revenue') }}
                                </a>
                                <a href="{{ route('income_categories.index') }}" class="dropdown-item">
                                    {{ __('Income category') }}
                                </a>

                                <a href="{{ route('expenses.index') }}" class="dropdown-item">
                                    {{ __('Expenses') }}
                                </a>

                                <a href="{{ route('expensecategories.index') }}" class="dropdown-item">
                                    {{ __('Expense Categories') }}
                                </a>
                            </div>
                        </div>

                        <!-- Team -->
                        <div x-data="{ open: false }" class="relative">

                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                type="button"
                                class="nav-menu-button"
                            >
                                {{ __('Team') }}

                                <svg
                                    :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                @click.outside="open = false"
                                x-transition
                                class="dropdown-menu w-44"
                            >
                                <a href="{{ route('staff.index') }}" class="dropdown-item">
                                    {{ __('Staff') }}
                                </a>
                            </div>
                        </div>

                    @endif


                    {{-- ==================================================
                         SELLER
                    =================================================== --}}
                    @if($isSeller)

                        <!-- Transactions -->
                        <div x-data="{ open: false }" class="relative">

                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                type="button"
                                class="nav-menu-button"
                            >
                                {{ __('Transactions') }}

                                <svg
                                    :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                @click.outside="open = false"
                                x-transition
                                class="dropdown-menu w-48"
                            >
                                <a href="{{ route('sales.index') }}" class="dropdown-item">
                                    {{ __('Sales') }}
                                </a>

                                <a href="{{ route('purchases.index') }}" class="dropdown-item">
                                    {{ __('Purchases') }}
                                </a>

                                <a href="{{ route('expenses.index') }}" class="dropdown-item">
                                    {{ __('Expenses') }}
                                </a>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <div x-data="{ open: false }" class="relative">

                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                type="button"
                                class="nav-menu-button"
                            >
                                {{ __('Inventory') }}

                                <svg
                                    :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                @click.outside="open = false"
                                x-transition
                                class="dropdown-menu w-48"
                            >
                                <a href="{{ route('products.index') }}" class="dropdown-item">
                                    {{ __('Products') }}
                                </a>

                                <a href="{{ route('categories.index') }}" class="dropdown-item">
                                    {{ __('Categories') }}
                                </a>
                            </div>
                        </div>

                    @endif


                    {{-- ==================================================
                         ORDERS
                    =================================================== --}}
                    @if($currentShop && $ordersRoute)

                        <a
                            href="{{ $ordersRoute }}"
                            class="relative inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition
                                {{ request()->routeIs('shops.orders.*')
                                    ? 'bg-indigo-50 text-indigo-700'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                        >
                            {{ $isWaiter ? __('Take Order') : __('Orders') }}

                            @if($pendingOrdersCount > 0)
                                <span class="min-w-[20px] h-5 px-1 inline-flex items-center justify-center rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">
                                    {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                                </span>
                            @endif
                        </a>

                    @endif

                </div>
            </div>


            <!-- ========================================================
                 RIGHT SIDE
            ========================================================= -->
            <div class="hidden lg:flex items-center gap-3 shrink-0">

                <!-- Language -->
                <x-language-switcher />

                <!-- User Profile -->
                <div x-data="{ open: false }" class="relative">

                    <button
                        @click="open = !open"
                        @keydown.escape.window="open = false"
                        type="button"
                        class="flex items-center gap-3 rounded-xl px-2 py-1.5 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    >

                        <!-- Avatar -->
                        <div class="relative shrink-0">

                            @if($profileImage)
                                <img
                                    src="{{ $profileImage }}"
                                    alt="{{ $currentUser->name }}"
                                    class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-gray-200"
                                >
                            @else
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-1 ring-indigo-200">
                                    {{ $userInitials ?: 'U' }}
                                </div>
                            @endif

                            <!-- Online indicator -->
                            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"></span>
                        </div>

                        <!-- Name -->
                        <div class="text-left max-w-[140px] hidden xl:block">
                            <div class="text-sm font-semibold text-gray-800 truncate">
                                {{ $currentUser->name }}
                            </div>

                            <div class="text-[11px] text-gray-500 truncate">
                                {{ $userRole }}
                            </div>
                        </div>

                        <svg
                            :class="{ 'rotate-180': open }"
                            class="w-4 h-4 text-gray-400 transition-transform"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>

                    </button>


                    <!-- User Dropdown -->
                    <div
                        x-show="open"
                        x-cloak
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
                    >

                        <!-- Profile Header -->
                        <div class="p-4 bg-gradient-to-br from-indigo-50 via-white to-purple-50 border-b border-gray-100">

                            <div class="flex items-center gap-3">

                                @if($profileImage)
                                    <img
                                        src="{{ $profileImage }}"
                                        alt="{{ $currentUser->name }}"
                                        class="h-12 w-12 rounded-full object-cover border-2 border-white shadow ring-1 ring-gray-200"
                                    >
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold shadow">
                                        {{ $userInitials ?: 'U' }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">
                                        {{ $currentUser->name }}
                                    </p>

                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $currentUser->email }}
                                    </p>

                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-semibold">
                                        {{ $userRole }}
                                    </span>
                                </div>

                            </div>

                        </div>

                        <!-- Links -->
                        <div class="p-2">

                            <a
                                href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-700 hover:bg-gray-50 transition"
                            >
                                <span class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>

                                <span>{{ __('Profile Settings') }}</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-600 hover:bg-red-50 transition"
                                >
                                    <span class="h-8 w-8 rounded-lg bg-red-50 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </span>

                                    <span>{{ __('Log Out') }}</span>
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ========================================================
                 MOBILE HEADER
            ========================================================= -->
            <div class="flex lg:hidden items-center gap-2">

                <!-- Small Avatar -->
                <div class="shrink-0">

                    @if($profileImage)
                        <img
                            src="{{ $profileImage }}"
                            alt="{{ $currentUser->name }}"
                            class="h-9 w-9 rounded-full object-cover border border-gray-200"
                        >
                    @else
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-xs">
                            {{ $userInitials ?: 'U' }}
                        </div>
                    @endif

                </div>

                <!-- Hamburger -->
                <button
                    @click="open = !open"
                    type="button"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{ 'hidden': open, 'inline-flex': !open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{ 'hidden': !open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12-12"
                        />
                    </svg>
                </button>

            </div>

        </div>
    </div>


    <!-- ================================================================
         MOBILE NAVIGATION
    ================================================================= -->
    <div
        x-show="open"
        x-cloak
        x-transition
        class="lg:hidden border-t border-gray-100 bg-white shadow-lg"
    >

        <div class="max-h-[calc(100vh-72px)] overflow-y-auto">

            <!-- Mobile User Header -->
            <div class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    @if($profileImage)
                        <img
                            src="{{ $profileImage }}"
                            alt="{{ $currentUser->name }}"
                            class="h-12 w-12 rounded-full object-cover border-2 border-white shadow"
                        >
                    @else
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold">
                            {{ $userInitials ?: 'U' }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <div class="font-semibold text-gray-900 truncate">
                            {{ $currentUser->name }}
                        </div>

                        <div class="text-xs text-gray-500 truncate">
                            {{ $currentUser->email }}
                        </div>

                        <span class="inline-flex mt-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-semibold">
                            {{ $userRole }}
                        </span>
                    </div>

                </div>

            </div>


            <!-- Mobile Navigation -->
            <div class="p-3 space-y-1">

                <x-responsive-nav-link
                    :href="route('dashboard')"
                    :active="
                        request()->routeIs('dashboard') ||
                        request()->routeIs('shop.dashboard') ||
                        request()->routeIs('admin.dashboard') ||
                        request()->routeIs('seller.dashboard') ||
                        request()->routeIs('accountant.dashboard')
                    "
                >
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>


                {{-- =====================================================
                     ADMIN MOBILE
                ====================================================== --}}
                @if($isAdmin)

                    <!-- Inventory -->
                    <div class="mobile-group">

                        <button
                            @click="openGroupMobile = openGroupMobile === 'inventory' ? null : 'inventory'"
                            type="button"
                            class="mobile-group-button"
                        >
                            <span>{{ __('Inventory') }}</span>

                            <svg
                                :class="{ 'rotate-180': openGroupMobile === 'inventory' }"
                                class="w-4 h-4 transition-transform"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="openGroupMobile === 'inventory'"
                            x-cloak
                            x-transition
                            class="mobile-submenu"
                        >
                            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                {{ __('Products') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                {{ __('Categories') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                {{ __('Suppliers') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                                {{ __('Customers') }}
                            </x-responsive-nav-link>
                        </div>

                    </div>


                    <!-- Transactions -->
                    <div class="mobile-group">

                        <button
                            @click="openGroupMobile = openGroupMobile === 'transactions' ? null : 'transactions'"
                            type="button"
                            class="mobile-group-button"
                        >
                            <span>{{ __('Transactions') }}</span>

                            <svg
                                :class="{ 'rotate-180': openGroupMobile === 'transactions' }"
                                class="w-4 h-4 transition-transform"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="openGroupMobile === 'transactions'"
                            x-cloak
                            x-transition
                            class="mobile-submenu"
                        >
                            <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                                {{ __('Purchases') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                                {{ __('Sales') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                                {{ __('Expenses') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('expensecategories.index')" :active="request()->routeIs('expensecategories.*')">
                                {{ __('Expense Categories') }}
                            </x-responsive-nav-link>
                        </div>

                    </div>


                    <!-- Team -->
                    <div class="mobile-group">

                        <button
                            @click="openGroupMobile = openGroupMobile === 'team' ? null : 'team'"
                            type="button"
                            class="mobile-group-button"
                        >
                            <span>{{ __('Team') }}</span>

                            <svg
                                :class="{ 'rotate-180': openGroupMobile === 'team' }"
                                class="w-4 h-4 transition-transform"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="openGroupMobile === 'team'"
                            x-cloak
                            x-transition
                            class="mobile-submenu"
                        >
                            <x-responsive-nav-link :href="route('staff.index')" :active="request()->routeIs('staff.*')">
                                {{ __('Staff') }}
                            </x-responsive-nav-link>
                        </div>

                    </div>

                @endif


                {{-- =====================================================
                     SELLER MOBILE
                ====================================================== --}}
                @if($isSeller)

                    <!-- Transactions -->
                    <div class="mobile-group">

                        <button
                            @click="openGroupMobile = openGroupMobile === 'seller-transactions' ? null : 'seller-transactions'"
                            type="button"
                            class="mobile-group-button"
                        >
                            <span>{{ __('Transactions') }}</span>

                            <svg
                                :class="{ 'rotate-180': openGroupMobile === 'seller-transactions' }"
                                class="w-4 h-4 transition-transform"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="openGroupMobile === 'seller-transactions'"
                            x-cloak
                            x-transition
                            class="mobile-submenu"
                        >
                            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                                {{ __('Sales') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                                {{ __('Purchases') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                                {{ __('Expenses') }}
                            </x-responsive-nav-link>
                        </div>

                    </div>


                    <!-- Inventory -->
                    <div class="mobile-group">

                        <button
                            @click="openGroupMobile = openGroupMobile === 'seller-inventory' ? null : 'seller-inventory'"
                            type="button"
                            class="mobile-group-button"
                        >
                            <span>{{ __('Inventory') }}</span>

                            <svg
                                :class="{ 'rotate-180': openGroupMobile === 'seller-inventory' }"
                                class="w-4 h-4 transition-transform"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="openGroupMobile === 'seller-inventory'"
                            x-cloak
                            x-transition
                            class="mobile-submenu"
                        >
                            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                {{ __('Products') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                {{ __('Categories') }}
                            </x-responsive-nav-link>
                        </div>

                    </div>

                @endif


                {{-- =====================================================
                     ORDERS MOBILE
                ====================================================== --}}
                @if($currentShop && $ordersRoute)

                    <a
                        href="{{ $ordersRoute }}"
                        class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition
                            {{ request()->routeIs('shops.orders.*')
                                ? 'bg-indigo-50 text-indigo-700'
                                : 'text-gray-700 hover:bg-gray-50' }}"
                    >
                        <span>
                            {{ $isWaiter ? __('Take Order') : __('Orders') }}
                        </span>

                        @if($pendingOrdersCount > 0)
                            <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                                {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>

                @endif

            </div>


            <!-- ========================================================
                 MOBILE ACCOUNT
            ========================================================= -->
            <div class="border-t border-gray-100 p-3">

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition"
                >
                    @if($profileImage)
                        <img
                            src="{{ $profileImage }}"
                            alt="{{ $currentUser->name }}"
                            class="h-9 w-9 rounded-full object-cover"
                        >
                    @else
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ $userInitials ?: 'U' }}
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $currentUser->name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ __('Profile Settings') }}
                        </p>
                    </div>

                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>


                <!-- Language -->
                <div class="px-4 py-3">

                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">
                        {{ __('Language') }}
                    </p>

                    <div class="flex flex-wrap gap-2">

                        @foreach([
                            'en' => '🇬🇧',
                            'fr' => '🇫🇷',
                            'rw' => '🇷🇼',
                            'sw' => '🇹🇿'
                        ] as $code => $flag)

                            <a
                                href="{{ route('language.switch', $code) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition
                                    {{ app()->getLocale() === $code
                                        ? 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                            >
                                <span>{{ $flag }}</span>
                                {{ strtoupper($code) }}
                            </a>

                        @endforeach

                    </div>

                </div>


                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>

                        {{ __('Log Out') }}
                    </button>
                </form>

            </div>

        </div>
    </div>


    <!-- ================================================================
         NAVIGATION STYLES
    ================================================================= -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        .nav-menu-button {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem .75rem;
            border-radius: .625rem;
            font-size: .875rem;
            font-weight: 500;
            color: #4b5563;
            background: transparent;
            transition: all .15s ease;
        }

        .nav-menu-button:hover {
            background: #f9fafb;
            color: #111827;
        }

        .nav-menu-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        .dropdown-menu {
            position: absolute;
            left: 0;
            top: 100%;
            z-index: 100;
            margin-top: .5rem;
            overflow: hidden;
            border-radius: .875rem;
            border: 1px solid #e5e7eb;
            background: white;
            box-shadow:
                0 10px 15px -3px rgba(0,0,0,.08),
                0 4px 6px -4px rgba(0,0,0,.08);
            padding: .35rem;
        }

        .dropdown-item {
            display: block;
            padding: .65rem .8rem;
            border-radius: .625rem;
            font-size: .875rem;
            color: #4b5563;
            transition: all .15s ease;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .mobile-group {
            border-radius: .875rem;
            overflow: hidden;
        }

        .mobile-group-button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1rem;
            border-radius: .75rem;
            font-size: .875rem;
            font-weight: 600;
            color: #374151;
            transition: all .15s ease;
        }

        .mobile-group-button:hover {
            background: #f9fafb;
        }

        .mobile-submenu {
            margin: .15rem 0 .35rem;
            padding-left: .5rem;
            border-left: 2px solid #eef2ff;
        }

        @media (max-width: 640px) {
            .dropdown-menu {
                max-width: calc(100vw - 2rem);
            }
        }
    </style>

</nav>