<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('customers.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $customer->phone ?? __('No phone') }}</p>
                </div>
            </div>
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                {{ __('Edit Customer') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Customer Details') }}</h3>
                        <dl class="mt-4 space-y-3 text-sm text-gray-600">
                            <div><dt class="font-medium text-gray-500">{{ __('Email') }}</dt><dd>{{ $customer->email ?? '—' }}</dd></div>
                            <div><dt class="font-medium text-gray-500">{{ __('Address') }}</dt><dd>{{ $customer->address ?? '—' }}</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Recent Sales') }}</h3>
                        @if($customer->sales->isEmpty())
                            <p class="mt-4 text-sm text-gray-500">{{ __('No sales linked yet.') }}</p>
                        @else
                            <ul class="mt-4 space-y-2">
                                @foreach($customer->sales as $sale)
                                    <li class="flex justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm">
                                        <span>#{{ $sale->id }} · {{ $sale->sale_date->format('M d, Y') }}</span>
                                        <span class="font-semibold text-green-600">{{ rwf($sale->total_amount) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
