@extends('layouts.app')

@section('content')

@include('reports.partials.ui-theme')

<div class="p-4 sm:p-6">

    <div class="mb-6">
        <h1 class="ui-text text-2xl font-bold">Other Income</h1>
        <p class="ui-muted mt-1 text-sm">Income that does not come directly from product sales</p>
    </div>

    @include('reports.partials.filters', ['action' => route('reports.other-income')])

    @include('reports.partials.other-income-table', ['data' => $data])

</div>

@endsection
