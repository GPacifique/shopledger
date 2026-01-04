<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display stock dashboard
     */
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $filter = $request->get('filter', 'all');

        $summary = $this->stockService->getShopStockSummary($shopId);
        $products = $this->stockService->getRealTimeStockData($shopId, $filter === 'all' ? null : $filter);
        
        $alerts = StockAlert::where('shop_id', $shopId)
            ->where('is_resolved', false)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $recentMovements = StockMovement::where('shop_id', $shopId)
            ->with(['product', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('stock.index', compact('summary', 'products', 'alerts', 'recentMovements', 'filter'));
    }

    /**
     * API endpoint for real-time stock data
     */
    public function realTimeData(Request $request): JsonResponse
    {
        $shopId = $request->user()->shop_id;
        $filter = $request->get('filter');

        $data = [
            'summary' => $this->stockService->getShopStockSummary($shopId),
            'products' => $this->stockService->getRealTimeStockData($shopId, $filter),
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json($data);
    }

    /**
     * API endpoint for stock alerts
     */
    public function alerts(Request $request): JsonResponse
    {
        $shopId = $request->user()->shop_id;

        $alerts = StockAlert::where('shop_id', $shopId)
            ->where('is_resolved', false)
            ->with('product:id,name,sku,stock')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'type' => $alert->alert_type,
                    'color' => $alert->alert_color,
                    'product' => [
                        'id' => $alert->product->id,
                        'name' => $alert->product->name,
                        'sku' => $alert->product->sku,
                        'stock' => $alert->product->stock,
                    ],
                    'current_stock' => $alert->current_stock,
                    'threshold' => $alert->threshold,
                    'created_at' => $alert->created_at->diffForHumans(),
                ];
            });

        return response()->json(['alerts' => $alerts, 'count' => $alerts->count()]);
    }

    /**
     * Show stock movements for a product
     */
    public function movements(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $movements = StockMovement::where('product_id', $product->id)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('stock.movements', compact('product', 'movements'));
    }

    /**
     * Show stock adjustment form
     */
    public function adjustForm(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        return view('stock.adjust', compact('product'));
    }

    /**
     * Process stock adjustment
     */
    public function adjust(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $validated = $request->validate([
            'adjustment_type' => 'required|in:set,add,subtract',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $currentStock = $product->stock;
        $newStock = match ($validated['adjustment_type']) {
            'set' => $validated['quantity'],
            'add' => $currentStock + $validated['quantity'],
            'subtract' => max(0, $currentStock - $validated['quantity']),
        };

        $this->stockService->recordAdjustment(
            $product,
            $newStock,
            $validated['reason'],
            $request->user()->id
        );

        return redirect()->route('stock.index')
            ->with('success', "Stock adjusted successfully. {$product->name}: {$currentStock} → {$newStock}");
    }

    /**
     * Record damaged stock
     */
    public function recordDamage(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
            'notes' => 'required|string|max:500',
        ]);

        $this->stockService->recordDamage(
            $product,
            $validated['quantity'],
            $validated['notes'],
            $request->user()->id
        );

        return redirect()->route('stock.index')
            ->with('success', "Damaged stock recorded. {$product->name}: -{$validated['quantity']} units");
    }

    /**
     * Resolve a stock alert
     */
    public function resolveAlert(Request $request, StockAlert $alert)
    {
        if ($alert->shop_id !== $request->user()->shop_id) {
            abort(403);
        }

        $alert->resolve($request->user()->id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Alert resolved successfully.');
    }

    /**
     * Export stock report
     */
    public function export(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $products = Product::where('shop_id', $shopId)
            ->orderBy('name')
            ->get();

        $filename = 'stock_report_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['SKU', 'Product Name', 'Current Stock', 'Low Stock Threshold', 'Status', 'Cost Price', 'Sale Price', 'Stock Value', 'Retail Value']);
            
            foreach ($products as $product) {
                $status = 'Healthy';
                if ($product->stock <= 0) {
                    $status = 'Out of Stock';
                } elseif ($product->stock <= $product->low_stock_threshold) {
                    $status = 'Low Stock';
                }

                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->stock,
                    $product->low_stock_threshold,
                    $status,
                    number_format($product->cost_price, 2),
                    number_format($product->sale_price, 2),
                    number_format($product->stock * $product->cost_price, 2),
                    number_format($product->stock * $product->sale_price, 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function authorizeProduct(Request $request, Product $product): void
    {
        if ($product->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
            abort(403);
        }
    }
}
