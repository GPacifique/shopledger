<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->order_number ?? $order->id }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 0; line-height: 1.45; }

        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .right  { text-align: right; }
        .center { text-align: center; }
        .muted  { color: #6b7280; }
        .small  { font-size: 10px; }
        .label  { font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; }

        /* Header */
        .brand       { font-size: 20px; font-weight: bold; color: #111827; margin: 0 0 3px; }
        .doc-title   { font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: #4f46e5; font-weight: bold; }
        .order-no    { font-size: 16px; font-weight: bold; color: #111827; margin: 2px 0; }
        .rule        { border: 0; border-top: 2px solid #4f46e5; margin: 14px 0 16px; }

        /* Status pill */
        .pill        { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .pill-pending   { background: #fef3c7; color: #92400e; }
        .pill-approved  { background: #dcfce7; color: #166534; }
        .pill-rejected  { background: #fee2e2; color: #991b1b; }
        .pill-cancelled { background: #f3f4f6; color: #374151; }

        /* Info blocks */
        .info td      { width: 50%; padding: 0 10px 0 0; }
        .info-box     { border: 1px solid #e5e7eb; border-radius: 6px; padding: 9px 11px; }
        .info-box p   { margin: 2px 0; }

        /* Items */
        .items { margin-top: 16px; }
        .items thead th {
            background: #f3f4f6; color: #374151; font-size: 9px; text-transform: uppercase;
            letter-spacing: .05em; padding: 7px 8px; border-bottom: 1px solid #d1d5db;
        }
        .items tbody td { padding: 8px; border-bottom: 1px solid #eef0f3; }
        .items tbody tr:nth-child(even) td { background: #fafafa; }
        .item-name { font-weight: bold; color: #111827; }

        /* Totals */
        .totals-wrap { margin-top: 14px; }
        .totals      { width: 48%; margin-left: 52%; }
        .totals td   { padding: 4px 8px; }
        .totals .grand td { border-top: 2px solid #111827; padding-top: 8px; font-size: 14px; font-weight: bold; color: #111827; }

        /* Notes + footer */
        .notes   { margin-top: 16px; border-left: 3px solid #c7d2fe; background: #f8fafc; padding: 8px 11px; }
        .footer  { margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 9px; }

        /* QR code */
        .qr      { margin-top: 20px; text-align: center; page-break-inside: avoid; }
        .qr img  { width: 90px; height: 90px; }
        .qr .cap { margin-top: 4px; font-size: 9px; color: #6b7280; }

        @media print { body { margin: 0; } }
    </style>
</head>
<body>
@php
    $currency = $currency ?? 'RWF';
    $money    = fn ($n) => number_format((float) $n);
    $status   = $order->status ?? 'pending';
    $pillClass = match ($status) {
        'approved'  => 'pill-approved',
        'rejected'  => 'pill-rejected',
        'cancelled' => 'pill-cancelled',
        default     => 'pill-pending',
    };
    $discount = (float) ($order->discount_amount ?? 0);
    $tax      = (float) ($order->tax_amount ?? 0);
    $paymentMethod = ucfirst(str_replace('_', ' ', $order->payment_method ?? '—'));
    $paymentStatus = ucfirst($order->payment_status ?? '—');

    // Logo: embedded as base64 so it renders in PDFs as well as in the browser.
    // New logos live in public/images/logos; older ones (shop-logos/...) are still read from storage.
    $logoSrc = null;
    if (!empty($shop->logo)) {
        try {
            $logoFile = str_starts_with($shop->logo, 'images/')
                ? public_path($shop->logo)
                : storage_path('app/public/' . ltrim(preg_replace('#^(public/|storage/)#', '', $shop->logo), '/'));

            if (is_file($logoFile)) {
                $mime = mime_content_type($logoFile) ?: 'image/png';
                $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
            }
        } catch (\Throwable $e) {
            $logoSrc = null;   // never let a bad logo break the bill
        }
    }

    // QR code: embedded as a base64 SVG <img>, which works in both the browser and DomPDF.
    // What it contains: order number, shop, date and total (swap for a signed URL if you prefer).
    $orderNo   = $order->order_number ?? $order->id;
    $qrPayload = implode("\n", [
        'Order: #' . $orderNo,
        'Shop: ' . ($shop->business_name ?? 'MahWi POS'),
        'Date: ' . optional($order->created_at)->format('Y-m-d H:i'),
        'Total: ' . $currency . ' ' . $money($order->total_amount ?? 0),
    ]);
    // Option: link to the order instead (replace 'orders.receipt' with your route name)
    // $qrPayload = \Illuminate\Support\Facades\URL::signedRoute('orders.receipt', $order);

    // PNG data URI (needs the GD extension, which DomPDF already uses). Works in PDF and browser.
    $qrSrc = null;
    try {
        $qrSrc = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($qrPayload)
            ->size(300)
            ->margin(0)
            ->build()
            ->getDataUri();
    } catch (\Throwable $e) {
        report($e);      // logged to storage/logs/laravel.log; the bill still prints without the QR
        $qrSrc = null;
    }
@endphp

    {{-- ===== Header ===== --}}
    <table>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        @if ($logoSrc)
                            <td style="width:70px; padding-right:10px;">
                                <img src="{{ $logoSrc }}" alt="{{ $shop->business_name ?? 'Logo' }}"
                                     style="max-width:60px; max-height:60px;">
                            </td>
                        @endif
                        <td>
                            <p class="brand">{{ $shop->business_name ?? 'MahWi POS' }}</p>
                            @if (!empty($shop->address) || !empty($shop->city))
                                <div class="muted">{{ collect([$shop->address ?? null, $shop->city ?? null])->filter()->implode(', ') }}</div>
                            @endif
                            @if (!empty($shop->phone))
                                <div class="muted">Tel: {{ $shop->phone }}</div>
                            @endif
                            @if (!empty($shop->email))
                                <div class="muted">{{ $shop->email }}</div>
                            @endif
                            @if (!empty($shop->tin_number))
                                <div class="muted">TIN: {{ $shop->tin_number }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="right" style="width:40%;">
                <div class="doc-title">Order Receipt</div>
                <div class="order-no">#{{ $order->order_number ?? $order->id }}</div>
                <div class="muted">{{ optional($order->created_at)->format('d M Y, H:i') }}</div>
                <div style="margin-top:5px;"><span class="pill {{ $pillClass }}">{{ ucfirst($status) }}</span></div>
            </td>
        </tr>
    </table>

    <hr class="rule">

    {{-- ===== Order details ===== --}}
    <table class="info">
        <tr>
            <td>
                <div class="info-box">
                    <div class="label">Customer</div>
                    <p><strong>{{ $order->customer->name ?? 'Walk-in customer' }}</strong></p>
                    @if (!empty($order->customer?->phone))
                        <p class="muted">{{ $order->customer->phone }}</p>
                    @endif
                </div>
            </td>
            <td style="padding-right:0;">
                <div class="info-box">
                    <div class="label">Served by</div>
                    <p><strong>{{ $order->waiter->name ?? '—' }}</strong></p>
                    <p class="muted">Payment: {{ $paymentMethod }} &middot; {{ $paymentStatus }}</p>
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== Items ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:5%;"  class="center">#</th>
                <th style="width:43%;" align="left">Item</th>
                <th style="width:10%;" class="right">Qty</th>
                <th style="width:20%;" class="right">Unit price</th>
                <th style="width:22%;" class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $i => $item)
                @php
                    $lineTotal = $item->line_total ?? (((float) $item->unit_price * (float) $item->quantity) - (float) $item->discount_amount + (float) $item->tax_amount);
                @endphp
                <tr>
                    <td class="center muted">{{ $i + 1 }}</td>
                    <td>
                        <span class="item-name">{{ $item->product->name ?? $item->description ?? 'Item' }}</span>
                    </td>
                    <td class="right">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                    <td class="right">{{ $money($item->unit_price) }}</td>
                    <td class="right"><strong>{{ $money($lineTotal) }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="5" class="center muted" style="padding:16px;">No items on this order.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== Totals ===== --}}
    <div class="totals-wrap">
        <table class="totals">
            <tr>
                <td class="muted">Subtotal</td>
                <td class="right">{{ $currency }} {{ $money($order->subtotal ?? 0) }}</td>
            </tr>
            @if ($discount > 0)
                <tr>
                    <td class="muted">Discount</td>
                    <td class="right">- {{ $currency }} {{ $money($discount) }}</td>
                </tr>
            @endif
            @if ($tax > 0)
                <tr>
                    <td class="muted">Tax</td>
                    <td class="right">{{ $currency }} {{ $money($tax) }}</td>
                </tr>
            @endif
            <tr class="grand">
                <td>Total</td>
                <td class="right">{{ $currency }} {{ $money($order->total_amount ?? 0) }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== Notes ===== --}}
    @if (!empty($order->notes))
        <div class="notes">
            <div class="label">Notes</div>
            {{ $order->notes }}
        </div>
    @endif

    @if ($status === 'rejected' && !empty($order->rejection_reason))
        <div class="notes" style="border-left-color:#fca5a5; background:#fef2f2;">
            <div class="label">Rejection reason</div>
            {{ $order->rejection_reason }}
        </div>
    @endif

    {{-- ===== QR code ===== --}}
    @if ($qrSrc)
        <div class="qr">
            <img src="{{ $qrSrc }}" alt="QR code for order #{{ $orderNo }}" width="90" height="90">
            <div class="cap">Scan to verify order #{{ $orderNo }}</div>
        </div>
    @endif

    {{-- ===== Footer ===== --}}
    <div class="footer">
        Thank you for your business.<br>
        Printed {{ now()->format('d M Y, H:i') }}@auth by {{ auth()->user()->name }}@endauth &middot; MahWi POS
    </div>
</body>
</html>