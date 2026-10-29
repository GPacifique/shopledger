<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * POS screen: product cards with images.
     */
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;

        abort_if(!$shopId, 403, 'No shop is associated with your account.');

        $products = Product::with('category:id,name')
            ->where('shop_id', $shopId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                return [
                    'id'            => $product->id,
                    'name'          => $product->name,
                    'sku'           => $product->sku,
                    'barcode'       => $product->barcode,
                    'price'         => (float) $product->selling_price,
                    'stock'         => (float) $product->stock,
                    'minimum_stock' => (float) $product->minimum_stock,
                    'category_id'   => $product->category_id,
                    'image'         => $product->image_url,
                ];
            })
            ->values();

        $categories = $products
            ->pluck('category_id')
            ->unique()
            ->filter()
            ->values();

        $categories = \App\Models\Category::where('shop_id', $shopId)
            ->whereIn('id', $categories)
            ->orderBy('name')
            ->get(['id', 'name']);

        $recentSales = Sale::where('shop_id', $shopId)
            ->latest('id')
            ->limit(5)
            ->get(['id', 'sale_date', 'total_amount', 'payment_status']);

        return view('pos.index', compact('products', 'categories', 'recentSales'));
    }

    /**
     * Complete a sale.
     *
     * Prices and costs are ALWAYS taken from the database, never from
     * the browser.
     */
    public function checkout(Request $request): JsonResponse
    {
        $shopId = $request->user()->shop_id;

        if (!$shopId) {
            return response()->json([
                'success' => false,
                'message' => 'No shop is associated with your account.',
            ], 403);
        }

        $data = $request->validate([
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'integer'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
            'payment_method'       => ['required', 'in:cash,mobile_money,card'],
            'amount_paid'          => ['nullable', 'numeric', 'min:0'],
        ]);

        // Merge duplicate product lines
        $requested = [];
        foreach ($data['items'] as $item) {
            $id = (int) $item['product_id'];
            $requested[$id] = ($requested[$id] ?? 0) + (float) $item['quantity'];
        }

        $sale = DB::transaction(function () use ($requested, $shopId, $data, $request) {

            // Lock rows so two cashiers cannot sell the same last unit
            $products = Product::where('shop_id', $shopId)
                ->where('status', 'active')
                ->whereIn('id', array_keys($requested))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($requested as $productId => $qty) {
                $product = $products->get($productId);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "A product in the cart is no longer available.",
                    ]);
                }

                if ((float) $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$product->name} (available: {$product->stock}).",
                    ]);
                }
            }

            $total = 0;
            foreach ($requested as $productId => $qty) {
                $total += $qty * (float) $products[$productId]->selling_price;
            }
            $total = round($total, 2);

            $paid = isset($data['amount_paid'])
                ? (float) $data['amount_paid']
                : $total;

            $sale = Sale::create([
                'shop_id'        => $shopId,
                'created_by'     => $request->user()->id,
                'total_amount'   => $total,
                'payment_status' => $paid >= $total
                    ? 'Paid'
                    : ($paid > 0 ? 'Partial' : 'Unpaid'),
                'payment_method' => $data['payment_method'] === 'mobile_money'
                    ? 'momo'
                    : $data['payment_method'],
                'sale_date'      => now()->toDateString(),
            ]);

            foreach ($requested as $productId => $qty) {
                $product = $products[$productId];
                $unitPrice = (float) $product->selling_price;
                $stockBefore = (float) $product->stock;
                $newStock = $stockBefore - $qty;

                SaleItem::create([
                    'shop_id'            => $shopId,
                    'sale_id'            => $sale->id,
                    'product_id'         => $product->id,
                    'quantity'           => $qty,
                    'unit_price'         => $unitPrice,
                    'cost_price_at_sale' => $product->buying_price,
                    'line_total'         => round($qty * $unitPrice, 2),
                ]);

                $product->update([
                    'stock'    => $newStock,
                    'quantity' => $newStock,
                ]);

                StockMovement::create([
                    'shop_id'         => $shopId,
                    'product_id'      => $product->id,
                    'type'            => 'sale',
                    'quantity'        => -(int) $qty,
                    'stock_before'    => (int) $stockBefore,
                    'stock_after'     => (int) $newStock,
                    'reference_type'  => Sale::class,
                    'reference_id'    => $sale->id,
                    'created_by'      => $request->user()->id,
                    'notes'           => 'POS sale',
                ]);
            }

            return $sale;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sale completed successfully.',
            'sale_id' => $sale->id,
            'sale_date' => $sale->sale_date->format('M d, Y'),
            'total_amount' => (float) $sale->total_amount,
            'payment_status' => $sale->payment_status,
            'print_url' => route('pos.sales.print', ['sale' => $sale, 'print' => 1]),
            'pdf_url' => route('pos.sales.export', $sale),
        ]);
    }
}