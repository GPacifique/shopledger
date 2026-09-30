@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Reports
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Business performance and financial reports
            </p>

        </div>

    </div>


    {{-- Report Types --}}

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        <a href="{{ route('reports.daily') }}"
           class="bg-white border rounded-xl p-5 hover:shadow-md transition">

            <p class="text-sm text-slate-500">
                Today
            </p>

            <h2 class="font-semibold text-lg mt-1">
                Daily Report
            </h2>

        </a>


        <a href="{{ route('reports.weekly') }}"
           class="bg-white border rounded-xl p-5 hover:shadow-md transition">

            <p class="text-sm text-slate-500">
                This Week
            </p>

            <h2 class="font-semibold text-lg mt-1">
                Weekly Report
            </h2>

        </a>


        <a href="{{ route('reports.monthly') }}"
           class="bg-white border rounded-xl p-5 hover:shadow-md transition">

            <p class="text-sm text-slate-500">
                This Month
            </p>

            <h2 class="font-semibold text-lg mt-1">
                Monthly Report
            </h2>

        </a>


        <a href="{{ route('reports.yearly') }}"
           class="bg-white border rounded-xl p-5 hover:shadow-md transition">

            <p class="text-sm text-slate-500">
                This Year
            </p>

            <h2 class="font-semibold text-lg mt-1">
                Yearly Report
            </h2>

        </a>

    </div>


    @include(
        'reports.partials.filters',
        [
            'action' => route('reports.index')
        ]
    )


    @include('reports.partials.summary-cards')


    {{-- Quick links --}}

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

        <a href="{{ route('reports.sales') }}"
           class="bg-white border rounded-xl p-5">

            <span class="font-semibold">
                Sales Report
            </span>

        </a>


        <a href="{{ route('reports.purchases') }}"
           class="bg-white border rounded-xl p-5">

            <span class="font-semibold">
                Purchase Report
            </span>

        </a>


        <a href="{{ route('reports.expenses') }}"
           class="bg-white border rounded-xl p-5">

            <span class="font-semibold">
                Expense Report
            </span>

        </a>


        <a href="{{ route('reports.profit-loss') }}"
           class="bg-white border rounded-xl p-5">

            <span class="font-semibold">
                Profit & Loss
            </span>

        </a>

    </div>


    {{-- Performance --}}

    <div class="mt-6">

        @include('reports.partials.daily-breakdown')

    </div>

</div>

@endsection