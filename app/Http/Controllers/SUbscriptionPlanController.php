<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('administer-platform');

        $plans = SubscriptionPlan::query()->orderBy('price')->paginate(20);

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create(): View
    {
        Gate::authorize('administer-platform');

        return view('admin.subscription-plans.form', [
            'subscriptionPlan' => new SubscriptionPlan(['billing_period' => 'monthly', 'max_users' => 3]),
            'action' => route('admin.subscription-plans.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('administer-platform');
        $validated = $this->validated($request);
        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')->with('success', __('subscription.plan_created'));
    }

    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        Gate::authorize('administer-platform');

        return view('admin.subscription-plans.form', [
            'subscriptionPlan' => $subscriptionPlan,
            'action' => route('admin.subscription-plans.update', $subscriptionPlan),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        Gate::authorize('administer-platform');
        $validated = $this->validated($request, $subscriptionPlan);
        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')->with('success', __('subscription.plan_updated'));
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        Gate::authorize('administer-platform');
        abort_if($subscriptionPlan->subscriptions()->exists(), 422, __('subscription.plan_delete_in_use'));
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')->with('success', __('subscription.plan_deleted'));
    }

    private function validated(Request $request, ?SubscriptionPlan $subscriptionPlan = null): array
    {
        $request->merge([
            'features' => array_values(array_filter(
                (array) $request->input('features', []),
                fn ($feature) => is_string($feature) && trim($feature) !== ''
            )),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'nullable',
                'string',
                'max:140',
                Rule::unique('subscription_plans', 'slug')->ignore($subscriptionPlan?->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', Rule::in(['monthly', 'quarterly', 'yearly'])],
            'max_users' => ['required', 'integer', 'min:1'],
            'max_products' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:100'],
        ]);
    }
}
