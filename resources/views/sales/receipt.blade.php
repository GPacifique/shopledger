<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Receipt') }} {{ $receiptNumber }}</title>
    <style>
        @page { size: {{ $pageSize }}; margin: {{ $pageMargin }}; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #eef1ef;
            color: #172d28;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }
        .print-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            justify-content: center;
            gap: 10px;
            margin: 18px auto;
            padding: 14px;
            max-width: 860px;
            border: 1px solid #dfe7e2;
            border-radius: 14px;
            background: white;
            box-shadow: 0 8px 26px #173a3412;
        }
        .toolbar-control { display: grid; gap: 4px; color: #52635c; font-size: 11px; }
        .toolbar-control select {
            min-width: 130px;
            border: 1px solid #cbd5cf;
            border-radius: 8px;
            background: white;
            padding: 8px 10px;
            color: #172d28;
        }
        .toolbar-link, .toolbar-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            border: 0;
            border-radius: 8px;
            padding: 8px 12px;
            color: #173a34;
            background: #edf3ef;
            font: 600 11px Arial, sans-serif;
            text-decoration: none;
            cursor: pointer;
        }
        .toolbar-primary { color: white; background: #176b53; }
        .document {
            width: {{ $receiptWidthMm }}mm;
            margin: 20px auto;
            padding: 5mm;
            background: white;
            box-shadow: 0 10px 38px #173a3417;
            overflow-wrap: anywhere;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .muted { color: #65756e; }
        .logo { display: block; max-width: 28mm; max-height: 16mm; margin: 0 auto 6px; object-fit: contain; }
        .brand { margin: 0; color: #173a34; font-size: 15px; font-weight: 800; }
        .contact { margin-top: 3px; font-size: 9px; }
        .receipt-heading { margin-top: 9px; color: #a37c43; font-size: 8px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; }
        .reference { margin: 2px 0; font-size: 12px; font-weight: bold; }
        .rule { margin: 9px 0; border-top: 1px dashed #9aa8a0; }
        .customer { margin: 7px 0; }
        .items { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items th { padding: 4px 2px; border-bottom: 1px solid #173a34; font-size: 8px; text-align: left; }
        .items td { padding: 5px 2px; border-bottom: 1px solid #e8ede9; overflow-wrap: anywhere; }
        .items .right { text-align: right; }
        .item-name { font-weight: 600; }
        .totals { width: 100%; margin-top: 8px; border-collapse: collapse; }
        .totals td { padding: 3px 1px; }
        .grand-total td { padding-top: 7px; border-top: 1px solid #173a34; color: #173a34; font-size: 12px; font-weight: bold; }
        .payment { margin-top: 8px; padding-top: 7px; border-top: 1px dashed #9aa8a0; }
        .qr { margin: 10px auto 0; width: 25mm; text-align: center; }
        .qr svg { display: block; width: 25mm; height: 25mm; }
        .qr small { display: block; margin-top: 3px; font-size: 8px; }
        .footer { margin-top: 12px; text-align: center; color: #52635c; font-size: 9px; white-space: pre-line; }
        @media print {
            html, body { width: {{ $receiptWidthMm }}mm; margin: 0; background: white; }
            .print-toolbar { display: none !important; }
            .document { width: 100%; margin: 0; padding: 0; box-shadow: none; }
            thead { display: table-header-group; }
            tr, .totals, .payment, .qr { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    @unless ($pdfMode)
        @include('sales.partials.print-preview-toolbar')
    @endunless

    <main class="document">
        <header class="center">
            @if ($printSettings['show_logo'] && $logoSrc)
                <img class="logo" src="{{ $logoSrc }}" alt="{{ $sale->shop->business_name }}">
            @endif
            <h1 class="brand">{{ $sale->shop->business_name }}</h1>
            @if ($printSettings['show_contact'])
                <div class="contact muted">
                    @if ($sale->shop->address) {{ $sale->shop->address }}<br> @endif
                    @if ($sale->shop->city || $sale->shop->country)
                        {{ collect([$sale->shop->city, $sale->shop->country])->filter()->implode(', ') }}<br>
                    @endif
                    @if ($sale->shop->phone) {{ __('Tel') }}: {{ $sale->shop->phone }} @endif
                    @if ($sale->shop->email) &nbsp;{{ $sale->shop->email }} @endif
                    @if ($sale->shop->tin_number)<br>TIN: {{ $sale->shop->tin_number }}@endif
                </div>
            @endif
            <div class="receipt-heading">{{ __('Sales receipt') }}</div>
            <div class="reference">{{ $receiptNumber }}</div>
            <div class="muted">{{ $sale->sale_date->format('d M Y') }} · {{ $sale->created_at->format('H:i') }}</div>
        </header>

        @if ($printSettings['show_customer'] && $sale->customer)
            <div class="customer">
                <strong>{{ __('Customer') }}:</strong> {{ $sale->customer->name }}
                @if ($sale->customer->phone) · {{ $sale->customer->phone }} @endif
                @if ($sale->customer->email)<br>{{ $sale->customer->email }}@endif
            </div>
        @endif

        @if ($printSettings['show_cashier'] && $sale->creator)
            <div><strong>{{ __('Cashier') }}:</strong> {{ $sale->creator->name }}</div>
        @endif

        <div class="rule"></div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 43%">{{ __('Item') }}</th>
                    <th class="right" style="width: 15%">{{ __('Qty') }}</th>
                    <th class="right" style="width: 21%">{{ __('Unit price') }}</th>
                    <th class="right" style="width: 21%">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td class="item-name">{{ $item->product?->name ?? __('Item no longer available') }}</td>
                        <td class="right">{{ format_qty($item->quantity) }}</td>
                        <td class="right">{{ $money($item->unit_price) }}</td>
                        <td class="right">{{ $money($item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>{{ __('Subtotal') }}</td>
                <td class="right">{{ $currency }} {{ $money($subtotal) }}</td>
            </tr>
            @if ($discountAmount > 0)
                <tr><td>{{ __('Discount') }}</td><td class="right">- {{ $currency }} {{ $money($discountAmount) }}</td></tr>
            @endif
            @if ($printSettings['show_tax'] && $taxAmount > 0)
                <tr><td>{{ __('Tax') }}</td><td class="right">{{ $currency }} {{ $money($taxAmount) }}</td></tr>
            @endif
            @if ($serviceCharges > 0)
                <tr><td>{{ __('Service charge') }}</td><td class="right">{{ $currency }} {{ $money($serviceCharges) }}</td></tr>
            @endif
            <tr class="grand-total">
                <td>{{ __('Grand total') }}</td>
                <td class="right">{{ $currency }} {{ $money($sale->total_amount) }}</td>
            </tr>
        </table>

        <div class="payment">
            <div><strong>{{ __('Payment') }}:</strong> {{ $paymentMethod }}</div>
            <div><strong>{{ __('Status') }}:</strong> {{ ucfirst($paymentStatus) }}</div>
            @if (!is_null($amountPaid))
                <div><strong>{{ __('Amount paid') }}:</strong> {{ $currency }} {{ $money($amountPaid) }}</div>
                <div><strong>{{ __('Balance due') }}:</strong> {{ $currency }} {{ $money($balanceDue) }}</div>
            @elseif ($paymentStatus === 'partial')
                <div class="muted">{{ __('Partial payment amount is not recorded on this transaction.') }}</div>
            @endif
        </div>

        @if ($printSettings['show_qr'] && $qrCode)
            <div class="qr">
                {!! $qrCode !!}
                <small class="muted">{{ __('Receipt') }} {{ $receiptNumber }}</small>
            </div>
        @endif

        @if ($printSettings['receipt_footer'])
            <div class="footer">{{ $printSettings['receipt_footer'] }}</div>
        @endif
    </main>
</body>
</html>
