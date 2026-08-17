<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Notifications\ProductDeleted;
use App\Notifications\ProductUpdated;
use App\Models\StockMovement;
use chillerlan\QRCode\QRCode;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;

        $query = Product::where('shop_id', $shopId);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock', '<=', 'minimum_stock')
                    ->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'in') {
                $query->whereColumn('stock', '>', 'minimum_stock');
            }
        }

        $statsQuery = clone $query;

        $totalProducts = (clone $statsQuery)->count();

        $totalStockUnits = (clone $statsQuery)->sum('stock');

        $stockValueCost = (clone $statsQuery)
            ->sum(DB::raw('stock * buying_price'));

        $stockValueRetail = (clone $statsQuery)
            ->sum(DB::raw('stock * selling_price'));

        $lowStockCount = (clone $statsQuery)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->count();

        $outOfStockCount = (clone $statsQuery)
            ->where('stock', '<=', 0)
            ->count();

        $expiringSoonCount = (clone $statsQuery)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [
                now(),
                now()->addDays(30),
            ])
            ->count();

        $products = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact(
            'products',
            'totalProducts',
            'totalStockUnits',
            'stockValueCost',
            'stockValueRetail',
            'lowStockCount',
            'outOfStockCount',
            'expiringSoonCount'
        ));
    }

    public function create()
    {
        $shopId = request()->user()->shop_id;

        return view('products.create', [
            'categories' => Category::where('shop_id', $shopId)
                ->orderBy('name')
                ->get(),

            'suppliers' => Supplier::where('shop_id', $shopId)
                ->orderBy('name')
                ->get(),
        ]);
    }

   public function store(Request $request)
{
    $shopId = $request->user()->shop_id;

    $validated = $request->validate([
        'sku' => [
            'required',
            'string',
            'max:100',
            Rule::unique('products', 'sku')
                ->where('shop_id', $shopId),
        ],

        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('products', 'name')
                ->where('shop_id', $shopId),
        ],

        'description' => 'nullable|string',

        'barcode' => [
            'nullable',
            'string',
            'max:255',
            Rule::unique('products', 'barcode')
                ->where('shop_id', $shopId),
        ],

        'buying_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',

        /*
        |--------------------------------------------------------------------------
        | Opening Stock
        |--------------------------------------------------------------------------
        |
        | This is the physical stock already available when the product
        | is introduced into MahWi POS.
        |
        */
        'opening_stock' => 'nullable|numeric|min:0',
        'opening_unit_cost' => 'nullable|numeric|min:0',
        'opening_stock_date' => 'nullable|date',

        'minimum_stock' => 'nullable|numeric|min:0',

        'expiry_date' => 'nullable|date',

        'category_id' => [
            'required',
            Rule::exists('categories', 'id')
                ->where('shop_id', $shopId),
        ],

        'supplier_id' => [
            'nullable',
            Rule::exists('suppliers', 'id')
                ->where('shop_id', $shopId),
        ],

        'status' => 'nullable|in:active,inactive',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Opening Stock Defaults
    |--------------------------------------------------------------------------
    */

    $openingStock = (float) ($validated['opening_stock'] ?? 0);

    /*
    | If no separate opening cost is entered, use the product buying price.
    |
    | This is useful when the user enters:
    |
    | Buying Price: 2,500
    | Opening Stock: 100
    |
    | The opening inventory value becomes:
    |
    | 100 × 2,500 = 250,000
    */
    $openingUnitCost = (float) (
        $validated['opening_unit_cost']
        ?? $validated['buying_price']
    );

    $openingStockDate = $validated['opening_stock_date'] ?? now();

    /*
    |--------------------------------------------------------------------------
    | Create Product + Opening Stock Movement Atomically
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use (
        &$validated,
        $shopId,
        $openingStock,
        $openingUnitCost,
        $openingStockDate,
        $request
    ) {

        /*
        |--------------------------------------------------------------------------
        | Product
        |--------------------------------------------------------------------------
        */

        $product = Product::create([
            'shop_id' => $shopId,

            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'supplier_id' => $validated['supplier_id'] ?? null,

            'barcode' => $validated['barcode'] ?? null,
            'description' => $validated['description'] ?? null,

            'buying_price' => $validated['buying_price'],
            'selling_price' => $validated['selling_price'],

            /*
            | Opening stock becomes the initial stock.
            */
            'quantity' => $openingStock,
            'stock' => $openingStock,

            'minimum_stock' => $validated['minimum_stock'] ?? 0,

            'expiry_date' => $validated['expiry_date'] ?? null,

            'status' => $validated['status'] ?? 'active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Opening Stock Movement
        |--------------------------------------------------------------------------
        |
        | NEVER silently put opening stock into products.stock.
        |
        | Every initial stock quantity must have an audit record.
        |
        */

        if ($openingStock > 0) {

            $openingTotalCost = round(
                $openingStock * $openingUnitCost,
                2
            );

            StockMovement::create([
                'shop_id' => $shopId,
                'product_id' => $product->id,

                'type' => 'opening',

                'reference_type' => 'product',
                'reference_id' => $product->id,

                'quantity_change' => $openingStock,

                'quantity_after' => $openingStock,

                'unit_cost' => $openingUnitCost,

                'total_cost' => $openingTotalCost,

                'movement_date' => $openingStockDate,

                'created_by' => $request->user()->id,

                'note' => 'Opening stock created when product was added.',
            ]);
        }
    });

    return redirect()
        ->route('products.index')
        ->with(
            'success',
            'Product created successfully with opening stock recorded.'
        );
}
    public function show(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        /*
        |--------------------------------------------------------------------------
        | Product Shop
        |--------------------------------------------------------------------------
        */

        $shopId = $product->shop_id;

        $product->load([
            'category',
            'supplier',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Purchase History
        |--------------------------------------------------------------------------
        */

        $purchaseItems = PurchaseItem::with([
                'purchase.supplier',
                'purchase.creator',
            ])
            ->where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(10, ['*'], 'purchases');

        /*
        |--------------------------------------------------------------------------
        | Sales History
        |--------------------------------------------------------------------------
        */

        $saleItems = SaleItem::with([
                'sale.customer',
                'sale.creator',
            ])
            ->where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(10, ['*'], 'sales');

        /*
        |--------------------------------------------------------------------------
        | Purchase Statistics
        |--------------------------------------------------------------------------
        */

        $totalPurchased = PurchaseItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->sum('quantity');

        $totalPurchaseCost = PurchaseItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->sum('line_total');

        /*
        |--------------------------------------------------------------------------
        | Sales & Profit Statistics
        |--------------------------------------------------------------------------
        |
        | Revenue and profit are computed from ONE query so they always
        | share the same basis (line_total) — previously totalSales used
        | line_total while grossProfit was derived from unit_price * qty,
        | so the two figures could silently disagree whenever line_total
        | included a discount, tax, or rounding adjustment.
        |
        | cost_price_at_sale can be NULL for legacy or bad rows. SQL's
        | SUM() skips NULLs rather than treating them as 0, which used to
        | quietly drop those units from the cost side of the profit calc
        | (while still counting them in totalSold) — understating cost
        | and overstating profit with no visible warning. We COALESCE the
        | missing cost down to the product's current buying_price as a
        | fallback, and separately count how many rows needed it so the
        | estimate is visible instead of silent.
        |
        | Gross Profit = SUM(line_total) - SUM(effective_cost * quantity)
        */

        $saleStats = SaleItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->selectRaw('
                COALESCE(SUM(quantity), 0) AS total_sold,
                COALESCE(SUM(line_total), 0) AS total_sales,
                COALESCE(
                    SUM(
                        line_total
                        - (COALESCE(cost_price_at_sale, ?) * quantity)
                    ),
                    0
                ) AS gross_profit,
                SUM(CASE WHEN cost_price_at_sale IS NULL THEN 1 ELSE 0 END) AS missing_cost_rows
            ', [$product->buying_price])
            ->first();

        $totalSold = $saleStats->total_sold;
        $totalSales = $saleStats->total_sales;
        $grossProfit = $saleStats->gross_profit;
        $missingCostRows = $saleStats->missing_cost_rows;

        /*
        |--------------------------------------------------------------------------
        | Last Purchase
        |--------------------------------------------------------------------------
        */

        $lastPurchase = PurchaseItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Last Sale
        |--------------------------------------------------------------------------
        */

        $lastSale = SaleItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->first();

        return view('products.show', compact(
            'product',
            'purchaseItems',
            'saleItems',
            'totalPurchased',
            'totalPurchaseCost',
            'totalSold',
            'totalSales',
            'grossProfit',
            'missingCostRows',
            'lastPurchase',
            'lastSale'
        ));
    }

    public function edit(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);
        $this->authorizeProductManage($request, $product);

        $shopId = $request->user()->shop_id;

        return view('products.edit', [
            'product' => $product,

            'categories' => Category::where('shop_id', $shopId)
                ->orderBy('name')
                ->get(),

            'suppliers' => Supplier::where('shop_id', $shopId)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);
        $this->authorizeProductManage($request, $product);

        $shopId = $product->shop_id;

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')
                    ->where('shop_id', $shopId)
                    ->ignore($product->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')
                    ->where('shop_id', $shopId)
                    ->ignore($product->id),
            ],

            'description' => 'nullable|string',

            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')
                    ->where('shop_id', $shopId)
                    ->ignore($product->id),
            ],

            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',

            'quantity' => 'nullable|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',

            'expiry_date' => 'nullable|date',

            'category_id' => 'required|exists:categories,id',

            'supplier_id' => 'nullable|exists:suppliers,id',

            'status' => 'nullable|in:active,inactive',

            // Optional reason for a manual stock/cost change, so the
            // audit trail records *why*, not just *what* changed.
            'adjustment_note' => 'nullable|string|max:255',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Manual Stock / Cost Adjustment Audit Trail
        |--------------------------------------------------------------------------
        |
        | store() never lets opening stock land in products.stock without
        | a StockMovement record. update() previously let stock and
        | buying_price be edited directly with no audit trail at all —
        | so products.stock could silently drift from the movement log,
        | and stockValueCost in index() (stock * buying_price) would be
        | retroactively re-valued using the NEW price for stock that was
        | actually bought at the OLD price, with no record of the change.
        |
        | We snapshot the "before" state, apply the update, then log a
        | movement for any actual stock delta and a separate note if the
        | buying price changed — inside the same transaction so the
        | product row and its audit trail can't get out of sync.
        */

        $stockBefore = (float) $product->stock;
        $buyingPriceBefore = (float) $product->buying_price;

        DB::transaction(function () use (
            $product,
            $validated,
            $request,
            $shopId,
            $stockBefore,
            $buyingPriceBefore
        ) {
            $product->update($validated);

            $stockAfter = (float) $validated['stock'];
            $buyingPriceAfter = (float) $validated['buying_price'];
            $stockDelta = $stockAfter - $stockBefore;

            if (abs($stockDelta) > 0.0001) {
                StockMovement::create([
                    'shop_id' => $shopId,
                    'product_id' => $product->id,

                    'type' => 'adjustment',

                    'reference_type' => 'product',
                    'reference_id' => $product->id,

                    'quantity_change' => $stockDelta,
                    'quantity_after' => $stockAfter,

                    'unit_cost' => $buyingPriceAfter,
                    'total_cost' => round(abs($stockDelta) * $buyingPriceAfter, 2),

                    'movement_date' => now(),

                    'created_by' => $request->user()->id,

                    'note' => $validated['adjustment_note']
                        ?? 'Manual stock adjustment via product edit.',
                ]);
            }

            if (abs($buyingPriceAfter - $buyingPriceBefore) > 0.0001) {
                StockMovement::create([
                    'shop_id' => $shopId,
                    'product_id' => $product->id,

                    'type' => 'cost_revision',

                    'reference_type' => 'product',
                    'reference_id' => $product->id,

                    'quantity_change' => 0,
                    'quantity_after' => $stockAfter,

                    'unit_cost' => $buyingPriceAfter,
                    'total_cost' => 0,

                    'movement_date' => now(),

                    'created_by' => $request->user()->id,

                    'note' => sprintf(
                        'Buying price changed from %s to %s. Note: existing stock valuation now uses the new price (no per-batch cost tracking).',
                        $buyingPriceBefore,
                        $buyingPriceAfter
                    ),
                ]);
            }
        });

        $this->notifyShopAdmins(
            $product->shop_id,
            new ProductUpdated(
                $product->id,
                $product->name,
                $request->user()->name
            ),
            $request->user()->id
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);
        $this->authorizeProductManage($request, $product);

        $productId = $product->id;
        $productName = $product->name;
        $shopId = $product->shop_id;
        $deletedBy = $request->user()->name;

        $product->delete();

        $this->notifyShopAdmins(
            $shopId,
            new ProductDeleted(
                $productId,
                $productName,
                $deletedBy
            ),
            $request->user()->id
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function qrCode(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $qrCodeData = $product->generateQrCode();

        return response($qrCodeData)
            ->header('Content-Type', 'image/svg+xml');
    }

    protected function authorizeProduct(
        Request $request,
        Product $product
    ): void {
        if (
            $product->shop_id !== $request->user()->shop_id
            && !$request->user()->isSystemAdmin()
        ) {
            abort(403);
        }
    }

    protected function authorizeProductManage(
        Request $request,
        Product $product
    ): void {
        if (
            !$request->user()->isSystemAdmin()
            && !$request->user()->isShopAdmin()
        ) {
            abort(403);
        }
    }
}