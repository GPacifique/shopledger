@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold">
        Purchase Report
    </h1>

    <p class="text-sm text-slate-500 mb-6">
        Detailed purchases and stock acquisition
    </p>


    @include(
        'reports.partials.filters',
        [
            'action' => route('reports.purchases')
        ]
    )


    @include(
        'reports.partials.purchases-table',
        ['data' => $data]
    )

</div>

@endsection