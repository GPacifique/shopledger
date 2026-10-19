@extends('layouts.app')

@section('content')

@include('reports.partials.ui-theme')

<div class="p-4 sm:p-6">

    <div class="mb-6">
        <h1 class="ui-text text-2xl font-bold">Sales Report</h1>
        <p class="ui-muted mt-1 text-sm">Detailed products sold</p>
    </div>

    @include('reports.partials.filters', ['action' => route('reports.sales')])

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="ui-card rounded-xl p-5">
            <p class="ui-muted text-sm">Orders</p>
            <p class="ui-text mt-2 text-2xl font-bold">{{ number_format($data['order_count']) }}</p>
        </div>

        <div class="ui-card rounded-xl p-5">
            <p class="ui-muted text-sm">Products Sold</p>
            <p class="ui-text mt-2 text-2xl font-bold">{{ number_format($data['quantity'], 2) }}</p>
        </div>

        <div class="ui-card rounded-xl p-5">
            <p class="ui-muted text-sm">Revenue</p>
            <p class="tone-green st mt-2 text-2xl font-bold">{{ number_format($data['total']) }} RWF</p>
        </div>

    </div>

    @include('reports.partials.sales-table', ['data' => $data])

</div>

@endsection
