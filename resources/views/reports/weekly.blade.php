@extends('layouts.app')

@section('content')

@include('partials.ui-theme')

<div class="p-4 sm:p-6">

    <div class="mb-6">
        <h1 class="ui-text text-2xl font-bold">Weekly Report</h1>
        <p class="ui-muted mt-1 text-sm">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    {{-- Filters. The export partial sits next to the form, not inside it, so forms never nest. --}}
    <div class="ui-card mb-6 flex flex-wrap items-end justify-between gap-4 rounded-xl p-5">

        <form method="GET" action="{{ route('reports.weekly') }}" class="flex flex-wrap items-end gap-4">

                <div>
                    <label for="report-date" class="ui-label mb-1 block text-sm font-medium">Week containing</label>
                    <input id="report-date" type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="ui-input rounded-lg">
                </div>

                <div>
                    <label for="report-shop" class="ui-label mb-1 block text-sm font-medium">Shop</label>
                    <select id="report-shop" name="shop_id" class="ui-input rounded-lg">
                        <option value="">Current Shop</option>

                        @foreach(\App\Models\Shop::all() as $reportShop)
                            <option value="{{ $reportShop->id }}" @selected(request('shop_id') == $reportShop->id)>
                                {{ $reportShop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="ui-btn-dark ui-focus rounded-lg px-5 py-2.5">Generate</button>
        </form>

        @include('reports.partials.export-buttons')

    </div>

    @include('reports.partials.summary-cards')

    <div class="mt-6">
        @include('reports.partials.daily-breakdown')
    </div>

    <div class="mt-6 space-y-6">

        @include('reports.partials.sales-table',        ['data' => $data['sales']])
        @include('reports.partials.purchases-table',    ['data' => $data['purchases']])
        @include('reports.partials.expenses-table',     ['data' => $data['expenses']])
        @include('reports.partials.other-income-table', ['data' => $data['other_income']])

    </div>

</div>

@endsection