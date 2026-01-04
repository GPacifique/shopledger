<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $query = Purchase::with(['supplier', 'items', 'creator'])
            ->where('shop_id', $shopId);

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->orderByDesc('purchase_date')->paginate(15)->withQueryString();
        $suppliers = Supplier::where('shop_id', $shopId)->orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $suppliers = Supplier::where('shop_id', $shopId)->orderBy('name')->get();
        $products = Product::where('shop_id', $shopId)->orderBy('name')->get();
        $selectedSupplier = $request->supplier ? Supplier::find($request->supplier) : null;

        return view('purchases.create', compact('suppliers', 'products', 'selectedSupplier'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $user = $request->user();
        $shopId = $user->shop_id;

        $purchase = DB::transaction(function () use ($request, $user, $shopId) {
            $purchase = Purchase::create([
                'shop_id' => $shopId,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'created_by' => $user->id,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])->where('shop_id', $shopId)->firstOrFail();
                $line = $item['quantity'] * $item['unit_cost'];
                PurchaseItem::create([
                    'shop_id' => $shopId,
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $line,
                ]);

                $product->stock += (int) $item['quantity'];
                $product->cost_price = $item['unit_cost'];
                $product->save();

                $total += $line;
            }

            $purchase->total_amount = $total;
            $purchase->save();

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase recorded successfully. Stock has been updated.');
    }

    public function show(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $purchase->load(['supplier', 'items.product', 'creator']);

        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);

        DB::transaction(function () use ($purchase) {
            // Reverse stock changes
            foreach ($purchase->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }
            $purchase->items()->delete();
            $purchase->delete();
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted and stock reversed.');
    }

    protected function authorizePurchase(Request $request, Purchase $purchase): void
    {
        if ($purchase->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
            abort(403);
        }
    }

    public function downloadPdf(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($request, $purchase);
        $purchase->load(['supplier', 'items.product', 'creator', 'shop']);

        $pdf = Pdf::loadView('purchases.pdf', compact('purchase'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('purchase-note-' . $purchase->id . '.pdf');
    }
}
