<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sale Receipt #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#111; }
        .receipt { max-width: 380px; margin: 0 auto; padding: 12px; }
        .center { text-align: center; }
        .items { width:100%; border-collapse: collapse; margin-top:8px; }
        .items td { padding:6px 0; }
        .line { border-top:1px dashed #ccc; margin:8px 0; }
        .totals { margin-top:8px; }
        .right { text-align: right; }
        h2 { margin:6px 0; font-size:18px; }
        p { margin:4px 0; font-size:13px; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            @if(!empty($sale->shop->logo))
                <img src="{{ asset('storage/' . $sale->shop->logo) }}" alt="logo" style="max-height:60px; margin-bottom:6px">
            @endif
            <h2>{{ $sale->shop->business_name ?? 'Shop' }}</h2>
            @if($sale->shop->address ?? null)
                <p>{{ $sale->shop->address }}</p>
            @endif
            @if($sale->shop->phone ?? null)
                <p>Tel: {{ $sale->shop->phone }}</p>
            @endif
            <p>Receipt: #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p>{{ $sale->sale_date->format('M d, Y') }} {{ $sale->created_at->format('h:i A') }}</p>
        </div>

        <div class="line"></div>

        <table class="items">
            @foreach($sale->items as $item)
            <tr>
                <td style="width:60%">{{ $item->product->name }} x{{ $item->quantity }}</td>
                <td class="right" style="width:40%">{{ rwf($item->line_total) }}</td>
            </tr>
            @endforeach
        </table>

        <div class="line"></div>

        <div class="totals">
            <table style="width:100%">
                <tr>
                    <td>Subtotal</td>
                    <td class="right">{{ rwf($sale->total_amount) }}</td>
                </tr>
                <tr>
                    <td>Tax (0%)</td>
                    <td class="right">{{ rwf(0) }}</td>
                </tr>
                <tr style="font-weight:700">
                    <td>Total</td>
                    <td class="right">{{ rwf($sale->total_amount) }}</td>
                </tr>
            </table>
        </div>

        <p class="center" style="margin-top:12px">Served by: {{ $sale->creator->name ?? 'Unknown' }}</p>
        @if($sale->customer)
            <p class="center">Customer: {{ $sale->customer->name }}</p>
        @endif

        <p class="center" style="margin-top:12px; font-size:12px">Thank you for your business</p>
    </div>

    @if(request()->query('print') == 1)
    <script>
        window.addEventListener('load', function() {
            window.print();
            setTimeout(function(){ window.close(); }, 500);
        });
    </script>
    @endif
</body>
</html>
