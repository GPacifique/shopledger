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
                @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        {{ __('Edit Product') }}
                    </a>
                @endif
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
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Product Information') }}</h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('SKU') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->sku }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Category') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Supplier') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->supplier->name ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Barcode') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->barcode ?: __('None') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Description') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->description ?: __('No description') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Expiry Date') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->expiry_date?->format('M d, Y') ?? __('N/A') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('QR Code') }}</dt>
                                        <dd class="mt-1">
                                            <img src="{{ route('products.qr-code', $product) }}" alt="QR Code" class="w-32 h-32 border rounded">
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Pricing & Stock -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Pricing & Stock') }}</h3>
                                <dl class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Buying Price') }}</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ rwf($product->buying_price) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Selling Price') }}</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-green-600">{{ rwf($product->selling_price) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Profit Margin') }}</dt>
                                        @php
                                            $margin = $product->buying_price > 0 ? (($product->selling_price - $product->buying_price) / $product->buying_price) * 100 : 0;
                                            $profit = $product->selling_price - $product->buying_price;
                                        @endphp
                                        <dd class="mt-1">
                                            <span class="text-2xl font-semibold {{ $margin > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($margin, 1) }}%</span>
                                            <span class="text-sm text-gray-500 ml-2">({{ rwf($profit) }} {{ __('per unit') }})</span>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Current Stock') }}</dt>
                                        <dd class="mt-1">
                                            @if($product->stock <= 0)
                                                <span class="text-2xl font-semibold text-red-600">{{ $product->stock }}</span>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ __('Out of Stock') }}
                                                </span>
                                            @elseif($product->stock <= $product->minimum_stock)
                                                <span class="text-2xl font-semibold text-yellow-600">{{ $product->stock }}</span>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ __('Low Stock') }}
                                                </span>
                                            @else
                                                <span class="text-2xl font-semibold text-green-600">{{ $product->stock }}</span>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ __('In Stock') }}
                                                </span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Minimum Stock') }}</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $product->minimum_stock }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Stock Value') }}</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ rwf($product->stock * $product->buying_price) }}</dd>
                                        <dd class="text-sm text-gray-500">{{ __('Potential sales') }}: {{ rwf($product->stock * $product->selling_price) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-5">

    <div class="bg-blue-50 rounded-lg p-5">
        <p class="text-gray-500 text-sm">Purchased</p>
        <h2 class="text-3xl font-bold text-blue-700">
            {{ number_format($totalPurchased,2) }}
        </h2>
    </div>

    <div class="bg-green-50 rounded-lg p-5">
        <p class="text-gray-500 text-sm">Sold</p>
        <h2 class="text-3xl font-bold text-green-700">
            {{ number_format($totalSold,2) }}
        </h2>
    </div>

    <div class="bg-yellow-50 rounded-lg p-5">
        <p class="text-gray-500 text-sm">Stock</p>
        <h2 class="text-3xl font-bold text-yellow-700">
            {{ number_format($product->stock,2) }}
        </h2>
    </div>

    <div class="bg-purple-50 rounded-lg p-5">
        <p class="text-gray-500 text-sm">Purchase Value</p>
        <h2 class="text-xl font-bold text-purple-700">
            {{ rwf($totalPurchaseCost) }}
        </h2>
    </div>

    <div class="bg-indigo-50 rounded-lg p-5">
        <p class="text-gray-500 text-sm">Sales Value</p>
        <h2 class="text-xl font-bold text-indigo-700">
            {{ rwf($totalSales) }}
        </h2>
    </div>

    <div class="bg-emerald-50 rounded-lg p-5">
        <p class="text-gray-500 text-sm">Profit</p>
        <h2 class="text-xl font-bold text-emerald-700">
            {{ rwf($grossProfit) }}
        </h2>
    </div>

</div>
<!--purchase history-->
<div class="mt-10 bg-white shadow rounded-lg">

    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">
            Purchase History
        </h2>
    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">

            <tr>

                <th>Date</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total</th>
                <th>User</th>

            </tr>

            </thead>

            <tbody>

            @forelse($purchaseItems as $item)

                <tr class="border-b">

                    <td>{{ $item->purchase->purchase_date }}</td>

                    <td>{{ $item->purchase->supplier->name ?? '-' }}</td>

                    <td>{{ number_format($item->quantity,2) }}</td>

                    <td>{{ rwf($item->unit_cost) }}</td>

                    <td>{{ rwf($item->line_total) }}</td>

                    <td>{{ $item->purchase->creator->name ?? '-' }}</td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center py-5">
                        No purchases found.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="p-4">

        {{ $purchaseItems->links() }}

    </div>

</div>
<!--Sales history -->
<div class="mt-10 bg-white shadow rounded-lg">

    <div class="px-6 py-4 border-b">

        <h2 class="text-lg font-semibold">
            Sales History
        </h2>

    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">

            <tr>

                <th>Date</th>

                <th>Customer</th>

                <th>Qty</th>

                <th>Price</th>

                <th>Total</th>

                <th>Profit</th>

                <th>Cashier</th>

            </tr>

            </thead>

            <tbody>

            @forelse($saleItems as $item)

            <tr class="border-b">

                <td>{{ $item->sale->sale_date }}</td>

                <td>{{ $item->sale->customer->name ?? 'Walk-in Customer' }}</td>

                <td>{{ number_format($item->quantity,2) }}</td>

                <td>{{ rwf($item->unit_price) }}</td>

                <td>{{ rwf($item->line_total) }}</td>

                <td class="text-green-700 font-semibold">
                    {{ rwf(($item->unit_price-$item->cost_price_at_sale)*$item->quantity) }}
                </td>

                <td>{{ $item->sale->creator->name ?? '-' }}</td>

            </tr>

            @empty

            <tr>

                <td colspan="7" class="text-center py-5">

                    No sales found.

                </td>

            </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="p-4">

        {{ $saleItems->links() }}

    </div>

</div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                        @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())
                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this product? This action cannot be undone.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    {{ __('Delete Product') }}
                                </button>
                            </form>
                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ __('Edit Product') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>