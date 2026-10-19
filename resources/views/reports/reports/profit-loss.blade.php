@extends('layouts.app')

@section('content')

@include('reports.partials.ui-theme')

<div class="p-4 sm:p-6">

    <div class="mb-6">
        <h1 class="ui-text text-2xl font-bold">Profit &amp; Loss</h1>
        <p class="ui-muted mt-1 text-sm">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    @include('reports.partials.filters', ['action' => route('reports.profit-loss')])

    @php
        $netTone = $data['net_profit'] >= 0 ? 'tone-green' : 'tone-red';
    @endphp

    <div class="ui-card overflow-hidden rounded-xl">
        <div class="space-y-5 p-6">

            <div class="flex justify-between gap-4">
                <span class="ui-soft">Sales Revenue</span>
                <strong class="ui-text">{{ number_format($data['sales']) }} RWF</strong>
            </div>

            <div class="flex justify-between gap-4">
                <span class="ui-soft">Cost of Goods Sold</span>
                <strong class="tone-red st">{{ number_format($data['sales_cost']) }} RWF</strong>
            </div>

            <div class="ui-line flex justify-between gap-4 border-t pt-5 text-lg">
                <span class="ui-text">Gross Profit</span>
                <strong class="ui-text">{{ number_format($data['gross_profit']) }} RWF</strong>
            </div>

            <div class="flex justify-between gap-4">
                <span class="ui-soft">Expenses</span>
                <strong class="tone-orange st">{{ number_format($data['expenses']) }} RWF</strong>
            </div>

            <div class="flex justify-between gap-4">
                <span class="ui-soft">Other Income</span>
                <strong class="tone-emerald st">{{ number_format($data['other_income']) }} RWF</strong>
            </div>

            <div class="ui-line flex justify-between gap-4 border-t pt-5 text-xl font-bold">
                <span class="ui-text">Net Profit</span>
                <strong class="{{ $netTone }} st">{{ number_format($data['net_profit']) }} RWF</strong>
            </div>

        </div>
    </div>

</div>

@endsection
