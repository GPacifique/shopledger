<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('sales.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Sale #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $sale->sale_date->format('l, F j, Y') }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
                <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Sale
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg print:hidden">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Print Header (Shop Info) - Only visible when printing -->
            <div class="hidden print:block mb-6 text-center border-b-2 border-gray-300 pb-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ $sale->shop->name ?? 'Shop Name' }}</h1>
                <p class="text-gray-600">{{ $sale->shop->address ?? '' }}</p>
                @if($sale->shop->phone ?? null)
                    <p class="text-gray-600">Tel: {{ $sale->shop->phone }}</p>
                @endif
                <p class="text-gray-600 mt-2">Served by: <span class="font-medium">{{ $sale->creator->name ?? 'Unknown' }}</span></p>
            </div>

            <!-- Sale Receipt Card -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6 print:shadow-none">
                <!-- Receipt Header -->
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-8 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-green-100 text-sm uppercase tracking-wider">Sale Receipt</p>
                            <p class="text-3xl font-bold mt-1">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-green-100 text-sm">Total Amount</p>
                            <p class="text-3xl font-bold">{{ rwf($sale->total_amount) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sale Details -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Date</p>
                            <p class="font-medium text-gray-900">{{ $sale->sale_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Time</p>
                            <p class="font-medium text-gray-900">{{ $sale->created_at->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Cashier</p>
                            <p class="font-medium text-gray-900">{{ $sale->creator->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Items</p>
                            <p class="font-medium text-gray-900">{{ $sale->items->sum('quantity') }} units</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">Product</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sale->items as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('products.show', $item->product) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition">
                                                {{ $item->product->name }}
                                            </a>
                                            <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-600">{{ rwf($item->unit_price) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ rwf($item->line_total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">{{ rwf($sale->total_amount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax (0%)</span>
                                <span class="text-gray-900">{{ rwf(0) }}</span>
                            </div>
                            <div class="border-t border-gray-300 pt-2 flex justify-between">
                                <span class="text-base font-semibold text-gray-900">Total</span>
                                <span class="text-xl font-bold text-green-600">{{ rwf($sale->total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Analysis (Hidden from print) -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6 print:hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Profit Analysis
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-6">
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-green-600 font-medium">Revenue</p>
                            <p class="text-2xl font-bold text-green-700 mt-1">{{ rwf($sale->total_amount) }}</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-red-600 font-medium">Cost of Goods</p>
                            <p class="text-2xl font-bold text-red-700 mt-1">{{ rwf($sale->total_amount - $profit) }}</p>
                        </div>
                        <div class="bg-{{ $profit >= 0 ? 'emerald' : 'rose' }}-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-{{ $profit >= 0 ? 'emerald' : 'rose' }}-600 font-medium">Net Profit</p>
                            <p class="text-2xl font-bold text-{{ $profit >= 0 ? 'emerald' : 'rose' }}-700 mt-1">{{ rwf($profit) }}</p>
                            @if($sale->total_amount > 0)
                                <p class="text-xs text-{{ $profit >= 0 ? 'emerald' : 'rose' }}-500 mt-1">
                                    {{ number_format(($profit / $sale->total_amount) * 100, 1) }}% margin
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Per Item Breakdown -->
                    <div class="mt-6">
                        <p class="text-sm font-medium text-gray-700 mb-3">Profit by Item</p>
                        <div class="space-y-2">
                            @foreach($sale->items as $item)
                            @php
                                $itemProfit = $item->line_total - ($item->cost_price_at_sale * $item->quantity);
                                $itemMargin = $item->line_total > 0 ? ($itemProfit / $item->line_total) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-2">
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-700">{{ $item->product->name }}</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ $item->quantity }}x)</span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm {{ $itemProfit >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                        {{ rwf($itemProfit) }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $itemMargin >= 20 ? 'bg-green-100 text-green-700' : ($itemMargin >= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ number_format($itemMargin, 1) }}%
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center print:hidden">
                <p class="text-sm text-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $sale->created_at->format('M d, Y \a\t h:i A') }}
                    </span>
                </p>
                <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sale? This will restore all stock.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition">
                        <svg class="-ml-1 mr-2 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Sale
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            nav { display: none !important; }
            header { display: none !important; }
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .py-12 { padding-top: 0 !important; }
        }
    </style>
</x-app-layout>
