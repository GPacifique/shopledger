<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
    $shopId = request()->user()->shop_id;

    return view('products.create', [
        'categories' => Category::where('shop_id', $shopId)->orderBy('name')->get(),
        'suppliers' => Supplier::where('shop_id', $shopId)->orderBy('name')->get(),
    ]);
}

    public function store(Request $request)
    {
        $shopId = $request->user()->shop_id;

        $validated = $request->validate([
            'sku' => [
                'required', 'string', 'max:100',
                Rule::unique('products', 'sku')->where('shop_id', $shopId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'barcode')->where('shop_id', $shopId),
            ],
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['shop_id'] = $shopId;
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['quantity'] = $validated['quantity'] ?? 0;
        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'active';

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

        $shopId = $request->user()->shop_id;

        return view('products.edit', [
            'product' => $product,
            'categories' => Category::where('shop_id', $shopId)->orderBy('name')->get(),
            'suppliers' => Supplier::where('shop_id', $shopId)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $shopId = $product->shop_id;

        $validated = $request->validate([
            'sku' => [
                'required', 'string', 'max:100',
                Rule::unique('products', 'sku')->where('shop_id', $shopId)->ignore($product->id),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'barcode')->where('shop_id', $shopId)->ignore($product->id),
            ],
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'nullable|in:active,inactive',
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

    public function qrCode(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $qrCodeData = $product->generateQrCode();

        return response($qrCodeData)->header('Content-Type', 'image/svg+xml');
    }

    protected function authorizeProduct(Request $request, Product $product): void
    {
        if ($product->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
            abort(403);
        }
    }
  
}