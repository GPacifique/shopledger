{{-- resources/views/products/create.blade.php --}}
<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center gap-3">

            <a
                href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center
                       h-9 w-9 rounded-lg
                       text-gray-500
                       hover:text-gray-700
                       hover:bg-red-100
                       transition"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>
            </a>

            <div>
                <h2 class="font-semibold text-lg sm:text-3xl text-blue-800">
                    {{ __('Add New Product') }}
                </h2>

                <p class="text-xs sm:text-sm text-red-500 mt-0.5">
                    {{ __('Create the product and record its opening inventory.') }}
                </p>
            </div>

        </div>

    </x-slot>


    <div class="py-5 sm:py-8">

        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">

            {{-- enctype is REQUIRED for the image upload to reach the server --}}
            <form
                method="POST"
                action="{{ route('products.store') }}"
                enctype="multipart/form-data"
                class="space-y-5"
            >

                @csrf


                {{-- ==========================================================
                     BASIC INFORMATION
                =========================================================== --}}

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-600
                                        flex items-center justify-center">

                                <svg class="h-5 w-5" fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>

                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    {{ __('Product Information') }}
                                </h3>

                                <p class="text-xs text-gray-500">
                                    {{ __('Basic information used to identify the product.') }}
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="p-4 sm:p-6 space-y-5">

                        {{-- Product Name --}}

                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700"
                            >
                                {{ __('Product Name') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                autocomplete="off"
                                placeholder="{{ __('e.g. Wireless Mouse') }}"
                                class="mt-1.5 block w-full rounded-lg border-gray-300
                                       focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        {{-- SKU + Barcode --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>

                                <label
                                    for="sku"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('SKU') }}
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="mt-1.5 flex">

                                    <input
                                        type="text"
                                        name="sku"
                                        id="sku"
                                        value="{{ old('sku') }}"
                                        required
                                        autocomplete="off"
                                        placeholder="{{ __('Auto-generated') }}"
                                        class="block min-w-0 flex-1 rounded-l-lg
                                               border-gray-300
                                               focus:border-indigo-500
                                               focus:ring-indigo-500"
                                    >

                                    <button
                                        type="button"
                                        id="generate-sku-btn"
                                        class="inline-flex items-center gap-1.5 px-3
                                               rounded-r-lg border border-l-0
                                               border-gray-300 bg-gray-50
                                               text-gray-600 hover:bg-gray-100"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                            />
                                        </svg>

                                        <span class="hidden sm:inline">
                                            {{ __('Generate') }}
                                        </span>
                                    </button>

                                </div>

                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            <div>

                                <label
                                    for="barcode"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Barcode') }}
                                </label>

                                <input
                                    type="text"
                                    name="barcode"
                                    id="barcode"
                                    value="{{ old('barcode') }}"
                                    autocomplete="off"
                                    placeholder="{{ __('Optional') }}"
                                    class="mt-1.5 block w-full rounded-lg border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>


                        {{-- Category + Supplier --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>

                                <label
                                    for="category_id"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Category') }}
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    name="category_id"
                                    id="category_id"
                                    required
                                    class="mt-1.5 block w-full rounded-lg border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                    <option value="">
                                        {{ __('Select category') }}
                                    </option>

                                    @foreach($categories as $category)

                                        <option
                                            value="{{ $category->id }}"
                                            @selected(old('category_id') == $category->id)
                                        >
                                            {{ $category->name }}
                                        </option>

                                    @endforeach

                                </select>

                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            <div>

                                <label
                                    for="supplier_id"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Supplier') }}
                                </label>

                                <select
                                    name="supplier_id"
                                    id="supplier_id"
                                    class="mt-1.5 block w-full rounded-lg border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                    <option value="">
                                        {{ __('Select supplier') }}
                                    </option>

                                    @foreach($suppliers as $supplier)

                                        <option
                                            value="{{ $supplier->id }}"
                                            @selected(old('supplier_id') == $supplier->id)
                                        >
                                            {{ $supplier->name }}
                                        </option>

                                    @endforeach

                                </select>

                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>


                        {{-- Description --}}

                        <div>

                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700"
                            >
                                {{ __('Description') }}
                            </label>

                            <textarea
                                name="description"
                                id="description"
                                rows="3"
                                placeholder="{{ __('Optional product description') }}"
                                class="mt-1.5 block w-full rounded-lg border-gray-300
                                       focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('description') }}</textarea>

                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- ==========================================================
                     PRODUCT IMAGE
                =========================================================== --}}

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100">

                        <h3 class="font-semibold text-gray-900">
                            {{ __('Product Image') }}
                        </h3>

                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ __('Optional. JPG, JPEG or PNG, up to 2 MB.') }}
                        </p>

                    </div>


                    <div class="p-4 sm:p-6">

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                            {{-- Preview --}}
                            <div
                                class="relative h-28 w-28 shrink-0 overflow-hidden
                                       rounded-xl border border-dashed border-gray-300
                                       bg-gray-50 flex items-center justify-center"
                            >

                                <img
                                    id="product-image-preview"
                                    src=""
                                    alt="{{ __('Product image preview') }}"
                                    class="hidden h-full w-full object-cover"
                                >

                                <svg
                                    id="product-image-placeholder"
                                    class="h-10 w-10 text-gray-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.6"/>
                                    <circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.6"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.6" d="m21 15-5-5L5 21"/>
                                </svg>

                            </div>


                            <div class="min-w-0 flex-1">

                                <label
                                    for="product_image"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Choose image') }}
                                </label>

                                <input
                                    type="file"
                                    name="product_image"
                                    id="product_image"
                                    accept="image/png,image/jpeg"
                                    class="mt-1.5 block w-full cursor-pointer rounded-lg
                                           border border-gray-300 bg-white text-sm text-gray-600
                                           file:mr-3 file:cursor-pointer file:border-0
                                           file:bg-indigo-50 file:px-4 file:py-2
                                           file:text-sm file:font-medium file:text-indigo-700
                                           hover:file:bg-indigo-100"
                                >

                                <p
                                    id="product-image-error"
                                    class="mt-1 hidden text-sm text-red-600"
                                    role="alert"
                                ></p>

                                @error('product_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <button
                                    type="button"
                                    id="product-image-clear"
                                    class="mt-2 hidden text-xs font-medium text-red-600 hover:text-red-700"
                                >
                                    {{ __('Remove selected image') }}
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================================
                     PRICING
                =========================================================== --}}

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100">

                        <h3 class="font-semibold text-gray-900">
                            {{ __('Pricing') }}
                        </h3>

                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ __('Set the current cost and selling price.') }}
                        </p>

                    </div>


                    <div class="p-4 sm:p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Buying price --}}

                            <div>

                                <label
                                    for="buying_price"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Current Buying Price') }}
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative mt-1.5">

                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3
                                                 text-xs font-medium text-gray-500">
                                        RWF
                                    </span>

                                    <input
                                        type="number"
                                        name="buying_price"
                                        id="buying_price"
                                        value="{{ old('buying_price') }}"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="block w-full rounded-lg border-gray-300 pl-14
                                               focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="0.00"
                                    >

                                </div>

                                @error('buying_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- Selling price --}}

                            <div>

                                <label
                                    for="selling_price"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Selling Price') }}
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative mt-1.5">

                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3
                                                 text-xs font-medium text-gray-500">
                                        RWF
                                    </span>

                                    <input
                                        type="number"
                                        name="selling_price"
                                        id="selling_price"
                                        value="{{ old('selling_price') }}"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="block w-full rounded-lg border-gray-300 pl-14
                                               focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="0.00"
                                    >

                                </div>

                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================================
                     OPENING STOCK
                =========================================================== --}}

                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-6 py-4 border-b border-indigo-100 bg-indigo-50/50">

                        <div class="flex items-start gap-3">

                            <div class="h-9 w-9 shrink-0 rounded-lg bg-indigo-100
                                        text-indigo-600 flex items-center justify-center">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"
                                    />
                                </svg>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    {{ __('Opening Stock') }}
                                </h3>

                                <p class="text-xs sm:text-sm text-gray-600 mt-0.5">
                                    {{ __('Record the inventory already available when this product is introduced.') }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-4 sm:p-6 space-y-5">

                        <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 sm:p-4">

                            <div class="flex gap-3">

                                <svg
                                    class="h-5 w-5 text-blue-600 shrink-0 mt-0.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                                    />
                                </svg>

                                <p class="text-sm text-blue-800">
                                    <strong>{{ __('Important:') }}</strong>
                                    {{ __('Opening stock is not a purchase. It is the stock you already have when starting to use the system. Its cost is recorded so that future sales calculate the correct gross profit.') }}
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Opening quantity --}}

                            <div>

                                <label
                                    for="opening_stock"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Opening Stock Quantity') }}
                                </label>

                                <input
                                    type="number"
                                    name="opening_stock"
                                    id="opening_stock"
                                    value="{{ old('opening_stock', 0) }}"
                                    min="0"
                                    step="0.01"
                                    class="mt-1.5 block w-full rounded-lg border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Quantity physically available at the beginning.') }}
                                </p>

                                @error('opening_stock')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- Opening cost --}}
                            {{-- Field name MUST be opening_unit_cost to match ProductController@store.
                                 Leave empty to use the buying price. --}}

                            <div>

                                <label
                                    for="opening_unit_cost"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Opening Stock Unit Cost') }}
                                </label>

                                <div class="relative mt-1.5">

                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3
                                                 text-xs font-medium text-gray-500">
                                        RWF
                                    </span>

                                    <input
                                        type="number"
                                        name="opening_unit_cost"
                                        id="opening_unit_cost"
                                        value="{{ old('opening_unit_cost') }}"
                                        min="0"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-300 pl-14
                                               focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="{{ __('Defaults to buying price') }}"
                                    >

                                </div>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('What one unit of the existing stock originally cost. If left empty, the buying price is used.') }}
                                </p>

                                @error('opening_unit_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>


                        {{-- Opening stock value --}}

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                            <div class="flex items-center justify-between gap-4">

                                <div>

                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                        {{ __('Opening Stock Value') }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ __('Quantity × Unit Cost') }}
                                    </p>

                                </div>

                                <div class="text-right">

                                    <p
                                        id="opening-stock-value"
                                        class="text-lg sm:text-xl font-bold text-gray-900"
                                    >
                                        RWF 0
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Opening date --}}

                        <div>

                            <label
                                for="opening_stock_date"
                                class="block text-sm font-medium text-gray-700"
                            >
                                {{ __('Opening Stock Date') }}
                            </label>

                            <input
                                type="date"
                                name="opening_stock_date"
                                id="opening_stock_date"
                                value="{{ old('opening_stock_date', now()->format('Y-m-d')) }}"
                                class="mt-1.5 block w-full rounded-lg border-gray-300
                                       focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Date on which this opening inventory was established.') }}
                            </p>

                            @error('opening_stock_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- ==========================================================
                     STOCK CONTROL
                =========================================================== --}}

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100">

                        <h3 class="font-semibold text-gray-900">
                            {{ __('Stock Control') }}
                        </h3>

                    </div>


                    <div class="p-4 sm:p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>

                                <label
                                    for="minimum_stock"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Minimum Stock Level') }}
                                </label>

                                <input
                                    type="number"
                                    name="minimum_stock"
                                    id="minimum_stock"
                                    value="{{ old('minimum_stock', 0) }}"
                                    min="0"
                                    step="0.01"
                                    class="mt-1.5 block w-full rounded-lg border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('The system will identify stock below this level as low stock.') }}
                                </p>

                                @error('minimum_stock')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            <div>

                                <label
                                    for="expiry_date"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Expiry Date') }}
                                </label>

                                <input
                                    type="date"
                                    name="expiry_date"
                                    id="expiry_date"
                                    value="{{ old('expiry_date') }}"
                                    class="mt-1.5 block w-full rounded-lg border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Leave empty if the product does not expire.') }}
                                </p>

                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==========================================================
                     STATUS
                =========================================================== --}}

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="p-4 sm:p-6">

                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700"
                        >
                            {{ __('Status') }}
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="mt-1.5 block w-full rounded-lg border-gray-300
                                   focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option
                                value="active"
                                @selected(old('status', 'active') === 'active')
                            >
                                {{ __('Active') }}
                            </option>

                            <option
                                value="inactive"
                                @selected(old('status') === 'inactive')
                            >
                                {{ __('Inactive') }}
                            </option>
                        </select>

                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                </div>


                {{-- ==========================================================
                     ACTIONS
                =========================================================== --}}

                <div class="flex flex-col-reverse sm:flex-row
                            items-stretch sm:items-center
                            justify-end gap-3
                            pt-2 pb-6">

                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center justify-center
                               px-5 py-2.5
                               rounded-lg
                               border border-gray-300
                               bg-white
                               text-sm font-medium text-gray-700
                               hover:bg-gray-50
                               transition"
                    >
                        {{ __('Cancel') }}
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center
                               px-5 py-2.5
                               rounded-lg
                               bg-indigo-600
                               text-sm font-semibold text-white
                               shadow-sm
                               hover:bg-indigo-700
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-500
                               focus:ring-offset-2
                               transition"
                    >

                        <svg
                            class="h-4 w-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        {{ __('Save') }}

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================
         JAVASCRIPT (CSP-compatible: no inline event-handler attributes)
    ================================================================= --}}

    <script nonce="{{ Vite::cspNonce() }}">

        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | SKU generator
            |--------------------------------------------------------------------------
            */

            const nameInput = document.getElementById('name');
            const skuInput = document.getElementById('sku');
            const skuButton = document.getElementById('generate-sku-btn');

            function generateSKU() {

                const name = nameInput.value.trim();

                let prefix = 'PRD';

                if (name) {
                    prefix =
                        name
                            .replace(/[^a-zA-Z]/g, '')
                            .substring(0, 3)
                            .toUpperCase() || 'PRD';
                }

                const random = Math.floor(10000 + Math.random() * 90000);

                skuInput.value = prefix + '-' + random;
            }

            skuButton.addEventListener('click', generateSKU);

            nameInput.addEventListener('blur', function () {
                if (!skuInput.value.trim()) {
                    generateSKU();
                }
            });


            /*
            |--------------------------------------------------------------------------
            | Opening stock value calculator
            |--------------------------------------------------------------------------
            |
            | If no opening unit cost is typed, the buying price is used,
            | exactly like the controller does.
            |
            */

            const openingStock = document.getElementById('opening_stock');
            const openingUnitCost = document.getElementById('opening_unit_cost');
            const buyingPrice = document.getElementById('buying_price');
            const valueLabel = document.getElementById('opening-stock-value');

            function calculateOpeningStockValue() {

                const quantity = parseFloat(openingStock.value) || 0;

                const costRaw = openingUnitCost.value.trim();

                const cost = costRaw !== ''
                    ? (parseFloat(costRaw) || 0)
                    : (parseFloat(buyingPrice.value) || 0);

                const value = quantity * cost;

                valueLabel.textContent =
                    'RWF ' +
                    value.toLocaleString('en-RW', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
            }

            openingStock.addEventListener('input', calculateOpeningStockValue);
            openingUnitCost.addEventListener('input', calculateOpeningStockValue);
            buyingPrice.addEventListener('input', calculateOpeningStockValue);

            calculateOpeningStockValue();


            /*
            |--------------------------------------------------------------------------
            | Product image: validation + live preview
            |--------------------------------------------------------------------------
            |
            | The server validates again (jpg/jpeg/png, max 2 MB); this just gives
            | instant feedback. If the preview does not show, add blob: to the
            | img-src directive of your Content-Security-Policy.
            |
            */

            const imageInput = document.getElementById('product_image');
            const imagePreview = document.getElementById('product-image-preview');
            const imagePlaceholder = document.getElementById('product-image-placeholder');
            const imageError = document.getElementById('product-image-error');
            const imageClear = document.getElementById('product-image-clear');

            const MAX_IMAGE_BYTES = 2 * 1024 * 1024;
            const ALLOWED_TYPES = ['image/jpeg', 'image/png'];

            let previewUrl = null;

            function resetImage() {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                imageInput.value = '';
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
                imagePlaceholder.classList.remove('hidden');
                imageClear.classList.add('hidden');
            }

            function showImageError(message) {
                imageError.textContent = message;
                imageError.classList.remove('hidden');
            }

            imageInput.addEventListener('change', function () {

                imageError.classList.add('hidden');
                imageError.textContent = '';

                const file = imageInput.files && imageInput.files[0];

                if (!file) {
                    resetImage();
                    return;
                }

                if (!ALLOWED_TYPES.includes(file.type)) {
                    resetImage();
                    showImageError(@json(__('Only JPG, JPEG or PNG images are allowed.')));
                    return;
                }

                if (file.size > MAX_IMAGE_BYTES) {
                    resetImage();
                    showImageError(@json(__('The image must not be larger than 2 MB.')));
                    return;
                }

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }

                previewUrl = URL.createObjectURL(file);

                imagePreview.src = previewUrl;
                imagePreview.classList.remove('hidden');
                imagePlaceholder.classList.add('hidden');
                imageClear.classList.remove('hidden');
            });

            imageClear.addEventListener('click', function () {
                imageError.classList.add('hidden');
                resetImage();
            });

        });

    </script>

</x-app-layout>