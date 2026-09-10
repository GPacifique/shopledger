<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Products') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Manage products, inventory, imports and exports.') }}
                </p>
            </div>

            @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

                <div class="flex flex-wrap items-center gap-2">

                    {{-- ================================================= --}}
                    {{-- IMPORT PRODUCTS --}}
                    {{-- ================================================= --}}

                    <button
                        type="button"
                        onclick="document.getElementById('import-products-modal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l-4-4m4 4l4-4"
                            />
                        </svg>

                        {{ __('Import') }}
                    </button>


                    {{-- ================================================= --}}
                    {{-- EXPORT DROPDOWN --}}
                    {{-- ================================================= --}}

                    <div class="relative" id="export-dropdown-wrapper">

                        <button
                            type="button"
                            onclick="toggleExportDropdown()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4M5 20h14"
                                />
                            </svg>

                            {{ __('Export') }}

                            <svg
                                class="ml-2 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>

                        </button>


                        {{-- Dropdown --}}
                        <div
                            id="export-dropdown"
                            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                        >

                            <div class="px-4 py-3 border-b border-gray-100">

                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('Export Products') }}
                                </p>

                            </div>


                            {{-- Excel --}}
                            <a
                                href="{{ route('products.export', ['format' => 'xlsx']) }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700"
                            >

                                <svg
                                    class="w-5 h-5 mr-3 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4h16v16H4zM8 8l8 8m0-8l-8 8"
                                    />
                                </svg>

                                <div>
                                    <div class="font-medium">
                                        {{ __('Excel Spreadsheet') }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        .xlsx
                                    </div>
                                </div>

                            </a>


                            {{-- CSV --}}
                            <a
                                href="{{ route('products.export', ['format' => 'csv']) }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700"
                            >

                                <svg
                                    class="w-5 h-5 mr-3 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4h16v16H4z"
                                    />
                                </svg>

                                <div>
                                    <div class="font-medium">
                                        {{ __('CSV File') }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        .csv
                                    </div>
                                </div>

                            </a>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- TEMPLATE DROPDOWN --}}
                    {{-- ================================================= --}}

                    <div class="relative" id="template-dropdown-wrapper">

                        <button
                            type="button"
                            onclick="toggleTemplateDropdown()"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >

                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z"
                                />
                            </svg>

                            {{ __('Template') }}

                            <svg
                                class="ml-2 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>

                        </button>


                        <div
                            id="template-dropdown"
                            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                        >

                            <div class="px-4 py-3 border-b border-gray-100">

                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('Import Template') }}
                                </p>

                            </div>


                            {{-- Excel Template --}}
                            <a
                                href="{{ route('products.import.template', ['format' => 'xlsx']) }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700"
                            >

                                <svg
                                    class="w-5 h-5 mr-3 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4h16v16H4z"
                                    />
                                </svg>

                                <div>
                                    <div class="font-medium">
                                        {{ __('Excel Template') }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        .xlsx
                                    </div>
                                </div>

                            </a>


                            {{-- CSV Template --}}
                            <a
                                href="{{ route('products.import.template', ['format' => 'csv']) }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700"
                            >

                                <svg
                                    class="w-5 h-5 mr-3 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4h16v16H4z"
                                    />
                                </svg>

                                <div>
                                    <div class="font-medium">
                                        {{ __('CSV Template') }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        .csv
                                    </div>
                                </div>

                            </a>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ADD PRODUCT --}}
                    {{-- ================================================= --}}

                    <a
                        href="{{ route('products.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >

                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>

                        {{ __('Add Product') }}

                    </a>

                </div>

            @endif

        </div>

    </x-slot>


    {{-- ========================================================= --}}
    {{-- PAGE --}}
    {{-- ========================================================= --}}

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            {{-- ========================================================= --}}
            {{-- SUCCESS --}}
            {{-- ========================================================= --}}

            @if(session('success'))

                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">

                    <div class="flex items-center">

                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                        <span>
                            {{ session('success') }}
                        </span>

                    </div>

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- ERROR --}}
            {{-- ========================================================= --}}

            @if(session('error'))

                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">

                    <div class="flex items-center">

                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>

                        <span>
                            {{ session('error') }}
                        </span>

                    </div>

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- VALIDATION ERRORS --}}
            {{-- ========================================================= --}}

            @if($errors->any())

                <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">

                    <div class="font-semibold mb-2">
                        {{ __('There were some problems with your import:') }}
                    </div>

                    <ul class="list-disc list-inside text-sm space-y-1">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- IMPORT / EXPORT INFORMATION --}}
            {{-- ========================================================= --}}

            @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5 mb-6">

                    <div class="flex items-start">

                        <svg
                            class="w-6 h-6 text-indigo-600 mr-3 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-8h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                            />
                        </svg>


                        <div class="flex-1">

                            <h3 class="font-semibold text-indigo-900">
                                {{ __('Product Import & Export') }}
                            </h3>

                            <p class="text-sm text-indigo-700 mt-1">
                                {{ __('Import multiple products using Excel or CSV files, or export your products for backup, editing and reporting.') }}
                            </p>

                            <div class="flex flex-wrap gap-2 mt-3">

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-green-700 border border-green-200">
                                    XLSX Excel
                                </span>

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-blue-700 border border-blue-200">
                                    CSV
                                </span>

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-gray-700 border border-gray-200">
                                    {{ __('Maximum 10 MB') }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- INVENTORY STATS --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">


                {{-- Total Products --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5">

                    <p class="text-sm font-medium text-gray-500">
                        {{ __('Total Products') }}
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ number_format($totalProducts) }}
                    </p>

                </div>


                {{-- Stock Units --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5">

                    <p class="text-sm font-medium text-gray-500">
                        {{ __('Total Stock') }}
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ format_qty($totalStockUnits) }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        {{ __('Units') }}
                    </p>

                </div>


                {{-- Cost Value --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5">

                    <p class="text-sm font-medium text-gray-500">
                        {{ __('Stock Value (Cost)') }}
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-blue-600">
                        {{ number_format($stockValueCost) }}
                    </p>

                    <p class="text-xs text-gray-400">
                        RWF
                    </p>

                </div>


                {{-- Retail Value --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5">

                    <p class="text-sm font-medium text-gray-500">
                        {{ __('Stock Value (Retail)') }}
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-green-600">
                        {{ number_format($stockValueRetail) }}
                    </p>

                    <p class="text-xs text-gray-400">
                        RWF
                    </p>

                </div>


                {{-- Low / Out --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5">

                    <p class="text-sm font-medium text-gray-500">
                        {{ __('Low / Out of Stock') }}
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-red-600">

                        {{ number_format($lowStockCount) }}

                        <span class="text-gray-300">
                            /
                        </span>

                        {{ number_format($outOfStockCount) }}

                    </p>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FILTERS --}}
            {{-- ========================================================= --}}

            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">

                <div class="p-6">

                    <form
                        method="GET"
                        action="{{ route('products.index') }}"
                        class="flex flex-wrap gap-4"
                    >

                        <div class="flex-1 min-w-[200px]">

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search by product name, SKU or description...') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                        </div>


                        <div>

                            <select
                                name="stock_status"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                                <option value="">
                                    {{ __('All Stock') }}
                                </option>

                                <option
                                    value="in"
                                    {{ request('stock_status') === 'in' ? 'selected' : '' }}
                                >
                                    {{ __('In Stock') }}
                                </option>

                                <option
                                    value="low"
                                    {{ request('stock_status') === 'low' ? 'selected' : '' }}
                                >
                                    {{ __('Low Stock') }}
                                </option>

                                <option
                                    value="out"
                                    {{ request('stock_status') === 'out' ? 'selected' : '' }}
                                >
                                    {{ __('Out of Stock') }}
                                </option>

                            </select>

                        </div>


                        <div>

                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                            >
                                {{ __('Filter') }}
                            </button>

                            @if(request('search') || request('stock_status'))

                                <a
                                    href="{{ route('products.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 ml-2"
                                >
                                    {{ __('Clear') }}
                                </a>

                            @endif

                        </div>

                    </form>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PRODUCTS TABLE --}}
            {{-- ========================================================= --}}

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('SKU') }}
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Name') }}
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Buying Price') }}
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Selling Price') }}
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Stock') }}
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Margin') }}
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>

                            </tr>

                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($products as $product)

                                <tr class="hover:bg-gray-50">

                                    {{-- SKU --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ $product->sku }}
                                    </td>


                                    {{-- Product --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $product->name }}
                                        </div>

                                        @if($product->description)

                                            <div class="text-sm text-gray-500 truncate max-w-xs">
                                                {{ Str::limit($product->description, 50) }}
                                            </div>

                                        @endif

                                    </td>


                                    {{-- Buying Price --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ rwf($product->buying_price) }}
                                    </td>


                                    {{-- Selling Price --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ rwf($product->selling_price) }}
                                    </td>


                                    {{-- Stock --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($product->stock <= 0)

                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ __('Out of Stock') }}
                                            </span>

                                        @elseif($product->stock <= $product->minimum_stock)

                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">

                                                {{ format_qty($product->stock) }}

                                                <span class="ml-1">
                                                    ({{ __('Low Stock') }})
                                                </span>

                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ format_qty($product->stock) }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Margin --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                                        @php

                                            $margin = $product->buying_price > 0
                                                ? (($product->selling_price - $product->buying_price) / $product->buying_price) * 100
                                                : 0;

                                        @endphp

                                        <span class="{{ $margin > 0 ? 'text-green-600' : 'text-red-600' }}">

                                            {{ number_format($margin, 1) }}%

                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">

                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="text-gray-600 hover:text-gray-900"
                                        >
                                            {{ __('View') }}
                                        </a>

                                        <a
                                            href="{{ route('products.qr-code', $product) }}"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            {{ __('QR') }}
                                        </a>


                                        @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

                                            <a
                                                href="{{ route('products.edit', $product) }}"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                {{ __('Edit') }}
                                            </a>


                                            <form
                                                action="{{ route('products.destroy', $product) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    {{ __('Delete') }}
                                                </button>

                                            </form>

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center text-gray-500"
                                    >

                                        <svg
                                            class="mx-auto h-12 w-12 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            />
                                        </svg>

                                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                                            {{ __('No products found') }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ __('Get started by creating a new product.') }}
                                        </p>

                                        <div class="mt-6">

                                            <a
                                                href="{{ route('products.create') }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                            >

                                                <svg
                                                    class="-ml-1 mr-2 h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    />
                                                </svg>

                                                {{ __('Add Product') }}

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($products->hasPages())

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $products->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- IMPORT PRODUCTS MODAL --}}
    {{-- ========================================================= --}}

    @if(auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

        <div
            id="import-products-modal"
            class="hidden fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="import-products-title"
            role="dialog"
            aria-modal="true"
        >

            {{-- Background --}}
            <div
                class="fixed inset-0 bg-gray-900 bg-opacity-50"
                onclick="closeImportModal()"
            ></div>


            <div class="flex min-h-screen items-center justify-center p-4">

                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-2xl">


                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">

                        <div>

                            <h3
                                id="import-products-title"
                                class="text-lg font-semibold text-gray-900"
                            >
                                {{ __('Import Products') }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Upload an Excel or CSV spreadsheet to add products to your shop.') }}
                            </p>

                        </div>


                        <button
                            type="button"
                            onclick="closeImportModal()"
                            class="text-gray-400 hover:text-gray-600"
                        >

                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>

                        </button>

                    </div>


                    {{-- Import Form --}}
                    <form
                        action="{{ route('products.import') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf

                        <div class="p-6">


                            {{-- Upload --}}
                            <div>

                                <label
                                    for="products_file"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    {{ __('Products Spreadsheet') }}
                                </label>


                                <input
                                    id="products_file"
                                    name="file"
                                    type="file"
                                    accept=".xlsx,.xls,.csv"
                                    required
                                    class="block w-full text-sm text-gray-500
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-md file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-indigo-50 file:text-indigo-700
                                           hover:file:bg-indigo-100
                                           border border-gray-300 rounded-md
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >


                                <p class="mt-2 text-xs text-gray-500">
                                    {{ __('Maximum file size: 10 MB. Supported formats: XLSX, XLS and CSV.') }}
                                </p>

                            </div>


                            {{-- Expected Columns --}}
                            <div class="mt-5 bg-gray-50 border border-gray-200 rounded-lg p-4">

                                <div class="flex items-center justify-between mb-3">

                                    <h4 class="text-sm font-semibold text-gray-800">
                                        {{ __('Spreadsheet Columns') }}
                                    </h4>

                                    <span class="text-xs text-gray-500">
                                        {{ __('Use the provided template') }}
                                    </span>

                                </div>


                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-xs text-gray-600">

                                    <div>
                                        <strong>SKU</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Unique product code') }}
                                    </div>

                                    <div>
                                        <strong>Product Name</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Product name') }}
                                    </div>

                                    <div>
                                        <strong>Category</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Existing category') }}
                                    </div>

                                    <div>
                                        <strong>Supplier</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Optional supplier') }}
                                    </div>

                                    <div>
                                        <strong>Barcode</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Optional barcode') }}
                                    </div>

                                    <div>
                                        <strong>QR Code</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Optional QR code') }}
                                    </div>

                                    <div>
                                        <strong>Buying Price</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Purchase cost') }}
                                    </div>

                                    <div>
                                        <strong>Selling Price</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Selling price') }}
                                    </div>

                                    <div>
                                        <strong>Opening Quantity</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Initial stock') }}
                                    </div>

                                    <div>
                                        <strong>Opening Unit Cost</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Initial stock cost') }}
                                    </div>

                                    <div>
                                        <strong>Minimum Stock</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Low-stock level') }}
                                    </div>

                                    <div>
                                        <strong>Expiry Date</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Optional date') }}
                                    </div>

                                    <div>
                                        <strong>Product Image</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Optional image path') }}
                                    </div>

                                    <div>
                                        <strong>Status</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('active or inactive') }}
                                    </div>

                                    <div>
                                        <strong>Opening Stock Date</strong>
                                        <span class="text-gray-400">—</span>
                                        {{ __('Optional stock date') }}
                                    </div>

                                </div>

                            </div>


                            {{-- Important Information --}}
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">

                                <div class="flex">

                                    <svg
                                        class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13.5A1 1 0 003.33 19h17.34a1 1 0 00.86-1.64l-7.82-13.5a1 1 0 00-1.72 0z"
                                        />
                                    </svg>

                                    <div class="text-xs text-yellow-800">

                                        <p class="font-semibold mb-1">
                                            {{ __('Before importing') }}
                                        </p>

                                        <ul class="list-disc list-inside space-y-1">

                                            <li>
                                                {{ __('Category names must already exist in your shop.') }}
                                            </li>

                                            <li>
                                                {{ __('SKU must be unique within your shop.') }}
                                            </li>

                                            <li>
                                                {{ __('Use numbers only for prices and quantities.') }}
                                            </li>

                                            <li>
                                                {{ __('Opening Quantity will become the initial stock.') }}
                                            </li>

                                        </ul>

                                    </div>

                                </div>

                            </div>


                            {{-- Template Downloads --}}
                            <div class="mt-5">

                                <p class="text-sm font-medium text-gray-700 mb-3 text-center">
                                    {{ __('Need a template?') }}
                                </p>


                                <div class="flex flex-col sm:flex-row gap-3">

                                    <a
                                        href="{{ route('products.import.template', ['format' => 'xlsx']) }}"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-green-50 border border-green-200 rounded-md text-sm font-medium text-green-700 hover:bg-green-100"
                                    >

                                        <svg
                                            class="w-5 h-5 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h16v16H4z"
                                            />
                                        </svg>

                                        {{ __('Excel Template') }}

                                    </a>


                                    <a
                                        href="{{ route('products.import.template', ['format' => 'csv']) }}"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-md text-sm font-medium text-blue-700 hover:bg-blue-100"
                                    >

                                        <svg
                                            class="w-5 h-5 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h16v16H4z"
                                            />
                                        </svg>

                                        {{ __('CSV Template') }}

                                    </a>

                                </div>

                            </div>

                        </div>


                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">

                            <button
                                type="button"
                                onclick="closeImportModal()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
                            >
                                {{ __('Cancel') }}
                            </button>


                            <button
                                type="submit"
                                class="inline-flex items-center px-5 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            >

                                <svg
                                    class="-ml-1 mr-2 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4"
                                    />
                                </svg>

                                {{ __('Import Products') }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- DROPDOWN JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        function toggleExportDropdown() {

            const dropdown = document.getElementById('export-dropdown');

            const templateDropdown = document.getElementById('template-dropdown');

            dropdown.classList.toggle('hidden');

            if (templateDropdown) {
                templateDropdown.classList.add('hidden');
            }

        }


        function toggleTemplateDropdown() {

            const dropdown = document.getElementById('template-dropdown');

            const exportDropdown = document.getElementById('export-dropdown');

            dropdown.classList.toggle('hidden');

            if (exportDropdown) {
                exportDropdown.classList.add('hidden');
            }

        }


        function closeImportModal() {

            const modal = document.getElementById('import-products-modal');

            if (modal) {
                modal.classList.add('hidden');
            }

        }


        // Close dropdowns when clicking outside
        document.addEventListener('click', function (event) {

            const exportWrapper = document.getElementById('export-dropdown-wrapper');

            const templateWrapper = document.getElementById('template-dropdown-wrapper');

            const exportDropdown = document.getElementById('export-dropdown');

            const templateDropdown = document.getElementById('template-dropdown');


            if (
                exportWrapper &&
                exportDropdown &&
                !exportWrapper.contains(event.target)
            ) {
                exportDropdown.classList.add('hidden');
            }


            if (
                templateWrapper &&
                templateDropdown &&
                !templateWrapper.contains(event.target)
            ) {
                templateDropdown.classList.add('hidden');
            }

        });


        // ESC closes modal and dropdowns
        document.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {

                closeImportModal();

                const exportDropdown = document.getElementById('export-dropdown');

                const templateDropdown = document.getElementById('template-dropdown');

                if (exportDropdown) {
                    exportDropdown.classList.add('hidden');
                }

                if (templateDropdown) {
                    templateDropdown.classList.add('hidden');
                }

            }

        });

    </script>

</x-app-layout>

