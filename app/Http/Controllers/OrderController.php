<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\RejectOrderRequest;
use App\Http\Requests\StoreOrderRequest;
 use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Services\OrderApprovalService;
use App\Services\OrderNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

    // Order-taking screen (waiters only).
    public function create(Shop $shop)
    {
        $user = auth()->user();

        if ($user->role !== 'waiter') {
            abort(403, 'Only waiters can take orders.');
        }

        $products = Product::where('shop_id', $shop->id)
            ->where('status', 'active')
            ->with('category:id,name')            // VERIFY: categories table has a `name` column
            ->orderBy('name')
            ->get();

        $catalogue = $products->map(fn ($p) => [
            'id'       => $p->id,
            'name'     => $p->name,
            'price'    => (float) $p->selling_price,
            'tracked'  => (bool) $p->track_stock,
            'stock'    => $p->track_stock ? (float) $p->stock : 999999,
            'category' => $p->category?->name,
            'barcode'  => trim(($p->barcode ?? '') . ' ' . ($p->sku ?? '')),
            'image'    => $this->imageUrl($p->product_image),
        ])->values();

        $categories = $catalogue->pluck('category')->filter()->unique()->sort()->values();

        $customers = Customer::where('shop_id', $shop->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $paymentMethods = $this->paymentMethods();

        $orders = Order::forShop($shop->id)
            ->byWaiter($user->id)
            ->latest()
            ->paginate(10);

        return view('orders.waiter', compact(
            'shop', 'catalogue', 'categories', 'customers','products', 'paymentMethods', 'orders'
        ));
    }

    public function show(Shop $shop, Order $order)
    {
        abort_unless($order->shop_id === $shop->id, 404);

        $order->load(['items.product', 'waiter', 'reviewer', 'customer', 'sale']);

        return view('orders.show', compact('shop', 'order'));
    }

    // Waiter submits a new order for approval. No Sale is created and no stock is deducted here.
    public function store(StoreOrderRequest $request, Shop $shop)
    {
        // One submission token = one order. Needs a real cache driver (file/redis/database, not "array").
        $tokenKey = 'order-submit:' . auth()->id() . ':' . $request->input('submission_token');
        if (! Cache::add($tokenKey, true, now()->addMinutes(15))) {
            return redirect()
                ->route('shops.orders.index', $shop)
                ->with('error', 'This order was already submitted.');
        }

        try {
            $order = DB::transaction(function () use ($request, $shop) {
                // Merge duplicate product lines.
                $wanted = collect($request->validated()['items'])
                    ->groupBy('product_id')
                    ->map(fn ($rows) => round($rows->sum(fn ($r) => (float) $r['quantity']), 2));

                // Lock the product rows so simultaneous orders see consistent stock.
                $products = Product::where('shop_id', $shop->id)
                    ->where('status', 'active')
                    ->whereIn('id', $wanted->keys())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $lines = [];
                foreach ($wanted as $productId => $qty) {
                    $product = $products->get($productId);

                    if (! $product) {
                        throw ValidationException::withMessages([
                            'items' => 'A selected product is no longer available.',
                        ]);
                    }
                    if ($product->track_stock && $qty > (float) $product->stock) {
                        throw ValidationException::withMessages([
                            'items' => "Only {$product->stock} of {$product->name} in stock.",
                        ]);
                    }

                    $lines[] = [
                        'shop_id'         => $shop->id,
                        'product_id'      => $product->id,
                        'description'     => $product->name,
                        'quantity'        => $qty,
                        'unit_price'      => $product->selling_price, // always from the database
                        'discount_amount' => 0,
                        'tax_amount'      => 0,
                    ];
                }

                $order = Order::create([
                    'shop_id'         => $shop->id,
                    'customer_id'     => $request->validated()['customer_id'] ?? null,
                    'created_by'      => auth()->id(),
                    'order_number'    => $this->orderNumbers->generate($shop),
                    'status'          => 'pending',
                    'payment_method'  => $request->validated()['payment_method'],
                    'payment_status'  => 'unpaid',
                    'discount_amount' => 0,
                    'tax_amount'      => 0,
                    'notes'           => $request->validated()['notes'] ?? null,
                ]);

                foreach ($lines as $line) {
                    $order->items()->create($line); // create() so OrderItem calculates line_total
                }

                $order->load('items');
                $order->recalculateTotals();

                return $order;
            });
        } catch (\Throwable $e) {
            Cache::forget($tokenKey); // let the waiter fix the problem and retry
            throw $e;
        }

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} submitted for approval.");
    }

    // Seller approves -> deducts stock, creates Sale + SaleItems.
    public function approve(Request $request, Shop $shop, Order $order)
{
    abort_unless($order->shop_id === $shop->id, 404);
    // TODO: replace with a proper OrderPolicy once you have one.
    abort_unless(in_array($request->user()->role, ['seller', 'admin'], true), 403);

    // NEW: only accept a known payment method
    $data = $request->validate([
        'payment_method' => ['nullable', Rule::in(array_keys($this->paymentMethods()))],
    ]);

    // NEW: use the seller's choice, else the order's own; refuse if neither exists
    $method = $data['payment_method'] ?? $order->payment_method;
    if (! $method) {
        return back()->withErrors(['payment_method' => 'Choose a payment method before approving.']);
    }

    try {
        $order = $this->approvals->approve($order, $request->user(), $method);   // CHANGED: $method
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
        abort_unless($order->shop_id === $shop->id, 404);

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
        abort_unless($order->shop_id === $shop->id, 404);
        $user = auth()->user();
        abort_if($user->role === 'waiter' && $order->created_by !== $user->id, 403);

        try {
            $order = $this->approvals->cancel($order);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} cancelled.");
    }

    // ── Helpers ────────────────────────────────────────────

    /** [value => label]; works whether Sale::PAYMENT_METHODS is a plain list or already keyed. */
    private function paymentMethods(): array
    {
        $m = Order::PAYMENT_METHODS;

        if (array_is_list($m)) {
            return collect($m)
                ->mapWithKeys(fn ($v) => [$v => ucfirst(str_replace('_', ' ', $v))])
                ->all();
        }

        return $m;
    }

    private function imageUrl(?string $path): ?string
    {
        if (! $path) return null;
        if (preg_match('#^https?://#i', $path)) return $path;

        return Storage::disk('public')->url($path); // needs `php artisan storage:link`
    }
    public function isFullyPaid(Order $order): bool
    {
        return (float) $order->amount_paid >= (float) $order->total_amount;
    }
    public function isPartiallyPaid(Order $order): bool
    {
        return (float) $order->amount_paid > 0 && (float) $order->amount_paid < (float) $order->total_amount;
    }
    public function isUnpaid(Order $order): bool
    {
        return (float) $order->amount_paid === 0;
    }
    public function isOverpaid(Order $order): bool
    {
        return (float) $order->amount_paid > (float) $order->total_amount;
    }
    public function waiterHasPendingOrders(Shop $shop, $waiterId): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where('status', 'pending')
            ->exists();
    }
    public function waiterHasUnpaidOrders(Shop $shop, $waiterId): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where('payment_status', 'unpaid')
            ->exists();
    }
    public function waiterHasPartiallyPaidOrders(Shop $shop, $waiterId): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where('payment_status', 'partially_paid')
            ->exists();
    }
    public function waiterHasOverpaidOrders(Shop $shop, $waiterId): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where('payment_status', 'overpaid')
            ->exists();
} public function waiterHasPaidOrders(Shop $shop, $waiterId): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where('payment_status', 'paid')
            ->exists();
    }
    public function waiterHasCancelledOrders(Shop $shop, $waiterId): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where('status', 'cancelled')
            ->exists();
    }
}