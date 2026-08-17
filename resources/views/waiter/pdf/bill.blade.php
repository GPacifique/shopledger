<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Bill #{{ $order->order_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 28px;
        }

        h1 {
            font-size: 24px;
            margin: 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .box {
            border: 1px solid #ddd;
            padding: 12px;
            margin: 15px 0;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items th,
        .items td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .right {
            text-align: right;
        }

        .total {
            font-size: 17px;
            font-weight: bold;
        }

        .center {
            text-align: center;
            color: #777;
            margin-top: 25px;
        }

        .shop-name {
            font-size: 18px;
            font-weight: bold;
        }

        .muted {
            color: #666;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="row">
        <div>
            <h1>{{ $shop->business_name ?? 'MahWi POS' }}</h1>

            <div>Customer Bill / Receipt</div>

            @if(!empty($shop->address))
                <div class="muted">{{ $shop->address }}</div>
            @endif

            @if(!empty($shop->phone))
                <div class="muted">{{ $shop->phone }}</div>
            @endif
        </div>

        <div class="right">
            <strong>#{{ $order->order_number }}</strong>
            <br>

            {{ optional($order->created_at)->format('d/m/Y H:i') }}
        </div>
    </div>


    {{-- Order information --}}
    <div class="box">

        <div class="row">
            <span>
                <strong>Order:</strong>
                #{{ $order->order_number }}
            </span>

            <span>
                <strong>Waiter:</strong>
                {{ $order->creator->name ?? $order->user->name ?? auth()->user()->name ?? '—' }}
            </span>
        </div>

        @if(!empty($order->notes))
            <div style="margin-top: 8px;">
                <strong>Notes:</strong>
                {{ $order->notes }}
            </div>
        @endif

    </div>


    {{-- Items --}}
    <table class="items">

        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Amount</th>
            </tr>
        </thead>

        <tbody>

            @foreach($order->items as $item)

                @php
                    $quantity = (float) ($item->quantity ?? 0);
                    $unitPrice = (float) ($item->unit_price ?? $item->price ?? 0);
                    $amount = (float) ($item->total ?? ($unitPrice * $quantity));
                @endphp

                <tr>

                    <td>
                        {{ $item->product->name ?? $item->name ?? 'Item' }}
                    </td>

                    <td class="right">
                        {{ $quantity }}
                    </td>

                    <td class="right">
                        {{ number_format($unitPrice, 0) }}
                    </td>

                    <td class="right">
                        {{ number_format($amount, 0) }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    {{-- Totals --}}
    <div class="box">

        <div class="row">
            <span>Subtotal</span>

            <span>
                RWF
                {{ number_format((float) ($order->subtotal ?? 0), 0) }}
            </span>
        </div>


        @if((float) ($order->discount_amount ?? 0) > 0)

            <div class="row">
                <span>Discount</span>

                <span>
                    - RWF
                    {{ number_format((float) $order->discount_amount, 0) }}
                </span>
            </div>

        @endif


        @if((float) ($order->tax_amount ?? 0) > 0)

            <div class="row">
                <span>Tax</span>

                <span>
                    RWF
                    {{ number_format((float) $order->tax_amount, 0) }}
                </span>
            </div>

        @endif


        <div class="row total">

            <span>Total</span>

            <span>
                RWF
                {{ number_format((float) ($order->total_amount ?? 0), 0) }}
            </span>

        </div>

    </div>


    {{-- Payment --}}
    <div class="box">

        <div class="row">
            <span>
                <strong>Payment Method:</strong>
            </span>

            <span>
                {{ ucfirst($order->payment_method ?? 'Cash') }}
            </span>
        </div>

        <div class="row" style="margin-top: 8px;">
            <span>
                <strong>Payment Status:</strong>
            </span>

            <span>
                {{ ucfirst($order->payment_status ?? 'Unpaid') }}
            </span>
        </div>

    </div>


    <p class="center">
        Thank you for dining with us.
    </p>

    <p class="center">
        Powered by MahWi POS
    </p>

</body>
</html>