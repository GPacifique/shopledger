<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'status' => 'paid', // default status
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
    if ($expense->shop_id !== auth()->user()->shop_id) {
        abort(403, 'Unauthorized action.');
    }
}
public function downloadAttachment(Expense $expense)
{
    $this->authorizeExpense($expense);

    if (!$expense->attachment) {
        abort(404, 'No attachment found.');
    }

    return Storage::disk('public')->download($expense->attachment);
}
public function toggleStatus(Expense $expense)
{
    $this->authorizeExpense($expense);

    $expense->status = $expense->status === 'paid' ? 'unpaid' : 'paid';
    $expense->save();

    return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense status updated successfully.');

}
public function filterByCategory(Request $request)
{
    $shop = auth()->user()->shop;

    $categoryId = $request->input('category_id');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($categoryId, function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
public function filterByDate(Request $request)
{
    $shop = auth()->user()->shop;

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($startDate, function ($query) use ($startDate) {
            return $query->whereDate('expense_date', '>=', $startDate);
        })
        ->when($endDate, function ($query) use ($endDate) {
            return $query->whereDate('expense_date', '<=', $endDate);
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
public function filterByStatus(Request $request)
{
    $shop = auth()->user()->shop;

    $status = $request->input('status');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
public function filterByReference(Request $request)
{
    $shop = auth()->user()->shop;

    $reference = $request->input('reference');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($reference, function ($query) use ($reference) {
            return $query->where('reference', 'like', "%{$reference}%");
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
public function filterByAmount(Request $request)
{
    $shop = auth()->user()->shop;

    $minAmount = $request->input('min_amount');
    $maxAmount = $request->input('max_amount');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($minAmount, function ($query) use ($minAmount) {
            return $query->where('amount', '>=', $minAmount);
        })
        ->when($maxAmount, function ($query) use ($maxAmount) {
            return $query->where('amount', '<=', $maxAmount);
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
public function filterByUser(Request $request)
{
    $shop = auth()->user()->shop;

    $userId = $request->input('user_id');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($userId, function ($query) use ($userId) {
            return $query->where('created_by', $userId);
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
public function filterByStatusAndDate(Request $request)
{
    $shop = auth()->user()->shop;

    $status = $request->input('status');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $expenses = Expense::where('shop_id', $shop->id)
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when($startDate, function ($query) use ($startDate) {
            return $query->whereDate('expense_date', '>=', $startDate);
        })
        ->when($endDate, function ($query) use ($endDate) {
            return $query->whereDate('expense_date', '<=', $endDate);
        })
        ->with('category')
        ->orderBy('expense_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('expenses.index', compact('expenses', 'shop'));
}
}