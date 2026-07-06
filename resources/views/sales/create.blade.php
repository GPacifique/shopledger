<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center flex-wrap gap-1">
            <a href="{{ route('sales.index') }}" class="mr-2 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Record New Sale') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}"
                  x-data="saleForm"
                  @submit="onSubmit"
                  data-i18n-min-item="{{ __('At least one item is required') }}"
                  data-i18n-stock-error="{{ __('Cannot complete sale: One or more items exceed available stock. Please adjust quantities.') }}">
                @csrf

                <!-- Sale Date / Payment Method / Customer -->
                <div class="bg-white shadow-sm rounded-lg mb-6 p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <x-form.field name="sale_date" :label="__('Sale Date')" required>
                            <input type="date" name="sale_date" id="sale_date"
                                   value="{{ old('sale_date', date('Y-m-d')) }}" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 text-base">
                        </x-form.field>

                        <x-form.field name="payment_method" :label="__('Payment Method')" required>
                            <select name="payment_method" id="payment_method" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 text-base">
                                @foreach(\App\Models\Sale::PAYMENT_METHODS as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form.field>

                        <x-form.field name="customer_id" :label="__('Customer')">
                            <select name="customer_id" id="customer_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 text-base">
                                <option value="">{{ __('Walk-in customer') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form.field>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white shadow-sm rounded-lg mb-6">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Sale Items') }}</h3>
                        <button type="button" @click="addItem()"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200">
                            {{ __('Add Item') }}
                        </button>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4">
                        <template x-for="(item, index) in items" :key="item.uid">
                            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">

                                <div class="lg:col-span-4 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                    <label class="text-sm font-medium text-gray-700 sm:w-24 sm:flex-shrink-0">{{ __('Product') }} *</label>
                                    <select :name="`items[${index}][product_id]`" required
                                            x-model="item.productId" @change="onProductChange(item)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 text-base">
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-price="{{ $product->selling_price }}"
                                                    data-stock="{{ $product->stock }}"
                                                    data-name="{{ $product->name }}">
                                                {{ $product->name }} (Stock: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="lg:col-span-3 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                    <label class="text-sm font-medium text-gray-700 sm:w-12 sm:flex-shrink-0">{{ __('Quantity') }} *</label>
                                    <div class="w-full">
                                        <input type="number" :name="`items[${index}][quantity]`" min="1" required
                                               x-model.number="item.quantity"
                                               :class="item.quantity > item.stock ? 'border-red-500 ring-red-500 bg-red-50' : ''"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 text-base">
                                        <p class="text-xs mt-1"
                                           :class="item.quantity > item.stock ? 'text-red-600 font-semibold' : 'text-gray-500'"
                                           x-show="item.productId" x-text="`Available: ${item.stock}`"></p>
                                    </div>
                                </div>

                                <div class="lg:col-span-3 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                    <label class="text-sm font-medium text-gray-700 sm:w-12 sm:flex-shrink-0">{{ __('Unit Price') }} (RWF) *</label>
                                    <input type="number" :name="`items[${index}][unit_price]`" min="0" step="1" required
                                           x-model.number="item.unitPrice"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 text-base">
                                </div>

                                <div class="lg:col-span-1 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                    <label class="text-sm font-medium text-gray-700 sm:w-12 sm:flex-shrink-0">{{ __('Line Total') }}</label>
                                    <div class="text-lg font-semibold text-gray-900"
                                         x-text="formatCurrency(item.quantity * item.unitPrice)"></div>
                                </div>

                                <div class="lg:col-span-1 flex items-center justify-end lg:justify-start">
                                    <button type="button" @click="removeItem(index)"
                                            class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded inline-flex items-center gap-1">
                                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="lg:hidden text-sm">{{ __('Remove') }}</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        @error('items')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-white shadow-sm rounded-lg mb-6 p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-lg">
                        <span class="font-medium text-gray-900">{{ __('Total Amount') }}:</span>
                        <span class="text-2xl font-bold text-green-600" x-text="formatCurrency(grandTotal)"></span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                    <a href="{{ route('sales.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        {{ __('Record Sale') }}
                    </button>
                </div>

                <!-- Stock toast -->
                <div x-show="toast.visible" x-transition
                     class="fixed top-4 right-4 left-4 sm:left-auto z-50 sm:max-w-sm">
                    <div class="bg-red-600 text-white px-4 sm:px-6 py-4 rounded-lg shadow-xl flex items-center space-x-3">
                        <div class="min-w-0">
                            <p class="font-semibold">{{ __('Insufficient Stock!') }}</p>
                            <p class="text-sm text-red-100 break-words" x-text="toast.message"></p>
                        </div>
                        <button type="button" @click="toast.visible = false" class="ml-2 flex-shrink-0 text-red-200 hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>