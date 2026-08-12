<nav x-data="{ open: false, openGroupMobile: null }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between py-4">
            <div class="flex flex-wrap items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:flex-wrap sm:gap-2 lg:gap-8">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('shop.dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('seller.dashboard') || request()->routeIs('accountant.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = ! open" type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ __('Inventory') }}
                                <svg :class="{'rotate-180': open}" class="ml-2 h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute left-0 z-20 mt-2 w-48 overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg">
                                <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Products') }}</a>
                                <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Categories') }}</a>
                                <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Suppliers') }}</a>
                                <a href="{{ route('customers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Customers') }}</a>
                            </div>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = ! open" type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ __('Transactions') }}
                                <svg :class="{'rotate-180': open}" class="ml-2 h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute left-0 z-20 mt-2 w-52 overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg">
                                <a href="{{ route('purchases.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Purchases') }}</a>
                                <a href="{{ route('sales.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Sales') }}</a>
                                <a href="{{ route('expenses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Expenses') }}</a>
                                <a href="{{ route('expensecategories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Expense Categories') }}</a>
                            </div>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = ! open" type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ __('Team') }}
                                <svg :class="{'rotate-180': open}" class="ml-2 h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute left-0 z-20 mt-2 w-40 overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg">
                                <a href="{{ route('staff.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Staff') }}</a>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->isSeller())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = ! open" type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ __('Transactions') }}
                                <svg :class="{'rotate-180': open}" class="ml-2 h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute left-0 z-20 mt-2 w-40 overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg">
                                <a href="{{ route('purchases.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Purchases') }}</a>
                                <a href="{{ route('sales.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Sales') }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
                <!-- Language Switcher -->
                <x-language-switcher />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isAdmin())
                <div>
                    <button @click="openGroupMobile === 'inventory' ? openGroupMobile = null : openGroupMobile = 'inventory'" type="button" class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-700 rounded-md hover:bg-gray-100">
                        <span>{{ __('Inventory') }}</span>
                        <svg :class="{'rotate-180': openGroupMobile === 'inventory'}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroupMobile === 'inventory'" x-cloak class="space-y-1 pl-4 mt-1">
                        <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">{{ __('Products') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">{{ __('Categories') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">{{ __('Suppliers') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">{{ __('Customers') }}</x-responsive-nav-link>
                    </div>
                </div>

                <div>
                    <button @click="openGroupMobile === 'transactions' ? openGroupMobile = null : openGroupMobile = 'transactions'" type="button" class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-700 rounded-md hover:bg-gray-100">
                        <span>{{ __('Transactions') }}</span>
                        <svg :class="{'rotate-180': openGroupMobile === 'transactions'}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroupMobile === 'transactions'" x-cloak class="space-y-1 pl-4 mt-1">
                        <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">{{ __('Purchases') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">{{ __('Sales') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">{{ __('Expenses') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('expensecategories.index')" :active="request()->routeIs('expensecategories.*')">{{ __('Expense Categories') }}</x-responsive-nav-link>
                    </div>
                </div>

                <div>
                    <button @click="openGroupMobile === 'team' ? openGroupMobile = null : openGroupMobile = 'team'" type="button" class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-700 rounded-md hover:bg-gray-100">
                        <span>{{ __('Team') }}</span>
                        <svg :class="{'rotate-180': openGroupMobile === 'team'}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroupMobile === 'team'" x-cloak class="space-y-1 pl-4 mt-1">
                        <x-responsive-nav-link :href="route('staff.index')" :active="request()->routeIs('staff.*')">{{ __('Staff') }}</x-responsive-nav-link>
                    </div>
                </div>
            @endif

            @if(Auth::user()->isSeller())
                <div>
                    <button @click="openGroupMobile === 'transactions' ? openGroupMobile = null : openGroupMobile = 'transactions'" type="button" class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-700 rounded-md hover:bg-gray-100">
                        <span>{{ __('Transactions') }}</span>
                        <svg :class="{'rotate-180': openGroupMobile === 'transactions'}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroupMobile === 'transactions'" x-cloak class="space-y-1 pl-4 mt-1">
                        <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">{{ __('Purchases') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">{{ __('Sales') }}</x-responsive-nav-link>
                    </div>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Language Switcher for Mobile -->
                <div class="px-4 py-2">
                    <div class="text-sm font-medium text-gray-500 mb-2">{{ __('Language') }}</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['en' => '🇬🇧', 'fr' => '🇫🇷', 'rw' => '🇷🇼', 'sw' => '🇹🇿'] as $code => $flag)
                            <a href="{{ route('language.switch', $code) }}"
                               class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ app()->getLocale() === $code ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                <span class="mr-1">{{ $flag }}</span>
                                {{ $code === 'en' ? 'EN' : ($code === 'fr' ? 'FR' : ($code === 'rw' ? 'RW' : 'SW')) }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
