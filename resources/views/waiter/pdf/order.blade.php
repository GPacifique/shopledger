<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order #{{ $order->order_number ?? $order->id }}</title>
    <style>
        *{box-sizing:border-box} body{font-family:DejaVu Sans, sans-serif;font-size:12px;color:#222;margin:28px} h1{font-size:22px;margin:0 0 5px} .muted{color:#666}.row{display:flex;justify-content:space-between}.box{border:1px solid #ddd;border-radius:8px;padding:12px;margin:14px 0}.items{width:100%;border-collapse:collapse}.items th,.items td{padding:8px;border-bottom:1px solid #eee;text-align:left}.right{text-align:right!important}.total{font-size:16px;font-weight:bold}.footer{margin-top:30px;text-align:center;color:#777;font-size:10px}
    </style>
</head>
<body>
    <div class="row"><div><h1>MahWi POS</h1><div class="muted">Waiter Order</div></div><div class="right"><strong>Order #{{ $order->order_number ?? $order->id }}</strong><br>{{ optional($order->created_at)->format('d/m/Y H:i') }}</div></div>
    <div class="box"><div class="row"><span><strong>Table:</strong> {{ $order->table->number ?? $order->table_id ?? '—' }}</span><span><strong>Waiter:</strong> {{ $order->waiter->name ?? auth()->user()->name ?? '—' }}</span></div></div>
    <table class="items"><thead><tr><th>Item</th><th class="right">Qty</th><th class="right">Price</th><th class="right">Total</th></tr></thead><tbody>
    @foreach($order->items as $item)<tr><td>{{ $item->product->name ?? $item->name ?? 'Item' }}</td><td class="right">{{ $item->quantity }}</td><td class="right">{{ number_format((float)($item->unit_price ?? $item->price ?? 0)) }}</td><td class="right">{{ number_format((float)($item->total ?? (($item->unit_price ?? $item->price ?? 0) * $item->quantity))) }}</td></tr>@endforeach
    </tbody></table>
    <div class="box"><div class="row"><span>Subtotal</span><span>{{ $currency ?? 'RWF' }} {{ number_format((float)($order->subtotal ?? $order->total ?? 0)) }}</span></div>@if(isset($order->tax))<div class="row"><span>Tax</span><span>{{ $currency ?? 'RWF' }} {{ number_format((float)$order->tax) }}</span></div>@endif<div class="row total"><span>Total</span><span>{{ $currency ?? 'RWF' }} {{ number_format((float)($order->total ?? 0)) }}</span></div></div>
    <div class="footer">Printed from MahWi POS</div>
</body></html>
