<x-app-layout>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('other_incomes.index') }}"
                   class="hover:text-indigo-600 transition">
                    Other Incomes
                </a>
                <span>/</span>
                <span>Edit</span>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">
                Edit Other Income
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Update the details of this income record.
            </p>
        </div>

        <a href="{{ route('other_incomes.show', $otherIncome) }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-eye mr-2"></i>
            View Record
        </a>
    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="flex items-start">
                <div class="text-red-500 mr-3">
                    <i class="fas fa-circle-exclamation"></i>
                </div>

                <div>
                    <h3 class="font-semibold text-red-800">
                        Please correct the following errors:
                    </h3>

                    <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif


    <form action="{{ route('other_incomes.update', $otherIncome) }}"
          method="POST"
          class="space-y-6">

        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Card Header --}}
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fas fa-pen"></i>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Income Information
                        </h2>

                        <p class="text-sm text-gray-500">
                            Update the income record information below.
                        </p>
                    </div>
                </div>
            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Income Category --}}
                    <div>
                        <label for="income_category_id"
                               class="block text-sm font-semibold text-gray-700 mb-2">
                            Income Category
                        </label>

                        <select name="income_category_id"
                                id="income_category_id"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                -- Select Category (Optional) --
                            </option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(old('income_category_id', $otherIncome->income_category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('income_category_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Amount --}}
                    <div>
                        <label for="amount"
                               class="block text-sm font-semibold text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 text-sm">
                                RWF
                            </span>

                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   step="0.01"
                                   min="0.01"
                                   value="{{ old('amount', $otherIncome->amount) }}"
                                   required
                                   class="w-full pl-14 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Income Date --}}
                    <div>
                        <label for="income_date"
                               class="block text-sm font-semibold text-gray-700 mb-2">
                            Income Date <span class="text-red-500">*</span>
                        </label>

                        <input type="date"
                               name="income_date"
                               id="income_date"
                               value="{{ old('income_date', \Carbon\Carbon::parse($otherIncome->income_date)->format('Y-m-d')) }}"
                               required
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        @error('income_date')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Status --}}
                    <div>
                        <label for="status"
                               class="block text-sm font-semibold text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>

                        <select name="status"
                                id="status"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="received"
                                @selected(old('status', $otherIncome->status) === 'received')>
                                Received
                            </option>

                            <option value="pending"
                                @selected(old('status', $otherIncome->status) === 'pending')>
                                Pending
                            </option>

                            <option value="cancelled"
                                @selected(old('status', $otherIncome->status) === 'cancelled')>
                                Cancelled
                            </option>
                        </select>

                        @error('status')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Reference --}}
                    <div class="md:col-span-2">
                        <label for="reference"
                               class="block text-sm font-semibold text-gray-700 mb-2">
                            Reference
                        </label>

                        <input type="text"
                               name="reference"
                               id="reference"
                               maxlength="255"
                               value="{{ old('reference', $otherIncome->reference) }}"
                               placeholder="e.g. REF-001 or Bank Transaction ID"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        @error('reference')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description"
                               class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>

                        <textarea name="description"
                                  id="description"
                                  rows="5"
                                  placeholder="Enter additional information about this income..."
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $otherIncome->description) }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>


            {{-- Actions --}}
            <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('other_incomes.show', $otherIncome) }}"
                   class="inline-flex justify-center items-center px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex justify-center items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                    <i class="fas fa-save mr-2"></i>
                    Update Income
                </button>

            </div>

        </div>

    </form>

</div>

</x-app-layout>
