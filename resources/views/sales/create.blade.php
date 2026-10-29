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
                <p class="mt-0.5 hidden text-xs sm:block {{ $mut }}">{{ __('Add items, set payment details, and confirm the sale') }}</p>
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
                        <p class="mt-1 text-sm {{ $mut }}">
                            {{ __('Search a product by name or SKU. Its selling price and stock are shown in the list so you can tell similar products apart.') }}
                        </p>
                    </div>

                    <div class="p-4 sm:p-6">
                        <div id="itemsContainer" class="space-y-4">
                            {{-- Product rows are added here --}}
                        </div>

                        <p id="itemsError" class="mt-3 hidden text-sm {{ $dng }}" role="alert"></p>
                        @error('items')
                            <p class="mt-3 text-sm {{ $dng }}">{{ $message }}</p>
                        @enderror

                        {{-- Add button sits under the list, where the eye ends up --}}
                        <button type="button" id="addItemBtn"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-[color:var(--border)] px-4 py-3 text-sm font-medium text-[color:var(--accent-text)] hover:bg-[color:var(--accent-soft)]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                            </svg>
                            {{ __('Add another product') }}
                        </button>
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

    {{-- Row template. No inline onclick/oninput/onchange: a nonce-based CSP blocks them. --}}
    <template id="itemTemplate">
        <div class="item-row rounded-lg border border-[color:var(--border)] bg-[color:var(--surface-2)] p-4">
            <div class="flex items-start gap-3">
                <span class="row-number mt-7 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[color:var(--accent-soft)] text-xs font-semibold text-[color:var(--accent-text)]">1</span>

                <div class="min-w-0 flex-1">
                    {{-- Product picker --}}
                    <label class="{{ $labelCls }}">{{ __('Product') }} *</label>
                    <div class="product-combo relative">
                        <input type="text"
                               class="product-search {{ $inputCls }}"
                               placeholder="{{ __('Search by name or SKU...') }}"
                               autocomplete="off"
                               role="combobox"
                               aria-autocomplete="list"
                               aria-expanded="false"
                               aria-label="{{ __('Product') }}">
                        <input type="hidden" class="product-id" name="items[__INDEX__][product_id]" value="">
                        <ul class="product-list absolute z-30 mt-1 hidden max-h-72 w-full overflow-auto rounded-md border border-[color:var(--border)] bg-[color:var(--surface)] shadow-lg"
                            role="listbox"></ul>
                    </div>
                    <p class="product-meta mt-1 text-xs {{ $mut }}"></p>

                    {{-- Numbers --}}
                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div>
                            <label class="{{ $labelCls }}">{{ __('Quantity') }} *</label>
                            <input type="number"
                                   name="items[__INDEX__][quantity]"
                                   min="0.00001" step="0.00001" inputmode="decimal"
                                   class="quantity-input {{ $inputCls }}"
                                   placeholder="0"
                                   aria-label="{{ __('Quantity') }}">
                            <p class="qty-hint mt-1 hidden text-xs"></p>
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">{{ __('Unit Price') }} (RWF) *</label>
                            <input type="number"
                                   name="items[__INDEX__][unit_price]"
                                   min="0" step="1" inputmode="decimal"
                                   class="price-input {{ $inputCls }}"
                                   placeholder="0"
                                   aria-label="{{ __('Unit Price') }}">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <span class="{{ $labelCls }}">{{ __('Line Total') }}</span>
                            <div class="line-total pt-2 text-lg font-semibold tabular-nums {{ $txt }}">RWF 0</div>
                        </div>
                    </div>
                </div>

                <button type="button"
                        class="remove-item mt-6 shrink-0 rounded p-2 text-[color:var(--danger-text)] hover:bg-[color:var(--danger-soft)]"
                        aria-label="{{ __('Remove item') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
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
                added:        @json(__('Already added')),
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
            const ROW_OK  = ['border-[color:var(--border)]', 'bg-[color:var(--surface-2)]'];
            const ROW_BAD = ['border-[color:var(--danger-text)]', 'bg-[color:var(--danger-soft)]'];
            const HINT_OK  = ['text-[color:var(--text-muted)]'];
            const HINT_BAD = ['text-[color:var(--danger-text)]', 'font-medium'];

            const form      = document.getElementById('saleForm');
            const container = document.getElementById('itemsContainer');
            const template  = document.getElementById('itemTemplate');
            const errorBox  = document.getElementById('itemsError');
            const submitBtn = document.getElementById('submitBtn');
            const toast     = document.getElementById('stockToast');
            const toastMsg  = document.getElementById('stockToastMsg');
            let itemIndex = 0;
            let toastTimer = null;

            /* ---------- helpers ---------- */
            const rowsList    = () => Array.from(container.querySelectorAll('.item-row'));
            const productIdOf = row => row.querySelector('.product-id').value;
            const productOf   = row => PRODUCTS.find(p => String(p.id) === productIdOf(row)) || null;

            function formatAmount(value) {
                return 'RWF ' + Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
            function productMeta(p) {
                const parts = [];
                if (p.sku) parts.push(p.sku);
                parts.push(LABELS.stock + ': ' + p.stock);
                if (p.price > 0) parts.push(LABELS.sellingPrice + ': ' + formatAmount(p.price));
                return parts.join(' · ');
            }
            function takenIds(exceptRow) {
                return new Set(rowsList().filter(r => r !== exceptRow).map(productIdOf).filter(Boolean));
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

            /* ---------- stock check ---------- */
            function checkStock(row, notify) {
                const product = productOf(row);
                const qty  = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const over = !!product && qty > product.stock;
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

                if (over && notify && row.dataset.over !== '1') {
                    showToast(LABELS.overStock.replace(':stock', product.stock).replace(':name', product.name));
                }
                row.dataset.over = over ? '1' : '';
                return over;
            }

            /* ---------- totals ---------- */
            function updateItemTotal(row, notify = false) {
                const qty   = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                row.querySelector('.line-total').textContent = formatAmount(qty * price);
                checkStock(row, notify);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                let count = 0;
                rowsList().forEach(row => {
                    const qty   = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    total += qty * price;
                    if (productIdOf(row)) count++;
                });
                document.getElementById('grandTotal').textContent = formatAmount(total);
                document.getElementById('itemCount').textContent = count;
                document.getElementById('itemCountLabel').textContent = count === 1 ? LABELS.item : LABELS.items;
            }

            /* ---------- rows ---------- */
            function renumber() {
                rowsList().forEach((row, i) => { row.querySelector('.row-number').textContent = i + 1; });
            }

            function focusRow(row) {
                row.querySelector('.product-search').focus();
                row.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }

            function addItem(prefill = null, focus = true) {
                container.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, itemIndex++));
                const row = container.lastElementChild;

                if (prefill) {
                    const product = PRODUCTS.find(p => String(p.id) === String(prefill.product_id));
                    if (product) applyProduct(row, product);
                    row.querySelector('.quantity-input').value = prefill.quantity ?? '';
                    row.querySelector('.price-input').value = prefill.unit_price ?? '';
                    updateItemTotal(row, false);
                }
                renumber();
                if (focus) focusRow(row);
                return row;
            }

            // Always keep one empty row at the bottom, ready for the next product.
            function ensureTrailingBlank() {
                const rows = rowsList();
                const last = rows[rows.length - 1];
                if (!last || productIdOf(last)) addItem(null, false);
            }

            function removeRow(row) {
                row.remove();
                if (!rowsList().length) addItem(null, false);
                ensureTrailingBlank();
                renumber();
                updateGrandTotal();
            }

            /* ---------- product picker ---------- */
            function applyProduct(row, product) {
                row.querySelector('.product-id').value = product.id;
                row.dataset.selectedName = product.name;
                row.querySelector('.product-search').value = product.name;
                row.querySelector('.product-meta').textContent = productMeta(product);
            }

            function closeList(row) {
                row.querySelector('.product-list').classList.add('hidden');
                row.querySelector('.product-search').setAttribute('aria-expanded', 'false');
            }
            function closeAllLists(exceptRow) {
                rowsList().forEach(r => { if (r !== exceptRow) closeList(r); });
            }

            const enabledOptions = row => Array.from(row.querySelectorAll('.product-option:not([data-disabled])'));

            function setActive(row, index) {
                const options = enabledOptions(row);
                if (!options.length) { row.dataset.active = -1; return; }
                index = Math.max(0, Math.min(index, options.length - 1));
                options.forEach(o => o.classList.remove(ACTIVE_CLASS));
                options[index].classList.add(ACTIVE_CLASS);
                options[index].scrollIntoView({ block: 'nearest' });
                row.dataset.active = index;
            }

            function renderList(row) {
                const input = row.querySelector('.product-search');
                const list  = row.querySelector('.product-list');
                const query = input.value.trim().toLowerCase();
                // Re-opening on an already chosen product shows the whole list, not just that one.
                const showAll = productIdOf(row) && input.value === row.dataset.selectedName;
                const taken = takenIds(row);

                const matches = PRODUCTS
                    .filter(p => showAll || !query ||
                        p.name.toLowerCase().includes(query) ||
                        (p.sku && String(p.sku).toLowerCase().includes(query)))
                    .slice(0, 50);

                list.innerHTML = '';

                if (!matches.length) {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-3 text-sm text-[color:var(--text-muted)]';
                    li.textContent = LABELS.none;
                    list.appendChild(li);
                }

                matches.forEach(p => {
                    const isTaken = taken.has(String(p.id));
                    const isOut   = p.stock <= 0;
                    const blocked = isTaken || isOut;

                    const li = document.createElement('li');
                    li.className = 'product-option flex items-center justify-between gap-3 px-3 py-2.5 text-sm ' +
                        (blocked ? 'cursor-not-allowed opacity-50' : 'cursor-pointer hover:bg-[color:var(--hover)]');
                    li.dataset.id = p.id;
                    li.setAttribute('role', 'option');
                    if (blocked) li.dataset.disabled = '1';

                    // Left: name + SKU/stock
                    const left = document.createElement('div');
                    left.className = 'min-w-0';
                    const name = document.createElement('div');
                    name.className = 'truncate font-medium text-[color:var(--text)]';
                    name.textContent = p.name;
                    const sub = document.createElement('div');
                    sub.className = 'truncate text-xs ' + (isOut ? 'text-[color:var(--danger-text)]' : 'text-[color:var(--text-muted)]');
                    const stockText = isOut ? LABELS.outOfStock : (isTaken ? LABELS.added : LABELS.stock + ': ' + p.stock);
                    sub.textContent = [p.sku, stockText].filter(Boolean).join(' · ');
                    left.append(name, sub);

                    // Right: selling price, so look-alike products are easy to tell apart
                    const right = document.createElement('div');
                    right.className = 'shrink-0 text-right';
                    const price = document.createElement('div');
                    price.className = 'font-semibold tabular-nums text-[color:var(--accent-text)]';
                    price.textContent = p.price > 0 ? formatAmount(p.price) : LABELS.noPrice;
                    right.appendChild(price);
                    if (p.price > 0) {
                        const caption = document.createElement('div');
                        caption.className = 'text-[11px] text-[color:var(--text-muted)]';
                        caption.textContent = LABELS.sellingPrice;
                        right.appendChild(caption);
                    }

                    li.append(left, right);
                    list.appendChild(li);
                });

                list.classList.remove('hidden');
                input.setAttribute('aria-expanded', 'true');
                setActive(row, 0);
            }

            function chooseProduct(row, productId) {
                const product = PRODUCTS.find(p => String(p.id) === String(productId));
                if (!product) return;

                applyProduct(row, product);

                // Auto-fill the selling price (stays editable) and start at quantity 1.
                row.querySelector('.price-input').value = product.price > 0 ? product.price : '';
                const qty = row.querySelector('.quantity-input');
                if (!(parseFloat(qty.value) > 0)) qty.value = 1;

                closeList(row);
                clearError();
                updateItemTotal(row, true);

                // A new empty row appears as soon as the last row has a product.
                ensureTrailingBlank();

                qty.focus();
                qty.select();
            }

            /* ---------- events (delegated, no inline handlers) ---------- */
            container.addEventListener('input', function (e) {
                const row = e.target.closest('.item-row');
                if (!row) return;

                if (e.target.matches('.product-search')) {
                    // Typing again invalidates the previous selection until a new one is picked.
                    row.querySelector('.product-id').value = '';
                    row.dataset.selectedName = '';
                    row.querySelector('.product-meta').textContent = '';
                    renderList(row);
                    updateItemTotal(row, false);
                } else if (e.target.matches('.quantity-input')) {
                    updateItemTotal(row, true);
                } else if (e.target.matches('.price-input')) {
                    updateItemTotal(row, false);
                }
            });

            container.addEventListener('focusin', function (e) {
                if (!e.target.matches('.product-search')) return;
                const row = e.target.closest('.item-row');
                closeAllLists(row);
                renderList(row);
            });

            container.addEventListener('focusout', function (e) {
                if (!e.target.matches('.product-search')) return;
                const row = e.target.closest('.item-row');
                closeList(row);
                // Drop half-typed text that never became a selection.
                e.target.value = productIdOf(row) ? (row.dataset.selectedName || '') : '';
                updateGrandTotal();
            });

            // mousedown (not click) so the input does not lose focus before the choice registers.
            container.addEventListener('mousedown', function (e) {
                const option = e.target.closest('.product-option');
                if (!option) return;
                e.preventDefault();
                if (option.dataset.disabled) return;
                chooseProduct(option.closest('.item-row'), option.dataset.id);
            });

            container.addEventListener('keydown', function (e) {
                const row = e.target.closest('.item-row');
                if (!row) return;

                // Quantity -> Enter -> unit price -> Enter -> next product (never submits the form by accident)
                if (e.target.matches('.quantity-input, .price-input')) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();
                    if (e.target.matches('.quantity-input')) {
                        const price = row.querySelector('.price-input');
                        price.focus();
                        price.select();
                    } else {
                        const rows = rowsList();
                        const next = rows[rows.indexOf(row) + 1];
                        next ? focusRow(next) : addItem(null, true);
                    }
                    return;
                }

                if (!e.target.matches('.product-search')) return;

                const list   = row.querySelector('.product-list');
                const open   = !list.classList.contains('hidden');
                const active = parseInt(row.dataset.active ?? '0', 10);

                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        open ? setActive(row, active + 1) : renderList(row);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        if (open) setActive(row, active - 1);
                        break;
                    case 'Enter': {
                        e.preventDefault();
                        const option = enabledOptions(row)[active];
                        if (open && option) chooseProduct(row, option.dataset.id);
                        break;
                    }
                    case 'Escape':
                    case 'Tab':
                        closeList(row);
                        break;
                }
            });

            container.addEventListener('click', function (e) {
                const btn = e.target.closest('.remove-item');
                if (btn) removeRow(btn.closest('.item-row'));
            });

            document.addEventListener('mousedown', function (e) {
                if (!e.target.closest('.product-combo')) closeAllLists(null);
            });

            document.getElementById('addItemBtn').addEventListener('click', function () {
                // Reuse the empty row at the bottom instead of stacking blank rows.
                const rows = rowsList();
                const last = rows[rows.length - 1];
                if (last && !productIdOf(last)) focusRow(last);
                else addItem(null, true);
            });

            /* ---------- submit: drop empty rows, validate, block double submits ---------- */
            form.addEventListener('submit', function (e) {
                rowsList().forEach(row => { if (!productIdOf(row)) row.remove(); });

                const rows = rowsList();
                let badInput = null;
                let overRow = null;

                for (const row of rows) {
                    const qty   = row.querySelector('.quantity-input');
                    const price = row.querySelector('.price-input');
                    if (!badInput && !(parseFloat(qty.value) > 0))                     badInput = qty;
                    if (!badInput && !(price.value !== '' && parseFloat(price.value) >= 0)) badInput = price;
                    if (!overRow && checkStock(row, false))                            overRow = row;
                }

                if (!rows.length || badInput || overRow) {
                    e.preventDefault();

                    if (!rows.length)   showError(LABELS.minItem);
                    else if (badInput)  showError(LABELS.incomplete);
                    else                showError(LABELS.stockError);

                    if (overRow && !badInput) {
                        const p = productOf(overRow);
                        showToast(LABELS.overStock.replace(':stock', p.stock).replace(':name', p.name));
                    }

                    ensureTrailingBlank();
                    renumber();

                    const target = badInput
                        || (overRow && overRow.querySelector('.quantity-input'))
                        || rowsList()[0].querySelector('.product-search');
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
                OLD_ITEMS.forEach(item => addItem(item, false)); // restore rows after a failed server validation
                ensureTrailingBlank();
                renumber();
                updateGrandTotal();
            });
        })();
    </script>
</x-app-layout>