@php
    // Product catalog for instant, in-browser search and scanning.
    // Barcode/SKU columns are optional: whichever of these exist on the product are used as scan codes.
    $posProducts = $products->map(function ($p) {
        $attrs = $p->getAttributes();

        return [
            'id'    => $p->id,
            'name'  => (string) $p->name,
            'price' => (float) $p->selling_price,
            'stock' => (float) $p->stock,
            'codes' => collect(['barcode', 'sku', 'code'])
                ->map(fn ($key) => $attrs[$key] ?? null)
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->map(fn ($v) => (string) $v)
                ->values()
                ->all(),
        ];
    })->values()->all();

    $posData = [
        'products'      => $posProducts,
        'old'           => array_values((array) old('items', [])),
        'paymentStatus' => old('payment_status', 'paid'),
        'labels'        => [
            'minItem'    => __('At least one item is required'),
            'stockError' => __('Cannot complete sale: One or more items exceed available stock. Please adjust quantities.'),
            'badQty'     => __('Every item needs a quantity greater than zero.'),
            'notFound'   => __('No product found for "{term}"'),
            'outOfStock' => __('{name} is out of stock'),
            'onlyLeft'   => __('Only {qty} of {name} in stock'),
            'problem'    => __('Cannot add item'),
            'added'      => __('Added'),
            'available'  => __('Available'),
            'item'       => __('item'),
            'items'      => __('items'),
        ],
    ];

    $paymentStatuses = [
        'paid'    => __('Paid'),
        'partial' => __('Partial'),
        'unpaid'  => __('Unpaid'),
    ];
