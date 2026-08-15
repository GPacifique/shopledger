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
use App\Notifications\SaleDeleted;
use Illuminate\Support\Facades\Notification;
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
            'payment_status' => 'nullable|in:Paid,Unpaid','Partial',
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
                    (int) $item['quantity'],
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
            'payment_status' => 'nullable|in:paid,unpaid',
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

        $profit = $sale->items->reduce(function ($carry, $item) {
            return $carry + ($item->line_total - ($item->cost_price_at_sale * $item->quantity));
        }, 0);

        return view('sales.receipt', compact('sale', 'profit'));
    }

    public function export(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);
        $sale->load(['items.product', 'creator', 'shop', 'customer']);

        $profit = $sale->items->reduce(function ($carry, $item) {
            return $carry + ($item->line_total - ($item->cost_price_at_sale * $item->quantity));
        }, 0);

        $pdf = Pdf::loadView('sales.receipt', compact('sale', 'profit'))
            ->setPaper([0, 0, 280, 800], 'portrait');

        return $pdf->download('sale-' . $sale->id . '.pdf');
    }

    public function updatePaymentStatus(Request $request, Sale $sale)
    {
        $this->authorizeSale($request, $sale);

        $request->validate([
            'payment_status' => 'required|in:paid,unpaid',
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