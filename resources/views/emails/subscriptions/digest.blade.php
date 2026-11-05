<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;background:#f1f5f9;color:#0f172a;font-family:Arial,sans-serif">
    <div style="max-width:760px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden">
        <div style="padding:22px;background:#1d4ed8;color:#fff">
            <img src="{{ asset('icons/apple-touch-icon.png') }}" width="48" height="48" alt="MahWi" style="vertical-align:middle;border-radius:10px">
            <strong style="margin-left:10px;font-size:22px">MahWi</strong>
        </div>
        <div style="padding:26px">
            <h1 style="margin-top:0">{{ __('subscription.digest_heading') }}</h1>
            <p>{{ __('subscription.digest_date') }}: {{ now()->toDateString() }}</p>
            <table style="width:100%;border-collapse:collapse;text-align:left">
                <thead><tr>
                    <th style="padding:10px;border-bottom:1px solid #cbd5e1">{{ __('subscription.shop') }}</th>
                    <th style="padding:10px;border-bottom:1px solid #cbd5e1">{{ __('subscription.plan') }}</th>
                    <th style="padding:10px;border-bottom:1px solid #cbd5e1">{{ __('subscription.status') }}</th>
                    <th style="padding:10px;border-bottom:1px solid #cbd5e1">{{ __('subscription.expiry_date') }}</th>
                    <th style="padding:10px;border-bottom:1px solid #cbd5e1">{{ __('subscription.days_left') }}</th>
                </tr></thead>
                <tbody>
                    @foreach($subscriptions as $item)
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0">{{ $item['shop'] }}</td>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0">{{ $item['plan'] }}</td>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0">{{ $item['status'] }}</td>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0">{{ $item['expiry'] }}</td>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0">{{ $item['days'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
