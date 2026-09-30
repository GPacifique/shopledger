@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-slate-800">
            Profit & Loss
        </h1>

        <p class="text-sm text-slate-500">
            {{ $startDate->format('d M Y') }}
            -
            {{ $endDate->format('d M Y') }}
        </p>

    </div>


    @include(
        'reports.partials.filters',
        [
            'action' => route('reports.profit-loss')
        ]
    )


    <div class="bg-white border rounded-xl overflow-hidden">

        <div class="p-6">

            <div class="space-y-5">

                <div class="flex justify-between">

                    <span>
                        Sales Revenue
                    </span>

                    <strong>
                        {{ number_format($data['sales']) }} RWF
                    </strong>

                </div>


                <div class="flex justify-between">

                    <span>
                        Cost of Goods Sold
                    </span>

                    <strong>
                        {{ number_format($data['sales_cost']) }} RWF
                    </strong>

                </div>


                <div class="border-t pt-5 flex justify-between text-lg">

                    <span>
                        Gross Profit
                    </span>

                    <strong>
                        {{ number_format($data['gross_profit']) }} RWF
                    </strong>

                </div>


                <div class="flex justify-between">

                    <span>
                        Expenses
                    </span>

                    <strong>
                        {{ number_format($data['expenses']) }} RWF
                    </strong>

                </div>


                <div class="flex justify-between">

                    <span>
                        Other Income
                    </span>

                    <strong>
                        {{ number_format($data['other_income']) }} RWF
                    </strong>

                </div>


                <div class="border-t pt-5 flex justify-between text-xl font-bold">

                    <span>
                        Net Profit
                    </span>

                    <strong>
                        {{ number_format($data['net_profit']) }} RWF
                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection