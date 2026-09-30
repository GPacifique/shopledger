@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="mb-6">

        <h1 class="text-2xl font-bold">
            Sales Report
        </h1>

        <p class="text-sm text-slate-500">
            Detailed products sold
        </p>

    </div>


    @include(
        'reports.partials.filters',
        [
            'action' => route('reports.sales')
        ]
    )


    <div class="mb-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="bg-white border rounded-xl p-5">

                <p class="text-sm text-slate-500">
                    Orders
                </p>

                <p class="text-2xl font-bold mt-2">
                    {{ number_format($data['order_count']) }}
                </p>

            </div>


            <div class="bg-white border rounded-xl p-5">

                <p class="text-sm text-slate-500">
                    Products Sold
                </p>

                <p class="text-2xl font-bold mt-2">
                    {{ number_format($data['quantity'], 2) }}
                </p>

            </div>


            <div class="bg-white border rounded-xl p-5">

                <p class="text-sm text-slate-500">
                    Revenue
                </p>

                <p class="text-2xl font-bold mt-2">
                    {{ number_format($data['total']) }}
                    RWF
                </p>

            </div>

        </div>

    </div>


    @include(
        'reports.partials.sales-table',
        ['data' => $data]
    )

</div>

@endsection