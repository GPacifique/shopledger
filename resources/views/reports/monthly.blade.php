@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-slate-800">
            Monthly Report
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            {{ $startDate->format('F Y') }}
        </p>

    </div>


    <form method="GET"
          action="{{ route('reports.monthly') }}"
          class="bg-white border rounded-xl p-5 mb-6">

        <div class="flex flex-wrap gap-4 items-end">

            <div>

                <label class="block text-sm font-medium mb-1">
                    Month
                </label>

                <input
                    type="month"
                    name="date"
                    value="{{ $date->format('Y-m') }}"
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

    </form>


    @include('reports.partials.summary-cards')


    <div class="mt-6">

        @include('reports.partials.daily-breakdown')

    </div>

@include('reports.partials.export-buttons')
    <div class="mt-6 space-y-6">

        @include(
            'reports.partials.sales-table',
            ['data' => $data['sales']]
        )

        @include(
            'reports.partials.purchases-table',
            ['data' => $data['purchases']]
        )

        @include(
            'reports.partials.expenses-table',
            ['data' => $data['expenses']]
        )

        @include(
            'reports.partials.other-income-table',
            ['data' => $data['other_income']]
        )

    </div>

</div>

@endsection