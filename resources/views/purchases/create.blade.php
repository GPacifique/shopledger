<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('purchases.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Record New Purchase') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm" novalidate>
                @csrf

                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700">{{ __('Supplier') }}</label>
                                <select name="supplier_id" id="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Supplier (Optional)') }}</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ ($selectedSupplier && $selectedSupplier->id == $supplier->id) || old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">{{ __('Purchase Date') }} *</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Section (no overflow-hidden here, or the product dropdown gets clipped) -->
                <div class="bg-white shadow-sm rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Purchase Items') }}</h3>
                        <button type="button" id="addItemBtn" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ __('Add Item') }}
                        </button>
                    </div>
                    <div class="p-6">
                        <div id="itemsContainer" class="space-y-4">
                            <!-- Item rows are added here -->
                        </div>
                        <p id="itemsError" class="hidden mt-3 text-sm text-red-600" role="alert"></p>
                        @error('items')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if ($errors->has('items.*'))
                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('items.*') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-medium text-gray-900">{{ __('Total Amount') }}:</span>
                            <span id="grandTotal" class="text-2xl font-bold text-red-600">RWF 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        {{ __('Record Purchase') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Row template. No inline onclick/oninput/onchange: a nonce-based CSP blocks them. --}}
    <template id="itemTemplate">
        <div class="item-row bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-2 md:grid-cols-12 gap-4">
                <div class="col-span-2 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Product') }} *</label>
                    <div class="product-combo relative">
                        <input type="text"
                               class="product-search w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="{{ __('Search product...') }}"
                               autocomplete="off"
                               role="combobox"
                               aria-autocomplete="list"
                               aria-expanded="false">
                        <input type="hidden" class="product-id" name="items[__INDEX__][product_id]" value="">
                        <ul class="product-list hidden absolute z-30 mt-1 max-h-64 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg"
                            role="listbox"></ul>
                    </div>
                    <p class="stock-hint mt-1 text-xs text-gray-500"></p>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Quantity') }} *</label>
                    <input type="number"
                           name="items[__INDEX__][quantity]"
                           min="0.01"
                           step="0.01"
                           inputmode="decimal"
                           class="quantity-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0.00">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Unit Cost (RWF)') }} *</label>
                    <input type="number"
                           name="items[__INDEX__][unit_cost]"
                           min="0.01"
                           step="0.01"
                           inputmode="decimal"
                           class="cost-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0.00">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Line Total') }}</label>
                    <div class="line-total mt-2 text-lg font-semibold text-gray-900">RWF 0.00</div>
                </div>
                <div class="col-span-1 md:col-span-1 flex items-end justify-end md:justify-start">
                    <button type="button" class="remove-item mb-1 p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded" aria-label="{{ __('Remove item') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>

    @php
        $productData = $products->map(fn ($p) => [
            'id'    => $p->id,
            'name'  => $p->name,
            'stock' => $p->stock,
            'cost'  => (float) ($p->buying_price ?? 0),
        ])->values();
    @endphp

    <script nonce="{{ Vite::cspNonce() }}">
        (function () {
            const PRODUCTS  = @json($productData);
            const OLD_ITEMS = @json(array_values((array) old('items', [])));
            const LABELS = {
                stock:      @json(__('Stock')),
                added:      @json(__('Already added')),
                none:       @json(__('No products found')),
                noItems:    @json(__('Add at least one product to record a purchase.')),
                incomplete: @json(__('Enter a quantity and unit cost for every product.')),
            };

            const form      = document.getElementById('purchaseForm');
            const container = document.getElementById('itemsContainer');
            const template  = document.getElementById('itemTemplate');
            const errorBox  = document.getElementById('itemsError');
            let itemIndex = 0;

            /* ---------- helpers ---------- */
            const rowsList = () => Array.from(container.querySelectorAll('.item-row'));
            const productIdOf = row => row.querySelector('.product-id').value;

            function formatAmount(value) {
                return 'RWF ' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function takenIds(exceptRow) {
                return new Set(rowsList().filter(r => r !== exceptRow).map(productIdOf).filter(Boolean));
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            }
            function clearError() {
                errorBox.textContent = '';
                errorBox.classList.add('hidden');
            }

            /* ---------- totals ---------- */
            function updateItemTotal(row) {
                const qty  = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                row.querySelector('.line-total').textContent = formatAmount(qty * cost);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                rowsList().forEach(row => {
                    const qty  = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                    total += qty * cost;
                });
                document.getElementById('grandTotal').textContent = formatAmount(total);
            }

            /* ---------- rows ---------- */
            function addItem(prefill = null, focus = true) {
                container.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, itemIndex++));
                const row = container.lastElementChild;

                if (prefill) {
                    const product = PRODUCTS.find(p => String(p.id) === String(prefill.product_id));
                    if (product) applyProduct(row, product);
                    row.querySelector('.quantity-input').value = prefill.quantity ?? '';
                    row.querySelector('.cost-input').value = prefill.unit_cost ?? '';
                    updateItemTotal(row);
                }
                if (focus) row.querySelector('.product-search').focus();
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
                updateGrandTotal();
            }

            /* ---------- product picker ---------- */
            function applyProduct(row, product) {
                const search = row.querySelector('.product-search');
                row.querySelector('.product-id').value = product.id;
                row.dataset.selectedName = product.name;
                search.value = product.name;
                row.querySelector('.stock-hint').textContent = LABELS.stock + ': ' + product.stock;
            }

            function closeList(row) {
                const list = row.querySelector('.product-list');
                list.classList.add('hidden');
                row.querySelector('.product-search').setAttribute('aria-expanded', 'false');
            }
            function closeAllLists(exceptRow) {
                rowsList().forEach(r => { if (r !== exceptRow) closeList(r); });
            }

            function enabledOptions(row) {
                return Array.from(row.querySelectorAll('.product-option:not([data-disabled])'));
            }

            function setActive(row, index) {
                const options = enabledOptions(row);
                if (!options.length) { row.dataset.active = -1; return; }
                index = Math.max(0, Math.min(index, options.length - 1));
                options.forEach(o => o.classList.remove('bg-indigo-100'));
                options[index].classList.add('bg-indigo-100');
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
                    .filter(p => showAll || !query || p.name.toLowerCase().includes(query))
                    .slice(0, 50);

                list.innerHTML = '';

                if (!matches.length) {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 text-sm text-gray-500';
                    li.textContent = LABELS.none;
                    list.appendChild(li);
                }

                matches.forEach(p => {
                    const isTaken = taken.has(String(p.id));
                    const li = document.createElement('li');
                    li.className = 'product-option flex items-center justify-between gap-3 px-3 py-2 text-sm ' +
                        (isTaken ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-indigo-50');
                    li.dataset.id = p.id;
                    li.setAttribute('role', 'option');
                    if (isTaken) li.dataset.disabled = '1';

                    const name = document.createElement('span');
                    name.className = 'truncate';
                    name.textContent = p.name;

                    const meta = document.createElement('span');
                    meta.className = 'shrink-0 text-xs text-gray-500';
                    meta.textContent = isTaken ? LABELS.added : LABELS.stock + ': ' + p.stock;

                    li.append(name, meta);
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

                // Auto-fill the unit cost (stays editable); leave empty when the product has no cost yet.
                row.querySelector('.cost-input').value = product.cost > 0 ? product.cost.toFixed(2) : '';

                closeList(row);
                clearError();
                updateItemTotal(row);

                // New empty row appears below as soon as the last row has a product.
                ensureTrailingBlank();

                const qty = row.querySelector('.quantity-input');
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
                    row.querySelector('.stock-hint').textContent = '';
                    renderList(row);
                } else if (e.target.matches('.quantity-input, .cost-input')) {
                    updateItemTotal(row);
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
                if (!e.target.matches('.product-search')) return;
                const row  = e.target.closest('.item-row');
                const list = row.querySelector('.product-list');
                const open = !list.classList.contains('hidden');
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
                        e.preventDefault(); // never submit the form from the search box
                        const option = enabledOptions(row)[active];
                        if (open && option) chooseProduct(row, option.dataset.id);
                        break;
                    }
                    case 'Escape':
                        closeList(row);
                        break;
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
                addItem(null, true);
            });

            /* ---------- submit: drop empty rows, then validate ---------- */
            form.addEventListener('submit', function (e) {
                rowsList().forEach(row => { if (!productIdOf(row)) row.remove(); });

                const rows = rowsList();
                let badInput = null;

                for (const row of rows) {
                    const qty  = row.querySelector('.quantity-input');
                    const cost = row.querySelector('.cost-input');
                    if (!(parseFloat(qty.value) > 0))  { badInput = qty;  break; }
                    if (!(parseFloat(cost.value) > 0)) { badInput = cost; break; }
                }

                if (!rows.length || badInput) {
                    e.preventDefault();
                    showError(rows.length ? LABELS.incomplete : LABELS.noItems);
                    ensureTrailingBlank();
                    (badInput || container.querySelector('.product-search')).focus();
                    return;
                }
                clearError();
            });

            /* ---------- init ---------- */
            document.addEventListener('DOMContentLoaded', function () {
                OLD_ITEMS.forEach(item => addItem(item, false)); // restore rows after a failed validation
                ensureTrailingBlank();
                updateGrandTotal();
            });
        })();
    </script>
</x-app-layout>