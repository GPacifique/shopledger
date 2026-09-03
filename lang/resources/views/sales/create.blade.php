<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}"
               class="inline-flex items-center justify-center h-9 w-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Record New Sale') }}
                </h2>
                <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">{{ __('Add items, set payment details, and confirm the sale') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
      action="{{ route('sales.store') }}"
      x-data="saleForm"
      @submit="onSubmit"
      data-payment-status="{{ old('payment_status', 'paid') }}"
      data-i18n-min-item="{{ __('At least one item is required') }}"
      data-i18n-stock-error="{{ __('Cannot complete sale: One or more items exceed available stock. Please adjust quantities.') }}"
      class="pb-28 lg:pb-0">
    @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- Main column -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Sale Date / Payment Method / Payment Status / Customer -->
                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl">
                            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900 tracking-wide">{{ __('Sale Details') }}</h3>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <x-form.field name="sale_date" :label="__('Sale Date')" required>
                                        <input type="date" name="sale_date" id="sale_date"
                                               value="{{ old('sale_date', date('Y-m-d')) }}" required
                                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors">
                                    </x-form.field>

                                    <x-form.field name="customer_id" :label="__('Customer')">
                                        <select name="customer_id" id="customer_id"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors">
                                            <option value="">{{ __('Walk-in customer') }}</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </x-form.field>

                                    <x-form.field name="payment_method" :label="__('Payment Method')" required>
                                        <select name="payment_method" id="payment_method" required
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors">
                                            @foreach(\App\Models\Sale::PAYMENT_METHODS as $value => $label)
                                                <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </x-form.field>

                                   <x-form.field name="payment_status" :label="__('Payment Status')" required>
    @php
        $paymentStatuses = [
            'paid' => __('paid'),
            'partial' => __('partial'),
            'unpaid' => __('unpaid'),
        ];
    @endphp

    <select
        name="payment_status"
        id="payment_status"
        required
        x-model="paymentStatus"
        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors"
    >
        @foreach($paymentStatuses as $value => $label)
            <option value="{{ $value }}">
                {{ $label }}
            </option>
        @endforeach
    </select>