@endphp

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
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Record New Sale') }}</h2>
                <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">{{ __('Scan a barcode or type a product name to add items') }}</p>
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

            <form method="POST" action="{{ route('sales.store') }}"
                  x-data="posSale"
                  @submit="onSubmit($event)"
                  @keydown.window="hotkeys($event)"
                  class="pb-28 lg:pb-0">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    {{-- ================= MAIN COLUMN: scan + cart ================= --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Scan / search --}}
                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-4 sm:p-5">
                            <div class="relative" @click.outside="open = false">
                                <label for="pos-search" class="sr-only">{{ __('Scan barcode or search product') }}</label>

                                <div class="relative">
                                    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6v12M7 6v12M11 6v12M14 6v12M18 6v12M20 6v12"/>
                                    </svg>

                                    <input id="pos-search" x-ref="search" type="text"
                                           x-model="query"
                                           @input="onInput()"
                                           @focus="if (query || browse) open = true"
                                           @keydown.enter.prevent="enter()"
                                           @keydown.arrow-down.prevent="move(1)"
                                           @keydown.arrow-up.prevent="move(-1)"
                                           @keydown.escape="clear()"
                                           role="combobox"
                                           :aria-expanded="open && matches.length > 0"
                                           aria-controls="pos-results"
                                           autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="done" autofocus
                                           placeholder="{{ __('Scan barcode or type product name, then press Enter') }}"
                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 pl-12 pr-28 py-3.5 text-lg transition-colors">

                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                        <span x-show="pendingQty !== 1" x-cloak
                                              class="rounded-md bg-emerald-100 px-2 py-1 text-sm font-semibold text-emerald-700 tabular-nums"
                                              x-text="'×' + fmtQty(pendingQty)"></span>

                                        <button type="button" @click="toggleSound()"
                                                :title="sound ? '{{ __('Sound on') }}' : '{{ __('Sound off') }}'"
                                                :aria-pressed="sound"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                            <svg x-show="sound" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707A1 1 0 0112 5.586v12.828a1 1 0 01-1.707.707L5.586 15z"/>
                                            </svg>
                                            <svg x-show="!sound" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707A1 1 0 0112 5.586v12.828a1 1 0 01-1.707.707L5.586 15zM17 14l4-4m0 4l-4-4"/>
                                            </svg>
                                        </button>

                                        <button type="button" @click="toggleBrowse()"
                                                title="{{ __('Browse all products') }}"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                            <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': browse }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Results --}}
                                <ul id="pos-results" x-ref="list" role="listbox"
                                    x-show="open && matches.length" x-cloak
                                    class="absolute left-0 right-0 z-30 mt-2 max-h-80 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-xl">
                                    <template x-for="(p, i) in matches" :key="p.id">
                                        <li role="option" :aria-selected="i === highlight" :data-active="i === highlight"
                                            @mousedown.prevent
                                            @mousemove="highlight = i"
                                            @click="add(p, pendingQty)"
                                            class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-3 py-2.5"
                                            :class="i === highlight ? 'bg-emerald-50' : ''">
                                            <div class="min-w-0">
                                                <p class="truncate font-medium text-gray-900" x-text="p.name"></p>
                                                <p class="truncate text-xs text-gray-400" x-show="p.codes.length" x-text="p.codes[0]"></p>
                                            </div>
                                            <div class="flex-shrink-0 text-right">
                                                <p class="text-sm font-semibold text-gray-900 tabular-nums" x-text="formatCurrency(p.price)"></p>
                                                <p class="text-xs tabular-nums" :class="p.stock <= 0 ? 'text-red-600 font-medium' : 'text-gray-400'"
                                                   x-text="p.stock <= 0 ? '{{ __('Out of stock') }}' : '{{ __('Stock') }}: ' + fmtQty(p.stock)"></p>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-x-4 gap-y-1 text-xs text-gray-400">
                                <p aria-live="polite" class="font-medium text-emerald-700" x-show="lastAdded" x-text="lastAdded"></p>
                                <p class="hidden sm:block">
                                    <kbd class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 font-sans">Enter</kbd> {{ __('adds 1') }}
                                    · <kbd class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 font-sans">3*name</kbd> {{ __('adds 3') }}
                                    · <kbd class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 font-sans">F2</kbd> {{ __('search') }}
                                    · <kbd class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 font-sans">F9</kbd> {{ __('record sale') }}
                                </p>
                            </div>
                        </div>

                        {{-- Cart --}}
                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl">
                            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex justify-between items-center gap-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 tracking-wide">{{ __('Sale Items') }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5" x-show="items.length" x-text="itemsLabel"></p>
                                </div>
                                <button type="button" x-show="items.length" x-cloak
                                        @click="if (confirm('{{ __('Remove all items?') }}')) { items = []; focusSearch(); }"
                                        class="text-sm font-medium text-red-600 hover:text-red-700">
                                    {{ __('Clear all') }}
                                </button>
                            </div>

                            <div class="hidden lg:grid grid-cols-12 gap-4 px-6 pt-4 text-xs font-medium text-gray-400">
                                <div class="col-span-4">{{ __('Product') }}</div>
                                <div class="col-span-3">{{ __('Quantity') }}</div>
                                <div class="col-span-2">{{ __('Unit Price') }}</div>
                                <div class="col-span-2">{{ __('Line Total') }}</div>
                                <div class="col-span-1"></div>
                            </div>

                            <div class="p-4 sm:p-6 space-y-3">
                                <template x-for="(item, index) in items" :key="item.uid">
                                    <div :id="'line-' + item.uid"
                                         class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 lg:items-center rounded-xl border p-3 sm:p-4 transition-colors duration-500"
                                         :class="over(item) ? 'border-red-200 bg-red-50/60' : (flashUid === item.uid ? 'border-emerald-300 bg-emerald-50' : 'border-gray-100 bg-gray-50/70')">

                                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.productId">
                                        <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                                        <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.unitPrice">

                                        {{-- Name --}}
                                        <div class="lg:col-span-4 flex items-start justify-between gap-2 min-w-0">
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                                <p class="text-xs mt-0.5" :class="over(item) ? 'text-red-600 font-medium' : 'text-gray-400'"
                                                   x-text="`${labels.available}: ${fmtQty(item.stock)}`"></p>
                                            </div>
                                            <button type="button" @click="removeItem(index)" aria-label="{{ __('Remove') }}"
                                                    class="lg:hidden inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-red-600 hover:bg-red-50">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 lg:contents">
                                            {{-- Quantity --}}
                                            <div class="lg:col-span-3">
                                                <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Quantity') }}</label>
                                                <div class="mt-1 lg:mt-0 flex items-stretch">
                                                    <button type="button" @click="step(item, -1)" aria-label="−"
                                                            class="w-10 flex-shrink-0 rounded-l-lg border border-r-0 border-gray-300 bg-white text-lg leading-none text-gray-600 hover:bg-gray-50">−</button>
                                                    <input type="number" x-model.number="item.quantity"
                                                           @keydown.enter.prevent="focusSearch()"
                                                           @change="fixQty(item)"
                                                           min="0.00001" step="any" inputmode="decimal"
                                                           :class="over(item) ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500'"
                                                           class="w-full min-w-0 text-center py-2.5 px-1 text-base">
                                                    <button type="button" @click="step(item, 1)" aria-label="+"
                                                            class="w-10 flex-shrink-0 rounded-r-lg border border-l-0 border-gray-300 bg-white text-lg leading-none text-gray-600 hover:bg-gray-50">+</button>
                                                </div>
                                            </div>

                                            {{-- Unit price --}}
                                            <div class="lg:col-span-2">
                                                <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Unit Price') }} (RWF)</label>
                                                <input type="number" x-model.number="item.unitPrice"
                                                       @keydown.enter.prevent="focusSearch()"
                                                       min="0" step="any" inputmode="decimal"
                                                       class="mt-1 lg:mt-0 w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base">
                                            </div>

                                            {{-- Line total --}}
                                            <div class="col-span-2 sm:col-span-1 lg:col-span-2 flex items-center justify-between lg:block">
                                                <label class="text-xs font-medium text-gray-500 lg:hidden">{{ __('Line Total') }}</label>
                                                <div class="text-base font-semibold text-gray-900 tabular-nums" x-text="formatCurrency(item.quantity * item.unitPrice)"></div>
                                            </div>
                                        </div>

                                        {{-- Remove (desktop) --}}
                                        <div class="hidden lg:flex lg:col-span-1 justify-center">
                                            <button type="button" @click="removeItem(index)" aria-label="{{ __('Remove') }}"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-red-600 hover:bg-red-50">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- Empty state --}}
                                <div x-show="!items.length" class="text-center py-10 px-4">
                                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6v12M7 6v12M11 6v12M14 6v12M18 6v12M20 6v12"/>
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-gray-600">{{ __('No items yet') }}</p>
                                    <p class="mt-1 text-sm text-gray-400">{{ __('Scan a barcode or type a product name above.') }}</p>
                                </div>

                                @error('items')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ================= SIDE COLUMN: details + summary ================= --}}
                    <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-24">

                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl">
                            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900 tracking-wide">{{ __('Sale Details') }}</h3>
                            </div>
                            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-5">
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
                                            <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </x-form.field>

                                <x-form.field name="payment_status" :label="__('Payment Status')" required>
                                    <select name="payment_status" id="payment_status" required x-model="paymentStatus"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3 text-base transition-colors">
                                        @foreach($paymentStatuses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </x-form.field>
                            </div>
                        </div>

                        {{-- Summary (desktop) --}}
                        <div class="hidden lg:block bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6">
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wide mb-4">{{ __('Summary') }}</h3>
                            <div class="space-y-2 text-sm text-gray-500 mb-4">
                                <div class="flex justify-between">
                                    <span x-text="itemsLabel"></span>
                                    <span class="tabular-nums" x-text="fmtQty(totalUnits) + ' {{ __('units') }}'"></span>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-4 flex justify-between items-baseline">
                                <span class="text-sm font-medium text-gray-900">{{ __('Total Amount') }}</span>
                                <span class="text-2xl font-bold text-emerald-600 tabular-nums" x-text="formatCurrency(grandTotal)"></span>
                            </div>

                            <div class="mt-6 space-y-2">
                                <button type="submit" x-ref="submitBtn" :disabled="submitting"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-60 transition-colors">
                                    {{ __('Record Sale') }}
                                    <span class="ml-2 text-xs font-normal text-emerald-100">F9</span>
                                </button>
                                <a href="{{ route('sales.index') }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile sticky action bar --}}
                <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3 shadow-[0_-4px_12px_-2px_rgba(0,0,0,0.06)]">
                    <div class="flex items-center justify-between gap-3 max-w-7xl mx-auto">
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 leading-none" x-text="itemsLabel"></p>
                            <p class="text-lg font-bold text-emerald-600 tabular-nums leading-tight" x-text="formatCurrency(grandTotal)"></p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('sales.index') }}"
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" :disabled="submitting"
                                    class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-60 transition-colors">
                                {{ __('Record Sale') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Toast --}}
                <div x-show="toast.visible" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-end="opacity-0"
                     role="alert"
                     class="fixed top-4 right-4 left-4 sm:left-auto z-50 sm:max-w-sm">
                    <div class="bg-red-600 text-white px-4 sm:px-5 py-4 rounded-xl shadow-xl flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <p class="min-w-0 flex-1 text-sm break-words" x-text="toast.message"></p>
                        <button type="button" @click="toast.visible = false" class="flex-shrink-0 text-red-200 hover:text-white" aria-label="{{ __('Close') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data for the POS component (not executable, so no CSP nonce needed) --}}
    <script type="application/json" id="pos-data">{!! json_encode($posData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>

    @push('scripts')
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSale', () => {
                // Kept outside Alpine's reactive state so searching thousands of products stays fast.
                let catalog = [];
                let audio = null;
                let toastTimer = null;
                let flashTimer = null;
                let uid = 0;

                return {
                    labels: {},
                    items: [],
                    matches: [],
                    query: '',
                    open: false,
                    browse: false,
                    highlight: 0,
                    paymentStatus: 'paid',
                    submitting: false,
                    lastAdded: '',
                    flashUid: null,
                    sound: true,
                    toast: { visible: false, message: '' },

                    init() {
                        const data = JSON.parse(document.getElementById('pos-data').textContent);
                        this.labels = data.labels;
                        this.paymentStatus = data.paymentStatus;

                        catalog = data.products.map(p => ({
                            ...p,
                            _name: this.norm(p.name),
                            _codes: p.codes.map(c => this.norm(c)),
                        }));

                        try { this.sound = localStorage.getItem('pos-sound') !== '0'; } catch (e) {}

                        // Restore the cart after a server-side validation error
                        Object.values(data.old || {}).forEach(o => {
                            const p = catalog.find(x => String(x.id) === String(o.product_id));
                            if (!p) return;
                            const qty = parseFloat(o.quantity);
                            const price = parseFloat(o.unit_price);
                            this.items.push(this.makeLine(p, qty > 0 ? qty : 1, isNaN(price) ? p.price : price));
                        });

                        this.$nextTick(() => this.focusSearch());
                    },

                    /* ---------- helpers ---------- */
                    norm(s) {
                        return String(s ?? '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
                    },
                    round(n) { return Math.round(n * 100000) / 100000; },
                    fmtQty(n) { return String(+Number(n || 0).toFixed(5)); },
                    formatCurrency(n) {
                        return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Math.round(n || 0)) + ' RWF';
                    },
                    makeLine(p, qty, price) {
                        return { uid: ++uid, productId: p.id, name: p.name, stock: p.stock, unitPrice: price, quantity: qty };
                    },
                    over(item) { return item.quantity > item.stock; },

                    /* ---------- totals ---------- */
                    get grandTotal() { return this.items.reduce((s, i) => s + (i.quantity * i.unitPrice || 0), 0); },
                    get totalUnits() { return this.items.reduce((s, i) => s + (Number(i.quantity) || 0), 0); },
                    get itemsLabel() {
                        return `${this.items.length} ${this.items.length === 1 ? this.labels.item : this.labels.items}`;
                    },
                    get pendingQty() { return this.parse(this.query).qty; },

                    /* ---------- search ---------- */
                    // "3*milk" adds three; anything else adds one
                    parse(raw) {
                        const m = String(raw).trim().match(/^(\d+(?:[.,]\d+)?)\s*[*×]\s*(.+)$/);
                        if (m) {
                            const qty = parseFloat(m[1].replace(',', '.'));
                            return { qty: qty > 0 ? qty : 1, term: m[2].trim() };
                        }
                        return { qty: 1, term: String(raw).trim() };
                    },

                    refresh() {
                        const t = this.norm(this.parse(this.query).term);
                        this.highlight = 0;

                        if (!t) {
                            this.matches = this.browse
                                ? [...catalog].sort((a, b) => a._name.localeCompare(b._name)).slice(0, 60)
                                : [];
                            return;
                        }

                        const tokens = t.split(/\s+/);
                        const scored = [];

                        for (const p of catalog) {
                            let s = 0;
                            if (p._codes.includes(t)) s = 1000;
                            else if (p._codes.some(c => c.startsWith(t))) s = 600;
                            else if (p._name.startsWith(t)) s = 500;
                            else if (p._name.split(/\s+/).some(w => w.startsWith(t))) s = 400;
                            else if (tokens.every(tok => p._name.includes(tok))) s = 200;
                            else if (p._codes.some(c => c.includes(t))) s = 100;
                            if (s) scored.push({ p, s: s + (p.stock > 0 ? 10 : 0) });
                        }

                        scored.sort((a, b) => b.s - a.s || a.p._name.localeCompare(b.p._name));
                        this.matches = scored.slice(0, 8).map(x => x.p);
                    },

                    onInput() { this.open = true; this.refresh(); },

                    move(dir) {
                        if (!this.matches.length) return;
                        this.open = true;
                        this.highlight = (this.highlight + dir + this.matches.length) % this.matches.length;
                        this.$nextTick(() => {
                            this.$refs.list?.querySelector('[data-active="true"]')?.scrollIntoView({ block: 'nearest' });
                        });
                    },

                    toggleBrowse() {
                        this.browse = !this.browse;
                        this.open = this.browse;
                        this.refresh();
                        this.focusSearch();
                    },

                    clear() {
                        this.query = '';
                        this.open = false;
                        this.browse = false;
                        this.matches = [];
                    },

                    focusSearch(select = false) {
                        const el = this.$refs.search;
                        if (!el) return;
                        el.focus();
                        if (select) el.select();
                    },

                    /* ---------- Enter: scanner or typed ---------- */
                    enter() {
                        const raw = this.query.trim();
                        if (!raw) return;

                        const { qty, term } = this.parse(raw);
                        const t = this.norm(term);

                        // 1) exact barcode / SKU match (what a scanner sends)
                        let product = catalog.find(p => p._codes.includes(t));

                        // 2) otherwise the highlighted (default: top) name match
                        if (!product) product = this.matches[this.highlight];

                        if (!product) {
                            this.fail(this.labels.notFound.replace('{term}', term));
                            this.focusSearch(true);
                            return;
                        }

                        this.add(product, qty);
                    },

                    /* ---------- cart ---------- */
                    add(p, qty = 1) {
                        qty = qty > 0 ? qty : 1;
                        const line = this.items.find(i => i.productId === p.id);
                        const next = this.round((line ? line.quantity : 0) + qty);

                        if (p.stock <= 0) {
                            this.fail(this.labels.outOfStock.replace('{name}', p.name));
                            this.focusSearch(true);
                            return;
                        }
                        if (next > p.stock) {
                            this.fail(this.labels.onlyLeft.replace('{qty}', this.fmtQty(p.stock)).replace('{name}', p.name));
                            this.focusSearch(true);
                            return;
                        }

                        let target;
                        if (line) {
                            line.quantity = next;
                            target = line;
                        } else {
                            target = this.makeLine(p, qty, p.price);
                            this.items.unshift(target);
                        }

                        this.mark(target);
                        this.lastAdded = `${this.labels.added}: ${p.name} — ${this.fmtQty(next)}`;
                        this.beep('ok');
                        this.clear();
                        this.focusSearch();
                    },

                    mark(line) {
                        this.flashUid = line.uid;
                        clearTimeout(flashTimer);
                        flashTimer = setTimeout(() => { this.flashUid = null; }, 900);
                        this.$nextTick(() => {
                            document.getElementById('line-' + line.uid)?.scrollIntoView({ block: 'nearest' });
                        });
                    },

                    step(item, dir) {
                        const next = this.round((Number(item.quantity) || 0) + dir);
                        if (next <= 0) return;
                        if (dir > 0 && next > item.stock) {
                            this.fail(this.labels.onlyLeft.replace('{qty}', this.fmtQty(item.stock)).replace('{name}', item.name));
                            return;
                        }
                        item.quantity = next;
                    },

                    fixQty(item) { if (!(item.quantity > 0)) item.quantity = 1; },

                    removeItem(index) {
                        this.items.splice(index, 1);
                        this.focusSearch();
                    },

                    /* ---------- feedback ---------- */
                    fail(message) {
                        this.beep('error');
                        this.toast.message = message;
                        this.toast.visible = true;
                        clearTimeout(toastTimer);
                        toastTimer = setTimeout(() => { this.toast.visible = false; }, 4000);
                    },

                    toggleSound() {
                        this.sound = !this.sound;
                        try { localStorage.setItem('pos-sound', this.sound ? '1' : '0'); } catch (e) {}
                        if (this.sound) this.beep('ok');
                    },

                    beep(kind) {
                        if (!this.sound) return;
                        try {
                            const Ctx = window.AudioContext || window.webkitAudioContext;
                            if (!Ctx) return;
                            audio = audio || new Ctx();
                            const now = audio.currentTime;
                            const tones = kind === 'ok' ? [[880, 0, 0.07]] : [[220, 0, 0.12], [170, 0.15, 0.18]];
                            tones.forEach(([freq, delay, len]) => {
                                const osc = audio.createOscillator();
                                const gain = audio.createGain();
                                osc.frequency.value = freq;
                                gain.gain.setValueAtTime(0.08, now + delay);
                                gain.gain.exponentialRampToValueAtTime(0.0001, now + delay + len);
                                osc.connect(gain);
                                gain.connect(audio.destination);
                                osc.start(now + delay);
                                osc.stop(now + delay + len + 0.02);
                            });
                            if (kind !== 'ok' && navigator.vibrate) navigator.vibrate(60);
                        } catch (e) {}
                    },

                    /* ---------- keyboard ---------- */
                    hotkeys(e) {
                        if (e.key === 'F2') { e.preventDefault(); this.focusSearch(true); return; }
                        if (e.key === 'F9') { e.preventDefault(); this.$refs.submitBtn?.click(); return; }

                        // Scanner safety net: if focus is on a non-input and characters start
                        // arriving, send them to the search box.
                        if (e.ctrlKey || e.metaKey || e.altKey || e.key.length !== 1) return;
                        const el = document.activeElement;
                        const editable = el && (['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName) || el.isContentEditable);
                        if (!editable) this.focusSearch();
                    },

                    /* ---------- submit ---------- */
                    onSubmit(e) {
                        if (this.submitting) { e.preventDefault(); return; }

                        if (!this.items.length) {
                            e.preventDefault();
                            this.fail(this.labels.minItem);
                            this.focusSearch();
                            return;
                        }
                        if (this.items.some(i => !(i.quantity > 0) || !(i.unitPrice >= 0))) {
                            e.preventDefault();
                            this.fail(this.labels.badQty);
                            return;
                        }
                        if (this.items.some(i => this.over(i))) {
                            e.preventDefault();
                            this.fail(this.labels.stockError);
                            return;
                        }

                        this.submitting = true;
                    },
                };
            });
        });
    </script>
    @endpush
</x-app-layout>