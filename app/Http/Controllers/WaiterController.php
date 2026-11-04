<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\OrderNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WaiterController extends Controller
{
    /** Status changes a waiter may make: current status => allowed next statuses */
    private const WAITER_TRANSITIONS = [
        'pending'  => ['cancelled'],
        'approved' => ['completed'],
    ];

    public function __construct(private readonly OrderNumberGenerator $orderNumbers) {}

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
            ->with(['customer', 'waiter', 'server', 'items.product'])
            ->latest()
            ->paginate(15);

        $products = Product::where('shop_id', $shop->id)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => $this->productPayload($p))
            ->values();

        $customers = $this->customersFor($shop)->orderBy('name')->get(['id', 'name']);
        $staff = User::where('shop_id', $shop->id)->orderBy('name')->get(['id', 'name']);

        return view('waiter.waiter-dashboard', [
            'shop'            => $shop,
            'orders'          => $orders,
            'products'        => $products,
            'customers'       => $customers,
            'staff'           => $staff,
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
            'ordered_by'           => ['required', 'string', 'max:120'],
            'served_by'            => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('shop_id', $shop->id),
            ],
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
                $total += round($price * $qty, 2);

                $lines[] = [
                    'shop_id'         => $shop->id,
                    'product_id'      => $product->id,
                    'description'     => $product->name,
                    'quantity'        => $qty,
                    'unit_price'      => $price,
                    'discount_amount' => 0,
                    'tax_amount'      => 0,
                ];
            }

            if ($errors) {
                throw ValidationException::withMessages($errors);
            }

            $order = Order::create([
                'shop_id'      => $shop->id,
                'created_by'   => $user->id,
                'customer_id'  => $data['customer_id'] ?? null,
                'ordered_by'  => $data['ordered_by'],
                'served_by'   => $data['served_by'],
                'order_number' => $this->orderNumbers->generate($shop),
                'table_number' => $data['table_number'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'status'       => 'pending',
                'subtotal'     => round($total, 2),
                'total_amount' => round($total, 2),
            ]);

            $order->items()->createMany($lines);
            $order->load('items');
            $order->recalculateTotals();

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
        $order->load(['items.product', 'customer', 'waiter', 'server']);

        $total = (float) ($order->total_amount
            ?: $order->items->sum(fn ($item) => (float) ($item->line_total
                ?? ((float) $item->unit_price * (float) $item->quantity
                    - (float) $item->discount_amount
                    + (float) $item->tax_amount))));
        $orderNo = (string) ($order->order_number ?? $order->id);
        $verificationUrl = URL::signedRoute('documents.verify', ['uuid' => $order->ensureUuid()]);
        $qrMimeType = extension_loaded('imagick') ? 'png' : 'svg+xml';
        $qr = extension_loaded('imagick')
            ? QrCode::format('png')->size(200)->margin(1)->errorCorrection('M')->generate($verificationUrl)
            : QrCode::format('svg')->size(200)->margin(1)->errorCorrection('M')->generate($verificationUrl);

        return [
            'shop'   => $shop,
            'order'  => $order,
            'total'  => $total,
            'waiter' => $order->waiter?->name,
            'qr' => base64_encode($qr),
            'qrMimeType' => $qrMimeType,
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
            'image' => $product->image_url,
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

}