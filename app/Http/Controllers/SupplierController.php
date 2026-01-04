<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;
        $query = Supplier::where('shop_id', $shopId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $validated['shop_id'] = $request->user()->shop_id;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Request $request, Supplier $supplier)
    {
        $this->authorizeSupplier($request, $supplier);

        $supplier->load(['purchases' => function ($query) {
            $query->latest('purchase_date')->limit(10);
        }]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Request $request, Supplier $supplier)
    {
        $this->authorizeSupplier($request, $supplier);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeSupplier($request, $supplier);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $this->authorizeSupplier($request, $supplier);
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    protected function authorizeSupplier(Request $request, Supplier $supplier): void
    {
        if ($supplier->shop_id !== $request->user()->shop_id && !$request->user()->isSystemAdmin()) {
            abort(403);
        }
    }
}
