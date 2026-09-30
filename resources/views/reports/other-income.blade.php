@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold">
        Other Income
    </h1>

    <p class="text-sm text-slate-500 mb-6">
        Income that does not come directly from product sales
    </p>


    @include(
        'reports.partials.filters',
        [
            'action' => route('reports.other-income')
        ]
    )


    @include(
        'reports.partials.other-income-table',
        ['data' => $data]
    )

</div>

@endsection