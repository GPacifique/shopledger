<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Seller Dashboard</h2>
                    <p class="text-sm text-gray-500">{{ $shop->name }}</p>
                </div>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-xs text-gray-500">Welcome back</p>
                <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Sale CTA -->
            <div class="mb-8 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                <div class="relative px-6 py-8 sm:px-10 sm:py-12">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <circle cx="1" cy="1" r="1" fill="white"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#dots)"/>
                        </svg>
                    </div>
                    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="text-white mb-6 md:mb-0">
                            <h3 class="text-3xl font-bold mb-2">Ready to make a sale? 🛒</h3>
                            <p class="text-blue-100 text-lg">Create new sales quickly and easily.</p>
                        </div>
                        <a href="{{ route('sales.create') }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold text-lg hover:bg-indigo-50 transition-all transform hover:scale-105 shadow-xl hover:shadow-2xl group">
                            <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            New Sale
                            <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Today's Sales Amount -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.1s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Live
                                </span>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Today's Sales</h3>
                        <p class="text-3xl font-bold text-gray-900 mb-2" id="todaySales">{{ rwf($todaySales) }}</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Revenue earned today</span>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gradient-to-r from-green-400 to-emerald-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Today's Transactions -->
                <div class="stat-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.2s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Today
                                </span>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Transactions</h3>
                        <p class="text-3xl font-bold text-gray-900 mb-2" id="todayCount">{{ $todaySalesCount }}</p>
                        <div class="flex items-center text-sm text-blue-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Sales completed</span>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gradient-to-r from-blue-400 to-indigo-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 animate-slide-up" style="animation-delay: 0.3s">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">My Recent Sales</h3>
                            <p class="text-xs text-gray-500">Your latest transactions</p>
                        </div>
                    </div>
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 group">
                        View all
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sale ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center mr-3">
                                            <span class="text-xs font-bold text-green-600">#</span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $sale->sale_date->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $sale->sale_date->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $sale->items->count() }} items
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-lg font-bold text-green-600">{{ rwf($sale->total_amount) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-1">No sales yet</h4>
                                        <p class="text-gray-500 mb-4">Start by creating your first sale</p>
                                        <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium hover:from-indigo-600 hover:to-purple-700 transition-all transform hover:scale-105">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Create Sale
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Helpful Tips -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200">
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-semibold text-amber-800">Pro Tip</h4>
                        <p class="text-sm text-amber-700 mt-1">Use keyboard shortcut <kbd class="px-2 py-0.5 bg-white rounded text-xs font-mono shadow-sm">Ctrl + N</kbd> to quickly create a new sale from anywhere!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
        .animate-slide-up { animation: slide-up 0.6s ease-out forwards; }
        .stat-card { animation: fade-in 0.5s ease-out forwards; opacity: 0; }
    </style>

    <script>
        // Keyboard shortcut for quick sale
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = '{{ route("sales.create") }}';
            }
        });
    </script>
</x-app-layout>
