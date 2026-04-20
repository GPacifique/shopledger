<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;

        $expenses = Expense::where('shop_id', $shop->id)
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
            ->latest()
            ->paginate(15);

        return view('expenses.index', compact('expenses', 'shop'));
    }

    public function create()
    {
        $shop = auth()->user()->shop;

        $categories = ExpenseCategory::where('shop_id', $shop->id)
            ->orderBy('name')
            ->get();

        return view('expenses.create', compact('shop', 'categories'));
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        // handle file upload
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('expenses', 'public');
        }

        Expense::create([
            'shop_id' => $shop->id,
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'reference' => $validated['reference'] ?? null,
            'description' => $validated['description'] ?? null,
            'attachment' => $path,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);

        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    private function authorizeExpense(Expense $expense)
    {
        if ($expense->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }
    }
}