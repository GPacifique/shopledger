@extends('layouts.app')

@section('content')

@include('reports.partials.ui-theme')

<div class="p-4 sm:p-6">

    <div class="mb-6">
        <h1 class="ui-text text-2xl font-bold">Purchase Report</h1>
        <p class="ui-muted mt-1 text-sm">Detailed purchases and stock acquisition</p>
    </div>

    @include('reports.partials.filters', ['action' => route('reports.purchases')])

    @include('reports.partials.purchases-table', ['data' => $data])

</div>

@endsection
