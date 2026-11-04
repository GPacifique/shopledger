<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bill {{ $order->order_number ?? $order->id }}</title>
    <style>
        @page { margin: 34px 38px; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #23332f;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.5;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .right { text-align: right; }
        .center { text-align: center; }
        .muted { color: #71807a; }
        .small { font-size: 9px; }
        .eyebrow {
            color: #ae8950;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header {
            background: #173a34;
            color: #fff;
        }
        .header td { padding: 24px 25px; }
        .brand {
            margin: 0 0 5px;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
        }
        .brand-details { color: #d3ddd8; font-size: 9px; }
        .document-label {
            color: #d9bd8f;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .order-number {
            margin: 5px 0;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }
        .header-meta { color: #d3ddd8; font-size: 9px; }
        .gold-rule { height: 3px; background: #bd985d; }
        .section { margin-top: 21px; }
        .info-card {
            border: 1px solid #e3e9e5;
            padding: 12px 14px;
        }
        .info-card p { margin: 3px 0 0; }
        .section-label {
            margin: 0 0 5px;
            color: #71807a;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .items { margin-top: 22px; }
        .items th {
            padding: 9px 8px;
            border-bottom: 1px solid #bd985d;
            color: #71807a;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: .7px;
            text-align: left;
            text-transform: uppercase;
        }
        .items th.right { text-align: right; }
        .items td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9eeeb;
        }
        .item-name { color: #23332f; font-weight: bold; }
        .totals-wrap { margin-top: 15px; }
        .totals { width: 52%; margin-left: 48%; }
        .totals td { padding: 5px 8px; }
        .grand-total td {
            padding-top: 10px;
            border-top: 1px solid #bd985d;
            color: #173a34;
            font-size: 13px;
            font-weight: bold;
        }
        .payment-card {
            margin-top: 18px;
            background: #f4f6f3;
            padding: 12px 14px;
        }
        .payment-card td { width: 50%; }
        .qr-section {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e3e9e5;
        }
        .qr-section td { vertical-align: middle; }
        .qr { width: 110px; height: 110px; }
        .footer {
            margin-top: 19px;
            color: #71807a;
            font-size: 8px;
            text-align: center;
        }
        .footer strong { color: #173a34; }
    </style>
</head>
<body>
@php
    $currency = $currency ?? 'RWF';
    $money = fn ($amount) => number_format((float) $amount);
    $orderNo = $order->order_number ?? $order->id;
    $customerName = $order->ordered_by ?? $order->customer?->name ?? 'Walk-in customer';
    $subtotal = (float) ($order->subtotal
        ?? $order->items->sum(fn ($item) => (float) ($item->line_total
            ?? ((float) $item->unit_price * (float) $item->quantity))));
    $totalAmount = (float) ($total ?? $order->total_amount ?? $subtotal);
    $shopName = $shop->business_name ?? 'MahWi POS';

@endphp

    <table class="header">
        <tr>
            <td style="width: 62%;">
                <div class="brand">{{ $shopName }}</div>
                <div class="brand-details">
                    @if (!empty($shop->address))
                        {{ $shop->address }}<br>
                    @endif
                    @if (!empty($shop->city))
                        {{ $shop->city }}<br>
                    @endif
                    @if (!empty($shop->phone))
                        {{ $shop->phone }}
                    @endif
                    @if (!empty($shop->email))
                        &nbsp;&middot;&nbsp; {{ $shop->email }}
                    @endif
                    @if (!empty($shop->tin_number))
                        <br>TIN: {{ $shop->tin_number }}
                    @endif
                </div>
            </td>
            <td class="right" style="width: 38%;">
                <div class="document-label">Order bill</div>
                <div class="order-number">#{{ $orderNo }}</div>
                <div class="header-meta">
                    {{ optional($order->created_at)->format('d M Y, H:i') }}<br>
                    {{ ucfirst(str_replace('_', ' ', $order->status ?? 'pending')) }}
                </div>
            </td>
        </tr>
    </table>
    <div class="gold-rule"></div>

    <table class="section">
        <tr>
            <td style="width: 50%; padding-right: 7px;">
                <div class="info-card">
                    <div class="section-label">Prepared for</div>
                    <strong>{{ $customerName }}</strong>
                    @if (!empty($order->customer?->phone))
                        <p class="muted">{{ $order->customer->phone }}</p>
                    @endif
                    @if (!empty($order->table_number))
                        <p class="muted">Table {{ $order->table_number }}</p>
                    @endif
                </div>
            </td>
            <td style="width: 50%; padding-left: 7px;">
                <div class="info-card">
                    <div class="section-label">Service</div>
                    <p><strong>Served by</strong> &nbsp;{{ $order->server?->name ?? '—' }}</p>
                    <p><strong>Order taken by</strong> &nbsp;{{ $waiter ?? '—' }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 45%;">Item</th>
                <th class="right" style="width: 15%;">Quantity</th>
                <th class="right" style="width: 20%;">Unit price</th>
                <th class="right" style="width: 20%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $item)
                @php
                    $quantity = (float) ($item->quantity ?? 0);
                    $unitPrice = (float) ($item->unit_price ?? $item->price ?? 0);
                    $lineAmount = (float) ($item->line_total
                        ?? (($unitPrice * $quantity)
                            - (float) ($item->discount_amount ?? 0)
                            + (float) ($item->tax_amount ?? 0)));
                @endphp
                <tr>
                    <td class="item-name">{{ $item->product?->name ?? $item->description ?? 'Item' }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format($quantity, 2), '0'), '.') }}</td>
                    <td class="right">{{ $currency }} {{ $money($unitPrice) }}</td>
                    <td class="right"><strong>{{ $currency }} {{ $money($lineAmount) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center muted">No items on this order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals">
            <tr>
                <td class="muted">Subtotal</td>
                <td class="right">{{ $currency }} {{ $money($subtotal) }}</td>
            </tr>
            @if ((float) ($order->discount_amount ?? 0) > 0)
                <tr>
                    <td class="muted">Discount</td>
                    <td class="right">- {{ $currency }} {{ $money($order->discount_amount) }}</td>
                </tr>
            @endif
            @if ((float) ($order->tax_amount ?? 0) > 0)
                <tr>
                    <td class="muted">Tax</td>
                    <td class="right">{{ $currency }} {{ $money($order->tax_amount) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>Total due</td>
                <td class="right">{{ $currency }} {{ $money($totalAmount) }}</td>
            </tr>
        </table>
    </div>

    <table class="payment-card">
        <tr>
            <td>
                <div class="section-label">Payment method</div>
                <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'Cash')) }}</strong>
            </td>
            <td class="right">
                <div class="section-label">Payment status</div>
                <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_status ?? 'Unpaid')) }}</strong>
            </td>
        </tr>
    </table>

    @if (!empty($order->notes))
        <div class="section">
            <div class="section-label">Order notes</div>
            {{ $order->notes }}
        </div>
    @endif

    <table class="qr-section">
        <tr>
            <td>
                <div class="eyebrow">Thank you</div>
                <p style="margin: 4px 0 0;"><strong>We appreciate your visit.</strong></p>
                <p class="muted small" style="margin: 3px 0 0;">Keep this bill for your records.</p>
            </td>
            <td class="right" style="width: 115px;">
                <img class="qr" src="data:image/{{ $qrMimeType }};base64,{{ $qr }}" alt="QR code for order #{{ $orderNo }}" width="110" height="110">
                <div class="muted small">Scan to verify</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <strong>{{ $shopName }}</strong>
        &nbsp;&middot;&nbsp; Order #{{ $orderNo }}
        &nbsp;&middot;&nbsp; Powered by MahWi POS
    </div>
</body>
</html>
