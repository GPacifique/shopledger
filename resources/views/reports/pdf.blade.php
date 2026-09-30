<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Mahwi Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 14px;
            margin-top: 25px;
        }

        .header {
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .label {
            color: #666;
            font-size: 9px;
        }

        .value {
            font-size: 13px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background: #f5f5f5;
        }

        .footer {
            margin-top: 30px;
            color: #777;
            font-size: 8px;
        }
    </style>
</head>

<body>

<div class="header">

    <h1>MAHWI BUSINESS REPORT</h1>

    <div>
        Period:
        {{ $startDate->format('d M Y') }}
        -
        {{ $endDate->format('d M Y') }}
    </div>

    @if($shop)
        <div>
            Shop: {{ $shop->name }}
        </div>
    @endif

    <div>
        Generated:
        {{ now()->format('d M Y H:i') }}
    </div>

</div>


{{-- SUMMARY --}}

<h2>Business Summary</h2>

<table class="summary">

    <tr>

        <td>
            <div class="label">Sales</div>

            <div class="value">
                {{ number_format($data['summary']['sales']) }} RWF
            </div>
        </td>

        <td>
            <div class="label">Purchases</div>

            <div class="value">
                {{ number_format($data['summary']['purchases']) }} RWF
            </div>
        </td>

        <td>
            <div class="label">Expenses</div>

            <div class="value">
                {{ number_format($data['summary']['expenses']) }} RWF
            </div>
        </td>

        <td>
            <div class="label">Other Income</div>

            <div class="value">
                {{ number_format($data['summary']['other_income']) }} RWF
            </div>
        </td>

    </tr>

</table>


{{-- SALES --}}

<h2>Sold Products</h2>

<table>

    <thead>

        <tr>

            <th>Date</th>

            <th>Order</th>

            <th>Product</th>

            <th>SKU</th>

            <th class="right">Qty</th>

            <th class="right">Buying</th>

            <th class="right">Selling</th>

            <th class="right">Revenue</th>

            <th class="right">Profit</th>

        </tr>

    </thead>


    <tbody>

        @foreach($data['sales']['items'] as $row)

            @php

                $item = $row['item'];

                $product = $row['product'];

                $quantity = (float) ($item->quantity ?? 0);

                $buyingPrice = (float) (
                    $item->buying_price
                    ?? $item->cost_price
                    ?? $product?->buying_price
                    ?? 0
                );

                $sellingPrice = (float) (
                    $item->selling_price
                    ?? $item->unit_price
                    ?? $item->price
                    ?? 0
                );

                $revenue = $quantity * $sellingPrice;

                $cost = $quantity * $buyingPrice;

                $profit = $revenue - $cost;

            @endphp

            <tr>

                <td>
                    {{ $row['order']->created_at?->format('d/m/Y') }}
                </td>

                <td>
                    #{{ $row['order']->id }}
                </td>

                <td>
                    {{ $product?->name ?? 'Unknown' }}
                </td>

                <td>
                    {{ $product?->sku ?? $item->sku ?? '' }}
                </td>

                <td class="right">
                    {{ number_format($quantity, 2) }}
                </td>

                <td class="right">
                    {{ number_format($buyingPrice) }}
                </td>

                <td class="right">
                    {{ number_format($sellingPrice) }}
                </td>

                <td class="right">
                    {{ number_format($revenue) }}
                </td>

                <td class="right">
                    {{ number_format($profit) }}
                </td>

            </tr>

        @endforeach

    </tbody>


    <tfoot>

        <tr class="total">

            <td colspan="4">
                TOTAL
            </td>

            <td class="right">
                {{ number_format($data['sales']['quantity'], 2) }}
            </td>

            <td></td>

            <td></td>

            <td class="right">
                {{ number_format($data['sales']['total']) }}
            </td>

            <td class="right">
                {{ number_format(
                    $data['sales']['total']
                    - $data['sales']['cost']
                ) }}
            </td>

        </tr>

    </tfoot>

</table>


{{-- EXPENSES --}}

<h2>Expenses</h2>

<table>

    <thead>

        <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Description</th>
            <th>Payment Method</th>
            <th class="right">Amount</th>
        </tr>

    </thead>

    <tbody>

        @foreach($data['expenses']['expenses'] as $expense)

            <tr>

                <td>
                    {{ $expense->created_at?->format('d/m/Y') }}
                </td>

                <td>
                    {{ $expense->category ?? 'Other' }}
                </td>

                <td>
                    {{ $expense->description ?? $expense->name ?? '-' }}
                </td>

                <td>
                    {{ $expense->payment_method ?? '-' }}
                </td>

                <td class="right">
                    {{ number_format(
                        $expense->amount
                        ?? $expense->total
                        ?? 0
                    ) }}
                </td>

            </tr>

        @endforeach

    </tbody>

    <tfoot>

        <tr class="total">

            <td colspan="4">
                TOTAL
            </td>

            <td class="right">
                {{ number_format($data['expenses']['total']) }}
            </td>

        </tr>

    </tfoot>

</table>


{{-- OTHER INCOME --}}

<h2>Other Income</h2>

<table>

    <thead>

        <tr>
            <th>Date</th>
            <th>Source</th>
            <th>Description</th>
            <th>Payment Method</th>
            <th class="right">Amount</th>
        </tr>

    </thead>

    <tbody>

        @foreach($data['other_income']['items'] as $income)

            <tr>

                <td>
                    {{ $income->created_at?->format('d/m/Y') }}
                </td>

                <td>
                    {{ $income->source ?? $income->category ?? '-' }}
                </td>

                <td>
                    {{ $income->description ?? $income->name ?? '-' }}
                </td>

                <td>
                    {{ $income->payment_method ?? '-' }}
                </td>

                <td class="right">
                    {{ number_format(
                        $income->amount
                        ?? $income->total
                        ?? 0
                    ) }}
                </td>

            </tr>

        @endforeach

    </tbody>

    <tfoot>

        <tr class="total">

            <td colspan="4">
                TOTAL
            </td>

            <td class="right">
                {{ number_format($data['other_income']['total']) }}
            </td>

        </tr>

    </tfoot>

</table>


{{-- PROFIT --}}

<h2>Profit & Loss Summary</h2>

<table>

    <tr>
        <td>Sales Revenue</td>
        <td class="right">
            {{ number_format($data['summary']['sales']) }} RWF
        </td>
    </tr>

    <tr>
        <td>Cost of Goods Sold</td>
        <td class="right">
            {{ number_format($data['summary']['sales_cost']) }} RWF
        </td>
    </tr>

    <tr class="total">
        <td>Gross Profit</td>
        <td class="right">
            {{ number_format($data['summary']['gross_profit']) }} RWF
        </td>
    </tr>

    <tr>
        <td>Expenses</td>
        <td class="right">
            {{ number_format($data['summary']['expenses']) }} RWF
        </td>
    </tr>

    <tr>
        <td>Other Income</td>
        <td class="right">
            {{ number_format($data['summary']['other_income']) }} RWF
        </td>
    </tr>

    <tr class="total">
        <td>NET PROFIT</td>
        <td class="right">
            {{ number_format($data['summary']['net_profit']) }} RWF
        </td>
    </tr>

</table>


<div class="footer">
    Mahwi Business Management System
</div>

</body>
</html>