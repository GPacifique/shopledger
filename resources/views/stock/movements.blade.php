<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Stock History: {{ $product->name }}
            </h2>
            <a href="{{ route('stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stock
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Product Info Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="grid md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Product</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Current Stock</p>
                        <p class="text-3xl font-bold {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= $product->low_stock_threshold ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->stock }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Low Stock Threshold</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $product->low_stock_threshold }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Stock Value</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($product->stock * $product->cost_price) }} RWF</p>
                    </div>
                </div>
            </div>

            <!-- Movements Timeline -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Stock Movement History</h3>
                </div>
                <div class="p-6">
                    @if($movements->count() > 0)
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($movements as $index => $movement)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex items-start space-x-3">
                                                <div class="relative">
                                                    @php
                                                        $bgColor = match($movement->type) {
                                                            'purchase', 'transfer_in', 'return' => 'bg-green-500',
                                                            'sale', 'transfer_out' => 'bg-blue-500',
                                                            'damage' => 'bg-red-500',
                                                            'adjustment' => 'bg-yellow-500',
                                                            default => 'bg-gray-500'
                                                        };
                                                    @endphp
                                                    <div class="h-10 w-10 rounded-full {{ $bgColor }} flex items-center justify-center ring-8 ring-white">
                                                        @if($movement->isIncoming())
                                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="bg-gray-50 rounded-lg p-4">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bgColor }} text-white">
                                                                    {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                                                                </span>
                                                                <span class="ml-2 text-2xl font-bold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                                                </span>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm text-gray-500">
                                                                    {{ $movement->stock_before }} → {{ $movement->stock_after }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @if($movement->notes)
                                                            <p class="mt-2 text-sm text-gray-600">{{ $movement->notes }}</p>
                                                        @endif
                                                        <div class="mt-2 flex items-center text-xs text-gray-500">
                                                            <span>{{ $movement->created_at->format('M d, Y H:i') }}</span>
                                                            <span class="mx-2">•</span>
                                                            <span>by {{ $movement->creator->name ?? 'System' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="mt-6">
                            {{ $movements->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500">No stock movements recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
