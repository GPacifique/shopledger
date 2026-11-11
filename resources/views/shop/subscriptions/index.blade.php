<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ __('subscription.my_subscription') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        @if($ownerShops->count() > 1)
            <form method="GET" action="{{ route('shop.subscriptions.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="subscription-shop" class="block text-sm font-medium">{{ __('Shop') }}</label>
                    <select id="subscription-shop" name="shop" class="mt-1 rounded-lg border-slate-300">
                        @foreach($ownerShops as $ownerShop)
                            <option value="{{ $ownerShop->id }}" @selected((int) request('shop', $shop->id) === $ownerShop->id)>
                                {{ $ownerShop->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">
                    {{ __('View subscription') }}
                </button>
            </form>
        @endif

        <section class="rounded-2xl border p-5 sm:p-7" style="border-color:var(--border);background:var(--surface)">
            <h2 class="text-lg font-semibold">{{ $shop->business_name }}</h2>
            @if($subscription)
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div><p class="text-sm text-slate-500">{{ __('subscription.plan') }}</p><p class="font-semibold">{{ $subscription->subscriptionPlan?->name ?? __('subscription.no_plan') }}</p></div>
                    <div><p class="text-sm text-slate-500">{{ __('Status') }}</p><p class="font-semibold">{{ __(ucfirst(str_replace('_', ' ', $subscription->status))) }}</p></div>
                    <div><p class="text-sm text-slate-500">{{ __('subscription.days_remaining') }}</p><p class="font-semibold">{{ max(0, $subscription->daysRemaining()) }}</p></div>
                    <div><p class="text-sm text-slate-500">{{ __('subscription.expiry_date') }}</p><p class="font-semibold">{{ $subscription->ends_at?->format('Y-m-d') ?? __('Not available') }}</p></div>
                    <div><p class="text-sm text-slate-500">{{ __('subscription.price') }}</p><p class="font-semibold">{{ number_format((float) ($subscription->subscriptionPlan?->price ?? 0)) }} RWF / {{ __($subscription->subscriptionPlan?->billing_period ?? 'monthly') }}</p></div>
                    @if($subscription->onGracePeriod())
                        <div><p class="text-sm text-slate-500">{{ __('subscription.grace_ends') }}</p><p class="font-semibold">{{ $subscription->grace_ends_at?->format('Y-m-d') }}</p></div>
                    @endif
                </div>
            @else
                <p class="mt-4">{{ __('subscription.no_subscription') }}</p>
            @endif
        </section>

        <section class="rounded-2xl border p-5 sm:p-7" style="border-color:var(--border);background:var(--surface)">
            <h2 class="text-lg font-semibold">{{ __('subscription.how_to_renew') }}</h2>
            <p class="mt-2" style="color:var(--text-soft)">{{ config('subscription.payment_instructions') ?: __('subscription.contact_support') }}</p>
            <p class="mt-2 text-sm" style="color:var(--text-muted)">{{ __('subscription.payment_confirmed') }}</p>
        </section>

        <section class="overflow-hidden rounded-2xl border" style="border-color:var(--border);background:var(--surface)">
            <h2 class="p-5 text-lg font-semibold">{{ __('subscription.payment_history') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-left text-sm">
                    <thead style="background:var(--surface-2)">
                        <tr>
                            <th class="p-3">{{ __('subscription.date_paid') }}</th>
                            <th class="p-3">{{ __('subscription.amount_due') }}</th>
                            <th class="p-3">{{ __('subscription.method') }}</th>
                            <th class="p-3">{{ __('subscription.reference') }}</th>
                            <th class="p-3">{{ __('subscription.period') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscription?->payments ?? [] as $payment)
                            <tr class="border-t" style="border-color:var(--border)">
                                <td class="p-3">{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                <td class="p-3">{{ number_format((float) $payment->amount) }} RWF</td>
                                <td class="p-3">{{ __(ucwords(str_replace('_', ' ', $payment->method))) }}</td>
                                <td class="p-3">{{ $payment->reference ?: '—' }}</td>
                                <td class="p-3">{{ $payment->period_start?->format('Y-m-d') }} – {{ $payment->period_end?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td class="p-5 text-center text-slate-500" colspan="5">{{ __('subscription.no_payments') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
