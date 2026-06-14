<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display all plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::latest()->paginate(10);

        return view('admin.subscription-plans.index', compact('plans'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    /**
     * Store new plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_users' => 'required|integer|min:1',
            'max_products' => 'required|integer|min:1',
            'max_branches' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        SubscriptionPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'max_users' => $request->max_users,
            'max_products' => $request->max_products,
            'max_branches' => $request->max_branches,
            'features' => $request->features,
            'status' => $request->status
        ]);

        return redirect()
            ->route('subscription-plans.index')
            ->with('success', 'Subscription plan created successfully');
    }

    /**
     * Show single plan
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        return view(
            'admin.subscription-plans.show',
            compact('subscriptionPlan')
        );
    }

    /**
     * Edit form
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view(
            'admin.subscription-plans.edit',
            compact('subscriptionPlan')
        );
    }

    /**
     * Update plan
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_users' => 'required|integer|min:1',
            'max_products' => 'required|integer|min:1',
            'max_branches' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $subscriptionPlan->update([
            'name' => $request->name,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'max_users' => $request->max_users,
            'max_products' => $request->max_products,
            'max_branches' => $request->max_branches,
            'features' => $request->features,
            'status' => $request->status
        ]);

        return redirect()
            ->route('subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully');
    }

    /**
     * Delete plan
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()
            ->route('subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully');
    }
}