<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Note #{{ $purchase->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            padding: 30px;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 20px;
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .logo-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .logo-text {
            font-size: 28px;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 5px;
        }
        .logo-tagline {
            font-size: 10px;
            color: #666;
        }
        .doc-title {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .doc-title h1 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .doc-number {
            font-size: 12px;
            color: #666;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-right {
            padding-left: 20px;
        }
        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-title {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-content {
            font-size: 11px;
            line-height: 1.6;
        }
        .info-content p {
            margin-bottom: 3px;
        }
        .info-label {
            color: #6b7280;
            display: inline-block;
            width: 80px;
        }
        .info-value {
            color: #1f2937;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background: #6366f1;
            color: white;
        }
        th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        th.text-right {
            text-align: right;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        td.text-right {
            text-align: right;
        }
        tbody tr:hover {
            background: #f9fafb;
        }
        .product-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }
        .product-sku {
            font-size: 9px;
            color: #9ca3af;
        }
        .total-row {
            background: #f9fafb;
            font-weight: bold;
        }
        .total-row td {
            border-bottom: none;
            padding: 15px 10px;
        }
        .total-label {
            text-align: right;
            font-size: 12px;
            color: #4b5563;
        }
        .total-amount {
            font-size: 16px;
            color: #dc2626;
            text-align: right;
        }
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 3px;
        }
        .notes-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .notes-content {
            color: #78350f;
            font-size: 10px;
            line-height: 1.5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
        .footer-brand {
            font-weight: bold;
            color: #6366f1;
        }
        .amount-highlight {
            background: #fef3c7;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="logo-section">
                    <div class="logo-text">Shopledger</div>
                    <div class="logo-tagline">Multi-Shop Management System</div>
                </div>
                <div class="doc-title">
                    <h1>PURCHASE NOTE</h1>
                    <div class="doc-number">{{ $purchase->shop->name ?? 'Shop' }}</div>
                </div>
            </div>
        </div>

        <!-- Info Sections -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <div class="info-title">Purchase Information</div>
                    <div class="info-content">
                        <p><span class="info-label">Purchase #:</span> <span class="info-value">{{ $purchase->id }}</span></p>
                        <p><span class="info-label">Date:</span> <span class="info-value">{{ $purchase->purchase_date->format('M d, Y') }}</span></p>
                        <p><span class="info-label">Created By:</span> <span class="info-value">{{ $purchase->creator->name ?? 'Unknown' }}</span></p>
                        <p><span class="info-label">Created At:</span> <span class="info-value">{{ $purchase->created_at->format('M d, Y h:i A') }}</span></p>
                    </div>
                </div>
            </div>
            <div class="info-right">
                @if($purchase->supplier)
                <div class="info-box">
                    <div class="info-title">Supplier Details</div>
                    <div class="info-content">
                        <p><span class="info-label">Name:</span> <span class="info-value">{{ $purchase->supplier->name }}</span></p>
                        @if($purchase->supplier->phone)
                        <p><span class="info-label">Phone:</span> <span class="info-value">{{ $purchase->supplier->phone }}</span></p>
                        @endif
                        @if($purchase->supplier->email)
                        <p><span class="info-label">Email:</span> <span class="info-value">{{ $purchase->supplier->email }}</span></p>
                        @endif
                        @if($purchase->supplier->address)
                        <p><span class="info-label">Address:</span> <span class="info-value">{{ $purchase->supplier->address }}</span></p>
                        @endif
                    </div>
                </div>
                @else
                <div class="info-box">
                    <div class="info-title">Supplier Details</div>
                    <div class="info-content">
                        <p style="color: #9ca3af; font-style: italic;">No supplier specified</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 42%;">Product</th>
                    <th class="text-right" style="width: 15%;">Quantity</th>
                    <th class="text-right" style="width: 17%;">Unit Cost</th>
                    <th class="text-right" style="width: 18%;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product->name }}</div>
                        <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ rwf($item->unit_cost) }}</td>
                    <td class="text-right"><span class="amount-highlight">{{ rwf($item->line_total) }}</span></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="total-label">TOTAL PURCHASE AMOUNT:</td>
                    <td class="total-amount">{{ rwf($purchase->total_amount) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Notes -->
        <div class="notes-section">
            <div class="notes-title">📝 Important Notes</div>
            <div class="notes-content">
                <p>• Stock quantities have been automatically updated for all purchased items.</p>
                <p>• This purchase note serves as a record of inventory received on {{ $purchase->purchase_date->format('F d, Y') }}.</p>
                <p>• Total items purchased: <strong>{{ $purchase->items->count() }}</strong></p>
                <p>• Total units received: <strong>{{ $purchase->items->sum('quantity') }}</strong></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            <p style="margin-top: 5px;">Powered by <span class="footer-brand">Shopledger</span> - Multi-Shop Management System</p>
            <p style="margin-top: 3px;">Made with ❤️ in Rwanda</p>
        </div>
    </div>
</body>
</html>
