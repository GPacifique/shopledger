<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\RejectOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Shop;
use App\Services\OrderApprovalService;
use App\Services\OrderNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Product;
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderNumberGenerator $orderNumbers,
        private readonly OrderApprovalService $approvals,
    ) {}

    public function index(Shop $shop, Request $request)
    {
        $user = $request->user();

        // waiters only ever see their own orders; sellers/admins see the shop's orders
        // and land on the pending queue by default, since that's what needs their action
        $status = $request->status ?? ($user->role === 'waiter' ? null : 'pending');

        $orders = $shop->orders()
            ->with(['customer', 'waiter', 'items'])
            ->when($user->role === 'waiter', fn ($q) => $q->byWaiter($user->id))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('shop', 'orders', 'status'));
    }
public function create(Shop $shop)
{
    $user = auth()->user();

    // Only waiters can access the order-taking screen
    if ($user->role !== 'waiter') {
        abort(403, 'Only waiters can take orders.');
    }

    // Products available in this shop
    $products = Product::where('shop_id', $shop->id)
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    // Customers belonging to this shop
    $customers = Customer::where('shop_id', $shop->id)
        ->orderBy('name')
        ->get();

    // Orders created by this waiter
    $orders = Order::where('shop_id', $shop->id)
        ->where('created_by', $user->id)
        ->with(['customer', 'items.product'])
        ->latest()
        ->get();

    return view('orders.waiter', compact(
        'shop',
        'products',
        'customers',
        'orders'
    ));
}    public function show(Shop $shop, Order $order)
    {
        $order->load(['items.product', 'waiter', 'reviewer', 'customer', 'sale']);

        return view('orders.show', compact('shop', 'order'));
    }

    // Waiter submits a new order for approval.
    public function store(StoreOrderRequest $request, Shop $shop)
    {
        $order = DB::transaction(function () use ($request, $shop) {
            $order = Order::create([
                'shop_id' => $shop->id,
                'customer_id' => $request->customer_id,
                'created_by' => auth()->id(),
                'order_number' => $this->orderNumbers->generate($shop),
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'discount_amount' => $request->discount_amount ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $order->items()->create([
                    'shop_id' => $shop->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'description' => $itemData['description'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_amount' => $itemData['discount_amount'] ?? 0,
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                ]);
            }

            $order->load('items');
            $order->recalculateTotals();

            return $order;
        });

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} submitted for approval.");
    }

    // Seller approves -> deducts stock, creates Sale + SaleItems.
    public function approve(Request $request, Shop $shop, Order $order)
    {
        // TODO: replace with a proper OrderPolicy once you have one;
        // this is a minimal role check to get you moving.
        abort_unless(in_array($request->user()->role, ['seller', 'admin'], true), 403);

        try {
            $order = $this->approvals->approve($order, $request->user(), $request->input('payment_method'));
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} approved and recorded in sales.");
    }

    // Seller rejects, with a reason.
    public function reject(RejectOrderRequest $request, Shop $shop, Order $order)
    {
        try {
            $order = $this->approvals->reject($order, $request->user(), $request->reason);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} rejected.");
    }

    // Waiter withdraws their own order before it's reviewed.
    public function cancel(Shop $shop, Order $order)
    {
        try {
            $order = $this->approvals->cancel($order);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} cancelled.");
    }
}