<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Notifications\SaleDeleted;
use Illuminate\Support\Facades\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class SaleController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;

        $query = Sale::query()
            ->with(['customer', 'items', 'creator'])
            ->where('shop_id', $shopId);

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        // Clone before pagination so stats reflect the full filtered set,
        // not just the current page
        $statsQuery = clone $query;

        $totalSales   = (clone $statsQuery)->count();
        $totalRevenue = (clone $statsQuery)->sum('total_amount');
        $averageSale  = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Profit: revenue minus cost of goods sold, via sale items.
        $totalCost = (clone $statsQuery)
            ->with('items')
            ->get()
            ->sum(function ($sale) {
                return $sale->items->sum(function ($item) {
                    return $item->cost_price_at_sale * $item->quantity;
                });
            });

        $totalProfit = $totalRevenue - $totalCost;

        // Items sold — total quantity across all filtered sale items
        $totalItemsSold = (clone $statsQuery)
            ->withSum('items', 'quantity')
            ->get()
            ->sum('items_sum_quantity');

        // Today's sales count — scoped to this shop, independent of the date filter
        $todaySales = Sale::where('shop_id', $shopId)
            ->whereDate('sale_date', today())
            ->count();

        // NOTE: `sales` currently has no `status` column (only `payment_status`),
        // so a `whereIn('status', [...])` query here would throw an "unknown
        // column" SQL error. Defaulting to 0 until the status migration/enum
        // from earlier is actually applied — swap this back to a real query
        // once that column exists.
        $refundedCount = 0;

        $sales = $query->orderByDesc('sale_date')->paginate(15)->withQueryString();

        return view('sales.index', compact(
            'sales', 'todaySales', 'totalProfit', 'refundedCount',
            'totalSales', 'totalRevenue', 'averageSale', 'totalItemsSold'
        ));
    }

    public function create(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $products = Product::where('shop_id', $shopId)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
        $customers = Customer::where('shop_id', $shopId)->orderBy('name')->get();

        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,momo,bank,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01|decimal:0,2',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_status' => 'nullable|in:paid,unpaid,partial',
        ]);

        $user = $request->user();
        $shopId = $user->shop_id;

        // Pre-validate stock for all items before starting transaction
        foreach ($request->items as $item) {
            $product = Product::where('id', $item['product_id'])->where('shop_id', $shopId)->first();
            if (!$product) {
                return redirect()->back()->withInput()
                    ->with('error', 'Product not found.');
            }

            if ($product->stock <= 0 || $product->stock < $item['quantity']) {
                return redirect()->back()->withInput()
                    ->with('error', "Insufficient stock for \"{$product->name}\". Available: {$product->stock}, Requested: {$item['quantity']}");
            }
        }

        $sale = DB::transaction(function () use ($request, $user, $shopId) {
            $sale = Sale::create([
                'shop_id' => $shopId,
                'customer_id' => $request->filled('customer_id') ? $request->customer_id : null,
                'sale_date' => $request->sale_date,
                'payment_method' => $request->payment_method,
                'created_by' => $user->id,
                'total_amount' => 0,
                'payment_status' => $request->payment_status,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])->where('shop_id', $shopId)->firstOrFail();
                $line = $item['quantity'] * $item['unit_price'];
                SaleItem::create([
                    'shop_id' => $shopId,
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price_at_sale' => $product->buying_price,
                    'line_total' => $line,
                ]);

                // Record stock movement (auto-creates audit trail)
                $this->stockService->recordSale(
                    $product,
                    (float) $item['quantity'],
                    $sale->id,
                    $user->id
                );

                $total += $line;
            }

            $sale->total_amount = $total;
            $sale->save();

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale recorded successfully. Stock has been updated.');
    }

    public function show(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);
        $sale->load(['items.product', 'creator', 'shop', 'customer']);

        // Calculate profit
        $profit = $sale->items->reduce(function ($carry, $item) {
            return $carry + ($item->line_total - ($item->cost_price_at_sale * $item->quantity));
        }, 0);

        return view('sales.show', compact('sale', 'profit'));
    }

    public function edit(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);
        $this->authorizeSaleManage($request, $sale);

        return view('sales.edit', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);
        $this->authorizeSaleManage($request, $sale);

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,momo,bank,card',
            'payment_status' => 'nullable|in:paid,unpaid,partial',
        ]);

        $sale->update($validated);

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale updated successfully.');
    }

/**public function destroy(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);

        DB::transaction(function () use ($sale) {
            // Reverse stock changes
            foreach ($sale->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale deleted and stock restored.');
    }

    protected function authorizeSale(Request $request, Sale $sale): void
    {
        if ($sale->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
            abort(403);
        }
    }
**/

