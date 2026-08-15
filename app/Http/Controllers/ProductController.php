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

            'quantity' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',

            'expiry_date' => 'nullable|date',

            'category_id' => 'required|exists:categories,id',

            'supplier_id' => 'nullable|exists:suppliers,id',

            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['shop_id'] = $shopId;
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['quantity'] = $validated['quantity'] ?? 0;
        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'active';

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
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