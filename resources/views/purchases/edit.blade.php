@php
    // Pre-fill: old() wins after a failed validation, otherwise use the saved items from the database
    $rows = old('items', $purchase->items->map(fn ($i) => [
        'product_id' => $i->product_id,
        'quantity'   => (float) $i->quantity,
        'unit_cost'  => (float) $i->unit_cost,
    ])->values()->all());

    $selectedSupplierId = old('supplier_id', $purchase->supplier_id);
    $purchaseDate = old('purchase_date', optional($purchase->purchase_date)->format('Y-m-d'));
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-red-800">
                    {{ __('Edit Purchase') }} #{{ $purchase->id }}
                </h2>
                <p class="mt-1 text-sm text-blue-500">
                    {{ __('Changes will adjust stock by the difference only.') }}
                </p>
            </div>
            <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ __('Please fix the following errors:') }}</p>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('purchases.update', $purchase) }}" id="purchase-form">
                @csrf
                @method('PUT')

                <!-- Purchase details -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Supplier') }}</label>
                            <select name="supplier_id" id="supplier_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('No supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected((string) $selectedSupplierId === (string) $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Purchase Date') }}</label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ $purchaseDate }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Items') }}</h3>
                            <button type="button" id="add-item"
                                    class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                + {{ __('Add Item') }}
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ __('Quantity') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">{{ __('Unit Cost') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-40">{{ __('Line Total') }}</th>
                                        <th class="px-4 py-3 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body" class="divide-y divide-gray-200">
                                    @foreach ($rows as $index => $row)
                                        <tr class="item-row">
                                            <td class="px-4 py-3">
                                                <select name="items[{{ $index }}][product_id]" required
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">{{ __('Select product') }}</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}" @selected((string) ($row['product_id'] ?? '') === (string) $product->id)>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" min="0.01" required
                                                       name="items[{{ $index }}][quantity]" value="{{ $row['quantity'] ?? '' }}"
                                                       class="qty-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" min="0.01" required
                                                       name="items[{{ $index }}][unit_cost]" value="{{ $row['unit_cost'] ?? '' }}"
                                                       class="cost-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                                <span class="line-total">0</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button type="button" class="remove-item text-red-600 hover:text-red-900 text-sm">{{ __('Remove') }}</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">{{ __('Total') }}</td>
                                        <td class="px-4 py-3 text-right text-lg font-semibold text-red-600">
                                            <span id="grand-total">0</span> RWF
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        {{ __('Update Purchase') }}
                    </button>
                </div>
            </form>

            {{-- Blank row used when clicking "Add Item" --}}
            <template id="item-template">
                <tr class="item-row">
                    <td class="px-4 py-3">
                        <select name="items[__INDEX__][product_id]" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Select product') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" step="0.01" min="0.01" required name="items[__INDEX__][quantity]"
                               class="qty-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" step="0.01" min="0.01" required name="items[__INDEX__][unit_cost]"
                               class="cost-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                        <span class="line-total">0</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" class="remove-item text-red-600 hover:text-red-900 text-sm">{{ __('Remove') }}</button>
                    </td>
                </tr>
            </template>
        </div>
    </div>

    {{-- No inline event handlers. If your CSP requires a nonce for <script>, add it here. --}}
    <script>
        (function () {
            const body = document.getElementById('items-body');
            const template = document.getElementById('item-template');
            const grandTotal = document.getElementById('grand-total');
            let nextIndex = body.querySelectorAll('.item-row').length;

            const fmt = (n) => Math.round(n).toLocaleString();

            function recalc() {
                let total = 0;
                body.querySelectorAll('.item-row').forEach((row) => {
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                    const line = qty * cost;
                    row.querySelector('.line-total').textContent = fmt(line);
                    total += line;
                });
                grandTotal.textContent = fmt(total);
            }

            document.getElementById('add-item').addEventListener('click', () => {
                const html = template.innerHTML.replace(/__INDEX__/g, nextIndex++);
                body.insertAdjacentHTML('beforeend', html);
                recalc();
            });

            body.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-item');
                if (!btn) return;
                if (body.querySelectorAll('.item-row').length <= 1) {
                    return; // a purchase needs at least one item
                }
                btn.closest('.item-row').remove();
                recalc();
            });

            body.addEventListener('input', recalc);
            recalc();
        })();
    </script>
</x-app-layout>