<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Take Order</h2>
                <p class="text-sm text-gray-500">{{ $shop->business_name }} · {{ auth()->user()->name }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800 ring-1 ring-amber-200">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Orders require seller approval
            </span>
        </div>
    </x-slot>

    <style>[x-cloak]{display:none !important}</style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 rounded-lg bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mx-4 sm:mx-0 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mx-4 sm:mx-0 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200">
                    <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('shops.orders.store', $shop) }}"
                  x-data="posOrder(@js($catalogue))" @submit="onSubmit($event)" novalidate
                  class="px-4 sm:px-0">
                @csrf
                <input type="hidden" name="submission_token" :value="token">

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    {{-- ============ PRODUCT CATALOGUE ============ --}}
                    <section class="lg:col-span-2 min-w-0">
                        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                            <input type="search" x-model.debounce.100ms="search"
                                   placeholder="Search by name, barcode or SKU…"
                                   class="w-full rounded-lg border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:ring-indigo-500">

                            @if ($categories->isNotEmpty())
                                <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                                    <button type="button" @click="category = ''"
                                            :class="category === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="shrink-0 rounded-full px-4 py-2 text-sm font-medium">All Products</button>
                                    @foreach ($categories as $cat)
                                        <button type="button" @click="category = @js($cat)"
                                                :class="category === @js($cat) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                class="shrink-0 rounded-full px-4 py-2 text-sm font-medium">{{ $cat }}</button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                            <template x-for="p in filtered" :key="p.id">
                                <button type="button" @click="add(p)" :disabled="p.stock <= 0"
                                        :aria-label="'Add ' + p.name"
                                        class="group relative flex flex-col overflow-hidden rounded-xl bg-white text-left shadow-sm ring-1 ring-gray-100 transition hover:shadow-md active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                                        :class="justAdded === p.id ? 'ring-2 ring-green-500' : ''">
                                    <div class="relative aspect-square w-full bg-gray-100">
                                        <template x-if="p.image && !broken[p.id]">
                                            <img :src="p.image" :alt="p.name" loading="lazy" @error="broken[p.id] = true"
                                                 class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!p.image || broken[p.id]">
                                            <div class="flex h-full w-full items-center justify-center text-gray-300">
                                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4-4a3 3 0 014 0l4 4m-2-2l1-1a3 3 0 014 0l2 2M4 6h16v12H4z"/></svg>
                                            </div>
                                        </template>
                                        <span x-show="p.stock <= 0" class="absolute inset-0 flex items-center justify-center bg-black/50 text-sm font-semibold text-white">Out of stock</span>
                                        <span x-show="qtyOf(p.id) > 0" x-text="qtyOf(p.id)" class="absolute right-2 top-2 rounded-full bg-indigo-600 px-2 py-0.5 text-xs font-bold text-white"></span>
                                    </div>
                                    <div class="flex flex-1 flex-col p-3">
                                        <p class="line-clamp-2 text-sm font-semibold text-gray-900" x-text="p.name"></p>
                                        <p x-show="p.category" class="text-xs text-gray-500" x-text="p.category"></p>
                                        <div class="mt-auto pt-2">
                                            <p class="text-sm font-bold text-indigo-700" x-text="money(p.price)"></p>
                                            <p class="text-xs" :class="p.tracked && p.stock <= 5 ? 'text-red-600' : 'text-gray-500'"
                                               x-text="!p.tracked ? 'Available' : (p.stock > 0 ? p.stock + ' in stock' : 'Unavailable')"></p>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div x-show="filtered.length === 0" x-cloak class="mt-4 rounded-xl bg-white p-10 text-center text-gray-500 ring-1 ring-gray-100">
                            <p class="font-medium">No products match your search.</p>
                            <button type="button" class="mt-2 text-sm text-indigo-600 underline" @click="search=''; category=''">Clear filters</button>
                        </div>
                    </section>

                    {{-- ============ ORDER SUMMARY ============ --}}
                    <aside id="order-summary" class="min-w-0 lg:sticky lg:top-4 lg:self-start">
                        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Current Order</h3>
                                <span class="text-sm text-gray-500" x-text="lines.length + (lines.length === 1 ? ' line' : ' lines')"></span>
                            </div>

                            <p x-show="lines.length === 0" class="py-8 text-center text-sm text-gray-500">Tap a product to add it.</p>

                            <ul class="mt-3 divide-y divide-gray-100">
                                <template x-for="(l, i) in lines" :key="l.id">
                                    <li class="py-3">
                                        <input type="hidden" :name="`items[${i}][product_id]`" :value="l.id">
                                        <input type="hidden" :name="`items[${i}][quantity]`" :value="l.qty">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-medium text-gray-900" x-text="l.name"></p>
                                                <p class="text-xs text-gray-500" x-text="money(l.price) + ' each'"></p>
                                            </div>
                                            <button type="button" @click="remove(l.id)" aria-label="Remove item"
                                                    class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click="change(l.id, -1)" :disabled="l.qty - 1 < 0.01"
                                                        class="h-9 w-9 rounded-lg bg-gray-100 text-lg font-bold disabled:opacity-40" aria-label="Decrease">−</button>
                                                <input type="number" step="0.01" min="0.01" :max="l.stock" x-model.number="l.qty" @change="clamp(l)"
                                                       class="h-9 w-16 rounded-lg border-gray-300 p-0 text-center text-sm" aria-label="Quantity">
                                                <button type="button" @click="change(l.id, 1)" :disabled="l.qty + 1 > l.stock"
                                                        class="h-9 w-9 rounded-lg bg-gray-100 text-lg font-bold disabled:opacity-40" aria-label="Increase">+</button>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900" x-text="money(l.qty * l.price)"></p>
                                        </div>
                                        <p x-show="l.tracked && l.qty >= l.stock" class="mt-1 text-xs text-amber-600">Maximum available stock reached.</p>
                                    </li>
                                </template>
                            </ul>

                            <div class="mt-3 flex items-center justify-between border-t pt-3">
                                <span class="text-sm font-medium text-gray-600">Subtotal</span>
                                <span class="text-xl font-bold text-gray-900" x-text="money(subtotal)"></span>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div>
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                                    <select id="customer_id" name="customer_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm">
                                        <option value="">Walk-in customer</option>
                                        @foreach ($customers as $c)
                                            <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment method</label>
                                    <select id="payment_method" name="payment_method" required class="mt-1 w-full rounded-lg border-gray-300 text-sm">
                                        @foreach ($paymentMethods as $value => $label)
                                            <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (table number, instructions)</label>
                                    <textarea id="notes" name="notes" rows="2" maxlength="1000" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600" role="alert"></p>

                            <button type="submit" :disabled="submitting || lines.length === 0"
                                    class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-3 text-base font-semibold text-white shadow hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <span x-show="!submitting">Submit Order for Approval</span>
                                <span x-show="submitting" x-cloak>Submitting…</span>
                            </button>
                            <p class="mt-2 text-center text-xs text-gray-500">A seller must approve this order before it becomes a sale.</p>
                        </div>
                    </aside>
                </div>

                {{-- Mobile quick-jump bar --}}
                <div class="fixed inset-x-0 bottom-0 z-20 border-t bg-white p-3 shadow-lg lg:hidden" x-show="lines.length > 0" x-cloak>
                    <a href="#order-summary" class="flex items-center justify-between rounded-lg bg-indigo-600 px-4 py-3 text-white">
                        <span x-text="lines.length + ' item(s)'"></span>
                        <span class="font-semibold" x-text="money(subtotal) + ' · Review'"></span>
                    </a>
                </div>
            </form>

            {{-- ============ MY ORDERS ============ --}}
            <section class="px-4 sm:px-0 pb-16 lg:pb-0">
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                    <h3 class="border-b px-4 py-3 text-lg font-semibold text-gray-900">My Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-4 py-2">Order</th><th class="px-4 py-2">Total</th>
                                    <th class="px-4 py-2">Submitted</th><th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($orders as $order)
                                    @php
                                        $badge = match ($order->status) {
                                            'pending'   => 'bg-amber-100 text-amber-800',
                                            'approved'  => 'bg-green-100 text-green-800',
                                            'rejected'  => 'bg-red-100 text-red-800',
                                            default     => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $order->order_number }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ number_format($order->total_amount) }} RWF</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">{{ ucfirst($order->status) }}</span>
                                            @if ($order->status === 'rejected' && $order->rejection_reason)
                                                <p class="mt-1 text-xs text-red-600">{{ $order->rejection_reason }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <a href="{{ route('shops.orders.show', [$shop, $order]) }}" class="text-indigo-600 hover:underline">View</a>
                                            @if ($order->canBeCancelled())
                                                <form method="POST" action="{{ route('shops.orders.cancel', [$shop, $order]) }}" class="inline"
                                                      onsubmit="return confirm('Cancel this order?')">
                                                    @csrf
                                                    <button class="ml-3 text-red-600 hover:underline">Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">You haven't submitted any orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($orders->hasPages())
                        <div class="border-t px-4 py-3">{{ $orders->links() }}</div>
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
                products, search: '', category: '', lines: [], broken: {},
                justAdded: null, submitting: false, error: '',
                token: (crypto.randomUUID ? crypto.randomUUID()
                    : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                        const r = Math.random() * 16 | 0; return (c === 'x' ? r : (r & 3 | 8)).toString(16);
                    })),

                get filtered() {
                    const q = this.search.trim().toLowerCase();
                    return this.products.filter(p =>
                        (!this.category || p.category === this.category) &&
                        (!q || p.name.toLowerCase().includes(q) || (p.barcode || '').toLowerCase().includes(q)));
                },
                get subtotal() { return this.lines.reduce((s, l) => s + l.qty * l.price, 0); },
                money(n) { return new Intl.NumberFormat('en-RW').format(Math.round(n)) + ' RWF'; },
                qtyOf(id) { const l = this.lines.find(x => x.id === id); return l ? l.qty : 0; },

                add(p) {
                    if (p.stock <= 0) return;
                    const l = this.lines.find(x => x.id === p.id);
                    if (l) {
                        if (l.qty + 1 > l.stock) { this.error = `Only ${l.stock} of ${l.name} available.`; return; }
                        l.qty = Math.round((l.qty + 1) * 100) / 100;
                    } else {
                        this.lines.push({ id: p.id, name: p.name, price: p.price, stock: p.stock, tracked: p.tracked, qty: 1 });
                    }
                    this.error = '';
                    this.justAdded = p.id;
                    setTimeout(() => { if (this.justAdded === p.id) this.justAdded = null; }, 400);
                },
                change(id, delta) {
                    const l = this.lines.find(x => x.id === id); if (!l) return;
                    const next = Math.round((l.qty + delta) * 100) / 100;
                    if (next < 0.01 || next > l.stock) return;   // never deletes the line
                    l.qty = next;
                },
                clamp(l) {
                    let q = Number(l.qty);
                    if (!isFinite(q) || q < 0.01) q = 0.01;
                    if (q > l.stock) q = l.stock;
                    l.qty = Math.round(q * 100) / 100;
                },
                remove(id) { this.lines = this.lines.filter(l => l.id !== id); },

                onSubmit(e) {
                    if (this.submitting) { e.preventDefault(); return; }
                    if (this.lines.length === 0) { e.preventDefault(); this.error = 'Add at least one product.'; return; }
                    this.lines.forEach(l => this.clamp(l));
                    this.submitting = true;
                },
            }));
        });
    </script>
</x-app-layout>