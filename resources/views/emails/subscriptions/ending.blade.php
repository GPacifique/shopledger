<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;background:#f1f5f9;color:#0f172a;font-family:Arial,sans-serif">
    <div style="max-width:620px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden">
        <div style="padding:24px;background:#1d4ed8;color:#fff;text-align:center">
            <img src="{{ asset('icons/apple-touch-icon.png') }}" width="56" height="56" alt="MahWi" style="display:block;margin:0 auto 12px;border-radius:12px">
            <strong style="font-size:22px">MahWi</strong>
        </div>
        <div style="padding:28px">
            <h1 style="margin-top:0;font-size:22px">{{ $heading }}</h1>
            <p>{{ __('subscription.shop') }}: <strong>{{ $shopName }}</strong></p>
            <p>{{ __('subscription.plan') }}: <strong>{{ $planName }}</strong></p>
            <p>{{ __('subscription.expiry_date') }}: <strong>{{ $expiryDate }}</strong></p>
            <p>{{ __('subscription.days_left') }}: <strong>{{ $daysLeft }}</strong></p>
            <p>{{ __('subscription.amount_due') }}: <strong>{{ $amountDue }} RWF</strong></p>
            <p style="margin:28px 0">
                <a href="{{ $renewUrl }}" style="display:inline-block;background:#1d4ed8;color:#fff;padding:13px 22px;border-radius:8px;text-decoration:none;font-weight:bold">
                    {{ __('subscription.renew_button') }}
                </a>
            </p>
            <p style="font-size:13px;color:#64748b">{{ config('subscription.payment_instructions') ?: __('subscription.contact_support') }}</p>
        </div>
    </div>
</body>
</html>
