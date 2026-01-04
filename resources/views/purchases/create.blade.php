<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('purchases.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Record New Purchase
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Supplier (Optional)</option>
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
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date *</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Purchase Items</h3>
                        <button type="button" onclick="addItem()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Item
                        </button>
                    </div>
                    <div class="p-6">
                        <div id="itemsContainer" class="space-y-4">
                            <!-- Items will be added here -->
                        </div>
                        @error('items')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-medium text-gray-900">Total Amount:</span>
                            <span id="grandTotal" class="text-2xl font-bold text-red-600">RWF 0</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Record Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>

    <template id="itemTemplate">
        <div class="item-row bg-gray-50 rounded-lg p-4" data-index="__INDEX__">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="items[__INDEX__][product_id]" required class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="updateItemTotal(this)">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="items[__INDEX__][quantity]" min="1" value="1" required class="quantity-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" oninput="updateItemTotal(this)">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost (RWF) *</label>
                    <input type="number" name="items[__INDEX__][unit_cost]" min="0" step="1" required class="cost-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" oninput="updateItemTotal(this)">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Line Total</label>
                    <div class="line-total mt-2 text-lg font-semibold text-gray-900">RWF 0</div>
                </div>
                <div class="col-span-1 flex items-end">
                    <button type="button" onclick="removeItem(this)" class="mb-1 p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script>
        let itemIndex = 0;

        function addItem() {
            const template = document.getElementById('itemTemplate').innerHTML;
            const html = template.replace(/__INDEX__/g, itemIndex);
            document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
            itemIndex++;
            updateGrandTotal();
        }

        function removeItem(btn) {
            const row = btn.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateGrandTotal();
            } else {
                alert('At least one item is required');
            }
        }

        function updateItemTotal(element) {
            const row = element.closest('.item-row');
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            const total = quantity * cost;
            row.querySelector('.line-total').textContent = 'RWF ' + total.toLocaleString();
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                total += quantity * cost;
            });
            document.getElementById('grandTotal').textContent = 'RWF ' + total.toLocaleString();
        }

        // Auto-fill cost when product is selected
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const option = e.target.options[e.target.selectedIndex];
                const cost = option.dataset.cost || 0;
                const row = e.target.closest('.item-row');
                row.querySelector('.cost-input').value = cost;
                updateItemTotal(e.target);
            }
        });

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });
    </script>
</x-app-layout>
