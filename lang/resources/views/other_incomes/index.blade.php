<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Other Income') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Manage income received outside normal POS sales.') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a href="{{ route('income_categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    {{ __('Income Categories') }}
                </a>

                <a href="{{ route('other_incomes.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 mr-2"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>

                    {{ __('Add Other Income') }}
                </a>

            </div>

        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                    <p class="text-sm text-red-700">
                        {{ session('error') }}
                    </p>
                </div>
            @endif

            {{-- ========================================================= --}}
            {{-- STATISTICS --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

                {{-- Today --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ __('Today') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($dailyIncome, 0) }}
                                <span class="text-sm font-medium text-gray-500">RWF</span>
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.105 0 2 .895 2 2m-2-2V6m0 12v-2m-6-4a6 6 0 1112 0 6 6 0 01-12 0z"/>
                            </svg>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        {{ $dailyTransactions }}
                        {{ __('transaction(s)') }}
                    </p>
                </div>


                {{-- This Week --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ __('This Week') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($weeklyIncome, 0) }}
                                <span class="text-sm font-medium text-gray-500">RWF</span>
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        {{ $weeklyTransactions }}
                        {{ __('transaction(s)') }}
                    </p>
                </div>


                {{-- This Month --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ __('This Month') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($monthlyIncome, 0) }}
                                <span class="text-sm font-medium text-gray-500">RWF</span>
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 4h10M5 11h14M5 15h14M5 19h8"/>
                            </svg>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        {{ $monthlyTransactions }}
                        {{ __('transaction(s)') }}
                    </p>
                </div>


                {{-- This Year --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ __('This Year') }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($yearlyIncome, 0) }}
                                <span class="text-sm font-medium text-gray-500">RWF</span>
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-lg bg-orange-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        {{ $yearlyTransactions }}
                        {{ __('transaction(s)') }}
                    </p>
                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- TOTAL STATUS CARDS --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">
                        {{ __('Total Received') }}
                    </p>

                    <p class="mt-2 text-xl font-bold text-green-600">
                        {{ number_format($totalReceived, 0) }} RWF
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">
                        {{ __('Pending') }}
                    </p>

                    <p class="mt-2 text-xl font-bold text-yellow-600">
                        {{ number_format($totalPending, 0) }} RWF
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">
                        {{ __('Cancelled') }}
                    </p>

                    <p class="mt-2 text-xl font-bold text-red-600">
                        {{ number_format($totalCancelled, 0) }} RWF
                    </p>
                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FILTERS --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">

                <div class="p-5">

                    <form method="GET"
                          action="{{ route('other_incomes.index') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

                            {{-- Search --}}
                            <div class="lg:col-span-2">
                                <label for="search"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Search') }}
                                </label>

                                <input type="text"
                                       name="search"
                                       id="search"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('Reference or description...') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="income_category_id"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Category') }}
                                </label>

                                <select name="income_category_id"
                                        id="income_category_id"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                    <option value="">
                                        {{ __('All Categories') }}
                                    </option>

                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('income_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Status') }}
                                </label>

                                <select name="status"
                                        id="status"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                    <option value="">
                                        {{ __('All Statuses') }}
                                    </option>

                                    <option value="received"
                                        {{ request('status') === 'received' ? 'selected' : '' }}>
                                        {{ __('Received') }}
                                    </option>

                                    <option value="pending"
                                        {{ request('status') === 'pending' ? 'selected' : '' }}>
                                        {{ __('Pending') }}
                                    </option>

                                    <option value="cancelled"
                                        {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('Cancelled') }}
                                    </option>

                                </select>
                            </div>

                            {{-- From --}}
                            <div>
                                <label for="from_date"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('From') }}
                                </label>

                                <input type="date"
                                       name="from_date"
                                       id="from_date"
                                       value="{{ request('from_date') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            {{-- To --}}
                            <div>
                                <label for="to_date"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('To') }}
                                </label>

                                <input type="date"
                                       name="to_date"
                                       id="to_date"
                                       value="{{ request('to_date') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                        </div>

                        <div class="flex flex-wrap gap-2 mt-4">

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                                {{ __('Filter') }}
                            </button>

                            <a href="{{ route('other_incomes.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                                {{ __('Clear') }}
                            </a>

                        </div>

                    </form>

                </div>
            </div>


            {{-- ========================================================= --}}
            {{-- CHARTS --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                {{-- Last 7 Days --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ __('Last 7 Days') }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            {{ __('Other income received by day') }}
                        </p>
                    </div>

                    <div class="h-72">
                        <canvas id="dailyIncomeChart"></canvas>
                    </div>

                </div>


                {{-- Current Year --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ __('Monthly Income') }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            {{ __('Other income received this year') }}
                        </p>
                    </div>

                    <div class="h-72">
                        <canvas id="monthlyIncomeChart"></canvas>
                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- CATEGORY BREAKDOWN --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">

                <div class="p-5 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ __('Income by Category') }}
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Received other income grouped by income stream.') }}
                    </p>
                </div>

                @if($categoryBreakdown->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Amount') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach($categoryBreakdown as $item)

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->category_name }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-gray-900">
                                            {{ number_format($item->total, 0) }} RWF
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="p-8 text-center text-gray-500">
                        {{ __('No income category data available.') }}
                    </div>

                @endif

            </div>


            {{-- ========================================================= --}}
            {{-- OTHER INCOME TABLE --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">

                <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ __('Other Income Records') }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Income received outside normal POS sales.') }}
                        </p>
                    </div>

                    <a href="{{ route('other_incomes.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                        + {{ __('Add Income') }}
                    </a>

                </div>

                @if($otherIncomes->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Amount') }}
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Reference') }}
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach($otherIncomes as $income)

                                    <tr class="hover:bg-gray-50">

                                        {{-- Date --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $income->income_date?->format('d M Y') }}
                                        </td>

                                        {{-- Category --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $income->category?->name ?? __('Uncategorized') }}
                                        </td>

                                        {{-- Amount --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-gray-900">
                                            {{ number_format($income->amount, 0) }} RWF
                                        </td>

                                        {{-- Reference --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $income->reference ?: '—' }}
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 whitespace-nowrap">

                                            @if($income->status === 'received')

                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    {{ __('Received') }}
                                                </span>

                                            @elseif($income->status === 'pending')

                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    {{ __('Pending') }}
                                                </span>

                                            @else

                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    {{ __('Cancelled') }}
                                                </span>

                                            @endif

                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">

                                            <div class="flex items-center justify-end gap-2">

                                                <a href="{{ route('other_incomes.show', $income) }}"
                                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                                    {{ __('View') }}
                                                </a>

                                                <a href="{{ route('other_incomes.edit', $income) }}"
                                                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                    {{ __('Edit') }}
                                                </a>

                                                <form action="{{ route('other_incomes.destroy', $income) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this income record?') }}');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800 font-medium">
                                                        {{ __('Delete') }}
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    {{-- Pagination --}}
                    @if($otherIncomes->hasPages())

                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $otherIncomes->links() }}
                        </div>

                    @endif

                @else

                    <div class="p-12 text-center">

                        <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">

                            <svg class="w-7 h-7 text-gray-400"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.105 0 2 .895 2 2m-2-2V6m0 12v-2m-6-4a6 6 0 1112 0 6 6 0 01-12 0z"/>
                            </svg>

                        </div>

                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('No Other Income Records') }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Start by recording your first other income transaction.') }}
                        </p>

                        <a href="{{ route('other_incomes.create') }}"
                           class="inline-flex items-center mt-5 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                            + {{ __('Add Other Income') }}
                        </a>

                    </div>

                @endif

            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- CHART.JS --}}
    {{-- ========================================================= --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | Daily Chart
            |--------------------------------------------------------------------------
            */

            const dailyCanvas = document.getElementById('dailyIncomeChart');

            if (dailyCanvas) {

                const dailyData = @json($dailyChart);

                const dailyLabels = dailyData.map(item => {
                    return new Date(item.income_date + 'T00:00:00')
                        .toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric'
                        });
                });

                const dailyValues = dailyData.map(item => Number(item.total));

                new Chart(dailyCanvas, {
                    type: 'line',

                    data: {
                        labels: dailyLabels,

                        datasets: [{
                            label: 'Other Income (RWF)',
                            data: dailyValues,
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat().format(value);
                                    }
                                }
                            }
                        },

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return new Intl.NumberFormat().format(context.raw) + ' RWF';
                                    }
                                }
                            }
                        }
                    }
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Monthly Chart
            |--------------------------------------------------------------------------
            */

            const monthlyCanvas = document.getElementById('monthlyIncomeChart');

            if (monthlyCanvas) {

                const monthlyData = @json($monthlyChart);

                const monthNames = [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ];

                const monthlyLabels = monthlyData.map(item => {
                    return monthNames[Number(item.month) - 1];
                });

                const monthlyValues = monthlyData.map(item => Number(item.total));

                new Chart(monthlyCanvas, {
                    type: 'bar',

                    data: {
                        labels: monthlyLabels,

                        datasets: [{
                            label: 'Other Income (RWF)',
                            data: monthlyValues,
                            borderWidth: 1
                        }]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat().format(value);
                                    }
                                }
                            }
                        },

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return new Intl.NumberFormat().format(context.raw) + ' RWF';
                                    }
                                }
                            }
                        }
                    }
                });
            }

        });
    </script>

</x-app-layout>
