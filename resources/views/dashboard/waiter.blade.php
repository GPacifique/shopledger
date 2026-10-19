<x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Take Order
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $shop->business_name }} · {{ auth()->user()->name }}
                </p>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800 ring-1 ring-amber-200">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Seller approval required
            </span>
        </div>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- Flexible page wrapper: adapts to the available sidebar content width --}}
    <div class="w-full min-w-0 py-4 sm:py-6 lg:py-8">
        <div class="mx-auto w-full min-w-0 max-w-[1600px] space-y-6 px-3 sm:px-5 xl:px-6">

            @if (session('success'))
                <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200"
                     role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200"
                     role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200"
                     role="alert">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('shops.orders.store', $shop) }}"
                x-data="posOrder(@js($catalogue))"
                @submit="onSubmit($event)"
                class="min-w-0 pb-24 xl:pb-0"
            >
                @csrf
                <input type="hidden" name="submission_token" :value="token">

                {{-- Stack on smaller screens; use columns only when enough room exists --}}
                <div class="grid min-w-0 grid-cols-1 items-start gap-5 xl:grid-cols-3 xl:gap-6">

                    {{-- PRODUCT CATALOGUE --}}
                    <section class="min-w-0 xl:col-span-2">
                        <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-200 sm:p-5">
                            <label for="product-search" class="sr-only">Search products</label>
                            <input
                                id="product-search"
                                type="search"
                                x-model.debounce.150ms="search"
                                placeholder="Search by name, barcode or SKU..."
                                class="w-full min-w-0 rounded-lg border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @if ($categories->isNotEmpty())
                                <div class="mt-4 flex min-w-0 gap-2 overflow-x-auto pb-2">
                                    <button
                                        type="button"
                                        @click="category = ''"
                                        :class="category === ''
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="shrink-0 rounded-full px-4 py-2 text-sm font-medium"
                                    >
                                        All Products
                                    </button>

                                    @foreach ($categories as $cat)
                                        <button
                                            type="button"
                                            @click="category = @js($cat)"
                                            :class="category === @js($cat)
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="shrink-0 rounded-full px-4 py-2 text-sm font-medium"
                                        >
                                            {{ $cat }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                <span x-text="filtered.length + ' products'"></span>
                                <button
                                    type="button"
                                    x-show="search || category"
                                    @click="search = ''; category = ''"
                                    class="text-indigo-600 hover:underline"
                                >
                                    Clear filters
                                </button>
                            </div>
                        </div>

                        {{-- Responsive product cards --}}
                        <div class="mt-4 grid min-w-0 grid-cols-2 gap-3 sm:grid-cols-3 2xl:grid-cols-4">
                            <template x-for="p in filtered" :key="p.id">
                                <button
                                    type="button"
                                    @click="add(p)"
                                    :disabled="p.stock <= 0"
                                    :aria-label="'Add ' + p.name"
                                    class="group flex min-w-0 flex-col overflow-hidden rounded-xl bg-white text-left shadow-sm ring-1 ring-gray-200 transition hover:shadow-md active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
                                    :class="justAdded === p.id ? 'ring-2 ring-green-500' : ''"
                                >
                                    <div class="relative aspect-square w-full overflow-hidden bg-gray-100">
                                        <template x-if="p.image && !broken[p.id]">
                                            <img
                                                :src="p.image"
                                                :alt="p.name"
                                                loading="lazy"
                                                @error="broken[p.id] = true"
                                                class="h-full w-full object-cover transition duration-200 group-hover:scale-105"
                                            >
                                        </template>

                                        <template x-if="!p.image || broken[p.id]">
                                            <div class="flex h-full w-full items-center justify-center text-gray-300">
                                                <svg class="h-10 w-10 sm:h-12 sm:w-12" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="1.5"
                                                          d="M4 16l4-4a3 3 0 014 0l4 4m-2-2l1-1a3 3 0 014 0l2 2M4 6h16v12H4z"/>
                                                </svg>
                                            </div>
                                        </template>

                                        <span
                                            x-show="p.stock <= 0"
                                            class="absolute inset-0 flex items-center justify-center bg-black/50 px-2 text-center text-xs font-semibold text-white sm:text-sm"
                                        >
                                            Out of stock
                                        </span>

                                        <span
                                            x-show="qtyOf(p.id) > 0"
                                            x-text="qtyOf(p.id)"
                                            class="absolute right-2 top-2 flex h-7 min-w-7 items-center justify-center rounded-full bg-indigo-600 px-2 text-xs font-bold text-white shadow"
                                        ></span>
                                    </div>

                                    <div class="flex min-w-0 flex-1 flex-col p-2.5 sm:p-3">
                                        <p
                                            class="line-clamp-2 break-words text-sm font-semibold text-gray-900"
                                            x-text="p.name"
                                        ></p>

                                        <p
                                            x-show="p.category"
                                            x-text="p.category"
                                            class="mt-1 truncate text-xs text-gray-500"
                                        ></p>

                                        <div class="mt-auto pt-3">
                                            <p
                                                class="break-words text-sm font-bold text-indigo-700 sm:text-base"
                                                x-text="money(p.price)"
                                            ></p>
                                            <p
                                                class="mt-1 text-xs"
                                                :class="p.tracked && p.stock <= 5
                                                    ? 'text-red-600'
                                                    : 'text-gray-500'"
                                                x-text="!p.tracked
                                                    ? 'Available'
                                                    : (p.stock > 0 ? p.stock + ' in stock' : 'Unavailable')"
                                            ></p>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div
                            x-show="filtered.length === 0"
                            x-cloak
                            class="mt-4 rounded-xl bg-white p-8 text-center text-gray-500 ring-1 ring-gray-200 sm:p-12"
                        >
                            <p class="font-medium">No products match your search.</p>
                            <button
                                type="button"
                                @click="search = ''; category = ''"
                                class="mt-2 text-sm text-indigo-600 underline"
                            >
                                Clear filters
                            </button>
                        </div>
                    </section>

                    {{-- ORDER SUMMARY --}}
                    <aside id="order-summary" class="min-w-0 scroll-mt-4 xl:sticky xl:top-4 xl:self-start">
                        <div class="min-w-0 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:p-5">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">Current Order</h3>
                                <span
                                    class="shrink-0 text-sm text-gray-500"
                                    x-text="lines.length + (lines.length === 1 ? ' line' : ' lines')"
                                ></span>
                            </div>

                            <p
                                x-show="lines.length === 0"
                                class="py-7 text-center text-sm text-gray-500"
                            >
                                Select a product to add it to the order.
                            </p>

                            <ul class="mt-3 divide-y divide-gray-100">
                                <template x-for="(l, i) in lines" :key="l.id">
                                    <li class="min-w-0 py-3">
                                        <input type="hidden" :name="`items[${i}][product_id]`" :value="l.id">
                                        <input type="hidden" :name="`items[${i}][quantity]`" :value="l.qty">

                                        <div class="flex min-w-0 items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <p class="break-words text-sm font-medium text-gray-900" x-text="l.name"></p>
                                                <p class="mt-1 text-xs text-gray-500" x-text="money(l.price) + ' each'"></p>
                                            </div>

                                            <button
                                                type="button"
                                                @click="remove(l.id)"
                                                aria-label="Remove item"
                                                class="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex items-center gap-1.5">
                                                <button
                                                    type="button"
                                                    @click="change(l.id, -1)"
                                                    :disabled="l.qty <= 0.01"
                                                    aria-label="Decrease quantity"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-lg font-bold hover:bg-gray-200 disabled:opacity-40"
                                                >−</button>

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    :max="l.stock"
                                                    x-model.number="l.qty"
                                                    @change="clamp(l)"
                                                    class="h-9 w-20 rounded-lg border-gray-300 p-0 text-center text-sm"
                                                    aria-label="Quantity"
                                                >

                                                <button
                                                    type="button"
                                                    @click="change(l.id, 1)"
                                                    :disabled="l.tracked && l.qty + 1 > l.stock"
                                                    aria-label="Increase quantity"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-lg font-bold hover:bg-gray-200 disabled:opacity-40"
                                                >+</button>
                                            </div>

                                            <p class="text-sm font-semibold text-gray-900"
                                               x-text="money(l.qty * l.price)"></p>
                                        </div>

                                        <p
                                            x-show="l.tracked && l.qty >= l.stock"
                                            class="mt-2 text-xs text-amber-600"
                                        >
                                            Maximum available stock reached.
                                        </p>
                                    </li>
                                </template>
                            </ul>

                            <div class="mt-3 flex items-center justify-between gap-3 border-t pt-4">
                                <span class="text-sm font-medium text-gray-600">Subtotal</span>
                                <span class="text-xl font-bold text-gray-900" x-text="money(subtotal)"></span>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700">
                                        Customer
                                    </label>
                                    <select
                                        id="customer_id"
                                        name="customer_id"
                                        class="mt-1 w-full min-w-0 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Walk-in customer</option>
                                        @foreach ($customers as $c)
                                            <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">
                                        Payment method
                                    </label>
                                    <select
                                        id="payment_method"
                                        name="payment_method"
                                        required
                                        class="mt-1 w-full min-w-0 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        @foreach ($paymentMethods as $value => $label)
                                            <option value="{{ $value }}" @selected(old('payment_method') === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">
                                        Notes (table number, instructions)
                                    </label>
                                    <textarea
                                        id="notes"
                                        name="notes"
                                        rows="2"
                                        maxlength="1000"
                                        class="mt-1 w-full min-w-0 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600" role="alert"></p>

                            <button
                                type="submit"
                                :disabled="submitting || lines.length === 0"
                                class="mt-5 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50 sm:text-base"
                            >
                                <span x-show="!submitting">Submit Order for Approval</span>
                                <span x-show="submitting" x-cloak>Submitting...</span>
                            </button>

                            <p class="mt-3 text-center text-xs leading-5 text-gray-500">
                                A seller must approve this order before it becomes a sale.
                            </p>
                        </div>
                    </aside>
                </div>

                {{-- Mobile order review bar --}}
                <div
                    x-show="lines.length > 0"
                    x-cloak
                    class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white p-3 shadow-lg xl:hidden"
                >
                    <a
                        href="#order-summary"
                        class="mx-auto flex w-full max-w-3xl items-center justify-between gap-3 rounded-lg bg-indigo-600 px-4 py-3 text-white"
                    >
                        <span class="text-sm" x-text="lines.length + ' item(s)'"></span>
                        <span class="text-sm font-semibold" x-text="money(subtotal) + ' · Review order'"></span>
                    </a>
                </div>
            </form>

            {{-- MY ORDERS --}}
            <section class="min-w-0 pb-4">
                <div class="min-w-0 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b px-4 py-4 sm:px-5">
                        <h3 class="text-lg font-semibold text-gray-900">My Orders</h3>
                        <span class="text-xs text-gray-500">Your recent submissions</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[650px] divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Order</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Submitted</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse ($orders as $order)
                                    @php
                                        $badge = match ($order->status) {
                                            'pending'  => 'bg-amber-100 text-amber-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default    => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            {{ number_format($order->total_amount) }} RWF
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-500">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $badge }}">
                                                {{ ucfirst($order->status) }}
                                            </span>

                                            @if ($order->status === 'rejected' && $order->rejection_reason)
                                                <p class="mt-1 max-w-xs text-xs text-red-600">
                                                    {{ $order->rejection_reason }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right">
                                            <a
                                                href="{{ route('shops.orders.show', [$shop, $order]) }}"
                                                class="font-medium text-indigo-600 hover:underline"
                                            >View</a>

                                            @if ($order->canBeCancelled())
                                                <form
                                                    method="POST"
                                                    action="{{ route('shops.orders.cancel', [$shop, $order]) }}"
                                                    class="ml-3 inline"
                                                    onsubmit="return confirm('Cancel this order?')"
                                                >
                                                    @csrf
                                                    <button type="submit" class="font-medium text-red-600 hover:underline">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                            You haven't submitted any orders yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div class="border-t px-4 py-3">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('alpine:init', () => {
            if (window.__posOrderRegistered) return;
            window.__posOrderRegistered = true;

            Alpine.data('posOrder', (products) => ({
                products: products || [],
                search: '',
                category: '',
                lines: [],
                broken: {},
                justAdded: null,
                submitting: false,
                error: '',

                token: (window.crypto && typeof window.crypto.randomUUID === 'function')
                    ? window.crypto.randomUUID()
                    : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                        const r = Math.random() * 16 | 0;
                        return (c === 'x' ? r : (r & 3 | 8)).toString(16);
                    }),

                get filtered() {
                    const q = this.search.trim().toLowerCase();

                    return this.products.filter(p =>
                        (!this.category || p.category === this.category) &&
                        (!q ||
                            (p.name || '').toLowerCase().includes(q) ||
                            (p.barcode || '').toLowerCase().includes(q) ||
                            (p.sku || '').toLowerCase().includes(q)
                        )
                    );
                },

                get subtotal() {
                    return this.lines.reduce((sum, line) => sum + line.qty * line.price, 0);
                },

                money(amount) {
                    return new Intl.NumberFormat('en-RW', {
                        maximumFractionDigits: 0
                    }).format(Number(amount) || 0) + ' RWF';
                },

                qtyOf(id) {
                    const line = this.lines.find(item => item.id === id);
                    return line ? line.qty : 0;
                },

                add(product) {
                    if (product.tracked && product.stock <= 0) return;

                    const line = this.lines.find(item => item.id === product.id);

                    if (line) {
                        if (line.tracked && line.qty + 1 > line.stock) {
                            this.error = `Only ${line.stock} of ${line.name} available.`;
                            return;
                        }

                        line.qty = Math.round((line.qty + 1) * 100) / 100;
                    } else {
                        this.lines.push({
                            id: product.id,
                            name: product.name,
                            price: Number(product.price) || 0,
                            stock: Number(product.stock) || 0,
                            tracked: Boolean(product.tracked),
                            qty: 1
                        });
                    }

                    this.error = '';
                    this.justAdded = product.id;

                    setTimeout(() => {
                        if (this.justAdded === product.id) this.justAdded = null;
                    }, 400);
                },

                change(id, delta) {
                    const line = this.lines.find(item => item.id === id);
                    if (!line) return;

                    const next = Math.round((line.qty + delta) * 100) / 100;

                    if (next < 0.01 || (line.tracked && next > line.stock)) return;

                    line.qty = next;
                    this.error = '';
                },

                clamp(line) {
                    let quantity = Number(line.qty);

                    if (!Number.isFinite(quantity) || quantity < 0.01) {
                        quantity = 0.01;
                    }

                    if (line.tracked && quantity > line.stock) {
                        quantity = line.stock;
                    }

                    line.qty = Math.round(quantity * 100) / 100;
                },

                remove(id) {
                    this.lines = this.lines.filter(item => item.id !== id);
                    this.error = '';
                },

                onSubmit(event) {
                    if (this.submitting) {
                        event.preventDefault();
                        return;
                    }

                    if (this.lines.length === 0) {
                        event.preventDefault();
                        this.error = 'Add at least one product.';
                        return;
                    }

                    for (const line of this.lines) {
                        if (!Number.isFinite(Number(line.qty)) || line.qty < 0.01) {
                            event.preventDefault();
                            this.error = 'Enter a valid quantity for every product.';
                            return;
                        }

                        if (line.tracked && line.qty > line.stock) {
                            event.preventDefault();
                            this.error = `Insufficient stock for ${line.name}.`;
                            return;
                        }
                    }

                    this.submitting = true;
                }
            }));
        });
    </script>

