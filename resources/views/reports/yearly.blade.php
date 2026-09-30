@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-slate-800">
            Yearly Report
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            {{ $startDate->format('Y') }}
        </p>

    </div>


    <form method="GET"
          action="{{ route('reports.yearly') }}"
          class="bg-white border rounded-xl p-5 mb-6">

        <div class="flex flex-wrap gap-4 items-end">

            <div>

                <label class="block text-sm font-medium mb-1">
                    Year
                </label>

                <select
                    name="date"
                    class="rounded-lg border-slate-300">

                    @for($year = now()->year; $year >= now()->year - 10; $year--)

                        <option
                            value="{{ $year }}-01-01"
                            @selected($date->year == $year)
                        >
                            {{ $year }}
                        </option>

                    @endfor

                </select>

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
@include('reports.partials.export-buttons')
        </div>

    </form>


    @include('reports.partials.summary-cards')


    <div class="mt-6">

        @include('reports.partials.daily-breakdown')

    </div>


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