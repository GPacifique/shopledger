<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ __('Seller Dashboard') }}</h2>
                    <p class="text-sm text-gray-500">{{ $shop->name }}</p>
                </div>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-xs text-gray-500">{{ __('Welcome back') }}</p>
                <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Sale CTA -->
            <div class="mb-8 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                <div class="relative px-6 py-8 sm:px-10 sm:py-12">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <circle cx="1" cy="1" r="1" fill="white"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#dots)"/>
                        </svg>
                    </div>
                    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="text-white mb-6 md:mb-0">
                            <h3 class="text-3xl font-bold mb-2">{{ __('Ready to make a sale?') }} 🛒</h3>
                            <p class="text-blue-100 text-lg">{{ __('Create new sales quickly and easily.') }}</p>
                        </div>
                        <a href="{{ route('sales.create') }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold text-lg hover:bg-indigo-50 transition-all transform hover:scale-105 shadow-xl hover:shadow-2xl group">
                            <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ __('New Sale') }}
                            <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.1s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Today') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Shop Sales') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ rwf($todaySales) }}</p>
                    </div>
                </div>

                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.15s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ __('My sales') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Your Sales') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ rwf($todayMySales) }}</p>
                    </div>
                </div>

                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.2s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">{{ __('Avg.') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Average Sale') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ rwf($averageSaleValue) }}</p>
                    </div>
                </div>

                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.25s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12h10M12 7v10m-7 0h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ __('Orders') }}</span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Transactions') }}</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $todaySalesCount }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">{{ __('Total Products') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">{{ __('Stock Units') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ format_qty($totalStockUnits) }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">{{ __('Low Stock') }}</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-600">{{ number_format($lowStockProducts) }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">{{ __('Out of Stock') }}</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($outOfStockProducts) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Inventory Snapshot') }}</h3>
                        <span class="text-sm text-gray-500">{{ __('Value') }}: {{ rwf($inventoryValue) }}</span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4">
                            <span class="text-gray-600">{{ __('Products in stock') }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format(max($totalProducts - $outOfStockProducts, 0)) }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4">
                            <span class="text-gray-600">{{ __('Purchases today') }}</span>
                            <span class="font-semibold text-gray-900">{{ rwf($todayPurchases) }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4">
                            <span class="text-gray-600">{{ __('My completed sales') }}</span>
                            <span class="font-semibold text-gray-900">{{ $todayMySalesCount }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Low Stock Alerts') }}</h3>
                        <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
                    </div>
                    @if($lowStockItems->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($lowStockItems as $item)
                                <div class="flex items-center justify-between rounded-xl border border-yellow-200 bg-yellow-50 p-3">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500">{{ __('Minimum') }}: {{ format_qty($item->minimum_stock) }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ format_qty($item->stock) }} {{ __('left') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                            {{ __('Inventory is healthy. No low-stock alerts at the moment.') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Weekly Sales Trend') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Your sales for the last 7 days') }}</p>
                        </div>
                        <span class="text-sm font-medium text-indigo-600">{{ __('7 days') }}</span>
                    </div>

                    <div class="flex items-end h-52 gap-3">
                        @foreach($weeklySalesTrend as $trend)
                            @php
                                $barHeight = $weeklySalesMax > 0 ? max(12, ($trend['total'] / $weeklySalesMax) * 100) : 12;
                                $visibleBarHeight = $trend['total'] > 0 ? $barHeight : 12;
                            @endphp
                            <div class="flex-1 flex flex-col items-center justify-end h-full">
                                <div class="w-full flex justify-center items-end">
                                    <div class="w-full rounded-t-xl bg-gradient-to-t from-indigo-500 to-blue-400 shadow-sm" style="height: {{ $visibleBarHeight }}%"></div>
                                </div>
                                <div class="mt-3 flex flex-col items-center text-center">
                                    <span class="text-xs font-medium text-gray-500">{{ $trend['day'] }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $trend['date'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Stock Movement') }}</h3>
                        <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentStockMovements as $movement)
                            <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $movement->product?->name ?? __('Product') }}</p>
                                    <p class="text-xs text-gray-500 uppercase">{{ $movement->type }}</p>
                                </div>
                                <span class="text-sm font-semibold {{ $movement->isIncoming() ? 'text-green-600' : 'text-blue-600' }}">
                                    {{ $movement->isIncoming() ? '+' : '-' }}{{ format_qty($movement->quantity) }}
                                </span>
                            </div>
                        @empty
                            <div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">{{ __('No stock movements recorded yet.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Best Selling Products') }}</h3>
                    <span class="text-sm text-gray-500">{{ __('Your top items') }}</span>
                </div>

                <div class="space-y-4">
                    @forelse($bestSellingProducts as $bestItem)
                        @php
                            $topProduct = $bestItem->product;
                            $maxQuantity = $bestSellingProducts->max('total_quantity') ?: 1;
                            $width = max(18, ($bestItem->total_quantity / $maxQuantity) * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $topProduct?->name ?? __('Unknown product') }}</span>
                                <span class="text-sm text-gray-500">{{ format_qty($bestItem->total_quantity) }} sold</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-green-400 to-emerald-500" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">{{ __('No sales data yet for your best-selling products.') }}</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 animate-slide-up" style="animation-delay: 0.3s">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('My Recent Sales') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Your latest transactions') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 group">
                        {{ __('View all') }}
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Sale ID') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Items') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center mr-3">
                                            <span class="text-xs font-bold text-green-600">#</span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $sale->sale_date->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $sale->sale_date->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ __(':count items', ['count' => $sale->items->count()]) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-lg font-bold text-green-600">{{ rwf($sale->total_amount) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ __('No sales yet') }}</h4>
                                        <p class="text-gray-500 mb-4">{{ __('Start by creating your first sale') }}</p>
                                        <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium hover:from-indigo-600 hover:to-purple-700 transition-all transform hover:scale-105">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            {{ __('Create Sale') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Helpful Tips -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200">
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-semibold text-amber-800">{{ __('Pro Tip') }}</h4>
                        <p class="text-sm text-amber-700 mt-1">{{ __('Use keyboard shortcut') }} <kbd class="px-2 py-0.5 bg-white rounded text-xs font-mono shadow-sm">Ctrl + N</kbd> {{ __('to quickly create a new sale from anywhere!') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
        .animate-slide-up { animation: slide-up 0.6s ease-out forwards; }
        .stat-card { animation: fade-in 0.5s ease-out forwards; opacity: 0; }
    </style>

    <script>
        // Keyboard shortcut for quick sale
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = '{{ route("sales.create") }}';
            }
        });
    </script>
</x-app-layout>
