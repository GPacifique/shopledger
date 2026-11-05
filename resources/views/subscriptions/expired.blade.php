@extends('layouts.app')

@section('title', __('subscription.expired_page_title'))

@section('content')
<div class="mx-auto max-w-3xl rounded-2xl border p-6 shadow-sm sm:p-8" style="border-color: var(--border); background: var(--surface)">
    <h1 class="text-2xl font-bold" style="color: var(--text)">{{ __('subscription.expired_page_title') }}</h1>

    @if($user->role === 'shop_admin')
        <p class="mt-3" style="color: var(--text-soft)">
            {{ __('subscription.renew_access') }}
        </p>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm" style="color: var(--text-muted)">{{ __('subscription.shop') }}</dt>
                <dd class="font-semibold">{{ $shop->business_name }}</dd>
            </div>
            <div>
                <dt class="text-sm" style="color: var(--text-muted)">{{ __('subscription.plan') }}</dt>
                <dd class="font-semibold">{{ $subscription?->subscriptionPlan?->name ?? __('subscription.no_plan') }}</dd>
            </div>
            <div>
                <dt class="text-sm" style="color: var(--text-muted)">{{ __('subscription.expiry_date') }}</dt>
                <dd class="font-semibold">{{ $subscription?->ends_at?->format('Y-m-d') ?? __('Not available') }}</dd>
            </div>
            <div>
                <dt class="text-sm" style="color: var(--text-muted)">{{ __('subscription.amount_due') }}</dt>
                <dd class="font-semibold">{{ number_format((float) ($subscription?->subscriptionPlan?->price ?? 0)) }} RWF</dd>
            </div>
        </dl>

        <div class="mt-6 rounded-xl border p-4" style="border-color: var(--border); background: var(--surface-2)">
            <h2 class="font-semibold">{{ __('subscription.payment_instructions') }}</h2>
            <p class="mt-1 text-sm" style="color: var(--text-soft)">{{ config('subscription.payment_instructions') ?: __('subscription.contact_support') }}</p>
        </div>
        <a href="{{ route('shop.subscriptions.index') }}" class="mt-5 inline-flex rounded-lg bg-blue-700 px-4 py-2.5 font-semibold text-white">{{ __('subscription.view_subscription') }}</a>
    @else
        <p class="mt-3" style="color: var(--text-soft)">
            {{ __('subscription.contact_shop_admin') }}
        </p>
    @endif
</div>
@endsection
