<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('products.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add New Product
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <form method="POST" action="{{ route('products.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Wireless Mouse">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">SKU *</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required class="block w-full rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-generated">
                            <button type="button" onclick="generateSKU()" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100 text-sm">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Generate
                            </button>
                        </div>
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional product description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">RWF</span>
                                </div>
                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" step="1" min="0" required class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                            </div>
                            @error('cost_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">RWF</span>
                                </div>
                                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" step="1" min="0" required class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                            </div>
                            @error('sale_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Initial Stock</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateSKU() {
            const nameInput = document.getElementById('name');
            const skuInput = document.getElementById('sku');
            const name = nameInput.value.trim();

            let prefix = 'PRD';
            if (name) {
                // Take first 3 letters of the product name (uppercase)
                prefix = name.replace(/[^a-zA-Z]/g, '').substring(0, 3).toUpperCase() || 'PRD';
            }

            // Generate random 5-digit number
            const random = Math.floor(10000 + Math.random() * 90000);

            skuInput.value = prefix + '-' + random;
        }

        // Auto-generate SKU when name field loses focus (if SKU is empty)
        document.getElementById('name').addEventListener('blur', function() {
            const skuInput = document.getElementById('sku');
            if (!skuInput.value.trim()) {
                generateSKU();
            }
        });
    </script>
</x-app-layout>
