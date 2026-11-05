<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Carbon;

class SubscriptionLifecycleService
{
    public function assignPlan(Shop $shop, SubscriptionPlan $plan): Subscription
    {
        $subscription = $shop->subscription;
        if ($subscription) {
            $subscription->update(['subscription_plan_id' => $plan->id]);

            return $subscription->refresh();
        }

        $startsAt = now();
        $pending = $shop->isPending();
        $endsAt = $pending
            ? $startsAt->copy()->addDays((int) config('subscription.trial_days', 14))
            : $this->periodEnd($startsAt, $plan);

        return $shop->subscriptions()->create([
            'subscription_plan_id' => $plan->id,
            'status' => $pending ? 'trial' : ($shop->isApproved() ? 'active' : 'suspended'),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'trial_ends_at' => $pending ? $endsAt : null,
        ]);
    }

    public function activate(Shop $shop): Subscription
    {
        $subscription = $shop->subscription()->with('subscriptionPlan')->first();

        if (! $subscription) {
            $plan = SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('price')
                ->firstOrFail();
            $subscription = Subscription::create([
                'shop_id' => $shop->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'trial',
                'starts_at' => now(),
                'ends_at' => now()->addDays((int) config('subscription.trial_days', 14)),
                'trial_ends_at' => now()->addDays((int) config('subscription.trial_days', 14)),
            ])->load('subscriptionPlan');
        }

        $startsAt = now();
        $subscription->update([
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $this->periodEnd($startsAt, $subscription->subscriptionPlan),
            'trial_ends_at' => null,
            'grace_ends_at' => null,
        ]);

        return $subscription->refresh();
    }

    public function recordPayment(
        Subscription $subscription,
        string $method,
        ?string $reference,
        ?string $notes,
        ?User $recordedBy
    ): SubscriptionPayment {
        $periodStart = $subscription->ends_at && $subscription->ends_at->isFuture()
            ? $subscription->ends_at->copy()
            : now();
        $periodEnd = $this->periodEnd($periodStart, $subscription->subscriptionPlan);

        $subscription->update([
            'status' => 'active',
            'ends_at' => $periodEnd,
            'grace_ends_at' => null,
            'cancelled_at' => null,
        ]);

        return $subscription->payments()->create([
            'shop_id' => $subscription->shop_id,
            'amount' => $subscription->subscriptionPlan->price,
            'method' => $method,
            'reference' => $reference,
            'paid_at' => now(),
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'recorded_by' => $recordedBy?->id,
            'notes' => $notes,
        ]);
    }

    private function periodEnd(Carbon $periodStart, SubscriptionPlan $plan): Carbon
    {
        return match ($plan->billing_period) {
            'quarterly' => $periodStart->copy()->addMonths(3),
            'yearly' => $periodStart->copy()->addYear(),
            default => $periodStart->copy()->addMonth(),
        };
    }
}
