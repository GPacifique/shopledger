<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $query = Sale::with(['items', 'creator'])
            ->where('shop_id', $shopId);

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderByDesc('sale_date')->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $shopId = auth()->user()->shop_id;
        $products = Product::where('shop_id', $shopId)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,momo,bank,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
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
            if ($product->stock < $item['quantity']) {
                return redirect()->back()->withInput()
                    ->with('error', "Insufficient stock for \"{$product->name}\". Available: {$product->stock}, Requested: {$item['quantity']}");
            }
        }

        $sale = DB::transaction(function () use ($request, $user, $shopId) {
            $sale = Sale::create([
                'shop_id' => $shopId,
                'sale_date' => $request->sale_date,
                'payment_method' => $request->payment_method,
                'created_by' => $user->id,
                'total_amount' => 0,
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
                    'cost_price_at_sale' => $product->cost_price,
                    'line_total' => $line,
                ]);

                $product->stock -= (int) $item['quantity'];
                $product->save();

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
        $sale->load(['items.product', 'creator', 'shop']);

        // Calculate profit
        $profit = $sale->items->sum(function ($item) {
            return $item->line_total - ($item->cost_price_at_sale * $item->quantity);
        });

        return view('sales.show', compact('sale', 'profit'));
    }

    public function destroy(Request $request, Sale $sale)
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
}
