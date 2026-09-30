@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Daily Report
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                {{ $date->format('l, d F Y') }}
            </p>

        </div>

        <button
            onclick="window.print()"
            class="mt-4 md:mt-0 px-4 py-2 rounded-lg bg-slate-900 text-white">

            Print Report

        </button>

    </div>


    <form method="GET"
          action="{{ route('reports.daily') }}"
          class="bg-white border rounded-xl p-5 mb-6">

        <div class="flex flex-col md:flex-row gap-4 items-end">

            <div>

                <label class="block text-sm font-medium mb-1">
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    value="{{ $date->format('Y-m-d') }}"
                    class="rounded-lg border-slate-300"
                >

            </div>


            <div>

                <label class="block text-sm font-medium mb-1">
                    Shop
                </label>

                <select
                    name="shop_id"
                    class="rounded-lg border-slate-300">

                    <option value="">
                        Current Shop
                    </option>

                    @foreach(\App\Models\Shop::all() as $reportShop)

                        <option
                            value="{{ $reportShop->id }}"
                            @selected(request('shop_id') == $reportShop->id)
                        >
                            {{ $reportShop->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <button
                class="px-5 py-2.5 bg-slate-900 text-white rounded-lg">

                Generate

            </button>

        </div>
@include('reports.partials.export-buttons')
    </form>


    @include('reports.partials.summary-cards')


    <div class="mt-6 space-y-6">

        @php
            $salesData = $data['sales'];
        @endphp

        @include(
            'reports.partials.sales-table',
            ['data' => $salesData]
        )


        @php
            $purchaseData = $data['purchases'];
        @endphp

        @include(
            'reports.partials.purchases-table',
            ['data' => $purchaseData]
        )


        @php
            $expenseData = $data['expenses'];
        @endphp

        @include(
            'reports.partials.expenses-table',
            ['data' => $expenseData]
        )


        @php
            $incomeData = $data['other_income'];
        @endphp

        @include(
            'reports.partials.other-income-table',
            ['data' => $incomeData]
        )

    </div>

</div>

@endsection