</x-form.field>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl">
                            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex justify-between items-center gap-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 tracking-wide">{{ __('Sale Items') }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5" x-show="items.length" x-text="`${items.length} ${items.length === 1 ? '{{ __('item') }}' : '{{ __('items') }}'}`"></p>
                                </div>
                                <button type="button" @click="addItem()"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-sm transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    {{ __('Add Item') }}
                                </button>
                            </div>

                            <!-- Column headers (desktop only) -->
                            <div class="hidden lg:grid grid-cols-12 gap-4 px-6 pt-4 text-xs font-medium text-gray-400 uppercase tracking-wide">
                                <div class="col-span-5">{{ __('Product') }}</div>
                                <div class="col-span-2">{{ __('Quantity') }}</div>
                                <div class="col-span-2">{{ __('Unit Price') }}</div>
                                <div class="col-span-2">{{ __('Line Total') }}</div>
                                <div class="col-span-1"></div>
                            </div>

                            <div class="p-4 sm:p-6 space-y-3">
                                <template x-for="(item, index) in items" :key="item.uid">
                                    <div class="group relative bg-gray-50/70 hover:bg-gray-50 border border-gray-100 rounded-xl p-4 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 lg:items-center transition-colors"
                                         :class="item.quantity > item.stock && item.productId ? 'ring-1 ring-red-200 bg-red-50/60' : ''">

                                        <div class="lg:col-span-5">
                                            <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Product') }} *</label>
                                            <select :name="`items[${index}][product_id]`" required
                                                    x-model="item.productId" @change="onProductChange(item)"
                                                    class="mt-1 lg:mt-0 w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors">
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

                                        <div class="lg:col-span-2">
                                            <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Quantity') }} *</label>
                                            <input type="number"
                                                   :name="`items[${index}][quantity]`"
                                                   min="0.00001"
                                                   step="0.00001"
                                                   required
                                                   x-model.number="item.quantity"
                                                   :class="item.quantity > item.stock ? 'border-red-400 ring-1 ring-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500'"
                                                   class="mt-1 lg:mt-0 w-full rounded-lg shadow-sm py-2.5 px-3 text-base transition-colors">
                                            <p class="text-xs mt-1 flex items-center gap-1"
                                               :class="item.quantity > item.stock ? 'text-red-600 font-medium' : 'text-gray-400'"
                                               x-show="item.productId">
                                                <svg x-show="item.quantity > item.stock" class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                                </svg>
                                                <span x-text="`{{ __('Available') }}: ${item.stock}`"></span>
                                            </p>
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Unit Price') }} (RWF) *</label>
                                            <input type="number" :name="`items[${index}][unit_price]`" min="0" step="1" required
                                                   x-model.number="item.unitPrice"
                                                   class="mt-1 lg:mt-0 w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors">
                                        </div>

                                        <div class="lg:col-span-2 flex items-center justify-between lg:block pt-1 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                            <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Line Total') }}</label>
                                            <div class="text-base font-semibold text-gray-900 tabular-nums" x-text="formatCurrency(item.quantity * item.unitPrice)"></div>
                                        </div>

                                        <div class="lg:col-span-1 flex lg:justify-center">
                                            <button type="button" @click="removeItem(index)"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="h-4.5 w-4.5 flex-shrink-0" style="height:1.1rem;width:1.1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span class="text-sm lg:hidden">{{ __('Remove') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty state -->
                                <div x-show="!items.length" class="text-center py-10 px-4">
                                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                                    </svg>
                                    <p class="mt-3 text-sm text-gray-500">{{ __('No items added yet') }}</p>
                                    <button type="button" @click="addItem()"
                                            class="mt-3 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                                        {{ __('Add your first item') }} &rarr;
                                    </button>
                                </div>

                                @error('items')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Summary sidebar -->
                    <div class="lg:col-span-1">
                        <div class="hidden lg:block sticky top-6 space-y-4">
                            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6">
                                <h3 class="text-sm font-semibold text-gray-900 tracking-wide mb-4">{{ __('Summary') }}</h3>
                                <div class="space-y-2 text-sm text-gray-500 mb-4">
                                    <div class="flex justify-between">
                                        <span x-text="`${items.length} ${items.length === 1 ? '{{ __('item') }}' : '{{ __('items') }}'}`"></span>
                                        <span x-text="formatCurrency(grandTotal)"></span>
                                    </div>
                                </div>
                                <div class="border-t border-gray-100 pt-4 flex justify-between items-baseline">
                                    <span class="text-sm font-medium text-gray-900">{{ __('Total Amount') }}</span>
                                    <span class="text-2xl font-bold text-emerald-600 tabular-nums" x-text="formatCurrency(grandTotal)"></span>
                                </div>

                                <div class="mt-6 space-y-2">
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition-colors">
                                        {{ __('Record Sale') }}
                                    </button>
                                    <a href="{{ route('sales.index') }}"
                                       class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile sticky action bar -->
                <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3 shadow-[0_-4px_12px_-2px_rgba(0,0,0,0.06)]">
                    <div class="flex items-center justify-between gap-3 max-w-7xl mx-auto">
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 leading-none">{{ __('Total Amount') }}</p>
                            <p class="text-lg font-bold text-emerald-600 tabular-nums leading-tight" x-text="formatCurrency(grandTotal)"></p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('sales.index') }}"
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition-colors">
                                {{ __('Record Sale') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stock toast -->
                <div x-show="toast.visible"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-end="opacity-0"
                     class="fixed top-4 right-4 left-4 sm:left-auto z-50 sm:max-w-sm">
                    <div class="bg-red-600 text-white px-4 sm:px-5 py-4 rounded-xl shadow-xl flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-sm">{{ __('Insufficient Stock!') }}</p>
                            <p class="text-sm text-red-100 break-words mt-0.5" x-text="toast.message"></p>
                        </div>
                        <button type="button" @click="toast.visible = false" class="flex-shrink-0 text-red-200 hover:text-white">
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