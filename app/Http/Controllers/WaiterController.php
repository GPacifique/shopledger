<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WaiterController extends Controller
{
    /** Product images live in public/Images/products/ */
    private const IMAGE_DIR = 'Images/products';

    /** Status changes a waiter may make: current status => allowed next statuses */
    private const WAITER_TRANSITIONS = [
        'pending'  => ['cancelled'],
        'approved' => ['completed'],
    ];

    /* ------------------------------------------------------------------
     | Dashboard
     ------------------------------------------------------------------ */
    public function dashboard(Request $request, Shop $shop)
    {
        $user = $this->authorizeWaiter($shop);

        $mine = fn () => Order::where('shop_id', $shop->id)->where('created_by', $user->id);

        // One query instead of four
        $statusCounts = $mine()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $orders = $mine()
            ->with(['customer', 'items.product'])
            ->latest()
            ->paginate(15);

        $products = Product::where('shop_id', $shop->id)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => $this->productPayload($p))
            ->values();

        $customers = $this->customersFor($shop)->orderBy('name')->get(['id', 'name']);

        return view('waiter.waiter-dashboard', [
            'shop'            => $shop,
            'orders'          => $orders,
            'products'        => $products,
            'customers'       => $customers,
            'pendingOrders'   => (int) ($statusCounts['pending'] ?? 0),
            'approvedOrders'  => (int) ($statusCounts['approved'] ?? 0),
            'completedOrders' => (int) ($statusCounts['completed'] ?? 0),
            'cancelledOrders' => (int) ($statusCounts['cancelled'] ?? 0),
        ]);
    }

    /* ------------------------------------------------------------------
     | Place an order
     ------------------------------------------------------------------ */
    public function storeOrder(Request $request, Shop $shop): RedirectResponse
    {
        $user = $this->authorizeWaiter($shop);

        $data = $request->validate([
            'customer_id'          => ['nullable', 'integer'],
            'table_number'         => ['nullable', 'string', 'max:20'],
            'notes'                => ['nullable', 'string', 'max:500'],
            'items'                => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id'   => [
                'required', 'integer', 'distinct',
                Rule::exists('products', 'id')->where('shop_id', $shop->id),
            ],
            'items.*.quantity'     => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'items.required'             => 'Add at least one product to the order.',
            'items.*.product_id.exists'  => 'One of the products is not available in this shop.',
        ]);

        if (! empty($data['customer_id'])
            && ! $this->customersFor($shop)->whereKey($data['customer_id'])->exists()) {
            throw ValidationException::withMessages(['customer_id' => 'That customer does not belong to this shop.']);
        }

        $order = DB::transaction(function () use ($data, $shop, $user) {
            // Lock the rows so two waiters cannot sell the last unit twice
            $products = Product::where('shop_id', $shop->id)
                ->whereIn('id', collect($data['items'])->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lines  = [];
            $total  = 0.0;
            $errors = [];

            foreach ($data['items'] as $i => $line) {
                $product = $products[$line['product_id']];
                $qty     = (int) $line['quantity'];
                $stock   = $this->stockOf($product);

                if ($stock !== null && $qty > $stock) {
                    $errors["items.$i.quantity"] = "{$product->name}: only {$stock} left in stock.";
                    continue;
                }

                // Price always comes from the database, never from the browser
                $price    = $this->unitPrice($product);
                $subtotal = round($price * $qty, 2);
                $total   += $subtotal;

                $lines[] = [
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ];
            }

            if ($errors) {
                throw ValidationException::withMessages($errors);
            }

            $order = Order::create([
                'shop_id'      => $shop->id,
                'created_by'   => $user->id,
                'customer_id'  => $data['customer_id'] ?? null,
                'table_number' => $data['table_number'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'status'       => 'pending',
                'total_amount' => round($total, 2),
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        return back()
            ->with('success', 'Order #' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) . ' placed.')
            ->with('new_order', $order->id);
    }

    /* ------------------------------------------------------------------
     | Cancel / complete
     ------------------------------------------------------------------ */
    public function updateStatus(Request $request, Shop $shop, Order $order): RedirectResponse
    {
        $user = $this->authorizeWaiter($shop);
        $this->authorizeOrderAccess($shop, $order);

        $request->validate(['status' => ['required', Rule::in(['cancelled', 'completed'])]]);
        $next = $request->input('status');

        $allowed = self::WAITER_TRANSITIONS[$order->status] ?? [];

        if (! in_array($next, $allowed, true)) {
            return back()->withErrors([
                'status' => "A {$order->status} order cannot be marked {$next}.",
            ]);
        }

        $order->update(['status' => $next]);

        return back()->with('success', 'Order #' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) . " marked {$next}.");
    }

    /* ------------------------------------------------------------------
     | Bill (A4 PDF download) and receipt (80 mm PDF, opens in a new tab)
     ------------------------------------------------------------------ */
    public function downloadBill(Shop $shop, Order $order)
    {
        $this->authorizeOrderAccess($shop, $order);

        return Pdf::loadView('waiter.pdf.bill', $this->pdfData($shop, $order))
            ->setPaper('a4')
            ->download("bill-order-{$order->id}.pdf");
    }

    public function printOrder(Shop $shop, Order $order)
    {
        $this->authorizeOrderAccess($shop, $order);

        $data   = $this->pdfData($shop, $order);
        $height = 360 + ($order->items->count() * 34);   // points; grows with the item count

        return Pdf::loadView('waiter.pdf.order', $data)
            ->setPaper([0, 0, 226.77, $height])           // 80 mm wide
            ->stream("receipt-order-{$order->id}.pdf");
    }

    /* ------------------------------------------------------------------
     | Helpers
     ------------------------------------------------------------------ */

    /** Waiter dashboard access: member of this shop AND role = waiter. */
    private function authorizeWaiter(Shop $shop)
    {
        $user = auth()->user();

        abort_unless($user && (int) $user->shop_id === (int) $shop->id, 403);
        abort_unless($user->role === 'waiter', 403);

        return $user;
    }

    /**
     * Order access: the order must belong to the shop in the URL, the user must
     * belong to that shop, and waiters may only touch their own orders.
     */
    private function authorizeOrderAccess(Shop $shop, Order $order): void
    {
        $user = auth()->user();

        abort_unless($user && (int) $user->shop_id === (int) $shop->id, 403);
        abort_unless((int) $order->shop_id === (int) $shop->id, 404);
        abort_if($user->role === 'waiter' && (int) $order->created_by !== (int) $user->id, 403);
    }

    private function pdfData(Shop $shop, Order $order): array
    {
        $order->load(['items.product', 'customer']);

        $total = (float) ($order->total_amount
            ?: $order->items->sum(fn ($i) => (float) $i->quantity * (float) $i->price));

        return [
            'shop'   => $shop,
            'order'  => $order,
            'total'  => $total,
            'waiter' => User::find($order->created_by)?->name,
        ];
    }

    private function customersFor(Shop $shop)
    {
        return Customer::query()->when(
            Schema::hasColumn('customers', 'shop_id'),
            fn ($q) => $q->where('shop_id', $shop->id)
        );
    }

    /** Plain array for the front end: id, name, price, stock (null = not tracked), image url */
    private function productPayload(Product $product): array
    {
        return [
            'id'    => $product->id,
            'name'  => $product->name,
            'price' => $this->unitPrice($product),
            'stock' => $this->stockOf($product),
            'image' => $this->imageUrl($product->getAttributes()['image'] ?? null),
        ];
    }

    /** Uses selling_price when the column exists, otherwise price. */
    private function unitPrice(Product $product): float
    {
        $a = $product->getAttributes();

        return (float) ($a['selling_price'] ?? $a['price'] ?? 0);
    }

    private function stockOf(Product $product): ?int
    {
        $a = $product->getAttributes();

        return isset($a['stock']) ? (int) $a['stock'] : null;
    }

    /**
     * Accepts "pizza.jpg", "Images/products/pizza.jpg" or a full URL.
     * Returns null when the file is missing so the page shows a placeholder.
     */
    private function imageUrl(?string $file): ?string
    {
        if (! $file) {
            return null;
        }

        if (Str::startsWith($file, ['http://', 'https://'])) {
            return $file;
        }

        $file = ltrim($file, '/');
        $path = Str::startsWith($file, self::IMAGE_DIR) ? $file : self::IMAGE_DIR . '/' . $file;

        return file_exists(public_path($path)) ? asset($path) : null;
    }
}