<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ $subscriptionPlan->exists ? __('subscription.edit_plan') : __('subscription.create_plan') }}</h1>
    </x-slot>

    <form method="POST" action="{{ $action }}" class="mx-auto max-w-3xl space-y-5 rounded-xl border p-5 sm:p-7" style="border-color:var(--border);background:var(--surface)">
        @csrf
        @if($method !== 'POST') @method($method) @endif
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm font-medium">{{ __('subscription.name') }}
                <input name="name" required maxlength="120" value="{{ old('name', $subscriptionPlan->name) }}" class="mt-1 w-full rounded-lg border p-2.5">
            </label>
            <label class="block text-sm font-medium">{{ __('subscription.slug') }}
                <input name="slug" maxlength="140" value="{{ old('slug', $subscriptionPlan->slug) }}" class="mt-1 w-full rounded-lg border p-2.5">
            </label>
            <label class="block text-sm font-medium">{{ __('subscription.price_rwf') }}
                <input type="number" name="price" required min="0" step="1" value="{{ old('price', $subscriptionPlan->price ?? 0) }}" class="mt-1 w-full rounded-lg border p-2.5">
            </label>
            <label class="block text-sm font-medium">{{ __('subscription.billing_period') }}
                <select name="billing_period" required class="mt-1 w-full rounded-lg border p-2.5">
                    @foreach(['monthly', 'quarterly', 'yearly'] as $period)
                        <option value="{{ $period }}" @selected(old('billing_period', $subscriptionPlan->billing_period) === $period)>{{ __(ucfirst($period)) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-medium">{{ __('subscription.max_users') }}
                <input type="number" name="max_users" required min="1" value="{{ old('max_users', $subscriptionPlan->max_users ?? 3) }}" class="mt-1 w-full rounded-lg border p-2.5">
            </label>
            <label class="block text-sm font-medium">{{ __('subscription.max_products') }} <span class="text-xs text-slate-500">({{ __('subscription.leave_blank_unlimited') }})</span>
                <input type="number" name="max_products" min="1" value="{{ old('max_products', $subscriptionPlan->max_products) }}" class="mt-1 w-full rounded-lg border p-2.5">
            </label>
        </div>

        <fieldset>
            <legend class="text-sm font-semibold">{{ __('subscription.features') }}</legend>
            @foreach(old('features', $subscriptionPlan->features ?? []) as $feature)
                <input name="features[]" value="{{ $feature }}" class="mt-2 w-full rounded-lg border p-2.5" maxlength="100">
            @endforeach
            <input name="features[]" value="" class="mt-2 w-full rounded-lg border p-2.5" maxlength="100" placeholder="{{ __('subscription.add_feature') }}">
        </fieldset>

        <label class="flex items-center gap-2 text-sm font-medium">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $subscriptionPlan->is_active ?? true))>
            {{ __('Active') }}
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 font-semibold text-white hover:bg-blue-800">{{ __('subscription.save_plan') }}</button>
            <a href="{{ route('admin.subscription-plans.index') }}" class="rounded-lg border px-5 py-2.5 font-semibold">{{ __('subscription.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
