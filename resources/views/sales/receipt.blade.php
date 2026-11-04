<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Receipt') }} {{ $receiptNumber }}</title>

    <style>
        /*
        |--------------------------------------------------------------------------
        | 58MM CONTINUOUS THERMAL RECEIPT
        |--------------------------------------------------------------------------
        | One receipt = one continuous roll feed.
        | No fixed paper height or artificial page breaks.
        */

        @page {
            size: 58mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #eef1ef;
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* PRINT PREVIEW TOOLBAR */

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

        .toolbar-control {
            display: grid;
            gap: 4px;
            color: #52635c;
            font-size: 11px;
        }

        .toolbar-control select {
            min-width: 130px;
            border: 1px solid #cbd5cf;
            border-radius: 8px;
            background: white;
            padding: 8px 10px;
            color: #172d28;
        }

        .toolbar-link,
        .toolbar-primary {
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

        .toolbar-primary {
            color: white;
            background: #176b53;
        }

        /* RECEIPT PAPER */

        .document {
            width: 58mm;
            max-width: 100%;
            min-height: 0;
            height: auto;
            margin: 20px auto;
            padding: 2mm;
            background: white;
            box-shadow: 0 10px 38px #173a3417;
            overflow-wrap: anywhere;
            word-wrap: break-word;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .muted {
            color: #555;
        }

        /* BUSINESS HEADER */

        .logo {
            display: block;
            max-width: 24mm;
            max-height: 13mm;
            margin: 0 auto 4px;
            object-fit: contain;
        }

        .brand {
            margin: 0;
            color: #000;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.2;
            overflow-wrap: anywhere;
            text-transform: uppercase;
        }

        .contact {
            margin-top: 3px;
            font-size: 8px;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .receipt-heading {
            margin-top: 7px;
            color: #000;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .reference {
            margin: 3px 0;
            font-size: 11px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .rule {
            margin: 6px 0;
            border-top: 1px dashed #555;
        }

        .customer {
            margin: 5px 0;
            font-size: 8px;
            overflow-wrap: anywhere;
        }

        /* PRODUCT TABLE */

        .items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8px;
        }

        .items th {
            padding: 4px 1px;
            border-top: 1px dashed #555;
            border-bottom: 1px solid #111;
            font-size: 7px;
            font-weight: bold;
            text-align: right;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        .items th:first-child {
            text-align: left;
        }

        .items td {
            padding: 4px 1px;
            border-bottom: 1px dotted #ccc;
            text-align: right;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .items td:first-child {
            text-align: left;
        }

        .items .item-name {
            font-weight: 600;
            line-height: 1.25;
        }

        /* FINANCIAL SUMMARY */

        .totals {
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
            font-size: 9px;
        }

        .totals td {
            padding: 3px 1px;
            vertical-align: top;
        }

        .totals td:first-child {
            text-align: left;
        }

        .totals td:last-child {
            text-align: right;
            overflow-wrap: anywhere;
        }

        .grand-total td {
            padding-top: 6px;
            padding-bottom: 5px;
            border-top: 1px solid #111;
            border-bottom: 1px double #111;
            color: #000;
            font-size: 11px;
            font-weight: bold;
        }

        /* PAYMENT DETAILS */

        .payment {
            margin-top: 7px;
            padding-top: 6px;
            border-top: 1px dashed #555;
            font-size: 8px;
        }

        .payment div {
            margin: 3px 0;
            overflow-wrap: anywhere;
        }

        /* QR CODE */

        .qr {
            margin: 8px auto 0;
            width: 25mm;
            text-align: center;
        }

        .qr svg,
        .qr img {
            display: block;
            width: 25mm;
            height: 25mm;
            max-width: 100%;
            margin: 0 auto;
        }

        .qr small {
            display: block;
            margin-top: 3px;
            font-size: 7px;
        }

        /* FOOTER */

        .footer {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px dashed #777;
            text-align: center;
            color: #222;
            font-size: 8px;
            line-height: 1.4;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        /* SCREEN PREVIEW */

        @media screen {
            .document {
                width: 58mm;
                max-width: calc(100vw - 20px);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PRINT MODE - CONTINUOUS ROLL
        |--------------------------------------------------------------------------
        */

        @media print {

            html,
            body {
                width: 58mm !important;
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            body {
                font-size: 9px;
            }

            .print-toolbar {
                display: none !important;
            }

            .document {
                display: block !important;
                width: 58mm !important;
                max-width: 58mm !important;
                min-height: 0 !important;
                height: auto !important;
                margin: 0 !important;
                padding: 2mm !important;
                box-shadow: none !important;
                border: none !important;
                overflow: visible !important;
            }

            /*
             * Allow every receipt section to flow naturally.
             * Do not force separate pages for receipt sections.
             */

            .document,
            header,
            .items,
            .items tbody,
            .items tr,
            .totals,
            .totals tr,
            .payment,
            .qr,
            .footer {
                break-before: auto !important;
                break-after: auto !important;
                break-inside: auto !important;

                page-break-before: auto !important;
                page-break-after: auto !important;
                page-break-inside: auto !important;
            }

            .items thead {
                display: table-header-group;
            }

            img,
            svg {
                max-width: 100%;
            }

            a {
                color: inherit;
                text-decoration: none;
            }
        }
    </style>
</head>

<body>

    @unless ($pdfMode)
        @include('sales.partials.print-preview-toolbar')
    @endunless

    <main class="document">

        {{-- BUSINESS INFORMATION --}}
        <header class="center">

            @if ($printSettings['show_logo'] && $logoSrc)
                <img
                    class="logo"
                    src="{{ $logoSrc }}"
                    alt="{{ $sale->shop->business_name }}"
                >
            @endif

            <h1 class="brand">
                {{ $sale->shop->business_name }}
            </h1>

            @if ($printSettings['show_contact'])
                <div class="contact muted">

                    @if ($sale->shop->address)
                        {{ $sale->shop->address }}<br>
                    @endif

                    @if ($sale->shop->city || $sale->shop->country)
                        {{ collect([$sale->shop->city, $sale->shop->country])->filter()->implode(', ') }}<br>
                    @endif

                    @if ($sale->shop->phone)
                        {{ __('Tel') }}: {{ $sale->shop->phone }}
                    @endif

                    @if ($sale->shop->email)
                        {{ $sale->shop->phone ? ' | ' : '' }}{{ $sale->shop->email }}
                    @endif

                    @if ($sale->shop->tin_number)
                        <br>TIN: {{ $sale->shop->tin_number }}
                    @endif

                </div>
            @endif

            <div class="receipt-heading">
                {{ __('Sales receipt') }}
            </div>

            <div class="reference">
                {{ $receiptNumber }}
            </div>

            <div class="muted">
                {{ $sale->sale_date->format('d M Y') }}
                ·
                {{ $sale->created_at->format('H:i') }}
            </div>

        </header>

        {{-- CUSTOMER INFORMATION --}}
        @if ($printSettings['show_customer'] && $sale->customer)
            <div class="customer">

                <strong>{{ __('Customer') }}:</strong>
                {{ $sale->customer->name }}

                @if ($sale->customer->phone)
                    <br>{{ __('Tel') }}: {{ $sale->customer->phone }}
                @endif

                @if ($sale->customer->email)
                    <br>{{ $sale->customer->email }}
                @endif

            </div>
        @endif

        {{-- CASHIER --}}
        @if ($printSettings['show_cashier'] && $sale->creator)
            <div class="customer">
                <strong>{{ __('Cashier') }}:</strong>
                {{ $sale->creator->name }}
            </div>
        @endif

        <div class="rule"></div>

        {{-- SOLD PRODUCTS --}}
        <table class="items">

            <thead>
                <tr>
                    <th style="width: 42%">
                        {{ __('Item') }}
                    </th>

                    <th style="width: 12%">
                        {{ __('Qty') }}
                    </th>

                    <th style="width: 23%">
                        {{ __('Price') }}
                    </th>

                    <th style="width: 23%">
                        {{ __('Total') }}
                    </th>
                </tr>
            </thead>

            <tbody>

                @foreach ($sale->items as $item)
                    <tr>

                        <td class="item-name">
                            {{ $item->product?->name ?? __('Item no longer available') }}
                        </td>

                        <td>
                            {{ format_qty($item->quantity) }}
                        </td>

                        <td>
                            {{ $money($item->unit_price) }}
                        </td>

                        <td>
                            {{ $money($item->line_total) }}
                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>

        {{-- FINANCIAL SUMMARY --}}
        <table class="totals">

            <tr>
                <td>{{ __('Subtotal') }}</td>
                <td>
                    {{ $currency }} {{ $money($subtotal) }}
                </td>
            </tr>

            @if ($discountAmount > 0)
                <tr>
                    <td>{{ __('Discount') }}</td>
                    <td>
                        - {{ $currency }} {{ $money($discountAmount) }}
                    </td>
                </tr>
            @endif

            @if ($printSettings['show_tax'] && $taxAmount > 0)
                <tr>
                    <td>{{ __('Tax') }}</td>
                    <td>
                        {{ $currency }} {{ $money($taxAmount) }}
                    </td>
                </tr>
            @endif

            @if ($serviceCharges > 0)
                <tr>
                    <td>{{ __('Service charge') }}</td>
                    <td>
                        {{ $currency }} {{ $money($serviceCharges) }}
                    </td>
                </tr>
            @endif

            <tr class="grand-total">
                <td>{{ __('Grand total') }}</td>
                <td>
                    {{ $currency }} {{ $money($sale->total_amount) }}
                </td>
            </tr>

        </table>

        {{-- PAYMENT DETAILS --}}
        <div class="payment">

            <div>
                <strong>{{ __('Payment') }}:</strong>
                {{ $paymentMethod }}
            </div>

            <div>
                <strong>{{ __('Status') }}:</strong>
                {{ ucfirst($paymentStatus) }}
            </div>

            @if (!is_null($amountPaid))

                <div>
                    <strong>{{ __('Amount paid') }}:</strong>
                    {{ $currency }} {{ $money($amountPaid) }}
                </div>

                <div>
                    <strong>{{ __('Balance due') }}:</strong>
                    {{ $currency }} {{ $money($balanceDue) }}
                </div>

            @elseif ($paymentStatus === 'partial')

                <div class="muted">
                    {{ __('Partial payment amount is not recorded on this transaction.') }}
                </div>

            @endif

        </div>

        {{-- QR CODE --}}
        @if ($printSettings['show_qr'] && ($pdfMode ? $qr : $qrCode))
            <div class="qr">

                @if ($pdfMode)
                    <table style="width: 100%; margin-top: 8px;">
                        <tr>
                            <td style="text-align: right;">
                                <img src="data:image/{{ $qrMimeType }};base64,{{ $qr }}" width="110" height="110" alt="{{ __('QR code for receipt') }} {{ $receiptNumber }}">
                                <small class="muted" style="display: block;">Scan to verify</small>
                            </td>
                        </tr>
                    </table>
                @else
                    {!! $qrCode !!}
                @endif

                <small class="muted">
                    {{ __('Receipt') }} {{ $receiptNumber }}
                </small>

            </div>
        @endif

        {{-- RECEIPT FOOTER --}}
        @if ($printSettings['receipt_footer'])
            <div class="footer">
                {{ $printSettings['receipt_footer'] }}
            </div>
        @endif

    </main>

</body>
</html>