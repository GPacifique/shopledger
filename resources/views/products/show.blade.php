<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('products.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $product->name }}
                </h2>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Edit Product
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Product Info -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Information</h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->sku }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->description ?: 'No description' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Pricing & Stock -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Stock</h3>
                                <dl class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Cost Price</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ rwf($product->cost_price) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Sale Price</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-green-600">{{ rwf($product->sale_price) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Profit Margin</dt>
                                        @php
                                            $margin = $product->cost_price > 0 ? (($product->sale_price - $product->cost_price) / $product->cost_price) * 100 : 0;
                                            $profit = $product->sale_price - $product->cost_price;
                                        @endphp
                                        <dd class="mt-1">
                                            <span class="text-2xl font-semibold {{ $margin > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($margin, 1) }}%</span>
                                            <span class="text-sm text-gray-500 ml-2">({{ rwf($profit) }} per unit)</span>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Current Stock</dt>
                                        <dd class="mt-1">
                                            @if($product->stock <= 0)
                                                <span class="text-2xl font-semibold text-red-600">{{ $product->stock }}</span>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Out of Stock
                                                </span>
                                            @elseif($product->stock <= 10)
                                                <span class="text-2xl font-semibold text-yellow-600">{{ $product->stock }}</span>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Low Stock
                                                </span>
                                            @else
                                                <span class="text-2xl font-semibold text-green-600">{{ $product->stock }}</span>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    In Stock
                                                </span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Stock Value</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ rwf($product->stock * $product->cost_price) }}</dd>
                                        <dd class="text-sm text-gray-500">Potential sales: {{ rwf($product->stock * $product->sale_price) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Product
                            </button>
                        </form>
                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
