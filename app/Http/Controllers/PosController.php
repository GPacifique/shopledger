<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * POS screen.
     */
   public function index(Request $request)
{
    $products = Product::where('shop_id', $request->user()->shop_id)
        ->where('status', 'active')
        ->orderBy('name')
        ->get();
        $customers = Customer::where('shop_id', $request->user()->shop_id)
        ->orderBy('name')
        ->get();

    return view('pos.index', compact('products', 'customers'));
}
    /**
     * Look up a product by scanned code (barcode, SKU or QR code).
     * Called by the scanner input on every scan.
     */
    public function lookup(Request $request): JsonResponse
    {
        $code = trim((string) $request->query('code', ''));

        if ($code === '') {
            return response()->json(['found' => false, 'message' => 'Empty code.'], 422);
        }

        $product = Product::where('shop_id', $request->user()->shop_id)
            ->where('status', 'active')
            ->where(function ($q) use ($code) {
                $q->where('barcode', $code)
                    ->orWhere('sku', $code)
                    ->orWhere('qr_code', $code);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'found' => false,
                'message' => "No product found for code {$code}.",
            ], 404);
        }

        return response()->json([
            'found' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->selling_price,
                'stock' => (float) $product->stock,
            ],
        ]);
    }

    /**
     * Save the sale. Prices and costs come from the database, never from
     * the browser, so a tampered request can't change what is charged.
     */
    public function store(Request $request): JsonResponse
    {
        $shopId = $request->user()->shop_id;

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        // Merge duplicate lines of the same product.
        $lines = [];
        foreach ($data['items'] as $item) {
            $id = (int) $item['product_id'];
            $lines[$id] = ($lines[$id] ?? 0) + (float) $item['quantity'];
        }

        $sale = DB::transaction(function () use ($lines, $shopId, $request) {
            // Lock rows in a stable order so two cashiers can't oversell
            // the same product or deadlock each other.
            $products = Product::where('shop_id', $shopId)
                ->whereIn('id', array_keys($lines))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($lines as $productId => $qty) {
                $product = $products->get($productId);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "Product #{$productId} was not found.",
                    ]);
                }

                if ($qty > (float) $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$product->name} (available: {$product->stock}).",
                    ]);
                }
            }

            // Adjust column names (sale_date, customer_id, ...) to your sales table.
            $sale = Sale::create([
                'shop_id' => $shopId,
                'customer_id' => null,
                'sale_date' => now(),
                'total_amount' => 0,
                'created_by' => $request->user()->id,
            ]);

            $total = 0;

            foreach ($lines as $productId => $qty) {
                $product = $products[$productId];
                $price = (float) $product->selling_price;
                $lineTotal = round($qty * $price, 2);
                $total += $lineTotal;

                SaleItem::create([
                    'shop_id' => $shopId,
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'line_total' => $lineTotal,
                    'cost_price_at_sale' => $product->buying_price,
                ]);

                $stockAfter = (float) $product->stock - $qty;

                $product->update([
                    'stock' => $stockAfter,
                    'quantity' => $stockAfter,
                ]);

                // If StockService has a sale method, call it here instead.
                StockMovement::create([
                    'shop_id' => $shopId,
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'quantity_change' => -$qty,
                    'quantity_after' => $stockAfter,
                    'unit_cost' => $product->buying_price,
                    'total_cost' => round($qty * (float) $product->buying_price, 2),
                    'movement_date' => now(),
                    'created_by' => $request->user()->id,
                    'note' => 'POS sale',
                ]);
            }

            $sale->update(['total_amount' => round($total, 2)]);

            return $sale;
        });

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'total' => (float) $sale->total_amount,
        ]);
    }
}