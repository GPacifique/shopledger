{{-- resources/views/products/index.blade.php --}}
<x-app-layout>
    @include('partials.ui-theme')

    @php
        $stockQuantityOf = fn ($p) => (float) ($p->quantity ?? $p->stock ?? 0);
        $minimumStockOf  = fn ($p) => (float) ($p->minimum_stock ?? $p->min_stock ?? 0);

        $summaryCards = [
            [
                'label' => __('Total Products'),
                'value' => number_format($totalProducts ?? 0),
                'hint'  => __('Products in your inventory'),
                'tone'  => 'tone-indigo',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7.5 12 12m0 0L4 7.5M12 12v9m8-13.5v9L12 21l-8-4.5v-9L12 3l8 4.5z"/>',
            ],
            [
                'label' => __('Total Stock'),
                'value' => number_format($totalStockUnits ?? 0, 2),
                'hint'  => __('Units available'),
                'tone'  => 'tone-blue',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3"/>',
            ],
            [
                'label' => __('Stock Cost Value'),
                'value' => number_format($stockValueCost ?? 0, 2),
                'hint'  => __('Value at buying price'),
                'tone'  => 'tone-amber',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m5-16H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
            ],
            [
                'label' => __('Stock Retail Value'),
                'value' => number_format($stockValueRetail ?? 0, 2),
                'hint'  => __('Value at selling price'),
                'tone'  => 'tone-green',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9-9 9-9-9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m3-6H11a2 2 0 000 4h2a2 2 0 010 4h-4"/>',
            ],
        ];

        $tableHeads = [
            [__('Product'),       'text-left'],
            [__('SKU'),           'text-left'],
            [__('Buying Price'),  'text-right'],
            [__('Selling Price'), 'text-right'],
            [__('Stock'),         'text-right'],
            [__('Margin'),        'text-right'],
            [__('Actions'),       'text-right'],
        ];
    @endphp

    <div class="ui-page min-h-screen py-6">
        <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">

            {{-- Page header --}}
            <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="ui-text text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ __('Products') }}
                    </h1>
                    <p class="ui-muted mt-1 text-sm">
                        {{ __('Manage products, inventory, imports and exports.') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Import --}}
                    <button
                        type="button"
                        class="js-open-import-modal ui-focus inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-green-700"
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
                            class="js-toggle-export ui-focus inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-blue-700"
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

                        <div id="export-dropdown" class="ui-menu absolute right-0 z-30 mt-2 hidden w-48 overflow-hidden rounded-xl py-1 shadow-lg">
                            <a href="{{ route('products.export') }}" class="ui-menu-item flex items-center gap-2 px-4 py-3 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tone-blue st h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
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
                            class="js-toggle-template ui-btn-outline ui-focus inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-xs font-semibold uppercase tracking-wider shadow-sm"
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

                        <div id="template-dropdown" class="ui-menu absolute right-0 z-30 mt-2 hidden w-56 overflow-hidden rounded-xl py-1 shadow-lg">
                            <a href="{{ route('products.import.template') }}" class="ui-menu-item flex items-center gap-3 px-4 py-3 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tone-green st h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/>
                                </svg>
                                {{ __('Download import template') }}
                            </a>
                        </div>
                    </div>

                    {{-- Add product --}}
                    <a href="{{ route('products.create') }}"
                       class="ui-focus inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                        </svg>
                        {{ __('Add Product') }}
                    </a>
                </div>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div role="status" class="ui-tint tone-green tx mb-5 flex items-start gap-3 rounded-xl p-4 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="ui-tint tone-red tx mb-5 rounded-xl p-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="ui-tint tone-red tx mb-5 rounded-xl p-4 text-sm">
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

                @foreach ($summaryCards as $card)
                    <div class="ui-card rounded-xl p-5 shadow-sm transition hover:shadow-md">
                        <div class="flex items-center justify-between gap-3">
                            <p class="ui-muted text-sm font-medium">{{ $card['label'] }}</p>
                            <span class="ui-icon {{ $card['tone'] }} flex h-10 w-10 shrink-0 items-center justify-center rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    {!! $card['icon'] !!}
                                </svg>
                            </span>
                        </div>
                        <p class="ui-text mt-4 break-words text-2xl font-bold tabular-nums">{{ $card['value'] }}</p>
                        <p class="ui-faint mt-1 text-xs">{{ $card['hint'] }}</p>
                    </div>
                @endforeach

                {{-- Low / out of stock --}}
                <div class="ui-card rounded-xl p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="ui-muted text-sm font-medium">{{ __('Low / Out of Stock') }}</p>
                        <span class="ui-icon tone-red flex h-10 w-10 shrink-0 items-center justify-center rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 001.7 3h15a2 2 0 001.7-3l-7.5-13.1a2 2 0 00-3.4 0z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="ui-text mt-4 text-2xl font-bold tabular-nums">
                        {{ number_format(($lowStockCount ?? 0) + ($outOfStockCount ?? 0)) }}
                    </p>
                    <p class="ui-faint mt-1 text-xs">
                        {{ number_format($lowStockCount ?? 0) }} {{ __('low') }}
                        <span class="px-1">·</span>
                        {{ number_format($outOfStockCount ?? 0) }} {{ __('out') }}
                    </p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="ui-card mb-6 rounded-xl p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <label for="product-search" class="ui-label mb-1.5 block text-sm font-medium">
                            {{ __('Search products') }}
                        </label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="ui-faint pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4"/>
                            </svg>
                            <input
                                id="product-search"
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search by product name, SKU or barcode...') }}"
                                class="ui-input w-full rounded-lg py-2.5 pl-10 pr-3 text-sm shadow-sm"
                            >
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label for="stock-status" class="ui-label mb-1.5 block text-sm font-medium">
                            {{ __('Stock status') }}
                        </label>
                        <select id="stock-status" name="stock_status" class="ui-input w-full rounded-lg py-2.5 text-sm shadow-sm">
                            <option value="">{{ __('All stock statuses') }}</option>
                            <option value="in_stock" @selected(request('stock_status') === 'in_stock')>{{ __('In stock') }}</option>
                            <option value="low_stock" @selected(request('stock_status') === 'low_stock')>{{ __('Low stock') }}</option>
                            <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>{{ __('Out of stock') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2 md:col-span-2">
                        <button type="submit" class="ui-btn-dark ui-focus inline-flex flex-1 items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('products.index') }}" class="ui-btn-outline ui-focus inline-flex items-center justify-center rounded-lg px-3 py-2.5 text-sm font-medium" aria-label="{{ __('Clear filters') }}" title="{{ __('Clear filters') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v6h6"/>
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Products table --}}
            <div class="ui-card overflow-hidden rounded-xl shadow-sm">
                <div class="ui-bd flex flex-col gap-2 border-b px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="ui-text text-base font-semibold">{{ __('Product inventory') }}</h2>
                        <p class="ui-muted mt-0.5 text-sm">{{ __('Review prices, stock levels and product actions.') }}</p>
                    </div>
                    @if (method_exists($products, 'total'))
                        <p class="ui-muted text-sm">
                            {{ __('Showing') }}
                            <span class="ui-text font-semibold">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
                            {{ __('of') }}
                            <span class="ui-text font-semibold">{{ $products->total() }}</span>
                        </p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="ui-table min-w-full">
                        <thead>
                            <tr>
                                @foreach ($tableHeads as [$headLabel, $headAlign])
                                    <th scope="col" class="whitespace-nowrap px-5 py-3.5 {{ $headAlign }} text-xs font-semibold uppercase tracking-wider">{{ $headLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($products as $product)
                                @php
                                    $stockQuantity = $stockQuantityOf($product);
                                    $minimumStock  = $minimumStockOf($product);
                                    $marginValue   = (float) ($product->selling_price ?? 0) - (float) ($product->buying_price ?? 0);

                                    $stockTone  = $stockQuantity <= 0
                                        ? 'tone-red'
                                        : (($minimumStock > 0 && $stockQuantity <= $minimumStock) ? 'tone-amber' : 'tone-green');
                                    $marginTone = $marginValue < 0 ? 'tone-red st' : ($marginValue > 0 ? 'tone-green st' : 'ui-muted');
                                @endphp

                                <tr class="transition">
                                    <td class="px-5 py-4">
                                        <div class="ui-text font-semibold">{{ $product->name }}</div>
                                        @if (!empty($product->barcode))
                                            <div class="ui-muted mt-1 text-xs">
                                                {{ __('Barcode') }}: {{ $product->barcode }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="ui-soft whitespace-nowrap px-5 py-4 text-sm">
                                        {{ $product->sku ?: '—' }}
                                    </td>

                                    <td class="ui-soft whitespace-nowrap px-5 py-4 text-right text-sm tabular-nums">
                                        {{ number_format((float) ($product->buying_price ?? 0), 2) }}
                                    </td>

                                    <td class="ui-text whitespace-nowrap px-5 py-4 text-right text-sm font-medium tabular-nums">
                                        {{ number_format((float) ($product->selling_price ?? 0), 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <span class="ui-badge {{ $stockTone }} inline-flex rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ number_format($stockQuantity, 2) }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm tabular-nums">
                                        <span class="{{ $marginTone }}">{{ number_format($marginValue, 2) }}</span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="ui-act tone-gray ui-focus inline-flex items-center rounded-lg px-2.5 py-2 text-xs font-medium"
                                               title="{{ __('View product') }}" aria-label="{{ __('View product') }}">
                                                {{ __('View') }}
                                            </a>

                                            <a href="{{ route('products.qr-code', $product) }}"
                                               class="ui-act tone-gray ui-focus inline-flex items-center rounded-lg px-2.5 py-2 text-xs font-medium"
                                               title="{{ __('Product QR code') }}" aria-label="{{ __('Product QR code') }}">
                                                {{ __('QR') }}
                                            </a>

                                            <a href="{{ route('products.edit', $product) }}"
                                               class="ui-act tone-indigo ui-focus inline-flex items-center rounded-lg px-2.5 py-2 text-xs font-medium"
                                               title="{{ __('Edit product') }}" aria-label="{{ __('Edit product') }}">
                                                {{ __('Edit') }}
                                            </a>

                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline js-confirm-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="ui-act tone-red ui-focus inline-flex items-center rounded-lg px-2.5 py-2 text-xs font-medium"
                                                        aria-label="{{ __('Delete product') }}" title="{{ __('Delete product') }}">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <div class="ui-icon-circle mx-auto flex h-14 w-14 items-center justify-center rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7.5 12 12m0 0L4 7.5M12 12v9m8-13.5v9L12 21l-8-4.5v-9L12 3l8 4.5z"/>
                                            </svg>
                                        </div>
                                        <h3 class="ui-text mt-4 text-sm font-semibold">{{ __('No products found') }}</h3>
                                        <p class="ui-muted mt-1 text-sm">{{ __('Try changing your filters or add your first product.') }}</p>
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
                    <div class="ui-bd border-t px-5 py-4">
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
            <div class="js-close-import-modal fixed inset-0 bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

            {{-- Dialog --}}
            <div class="ui-modal relative z-10 w-full max-w-xl overflow-hidden rounded-2xl shadow-2xl">
                <div class="ui-bd flex items-start justify-between gap-4 border-b px-6 py-5">
                    <div>
                        <h2 id="import-modal-title" class="ui-text text-lg font-bold">{{ __('Import Products') }}</h2>
                        <p class="ui-muted mt-1 text-sm">{{ __('Upload a supported file to import your inventory.') }}</p>
                    </div>

                    <button type="button"
                            class="js-close-import-modal ui-icon-btn ui-focus inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
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
                            <label for="products-import-file" class="ui-label mb-2 block text-sm font-semibold">
                                {{ __('Choose file') }}
                            </label>

                            <input
                                id="products-import-file"
                                type="file"
                                name="file"
                                accept=".csv,.xlsx,.xls"
                                required
                                class="ui-input ui-soft block w-full cursor-pointer rounded-lg text-sm"
                            >
                            <p class="ui-muted mt-2 text-xs">{{ __('Accepted formats: CSV, XLS and XLSX.') }}</p>
                        </div>

                        <div class="ui-tint tone-blue rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="st mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0-8h.01"/>
                                </svg>
                                <div>
                                    <p class="tx text-sm font-semibold">{{ __('Before importing') }}</p>
                                    <p class="tx mt-1 text-sm leading-6">
                                        {{ __('Use the provided template and keep the column headings unchanged. Check product names, SKUs, prices and quantities before uploading.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="ui-muted text-xs font-semibold uppercase tracking-wider">{{ __('Template') }}</span>
                            <a href="{{ route('products.import.template') }}" class="ui-btn-outline ui-focus inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tone-green st h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/>
                                </svg>
                                {{ __('Download template') }}
                            </a>
                        </div>
                    </div>

                    <div class="ui-modal-foot flex flex-col-reverse gap-3 px-6 py-4 sm:flex-row sm:justify-end">
                        <button type="button" class="js-close-import-modal ui-btn-outline ui-focus inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold">
                            {{ __('Cancel') }}
                        </button>

                        <button type="submit" class="ui-focus inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
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