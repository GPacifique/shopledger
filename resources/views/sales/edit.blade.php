<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('sales.show', $sale) }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Sale') }} #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <form method="POST" action="{{ route('sales.update', $sale) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="sale_date" class="block text-sm font-medium text-gray-700">{{ __('Sale Date') }} *</label>
                        <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('sale_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">{{ __('Customer') }}</label>
                        <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Walk-in customer') }}</option>
                            @foreach(\App\Models\Customer::where('shop_id', $sale->shop_id)->orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id', $sale->customer_id) == $customer->id)> {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">{{ __('Payment Method') }} *</label>
                        <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\Sale::PAYMENT_METHODS as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method', $sale->payment_method) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700">{{ __('Payment Status') }}</label>
                        <select name="payment_status" id="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="paid" @selected(old('payment_status', $sale->payment_status) === 'paid')>{{ __('Paid') }}</option>
                            <option value="unpaid" @selected(old('payment_status', $sale->payment_status) === 'unpaid')>{{ __('Unpaid') }}</option>
                        </select>
                        @error('payment_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Update Sale') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
