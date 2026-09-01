<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Add Other Income') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Record income received outside normal POS sales.') }}
                </p>
            </div>

            <a href="{{ route('other_incomes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 transition">
                ← {{ __('Back to Other Income') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                    <div class="text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400"
                                 viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 10-1.5 0v4.5a.75.75 0 001.5 0v-4.5zM10 14.5a1 1 0 100-2 1 1 0 000 2z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('Please correct the following errors:') }}
                            </h3>

                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">

                <form action="{{ route('other_incomes.store') }}"
                      method="POST">

                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Income Category --}}
                        <div>
                            <label for="income_category_id"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Income Category') }}
                            </label>

                            <select name="income_category_id"
                                    id="income_category_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('income_category_id') border-red-500 @enderror">

                                <option value="">
                                    {{ __('Select income category') }}
                                </option>

                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('income_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>
                                        {{ __('No income categories available') }}
                                    </option>
                                @endforelse

                            </select>

                            @error('income_category_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Select the income stream for this transaction.') }}
                            </p>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label for="amount"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Amount') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500">
                                    RWF
                                </span>

                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       value="{{ old('amount') }}"
                                       min="0.01"
                                       step="0.01"
                                       required
                                       placeholder="0.00"
                                       class="w-full pl-14 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror">
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
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Income Date') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="date"
                                   name="income_date"
                                   id="income_date"
                                   value="{{ old('income_date', now()->format('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('income_date') border-red-500 @enderror">

                            @error('income_date')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Reference --}}
                        <div>
                            <label for="reference"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Reference') }}
                            </label>

                            <input type="text"
                                   name="reference"
                                   id="reference"
                                   value="{{ old('reference') }}"
                                   maxlength="255"
                                   placeholder="{{ __('Receipt number, transaction ID, etc.') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('reference') border-red-500 @enderror">

                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Description') }}
                            </label>

                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      placeholder="{{ __('Describe the source or reason for this income...') }}"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>

                            @error('description')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Status') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="status"
                                    id="status"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror">

                                <option value="received"
                                    {{ old('status', 'received') === 'received' ? 'selected' : '' }}>
                                    {{ __('Received') }}
                                </option>

                                <option value="pending"
                                    {{ old('status') === 'pending' ? 'selected' : '' }}>
                                    {{ __('Pending') }}
                                </option>

                                <option value="cancelled"
                                    {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                    {{ __('Cancelled') }}
                                </option>

                            </select>

                            @error('status')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Only received income is included in actual income statistics.') }}
                            </p>
                        </div>

                    </div>

                    {{-- Form Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end gap-3">

                        <a href="{{ route('other_incomes.index') }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                            {{ __('Cancel') }}
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            {{ __('Save Other Income') }}
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>

