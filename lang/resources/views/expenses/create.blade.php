<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                {{ __('Create Expense') }}
            </h2>

            <a href="{{ route('expenses.index') }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm rounded-2xl border">

                <form method="POST"
                      action="{{ route('expenses.store') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <!-- CATEGORY -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Category
                        </label>

                        <select name="category_id"
                                class="mt-1 w-full border-gray-300 rounded-xl shadow-sm">
                            <option value="">-- Select Category --</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- AMOUNT -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Amount
                        </label>

                        <input type="number"
                               name="amount"
                               value="{{ old('amount') }}"
                               class="mt-1 w-full border-gray-300 rounded-xl"
                               required>

                        @error('amount')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DATE -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Expense Date
                        </label>

                        <input type="date"
                               name="expense_date"
                               value="{{ old('expense_date', date('Y-m-d')) }}"
                               class="mt-1 w-full border-gray-300 rounded-xl"
                               required>

                        @error('expense_date')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- REFERENCE -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Reference (Optional)
                        </label>

                        <input type="text"
                               name="reference"
                               value="{{ old('reference') }}"
                               class="mt-1 w-full border-gray-300 rounded-xl"
                               placeholder="Receipt / Invoice No">
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea name="description"
                                  class="mt-1 w-full border-gray-300 rounded-xl"
                                  rows="4">{{ old('description') }}</textarea>
                    </div>

                    <!-- ATTACHMENT -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Attachment (Optional)
                        </label>

                        <input type="file"
                               name="attachment"
                               class="mt-1 w-full border-gray-300 rounded-xl">
                    </div>

                    <!-- SUBMIT -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                            Save Expense
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>