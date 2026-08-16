<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    /**
     * Waiter dashboard.
     */
    public function dashboard(Request $request, Shop $shop)
    {
        $user = auth()->user();

        // Make sure the waiter belongs to this shop
        abort_unless(
            $user->shop_id === $shop->id,
            403
        );

        // Only waiters can access this dashboard
        abort_unless(
            $user->role === 'waiter',
            403
        );

        $orders = Order::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->with([
                'customer',
                'items',
            ])
            ->latest()
            ->paginate(20);

        $pendingOrders = Order::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedOrders = Order::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->where('status', 'approved')
            ->count();

        $completedOrders = Order::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->where('status', 'completed')
            ->count();

        $cancelledOrders = Order::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->where('status', 'cancelled')
            ->count();

        return view('waiter.dashboard', compact(
            'shop',
            'orders',
            'pendingOrders',
            'approvedOrders',
            'completedOrders',
            'cancelledOrders'
        ));
    }
}