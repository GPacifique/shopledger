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
        | Sales Statistics
        |--------------------------------------------------------------------------
        */

        $totalSold = SaleItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->sum('quantity');

        $totalSales = SaleItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->sum('line_total');

        /*
        |--------------------------------------------------------------------------
        | Gross Profit
        |--------------------------------------------------------------------------
        |
        | Gross Profit =
        | (Selling Price - Cost Price At Sale) × Quantity
        |
        */

        $grossProfit = SaleItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->selectRaw(
                'COALESCE(
                    SUM(
                        (unit_price - cost_price_at_sale) * quantity
                    ),
                    0
                ) AS profit'
            )
            ->value('profit');

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
        ]);

        $product->update($validated);

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