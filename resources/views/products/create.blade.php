<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center gap-3">

            <a
                href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center
                       h-9 w-9 rounded-lg
                       text-gray-500
                       hover:text-gray-700
                       hover:bg-gray-100
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
                <h2 class="font-semibold text-lg sm:text-xl text-gray-800">
                    {{ __('Add New Product') }}
                </h2>

                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                    {{ __('Create the product and record its opening inventory.') }}
                </p>
            </div>

        </div>

    </x-slot>


    <div class="py-5 sm:py-8">

        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">

            <form
                method="POST"
                action="{{ route('products.store') }}"
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
                                        onclick="generateSKU()"
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

                            <div>

                                <label
                                    for="opening_stock_cost"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ __('Opening Stock Unit Cost') }}

                                    <span class="text-red-500">
                                        *
                                    </span>
                                </label>

                                <div class="relative mt-1.5">

                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3
                                                 text-xs font-medium text-gray-500">
                                        RWF
                                    </span>

                                    <input
                                        type="number"
                                        name="opening_stock_cost"
                                        id="opening_stock_cost"
                                        value="{{ old('opening_stock_cost') }}"
                                        min="0"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-300 pl-14
                                               focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="0.00"
                                    >

                                </div>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('What one unit of the existing stock originally cost.') }}
                                </p>

                                @error('opening_stock_cost')
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
                                <span class="text-red-500">*</span>
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

                        {{ __('Create Product') }}

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================
         JAVASCRIPT
    ================================================================= --}}

    <script>

        function generateSKU() {

            const nameInput = document.getElementById('name');
            const skuInput = document.getElementById('sku');

            const name = nameInput.value.trim();

            let prefix = 'PRD';

            if (name) {

                prefix =
                    name
                        .replace(/[^a-zA-Z]/g, '')
                        .substring(0, 3)
                        .toUpperCase() || 'PRD';

            }

            const random =
                Math.floor(10000 + Math.random() * 90000);

            skuInput.value =
                prefix + '-' + random;
        }


        document
            .getElementById('name')
            .addEventListener('blur', function () {

                const skuInput =
                    document.getElementById('sku');

                if (!skuInput.value.trim()) {
                    generateSKU();
                }

            });


        /*
        |--------------------------------------------------------------------------
        | Opening Stock Value Calculator
        |--------------------------------------------------------------------------
        */

        function calculateOpeningStockValue() {

            const quantity =
                parseFloat(
                    document.getElementById('opening_stock').value
                ) || 0;

            const cost =
                parseFloat(
                    document.getElementById('opening_stock_cost').value
                ) || 0;

            const value = quantity * cost;

            document.getElementById('opening-stock-value').textContent =
                'RWF ' +
                value.toLocaleString('en-RW', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
        }


        document
            .getElementById('opening_stock')
            .addEventListener(
                'input',
                calculateOpeningStockValue
            );


        document
            .getElementById('opening_stock_cost')
            .addEventListener(
                'input',
                calculateOpeningStockValue
            );


        calculateOpeningStockValue();

    </script>

</x-app-layout>