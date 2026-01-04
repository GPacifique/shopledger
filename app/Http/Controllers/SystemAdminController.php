<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemAdminController extends Controller
{
    public function dashboard()
    {
        $totalShops = Shop::count();
        $pendingShops = Shop::where('status', 'pending')->count();
        $approvedShops = Shop::where('status', 'approved')->count();
        $totalUsers = User::count();
        $unassignedUsers = User::whereNull('shop_id')->where('role', 'user')->count();

        $pendingShopsList = Shop::where('status', 'pending')
            ->with('users')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $recentShops = Shop::with('users')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalShops', 'pendingShops', 'approvedShops', 'totalUsers',
            'pendingShopsList', 'recentShops', 'unassignedUsers'
        ));
    }

    public function approveShop(Shop $shop)
    {
        $shop->status = 'approved';
        $shop->approved_by = auth()->id();
        $shop->approved_at = now();
        $shop->save();

        return back()->with('success', 'Shop approved successfully.');
    }

    public function rejectShop(Shop $shop)
    {
        $shop->status = 'rejected';
        $shop->save();

        return back()->with('success', 'Shop rejected.');
    }

    public function createShop()
    {
        $unassignedUsers = User::whereNull('shop_id')
            ->where('role', 'user')
            ->orderBy('name')
            ->get();

        return view('admin.shops.create', compact('unassignedUsers'));
    }

    public function storeShop(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,approved',
        ]);

        $shop = Shop::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'approved_by' => $request->status === 'approved' ? auth()->id() : null,
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);

        // Assign user as shop_admin
        $user = User::findOrFail($request->user_id);
        $user->shop_id = $shop->id;
        $user->role = 'shop_admin';
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Shop created and assigned to ' . $user->name . ' successfully.');
    }

    public function listUsers()
    {
        $users = User::with('shop')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}
