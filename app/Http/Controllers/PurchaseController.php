<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Notifications\PurchaseDeleted;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $query = Purchase::with(['supplier', 'items', 'creator'])
            ->where('shop_id', $shopId);

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        $stats = [
            'total_purchases' => (clone $query)->count(),
            'total_amount'    => (clone $query)->sum('total_amount'),
            'total_items'     => (clone $query)->withCount('items')->get()->sum('items_count'),
            'avg_purchase'    => (clone $query)->avg('total_amount'),
        ];

        $purchases = $query->orderByDesc('purchase_date')->paginate(15)->withQueryString();
        $suppliers = Supplier::where('shop_id', $shopId)->orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'stats', 'suppliers'));
    }

    public function create(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $suppliers = Supplier::where('shop_id', $shopId)->orderBy('name')->get();
        $products = Product::where('shop_id', $shopId)->orderBy('name')->get();
        $selectedSupplier = $request->supplier ? Supplier::find($request->supplier) : null;

        return view('purchases.create', compact('suppliers', 'products', 'selectedSupplier'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'supplier_id' => $request->filled('supplier_id') ? $request->supplier_id : null,
        ]);

        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();
        $shopId = $user->shop_id;

        $purchase = DB::transaction(function () use ($request, $user, $shopId) {
            $purchase = Purchase::create([
                'shop_id' => $shopId,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'created_by' => $user->id,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])->where('shop_id', $shopId)->firstOrFail();
                $line = $item['quantity'] * $item['unit_cost'];
                PurchaseItem::create([
                    'shop_id' => $shopId,
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $line,
                ]);

                // Update buying price
                $product->buying_price = $item['unit_cost'];
                $product->save();

                // Record stock movement (auto-creates audit trail)
                $this->stockService->recordPurchase(
                    $product,
                    (float) $item['quantity'],
                    $purchase->id,
                    $user->id
                );

                $total += $line;
            }

            $purchase->total_amount = $total;
            $purchase->save();

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase recorded successfully. Stock has been updated.');
    }

    public function show(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $purchase->load(['supplier', 'items.product', 'creator']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the edit form, pre-filled with the purchase's saved data.
     */
    public function edit(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $this->authorizePurchaseManage($request, $purchase);

        // Scope dropdowns to the purchase's own shop (matters for system admins
        // who may edit a purchase belonging to another shop).
        $shopId = $purchase->shop_id;

        $purchase->load(['supplier', 'items.product']);
        $suppliers = Supplier::where('shop_id', $shopId)->orderBy('name')->get();
        $products = Product::where('shop_id', $shopId)->orderBy('name')->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    /**
     * Update a purchase and adjust stock by the difference only.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $this->authorizePurchaseManage($request, $purchase);

        $request->merge([
            'supplier_id' => $request->filled('supplier_id') ? $request->supplier_id : null,
        ]);

        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();
        $shopId = $purchase->shop_id;

        DB::transaction(function () use ($request, $purchase, $user, $shopId) {
            // Lock the row so two edits can't race each other
            $purchase = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            // Old vs new quantity per product (so we only move the difference)
            $oldQty = $purchase->items()->get()
                ->groupBy('product_id')
                ->map(fn ($rows) => (float) $rows->sum('quantity'));

            $newQty = collect($request->items)
                ->groupBy('product_id')
                ->map(fn ($rows) => (float) $rows->sum(fn ($r) => (float) $r['quantity']));

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
            ]);

            // Replace the line items
            $purchase->items()->delete();

            $total = 0;
            $products = [];
            $latestCost = [];

            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();

                $line = $item['quantity'] * $item['unit_cost'];

                PurchaseItem::create([
                    'shop_id' => $shopId,
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $line,
                ]);

                $products[$product->id] = $product;
                $latestCost[$product->id] = $item['unit_cost'];
                $total += $line;
            }

            // Stock adjustment: new quantity minus old quantity, per product
            $productIds = $oldQty->keys()->merge($newQty->keys())->unique();

            foreach ($productIds as $productId) {
                $delta = (float) ($newQty[$productId] ?? 0) - (float) ($oldQty[$productId] ?? 0);

                if (abs($delta) < 0.0001) {
                    continue;
                }

                $product = $products[$productId]
                    ?? Product::where('id', $productId)->where('shop_id', $shopId)->firstOrFail();

                if ($delta > 0) {
                    $this->stockService->recordPurchase($product, $delta, $purchase->id, $user->id);
                } else {
                    // Same reversal approach destroy() uses
                    $this->stockService->recordMovement(
                        $product,
                        StockMovement::TYPE_SALE,
                        abs($delta),
                        'App\Models\Purchase',
                        $purchase->id,
                        'Purchase #' . $purchase->id . ' edited - stock reduced',
                        $user->id
                    );
                }
            }

            // Only touch the buying price if no newer purchase exists for the product,
            // so editing an old purchase doesn't overwrite a more recent cost.
            foreach ($latestCost as $productId => $cost) {
                $date = $purchase->purchase_date->toDateString();

                $hasNewer = Purchase::where('shop_id', $shopId)
                    ->where('id', '!=', $purchase->id)
                    ->whereHas('items', fn ($q) => $q->where('product_id', $productId))
                    ->where(function ($q) use ($date, $purchase) {
                        $q->whereDate('purchase_date', '>', $date)
                          ->orWhere(function ($q2) use ($date, $purchase) {
                              $q2->whereDate('purchase_date', $date)->where('id', '>', $purchase->id);
                          });
                    })
                    ->exists();

                if (! $hasNewer) {
                    $products[$productId]->buying_price = $cost;
                    $products[$productId]->save();
                }
            }

            $purchase->total_amount = $total;
            $purchase->save();
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully. Stock has been adjusted.');
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $this->authorizePurchaseManage($request, $purchase);

        // Snapshot before it's gone
        $purchaseId  = $purchase->id;
        $totalAmount = $purchase->total_amount;
        $shopId      = $purchase->shop_id;
        $deletedBy   = $request->user()->name;
        $user        = $request->user();

        DB::transaction(function () use ($purchase, $user) {
            // Reverse stock changes with movement tracking
            foreach ($purchase->items as $item) {
                // Use TYPE_SALE to reverse the stock increase from purchase
                $this->stockService->recordMovement(
                    $item->product,
                    StockMovement::TYPE_SALE,
                    $item->quantity,
                    'App\Models\Purchase',
                    $purchase->id,
                    'Purchase deleted - stock reversal',
                    $user->id
                );
            }
            $purchase->items()->delete();
            $purchase->delete();
        });

        $this->notifyShopAdmins($shopId, new PurchaseDeleted($purchaseId, $totalAmount, $deletedBy), $request->user()->id);

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted and stock reversed.');
    }

    protected function authorizePurchase(Request $request, Purchase $purchase): void
    {
        if ($purchase->shop_id !== $request->user()->shop_id && ! $request->user()->isSystemAdmin()) {
            abort(403);
        }
    }

    protected function authorizePurchaseManage(Request $request, Purchase $purchase): void
    {
        if (! $request->user()->isSystemAdmin() && ! $request->user()->isShopAdmin()) {
            abort(403);
        }
    }

    public function downloadPdf(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $purchase->load(['supplier', 'items.product', 'creator', 'shop']);

        $pdf = Pdf::loadView('purchases.pdf', compact('purchase'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('purchase-note-' . $purchase->id . '.pdf');
    }
}