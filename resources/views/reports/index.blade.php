@extends('layouts.app')

@section('content')

@include('partials.ui-theme')

<div class="ui-page min-h-screen p-4 sm:p-6 lg:p-8">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="ui-muted mb-2 flex items-center gap-2 text-sm">
                <i class="fas fa-home" aria-hidden="true"></i>
                <span>Dashboard</span>
                <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                <span class="tone-indigo st font-medium">Reports</span>
            </div>

            <h1 class="ui-text text-3xl font-extrabold tracking-tight">
                Reports &amp; Analytics
            </h1>

            <p class="ui-muted mt-2 text-sm">
                Monitor business performance, review financial activity, and track profitability.
            </p>
        </div>

        <div class="ui-card flex items-center gap-2 rounded-xl px-4 py-3 shadow-sm">
            <div class="ui-icon tone-indigo flex h-10 w-10 items-center justify-center rounded-lg">
                <i class="fas fa-chart-pie text-lg" aria-hidden="true"></i>
            </div>

            <div>
                <p class="ui-faint text-xs font-medium uppercase tracking-wide">Report Center</p>
                <p class="ui-text text-sm font-bold">Business Overview</p>
            </div>
        </div>

    </div>


    {{-- Report Periods --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="ui-text text-lg font-bold">Report Periods</h2>
            <p class="ui-muted mt-1 text-sm">Choose a reporting period to view your financial results.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Daily (highlighted: coloured card with white text works in both themes) --}}
            <a href="{{ route('reports.daily') }}"
               class="group relative overflow-hidden rounded-2xl border border-green-700 bg-gradient-to-br from-green-700 to-green-950 p-5 text-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg ui-focus">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-100">Today</p>
                        <h3 class="mt-2 text-xl font-bold">Daily Report</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 transition group-hover:bg-white/25">
                        <i class="fas fa-calendar-day text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-white/15 pt-4">
                    <span class="text-sm text-green-100">View daily activity</span>
                    <i class="fas fa-arrow-right transition group-hover:translate-x-1" aria-hidden="true"></i>
                </div>

                <i class="fas fa-chart-line absolute -bottom-5 -left-2 text-7xl text-white/5" aria-hidden="true"></i>
            </a>

            <a href="{{ route('reports.weekly') }}"
               class="ui-card ui-card-lift tone-blue group rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-lg ui-focus">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="ui-muted text-sm font-medium">This Week</p>
                        <h3 class="ui-text mt-2 text-xl font-bold">Weekly Report</h3>
                    </div>

                    <div class="ui-icon flex h-12 w-12 items-center justify-center rounded-xl">
                        <i class="fas fa-calendar-week text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="ui-line mt-6 flex items-center justify-between border-t pt-4">
                    <span class="ui-muted text-sm">Review weekly results</span>
                    <i class="fas fa-arrow-right ui-arrow group-hover:translate-x-1" aria-hidden="true"></i>
                </div>
            </a>

            <a href="{{ route('reports.monthly') }}"
               class="ui-card ui-card-lift tone-violet group rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-lg ui-focus">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="ui-muted text-sm font-medium">This Month</p>
                        <h3 class="ui-text mt-2 text-xl font-bold">Monthly Report</h3>
                    </div>

                    <div class="ui-icon flex h-12 w-12 items-center justify-center rounded-xl">
                        <i class="fas fa-calendar-alt text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="ui-line mt-6 flex items-center justify-between border-t pt-4">
                    <span class="ui-muted text-sm">Review monthly results</span>
                    <i class="fas fa-arrow-right ui-arrow group-hover:translate-x-1" aria-hidden="true"></i>
                </div>
            </a>

            <a href="{{ route('reports.yearly') }}"
               class="ui-card ui-card-lift tone-amber group rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-lg ui-focus">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="ui-muted text-sm font-medium">This Year</p>
                        <h3 class="ui-text mt-2 text-xl font-bold">Yearly Report</h3>
                    </div>

                    <div class="ui-icon flex h-12 w-12 items-center justify-center rounded-xl">
                        <i class="fas fa-calendar-check text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="ui-line mt-6 flex items-center justify-between border-t pt-4">
                    <span class="ui-muted text-sm">Review yearly results</span>
                    <i class="fas fa-arrow-right ui-arrow group-hover:translate-x-1" aria-hidden="true"></i>
                </div>
            </a>

        </div>

    </section>


    {{-- Filters --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="ui-text text-lg font-bold">Filter Reports</h2>
            <p class="ui-muted mt-1 text-sm">Narrow your reports to a specific date range or available filter.</p>
        </div>

        <div class="ui-card overflow-hidden rounded-2xl shadow-sm">
            @include('reports.partials.filters', ['action' => route('reports.index')])
        </div>

    </section>


    {{-- Financial Summary --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="ui-text text-lg font-bold">Financial Summary</h2>
            <p class="ui-muted mt-1 text-sm">Key financial indicators for the selected reporting period.</p>
        </div>

        @include('reports.partials.summary-cards')

    </section>


    {{-- Detailed Reports --}}
    <section class="mb-8">

        <div class="mb-4">
            <h2 class="ui-text text-lg font-bold">Detailed Reports</h2>
            <p class="ui-muted mt-1 text-sm">Open a report for more detailed financial information.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <a href="{{ route('reports.sales') }}"
               class="ui-card ui-card-lift tone-green group flex items-center gap-4 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md ui-focus">

                <div class="ui-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl">
                    <i class="fas fa-cash-register text-xl" aria-hidden="true"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="ui-text font-bold">Sales Report</h3>
                    <p class="ui-muted mt-1 text-xs">Sales transactions and totals</p>
                </div>

                <i class="fas fa-chevron-right ui-arrow text-sm group-hover:translate-x-1" aria-hidden="true"></i>
            </a>

            <a href="{{ route('reports.purchases') }}"
               class="ui-card ui-card-lift tone-blue group flex items-center gap-4 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md ui-focus">

                <div class="ui-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl">
                    <i class="fas fa-truck-loading text-xl" aria-hidden="true"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="ui-text font-bold">Purchase Report</h3>
                    <p class="ui-muted mt-1 text-xs">Purchases and stock costs</p>
                </div>

                <i class="fas fa-chevron-right ui-arrow text-sm group-hover:translate-x-1" aria-hidden="true"></i>
            </a>

            <a href="{{ route('reports.expenses') }}"
               class="ui-card ui-card-lift tone-red group flex items-center gap-4 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md ui-focus">

                <div class="ui-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl">
                    <i class="fas fa-wallet text-xl" aria-hidden="true"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="ui-text font-bold">Expense Report</h3>
                    <p class="ui-muted mt-1 text-xs">Business expenses and costs</p>
                </div>

                <i class="fas fa-chevron-right ui-arrow text-sm group-hover:translate-x-1" aria-hidden="true"></i>
            </a>

            <a href="{{ route('reports.profit-loss') }}"
               class="ui-card ui-card-lift tone-emerald group flex items-center gap-4 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md ui-focus">

                <div class="ui-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl">
                    <i class="fas fa-chart-line text-xl" aria-hidden="true"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="ui-text font-bold">Profit &amp; Loss</h3>
                    <p class="ui-muted mt-1 text-xs">Income, costs and profitability</p>
                </div>

                <i class="fas fa-chevron-right ui-arrow text-sm group-hover:translate-x-1" aria-hidden="true"></i>
            </a>

        </div>

    </section>


    {{-- Period Performance --}}
    <section class="mb-6">

        <div class="mb-4">
            <h2 class="ui-text text-lg font-bold">Period Performance</h2>
            <p class="ui-muted mt-1 text-sm">Review daily financial activity and compare performance throughout the selected period.</p>
        </div>

        @include('reports.partials.daily-breakdown')

    </section>

</div>

@endsection