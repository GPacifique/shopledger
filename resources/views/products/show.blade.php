<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('products.index') }}"
                   class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                          border border-gray-200 bg-white text-gray-500 shadow-sm
                          transition hover:bg-gray-50 hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">

                        <h2 class="truncate text-xl font-bold tracking-tight text-gray-900">
                            {{ $product->name }}
                        </h2>

                        @if($product->status === 'active')
                            <span class="inline-flex items-center gap-1.5 rounded-full
                                         bg-emerald-50 px-2.5 py-1 text-xs font-semibold
                                         text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full
                                         bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                Inactive
                            </span>
                        @endif

                    </div>

                    <p class="mt-0.5 text-sm text-gray-500">
                        Product, inventory, pricing and financial performance
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">

                <a href="{{ route('products.index') }}"
                   class="hidden sm:inline-flex items-center gap-2 rounded-xl
                          border border-gray-200 bg-white px-4 py-2.5
                          text-sm font-semibold text-gray-700 shadow-sm
                          hover:bg-gray-50">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M3 12h18M3 12l6-6m-6 6l6 6"/>
                    </svg>

                    Products
                </a>

                @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

                    <a href="{{ route('products.edit', $product) }}"
                       class="inline-flex items-center gap-2 rounded-xl
                              bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                              text-white shadow-sm transition hover:bg-indigo-700
                              focus:outline-none focus:ring-2 focus:ring-indigo-500
                              focus:ring-offset-2">

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h5m4-15l2 2m-2-2l-8 8v3h3l8-8m0 0l2 2"/>
                        </svg>

                        Edit Product
                    </a>

                @endif
            </div>
        </div>
    </x-slot>

    @php

        /*
        |--------------------------------------------------------------------------
        | FINANCIAL CALCULATIONS
        |--------------------------------------------------------------------------
        */

        $buyingPrice = (float) ($product->buying_price ?? 0);
        $sellingPrice = (float) ($product->selling_price ?? 0);
        $currentStock = (float) ($product->stock ?? 0);
        $minimumStock = (float) ($product->minimum_stock ?? 0);

        $unitProfit = $sellingPrice - $buyingPrice;

        $margin = $buyingPrice > 0
            ? ($unitProfit / $buyingPrice) * 100
            : 0;

        $realizedMargin = $totalSales > 0
            ? ($grossProfit / $totalSales) * 100
            : 0;

        /*
        | Current inventory valuation
        */
        $inventoryCostValue = $currentStock * $buyingPrice;

        $potentialRevenue = $currentStock * $sellingPrice;

        $potentialProfit = $potentialRevenue - $inventoryCostValue;

        /*
        | Stock health
        */
        if ($currentStock <= 0) {

            $stockStatus = 'Out of Stock';
            $stockStatusClass = 'bg-red-50 text-red-700 ring-red-600/20';
            $stockDotClass = 'bg-red-500';
            $stockTextClass = 'text-red-600';

        } elseif ($minimumStock > 0 && $currentStock <= $minimumStock) {

            $stockStatus = 'Low Stock';
            $stockStatusClass = 'bg-amber-50 text-amber-700 ring-amber-600/20';
            $stockDotClass = 'bg-amber-500';
            $stockTextClass = 'text-amber-600';

        } else {

            $stockStatus = 'In Stock';
            $stockStatusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
            $stockDotClass = 'bg-emerald-500';
            $stockTextClass = 'text-emerald-600';
        }

        /*
        | Stock percentage.
        | We intentionally cap it at 100%.
        */
        $stockPercentage = $minimumStock > 0
            ? min(100, ($currentStock / $minimumStock) * 100)
            : 100;

        /*
        | Expiry status
        */
        $expiryStatus = null;
        $expiryClass = null;

        if ($product->expiry_date) {

            $daysToExpiry = now()->startOfDay()
                ->diffInDays($product->expiry_date, false);

            if ($daysToExpiry < 0) {

                $expiryStatus = 'Expired';
                $expiryClass = 'bg-red-50 text-red-700 ring-red-600/20';

            } elseif ($daysToExpiry <= 30) {

                $expiryStatus = 'Expires Soon';
                $expiryClass = 'bg-amber-50 text-amber-700 ring-amber-600/20';

            } else {

                $expiryStatus = 'Valid';
                $expiryClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
            }
        }

    @endphp


    <div class="min-h-screen bg-gray-50/70 py-6 sm:py-8">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">


            {{-- =========================================================
                 PRODUCT HERO
            ========================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="relative">

                    <div class="absolute inset-0 bg-gradient-to-r
                                from-indigo-50 via-white to-emerald-50 opacity-70">
                    </div>

                    <div class="relative p-5 sm:p-7">

                        <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">

                            <div class="flex min-w-0 items-start gap-5">

                                <div class="flex h-16 w-16 shrink-0 items-center justify-center
                                            rounded-2xl bg-indigo-600 text-white
                                            shadow-lg shadow-indigo-600/20">

                                    <svg class="h-8 w-8"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="1.7"
                                              d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m0 10V11M4 7l8 4"/>
                                    </svg>

                                </div>


                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                                            {{ $product->name }}
                                        </h1>

                                        <span class="inline-flex items-center gap-1.5 rounded-full
                                                     px-2.5 py-1 text-xs font-semibold
                                                     ring-1 ring-inset {{ $stockStatusClass }}">

                                            <span class="h-1.5 w-1.5 rounded-full {{ $stockDotClass }}"></span>

                                            {{ $stockStatus }}

                                        </span>

                                        @if($expiryStatus)

                                            <span class="inline-flex items-center rounded-full
                                                         px-2.5 py-1 text-xs font-semibold
                                                         ring-1 ring-inset {{ $expiryClass }}">

                                                {{ $expiryStatus }}

                                            </span>

                                        @endif

                                    </div>


                                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">

                                        <span>
                                            <span class="text-gray-400">SKU</span>
                                            <span class="ml-1 font-mono font-semibold text-gray-700">
                                                {{ $product->sku }}
                                            </span>
                                        </span>

                                        @if($product->barcode)

                                            <span class="hidden h-4 w-px bg-gray-200 sm:block"></span>

                                            <span>
                                                <span class="text-gray-400">Barcode</span>
                                                <span class="ml-1 font-mono text-gray-700">
                                                    {{ $product->barcode }}
                                                </span>
                                            </span>

                                        @endif

                                        @if($product->category)

                                            <span class="hidden h-4 w-px bg-gray-200 sm:block"></span>

                                            <span class="text-gray-600">
                                                {{ $product->category->name }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- QR CODE --}}

                            <div class="flex items-center gap-4 rounded-2xl
                                        border border-gray-200 bg-white/90 p-3 shadow-sm">

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                        Product QR
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Scan to identify
                                    </p>

                                </div>

                                <img src="{{ route('products.qr-code', $product) }}"
                                     alt="QR Code for {{ $product->name }}"
                                     class="h-20 w-20 rounded-lg border border-gray-200 bg-white p-1">

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =========================================================
                 KPI CARDS
            ========================================================== --}}

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">


                {{-- CURRENT STOCK --}}

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <div class="flex items-start justify-between">

                        <div>

                            <p class="text-sm font-medium text-gray-500">
                                Current Stock
                            </p>

                            <p class="mt-2 text-2xl font-bold {{ $stockTextClass }}">
                                {{ number_format($currentStock, 2) }}
                            </p>

                        </div>

                        <div class="flex h-11 w-11 items-center justify-center
                                    rounded-xl bg-gray-100 text-gray-600">

                            <svg class="h-5 w-5"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M4 7.5L12 3l8 4.5M4 7.5V16l8 5 8-5V7.5M4 7.5l8 4.5m8-4.5L12 12M12 12v9"/>

                            </svg>

                        </div>

                    </div>

                    <div class="mt-4">

                        <div class="mb-1.5 flex justify-between text-xs">

                            <span class="text-gray-500">
                                Minimum
                            </span>

                            <span class="font-semibold text-gray-700">
                                {{ number_format($minimumStock, 2) }}
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">

                            <div class="h-full rounded-full
                                        {{ $currentStock <= 0
                                            ? 'bg-red-500'
                                            : ($currentStock <= $minimumStock
                                                ? 'bg-amber-500'
                                                : 'bg-emerald-500') }}"
                                 style="width: {{ $stockPercentage }}%">
                            </div>

                        </div>

                    </div>

                </div>


                {{-- PURCHASE VALUE --}}

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-gray-500">
                        Purchase Value
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ rwf($totalPurchaseCost) }}
                    </p>

                    <div class="mt-4 border-t border-gray-100 pt-3">

                        <div class="flex justify-between text-xs">

                            <span class="text-gray-500">
                                Units purchased
                            </span>

                            <span class="font-semibold text-gray-700">
                                {{ number_format($totalPurchased, 2) }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- SALES VALUE --}}

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-gray-500">
                        Sales Revenue
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ rwf($totalSales) }}
                    </p>

                    <div class="mt-4 border-t border-gray-100 pt-3">

                        <div class="flex justify-between text-xs">

                            <span class="text-gray-500">
                                Units sold
                            </span>

                            <span class="font-semibold text-gray-700">
                                {{ number_format($totalSold, 2) }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- COGS / PROFIT --}}

                <div class="rounded-2xl border
                            {{ $grossProfit >= 0 ? 'border-emerald-200' : 'border-red-200' }}
                            bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium
                              {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">

                        Gross Profit

                    </p>

                    <p class="mt-2 text-2xl font-bold
                              {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">

                        {{ rwf($grossProfit) }}

                    </p>

                    <div class="mt-4 border-t
                                {{ $grossProfit >= 0 ? 'border-emerald-100' : 'border-red-100' }}
                                pt-3">

                        <div class="flex justify-between text-xs">

                            <span class="text-gray-500">
                                Realized margin
                            </span>

                            <span class="font-bold
                                         {{ $realizedMargin >= 0
                                            ? 'text-emerald-700'
                                            : 'text-red-600' }}">

                                {{ number_format($realizedMargin, 1) }}%

                            </span>

                        </div>

                    </div>

                </div>


                {{-- INVENTORY VALUE --}}

                <div class="rounded-2xl border border-indigo-200
                            bg-gradient-to-br from-indigo-50 to-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-indigo-700">
                        Inventory Value
                    </p>

                    <p class="mt-2 text-2xl font-bold text-indigo-700">
                        {{ rwf($inventoryCostValue) }}
                    </p>

                    <div class="mt-4 border-t border-indigo-100 pt-3">

                        <div class="flex justify-between text-xs">

                            <span class="text-gray-500">
                                Potential profit
                            </span>

                            <span class="font-bold
                                         {{ $potentialProfit >= 0
                                            ? 'text-emerald-700'
                                            : 'text-red-600' }}">

                                {{ rwf($potentialProfit) }}

                            </span>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =========================================================
                 INVENTORY & PRICING OVERVIEW
            ========================================================== --}}

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">


                {{-- PRODUCT INFORMATION --}}

                <div class="lg:col-span-2 rounded-2xl border border-gray-200
                            bg-white shadow-sm">

                    <div class="border-b border-gray-100 px-5 py-4 sm:px-6">

                        <h3 class="font-semibold text-gray-900">
                            Product Information
                        </h3>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Product identification and inventory information
                        </p>

                    </div>


                    <div class="grid grid-cols-1 gap-6 p-5 sm:grid-cols-2 sm:p-6">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Product Name
                            </p>

                            <p class="mt-1.5 font-medium text-gray-900">
                                {{ $product->name }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                SKU
                            </p>

                            <p class="mt-1.5 font-mono font-medium text-gray-900">
                                {{ $product->sku }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Category
                            </p>

                            <p class="mt-1.5 text-gray-800">
                                {{ $product->category->name ?? '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Supplier
                            </p>

                            <p class="mt-1.5 text-gray-800">
                                {{ $product->supplier->name ?? '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Barcode
                            </p>

                            <p class="mt-1.5 font-mono text-sm text-gray-800">
                                {{ $product->barcode ?: 'Not provided' }}
                            </p>
                        </div>


                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Expiry Date
                            </p>

                            @if($product->expiry_date)

                                <div class="mt-1.5 flex items-center gap-2">

                                    <span class="text-gray-800">
                                        {{ $product->expiry_date->format('M d, Y') }}
                                    </span>

                                    @if($expiryStatus)

                                        <span class="rounded-full px-2 py-0.5 text-[11px]
                                                     font-semibold ring-1 ring-inset {{ $expiryClass }}">
                                            {{ $expiryStatus }}
                                        </span>

                                    @endif

                                </div>

                            @else

                                <p class="mt-1.5 text-gray-500">
                                    No expiry date
                                </p>

                            @endif

                        </div>


                        <div class="sm:col-span-2">

                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Description
                            </p>

                            <p class="mt-1.5 leading-relaxed text-gray-700">
                                {{ $product->description ?: 'No description has been added for this product.' }}
                            </p>

                        </div>


                        <div class="border-t border-gray-100 pt-5 sm:col-span-2">

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

                                <div>

                                    <p class="text-xs text-gray-400">
                                        Product ID
                                    </p>

                                    <p class="mt-1 font-mono text-sm font-semibold text-gray-700">
                                        #{{ $product->id }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400">
                                        Created
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-gray-700">
                                        {{ $product->created_at->format('M d, Y · H:i') }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400">
                                        Last Updated
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-gray-700">
                                        {{ $product->updated_at->format('M d, Y · H:i') }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- PRICING --}}

                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-100 px-5 py-4">

                        <h3 class="font-semibold text-gray-900">
                            Pricing
                        </h3>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Current unit economics
                        </p>

                    </div>


                    <div class="space-y-4 p-5">


                        {{-- BUYING PRICE --}}

                        <div class="rounded-xl bg-gray-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Buying Price
                            </p>

                            <p class="mt-1.5 text-xl font-bold text-gray-900">
                                {{ rwf($buyingPrice) }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Unit acquisition cost
                            </p>

                        </div>


                        {{-- SELLING PRICE --}}

                        <div class="rounded-xl bg-emerald-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                                Selling Price
                            </p>

                            <p class="mt-1.5 text-xl font-bold text-emerald-700">
                                {{ rwf($sellingPrice) }}
                            </p>

                            <p class="mt-1 text-xs text-emerald-600">
                                Current retail price
                            </p>

                        </div>


                        {{-- UNIT PROFIT --}}

                        <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">

                            <div class="flex items-center justify-between">

                                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">
                                    Unit Profit
                                </p>

                                <span class="rounded-full bg-white px-2 py-1
                                             text-xs font-bold text-indigo-700 shadow-sm">

                                    {{ number_format($margin, 1) }}%

                                </span>

                            </div>

                            <p class="mt-2 text-lg font-bold
                                      {{ $unitProfit >= 0
                                         ? 'text-indigo-700'
                                         : 'text-red-600' }}">

                                {{ rwf($unitProfit) }}

                                <span class="text-xs font-medium text-gray-500">
                                    / unit
                                </span>

                            </p>

                        </div>


                        {{-- INVENTORY ECONOMICS --}}

                        <div class="border-t border-gray-100 pt-4 space-y-3">

                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-500">
                                    Inventory cost
                                </span>

                                <span class="font-semibold text-gray-900">
                                    {{ rwf($inventoryCostValue) }}
                                </span>

                            </div>


                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-500">
                                    Potential revenue
                                </span>

                                <span class="font-semibold text-emerald-700">
                                    {{ rwf($potentialRevenue) }}
                                </span>

                            </div>


                            <div class="flex items-center justify-between border-t
                                        border-gray-100 pt-3 text-sm">

                                <span class="font-medium text-gray-600">
                                    Potential profit
                                </span>

                                <span class="font-bold
                                             {{ $potentialProfit >= 0
                                                ? 'text-emerald-700'
                                                : 'text-red-600' }}">

                                    {{ rwf($potentialProfit) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =========================================================
                 PERFORMANCE SUMMARY
            ========================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-5 py-4 sm:px-6">

                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Product Performance
                            </h3>

                            <p class="text-xs text-gray-500">
                                Lifetime purchasing and sales activity
                            </p>

                        </div>

                        <span class="text-xs text-gray-400">
                            Product #{{ $product->id }}
                        </span>

                    </div>

                </div>


                <div class="grid grid-cols-2 divide-x divide-y
                            divide-gray-100 sm:grid-cols-3 lg:grid-cols-6
                            lg:divide-y-0">


                    <div class="p-5">

                        <p class="text-xs font-medium text-gray-500">
                            Purchased
                        </p>

                        <p class="mt-2 text-2xl font-bold text-blue-700">
                            {{ number_format($totalPurchased, 2) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Units
                        </p>

                    </div>


                    <div class="p-5">

                        <p class="text-xs font-medium text-gray-500">
                            Sold
                        </p>

                        <p class="mt-2 text-2xl font-bold text-indigo-700">
                            {{ number_format($totalSold, 2) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Units
                        </p>

                    </div>


                    <div class="p-5">

                        <p class="text-xs font-medium text-gray-500">
                            Remaining
                        </p>

                        <p class="mt-2 text-2xl font-bold {{ $stockTextClass }}">
                            {{ number_format($currentStock, 2) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Units
                        </p>

                    </div>


                    <div class="p-5">

                        <p class="text-xs font-medium text-gray-500">
                            Purchase Value
                        </p>

                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ rwf($totalPurchaseCost) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Historical cost
                        </p>

                    </div>


                    <div class="p-5">

                        <p class="text-xs font-medium text-gray-500">
                            Revenue
                        </p>

                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ rwf($totalSales) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Sales value
                        </p>

                    </div>


                    <div class="p-5">

                        <p class="text-xs font-medium text-gray-500">
                            Gross Profit
                        </p>

                        <p class="mt-2 text-lg font-bold
                                  {{ $grossProfit >= 0
                                     ? 'text-emerald-700'
                                     : 'text-red-600' }}">

                            {{ rwf($grossProfit) }}

                        </p>

                        <p class="mt-1 text-xs
                                  {{ $grossProfit >= 0
                                     ? 'text-emerald-600'
                                     : 'text-red-500' }}">

                            {{ number_format($realizedMargin, 1) }}% margin

                        </p>

                    </div>

                </div>

            </section>


            {{-- =========================================================
                 PURCHASE HISTORY
            ========================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="flex flex-col gap-3 border-b border-gray-100
                            px-5 py-4 sm:flex-row sm:items-center
                            sm:justify-between sm:px-6">

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Purchase History
                        </h3>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Stock purchased for this product
                        </p>

                    </div>

                    <div class="rounded-lg bg-blue-50 px-3 py-1.5
                                text-xs font-semibold text-blue-700">

                        {{ number_format($totalPurchased, 2) }}
                        units purchased

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-100">

                        <thead class="bg-gray-50/80">

                            <tr>

                                <th class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase tracking-wider text-gray-500">
                                    Date
                                </th>

                                <th class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase tracking-wider text-gray-500">
                                    Supplier
                                </th>

                                <th class="px-5 py-3 text-right text-xs
                                           font-semibold uppercase tracking-wider text-gray-500">
                                    Quantity
                                </th>

                                <th class="px-5 py-3 text-right text-xs
                                           font-semibold uppercase tracking-wider text-gray-500">
                                    Unit Cost
                                </th>

                                <th class="px-5 py-3 text-right text-xs
                                           font-semibold uppercase tracking-wider text-gray-500">
                                    Total
                                </th>

                                <th class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase tracking-wider text-gray-500">
                                    Recorded By
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100 bg-white">

                            @forelse($purchaseItems as $item)

                                <tr class="transition hover:bg-gray-50/70">

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">

                                        {{ $item->purchase?->purchase_date
                                            ? \Carbon\Carbon::parse($item->purchase->purchase_date)->format('M d, Y')
                                            : '—' }}

                                    </td>


                                    <td class="px-5 py-4">

                                        <span class="text-sm font-medium text-gray-800">
                                            {{ $item->purchase?->supplier?->name ?? '—' }}
                                        </span>

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4
                                               text-right text-sm font-semibold text-gray-800">

                                        {{ number_format($item->quantity, 2) }}

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4
                                               text-right text-sm text-gray-600">

                                        {{ rwf($item->unit_cost) }}

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4
                                               text-right text-sm font-bold text-gray-900">

                                        {{ rwf($item->line_total) }}

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4">

                                        <div class="flex items-center gap-2">

                                            <div class="flex h-7 w-7 items-center justify-center
                                                        rounded-full bg-gray-100 text-xs font-bold text-gray-600">

                                                {{ strtoupper(substr($item->purchase?->creator?->name ?? 'U', 0, 1)) }}

                                            </div>

                                            <span class="text-sm text-gray-600">
                                                {{ $item->purchase?->creator?->name ?? '—' }}
                                            </span>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="px-5 py-12 text-center">

                                        <p class="text-sm font-semibold text-gray-700">
                                            No purchases found
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Purchase history will appear here.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($purchaseItems->hasPages())

                    <div class="border-t border-gray-100 px-5 py-4">
                        {{ $purchaseItems->links() }}
                    </div>

                @endif

            </section>


            {{-- =========================================================
                 SALES HISTORY
            ========================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="flex flex-col gap-3 border-b border-gray-100
                            px-5 py-4 sm:flex-row sm:items-center
                            sm:justify-between sm:px-6">

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Sales History
                        </h3>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Revenue and realized product profit
                        </p>

                    </div>

                    <div class="rounded-lg bg-emerald-50 px-3 py-1.5
                                text-xs font-semibold text-emerald-700">

                        {{ number_format($totalSold, 2) }}
                        units sold

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-100">

                        <thead class="bg-gray-50/80">

                            <tr>

                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Date
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Customer
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Qty
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Unit Price
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Revenue
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Gross Profit
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Cashier
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100 bg-white">

                            @forelse($saleItems as $item)

                                @php

                                    $saleQuantity = (float) ($item->quantity ?? 0);
                                    $salePrice = (float) ($item->unit_price ?? 0);
                                    $saleCost = (float) ($item->cost_price_at_sale ?? 0);

                                    $itemProfit =
                                        ($salePrice - $saleCost)
                                        * $saleQuantity;

                                @endphp


                                <tr class="transition hover:bg-gray-50/70">

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">

                                        {{ $item->sale?->sale_date
                                            ? \Carbon\Carbon::parse($item->sale->sale_date)->format('M d, Y')
                                            : '—' }}

                                    </td>


                                    <td class="px-5 py-4">

                                        <span class="text-sm font-medium text-gray-800">

                                            {{ $item->sale?->customer?->name
                                                ?? 'Walk-in Customer' }}

                                        </span>

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4
                                               text-right text-sm font-semibold text-gray-800">

                                        {{ number_format($saleQuantity, 2) }}

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4
                                               text-right text-sm text-gray-600">

                                        {{ rwf($salePrice) }}

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4
                                               text-right text-sm font-bold text-gray-900">

                                        {{ rwf($item->line_total) }}

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4 text-right">

                                        <span class="inline-flex rounded-lg px-2.5 py-1
                                                     text-sm font-bold
                                                     {{ $itemProfit >= 0
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-red-50 text-red-700' }}">

                                            {{ rwf($itemProfit) }}

                                        </span>

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4">

                                        <div class="flex items-center gap-2">

                                            <div class="flex h-7 w-7 items-center justify-center
                                                        rounded-full bg-indigo-50 text-xs
                                                        font-bold text-indigo-600">

                                                {{ strtoupper(substr($item->sale?->creator?->name ?? 'U', 0, 1)) }}

                                            </div>

                                            <span class="text-sm text-gray-600">

                                                {{ $item->sale?->creator?->name ?? '—' }}

                                            </span>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="px-5 py-12 text-center">

                                        <p class="text-sm font-semibold text-gray-700">
                                            No sales found
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Sales history will appear here.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($saleItems->hasPages())

                    <div class="border-t border-gray-100 px-5 py-4">
                        {{ $saleItems->links() }}
                    </div>

                @endif

            </section>


            {{-- =========================================================
                 LAST ACTIVITY
            ========================================================== --}}

            <section class="grid grid-cols-1 gap-6 md:grid-cols-2">


                {{-- LAST PURCHASE --}}

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center
                                    rounded-xl bg-blue-50 text-blue-600">

                            <svg class="h-5 w-5"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m0 10V11M4 7l8 4"/>

                            </svg>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Last Purchase
                            </h3>

                            <p class="text-xs text-gray-500">
                                Most recent stock acquisition
                            </p>

                        </div>

                    </div>


                    @if($lastPurchase)

                        <div class="mt-5 rounded-xl bg-gray-50 p-4 space-y-3">

                            <div class="flex justify-between">

                                <span class="text-sm text-gray-500">
                                    Quantity
                                </span>

                                <span class="font-bold text-gray-900">
                                    {{ number_format($lastPurchase->quantity, 2) }}
                                </span>

                            </div>


                            <div class="flex justify-between">

                                <span class="text-sm text-gray-500">
                                    Unit Cost
                                </span>

                                <span class="font-semibold text-gray-900">
                                    {{ rwf($lastPurchase->unit_cost) }}
                                </span>

                            </div>


                            <div class="flex justify-between border-t border-gray-200 pt-3">

                                <span class="text-sm text-gray-500">
                                    Total
                                </span>

                                <span class="font-bold text-blue-700">
                                    {{ rwf($lastPurchase->line_total) }}
                                </span>

                            </div>

                        </div>

                    @else

                        <p class="mt-5 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">
                            No purchase activity recorded yet.
                        </p>

                    @endif

                </div>


                {{-- LAST SALE --}}

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center
                                    rounded-xl bg-emerald-50 text-emerald-600">

                            <svg class="h-5 w-5"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M3 12h18M3 12l6-6m-6 6l6 6"/>

                            </svg>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Last Sale
                            </h3>

                            <p class="text-xs text-gray-500">
                                Most recent customer transaction
                            </p>

                        </div>

                    </div>


                    @if($lastSale)

                        @php

                            $lastSaleQuantity = (float) ($lastSale->quantity ?? 0);
                            $lastSalePrice = (float) ($lastSale->unit_price ?? 0);
                            $lastSaleCost = (float) ($lastSale->cost_price_at_sale ?? 0);

                            $lastSaleProfit =
                                ($lastSalePrice - $lastSaleCost)
                                * $lastSaleQuantity;

                        @endphp


                        <div class="mt-5 rounded-xl bg-gray-50 p-4 space-y-3">

                            <div class="flex justify-between">

                                <span class="text-sm text-gray-500">
                                    Quantity
                                </span>

                                <span class="font-bold text-gray-900">
                                    {{ number_format($lastSaleQuantity, 2) }}
                                </span>

                            </div>


                            <div class="flex justify-between">

                                <span class="text-sm text-gray-500">
                                    Revenue
                                </span>

                                <span class="font-semibold text-gray-900">
                                    {{ rwf($lastSale->line_total) }}
                                </span>

                            </div>


                            <div class="flex justify-between border-t border-gray-200 pt-3">

                                <span class="text-sm text-gray-500">
                                    Gross Profit
                                </span>

                                <span class="font-bold
                                             {{ $lastSaleProfit >= 0
                                                ? 'text-emerald-700'
                                                : 'text-red-600' }}">

                                    {{ rwf($lastSaleProfit) }}

                                </span>

                            </div>

                        </div>

                    @else

                        <p class="mt-5 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">
                            No sales activity recorded yet.
                        </p>

                    @endif

                </div>

            </section>


            {{-- =========================================================
                 ADMIN ACTIONS
            ========================================================== --}}

            @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

                <section class="flex flex-col-reverse gap-3
                                border-t border-gray-200 pt-6
                                sm:flex-row sm:items-center sm:justify-between">

                    <form action="{{ route('products.destroy', $product) }}"
                          method="POST"
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this product? This action cannot be undone.') }}')">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2
                                       rounded-xl border border-red-200 bg-white px-4 py-2.5
                                       text-sm font-semibold text-red-600 shadow-sm
                                       transition hover:bg-red-50 sm:w-auto">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/>

                            </svg>

                            Delete Product

                        </button>

                    </form>


                    <div class="flex w-full gap-3 sm:w-auto">

                        <a href="{{ route('products.index') }}"
                           class="inline-flex flex-1 items-center justify-center
                                  rounded-xl border border-gray-200 bg-white px-4 py-2.5
                                  text-sm font-semibold text-gray-700 shadow-sm
                                  hover:bg-gray-50 sm:flex-none">

                            Cancel

                        </a>


                        <a href="{{ route('products.edit', $product) }}"
                           class="inline-flex flex-1 items-center justify-center gap-2
                                  rounded-xl bg-indigo-600 px-5 py-2.5
                                  text-sm font-semibold text-white shadow-sm
                                  hover:bg-indigo-700 sm:flex-none">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h5m4-15l2 2m-2-2l-8 8v3h3l8-8m0 0l2 2"/>

                            </svg>

                            Edit Product

                        </a>

                    </div>

                </section>

            @endif

        </div>

    </div>

</x-app-layout>