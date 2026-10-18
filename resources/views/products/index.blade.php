
{{-- resources/views/products/index.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-gray-50/70 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">

            {{-- Page header --}}
            <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                        {{ __('Products') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Manage products, inventory, imports and exports.') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Import --}}
                    <button
                        type="button"
                        class="js-open-import-modal inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        aria-controls="import-products-modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5 5 5M4 16v4h16v-4"/>
                        </svg>
                        {{ __('Import') }}
                    </button>

                    {{-- Export dropdown --}}
                    <div id="export-dropdown-wrapper" class="relative">
                        <button
                            type="button"
                            class="js-toggle-export inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            aria-controls="export-dropdown"
                            aria-expanded="false"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0 5-5m-5 5-5-5M4 20h16"/>
                            </svg>
                            {{ __('Export') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 011.06 0L10 10.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 8.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div
                            id="export-dropdown"
                            class="absolute right-0 z-30 mt-2 hidden w-48 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg"
                        >
                            <a href="{{ route('products.export') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M8 13h8M8 17h8"/>
                                </svg>
                                {{ __('Export products') }}
                            </a>
                        </div>
                    </div>

                    {{-- Template dropdown --}}
                    <div id="template-dropdown-wrapper" class="relative">
                        <button
                            type="button"
                            class="js-toggle-template inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-700 shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                            aria-controls="template-dropdown"
                            aria-expanded="false"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M8 13h8M8 17h5"/>
                            </svg>
                            {{ __('Template') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 011.06 0L10 10.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 8.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div
                            id="template-dropdown"
                            class="absolute right-0 z-30 mt-2 hidden w-56 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg"
                        >
                            <a href="{{ route('products.import.template') }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/>
                                </svg>
                                {{ __('Download import template') }}
                            </a>
                        </div>
                    </div>

                    {{-- Add product --}}
                    <a href="{{ route('products.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                        </svg>
                        {{ __('Add Product') }}
                    </a>
                </div>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div role="status" class="mb-5 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-semibold">{{ __('Please correct the following errors:') }}</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Inventory summary --}}
            <div class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-gray-500">{{ __('Total Products') }}</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7.5 12 12m0 0L4 7.5M12 12v9m8-13.5v9L12 21l-8-4.5v-9L12 3l8 4.5z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-2xl font-bold tabular-nums text-gray-900">{{ number_format($totalProducts ?? 0) }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Products in your inventory') }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-gray-500">{{ __('Total Stock') }}</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-2xl font-bold tabular-nums text-gray-900">{{ number_format($totalStockUnits ?? 0, 2) }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Units available') }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-gray-500">{{ __('Stock Cost Value') }}</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m5-16H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 break-words text-2xl font-bold tabular-nums text-gray-900">{{ number_format($stockValueCost ?? 0, 2) }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Value at buying price') }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-gray-500">{{ __('Stock Retail Value') }}</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9-9 9-9-9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m3-6H11a2 2 0 000 4h2a2 2 0 010 4h-4"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 break-words text-2xl font-bold tabular-nums text-gray-900">{{ number_format($stockValueRetail ?? 0, 2) }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Value at selling price') }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-gray-500">{{ __('Low / Out of Stock') }}</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 001.7 3h15a2 2 0 001.7-3l-7.5-13.1a2 2 0 00-3.4 0z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-2xl font-bold tabular-nums text-gray-900">
                        {{ number_format(($lowStockCount ?? 0) + ($outOfStockCount ?? 0)) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ number_format($lowStockCount ?? 0) }} {{ __('low') }}
                        <span class="px-1">·</span>
                        {{ number_format($outOfStockCount ?? 0) }} {{ __('out') }}
                    </p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <label for="product-search" class="mb-1.5 block text-sm font-medium text-gray-700">
                            {{ __('Search products') }}
                        </label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4"/>
                            </svg>
                            <input
                                id="product-search"
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search by product name, SKU or barcode...') }}"
                                class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label for="stock-status" class="mb-1.5 block text-sm font-medium text-gray-700">
                            {{ __('Stock status') }}
                        </label>
                        <select
                            id="stock-status"
                            name="stock_status"
                            class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">{{ __('All stock statuses') }}</option>
                            <option value="in_stock" @selected(request('stock_status') === 'in_stock')>{{ __('In stock') }}</option>
                            <option value="low_stock" @selected(request('stock_status') === 'low_stock')>{{ __('Low stock') }}</option>
                            <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>{{ __('Out of stock') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2 md:col-span-2">
                        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50" aria-label="{{ __('Clear filters') }}" title="{{ __('Clear filters') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v6h6"/>
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Products table --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ __('Product inventory') }}</h2>
                        <p class="mt-0.5 text-sm text-gray-500">{{ __('Review prices, stock levels and product actions.') }}</p>
                    </div>
                    @if (method_exists($products, 'total'))
                        <p class="text-sm text-gray-500">
                            {{ __('Showing') }}
                            <span class="font-semibold text-gray-800">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
                            {{ __('of') }}
                            <span class="font-semibold text-gray-800">{{ $products->total() }}</span>
                        </p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Product') }}</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('SKU') }}</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Buying Price') }}</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Selling Price') }}</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Stock') }}</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Margin') }}</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($products as $product)
                                <tr class="transition hover:bg-gray-50/80">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                                        @if (!empty($product->barcode))
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ __('Barcode') }}: {{ $product->barcode }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                        {{ $product->sku ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm tabular-nums text-gray-700">
                                        {{ number_format((float) ($product->buying_price ?? 0), 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium tabular-nums text-gray-900">
                                        {{ number_format((float) ($product->selling_price ?? 0), 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        @php
                                            $stockQuantity = (float) ($product->quantity ?? $product->stock ?? 0);
                                            $minimumStock = (float) ($product->minimum_stock ?? $product->min_stock ?? 0);
                                        @endphp

                                        @if ($stockQuantity <= 0)
                                            <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                {{ number_format($stockQuantity, 2) }}
                                            </span>
                                        @elseif ($minimumStock > 0 && $stockQuantity <= $minimumStock)
                                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                {{ number_format($stockQuantity, 2) }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                {{ number_format($stockQuantity, 2) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm tabular-nums">
                                        @php
                                            $marginValue = (float) ($product->selling_price ?? 0) - (float) ($product->buying_price ?? 0);
                                        @endphp
                                        <span class="{{ $marginValue < 0 ? 'text-red-600' : ($marginValue > 0 ? 'text-green-700' : 'text-gray-500') }}">
                                            {{ number_format($marginValue, 2) }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="inline-flex items-center rounded-lg border border-gray-200 px-2.5 py-2 text-xs font-medium text-gray-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700"
                                               title="{{ __('View product') }}"
                                               aria-label="{{ __('View product') }}">
                                                {{ __('View') }}
                                            </a>

                                            <a href="{{ route('products.qr-code', $product) }}"
                                               class="inline-flex items-center rounded-lg border border-gray-200 px-2.5 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-100"
                                               title="{{ __('Product QR code') }}"
                                               aria-label="{{ __('Product QR code') }}">
                                                {{ __('QR') }}
                                            </a>

                                            <a href="{{ route('products.edit', $product) }}"
                                               class="inline-flex items-center rounded-lg border border-indigo-200 px-2.5 py-2 text-xs font-medium text-indigo-700 transition hover:bg-indigo-50"
                                               title="{{ __('Edit product') }}"
                                               aria-label="{{ __('Edit product') }}">
                                                {{ __('Edit') }}
                                            </a>

                                            <form action="{{ route('products.destroy', $product) }}"
                                                  method="POST"
                                                  class="inline js-confirm-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-200 px-2.5 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                        aria-label="{{ __('Delete product') }}"
                                                        title="{{ __('Delete product') }}">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7.5 12 12m0 0L4 7.5M12 12v9m8-13.5v9L12 21l-8-4.5v-9L12 3l8 4.5z"/>
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-sm font-semibold text-gray-900">{{ __('No products found') }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ __('Try changing your filters or add your first product.') }}</p>
                                        <a href="{{ route('products.create') }}" class="mt-4 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                            {{ __('Add Product') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if (method_exists($products, 'links'))
                    <div class="border-t border-gray-200 px-5 py-4">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Import products modal --}}
    <div id="import-products-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="import-modal-title">
        <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm js-close-import-modal" aria-hidden="true"></div>

            {{-- Dialog --}}
            <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5">
                    <div>
                        <h2 id="import-modal-title" class="text-lg font-bold text-gray-900">{{ __('Import Products') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Upload a supported file to import your inventory.') }}</p>
                    </div>

                    <button type="button"
                            class="js-close-import-modal inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="{{ __('Close import dialog') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5 px-6 py-6">
                        <div>
                            <label for="products-import-file" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('Choose file') }}
                            </label>

                            <input
                                id="products-import-file"
                                type="file"
                                name="file"
                                accept=".csv,.xlsx,.xls"
                                required
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 text-sm text-gray-700 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                            <p class="mt-2 text-xs text-gray-500">{{ __('Accepted formats: CSV, XLS and XLSX.') }}</p>
                        </div>

                        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0-8h.01"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-blue-900">{{ __('Before importing') }}</p>
                                    <p class="mt-1 text-sm leading-6 text-blue-800">
                                        {{ __('Use the provided template and keep the column headings unchanged. Check product names, SKUs, prices and quantities before uploading.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Template') }}</span>
                            <a href="{{ route('products.import.template') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/>
                                </svg>
                                {{ __('Download template') }}
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <button type="button" class="js-close-import-modal inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                            {{ __('Cancel') }}
                        </button>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5 5 5M4 16v4h16v-4"/>
                            </svg>
                            {{ __('Import Products') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CSP-compatible JavaScript: no inline event-handler attributes --}}
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('import-products-modal');
            const exportWrapper = document.getElementById('export-dropdown-wrapper');
            const templateWrapper = document.getElementById('template-dropdown-wrapper');
            const exportMenu = document.getElementById('export-dropdown');
            const templateMenu = document.getElementById('template-dropdown');

            const importButton = document.querySelector('.js-open-import-modal');
            const exportButton = document.querySelector('.js-toggle-export');
            const templateButton = document.querySelector('.js-toggle-template');

            let previousFocus = null;

            function setMenu(button, menu, open) {
                if (!button || !menu) return;

                menu.classList.toggle('hidden', !open);
                button.setAttribute('aria-expanded', String(open));
            }

            function closeMenus() {
                setMenu(exportButton, exportMenu, false);
                setMenu(templateButton, templateMenu, false);
            }

            function closeModal() {
                if (!modal || modal.classList.contains('hidden')) return;

                modal.classList.add('hidden');

                if (previousFocus && typeof previousFocus.focus === 'function') {
                    previousFocus.focus();
                } else if (importButton) {
                    importButton.focus();
                }
            }

            function openModal() {
                if (!modal) return;

                previousFocus = document.activeElement;
                closeMenus();
                modal.classList.remove('hidden');

                const fileInput = document.getElementById('products-import-file');
                if (fileInput) {
                    fileInput.focus();
                }
            }

            document.addEventListener('click', function (event) {
                const target = event.target;

                if (!(target instanceof Element)) return;

                if (target.closest('.js-open-import-modal')) {
                    openModal();
                    return;
                }

                if (target.closest('.js-close-import-modal')) {
                    closeModal();
                    return;
                }

                if (target.closest('.js-toggle-export')) {
                    const shouldOpen = exportMenu && exportMenu.classList.contains('hidden');

                    setMenu(exportButton, exportMenu, Boolean(shouldOpen));
                    setMenu(templateButton, templateMenu, false);
                    return;
                }

                if (target.closest('.js-toggle-template')) {
                    const shouldOpen = templateMenu && templateMenu.classList.contains('hidden');

                    setMenu(templateButton, templateMenu, Boolean(shouldOpen));
                    setMenu(exportButton, exportMenu, false);
                    return;
                }

                if (exportWrapper && !exportWrapper.contains(target)) {
                    setMenu(exportButton, exportMenu, false);
                }

                if (templateWrapper && !templateWrapper.contains(target)) {
                    setMenu(templateButton, templateMenu, false);
                }
            });

            document.addEventListener('submit', function (event) {
                const form = event.target;

                if (
                    form instanceof HTMLFormElement &&
                    form.matches('.js-confirm-delete')
                ) {
                    const confirmed = window.confirm(
                        @json(__('Are you sure you want to delete this product?'))
                    );

                    if (!confirmed) {
                        event.preventDefault();
                    }
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenus();
                    closeModal();
                }

                // Keep keyboard focus within the open dialog.
                if (event.key === 'Tab' && modal && !modal.classList.contains('hidden')) {
                    const focusable = modal.querySelectorAll(
                        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );

                    if (!focusable.length) return;

                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];

                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                }
            });
        });
    </script>
</x-app-layout>
