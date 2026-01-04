<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Create New Shop</h2>
                    <p class="text-sm text-gray-500">Create and assign shop to a user</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Shop Details</h3>
                            <p class="text-xs text-gray-500">Fill in the shop information</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.shops.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Shop Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Shop Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                            placeholder="Enter shop name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="3"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                            placeholder="Enter shop address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                            placeholder="+250 7XX XXX XXX">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assign to User -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Assign to User (Shop Admin) *</label>
                        <select name="user_id" id="user_id" required
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                            <option value="">Select a user...</option>
                            @forelse($unassignedUsers as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @empty
                                <option value="" disabled>No unassigned users available</option>
                            @endforelse
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($unassignedUsers->isEmpty())
                            <p class="mt-2 text-sm text-yellow-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                All registered users already have shops assigned.
                            </p>
                        @endif
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center p-4 bg-green-50 rounded-xl border-2 cursor-pointer transition hover:bg-green-100
                                {{ old('status', 'approved') === 'approved' ? 'border-green-500' : 'border-transparent' }}">
                                <input type="radio" name="status" value="approved" class="sr-only" {{ old('status', 'approved') === 'approved' ? 'checked' : '' }}>
                                <div class="h-8 w-8 rounded-lg bg-green-500 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-green-800">Approved</p>
                                    <p class="text-xs text-green-600">Shop will be active immediately</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 bg-yellow-50 rounded-xl border-2 cursor-pointer transition hover:bg-yellow-100
                                {{ old('status') === 'pending' ? 'border-yellow-500' : 'border-transparent' }}">
                                <input type="radio" name="status" value="pending" class="sr-only" {{ old('status') === 'pending' ? 'checked' : '' }}>
                                <div class="h-8 w-8 rounded-lg bg-yellow-500 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-yellow-800">Pending</p>
                                    <p class="text-xs text-yellow-600">Requires separate approval</p>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.dashboard') }}" 
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-medium hover:from-indigo-600 hover:to-purple-700 transition transform hover:scale-105 shadow-lg"
                            {{ $unassignedUsers->isEmpty() ? 'disabled' : '' }}>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Create Shop
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Radio button styling
        document.querySelectorAll('input[name="status"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="status"]').forEach(r => {
                    r.closest('label').classList.remove('border-green-500', 'border-yellow-500');
                    r.closest('label').classList.add('border-transparent');
                });
                if (this.value === 'approved') {
                    this.closest('label').classList.add('border-green-500');
                } else {
                    this.closest('label').classList.add('border-yellow-500');
                }
            });
        });
    </script>
</x-app-layout>
