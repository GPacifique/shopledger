<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\ProductDeleted;
use App\Notifications\ProductUpdated;
use App\Models\StockMovement;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Validator;
use App\Exports\ProductsExport;
use App\Exports\ImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Carbon\Carbon;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Product image settings
    |--------------------------------------------------------------------------
    |
    | Images are stored as public assets in:
    |     public/images/products/xxxxx.png|jpg|jpeg
    |
    | The database stores the path RELATIVE to /public, e.g.
    |     images/products/1730000000_ab12cd34.jpg
    | and views render it with asset($product->product_image)
    | (or the $product->image_url accessor).
    |
    */

    protected const IMAGE_DIRECTORY = 'images/products';

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

    public function export(Request $request)
    {
        $shopId = $request->user()->shop_id;

        if (!$shopId && !$request->user()->isSystemAdmin()) {
            return back()->with('error', 'No shop is associated with your account.');
        }

        $format = strtolower($request->get('format', 'xlsx'));

        $filename = 'products-' . now()->format('Y-m-d-H-i-s');

        if ($format === 'xlsx') {
            return Excel::download(
                new ProductsExport($shopId),
                $filename . '.xlsx',
                ExcelFormat::XLSX
            );
        }

        return Excel::download(
            new ProductsExport($shopId),
            $filename . '.csv',
            ExcelFormat::CSV
        );
    }

    public function importTemplate(Request $request)
    {
        $format = strtolower($request->get('format', 'xlsx'));

        if ($format === 'xlsx') {
            return Excel::download(
                new ImportTemplateExport('products'),
                'mahwi-product-import-template.xlsx',
                ExcelFormat::XLSX
            );
        }

        return Excel::download(
            new ImportTemplateExport('products'),
            'mahwi-product-import-template.csv',
            ExcelFormat::CSV
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt,xlsx,xls',
                'max:10240',
            ],
        ]);

        $shopId = $request->user()->shop_id;

        if (!$shopId) {
            return back()->with(
                'error',
                'No shop is associated with your account.'
            );
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Read CSV / XLSX / XLS
            |--------------------------------------------------------------------------
            */

            $rows = Excel::toArray(
                null,
                $request->file('file')
            );

            if (empty($rows) || empty($rows[0])) {
                return back()->with(
                    'error',
                    'The uploaded spreadsheet is empty.'
                );
            }

            $sheet = $rows[0];

            /*
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            */

            $header = array_shift($sheet);

            $header = array_map(function ($value) {
                $value = preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    (string) $value
                );

                return strtolower(trim((string) $value));
            }, $header);

            /*
            |--------------------------------------------------------------------------
            | Required Columns
            |--------------------------------------------------------------------------
            */

            $requiredColumns = [
                'sku',
                'product name',
                'category',
                'buying price',
                'selling price',
                'opening quantity',
            ];

            foreach ($requiredColumns as $column) {
                if (!in_array($column, $header, true)) {
                    return back()->with(
                        'error',
                        "Missing required column: {$column}"
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Import Counters
            |--------------------------------------------------------------------------
            */

            $imported = 0;
            $skipped = 0;
            $errors = [];

            DB::transaction(function () use (
                $sheet,
                $header,
                $shopId,
                $request,
                &$imported,
                &$skipped,
                &$errors
            ) {

                foreach ($sheet as $index => $row) {

                    $rowNumber = $index + 2;

                    // Skip empty rows
                    if (
                        empty(array_filter(
                            $row,
                            fn ($value) => trim((string) $value) !== ''
                        ))
                    ) {
                        continue;
                    }

                    // Normalize row
                    $row = array_pad(
                        $row,
                        count($header),
                        null
                    );

                    $data = array_combine(
                        $header,
                        array_slice($row, 0, count($header))
                    );

                    // Basic fields
                    $sku = trim((string) ($data['sku'] ?? ''));
                    $name = trim((string) ($data['product name'] ?? ''));
                    $categoryName = trim((string) ($data['category'] ?? ''));

                    // Required validation
                    if ($sku === '') {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: SKU is required.";
                        continue;
                    }

                    if ($name === '') {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Product Name is required.";
                        continue;
                    }

                    if ($categoryName === '') {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Category is required.";
                        continue;
                    }

                    // SKU duplicate
                    if (
                        Product::where('shop_id', $shopId)
                            ->where('sku', $sku)
                            ->exists()
                    ) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: SKU '{$sku}' already exists.";
                        continue;
                    }

                    // Product Name duplicate
                    if (
                        Product::where('shop_id', $shopId)
                            ->where('name', $name)
                            ->exists()
                    ) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Product '{$name}' already exists.";
                        continue;
                    }

                    // Category
                    $category = Category::where('shop_id', $shopId)
                        ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                        ->first();

                    if (!$category) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Category '{$categoryName}' does not exist in this shop.";
                        continue;
                    }

                    // Supplier
                    $supplier = null;

                    $supplierName = trim((string) ($data['supplier'] ?? ''));

                    if ($supplierName !== '') {
                        $supplier = Supplier::where('shop_id', $shopId)
                            ->whereRaw('LOWER(name) = ?', [strtolower($supplierName)])
                            ->first();

                        if (!$supplier) {
                            $skipped++;
                            $errors[] = "Row {$rowNumber}: Supplier '{$supplierName}' does not exist in this shop.";
                            continue;
                        }
                    }

                    // Barcode
                    $barcode = trim((string) ($data['barcode'] ?? ''));
                    $barcode = $barcode !== '' ? $barcode : null;

                    if (
                        $barcode &&
                        Product::where('shop_id', $shopId)
                            ->where('barcode', $barcode)
                            ->exists()
                    ) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Barcode '{$barcode}' already exists.";
                        continue;
                    }

                    // QR Code
                    $qrCode = trim((string) ($data['qr code'] ?? ''));
                    $qrCode = $qrCode !== '' ? $qrCode : null;

                    if (
                        $qrCode &&
                        Product::where('shop_id', $shopId)
                            ->where('qr_code', $qrCode)
                            ->exists()
                    ) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: QR Code '{$qrCode}' already exists.";
                        continue;
                    }

                    // Prices
                    $buyingPrice = $data['buying price'] ?? 0;
                    $sellingPrice = $data['selling price'] ?? 0;

                    if (!is_numeric($buyingPrice) || (float) $buyingPrice < 0) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Invalid Buying Price.";
                        continue;
                    }

                    if (!is_numeric($sellingPrice) || (float) $sellingPrice < 0) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Invalid Selling Price.";
                        continue;
                    }

                    $buyingPrice = (float) $buyingPrice;
                    $sellingPrice = (float) $sellingPrice;

                    // Opening Quantity
                    $openingQuantity = $data['opening quantity'] ?? 0;

                    if (!is_numeric($openingQuantity) || (float) $openingQuantity < 0) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Invalid Opening Quantity.";
                        continue;
                    }

                    $openingQuantity = (float) $openingQuantity;

                    // Opening Unit Cost
                    $openingUnitCost = $data['opening unit cost'] ?? null;

                    if ($openingUnitCost === null || $openingUnitCost === '') {
                        $openingUnitCost = $buyingPrice;
                    }

                    if (!is_numeric($openingUnitCost) || (float) $openingUnitCost < 0) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Invalid Opening Unit Cost.";
                        continue;
                    }

                    $openingUnitCost = (float) $openingUnitCost;

                    // Minimum Stock
                    $minimumStock = $data['minimum stock'] ?? 0;

                    if (!is_numeric($minimumStock) || (float) $minimumStock < 0) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Invalid Minimum Stock.";
                        continue;
                    }

                    $minimumStock = (float) $minimumStock;

                    // Expiry Date
                    $expiryDate = null;

                    if (!empty($data['expiry date'])) {
                        try {
                            $expiryDate = Carbon::parse($data['expiry date'])->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $skipped++;
                            $errors[] = "Row {$rowNumber}: Invalid Expiry Date.";
                            continue;
                        }
                    }

                    // Status
                    $status = strtolower(trim((string) ($data['status'] ?? 'active')));

                    if (!in_array($status, ['active', 'inactive'], true)) {
                        $status = 'active';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Product Image
                    |--------------------------------------------------------------------------
                    |
                    | The spreadsheet may contain just a filename (e.g. "rice.jpg").
                    | The file must ALREADY exist in public/images/products/.
                    | basename() prevents path tricks such as "../../.env".
                    | If the file is not found, the product is still imported
                    | without an image and a warning is added to the report.
                    |
                    */

                    $productImage = trim((string) ($data['product image'] ?? ''));

                    if ($productImage !== '') {
                        $imageFile = basename($productImage);
                        $imageExt = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
                        $imagePath = self::IMAGE_DIRECTORY . '/' . $imageFile;

                        if (
                            in_array($imageExt, ['jpg', 'jpeg', 'png'], true)
                            && is_file(public_path($imagePath))
                        ) {
                            $productImage = $imagePath;
                        } else {
                            $errors[] = "Row {$rowNumber}: Image '{$imageFile}' was not found in " . self::IMAGE_DIRECTORY . "/ — product imported without an image.";
                            $productImage = null;
                        }
                    } else {
                        $productImage = null;
                    }

                    // Description
                    $description = trim((string) ($data['description'] ?? ''));
                    $description = $description !== '' ? $description : null;

                    // Create Product
                    $product = Product::create([
                        'shop_id' => $shopId,
                        'sku' => $sku,
                        'name' => $name,
                        'category_id' => $category->id,
                        'supplier_id' => $supplier?->id,
                        'barcode' => $barcode,
                        'qr_code' => $qrCode,
                        'description' => $description,
                        'buying_price' => $buyingPrice,
                        'selling_price' => $sellingPrice,
                        'quantity' => $openingQuantity,
                        'stock' => $openingQuantity,
                        'minimum_stock' => $minimumStock,
                        'expiry_date' => $expiryDate,
                        'product_image' => $productImage,
                        'status' => $status,
                    ]);

                    // Opening Stock Audit
                    if ($openingQuantity > 0) {

                        $openingTotalCost = round(
                            $openingQuantity * $openingUnitCost,
                            2
                        );

                        StockMovement::create([
                            'shop_id' => $shopId,
                            'product_id' => $product->id,
                            'type' => 'opening',
                            'reference_type' => 'product',
                            'reference_id' => $product->id,
                            'quantity_change' => $openingQuantity,
                            'quantity_after' => $openingQuantity,
                            'unit_cost' => $openingUnitCost,
                            'total_cost' => $openingTotalCost,
                            'movement_date' => now(),
                            'created_by' => $request->user()->id,
                            'note' => 'Opening stock imported from spreadsheet.',
                        ]);
                    }

                    $imported++;
                }
            });

            $message = "{$imported} product(s) imported successfully.";

            if ($skipped > 0) {
                $message .= " {$skipped} row(s) were skipped.";
            }

            return back()
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Throwable $e) {

            report($e);

            return back()->with(
                'error',
                'Product import failed: ' . $e->getMessage()
            );
        }
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
            | Opening Stock: the physical stock already available when the
            | product is introduced into MahWi.
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

            // Product image (stored in public/images/products)
            'product_image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Opening Stock Defaults
        |--------------------------------------------------------------------------
        */

        $openingStock = (float) ($validated['opening_stock'] ?? 0);

        /*
        | If no separate opening cost is entered, use the product buying price.
        | Example: Buying Price 2,500 x Opening Stock 100 = 250,000
        */
        $openingUnitCost = (float) (
            $validated['opening_unit_cost']
            ?? $validated['buying_price']
        );

        $openingStockDate = $validated['opening_stock_date'] ?? now();

        /*
        |--------------------------------------------------------------------------
        | Save the image file first (if any)
        |--------------------------------------------------------------------------
        |
        | If the database transaction fails afterwards, the file is removed
        | again so no orphan images are left in public/images/products.
        |
        */

        $imagePath = $request->hasFile('product_image')
            ? $this->storeProductImage($request->file('product_image'))
            : null;

        /*
        |--------------------------------------------------------------------------
        | Create Product + Opening Stock Movement Atomically
        |--------------------------------------------------------------------------
        */

        try {
            DB::transaction(function () use (
                $validated,
                $shopId,
                $openingStock,
                $openingUnitCost,
                $openingStockDate,
                $imagePath,
                $request
            ) {

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

                    // Opening stock becomes the initial stock.
                    'quantity' => $openingStock,
                    'stock' => $openingStock,

                    'minimum_stock' => $validated['minimum_stock'] ?? 0,

                    'expiry_date' => $validated['expiry_date'] ?? null,

                    'product_image' => $imagePath,

                    'status' => $validated['status'] ?? 'active',
                ]);

                /*
                | NEVER silently put opening stock into products.stock.
                | Every initial stock quantity must have an audit record.
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
        } catch (\Throwable $e) {
            // Roll back the uploaded file too.
            $this->deleteProductImage($imagePath);

            throw $e;
        }

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

        $shopId = $product->shop_id;

        $product->load([
            'category',
            'supplier',
        ]);

        // Purchase History
        $purchaseItems = PurchaseItem::with([
                'purchase.supplier',
                'purchase.creator',
            ])
            ->where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(10, ['*'], 'purchases');

        // Sales History
        $saleItems = SaleItem::with([
                'sale.customer',
                'sale.creator',
            ])
            ->where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(10, ['*'], 'sales');

        // Purchase Statistics
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
        | share the same basis (line_total).
        |
        | cost_price_at_sale can be NULL for legacy or bad rows. SQL's
        | SUM() skips NULLs, which would understate cost and overstate
        | profit. We COALESCE the missing cost to the product's current
        | buying_price and count how many rows needed it, so the estimate
        | is visible instead of silent.
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

        // Last Purchase
        $lastPurchase = PurchaseItem::where('shop_id', $shopId)
            ->where('product_id', $product->id)
            ->latest()
            ->first();

        // Last Sale
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
            'is_featured' => 'required|boolean',

            // Optional reason for a manual stock/cost change, so the
            // audit trail records *why*, not just *what* changed.
            'adjustment_note' => 'nullable|string|max:255',

            // Product image (stored in public/images/products)
            'product_image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
            'product_image_url' => 'nullable|url:http,https|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Image handling
        |--------------------------------------------------------------------------
        |
        | - New file uploaded  -> store it, replace the old one
        | - "remove_image" on  -> clear the image, delete the old file
        | - Nothing sent       -> keep the current image untouched
        |
        | The old file is deleted only AFTER the transaction succeeds, so a
        | failed update never loses the existing image.
        |
        */

        $oldImage = $product->product_image;
        $newImage = $oldImage;
        $uploadedImage = null;

        if ($request->hasFile('product_image')) {
            $uploadedImage = $this->storeProductImage($request->file('product_image'));
            $newImage = $uploadedImage;
        } elseif ($request->boolean('remove_image')) {
            $newImage = null;
        } elseif ($request->filled('product_image_url')) {
            $newImage = $validated['product_image_url'];
        }

        // Pull non-column fields out of $validated before mass-updating.
        $adjustmentNote = $validated['adjustment_note'] ?? null;

        unset(
            $validated['adjustment_note'],
            $validated['remove_image']
        );
        unset($validated['product_image_url']);

        $validated['product_image'] = $newImage;

        /*
        |--------------------------------------------------------------------------
        | Manual Stock / Cost Adjustment Audit Trail
        |--------------------------------------------------------------------------
        |
        | store() never lets opening stock land in products.stock without a
        | StockMovement record, so update() must not either. We snapshot the
        | "before" state, apply the update, then log a movement for any actual
        | stock delta and a separate cost_revision entry if the buying price
        | changed — all inside one transaction.
        */

        $stockBefore = (float) $product->stock;
        $buyingPriceBefore = (float) $product->buying_price;

        try {
            DB::transaction(function () use (
                $product,
                $validated,
                $request,
                $shopId,
                $stockBefore,
                $buyingPriceBefore,
                $adjustmentNote
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

                        'note' => $adjustmentNote
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
        } catch (\Throwable $e) {
            // Update failed: discard the newly uploaded file, keep the old one.
            $this->deleteProductImage($uploadedImage);

            throw $e;
        }

        // Update succeeded: now it is safe to remove the replaced/removed file.
        if ($oldImage && $oldImage !== $newImage) {
            $this->deleteProductImage($oldImage);
        }

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
        $imagePath = $product->product_image;

        $product->delete();

        /*
        | If Product uses SoftDeletes, REMOVE the next line so the image
        | is still there when the product is restored.
        */
        $this->deleteProductImage($imagePath);

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

    /*
    |--------------------------------------------------------------------------
    | Image helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Move an uploaded image into public/images/products and return the
     * path relative to /public (what gets saved in products.product_image).
     */
    protected function storeProductImage(UploadedFile $file): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        // Never trust the client extension alone: fall back to the guessed one.
        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            $extension = $file->guessExtension() === 'png' ? 'png' : 'jpg';
        }

        $filename = time() . '_' . Str::random(8) . '.' . $extension;

        $file->move($directory, $filename);

        return self::IMAGE_DIRECTORY . '/' . $filename;
    }

    /**
     * Delete a product image file. Only files inside
     * public/images/products can ever be removed.
     */
    protected function deleteProductImage(?string $path): void
    {
        if (!$path || !str_starts_with($path, self::IMAGE_DIRECTORY . '/')) {
            return;
        }

        // basename() blocks "../" tricks from a tampered DB value.
        $fullPath = public_path(self::IMAGE_DIRECTORY . '/' . basename($path));

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
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