<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Shop;
use App\Services\SubscriptionLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminSubscriptionController extends Controller
{
    public function assignPlan(
        Request $request,
        Shop $shop,
        SubscriptionLifecycleService $lifecycle
    ): RedirectResponse {
        Gate::authorize('administer-platform');

        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);
        $plan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->findOrFail($validated['subscription_plan_id']);

        $lifecycle->assignPlan($shop, $plan);

        return back()->with('success', __('subscription.plan_assigned'));
    }

    public function recordPayment(
        Request $request,
        Subscription $subscription,
        SubscriptionLifecycleService $lifecycle
    ): RedirectResponse {
        Gate::authorize('administer-platform');
        $this->ensureCurrent($subscription);

        $validated = $request->validate([
            'method' => ['required', 'in:mobile_money,bank,cash'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lifecycle->recordPayment(
            $subscription->load('subscriptionPlan'),
            $validated['method'],
            $validated['reference'] ?? null,
            $validated['notes'] ?? null,
            $request->user()
        );

        return back()->with('success', __('subscription.payment_recorded'));
    }

    public function extend(Request $request, Subscription $subscription): RedirectResponse
    {
        Gate::authorize('administer-platform');
        $this->ensureCurrent($subscription);
        $validated = $request->validate(['days' => ['required', 'integer', 'between:1,365']]);
        $periodStart = $subscription->ends_at?->isFuture() ? $subscription->ends_at->copy() : now();

        $subscription->update([
            'status' => 'active',
            'ends_at' => $periodStart->addDays($validated['days']),
            'grace_ends_at' => null,
            'cancelled_at' => null,
        ]);

        return back()->with('success', __('subscription.subscription_extended'));
    }

    public function changePlan(Request $request, Subscription $subscription): RedirectResponse
    {
        Gate::authorize('administer-platform');
        $this->ensureCurrent($subscription);
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->findOrFail($validated['subscription_plan_id']);
        $subscription->update(['subscription_plan_id' => $plan->id]);

        return back()->with('success', __('subscription.plan_changed'));
    }

    public function suspend(Subscription $subscription): RedirectResponse
    {
        Gate::authorize('administer-platform');
        $this->ensureCurrent($subscription);
        $subscription->update(['status' => 'suspended']);

        return back()->with('success', __('subscription.subscription_suspended'));
    }

    public function reactivate(
        Subscription $subscription,
        SubscriptionLifecycleService $lifecycle
    ): RedirectResponse {
        Gate::authorize('administer-platform');
        $this->ensureCurrent($subscription);
        $lifecycle->activate($subscription->shop);

        return back()->with('success', __('subscription.subscription_reactivated'));
    }

    private function ensureCurrent(Subscription $subscription): void
    {
        abort_unless(
            $subscription->shop?->subscription?->is($subscription),
            404
        );
    }
}
