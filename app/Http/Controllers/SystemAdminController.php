<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
 use App\Models\SubscriptionPlan; 
 use Illuminate\Support\Facades\DB;
class SystemAdminController extends Controller
{
    /**
     * Display the system administration settings page.
     */
    public function settings()
    {
        // Use the settings view when it exists.
        if (view()->exists('admin.settings')) {
            return view('admin.settings');
        }

        // Prevent another error if the Blade view has not been created.
        return response()->view('admin.settings-placeholder', [
            'message' => 'The system settings page is not configured yet.',
        ], 200);
    }
    public function dashboard()
{
    $subscriptionPlans = \App\Models\SubscriptionPlan::all();
    $totalShops = \App\Models\Shop::count();

    $pendingShops = \App\Models\Shop::where('status', 'pending')
        ->count();

    $approvedShops = \App\Models\Shop::where('status', 'approved')
        ->count();

    $totalUsers = \App\Models\User::count();

    $unassignedUsers = \App\Models\User::whereNull('shop_id')
        ->count();

    $pendingShopsList = \App\Models\Shop::where('status', 'pending')
        ->latest()
        ->take(5)
        ->get();

    $recentShops = \App\Models\Shop::latest()
        ->take(5)
        ->get();

    return view('dashboard.system-admin', compact(
        'totalShops',
        'pendingShops',
        'approvedShops',
        'totalUsers',
        'unassignedUsers',
        'pendingShopsList',
        'recentShops',
        'subscriptionPlans'
    ));
}
 public function listUsers()
    {
        $users = User::query()->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // GET /admin/shops  (admin.shops.index)
    public function shopsIndex()
    {
        $shops = Shop::query()->latest()->paginate(20);

        return view('admin.shops.index', compact('shops'));
    }

    // GET /admin/shops/create  (admin.shops.create)
   

    // POST /admin/shops  (admin.shops.store), likely the next one you'll hit
    public function storeShop(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // add the other shop columns here
        ]);

        Shop::create($data);

        return redirect()->route('admin.shops.index')
            ->with('status', 'Shop created.');
    }
   

public function createShop()
{
    $subscriptionPlans = SubscriptionPlan::query()->orderBy('name')->get();

   $unassignedUsers = User::query()
    ->whereNull('shop_id')                 // keep whichever filter matched your schema
    ->where('role', '!=', 'system_admin')
    ->orderBy('name')
    ->get(['id', 'name', 'email', 'phone', 'country', 'city', 'address']);

    return view('admin.shops.create', compact('subscriptionPlans', 'unassignedUsers'));
}

public function approveShop(Shop $shop): RedirectResponse
{
    DB::transaction(function () use ($shop) {
        $shop->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    });

    return redirect()
        ->back()
        ->with('success', 'Shop approved successfully. Its users can now access shop data.');
}

}