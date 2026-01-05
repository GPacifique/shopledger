<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('suppliers.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $supplier->name }}
                </h2>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('purchases.create', ['supplier' => $supplier->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    {{ __('New Purchase') }}
                </a>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Supplier') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Supplier Info -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Contact Information') }}</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Supplier Name') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $supplier->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Contact Person') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $supplier->contact_name ?: __('Not specified') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-indigo-600 hover:text-indigo-900">{{ $supplier->email }}</a>
                                @else
                                    {{ __('Not specified') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($supplier->phone)
                                    <a href="tel:{{ $supplier->phone }}" class="text-indigo-600 hover:text-indigo-900">{{ $supplier->phone }}</a>
                                @else
                                    {{ __('Not specified') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Address') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $supplier->address ?: __('Not specified') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Stats -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Purchase Statistics') }}</h3>
                    <dl class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Total Purchases') }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $supplier->purchases->count() }}</dd>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Total Amount') }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-red-600">{{ rwf($supplier->purchases->sum('total_amount')) }}</dd>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Added On') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $supplier->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Recent Purchases -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Recent Purchases') }}</h3>
                    <a href="{{ route('purchases.index', ['supplier' => $supplier->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('View All') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Items') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($supplier->purchases as $purchase)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $purchase->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->items->count() }} {{ __('items') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">{{ rwf($purchase->total_amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">{{ __('No purchases from this supplier yet') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delete Action -->
            <div class="mt-6 flex justify-end">
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this supplier? This action cannot be undone.') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('Delete Supplier') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