public function destroy(Request $request, Sale $sale)
{
    $this->authorizeSale($request, $sale);
    $this->authorizeSaleManage($request, $sale);

    // Snapshot before it's gone
    $saleId        = $sale->id;
    $totalAmount   = $sale->total_amount;
    $paymentMethod = Sale::PAYMENT_METHODS[$sale->payment_method] ?? $sale->payment_method;
    $shopId        = $sale->shop_id;
    $deletedBy     = $request->user()->name;
    $user          = $request->user();

    DB::transaction(function () use ($sale, $user) {
        // Restore stock with movement tracking (reverse the sale)
        foreach ($sale->items as $item) {
            // Use TYPE_RETURN to reverse the stock decrease from sale
            $this->stockService->recordMovement(
                $item->product,
                StockMovement::TYPE_RETURN,
                $item->quantity,
                'App\Models\Sale',
                $sale->id,
                'Sale deleted - stock reversal',
                $user->id
            );
        }
        $sale->items()->delete();
        $sale->delete();
    });

    $this->notifyShopAdmins($shopId, new SaleDeleted($saleId, $totalAmount, $paymentMethod, $deletedBy), $request->user()->id);

    return redirect()->route('sales.index')
        ->with('success', 'Sale deleted and stock restored.');
}

protected function authorizeSale(Request $request, Sale $sale): void
{
    if ($sale->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
        abort(403);
    }
}

