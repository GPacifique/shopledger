<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    {{ strtoupper(substr($shop->name, 0, 2)) }}
                </div>

                <div>
                    <h2 class="font-bold text-xl text-gray-800">{{ $shop->name }}</h2>
                    <p class="text-sm text-gray-500">Financial Dashboard</p>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $salesToday = $stats['today_sales'] ?? 0;
        $purchasesToday = $stats['today_purchases'] ?? 0;
        $expensesToday = $stats['today_expenses'] ?? 0;

        $salesMonth = $stats['month_sales'] ?? 0;
        $purchasesMonth = $stats['month_purchases'] ?? 0;
        $expensesMonth = $stats['month_expenses'] ?? 0;

        $grossProfit = $salesMonth - $purchasesMonth;
        $netProfit = $salesMonth - ($purchasesMonth + $expensesMonth);
    @endphp

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- TOP CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">

            {{-- SALES TODAY --}}
            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-gray-500 text-sm">Today Sales</p>
                <p class="text-2xl font-bold text-green-600">{{ rwf($salesToday) }}</p>
            </div>

            {{-- PURCHASES TODAY --}}
            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-gray-500 text-sm">Today Purchases</p>
                <p class="text-2xl font-bold text-red-600">{{ rwf($purchasesToday) }}</p>
            </div>

            {{-- EXPENSES TODAY --}}
            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-gray-500 text-sm">Today Expenses</p>
                <p class="text-2xl font-bold text-yellow-600">{{ rwf($expensesToday) }}</p>
            </div>

            {{-- GROSS PROFIT --}}
            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-gray-500 text-sm">Gross Profit</p>
                <p class="text-2xl font-bold text-indigo-600">{{ rwf($grossProfit) }}</p>
            </div>

            {{-- NET PROFIT --}}
            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-gray-500 text-sm">Net Profit</p>
                <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ rwf($netProfit) }}
                </p>
            </div>

        </div>

        {{-- MONTHLY SUMMARY --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">Monthly Financial Summary</h3>

            <div class="space-y-4">

                <div class="flex justify-between">
                    <span class="text-gray-600">Sales</span>
                    <span class="font-bold text-green-600">{{ rwf($salesMonth) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Purchases</span>
                    <span class="font-bold text-red-600">{{ rwf($purchasesMonth) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Expenses</span>
                    <span class="font-bold text-yellow-600">{{ rwf($expensesMonth) }}</span>
                </div>

                <hr>

                <div class="flex justify-between">
                    <span class="text-gray-800 font-semibold">Gross Profit</span>
                    <span class="font-bold text-indigo-600">{{ rwf($grossProfit) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-800 font-semibold">Net Profit</span>
                    <span class="font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ rwf($netProfit) }}
                    </span>
                </div>

            </div>
        </div>

        {{-- DIFFERENCE ANALYSIS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white p-6 rounded-2xl shadow">
                <p class="text-gray-500">Sales vs Purchases</p>
                <p class="text-xl font-bold text-indigo-600">
                    {{ rwf($salesMonth - $purchasesMonth) }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
                <p class="text-gray-500">Sales vs Expenses</p>
                <p class="text-xl font-bold text-yellow-600">
                    {{ rwf($salesMonth - $expensesMonth) }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
                <p class="text-gray-500">Total Cost (Purchases + Expenses)</p>
                <p class="text-xl font-bold text-red-600">
                    {{ rwf($purchasesMonth + $expensesMonth) }}
                </p>
            </div>

        </div>

    </div>
</x-app-layout>