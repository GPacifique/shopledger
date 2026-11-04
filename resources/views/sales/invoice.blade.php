<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Invoice') }} {{ $receiptNumber }}</title>
    <style>
        @page { size: {{ $pageSize }}; margin: {{ $pageMargin }}; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1ef; color: #23332f; font: 11px/1.5 Arial, Helvetica, sans-serif; }
        .print-toolbar {
            display: flex; flex-wrap: wrap; align-items: end; justify-content: center; gap: 10px;
            margin: 18px auto; padding: 14px; max-width: 860px; border: 1px solid #dfe7e2;
            border-radius: 14px; background: white; box-shadow: 0 8px 26px #173a3412;
        }
        .toolbar-control { display: grid; gap: 4px; color: #52635c; font-size: 11px; }
        .toolbar-control select { min-width: 130px; border: 1px solid #cbd5cf; border-radius: 8px; background: white; padding: 8px 10px; }
        .toolbar-link, .toolbar-primary {
            display: inline-flex; align-items: center; justify-content: center; min-height: 36px;
            border: 0; border-radius: 8px; padding: 8px 12px; color: #173a34; background: #edf3ef;
            font: 600 11px Arial, sans-serif; text-decoration: none; cursor: pointer;
        }
        .toolbar-primary { color: white; background: #176b53; }
        .document { width: {{ $pageWidth }}; min-height: 180mm; margin: 20px auto; padding: 12mm; background: white; box-shadow: 0 10px 38px #173a3417; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .muted { color: #71807a; }
        .right { text-align: right; }
        .center { text-align: center; }
        .brand { margin: 0 0 4px; color: #173a34; font-size: 20px; font-weight: 700; }
        .logo { max-width: 34mm; max-height: 22mm; margin-bottom: 8px; object-fit: contain; }
        .eyebrow { color: #ae8950; font-size: 9px; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase; }
        .title { margin: 0; color: #173a34; font-size: 27px; letter-spacing: 1px; }
        .invoice-number { margin-top: 5px; font-size: 15px; font-weight: bold; }
        .gold-rule { height: 3px; margin: 18px 0; background: #bd985d; }
        .details { margin-bottom: 22px; }
        .details td { width: 50%; padding-right: 16px; }
        .section-label { margin-bottom: 4px; color: #71807a; font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .details p { margin: 2px 0; }
        .items th { padding: 10px 8px; border-bottom: 1px solid #bd985d; color: #64736d; font-size: 9px; text-align: left; text-transform: uppercase; }
        .items td { padding: 10px 8px; border-bottom: 1px solid #e8ede9; overflow-wrap: anywhere; }
        .items .right, .totals .right { text-align: right; }
        .item-name { font-weight: bold; }
        .totals { width: 52%; margin: 15px 0 0 auto; }
        .totals td { padding: 5px 8px; }
        .grand-total td { padding-top: 10px; border-top: 1px solid #bd985d; color: #173a34; font-size: 15px; font-weight: bold; }
        .payment-panel { margin-top: 18px; padding: 12px 14px; background: #f3f6f3; }
        .payment-panel td { width: 50%; }
        .notes { margin-top: 18px; page-break-inside: avoid; break-inside: avoid; }
        .signature { width: 48%; margin: 38px 0 0 auto; text-align: center; page-break-inside: avoid; break-inside: avoid; }
        .signature-line { margin-bottom: 7px; border-top: 1px solid #86948d; }
        .qr { margin-top: 18px; text-align: right; page-break-inside: avoid; break-inside: avoid; }
        .qr svg, .qr img { width: 29mm; height: 29mm; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e1e7e3; color: #71807a; font-size: 9px; white-space: pre-line; }
        @media print {
            body { background: white; }
            .print-toolbar { display: none !important; }
            .document { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
            thead { display: table-header-group; }
            tr, .totals, .payment-panel, .notes, .signature, .qr { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    @unless ($pdfMode)
        @include('sales.partials.print-preview-toolbar')
    @endunless

    <main class="document">
        <table>
            <tr>
                <td style="width: 57%">
                    @if ($printSettings['show_logo'] && $logoSrc)
                        <img class="logo" src="{{ $logoSrc }}" alt="{{ $sale->shop->business_name }}">
                    @endif
                    <h1 class="brand">{{ $sale->shop->business_name }}</h1>
                    @if ($printSettings['show_contact'])
                        <div class="muted">
                            @if ($sale->shop->address) {{ $sale->shop->address }}<br> @endif
                            {{ collect([$sale->shop->city, $sale->shop->country])->filter()->implode(', ') }}<br>
                            @if ($sale->shop->phone) {{ __('Tel') }}: {{ $sale->shop->phone }}<br> @endif
                            @if ($sale->shop->email) {{ $sale->shop->email }}<br> @endif
                            @if ($sale->shop->tin_number) TIN: {{ $sale->shop->tin_number }} @endif
                        </div>
                    @endif
                </td>
                <td class="right">
                    <div class="eyebrow">{{ __('Mahwi POS · Sales document') }}</div>
                    <h2 class="title">{{ __('INVOICE') }}</h2>
                    <div class="invoice-number">{{ $receiptNumber }}</div>
                    <div class="muted">{{ __('Issue date') }}: {{ $sale->sale_date->format('d M Y') }}</div>
                    <div class="muted">{{ $sale->created_at->format('H:i') }}</div>
                </td>
            </tr>
        </table>
        <div class="gold-rule"></div>

        <table class="details">
            <tr>
                <td>
                    <div class="section-label">{{ __('Bill to') }}</div>
                    @if ($printSettings['show_customer'])
                        @if ($sale->customer)
                            <strong>{{ $sale->customer->name }}</strong>
                            @if ($sale->customer->address)<p class="muted">{{ $sale->customer->address }}</p>@endif
                            @if ($sale->customer->phone)<p class="muted">{{ $sale->customer->phone }}</p>@endif
                            @if ($sale->customer->email)<p class="muted">{{ $sale->customer->email }}</p>@endif
                        @else
                            <strong>{{ __('Walk-in sale') }}</strong>
                        @endif
                    @endif
                </td>
                <td>
                    <div class="section-label">{{ __('Transaction') }}</div>
                    <p>{{ __('Payment method') }}: <strong>{{ $paymentMethod }}</strong></p>
                    <p>{{ __('Payment status') }}: <strong>{{ ucfirst($paymentStatus) }}</strong></p>
                    @if ($printSettings['show_cashier'] && $sale->creator)
                        <p>{{ __('Cashier') }}: <strong>{{ $sale->creator->name }}</strong></p>
                    @endif
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 46%">{{ __('Description') }}</th>
                    <th class="right" style="width: 14%">{{ __('Quantity') }}</th>
                    <th class="right" style="width: 19%">{{ __('Unit price') }}</th>
                    <th class="right" style="width: 21%">{{ __('Line total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td class="item-name">{{ $item->product?->name ?? __('Item no longer available') }}</td>
                        <td class="right">{{ format_qty($item->quantity) }}</td>
                        <td class="right">{{ $currency }} {{ $money($item->unit_price) }}</td>
                        <td class="right">{{ $currency }} {{ $money($item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr><td class="muted">{{ __('Subtotal') }}</td><td class="right">{{ $currency }} {{ $money($subtotal) }}</td></tr>
            @if ($discountAmount > 0)
                <tr><td class="muted">{{ __('Discount') }}</td><td class="right">- {{ $currency }} {{ $money($discountAmount) }}</td></tr>
            @endif
            @if ($printSettings['show_tax'] && $taxAmount > 0)
                <tr><td class="muted">{{ __('Tax') }}</td><td class="right">{{ $currency }} {{ $money($taxAmount) }}</td></tr>
            @endif
            @if ($serviceCharges > 0)
                <tr><td class="muted">{{ __('Service charge') }}</td><td class="right">{{ $currency }} {{ $money($serviceCharges) }}</td></tr>
            @endif
            <tr class="grand-total"><td>{{ __('Total payable') }}</td><td class="right">{{ $currency }} {{ $money($sale->total_amount) }}</td></tr>
        </table>

        <table class="payment-panel">
            <tr>
                <td><div class="section-label">{{ __('Amount paid') }}</div>
                    @if (!is_null($amountPaid))
                        <strong>{{ $currency }} {{ $money($amountPaid) }}</strong>
                    @else
                        <span class="muted">{{ __('Not recorded for partial payment') }}</span>
                    @endif
                </td>
                <td class="right"><div class="section-label">{{ __('Outstanding balance') }}</div>
                    @if (!is_null($balanceDue))
                        <strong>{{ $currency }} {{ $money($balanceDue) }}</strong>
                    @else
                        <span class="muted">{{ __('Not recorded for partial payment') }}</span>
                    @endif
                </td>
            </tr>
        </table>

        @if ($printSettings['invoice_notes'])
            <section class="notes">
                <div class="section-label">{{ __('Payment instructions') }}</div>
                <div>{{ $printSettings['invoice_notes'] }}</div>
            </section>
        @endif
        @if ($printSettings['invoice_terms'])
            <section class="notes">
                <div class="section-label">{{ __('Terms and conditions') }}</div>
                <div>{{ $printSettings['invoice_terms'] }}</div>
            </section>
        @endif

        <div class="signature">
            <div class="signature-line"></div>
            <span class="muted">{{ __('Authorized signature') }}</span>
        </div>

        @if ($printSettings['show_qr'] && ($pdfMode ? $qr : $qrCode))
            <div class="qr">
                @if ($pdfMode)
                    <table style="width: 100%;">
                        <tr>
                            <td style="text-align: right;">
                                <img src="data:image/{{ $qrMimeType }};base64,{{ $qr }}" width="110" height="110" alt="{{ __('QR code for invoice') }} {{ $receiptNumber }}">
                                <div class="muted">Scan to verify</div>
                            </td>
                        </tr>
                    </table>
                @else
                    {!! $qrCode !!}
                @endif
                <div class="muted">{{ __('Invoice reference') }} {{ $receiptNumber }}</div>
            </div>
        @endif

        <footer class="footer">{{ $printSettings['receipt_footer'] }}</footer>
    </main>
</body>
</html>
