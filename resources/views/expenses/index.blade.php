<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Expenses
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Track and manage your business expenses.
            </p>
        </div>

        @if (auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())
            <a
                href="{{ route('expenses.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700"
            >
                <svg
                    class="mr-2 h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Add Expense
            </a>
        @endif
    </div>
</x-slot>

@php
    /*
    |--------------------------------------------------------------------------
    | Safe defaults
    |--------------------------------------------------------------------------
    */
    $dailyExpense = (float) ($dailyExpense ?? 0);
    $weeklyExpense = (float) ($weeklyExpense ?? 0);
    $monthlyExpense = (float) ($monthlyExpense ?? 0);
    $yearlyExpense = (float) ($yearlyExpense ?? 0);

    $dailyTransactions = (int) ($dailyTransactions ?? 0);
    $weeklyTransactions = (int) ($weeklyTransactions ?? 0);
    $monthlyTransactions = (int) ($monthlyTransactions ?? 0);
    $yearlyTransactions = (int) ($yearlyTransactions ?? 0);

    $totalPaid = (float) ($totalPaid ?? 0);
    $totalUnpaid = (float) ($totalUnpaid ?? 0);

    $dailyTrend = $dailyTrend ?? [
        'direction' => 'same',
        'percentage' => 0,
    ];

    $weeklyTrend = $weeklyTrend ?? [
        'direction' => 'same',
        'percentage' => 0,
    ];

    $monthlyTrend = $monthlyTrend ?? [
        'direction' => 'same',
        'percentage' => 0,
    ];

    $dailyChart = $dailyChart ?? [];
    $weeklyChart = $weeklyChart ?? [];
    $monthlyChart = $monthlyChart ?? [];
    $categoryBreakdown = $categoryBreakdown ?? [];

    $categories = $categories ?? collect();
@endphp

