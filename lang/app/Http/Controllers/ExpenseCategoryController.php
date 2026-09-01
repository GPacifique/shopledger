<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display all categories for current shop
     */
    public function index()
    {
        $shop = auth()->user()->shop;

        $categories = ExpenseCategory::where('shop_id', $shop->id)
            ->latest()
            ->get();

        return view('expensecategories.index', compact('categories', 'shop'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('expensecategories.create');
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $shop = auth()->user()->shop;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create([
            'shop_id' => $shop->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('expensecategories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(ExpenseCategory $expenseCategory)
    {

        return view('expensecategories.edit', [
            'category' => $expenseCategory
        ]);
    }

    /**
     * Update category
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $expenseCategory->update($validated);

        return redirect()
            ->route('expensecategories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category
     */
  public function destroy(ExpenseCategory $expenseCategory)
{
    if ($expenseCategory->expenses()->exists()) {
        return back()->withErrors([
            'error' => 'Cannot delete category because it is used in expenses.'
        ]);
    }

    $expenseCategory->delete();

    return redirect()
        ->route('expensecategories.index')
        ->with('success', 'Category deleted successfully.');
}
  
}