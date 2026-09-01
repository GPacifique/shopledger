<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            {{ __('Create Expense Category') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('expensecategories.store') }}"
                  class="bg-white p-6 rounded-xl shadow-sm space-y-4">

                @csrf

                <!-- Name -->
                <div>
                    <label class="text-sm font-medium text-gray-700">Name</label>
                    <input type="text"
                           name="name"
                           class="mt-1 w-full border-gray-300 rounded-xl"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description"
                              class="mt-1 w-full border-gray-300 rounded-xl"
                              rows="3">{{ old('description') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full bg-rose-600 text-white py-2 rounded-xl hover:bg-rose-700">
                    Save Category
                </button>

            </form>

        </div>
    </div>
</x-app-layout>