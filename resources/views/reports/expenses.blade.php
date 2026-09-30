@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold">
        Expense Report
    </h1>

    <p class="text-sm text-slate-500 mb-6">
        Business expenses for the selected period
    </p>


    @include(
        'reports.partials.filters',
        [
            'action' => route('reports.expenses')
        ]
    )


    @include(
        'reports.partials.expenses-table',
        ['data' => $data]
    )

</div>

@endsection