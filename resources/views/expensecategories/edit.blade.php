<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            {{ __('Edit Expense Category') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <form method="POST"
                  action="{{ route('expensecategories.update', $category->id) }}"
                  class="bg-white p-6 rounded-xl shadow-sm space-y-4">

                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label class="text-sm font-medium text-gray-700">Name</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           class="mt-1 w-full border-gray-300 rounded-xl"
                           required>
                </div>

                <!-- Description -->
                <div>
                    <label class="text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description"
                              class="mt-1 w-full border-gray-300 rounded-xl"
                              rows="3">{{ old('description', $category->description) }}</textarea>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white py-2 rounded-xl hover:bg-indigo-700">
                    Update Category
                </button>

            </form>

        </div>
    </div>
</x-app-layout>