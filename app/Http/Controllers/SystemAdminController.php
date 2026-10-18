<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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



public function editUser(User $user)
{
    $shops = Shop::orderBy('business_name')->get();

    return view('admin.users.edit', compact('user', 'shops'));
}

public function updateUser(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
        'password' => ['nullable', 'string', 'min:8'],
        'role' => [
            'required',
            Rule::in([
                'system_admin',
                'business_admin',
                'shop_admin',
                'seller',
                'waiter',
                'user',
            ]),
        ],
        'shop_id' => ['nullable', 'integer', 'exists:shops,id'],
        'account_status' => [
            'required',
            Rule::in(['active', 'suspended']),
        ],
        'locale' => ['required', Rule::in(['en', 'fr', 'rw', 'sw'])],
    ]);

    // Update the user's basic information.
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];
    $user->shop_id = $validated['shop_id'] ?? null;
    $user->account_status = $validated['account_status'];
    $user->locale = $validated['locale'];

    // Only change the password when one was entered.
    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User updated successfully.');
}

}