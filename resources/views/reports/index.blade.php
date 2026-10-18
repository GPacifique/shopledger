@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50/70 p-4 sm:p-6 lg:p-8">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="mb-2 flex items-center gap-2 text-sm text-slate-500">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="font-medium text-indigo-600">Reports</span>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Reports & Analytics
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Monitor business performance, review financial activity, and track profitability.
            </p>
        </div>

        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Report Center
                </p>
                <p class="text-sm font-bold text-slate-800">
                    Business Overview
                </p>
            </div>
        </div>

    </div>


    {{-- Report Periods --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">
                Report Periods
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Choose a reporting period to view your financial results.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Daily --}}
            <a href="{{ route('reports.daily') }}"
               class="group relative overflow-hidden rounded-2xl border border-green-700 bg-gradient-to-br from-green-700 to-green-950 p-5 text-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-100">
                            Today
                        </p>
                        <h3 class="mt-2 text-xl font-bold">
                            Daily Report
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 transition group-hover:bg-white/25">
                        <i class="fas fa-calendar-day text-xl"></i>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-white/15 pt-4">
                    <span class="text-sm text-green-100">View daily activity</span>
                    <i class="fas fa-arrow-right transition group-hover:translate-x-1"></i>
                </div>

                <i class="fas fa-chart-line absolute -bottom-5 -left-2 text-7xl text-white/5"></i>
            </a>


            {{-- Weekly --}}
            <a href="{{ route('reports.weekly') }}"
               class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            This Week
                        </p>
                        <h3 class="mt-2 text-xl font-bold text-slate-800">
                            Weekly Report
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-100">
                        <i class="fas fa-calendar-week text-xl"></i>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-sm text-slate-500">Review weekly results</span>
                    <i class="fas fa-arrow-right text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                </div>
            </a>


            {{-- Monthly --}}
            <a href="{{ route('reports.monthly') }}"
               class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-violet-200 hover:shadow-lg">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            This Month
                        </p>
                        <h3 class="mt-2 text-xl font-bold text-slate-800">
                            Monthly Report
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 transition group-hover:bg-violet-100">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-sm text-slate-500">Review monthly results</span>
                    <i class="fas fa-arrow-right text-slate-400 transition group-hover:translate-x-1 group-hover:text-violet-600"></i>
                </div>
            </a>


            {{-- Yearly --}}
            <a href="{{ route('reports.yearly') }}"
               class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            This Year
                        </p>
                        <h3 class="mt-2 text-xl font-bold text-slate-800">
                            Yearly Report
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-100">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-sm text-slate-500">Review yearly results</span>
                    <i class="fas fa-arrow-right text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600"></i>
                </div>
            </a>

        </div>

    </section>


    {{-- Filters --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">
                Filter Reports
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Narrow your reports to a specific date range or available filter.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @include(
                'reports.partials.filters',
                [
                    'action' => route('reports.index')
                ]
            )
        </div>

    </section>


    {{-- Financial Summary --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">
                Financial Summary
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Key financial indicators for the selected reporting period.
            </p>
        </div>

        @include('reports.partials.summary-cards')

    </section>


    {{-- Detailed Reports --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">
                Detailed Reports
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Open a report for more detailed financial information.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Sales --}}
            <a href="{{ route('reports.sales') }}"
               class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-green-200 hover:shadow-md">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600 transition group-hover:bg-green-100">
                    <i class="fas fa-cash-register text-xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-800">Sales Report</h3>
                    <p class="mt-1 text-xs text-slate-500">Sales transactions and totals</p>
                </div>

                <i class="fas fa-chevron-right text-sm text-slate-300 transition group-hover:translate-x-1 group-hover:text-green-600"></i>
            </a>


            {{-- Purchases --}}
            <a href="{{ route('reports.purchases') }}"
               class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-md">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-100">
                    <i class="fas fa-truck-loading text-xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-800">Purchase Report</h3>
                    <p class="mt-1 text-xs text-slate-500">Purchases and stock costs</p>
                </div>

                <i class="fas fa-chevron-right text-sm text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
            </a>


            {{-- Expenses --}}
            <a href="{{ route('reports.expenses') }}"
               class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-red-200 hover:shadow-md">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 transition group-hover:bg-red-100">
                    <i class="fas fa-wallet text-xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-800">Expense Report</h3>
                    <p class="mt-1 text-xs text-slate-500">Business expenses and costs</p>
                </div>

                <i class="fas fa-chevron-right text-sm text-slate-300 transition group-hover:translate-x-1 group-hover:text-red-600"></i>
            </a>


            {{-- Profit and Loss --}}
            <a href="{{ route('reports.profit-loss') }}"
               class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-100">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-800">Profit &amp; Loss</h3>
                    <p class="mt-1 text-xs text-slate-500">Income, costs and profitability</p>
                </div>

                <i class="fas fa-chevron-right text-sm text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-600"></i>
            </a>

        </div>

    </section>


    {{-- Period Performance --}}
    <section class="mb-6">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">
                Period Performance
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Review daily financial activity and compare performance throughout the selected period.
            </p>
        </div>

        @include('reports.partials.daily-breakdown')

    </section>

</div>

@endsection
