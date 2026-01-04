<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('staff.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Staff Details
                </h2>
            </div>
            <a href="{{ route('staff.edit', $staff) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Card -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-10">
                    <div class="flex items-center">
                        <div class="h-24 w-24 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-3xl font-bold border-4 border-white/30">
                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                        </div>
                        <div class="ml-6 text-white">
                            <h1 class="text-2xl font-bold">{{ $staff->name }}</h1>
                            <p class="text-indigo-100">{{ $staff->email }}</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur">
                                    {{ ucfirst($staff->role) }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $staff->account_status === 'active' ? 'bg-green-400/30 text-green-100' : 'bg-red-400/30 text-red-100' }}">
                                    {{ ucfirst($staff->account_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $staff->sales()->count() }}</p>
                            <p class="text-sm text-gray-500">Total Sales</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ rwf($staff->sales()->sum('total_amount')) }}</p>
                            <p class="text-sm text-gray-500">Sales Revenue</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $staff->created_at->diffInDays(now()) }}</p>
                            <p class="text-sm text-gray-500">Days Active</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $staff->created_at->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-500">Joined</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Account Information
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Full Name</dt>
                            <dd class="font-medium text-gray-900">{{ $staff->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Email</dt>
                            <dd class="font-medium text-gray-900">{{ $staff->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Role</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $staff->role === 'seller' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($staff->role) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $staff->account_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($staff->account_status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Permissions
                    </h3>
                    @if($staff->role === 'seller')
                        <ul class="space-y-2">
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Create new sales</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">View product inventory</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">View own sales history</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-400">Cannot access reports</span>
                            </li>
                        </ul>
                    @else
                        <ul class="space-y-2">
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">View all sales</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">View all purchases</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Access financial reports</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-400">Cannot create sales</span>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Sales by {{ $staff->name }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($staff->sales()->with('items')->latest('sale_date')->take(5)->get() as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->sale_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->items->count() }} items
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                    {{ rwf($sale->total_amount) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No sales recorded yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
