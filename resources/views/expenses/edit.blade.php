<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.Edit Expense') }}
            </h2>
            <a href="{{ route('expenses.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                {{ __('messages.Back to Expenses') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">{{ __('messages.Title') }} <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $expense->title) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="e.g., Office Supplies, Rent, Utilities">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700">{{ __('messages.Amount (RWF)') }} <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="amount" id="amount"
                                value="{{ old('amount', $expense->amount) }}" required min="0" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="0.00">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">{{ __('messages.Date') }} <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="date" id="date"
                                value="{{ old('date', $expense->date->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">{{ __('messages.Category') }}</label>
                            <select name="category_id" id="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                <option value="">{{ __('messages.Select a category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">{{ __('messages.Payment Method') }}
                                <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                <option value="">{{ __('messages.Select payment method') }}</option>
                                <option value="cash"
                                    {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>
                                    {{ __('messages.Cash') }}</option>
                                <option value="bank_transfer"
                                    {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>
                                    {{ __('messages.Bank Transfer') }}</option>
                                <option value="mobile_money"
                                    {{ old('payment_method', $expense->payment_method) == 'mobile_money' ? 'selected' : '' }}>
                                    {{ __('messages.Mobile Money') }}</option>
                                <option value="cheque"
                                    {{ old('payment_method', $expense->payment_method) == 'cheque' ? 'selected' : '' }}>
                                    {{ __('messages.Cheque') }}</option>
                                <option value="other"
                                    {{ old('payment_method', $expense->payment_method) == 'other' ? 'selected' : '' }}>
                                    {{ __('messages.Other') }}</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reference Number -->
                        <div class="mb-4">
                            <label for="reference_number" class="block text-sm font-medium text-gray-700">{{ __('messages.Reference Number') }}</label>
                            <input type="text" name="reference_number" id="reference_number"
                                value="{{ old('reference_number', $expense->reference_number) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="e.g., Invoice #, Receipt #">
                            @error('reference_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('messages.Notes') }}</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="Additional details...">{{ old('notes', $expense->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('messages.Update Expense') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
