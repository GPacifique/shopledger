<?php

namespace App\Http\Controllers;

use App\Models\ShopSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopSubscriptionController extends Controller
{
    /**
     * Display subscriptions for current shop
     */
    public function index()
    {
        $shopSubscriptions = ShopSubscription::with('subscriptionPlan')
            ->where('shop_id', Auth::user()->shop_id)
            ->latest()
            ->paginate(15);

        return view(
            'admin.subscriptions.index',
            compact('shopSubscriptions')
        );
    }

    /**
     * Show available plans
     */
    public function create()
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();

        return view(
            'admin.subscriptions.create',
            compact('plans')
        );
    }

    /**
     * Store new subscription request
     */
    public function store(Request $request)
    {
        $request->validate([
            'subscriptionplan_id' => 'required|exists:subscription_plans,id'
        ]);

        $plan = SubscriptionPlan::findOrFail(
            $request->subscriptionplan_id
        );

        $duration = $plan->billing_cycle === 'yearly' ? 12 : 1;

        ShopSubscription::create([
            'shop_id' => Auth::user()->shop_id,
            'subscriptionplan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addMonths($duration),
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        return redirect()
            ->route('shop.subscriptions.index')
            ->with(
                'success',
                'Subscription request submitted successfully.'
            );
    }

    /**
     * Display specific subscription
     */
    public function show(ShopSubscription $shopSubscription)
    {
        return view(
            'admin.subscriptions.show',
            compact('shopSubscription')
        );
    }

    /**
     * Show edit form
     */
    public function edit(ShopSubscription $shopSubscription)
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();

        return view(
            'admin.subscriptions.edit',
            compact('shopSubscription', 'plans')
        );
    }

    /**
     * Update subscription
     */
    public function update(
        Request $request,
        ShopSubscription $shopSubscription
    ) {
        $request->validate([
            'subscriptionplan_id' => 'required|exists:subscription_plans,id'
        ]);

        $shopSubscription->update([
            'subscriptionplan_id' => $request->subscriptionplan_id
        ]);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with(
                'success',
                'Subscription updated successfully.'
            );
    }

    /**
     * Cancel subscription
     */
    public function destroy(ShopSubscription $shopSubscription)
    {
        $shopSubscription->update([
            'status' => 'cancelled'
        ]);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with(
                'success',
                'Subscription cancelled successfully.'
            );
    }
}