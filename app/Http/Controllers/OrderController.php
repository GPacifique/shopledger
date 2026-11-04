<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\RejectOrderRequest;
use App\Http\Requests\StoreOrderRequest;
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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /** Roles that can see every order of their shop and approve/reject them. */
    private const REVIEWER_ROLES = ['seller', 'admin'];

    /** Real order statuses (the "all" tab is not a status). */
    private const STATUSES = ['pending', 'approved', 'rejected', 'cancelled'];

    public function __construct(
        private readonly OrderNumberGenerator $orderNumbers,
        private readonly OrderApprovalService $approvals,
    ) {}

    // ── Listing ────────────────────────────────────────────

    public function index(Shop $shop, Request $request)
    {
        $user = $request->user();
        $this->authorizeShopAccess($user, $shop);

        $isWaiter = $user->role === 'waiter';

        // ?status=all        -> no filter
        // ?status=<real one> -> that status
        // missing / unknown  -> reviewers land on the pending queue, waiters see everything of theirs
        $requested = $request->query('status');

        if ($requested === 'all') {
            $status = null;
        } elseif (in_array($requested, self::STATUSES, true)) {
            $status = $requested;
        } else {
            $status = $isWaiter ? null : 'pending';
        }

        $orders = $shop->orders()
            ->with(['customer', 'waiter', 'items'])
            // Waiters only ever see their own orders.
            // Sellers and shop admins see every order of this shop.
            ->when($isWaiter, fn ($q) => $q->where('created_by', $user->id))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('orders.index', compact('shop', 'orders', 'status'));
    }

    public function notifications(Request $request, Shop $shop)
    {
        $user = $request->user();
        $this->authorizeShopAccess($user, $shop);

        $orders = Order::query()
            ->where('shop_id', $shop->id)
            ->where('status', 'pending')
            ->when($user->role === 'waiter', fn ($q) => $q->where('created_by', $user->id))
            ->with('waiter:id,name')
            ->latest()
            ->limit(20)
            ->get(['id', 'order_number', 'created_by', 'created_at'])
            ->map(fn (Order $order) => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'waiter'       => $order->waiter?->name,
            ])
            ->values();

        return response()->json(['orders' => $orders]);
    }

    // ── Order-taking screen (waiters only) ─────────────────

    public function create(Shop $shop)
    {
        $user = auth()->user();
        $this->authorizeShopAccess($user, $shop);

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
            'shop', 'catalogue', 'categories', 'customers', 'products', 'paymentMethods', 'orders'
        ));
    }

    public function show(Shop $shop, Order $order)
    {
        $user = auth()->user();
        $this->authorizeShopAccess($user, $shop);
        abort_unless((int) $order->shop_id === (int) $shop->id, 404);

        // A waiter may only open their own orders.
        abort_if($user->role === 'waiter' && (int) $order->created_by !== (int) $user->id, 403);

        $order->load(['items.product', 'waiter', 'reviewer', 'customer', 'sale']);

        return view('orders.show', compact('shop', 'order'));
    }

    // ── Waiter submits a new order for approval ────────────
    // No Sale is created and no stock is deducted here.

    public function store(StoreOrderRequest $request, Shop $shop)
    {
        $user = $request->user();
        $this->authorizeShopAccess($user, $shop);
        abort_unless($user->role === 'waiter', 403, 'Only waiters can take orders.');

        // One submission token = one order. Needs a real cache driver (file/redis/database, not "array").
        $tokenKey = 'order-submit:' . $user->id . ':' . $request->input('submission_token');
        if (! Cache::add($tokenKey, true, now()->addMinutes(15))) {
            return redirect()
                ->route('shops.orders.index', $shop)
                ->with('error', 'This order was already submitted.');
        }

        try {
            $order = DB::transaction(function () use ($request, $shop, $user) {
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
                    'created_by'      => $user->id,
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

    // ── Review (seller / shop admin) ───────────────────────

    // Approve -> deducts stock, creates Sale + SaleItems.
    public function approve(Request $request, Shop $shop, Order $order)
    {
        $user = $request->user();
        $this->authorizeShopAccess($user, $shop);
        abort_unless((int) $order->shop_id === (int) $shop->id, 404);
        // TODO: replace with a proper OrderPolicy once you have one.
        abort_unless(in_array($user->role, self::REVIEWER_ROLES, true), 403);

        // Only accept a known payment method.
        $data = $request->validate([
            'payment_method' => ['nullable', Rule::in(array_keys($this->paymentMethods()))],
        ]);

        // Use the reviewer's choice, else the order's own; refuse if neither exists.
        $method = $data['payment_method'] ?? $order->payment_method;
        if (! $method) {
            return back()->withErrors(['payment_method' => 'Choose a payment method before approving.']);
        }

        try {
            $order = $this->approvals->approve($order, $user, $method);
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()
            ->route('shops.orders.index', $shop)
            ->with('success', "Order {$order->order_number} approved and recorded in sales.");
    }

    // Reject, with a reason.
    public function reject(RejectOrderRequest $request, Shop $shop, Order $order)
    {
        $user = $request->user();
        $this->authorizeShopAccess($user, $shop);
        abort_unless((int) $order->shop_id === (int) $shop->id, 404);
        abort_unless(in_array($user->role, self::REVIEWER_ROLES, true), 403);

        try {
            $order = $this->approvals->reject($order, $user, $request->reason);
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
        $user = auth()->user();
        $this->authorizeShopAccess($user, $shop);
        abort_unless((int) $order->shop_id === (int) $shop->id, 404);
        abort_if($user->role === 'waiter' && (int) $order->created_by !== (int) $user->id, 403);

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

    /**
     * The signed-in user must belong to this shop. Without this check, a seller or
     * admin of one shop could open another shop's orders just by changing the URL.
     * If your shop admins are linked differently (e.g. $shop->owner_id), adjust here.
     */
    private function authorizeShopAccess($user, Shop $shop): void
    {
        abort_unless($user && (int) $user->shop_id === (int) $shop->id, 403);
    }

    /** [value => label]; works whether Order::PAYMENT_METHODS is a plain list or already keyed. */
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

    // ── Payment / waiter helpers ───────────────────────────
    // These are not routes; they fit better on the Order model, but are kept here unchanged.

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
        // Was `=== 0`, which is never true for a float.
        return (float) $order->amount_paid <= 0;
    }

    public function isOverpaid(Order $order): bool
    {
        return (float) $order->amount_paid > (float) $order->total_amount;
    }

    public function waiterHasPendingOrders(Shop $shop, $waiterId): bool
    {
        return $this->waiterHasOrders($shop, $waiterId, 'status', 'pending');
    }

    public function waiterHasUnpaidOrders(Shop $shop, $waiterId): bool
    {
        return $this->waiterHasOrders($shop, $waiterId, 'payment_status', 'unpaid');
    }

    public function waiterHasPartiallyPaidOrders(Shop $shop, $waiterId): bool
    {
        return $this->waiterHasOrders($shop, $waiterId, 'payment_status', 'partially_paid');
    }

    public function waiterHasOverpaidOrders(Shop $shop, $waiterId): bool
    {
        return $this->waiterHasOrders($shop, $waiterId, 'payment_status', 'overpaid');
    }

    public function waiterHasPaidOrders(Shop $shop, $waiterId): bool
    {
        return $this->waiterHasOrders($shop, $waiterId, 'payment_status', 'paid');
    }

    public function waiterHasCancelledOrders(Shop $shop, $waiterId): bool
    {
        return $this->waiterHasOrders($shop, $waiterId, 'status', 'cancelled');
    }

    private function waiterHasOrders(Shop $shop, $waiterId, string $column, string $value): bool
    {
        return Order::where('shop_id', $shop->id)
            ->where('created_by', $waiterId)
            ->where($column, $value)
            ->exists();
    }
}