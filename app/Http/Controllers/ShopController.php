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
            return response()->json(Shop::with('users')->paginate(20));
        }
        return response()->json(Shop::where('id', $user->shop_id)->first());
    }

    // Shop admin registration (creates pending shop)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $user = $request->user();

        $shop = Shop::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        // Attach shop to user if not already
        if (!$user->shop_id) {
            $user->shop_id = $shop->id;
            $user->role = 'shop_admin';
            $user->save();
        }

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
}