protected function authorizeSaleManage(Request $request, Sale $sale): void
{
    if (!$request->user()->isSystemAdmin() && !$request->user()->isShopAdmin()) {
        abort(403);
    }
}
    public function print(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);
        $sale->load(['items.product', 'creator', 'shop', 'customer']);

        [$view, $data] = $this->printDocumentData($request, $sale, false);

        return view($view, $data);
    }

    public function export(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);
        $sale->load(['items.product', 'creator', 'shop', 'customer']);

        [$view, $data, $paper] = $this->printDocumentData($request, $sale, true);

        return Pdf::loadView($view, $data)
            ->setPaper($paper, 'portrait')
            ->download(strtolower($data['receiptNumber']) . '.pdf');
    }

    /**
     * Resolve printable formats and settings from the sale's owning shop only.
     * Amounts are read from the transaction; this does not alter sales logic.
     */
    private function printDocumentData(Request $request, Sale $sale, bool $pdfMode): array
    {
        $validated = $request->validate([
            'document' => ['nullable', 'in:receipt,invoice'],
            'paper' => ['nullable', 'in:58mm,80mm,a4,a5,letter,custom'],
        ]);
        $documentType = $validated['document'] ?? 'receipt';
        $printSettings = $sale->shop->resolvedPrintSettings();
        $defaultPaper = $documentType === 'receipt'
            ? $printSettings['receipt_paper_size']
            : $printSettings['invoice_paper_size'];
        $paperSize = $validated['paper'] ?? $defaultPaper;
        $allowedPapers = $documentType === 'receipt'
            ? ['58mm', '80mm', 'custom']
            : ['a4', 'a5', 'letter', 'custom'];

        if (! in_array($paperSize, $allowedPapers, true)) {
            $paperSize = $defaultPaper;
        }

        $millimetersToPoints = static fn (float $millimeters): float => $millimeters * 72 / 25.4;

        $receiptWidthMm = null;
        if ($documentType === 'receipt') {
            $receiptWidthMm = match ($paperSize) {
                '58mm' => 58,
                'custom' => min(120, max(40, (float) $printSettings['receipt_custom_width_mm'])),
                default => 80,
            };
            $pageSize = $receiptWidthMm . 'mm auto';
            $pageMargin = $receiptWidthMm <= 58 ? '2mm' : '3mm';
            $pageWidth = $receiptWidthMm . 'mm';
            $itemHeight = $sale->items->sum(function ($item) use ($receiptWidthMm): int {
                $name = (string) ($item->product?->name ?? '');
                $charactersPerLine = $receiptWidthMm <= 58 ? 24 : 38;

                return 22 + (max(1, (int) ceil(strlen($name) / $charactersPerLine)) * 12);
            });
            $pdfPaper = [
                0,
                0,
                $millimetersToPoints($receiptWidthMm),
                max(360, 250 + $itemHeight + ($printSettings['show_qr'] ? 100 : 0)),
            ];
        } else {
            [$invoiceWidthMm, $invoiceHeightMm, $pageMargin, $pageSize, $pdfPaper] = match ($paperSize) {
                'a5' => [148, 210, '10mm', 'A5 portrait', 'a5'],
                'letter' => [216, 279, '15mm', 'letter portrait', 'letter'],
                'custom' => [
                    min(300, max(100, (float) $printSettings['invoice_custom_width_mm'])),
                    min(400, max(100, (float) $printSettings['invoice_custom_height_mm'])),
                    '12mm',
                    min(300, max(100, (float) $printSettings['invoice_custom_width_mm'])) . 'mm '
                        . min(400, max(100, (float) $printSettings['invoice_custom_height_mm'])) . 'mm',
                    [
                        0,
                        0,
                        $millimetersToPoints(min(300, max(100, (float) $printSettings['invoice_custom_width_mm']))),
                        $millimetersToPoints(min(400, max(100, (float) $printSettings['invoice_custom_height_mm']))),
                    ],
                ],
                default => [210, 297, '15mm', 'A4 portrait', 'a4'],
            };
            $pageWidth = $invoiceWidthMm . 'mm';
        }

        $saleTotal = (float) $sale->total_amount;
        $paymentStatus = strtolower((string) $sale->payment_status);
        $amountPaid = match ($paymentStatus) {
            'paid' => $saleTotal,
            'unpaid' => 0.0,
            default => null,
        };
        $balanceDue = is_null($amountPaid) ? null : max(0, $saleTotal - $amountPaid);
        $paymentMethod = Sale::PAYMENT_METHODS[$sale->payment_method]
            ?? ucfirst(str_replace('_', ' ', (string) $sale->payment_method));
        $referencePrefix = $documentType === 'invoice' ? 'INV' : 'RCP';
        $receiptNumber = $referencePrefix . '-' . str_pad((string) $sale->id, 8, '0', STR_PAD_LEFT);
        $qrCode = null;
        $qr = null;
        $qrMimeType = null;

        if ($printSettings['show_qr']) {
            $verificationUrl = URL::signedRoute('documents.verify', ['uuid' => $sale->uuid]);
            if ($pdfMode && extension_loaded('imagick')) {
                $qrMimeType = 'png';
                $qr = base64_encode(
                    QrCode::format('png')->size(200)->margin(1)->errorCorrection('M')->generate($verificationUrl)
                );
                $qrCode = $pdfMode
                    ? null
                    : QrCode::format('svg')->size(200)->margin(1)->errorCorrection('M')->generate($verificationUrl);
            } else {
                $qrSvg = QrCode::format('svg')->size(200)->margin(1)->errorCorrection('M')->generate($verificationUrl);
                $qrMimeType = 'svg+xml';
                $qr = base64_encode($qrSvg);
                $qrCode = $pdfMode ? null : $qrSvg;
            }
        }

        $subtotalAttribute = $sale->getAttribute('subtotal');
        $discountAmount = (float) ($sale->getAttribute('discount_amount') ?? 0);
        $taxAmount = (float) ($sale->getAttribute('tax_amount') ?? 0);
        $serviceCharges = (float) ($sale->getAttribute('service_charge') ?? 0);
        $logoSrc = $printSettings['show_logo'] ? $this->printLogoSource($sale->shop, $pdfMode) : null;
        $view = $documentType === 'invoice' ? 'sales.invoice' : 'sales.receipt';
        $data = compact(
            'sale',
            'printSettings',
            'documentType',
            'paperSize',
            'pageSize',
            'pageMargin',
            'pageWidth',
            'receiptWidthMm',
            'receiptNumber',
            'paymentMethod',
            'paymentStatus',
            'amountPaid',
            'balanceDue',
            'subtotalAttribute',
            'discountAmount',
            'taxAmount',
            'serviceCharges',
            'qr',
            'qrMimeType',
            'qrCode',
            'logoSrc',
            'pdfMode',
        ) + [
            'currency' => 'RWF',
            'money' => static fn ($amount): string => number_format((float) $amount),
            'subtotal' => is_null($subtotalAttribute)
                ? max(0, $saleTotal + $discountAmount - $taxAmount - $serviceCharges)
                : (float) $subtotalAttribute,
            'paperOptions' => $documentType === 'receipt'
                ? ['58mm' => '58 mm thermal', '80mm' => '80 mm thermal', 'custom' => 'Custom width']
                : ['a4' => 'A4', 'a5' => 'A5', 'letter' => 'Letter', 'custom' => 'Custom size'],
        ];
        $data['pdfRouteName'] = $request->routeIs('pos.sales.*')
            ? 'pos.sales.export'
            : 'sales.export';
        $data['previewReturnUrl'] = $request->routeIs('pos.sales.*')
            ? route('pos.index')
            : route('sales.show', $sale);

        return [$view, $data, $pdfPaper];
    }

    private function printLogoSource(\App\Models\Shop $shop, bool $pdfMode): ?string
    {
        if (! $shop->logo) {
            return null;
        }

        if (! $pdfMode) {
            return $shop->logo_url;
        }

        $isPublicImage = str_starts_with($shop->logo, 'images/');
        $basePath = realpath($isPublicImage ? public_path('images') : storage_path('app/public'));
        $relativePath = $isPublicImage
            ? substr($shop->logo, strlen('images/'))
            : preg_replace('#^(public/|storage/)?#', '', $shop->logo);
        $filePath = $basePath ? realpath($basePath . DIRECTORY_SEPARATOR . $relativePath) : false;

        if (! $basePath || ! $filePath || ! str_starts_with($filePath, $basePath . DIRECTORY_SEPARATOR) || ! is_file($filePath)) {
            return null;
        }

        $mimeType = mime_content_type($filePath);
        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'], true)) {
            return null;
        }

        return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($filePath));
    }

    public function updatePaymentStatus(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);

        $request->validate([
            'payment_status' => 'required|in:paid,unpaid,partial',
        ]);

        $sale->payment_status = $request->payment_status;
        $sale->save();

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Payment status updated successfully.');
    }

    public function updatePaymentMethod(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);

        $request->validate([
            'payment_method' => 'required|in:cash,momo,bank,card',
        ]);

        $sale->payment_method = $request->payment_method;
        $sale->save();

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Payment method updated successfully.');
    }
}