<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isSystemAdmin()) {
            return response()->json(Shop::with('creator', 'subscriptionPlan')->paginate(20));
        }

        return response()->json($user->shop()->with('subscriptionPlan')->first());
    }

    // Shop admin registration (creates pending shop)
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:shops,registration_number',
            'tin_number' => 'nullable|string|max:255',
            'email' => 'required|email|unique:shops,email',
            'phone' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'logo' => 'nullable|string',
            'subscriptionplan_id' => 'nullable|exists:subscriptionplans,id',
        ]);

        $user = $request->user();

        // Prevent a user from registering more than one shop
        if ($user->shop_id) {
            return response()->json(['message' => 'You already have a registered shop.'], 422);
        }

        $shop = Shop::create([
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'registration_number' => $request->registration_number,
            'tin_number' => $request->tin_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'logo' => $request->logo,
            'subscriptionplan_id' => $request->subscriptionplan_id,
            'slug' => Str::slug($request->business_name) . '-' . Str::random(6),
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        // Link user to shop and promote to shop_admin
        $user->shop_id = $shop->id;
        $user->role = 'shop_admin';
        $user->save();

        return response()->json($shop, 201);
    }

    // System admin approves
    public function approve(Request $request, Shop $shop)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin()) {
            abort(403);
        }

        $shop->status = 'approved';
        $shop->approved_by = $user->id;
        $shop->approved_at = now();
        $shop->save();

        return response()->json($shop);
    }

    public function show(Request $request, Shop $shop)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin() && $user->shop_id !== $shop->id) {
            abort(403);
        }

        return response()->json($shop->load('subscriptionPlan'));
    }

    public function update(Request $request, Shop $shop)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin() && $user->shop_id !== $shop->id) {
            abort(403);
        }

        $request->validate([
            'business_name' => 'sometimes|required|string|max:255',
            'business_type' => 'sometimes|required|string|max:255',
            'tin_number' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:255',
            'country' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pending,approved,rejected',
            'logo' => 'sometimes|nullable|string',
            'subscriptionplan_id' => 'sometimes|nullable|integer|exists:subscriptionplans,id',
        ]);

        $shop->update($request->only(
            'business_name',
            'business_type',
            'tin_number',
            'phone',
            'country',
            'city',
            'address',
            'status',
            'logo',
            'subscriptionplan_id'
        ));

        return response()->json($shop);
    }
}