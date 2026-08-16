<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg transform hover:scale-105 transition-transform">
                    {{ strtoupper(substr($shop->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ $shop->name }}</h2>
                    <p class="text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ __('Shop Admin Dashboard') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $shop->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    <span class="w-2 h-2 rounded-full mr-2 animate-pulse {{ $shop->status === 'approved' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                    {{ __($shop->status === 'approved' ? 'Approved' : 'Pending') }}
                </span>
                <div class="text-right hidden md:block">
                    <p class="text-xs text-gray-500">{{ __('Today') }}</p>
                    <p class="text-sm font-semibold text-gray-700" id="current-time"></p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-0">

            @if($outOfStockProducts->isNotEmpty() || $expiringProducts->isNotEmpty() || $lowStockProducts->isNotEmpty())
                <div class="fixed right-4 top-20 z-50 w-[min(92vw,24rem)] rounded-2xl border border-amber-200 bg-white/95 shadow-2xl backdrop-blur">
                    <div class="flex items-center justify-between border-b border-amber-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-amber-700">{{ __('Inventory Alerts') }}</p>
                            <p class="text-xs text-amber-600">{{ __('Products needing attention') }}</p>
                        </div>
                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            {{ $outOfStockProducts->count() + $expiringProducts->count() }}
                        </span>
                    </div>
                    <div class="max-h-72 space-y-2 overflow-y-auto p-4">
                        @if($outOfStockProducts->isNotEmpty())
                            <div class="rounded-xl border border-red-100 bg-red-50 p-3">
                                <p class="text-sm font-semibold text-red-700">{{ __('Out of stock') }}</p>
                                @foreach($outOfStockProducts as $product)
                                    <div class="mt-2 flex items-center justify-between text-sm text-red-600">
                                        <span>{{ $product->name }}</span>
                                        <span class="font-medium">0 {{ __('in stock') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($expiringProducts->isNotEmpty())
                            <div class="rounded-xl border border-orange-100 bg-orange-50 p-3">
                                <p class="text-sm font-semibold text-orange-700">{{ __('Expiring soon') }}</p>
                                @foreach($expiringProducts as $product)
                                    <div class="mt-2 flex items-center justify-between text-sm text-orange-600">
                                        <span>{{ $product->name }}</span>
                                        <span class="font-medium">{{ $product->expiry_date->format('M d, Y') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Welcome Banner -->
            <div class="mb-8 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                <div class="relative px-6 py-8 sm:px-10 sm:py-10">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="welcome-grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <circle cx="1" cy="1" r="1" fill="white"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#welcome-grid)"/>
                        </svg>
                    </div>
                    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="text-white mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold mb-2">{{ __('Welcome back') }}, {{ auth()->user()->name }}! 👋</h3>
                            <p class="text-indigo-100">{{ __("Here's what's happening with your shop today.") }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('sales.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 rounded-xl font-semibold text-sm hover:bg-indigo-50 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                {{ __('New Sale') }}
                            </a>
                            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white/20 text-white rounded-xl font-semibold text-sm hover:bg-white/30 transition-all transform hover:scale-105 backdrop-blur">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                {{ __('New Purchase') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Profit Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="p-4 bg-green-50 shadow rounded-2xl border border-green-100">
                    <h3 class="text-green-600 text-lg font-bold">{{ __('Daily Net Profit') }}</h3>
                    <p class="text-2xl font-bold text-gray-900">RWF {{ number_format($dailyNetProfit, 0) }}</p>
                    <small class="text-gray-400">
                        {{ __('Sales') }}: {{ number_format($dailySales, 0) }} |
                        {{ __('Purchases') }}: {{ number_format($dailyPurchases, 0) }} |
                        {{ __('Expenses') }}: {{ number_format($dailyExpenses, 0) }}
                    </small>
                </div>
                <div class="p-4 bg-green-50 shadow rounded-2xl border border-green-100">
                    <h3 class="text-green-600 text-lg font-bold">{{ __('Weekly Net Profit') }}</h3>
                    <p class="text-2xl font-bold text-gray-900">RWF {{ number_format($weeklyNetProfit, 0) }}</p>
                    <small class="text-gray-400">
                        {{ __('Sales') }}: {{ number_format($weeklySales, 0) }} |
                        {{ __('Purchases') }}: {{ number_format($weeklyPurchases, 0) }} |
                        {{ __('Expenses') }}: {{ number_format($weeklyExpenses, 0) }}
                    </small>
                </div>
                <div class="p-4 bg-green-50 shadow rounded-2xl border border-green-100">
                    <h3 class="text-green-600 text-lg font-bold">{{ __('Yearly Net Profit') }}</h3>
                    <p class="text-2xl font-bold text-gray-900">RWF {{ number_format($yearlyNetProfit, 0) }}</p>
                    <small class="text-gray-400">
                        {{ __('Sales') }}: {{ number_format($yearlySales, 0) }} |
                        {{ __('Purchases') }}: {{ number_format($yearlyPurchases, 0) }} |
                        {{ __('Expenses') }}: {{ number_format($yearlyExpenses, 0) }}
                    </small>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
                <!-- Today's Sales -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.1s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">{{ __('Today') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __("Today's Sales") }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ rwf($stats['today_sales']) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-green-600 font-medium">{{ __('Revenue') }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-green-400 to-emerald-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Today's Purchases -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.15s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-red-400 to-rose-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-red-600 bg-red-50 px-2.5 py-1 rounded-full">{{ __('Today') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __("Today's Purchases") }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ rwf($stats['today_purchases']) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-red-600 font-medium">{{ __('Expenses') }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-red-400 to-rose-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Stock In Today -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.2s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-teal-400 to-cyan-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m0-16l-6 6m6-6l6 6"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">{{ __('Today') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('Stock In') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($todayStockIn) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-teal-600 font-medium">{{ __('Units received') }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-teal-400 to-cyan-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Stock Out Today -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.25s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-orange-400 to-amber-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4m0 16l-6-6m6 6l6-6"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">{{ __('Today') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('Stock Out') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($todayStockOut) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-orange-600 font-medium">{{ __('Units dispatched') }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-orange-400 to-amber-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Total Products -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.3s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-indigo-400 to-blue-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <a href="{{ route('products.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">{{ __('View All') }} →</a>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('Total Products') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-indigo-600 font-medium">{{ __('In inventory') }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-indigo-400 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Staff Members -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.35s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-purple-400 to-violet-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <a href="{{ route('staff.index') }}" class="text-xs font-medium text-purple-600 hover:text-purple-800 transition">{{ __('View All') }} →</a>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('Staff') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_staff']) }}</p>
                        <div class="mt-3 flex items-center text-sm">
                            <span class="text-purple-600 font-medium">{{ __('Active team') }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-purple-400 to-violet-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>
            </div>

            <!-- Monthly Summary, Shop Info & Payment Status -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Monthly Summary Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            {{ __('Monthly Overview') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl">
                                <span class="text-gray-700 font-medium">{{ __('Total Sales') }}</span>
                                <span class="text-xl font-bold text-green-600">{{ rwf($stats['month_sales']) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl">
                                <span class="text-gray-700 font-medium">{{ __('Total Purchases') }}</span>
                                <span class="text-xl font-bold text-red-600">{{ rwf($stats['month_purchases']) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ ($stats['month_sales'] - $stats['month_purchases']) >= 0 ? 'from-emerald-500 to-green-600' : 'from-red-500 to-rose-600' }} rounded-xl text-white shadow-lg">
                                <span class="font-semibold">{{ __('Gross Profit') }}</span>
                                <span class="text-2xl font-bold">{{ rwf($stats['month_sales'] - $stats['month_purchases']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shop Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ __('Shop Information') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                {{ strtoupper(substr($shop->name, 0, 2)) }}
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-900">{{ $shop->name }}</h4>
                                <p class="text-sm text-gray-500 font-mono">{{ $shop->slug }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2.5 px-4 bg-gray-50 rounded-xl">
                                <span class="text-gray-500 text-sm">{{ __('Status') }}</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $shop->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ __($shop->status === 'approved' ? 'Approved' : 'Pending') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2.5 px-4 rounded-xl">
                                <span class="text-gray-500 text-sm">{{ __('Created') }}</span>
                                <span class="font-medium text-gray-900 text-sm">{{ $shop->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2.5 px-4 rounded-xl">
                                <span class="text-gray-500 text-sm">{{ __('Suppliers') }}</span>
                                <span class="font-medium text-gray-900 text-sm">{{ $stats['total_suppliers'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Breakdown -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('Payment Status') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @php
                            $ps = $stats['paymentStatusStats'];
                            $psTotal = max(($ps['paid'] ?? 0) + ($ps['unpaid'] ?? 0) + ($ps['partial'] ?? 0), 1);
                            $paidPct = round((($ps['paid'] ?? 0) / $psTotal) * 100);
                            $partialPct = round((($ps['partial'] ?? 0) / $psTotal) * 100);
                            $unpaidPct = max(100 - $paidPct - $partialPct, 0);
                        @endphp
                        <div class="w-full h-3 rounded-full overflow-hidden flex bg-gray-100 mb-5">
                            <div class="bg-green-500" style="width: {{ $paidPct }}%"></div>
                            <div class="bg-yellow-400" style="width: {{ $partialPct }}%"></div>
                            <div class="bg-red-400" style="width: {{ $unpaidPct }}%"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2"></span>{{ __('Paid') }}</span>
                                <span class="font-semibold text-gray-900">{{ rwf($ps['paid'] ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 mr-2"></span>{{ __('Partial') }}</span>
                                <span class="font-semibold text-gray-900">{{ rwf($ps['partial'] ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-red-400 mr-2"></span>{{ __('Unpaid') }}</span>
                                <span class="font-semibold text-gray-900">{{ rwf($ps['unpaid'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Stats -->
            @php
                $paymentIcons = [
                    'cash' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    'momo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
                    'bank' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>',
                    'card' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
                ];
                $paymentColors = [
                    'cash' => 'from-green-400 to-emerald-500',
                    'momo' => 'from-yellow-400 to-orange-500',
                    'bank' => 'from-blue-400 to-indigo-500',
                    'card' => 'from-purple-400 to-violet-500',
                ];
                $paymentBgColors = [
                    'cash' => 'from-green-50 to-emerald-50',
                    'momo' => 'from-yellow-50 to-orange-50',
                    'bank' => 'from-blue-50 to-indigo-50',
                    'card' => 'from-purple-50 to-violet-50',
                ];
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __("Today's Sales by Payment Method") }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @forelse(\App\Models\Sale::PAYMENT_METHODS as $method => $label)
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ $paymentBgColors[$method] ?? 'from-gray-50 to-gray-100' }} rounded-xl hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br {{ $paymentColors[$method] ?? 'from-gray-400 to-gray-500' }} flex items-center justify-center mr-3 shadow">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $paymentIcons[$method] ?? '' !!}</svg>
                                        </div>
                                        <span class="text-gray-700 font-medium">{{ __($label) }}</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ rwf($paymentMethodStats['today'][$method] ?? 0) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">{{ __('No payment methods defined') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __("This Month's Sales by Payment Method") }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @forelse(\App\Models\Sale::PAYMENT_METHODS as $method => $label)
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ $paymentBgColors[$method] ?? 'from-gray-50 to-gray-100' }} rounded-xl hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br {{ $paymentColors[$method] ?? 'from-gray-400 to-gray-500' }} flex items-center justify-center mr-3 shadow">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $paymentIcons[$method] ?? '' !!}</svg>
                                        </div>
                                        <span class="text-gray-700 font-medium">{{ __($label) }}</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ rwf($paymentMethodStats['month'][$method] ?? 0) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">{{ __('No payment methods defined') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Sales by Category') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-80"><canvas id="salesCategoryChart"></canvas></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Expenses by Category') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-80"><canvas id="expenseCategoryChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Trend Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Last 7 Days') }}</h3>
                        <div class="flex space-x-4 text-xs">
                            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 mr-1"></span>{{ __('Sales') }}</span>
                            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-gradient-to-r from-red-400 to-rose-500 mr-1"></span>{{ __('Purchases') }}</span>
                        </div>
                    </div>
                    <div class="p-6"><div class="h-64"><canvas id="dailyChart"></canvas></div></div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Monthly Trend') }}</h3>
                    </div>
                    <div class="p-6"><div class="h-64"><canvas id="monthlyChart"></canvas></div></div>
                </div>
            </div>

            <!-- Quick Actions, Recent Activity (tabbed), Low Stock -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Quick Actions') }}</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('sales.create') }}" class="flex items-center p-3 rounded-xl hover:bg-green-50 transition-all group">
                            <div class="h-10 w-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center group-hover:bg-gradient-to-br group-hover:from-green-400 group-hover:to-emerald-500 group-hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </div>
                            <span class="ml-3 font-medium text-gray-700 group-hover:text-green-700">{{ __('New Sale') }}</span>
                        </a>
                        <a href="{{ route('purchases.create') }}" class="flex items-center p-3 rounded-xl hover:bg-red-50 transition-all group">
                            <div class="h-10 w-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:bg-gradient-to-br group-hover:from-red-400 group-hover:to-rose-500 group-hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <span class="ml-3 font-medium text-gray-700 group-hover:text-red-700">{{ __('New Purchase') }}</span>
                        </a>
                        <a href="{{ route('products.create') }}" class="flex items-center p-3 rounded-xl hover:bg-indigo-50 transition-all group">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:bg-gradient-to-br group-hover:from-indigo-400 group-hover:to-blue-500 group-hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <span class="ml-3 font-medium text-gray-700 group-hover:text-indigo-700">{{ __('Add Product') }}</span>
                        </a>
                        <a href="{{ route('staff.create') }}" class="flex items-center p-3 rounded-xl hover:bg-purple-50 transition-all group">
                            <div class="h-10 w-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-gradient-to-br group-hover:from-purple-400 group-hover:to-violet-500 group-hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <span class="ml-3 font-medium text-gray-700 group-hover:text-purple-700">{{ __('Add Staff') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Sales / Purchases tabs -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <div class="flex space-x-2" role="tablist">
                            <button type="button" data-tab-target="tab-sales" class="tab-btn px-3 py-1.5 rounded-lg text-sm font-semibold bg-indigo-600 text-white transition">
                                {{ __('Recent Sales') }}
                            </button>
                            <button type="button" data-tab-target="tab-purchases" class="tab-btn px-3 py-1.5 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                                {{ __('Recent Purchases') }}
                            </button>
                        </div>
                    </div>

                    <div id="tab-sales" class="tab-panel overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Sale ID') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Items') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                            #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $sale->items->count() }} {{ __('items') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right"><span class="font-bold text-green-600">{{ rwf($sale->total_amount) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">{{ __('No sales yet') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div id="tab-purchases" class="tab-panel overflow-x-auto hidden">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Purchase ID') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Supplier') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Date') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentPurchases as $purchase)
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                            #{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->supplier->name ?? __('N/A') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-right"><span class="font-bold text-red-600">{{ rwf($purchase->total_amount) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">{{ __('No purchases yet') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Stock & Recent Stock Movements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Low Stock Products -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Low Stock Products') }}</h3>
                        <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">{{ __('View all') }} →</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($lowStockProducts as $product)
                            <div class="flex items-center justify-between px-6 py-3">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ __('Product') }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $product->stock <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $product->stock }} {{ __('left') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">{{ __('All products are well stocked') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Stock Movements -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Stock Movements') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                        @forelse($recentStockMovements as $movement)
                            @php
    $quantityChange = (float) $movement->quantity_change;

    $isIn = $quantityChange > 0;

    $referenceType = strtolower((string) $movement->reference_type);

    $referenceLabel = match ($referenceType) {
        'purchase'   => __('Purchase'),
        'sale'       => __('Sale'),
        'order'      => __('Order'),
        'return'     => __('Return'),
        'transfer'   => __('Transfer'),
        'adjustment' => __('Stock Adjustment'),
        'opening'    => __('Opening Stock'),
        'damage'     => __('Damaged Stock'),
        default      => ucfirst(str_replace('_', ' ', $referenceType)),
    };

    $movementQuantity = abs($quantityChange);

    if ($quantityChange > 0) {
        $movementBg = 'bg-teal-100';
        $movementText = 'text-teal-600';
        $quantityText = 'text-teal-600';
        $sign = '+';
    } elseif ($quantityChange < 0) {
        $movementBg = 'bg-orange-100';
        $movementText = 'text-orange-600';
        $quantityText = 'text-orange-600';
        $sign = '-';
    } else {
        $movementBg = 'bg-gray-100';
        $movementText = 'text-gray-600';
        $quantityText = 'text-gray-600';
        $sign = '';
    }
@endphp
                            <div class="flex items-center justify-between px-6 py-3">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-lg flex items-center justify-center mr-3 {{ $isIn ? 'bg-teal-100 text-teal-600' : 'bg-orange-100 text-orange-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($isIn)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m0-16l-6 6m6-6l6 6"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4m0 16l-6-6m6 6l6-6"/>
                                            @endif
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $movement->product->name ?? __('Unknown product') }}</p>
                                        <p class="text-xs text-gray-400">{{ str_replace('_', ' ', ucfirst($movement->type)) }} · {{ $movement->creator->name ?? __('System') }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold {{ $isIn ? 'text-teal-600' : 'text-orange-600' }}">
                                    {{ $isIn ? '+' : '-' }}{{ $movement->quantity_change }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">{{ __('No stock movements yet') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Staff Directory -->
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Staff Directory') }}</h3>
                    <a href="{{ route('staff.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">{{ __('View all') }} →</a>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($staff as $member)
                        <div class="flex items-center p-3 rounded-xl border border-gray-100">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-violet-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="font-medium text-gray-800 text-sm truncate">{{ $member->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $member->email }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4 sm:col-span-2 lg:col-span-3">{{ __('No staff added yet') }}</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
        .stat-card { animation: fade-in 0.5s ease-out forwards; opacity: 0; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ---- Current time ----
            function updateTime() {
                const now = new Date();
                const el = document.getElementById('current-time');
                if (el) {
                    el.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                }
            }
            updateTime();
            setInterval(updateTime, 1000);

            // ---- Recent Sales / Purchases tabs ----
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanels = document.querySelectorAll('.tab-panel');
            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.getAttribute('data-tab-target');
                    tabPanels.forEach(panel => panel.classList.toggle('hidden', panel.id !== target));
                    tabButtons.forEach(b => {
                        const active = b === btn;
                        b.classList.toggle('bg-indigo-600', active);
                        b.classList.toggle('text-white', active);
                        b.classList.toggle('text-gray-600', !active);
                    });
                });
            });

            // ---- Chart.js global config ----
            Chart.defaults.font.family = 'Figtree, system-ui, sans-serif';
            Chart.defaults.plugins.legend.display = false;

            const rwfFormat = (value) => 'RWF ' + Number(value).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            const categoryColors = ['#10B981', '#F59E0B', '#EF4444', '#6366F1', '#06B6D4', '#8B5CF6', '#F97316', '#14B8A6'];

            // ---- Daily Chart ----
            const dailyCtx = document.getElementById('dailyChart');
            if (dailyCtx) {
                new Chart(dailyCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartData['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Sales',
                            data: {!! json_encode($chartData['sales'] ?? []) !!},
                            borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true, tension: 0.4, borderWidth: 3,
                            pointBackgroundColor: '#10B981', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6
                        }, {
                            label: 'Purchases',
                            data: {!! json_encode($chartData['purchases'] ?? []) !!},
                            borderColor: '#EF4444', backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true, tension: 0.4, borderWidth: 3,
                            pointBackgroundColor: '#EF4444', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (c) => c.dataset.label + ': ' + rwfFormat(c.raw) } }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: (v) => rwfFormat(v) } },
                            x: { grid: { display: false } }
                        },
                        interaction: { intersect: false, mode: 'index' }
                    }
                });
            }

            // ---- Monthly Chart ----
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($monthlyChartData['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Sales', data: {!! json_encode($monthlyChartData['sales'] ?? []) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 8, borderSkipped: false
                        }, {
                            label: 'Purchases', data: {!! json_encode($monthlyChartData['purchases'] ?? []) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.8)', borderRadius: 8, borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (c) => c.dataset.label + ': ' + rwfFormat(c.raw) } }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: (v) => rwfFormat(v) } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // ---- Sales by Category (Doughnut) ----
            const salesCategoryCtx = document.getElementById('salesCategoryChart');
            if (salesCategoryCtx) {
                const labels = {!! json_encode($salesCategoryData->pluck('category')->toArray() ?? []) !!};
                const values = {!! json_encode($salesCategoryData->pluck('total')->map(fn($v) => (float) $v)->toArray() ?? []) !!};
                new Chart(salesCategoryCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: { labels, datasets: [{ data: values, backgroundColor: labels.map((_, i) => categoryColors[i % categoryColors.length]), hoverOffset: 8 }] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'right' },
                            tooltip: { callbacks: { label: (c) => c.label + ': ' + rwfFormat(c.raw) } }
                        }
                    }
                });
            }

            // ---- Expenses by Category (Doughnut) ----
            const expenseCategoryCtx = document.getElementById('expenseCategoryChart');
            if (expenseCategoryCtx) {
                const labels = {!! json_encode($expenseCategoryData->pluck('category')->toArray() ?? []) !!};
                const values = {!! json_encode($expenseCategoryData->pluck('total')->map(fn($v) => (float) $v)->toArray() ?? []) !!};
                new Chart(expenseCategoryCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: { labels, datasets: [{ data: values, backgroundColor: labels.map((_, i) => categoryColors[i % categoryColors.length]), hoverOffset: 8 }] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'right' },
                            tooltip: { callbacks: { label: (c) => c.label + ': ' + rwfFormat(c.raw) } }
                        }
                    }
                });
            }

        });
    </script>
</x-app-layout>