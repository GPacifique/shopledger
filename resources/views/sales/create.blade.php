@php
    // Theme-aware class sets. They read the CSS variables from layouts/app.blade.php
    // (--surface, --text, --border, ...), which switch automatically with `html.dark`.
    // Keep the class names written out in full so Tailwind's JIT can see them.
    $cardCls  = 'rounded-lg border border-[color:var(--border)] bg-[color:var(--surface)] shadow-sm';
    $txt      = 'text-[color:var(--text)]';
    $mut      = 'text-[color:var(--text-muted)]';
    $dng      = 'text-[color:var(--danger-text)]';
    $labelCls = 'mb-1 block text-sm font-medium text-[color:var(--text-soft)]';
    $inputCls = 'block w-full rounded-md border border-[color:var(--border)] bg-[color:var(--surface)] text-[color:var(--text)] shadow-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--accent)]';

    $paymentStatuses = [
        'paid'    => __('Paid'),
        'partial' => __('Partial'),
        'unpaid'  => __('Unpaid'),
    ];

    $productData = $products->map(fn ($p) => [
        'id'    => $p->id,
        'name'  => $p->name,
        'sku'   => $p->sku,
        'stock' => (float) ($p->stock ?? 0),
        'price' => (float) ($p->selling_price ?? 0),
    ])->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}"
               class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-[color:var(--text-muted)] transition-colors hover:bg-[color:var(--hover)] hover:text-[color:var(--text)]"
               aria-label="{{ __('Back to sales') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-semibold leading-tight {{ $txt }}">
                    {{ __('Record New Sale') }}
                </h2>
                <p class="mt-0.5 hidden text-xs sm:block {{ $mut }}">{{ __('Search or scan products to add them to the sale, then confirm') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Session errors and the validation list are already shown by layouts/app.blade.php --}}

            <form method="POST" action="{{ route('sales.store') }}" id="saleForm" novalidate>
                @csrf

                {{-- 1. Sale details --}}
                <section class="{{ $cardCls }} mb-6">
                    <div class="border-b border-[color:var(--border)] px-5 py-4 sm:px-6">
                        <h3 class="text-lg font-semibold {{ $txt }}">{{ __('Sale Details') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                        <x-form.field name="sale_date" :label="__('Sale Date')" required>
                            <input type="date" name="sale_date" id="sale_date"
                                   value="{{ old('sale_date', date('Y-m-d')) }}" required
                                   class="{{ $inputCls }}">
                        </x-form.field>

                        <x-form.field name="customer_id" :label="__('Customer')">
                            <select name="customer_id" id="customer_id" class="{{ $inputCls }}">
                                <option value="">{{ __('Walk-in customer') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form.field>

                        <x-form.field name="payment_method" :label="__('Payment Method')" required>
                            <select name="payment_method" id="payment_method" required class="{{ $inputCls }}">
                                @foreach(\App\Models\Sale::PAYMENT_METHODS as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form.field>

                        <x-form.field name="payment_status" :label="__('Payment Status')" required>
                            <select name="payment_status" id="payment_status" required class="{{ $inputCls }}">
                                @foreach($paymentStatuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_status', 'paid') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form.field>
                    </div>
                </section>

                {{-- 2. Items (no overflow-hidden here, or the product dropdown gets clipped) --}}
                <section class="{{ $cardCls }}">
                    <div class="border-b border-[color:var(--border)] px-5 py-4 sm:px-6">
                        <h3 class="text-lg font-semibold {{ $txt }}">{{ __('Sale Items') }}</h3>
                    </div>

                    <div class="p-4 sm:p-6">
                        {{-- Single POS search: pick or scan a product and it is added to the cart below --}}
                        <div class="product-combo relative" id="posCombo">
                            <label for="posSearch" class="{{ $labelCls }}">{{ __('Add product') }}</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[color:var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                </svg>
                                <input type="text" id="posSearch"
                                       class="{{ $inputCls }} py-3 pl-10 text-base"
                                       placeholder="{{ __('Search by name or SKU, or scan a barcode...') }}"
                                       autocomplete="off"
                                       autofocus
                                       role="combobox"
                                       aria-autocomplete="list"
                                       aria-expanded="false"
                                       aria-controls="posList">
                            </div>
                            <ul id="posList"
                                class="absolute z-30 mt-1 hidden max-h-80 w-full overflow-auto rounded-md border border-[color:var(--border)] bg-[color:var(--surface)] shadow-lg"
                                role="listbox"></ul>
                            <p class="mt-1.5 text-xs {{ $mut }}">
                                {{ __('Enter adds the highlighted product. Type 5*name to add 5 at once. Press F2 to jump back here.') }}
                            </p>
                        </div>

                        {{-- Cart --}}
                        <div class="mt-5">
                            <div class="hidden grid-cols-12 gap-3 px-4 pb-2 text-xs font-medium uppercase tracking-wide sm:grid {{ $mut }}">
                                <div class="col-span-4">{{ __('Product') }}</div>
                                <div class="col-span-3">{{ __('Quantity') }}</div>
                                <div class="col-span-2">{{ __('Unit Price') }} (RWF)</div>
                                <div class="col-span-2 text-right">{{ __('Line Total') }}</div>
                                <div class="col-span-1"></div>
                            </div>

                            <div id="itemsContainer" class="space-y-3">
                                {{-- Cart rows are added here --}}
                            </div>

                            <div id="emptyCart"
                                 class="rounded-lg border-2 border-dashed border-[color:var(--border)] px-4 py-10 text-center text-sm {{ $mut }}">
                                {{ __('No products yet. Search or scan a product above to add it.') }}
                            </div>
                        </div>

                        <p id="itemsError" class="mt-3 hidden text-sm {{ $dng }}" role="alert"></p>
                        @error('items')
                            <p class="mt-3 text-sm {{ $dng }}">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                {{-- 3. Bottom action bar: total + buttons, always visible --}}
                <div class="sticky bottom-0 z-20 mt-6 rounded-lg border border-[color:var(--border)] bg-[color:var(--surface)] px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] shadow-lg sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs {{ $mut }}">
                                {{ __('Total Amount') }}
                                (<span id="itemCount">0</span> <span id="itemCountLabel">{{ __('items') }}</span>)
                            </p>
                            <p id="grandTotal" class="text-2xl font-bold tabular-nums text-[color:var(--accent-text)]">RWF 0</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:flex">
                            <a href="{{ route('sales.index') }}"
                               class="inline-flex items-center justify-center rounded-md border border-[color:var(--border)] bg-[color:var(--surface)] px-5 py-2.5 text-sm font-medium text-[color:var(--text-soft)] shadow-sm hover:bg-[color:var(--hover)]">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" id="submitBtn"
                                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                                {{ __('Record Sale') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Stock toast --}}
    <div id="stockToast" class="fixed left-4 right-4 top-4 z-50 hidden sm:left-auto sm:max-w-sm" role="alert">
        <div class="flex items-start gap-3 rounded-xl bg-red-600 px-4 py-4 text-white shadow-xl sm:px-5">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold">{{ __('Insufficient Stock!') }}</p>
                <p id="stockToastMsg" class="mt-0.5 break-words text-sm text-red-100"></p>
            </div>
            <button type="button" id="stockToastClose" class="flex-shrink-0 text-red-200 hover:text-white" aria-label="{{ __('Close') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Cart row template. No inline handlers: a nonce-based CSP blocks them. --}}
    <template id="itemTemplate">
        <div class="item-row rounded-lg border border-[color:var(--border)] bg-[color:var(--surface-2)] p-3 sm:p-4">
            <input type="hidden" class="product-id" name="items[__INDEX__][product_id]" value="">

            <div class="grid grid-cols-12 items-center gap-3">
                {{-- Product --}}
                <div class="col-span-12 flex min-w-0 items-start gap-3 sm:col-span-4">
                    <span class="row-number flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[color:var(--accent-soft)] text-xs font-semibold text-[color:var(--accent-text)]">1</span>
                    <div class="min-w-0 flex-1">
                        <p class="product-name truncate text-sm font-semibold {{ $txt }}"></p>
                        <p class="product-meta truncate text-xs {{ $mut }}"></p>
                    </div>
                    {{-- Mobile remove button --}}
                    <button type="button"
                            class="remove-item -mt-1 shrink-0 rounded p-2 text-[color:var(--danger-text)] hover:bg-[color:var(--danger-soft)] sm:hidden"
                            aria-label="{{ __('Remove item') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Quantity stepper --}}
                <div class="col-span-6 sm:col-span-3">
                    <span class="{{ $labelCls }} sm:hidden">{{ __('Quantity') }}</span>
                    <div class="flex items-stretch">
                        <button type="button"
                                class="qty-minus inline-flex w-9 items-center justify-center rounded-l-md border border-r-0 border-[color:var(--border)] bg-[color:var(--surface)] text-lg font-semibold text-[color:var(--text-soft)] hover:bg-[color:var(--hover)]"
                                aria-label="{{ __('Decrease quantity') }}" tabindex="-1">&minus;</button>
                        <input type="number"
                               name="items[__INDEX__][quantity]"
                               min="0.00001" step="0.00001" inputmode="decimal"
                               class="quantity-input block w-full min-w-0 rounded-none border border-[color:var(--border)] bg-[color:var(--surface)] text-center text-[color:var(--text)] shadow-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--accent)]"
                               placeholder="0"
                               aria-label="{{ __('Quantity') }}">
                        <button type="button"
                                class="qty-plus inline-flex w-9 items-center justify-center rounded-r-md border border-l-0 border-[color:var(--border)] bg-[color:var(--surface)] text-lg font-semibold text-[color:var(--text-soft)] hover:bg-[color:var(--hover)]"
                                aria-label="{{ __('Increase quantity') }}" tabindex="-1">+</button>
                    </div>
                    <p class="qty-hint mt-1 hidden text-xs"></p>
                </div>

                {{-- Unit price --}}
                <div class="col-span-6 sm:col-span-2">
                    <span class="{{ $labelCls }} sm:hidden">{{ __('Unit Price') }} (RWF)</span>
                    <input type="number"
                           name="items[__INDEX__][unit_price]"
                           min="0" step="1" inputmode="decimal"
                           class="price-input {{ $inputCls }}"
                           placeholder="0"
                           aria-label="{{ __('Unit Price') }}">
                </div>

                {{-- Line total --}}
                <div class="col-span-11 flex items-center justify-between sm:col-span-2 sm:block sm:text-right">
                    <span class="text-xs {{ $mut }} sm:hidden">{{ __('Line Total') }}</span>
                    <div class="line-total text-lg font-semibold tabular-nums {{ $txt }}">RWF 0</div>
                </div>

                {{-- Desktop remove button --}}
                <div class="col-span-1 hidden justify-end sm:flex">
                    <button type="button"
                            class="remove-item rounded p-2 text-[color:var(--danger-text)] hover:bg-[color:var(--danger-soft)]"
                            aria-label="{{ __('Remove item') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script nonce="{{ Vite::cspNonce() }}">
        (function () {
            const PRODUCTS  = @json($productData);
            const OLD_ITEMS = @json(array_values((array) old('items', [])));
            const LABELS = {
                stock:        @json(__('Stock')),
                sellingPrice: @json(__('Selling price')),
                noPrice:      @json(__('No price set')),
                inCart:       @json(__('In cart')),
                outOfStock:   @json(__('Out of stock')),
                available:    @json(__('Available')),
                none:         @json(__('No products found')),
                item:         @json(__('item')),
                items:        @json(__('items')),
                minItem:      @json(__('At least one item is required')),
                incomplete:   @json(__('Enter a quantity and unit price for every product.')),
                stockError:   @json(__('Cannot complete sale: One or more items exceed available stock. Please adjust quantities.')),
                overStock:    @json(__('Only :stock available for :name')),
            };

            // Classes swapped at runtime (written in full so Tailwind generates them).
            const ACTIVE_CLASS = 'bg-[color:var(--accent-soft)]';
            const ROW_OK   = ['border-[color:var(--border)]', 'bg-[color:var(--surface-2)]'];
            const ROW_BAD  = ['border-[color:var(--danger-text)]', 'bg-[color:var(--danger-soft)]'];
            const HINT_OK  = ['text-[color:var(--text-muted)]'];
            const HINT_BAD = ['text-[color:var(--danger-text)]', 'font-medium'];
            const FLASH    = ['ring-2', 'ring-[color:var(--accent)]'];

            const form      = document.getElementById('saleForm');
            const container = document.getElementById('itemsContainer');
            const template  = document.getElementById('itemTemplate');
            const emptyCart = document.getElementById('emptyCart');
            const errorBox  = document.getElementById('itemsError');
            const submitBtn = document.getElementById('submitBtn');
            const toast     = document.getElementById('stockToast');
            const toastMsg  = document.getElementById('stockToastMsg');
            const search    = document.getElementById('posSearch');
            const list      = document.getElementById('posList');
            const combo     = document.getElementById('posCombo');

            let itemIndex  = 0;
            let toastTimer = null;
            let activeIdx  = 0;

            /* ---------- helpers ---------- */
            const rowsList    = () => Array.from(container.querySelectorAll('.item-row'));
            const productIdOf = row => row.querySelector('.product-id').value;
            const productOf   = row => PRODUCTS.find(p => String(p.id) === productIdOf(row)) || null;
            const rowFor      = id  => rowsList().find(r => productIdOf(r) === String(id)) || null;
            const qtyOf       = row => parseFloat(row.querySelector('.quantity-input').value) || 0;
            const priceOf     = row => parseFloat(row.querySelector('.price-input').value) || 0;
            const round5      = n   => Math.round(n * 100000) / 100000;

            function formatAmount(value) {
                return 'RWF ' + Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
            function productMeta(p) {
                const parts = [];
                if (p.sku) parts.push(p.sku);
                parts.push(LABELS.stock + ': ' + p.stock);
                return parts.join(' · ');
            }
            function showError(message) { errorBox.textContent = message; errorBox.classList.remove('hidden'); }
            function clearError()       { errorBox.textContent = ''; errorBox.classList.add('hidden'); }

            function showToast(message) {
                toastMsg.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(hideToast, 6000);
            }
            function hideToast() { toast.classList.add('hidden'); }
            document.getElementById('stockToastClose').addEventListener('click', hideToast);

            function overStockMessage(p) {
                return LABELS.overStock.replace(':stock', p.stock).replace(':name', p.name);
            }

            /* ---------- stock check (per cart row) ---------- */
            function checkStock(row) {
                const product = productOf(row);
                const over = !!product && qtyOf(row) > product.stock;
                const hint = row.querySelector('.qty-hint');

                row.classList.remove(...(over ? ROW_OK : ROW_BAD));
                row.classList.add(...(over ? ROW_BAD : ROW_OK));

                if (product) {
                    hint.textContent = LABELS.available + ': ' + product.stock;
                    hint.classList.remove(...(over ? HINT_OK : HINT_BAD));
                    hint.classList.add(...(over ? HINT_BAD : HINT_OK));
                    hint.classList.remove('hidden');
                } else {
                    hint.classList.add('hidden');
                }

                if (over && row.dataset.over !== '1') showToast(overStockMessage(product));
                row.dataset.over = over ? '1' : '';
                return over;
            }

            /* ---------- totals ---------- */
            function updateRow(row) {
                row.querySelector('.line-total').textContent = formatAmount(qtyOf(row) * priceOf(row));
                checkStock(row);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                const rows = rowsList();
                rows.forEach(row => { total += qtyOf(row) * priceOf(row); });
                document.getElementById('grandTotal').textContent = formatAmount(total);
                document.getElementById('itemCount').textContent = rows.length;
                document.getElementById('itemCountLabel').textContent = rows.length === 1 ? LABELS.item : LABELS.items;
                emptyCart.classList.toggle('hidden', rows.length > 0);
            }

            function renumber() {
                rowsList().forEach((row, i) => { row.querySelector('.row-number').textContent = i + 1; });
            }

            function flash(row) {
                row.classList.add(...FLASH);
                setTimeout(() => row.classList.remove(...FLASH), 700);
            }

            /* ---------- cart ---------- */
            function createRow(product, qty, price) {
                container.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, itemIndex++));
                const row = container.lastElementChild;
                row.querySelector('.product-id').value = product.id;
                row.querySelector('.product-name').textContent = product.name;
                row.querySelector('.product-meta').textContent = productMeta(product);
                row.querySelector('.quantity-input').value = qty;
                row.querySelector('.price-input').value = price;
                renumber();
                updateRow(row);
                return row;
            }

            // Add a product to the cart, or bump its quantity if it is already there.
            function addProduct(product, qty = 1) {
                clearError();
                const existing = rowFor(product.id);

                if (existing) {
                    const wanted = round5(qtyOf(existing) + qty);
                    if (wanted > product.stock) {
                        showToast(overStockMessage(product));
                        flash(existing);
                        return existing;
                    }
                    existing.querySelector('.quantity-input').value = wanted;
                    updateRow(existing);
                    flash(existing);
                    return existing;
                }

                if (qty > product.stock) {
                    showToast(overStockMessage(product));
                    qty = product.stock;
                }
                if (!(qty > 0)) return null;

                const row = createRow(product, qty, product.price > 0 ? product.price : '');
                flash(row);
                row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                return row;
            }

            function removeRow(row) {
                row.remove();
                renumber();
                updateGrandTotal();
                search.focus();
            }

            function stepQty(row, delta) {
                const product = productOf(row);
                const next = round5(qtyOf(row) + delta);
                if (next <= 0) return;
                if (product && delta > 0 && next > product.stock) {
                    showToast(overStockMessage(product));
                    return;
                }
                row.querySelector('.quantity-input').value = next;
                updateRow(row);
            }

            /* ---------- POS search ---------- */
            // "5*rice" -> quantity 5, query "rice"
            function parseQuery(raw) {
                const m = raw.match(/^\s*(\d+(?:\.\d+)?)\s*\*\s*(.*)$/);
                return m ? { qty: parseFloat(m[1]), q: m[2].trim() } : { qty: 1, q: raw.trim() };
            }

            // Exact SKU first, then SKU/name starts-with, then contains.
            function rank(p, q) {
                const name = p.name.toLowerCase();
                const sku  = p.sku ? String(p.sku).toLowerCase() : '';
                if (sku && sku === q) return 0;
                if (sku.startsWith(q)) return 1;
                if (name.startsWith(q)) return 2;
                if (sku.includes(q)) return 3;
                if (name.includes(q)) return 4;
                return -1;
            }

            function searchProducts(q) {
                q = q.toLowerCase();
                if (!q) return PRODUCTS.slice(0, 50);
                return PRODUCTS
                    .map(p => ({ p, r: rank(p, q) }))
                    .filter(x => x.r >= 0)
                    .sort((a, b) => a.r - b.r || a.p.name.localeCompare(b.p.name))
                    .slice(0, 50)
                    .map(x => x.p);
            }

            const enabledOptions = () => Array.from(list.querySelectorAll('.product-option:not([data-disabled])'));

            function setActive(index) {
                const options = enabledOptions();
                if (!options.length) { activeIdx = -1; return; }
                index = Math.max(0, Math.min(index, options.length - 1));
                options.forEach(o => o.classList.remove(ACTIVE_CLASS));
                options[index].classList.add(ACTIVE_CLASS);
                options[index].scrollIntoView({ block: 'nearest' });
                activeIdx = index;
            }

            function closeList() {
                list.classList.add('hidden');
                search.setAttribute('aria-expanded', 'false');
            }

            function renderList() {
                const { q } = parseQuery(search.value);
                const matches = searchProducts(q);

                list.innerHTML = '';

                if (!matches.length) {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-3 text-sm text-[color:var(--text-muted)]';
                    li.textContent = LABELS.none;
                    list.appendChild(li);
                }

                matches.forEach(p => {
                    const inCartRow = rowFor(p.id);
                    const isOut = p.stock <= 0;

                    const li = document.createElement('li');
                    li.className = 'product-option flex items-center justify-between gap-3 px-3 py-2.5 text-sm ' +
                        (isOut ? 'cursor-not-allowed opacity-50' : 'cursor-pointer hover:bg-[color:var(--hover)]');
                    li.dataset.id = p.id;
                    li.setAttribute('role', 'option');
                    if (isOut) li.dataset.disabled = '1';

                    const left = document.createElement('div');
                    left.className = 'min-w-0';
                    const name = document.createElement('div');
                    name.className = 'truncate font-medium text-[color:var(--text)]';
                    name.textContent = p.name;
                    const sub = document.createElement('div');
                    sub.className = 'truncate text-xs ' + (isOut ? 'text-[color:var(--danger-text)]' : 'text-[color:var(--text-muted)]');
                    const stockText = isOut ? LABELS.outOfStock : LABELS.stock + ': ' + p.stock;
                    const cartText  = inCartRow ? LABELS.inCart + ': ' + qtyOf(inCartRow) : '';
                    sub.textContent = [p.sku, stockText, cartText].filter(Boolean).join(' · ');
                    left.append(name, sub);

                    const right = document.createElement('div');
                    right.className = 'shrink-0 text-right';
                    const price = document.createElement('div');
                    price.className = 'font-semibold tabular-nums text-[color:var(--accent-text)]';
                    price.textContent = p.price > 0 ? formatAmount(p.price) : LABELS.noPrice;
                    right.appendChild(price);

                    li.append(left, right);
                    list.appendChild(li);
                });

                list.classList.remove('hidden');
                search.setAttribute('aria-expanded', 'true');
                setActive(0);
            }

            // Add the chosen product, reset the search and keep the cursor there for the next one.
            function pick(productId) {
                const product = PRODUCTS.find(p => String(p.id) === String(productId));
                if (!product) return;
                const { qty } = parseQuery(search.value);
                addProduct(product, qty > 0 ? qty : 1);
                search.value = '';
                closeList();
                search.focus();
            }

            search.addEventListener('input', renderList);
            search.addEventListener('focus', renderList);
            search.addEventListener('blur', closeList);

            search.addEventListener('keydown', function (e) {
                const open = !list.classList.contains('hidden');
                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        open ? setActive(activeIdx + 1) : renderList();
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        if (open) setActive(activeIdx - 1);
                        break;
                    case 'Enter': {
                        e.preventDefault(); // never submit the form from the search box
                        const option = enabledOptions()[activeIdx];
                        if (open && option) pick(option.dataset.id);
                        break;
                    }
                    case 'Escape':
                        if (open) closeList(); else search.value = '';
                        break;
                    case 'Tab':
                        closeList();
                        break;
                }
            });

            // mousedown (not click) so the input does not lose focus before the choice registers.
            list.addEventListener('mousedown', function (e) {
                const option = e.target.closest('.product-option');
                e.preventDefault();
                if (!option || option.dataset.disabled) return;
                pick(option.dataset.id);
            });

            // F2 jumps back to the search from anywhere on the page.
            document.addEventListener('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    search.scrollIntoView({ block: 'center', behavior: 'smooth' });
                    search.focus();
                }
            });

            /* ---------- cart events (delegated, no inline handlers) ---------- */
            container.addEventListener('input', function (e) {
                const row = e.target.closest('.item-row');
                if (!row) return;
                if (e.target.matches('.quantity-input, .price-input')) updateRow(row);
            });

            container.addEventListener('click', function (e) {
                const row = e.target.closest('.item-row');
                if (!row) return;
                if (e.target.closest('.remove-item')) removeRow(row);
                else if (e.target.closest('.qty-plus'))  stepQty(row, 1);
                else if (e.target.closest('.qty-minus')) stepQty(row, -1);
            });

            // Enter in quantity or price returns to the search, ready for the next product.
            container.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' || !e.target.matches('.quantity-input, .price-input')) return;
                e.preventDefault();
                search.focus();
            });

            // Select the number on focus so typing replaces it.
            container.addEventListener('focusin', function (e) {
                if (e.target.matches('.quantity-input, .price-input')) e.target.select();
            });

            /* ---------- submit: validate and block double submits ---------- */
            form.addEventListener('submit', function (e) {
                const rows = rowsList();
                let badInput = null;
                let overRow = null;

                for (const row of rows) {
                    const qty   = row.querySelector('.quantity-input');
                    const price = row.querySelector('.price-input');
                    if (!badInput && !(parseFloat(qty.value) > 0))                          badInput = qty;
                    if (!badInput && !(price.value !== '' && parseFloat(price.value) >= 0)) badInput = price;
                    if (!overRow && checkStock(row))                                         overRow = row;
                }

                if (!rows.length || badInput || overRow) {
                    e.preventDefault();

                    if (!rows.length)   showError(LABELS.minItem);
                    else if (badInput)  showError(LABELS.incomplete);
                    else                showError(LABELS.stockError);

                    if (overRow && !badInput) showToast(overStockMessage(productOf(overRow)));

                    const target = badInput
                        || (overRow && overRow.querySelector('.quantity-input'))
                        || search;
                    target.focus();
                    target.scrollIntoView({ block: 'center', behavior: 'smooth' });
                    return;
                }

                clearError();
                renumber();
                submitBtn.disabled = true; // a double click would record the sale (and take stock) twice
            });

            // Back/forward cache can restore the page with the button still disabled.
            window.addEventListener('pageshow', function () { submitBtn.disabled = false; });

            /* ---------- init ---------- */
            document.addEventListener('DOMContentLoaded', function () {
                // Restore cart rows after a failed server validation.
                OLD_ITEMS.forEach(item => {
                    const product = PRODUCTS.find(p => String(p.id) === String(item.product_id));
                    if (!product || rowFor(product.id)) return;
                    createRow(product, item.quantity ?? '', item.unit_price ?? '');
                });
                renumber();
                updateGrandTotal();
                search.focus();
                closeList(); // focus opens the list; start with it closed
            });
        })();
    </script>
</x-app-layout>