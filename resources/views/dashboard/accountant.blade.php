<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ __('Accountant Dashboard') }}</h2>
                    <p class="text-sm text-gray-500">{{ $shop->name }}</p>
                </div>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-xs text-gray-500">{{ __('Financial Overview') }}</p>
                <p class="text-sm font-semibold text-gray-700" id="current-date"></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Period Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today -->
                <div class="period-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.1s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ __('Today') }}</h4>
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    {{ __('Sales') }}
                                </span>
                                <span class="font-bold text-green-600">{{ rwf($todaySales) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ __('Purchases') }}
                                </span>
                                <span class="font-bold text-red-600">{{ rwf($todayPurchases) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ ($todaySales - $todayPurchases) >= 0 ? 'from-emerald-500 to-green-600' : 'from-red-500 to-rose-600' }} rounded-xl text-white">
                                <span class="text-sm font-medium">{{ __('Net') }}</span>
                                <span class="text-lg font-bold">{{ rwf($todaySales - $todayPurchases) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-blue-400 to-indigo-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- This Week -->
                <div class="period-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.2s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ __('This Week') }}</h4>
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    {{ __('Sales') }}
                                </span>
                                <span class="font-bold text-green-600">{{ rwf($weeklySales) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ __('Purchases') }}
                                </span>
                                <span class="font-bold text-red-600">{{ rwf($weeklyPurchases) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ ($weeklySales - $weeklyPurchases) >= 0 ? 'from-emerald-500 to-green-600' : 'from-red-500 to-rose-600' }} rounded-xl text-white">
                                <span class="text-sm font-medium">{{ __('Net') }}</span>
                                <span class="text-lg font-bold">{{ rwf($weeklySales - $weeklyPurchases) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-purple-400 to-pink-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- This Month -->
                <div class="period-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.3s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ __('This Month') }}</h4>
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    {{ __('Sales') }}
                                </span>
                                <span class="font-bold text-green-600">{{ rwf($monthSales) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ __('Purchases') }}
                                </span>
                                <span class="font-bold text-red-600">{{ rwf($monthPurchases) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ $monthProfit >= 0 ? 'from-emerald-500 to-green-600' : 'from-red-500 to-rose-600' }} rounded-xl text-white">
                                <span class="text-sm font-medium">{{ __('Profit') }}</span>
                                <span class="text-lg font-bold">{{ rwf($monthProfit) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-orange-400 to-red-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- This Year -->
                <div class="period-card group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100" style="animation-delay: 0.4s">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ __('This Year') }}</h4>
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    {{ __('Sales') }}
                                </span>
                                <span class="font-bold text-green-600">{{ rwf($yearSales) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ __('Purchases') }}
                                </span>
                                <span class="font-bold text-red-600">{{ rwf($yearPurchases) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ ($yearSales - $yearPurchases) >= 0 ? 'from-emerald-500 to-green-600' : 'from-red-500 to-rose-600' }} rounded-xl text-white">
                                <span class="text-sm font-medium">{{ __('Net') }}</span>
                                <span class="text-lg font-bold">{{ rwf($yearSales - $yearPurchases) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-emerald-400 to-teal-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('sales.index') }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600 transition">{{ __('View Sales') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('All sales records') }}</p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400 group-hover:text-green-500 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('purchases.index') }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-red-400 to-rose-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-red-600 transition">{{ __('View Purchases') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('All purchase records') }}</p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400 group-hover:text-red-500 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('stats.summary') }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition">{{ __('Detailed Reports') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Full analytics') }}</p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400 group-hover:text-indigo-500 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Info Banner -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-200">
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-semibold text-indigo-800">{{ __('Financial Overview') }}</h4>
                        <p class="text-sm text-indigo-600 mt-1">{{ __('As an accountant, you have view-only access to all financial data. For detailed reports and analytics, use the "Detailed Reports" section.') }}</p>
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
        .period-card { animation: fade-in 0.5s ease-out forwards; opacity: 0; }
    </style>

    <script>
        // Update current date
        function updateDate() {
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', { 
                weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' 
            });
        }
        updateDate();
    </script>
</x-app-layout>