<div class="py-6">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

        {{-- Success --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- =========================================================
             STATISTICS
        ========================================================== --}}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Daily --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">
                    Today's Expense
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ number_format($dailyExpense, 0) }} RWF
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $dailyTransactions }}
                    transaction{{ $dailyTransactions != 1 ? 's' : '' }}
                </p>

                <div class="mt-4">
                    @if (($dailyTrend['direction'] ?? 'same') === 'up')
                        <span class="text-sm font-medium text-red-600">
                            ↑ {{ number_format(abs((float) ($dailyTrend['percentage'] ?? 0)), 1) }}%
                            <span class="text-gray-500">vs yesterday</span>
                        </span>
                    @elseif (($dailyTrend['direction'] ?? 'same') === 'down')
                        <span class="text-sm font-medium text-green-600">
                            ↓ {{ number_format(abs((float) ($dailyTrend['percentage'] ?? 0)), 1) }}%
                            <span class="text-gray-500">vs yesterday</span>
                        </span>
                    @else
                        <span class="text-sm text-gray-500">
                            No change from yesterday
                        </span>
                    @endif
                </div>
            </div>

            {{-- Weekly --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">
                    This Week
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ number_format($weeklyExpense, 0) }} RWF
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $weeklyTransactions }}
                    transaction{{ $weeklyTransactions != 1 ? 's' : '' }}
                </p>

                <div class="mt-4">
                    @if (($weeklyTrend['direction'] ?? 'same') === 'up')
                        <span class="text-sm font-medium text-red-600">
                            ↑ {{ number_format(abs((float) ($weeklyTrend['percentage'] ?? 0)), 1) }}%
                            <span class="text-gray-500">vs last week</span>
                        </span>
                    @elseif (($weeklyTrend['direction'] ?? 'same') === 'down')
                        <span class="text-sm font-medium text-green-600">
                            ↓ {{ number_format(abs((float) ($weeklyTrend['percentage'] ?? 0)), 1) }}%
                            <span class="text-gray-500">vs last week</span>
                        </span>
                    @else
                        <span class="text-sm text-gray-500">
                            No change from last week
                        </span>
                    @endif
                </div>
            </div>

            {{-- Monthly --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">
                    This Month
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ number_format($monthlyExpense, 0) }} RWF
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $monthlyTransactions }}
                    transaction{{ $monthlyTransactions != 1 ? 's' : '' }}
                </p>

                <div class="mt-4">
                    @if (($monthlyTrend['direction'] ?? 'same') === 'up')
                        <span class="text-sm font-medium text-red-600">
                            ↑ {{ number_format(abs((float) ($monthlyTrend['percentage'] ?? 0)), 1) }}%
                            <span class="text-gray-500">vs last month</span>
                        </span>
                    @elseif (($monthlyTrend['direction'] ?? 'same') === 'down')
                        <span class="text-sm font-medium text-green-600">
                            ↓ {{ number_format(abs((float) ($monthlyTrend['percentage'] ?? 0)), 1) }}%
                            <span class="text-gray-500">vs last month</span>
                        </span>
                    @else
                        <span class="text-sm text-gray-500">
                            No change from last month
                        </span>
                    @endif
                </div>
            </div>

            {{-- Yearly --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">
                    This Year
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ number_format($yearlyExpense, 0) }} RWF
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $yearlyTransactions }}
                    transaction{{ $yearlyTransactions != 1 ? 's' : '' }}
                </p>

                <p class="mt-4 text-sm text-gray-500">
                    Total expenses recorded this year
                </p>
            </div>

        </div>

        {{-- =========================================================
             PAID / UNPAID
        ========================================================== --}}

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">
                    Total Paid Expenses
                </p>

                <p class="mt-2 text-2xl font-bold text-green-600">
                    {{ number_format($totalPaid, 0) }} RWF
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">
                    Total Unpaid Expenses
                </p>

                <p class="mt-2 text-2xl font-bold text-red-600">
                    {{ number_format($totalUnpaid, 0) }} RWF
                </p>
            </div>

        </div>

        {{-- =========================================================
             CHARTS
        ========================================================== --}}

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{-- Daily --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Daily Expense Trend
                </h3>

                <p class="mb-5 text-sm text-gray-500">
                    Expenses for the last 7 days
                </p>

                <div class="h-80">
                    <canvas id="dailyExpenseChart"></canvas>
                </div>
            </div>

            {{-- Weekly --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Weekly Expense Trend
                </h3>

                <p class="mb-5 text-sm text-gray-500">
                    Expenses for the last 8 weeks
                </p>

                <div class="h-80">
                    <canvas id="weeklyExpenseChart"></canvas>
                </div>
            </div>

            {{-- Monthly --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Monthly Expense Trend
                </h3>

                <p class="mb-5 text-sm text-gray-500">
                    Expenses for the last 6 months
                </p>

                <div class="h-80">
                    <canvas id="monthlyExpenseChart"></canvas>
                </div>
            </div>

            {{-- Category --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Expenses by Category
                </h3>

                <p class="mb-5 text-sm text-gray-500">
                    Expense distribution by category
                </p>

                <div class="h-80">
                    <canvas id="categoryExpenseChart"></canvas>
                </div>
            </div>

        </div>

        {{-- =========================================================
             FILTERS
        ========================================================== --}}

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

            <h3 class="mb-5 text-lg font-semibold text-gray-900">
                Filter Expenses
            </h3>

            <form
                method="GET"
                action="{{ route('expenses.index') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4"
            >

                {{-- Category --}}
                <div>
                    <label
                        for="category_id"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Category
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Categories
                        </option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(request('category_id') == $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label
                        for="status"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Statuses
                        </option>

                        <option
                            value="paid"
                            @selected(request('status') === 'paid')
                        >
                            Paid
                        </option>

                        <option
                            value="unpaid"
                            @selected(request('status') === 'unpaid')
                        >
                            Unpaid
                        </option>
                    </select>
                </div>

                {{-- Start --}}
                <div>
                    <label
                        for="start_date"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        From Date
                    </label>

                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                {{-- End --}}
                <div>
                    <label
                        for="end_date"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        To Date
                    </label>

                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                {{-- Minimum --}}
                <div>
                    <label
                        for="min_amount"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Minimum Amount
                    </label>

                    <input
                        type="number"
                        id="min_amount"
                        name="min_amount"
                        value="{{ request('min_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="0"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                {{-- Maximum --}}
                <div>
                    <label
                        for="max_amount"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Maximum Amount
                    </label>

                    <input
                        type="number"
                        id="max_amount"
                        name="max_amount"
                        value="{{ request('max_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="0"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                {{-- Reference --}}
                <div>
                    <label
                        for="reference"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Reference
                    </label>

                    <input
                        type="text"
                        id="reference"
                        name="reference"
                        value="{{ request('reference') }}"
                        placeholder="Search reference"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                {{-- Buttons --}}
                <div class="flex items-end gap-3">
                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('expenses.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Reset
                    </a>
                </div>

            </form>
        </div>

        {{-- =========================================================
             EXPENSE TABLE
        ========================================================== --}}

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold text-gray-900">
                    Expense Records
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $expenses->total() }}
                    expense{{ $expenses->total() != 1 ? 's' : '' }}
                    found
                </p>
            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Date
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Category
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Description
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Reference
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Amount
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">

                        @forelse ($expenses as $expense)

                            <tr class="hover:bg-gray-50">

                                {{-- Date --}}
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $expense->expense_date
                                        ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y')
                                        : '-' }}
                                </td>

                                {{-- Category --}}
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $expense->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>

                                {{-- Description --}}
                                <td class="max-w-xs px-6 py-4 text-sm text-gray-600">
                                    <div class="truncate">
                                        {{ $expense->description ?: '-' }}
                                    </div>
                                </td>

                                {{-- Reference --}}
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $expense->reference ?: '-' }}
                                </td>

                                {{-- Amount --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                    {{ number_format((float) $expense->amount, 0) }} RWF
                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">

                                    @if ($expense->status === 'paid')
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Paid
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Unpaid
                                        </span>
                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">

                                    <div class="flex justify-end gap-3">

                                        @if ($expense->attachment)
                                            <a
                                                href="{{ route('expenses.download', $expense) }}"
                                                class="font-medium text-blue-600 hover:text-blue-800"
                                            >
                                                Attachment
                                            </a>
                                        @endif

                                        @if (auth()->user()->isSystemAdmin() || auth()->user()->isShopAdmin())

                                            <a
                                                href="{{ route('expenses.edit', $expense) }}"
                                                class="font-medium text-indigo-600 hover:text-indigo-800"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('expenses.toggle-status', $expense) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="font-medium text-orange-600 hover:text-orange-800"
                                                >
                                                    Toggle
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route('expenses.destroy', $expense) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this expense?');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="font-medium text-red-600 hover:text-red-800"
                                                >
                                                    Delete
                                                </button>
                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="7"
                                    class="px-6 py-12 text-center text-sm text-gray-500"
                                >
                                    No expenses found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($expenses->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $expenses->withQueryString()->links() }}
                </div>
            @endif

        </div>

    </div>
</div>

{{-- ===============================================================
     CHART.JS
================================================================ --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const dailyData = @json($dailyChart);
        const weeklyData = @json($weeklyChart);
        const monthlyData = @json($monthlyChart);
        const categoryData = @json($categoryBreakdown);

        /*
        |--------------------------------------------------------------------------
        | Helper
        |--------------------------------------------------------------------------
        */

        function formatRwf(value) {
            return Number(value || 0).toLocaleString() + ' RWF';
        }

        /*
        |--------------------------------------------------------------------------
        | Daily
        |--------------------------------------------------------------------------
        */

        const dailyElement =
            document.getElementById('dailyExpenseChart');

        if (dailyElement) {

            new Chart(dailyElement, {
                type: 'line',

                data: {
                    labels: dailyData.map(item => item.label),

                    datasets: [{
                        label: 'Expenses (RWF)',

                        data: dailyData.map(
                            item => Number(item.total || 0)
                        ),

                        borderWidth: 2,

                        tension: 0.4,

                        fill: true
                    }]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return formatRwf(context.raw);
                                }
                            }
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,

                            ticks: {
                                callback: function (value) {
                                    return formatRwf(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Weekly
        |--------------------------------------------------------------------------
        */

        const weeklyElement =
            document.getElementById('weeklyExpenseChart');

        if (weeklyElement) {

            new Chart(weeklyElement, {
                type: 'bar',

                data: {
                    labels: weeklyData.map(item => item.label),

                    datasets: [{
                        label: 'Expenses (RWF)',

                        data: weeklyData.map(
                            item => Number(item.total || 0)
                        ),

                        borderWidth: 1
                    }]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return formatRwf(context.raw);
                                }
                            }
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,

                            ticks: {
                                callback: function (value) {
                                    return formatRwf(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Monthly
        |--------------------------------------------------------------------------
        */

        const monthlyElement =
            document.getElementById('monthlyExpenseChart');

        if (monthlyElement) {

            new Chart(monthlyElement, {
                type: 'line',

                data: {
                    labels: monthlyData.map(item => item.label),

                    datasets: [{
                        label: 'Expenses (RWF)',

                        data: monthlyData.map(
                            item => Number(item.total || 0)
                        ),

                        borderWidth: 2,

                        tension: 0.4,

                        fill: true
                    }]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return formatRwf(context.raw);
                                }
                            }
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,

                            ticks: {
                                callback: function (value) {
                                    return formatRwf(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        const categoryElement =
            document.getElementById('categoryExpenseChart');

        if (categoryElement) {

            new Chart(categoryElement, {
                type: 'doughnut',

                data: {
                    labels: categoryData.map(
                        item => item.category_name
                    ),

                    datasets: [{
                        data: categoryData.map(
                            item => Number(item.total || 0)
                        )
                    }]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            position: 'bottom'
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label
                                        + ': '
                                        + formatRwf(context.raw);
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
