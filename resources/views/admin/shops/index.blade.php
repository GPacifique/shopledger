<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold">{{ __('subscription.shops_and_subscriptions') }}</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.subscription-plans.index') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold">{{ __('subscription.manage_plans') }}</a>
                <a href="{{ route('admin.shops.create') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">{{ __('subscription.create_shop') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border p-5" style="border-color:var(--border);background:var(--surface)">
                <p class="text-sm" style="color:var(--text-muted)">{{ __('subscription.monthly_revenue') }}</p>
                <p class="mt-1 text-2xl font-bold">{{ number_format($mrr) }} RWF</p>
            </div>
            <div class="rounded-xl border p-5" style="border-color:var(--border);background:var(--surface)">
                <p class="text-sm" style="color:var(--text-muted)">{{ __('subscription.expiring_this_month') }}</p>
                <p class="mt-1 text-2xl font-bold">{{ $expiringThisMonth }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.shops.index') }}" class="flex flex-wrap items-center gap-3 rounded-xl border p-4" style="border-color:var(--border);background:var(--surface)">
            <label for="subscription-filter" class="text-sm font-semibold">{{ __('subscription.subscription_status') }}</label>
            <select id="subscription-filter" name="subscription" class="rounded-lg border p-2 text-sm">
                @foreach(['all' => __('subscription.all_shops'), 'active' => __('Active'), 'expiring' => __('subscription.expiring_soon'), 'expired' => __('subscription.expired_title')] as $value => $label)
                    <option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">{{ __('subscription.filter') }}</button>
        </form>

        <div class="overflow-x-auto rounded-xl border" style="border-color:var(--border);background:var(--surface)">
            <table class="w-full min-w-[1250px] text-left text-sm">
                <thead style="background:var(--surface-2);color:var(--text-soft)">
                    <tr>
                        <th class="p-3">{{ __('subscription.shop') }}</th>
                        <th class="p-3">{{ __('subscription.owner') }}</th>
                        <th class="p-3">{{ __('subscription.subscription') }}</th>
                        <th class="p-3">{{ __('subscription.plan') }}</th>
                        <th class="p-3">{{ __('subscription.expiry_date') }}</th>
                        <th class="p-3">{{ __('subscription.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shops as $shop)
                        @php
                            $subscription = $shop->subscription;
                            $plan = $subscription?->subscriptionPlan;
                            $status = $subscription?->status ?? 'missing';
                        @endphp
                        <tr class="border-t align-top" style="border-color:var(--border)">
                            <td class="p-3">
                                <p class="font-semibold">{{ $shop->business_name }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($shop->status) }}</p>
                            </td>
                            <td class="p-3">{{ $shop->creator?->name ?? '—' }}</td>
                            <td class="p-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ in_array($status, ['active', 'trial'], true) ? 'bg-green-100 text-green-800' : ($status === 'past_due' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ __(ucfirst(str_replace('_', ' ', $status))) }}
                                </span>
                            </td>
                            <td class="p-3">{{ $plan?->name ?? __('subscription.no_plan') }}</td>
                            <td class="p-3">{{ $subscription?->ends_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="p-3">
                                @if($subscription)
                                    <div class="grid gap-3">
                                        <form method="POST" action="{{ route('admin.subscriptions.payments.store', $subscription) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <select name="method" required class="rounded border p-1.5 text-xs">
                                                <option value="mobile_money">{{ __('subscription.mobile_money') }}</option>
                                                <option value="bank">{{ __('subscription.bank') }}</option>
                                                <option value="cash">{{ __('subscription.cash') }}</option>
                                            </select>
                                            <input name="reference" maxlength="255" placeholder="{{ __('subscription.reference') }}" class="w-32 rounded border p-1.5 text-xs">
                                            <button class="rounded bg-green-700 px-2.5 py-1.5 text-xs font-semibold text-white">{{ __('subscription.record_payment') }}</button>
                                        </form>

                                        <div class="flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.subscriptions.extend', $subscription) }}" class="flex items-center gap-1">
                                                @csrf
                                                <input type="number" name="days" value="30" min="1" max="365" class="w-20 rounded border p-1.5 text-xs" aria-label="{{ __('subscription.days_to_extend') }}">
                                                <button class="rounded border px-2 py-1.5 text-xs font-semibold">{{ __('subscription.extend') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.subscriptions.plan.update', $subscription) }}" class="flex items-center gap-1">
                                                @csrf
                                                <select name="subscription_plan_id" required class="max-w-36 rounded border p-1.5 text-xs">
                                                    @foreach($plans as $availablePlan)
                                                        <option value="{{ $availablePlan->id }}" @selected($plan?->id === $availablePlan->id)>{{ $availablePlan->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="rounded border px-2 py-1.5 text-xs font-semibold">{{ __('subscription.change_plan') }}</button>
                                            </form>
                                            @if($status === 'suspended')
                                                <form method="POST" action="{{ route('admin.subscriptions.reactivate', $subscription) }}">
                                                    @csrf
                                                    <button class="rounded bg-green-700 px-2.5 py-1.5 text-xs font-semibold text-white">{{ __('subscription.reactivate') }}</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.subscriptions.suspend', $subscription) }}">
                                                    @csrf
                                                    <button class="rounded bg-red-700 px-2.5 py-1.5 text-xs font-semibold text-white">{{ __('subscription.suspend') }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('admin.shops.subscription.assign', $shop) }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        <label for="assign-plan-{{ $shop->id }}" class="sr-only">{{ __('subscription.plan') }}</label>
                                        <select id="assign-plan-{{ $shop->id }}" name="subscription_plan_id" required class="max-w-40 rounded border p-1.5 text-xs">
                                            @foreach($plans as $availablePlan)
                                                <option value="{{ $availablePlan->id }}">{{ $availablePlan->name }} — {{ number_format($availablePlan->price) }} RWF</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded bg-blue-700 px-2.5 py-1.5 text-xs font-semibold text-white">{{ __('subscription.assign_plan') }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-6 text-center text-slate-500" colspan="6">{{ __('subscription.no_shops_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $shops->links() }}
    </div>
</x-app-layout>
