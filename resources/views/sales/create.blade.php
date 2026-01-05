<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('sales.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Record New Sale
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="sale_date" class="block text-sm font-medium text-gray-700">Sale Date *</label>
                                <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('sale_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                                <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach(\App\Models\Sale::PAYMENT_METHODS as $value => $label)
                                        <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Sale Items</h3>
                        <button type="button" onclick="addItem()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200">
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
                            <span id="grandTotal" class="text-2xl font-bold text-green-600">RWF 0</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Record Sale
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
                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">
                                {{ $product->name }} (Stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="items[__INDEX__][quantity]" min="1" value="1" required class="quantity-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" oninput="updateItemTotal(this)">
                    <p class="available-stock text-xs text-gray-500 mt-1"></p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price (RWF) *</label>
                    <input type="number" name="items[__INDEX__][unit_price]" min="0" step="1" required class="price-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" oninput="updateItemTotal(this)">
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

    <!-- Toast Notification -->
    <div id="stockToast" class="fixed top-4 right-4 z-50 hidden transform transition-all duration-300 translate-x-full">
        <div class="bg-red-600 text-white px-6 py-4 rounded-lg shadow-xl flex items-center space-x-3">
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold">Insufficient Stock!</p>
                <p id="stockToastMessage" class="text-sm text-red-100"></p>
            </div>
            <button onclick="hideStockToast()" class="ml-4 text-red-200 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        let toastTimeout = null;

        function showStockToast(productName, requested, available) {
            const toast = document.getElementById('stockToast');
            const message = document.getElementById('stockToastMessage');
            message.textContent = `"${productName}" has only ${available} units available. You requested ${requested}.`;

            toast.classList.remove('hidden', 'translate-x-full');
            toast.classList.add('translate-x-0');

            // Auto-hide after 5 seconds
            if (toastTimeout) clearTimeout(toastTimeout);
            toastTimeout = setTimeout(hideStockToast, 5000);
        }

        function hideStockToast() {
            const toast = document.getElementById('stockToast');
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }

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
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = quantity * price;
            row.querySelector('.line-total').textContent = 'RWF ' + total.toLocaleString();

            // Check stock
            const select = row.querySelector('.product-select');
            const option = select.options[select.selectedIndex];
            const stock = parseInt(option.dataset.stock) || 0;
            const productName = option.text.split(' (Stock:')[0];
            const stockInfo = row.querySelector('.available-stock');
            const quantityInput = row.querySelector('.quantity-input');

            if (select.value) {
                stockInfo.textContent = `Available: ${stock}`;

                if (quantity > stock) {
                    stockInfo.classList.remove('text-gray-500');
                    stockInfo.classList.add('text-red-600', 'font-semibold');
                    quantityInput.classList.add('border-red-500', 'ring-red-500', 'bg-red-50');
                    showStockToast(productName, quantity, stock);
                } else {
                    stockInfo.classList.remove('text-red-600', 'font-semibold');
                    stockInfo.classList.add('text-gray-500');
                    quantityInput.classList.remove('border-red-500', 'ring-red-500', 'bg-red-50');
                }
            } else {
                stockInfo.textContent = '';
            }

            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                total += quantity * price;
            });
            document.getElementById('grandTotal').textContent = 'RWF ' + total.toLocaleString();
        }

        // Auto-fill price when product is selected
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const option = e.target.options[e.target.selectedIndex];
                const price = option.dataset.price || 0;
                const stock = parseInt(option.dataset.stock) || 0;
                const row = e.target.closest('.item-row');
                row.querySelector('.price-input').value = price;
                row.querySelector('.available-stock').textContent = option.value ? `Available: ${stock}` : '';
                updateItemTotal(e.target);
            }
        });

        // Form submission validation
        document.getElementById('saleForm').addEventListener('submit', function(e) {
            let hasStockError = false;
            document.querySelectorAll('.item-row').forEach(row => {
                const select = row.querySelector('.product-select');
                const option = select.options[select.selectedIndex];
                const stock = parseInt(option.dataset.stock) || 0;
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;

                if (quantity > stock) {
                    hasStockError = true;
                    const productName = option.text.split(' (Stock:')[0];
                    showStockToast(productName, quantity, stock);
                }
            });

            if (hasStockError) {
                e.preventDefault();
                alert('Cannot complete sale: One or more items exceed available stock. Please adjust quantities.');
            }
        });

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });
    </script>
</x-app-layout>
