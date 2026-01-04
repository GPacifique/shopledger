<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('purchases.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Purchase #{{ $purchase->id }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('purchases.download', $purchase) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    New Purchase
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Purchase Info -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $purchase->purchase_date->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                            <dd class="mt-1 text-lg text-gray-900">
                                @if($purchase->supplier)
                                    <a href="{{ route('suppliers.show', $purchase->supplier) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $purchase->supplier->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">No supplier</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $purchase->creator->name ?? 'Unknown' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                            <dd class="mt-1 text-2xl font-bold text-red-600">{{ rwf($purchase->total_amount) }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Purchase Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('products.show', $item->product) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ $item->product->name }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ rwf($item->unit_cost) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">{{ rwf($item->line_total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-red-600">{{ rwf($purchase->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Created on {{ $purchase->created_at->format('M d, Y \a\t h:i A') }}
                </div>
                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this purchase? This will reverse all stock changes.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Purchase
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
