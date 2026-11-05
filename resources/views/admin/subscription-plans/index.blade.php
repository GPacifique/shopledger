<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold">{{ __('subscription.subscription_plans') }}</h1>
            <a href="{{ route('admin.subscription-plans.create') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                {{ __('subscription.create_plan') }}
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5">
        <div class="overflow-x-auto rounded-xl border" style="border-color:var(--border);background:var(--surface)">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead style="background:var(--surface-2);color:var(--text-soft)">
                    <tr>
                        <th class="p-3">{{ __('subscription.plan') }}</th>
                        <th class="p-3">{{ __('subscription.price') }}</th>
                        <th class="p-3">{{ __('subscription.billing_period') }}</th>
                        <th class="p-3">{{ __('subscription.user_limit') }}</th>
                        <th class="p-3">{{ __('subscription.product_limit') }}</th>
                        <th class="p-3">{{ __('Status') }}</th>
                        <th class="p-3">{{ __('subscription.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr class="border-t" style="border-color:var(--border)">
                            <td class="p-3 font-semibold">{{ $plan->name }} <span class="text-xs font-normal text-slate-500">({{ $plan->slug }})</span></td>
                            <td class="p-3">{{ number_format((float) $plan->price) }} RWF</td>
                            <td class="p-3">{{ __(ucfirst($plan->billing_period)) }}</td>
                            <td class="p-3">{{ $plan->max_users }}</td>
                            <td class="p-3">{{ $plan->max_products ?? __('subscription.unlimited') }}</td>
                            <td class="p-3">{{ $plan->is_active ? __('Active') : __('Inactive') }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-3">
                                    <a class="font-semibold text-blue-700 underline" href="{{ route('admin.subscription-plans.edit', $plan) }}">{{ __('subscription.edit_plan') }}</a>
                                    <form method="POST" action="{{ route('admin.subscription-plans.destroy', $plan) }}" onsubmit="return confirm(@js(__('subscription.delete_plan_confirm')))">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-700 underline" type="submit">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-6 text-center text-slate-500" colspan="7">{{ __('subscription.no_plans_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $plans->links() }}
    </div>
</x-app-layout>
