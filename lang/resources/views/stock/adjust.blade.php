<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Adjust Stock: {{ $product->name }}
            </h2>
            <a href="{{ route('stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stock
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Current Stock Info -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div>
                        <p class="text-sm text-gray-500">SKU</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $product->sku }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Current Stock</p>
                        <p class="text-3xl font-bold {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= $product->low_stock_threshold ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->stock }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Threshold</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $product->low_stock_threshold }}</p>
                    </div>
                </div>
            </div>

            <!-- Adjustment Form -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-yellow-500 to-orange-500">
                    <h3 class="font-semibold text-white">Stock Adjustment</h3>
                </div>
                <form action="{{ route('stock.adjust.store', $product) }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Adjustment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Adjustment Type</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer hover:bg-gray-50 transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                <input type="radio" name="adjustment_type" value="set" class="peer sr-only" checked>
                                <div class="text-center peer-checked:text-indigo-600">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">Set To</span>
                                    <p class="text-xs text-gray-500 mt-1">Set exact quantity</p>
                                </div>
                                <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-indigo-600"></span>
                            </label>
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="adjustment_type" value="add" class="peer sr-only">
                                <div class="text-center peer-checked:text-green-600">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium">Add</span>
                                    <p class="text-xs text-gray-500 mt-1">Increase stock</p>
                                </div>
                                <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-green-600"></span>
                            </label>
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="adjustment_type" value="subtract" class="peer sr-only">
                                <div class="text-center peer-checked:text-red-600">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium">Subtract</span>
                                    <p class="text-xs text-gray-500 mt-1">Decrease stock</p>
                                </div>
                                <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-red-600"></span>
                            </label>
                        </div>
                        @error('adjustment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" 
                               name="quantity" 
                               id="quantity" 
                               min="0" 
                               value="{{ old('quantity', $product->stock) }}"
                               class="w-full px-4 py-3 text-2xl font-bold text-center border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reason -->
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Adjustment</label>
                        <textarea name="reason" 
                                  id="reason" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="e.g., Physical count correction, Found additional stock, Stock damaged..."
                                  required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quick Reasons -->
                    <div class="flex flex-wrap gap-2">
                        <span class="text-sm text-gray-500">Quick select:</span>
                        <button type="button" onclick="setReason('Physical count correction')" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition">Physical count</button>
                        <button type="button" onclick="setReason('Inventory audit adjustment')" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition">Audit adjustment</button>
                        <button type="button" onclick="setReason('Found additional stock')" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition">Found stock</button>
                        <button type="button" onclick="setReason('Stock moved to another location')" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition">Relocation</button>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                        <a href="{{ route('stock.index') }}" class="px-6 py-3 text-gray-700 hover:text-gray-900 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Apply Adjustment
                        </button>
                    </div>
                </form>
            </div>

            <!-- Record Damage Form -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-red-500 to-pink-500">
                    <h3 class="font-semibold text-white">Report Damaged Stock</h3>
                </div>
                <form action="{{ route('stock.damage', $product) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="damage_quantity" class="block text-sm font-medium text-gray-700 mb-1">Damaged Quantity</label>
                            <input type="number" 
                                   name="quantity" 
                                   id="damage_quantity" 
                                   min="1" 
                                   max="{{ $product->stock }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="0">
                        </div>
                        <div>
                            <label for="damage_notes" class="block text-sm font-medium text-gray-700 mb-1">Damage Notes</label>
                            <input type="text" 
                                   name="notes" 
                                   id="damage_notes" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="e.g., Water damage, broken items...">
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                        Record Damage
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function setReason(reason) {
            document.getElementById('reason').value = reason;
        }

        // Add visual feedback for radio selection
        document.querySelectorAll('input[name="adjustment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="adjustment_type"]').forEach(r => {
                    r.closest('label').classList.remove('border-indigo-600', 'border-green-600', 'border-red-600', 'bg-indigo-50', 'bg-green-50', 'bg-red-50');
                });
                
                const colors = {
                    'set': ['border-indigo-600', 'bg-indigo-50'],
                    'add': ['border-green-600', 'bg-green-50'],
                    'subtract': ['border-red-600', 'bg-red-50']
                };
                
                this.closest('label').classList.add(...colors[this.value]);
            });
        });

        // Trigger initial state
        document.querySelector('input[name="adjustment_type"]:checked').dispatchEvent(new Event('change'));
    </script>
    @endpush
</x-app-layout>
