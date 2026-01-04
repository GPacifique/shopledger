<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
                $query->where('stock', '<=', 10)->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'in') {
                $query->where('stock', '>', 10);
            }
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        $validated['shop_id'] = $request->user()->shop_id;
        $validated['stock'] = $validated['stock'] ?? 0;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        return view('products.show', compact('product'));
    }

    public function edit(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    protected function authorizeProduct(Request $request, Product $product): void
    {
        if ($product->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
            abort(403);
        }
    }
}
