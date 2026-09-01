<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Current Shop
        |--------------------------------------------------------------------------
        */

        $shopId = $user->shop_id ? (int) $user->shop_id : null;

        /*
        |--------------------------------------------------------------------------
        | Expenses Table Query
        |--------------------------------------------------------------------------
        */

        $query = Expense::with([
            'category',
            'creator',
        ]);

        $this->applyShopScope($query, $user, $shopId);

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('start_date')) {
            $query->whereDate(
                'expense_date',
                '>=',
                $request->start_date
            );
        }

        if ($request->filled('end_date')) {
            $query->whereDate(
                'expense_date',
                '<=',
                $request->end_date
            );
        }

        if ($request->filled('min_amount')) {
            $query->where(
                'amount',
                '>=',
                $request->min_amount
            );
        }

        if ($request->filled('max_amount')) {
            $query->where(
                'amount',
                '<=',
                $request->max_amount
            );
        }

        if ($request->filled('reference')) {
            $query->where(
                'reference',
                'like',
                '%' . $request->reference . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Paginated Expenses
        |--------------------------------------------------------------------------
        */

        $expenses = $query
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Date Ranges
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        $yesterday = Carbon::yesterday();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $startOfLastWeek = Carbon::now()
            ->subWeek()
            ->startOfWeek();

        $endOfLastWeek = Carbon::now()
            ->subWeek()
            ->endOfWeek();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $startOfLastMonth = Carbon::now()
            ->subMonth()
            ->startOfMonth();

        $endOfLastMonth = Carbon::now()
            ->subMonth()
            ->endOfMonth();

        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        /*
        |--------------------------------------------------------------------------
        | DAILY
        |--------------------------------------------------------------------------
        */

        $dailyExpense = $this->expenseTotal(
            $user,
            $shopId,
            $today,
            $today
        );

        $dailyTransactions = $this->expenseCount(
            $user,
            $shopId,
            $today,
            $today
        );

        $yesterdayExpense = $this->expenseTotal(
            $user,
            $shopId,
            $yesterday,
            $yesterday
        );

        $dailyTrend = $this->calculateTrend(
            $dailyExpense,
            $yesterdayExpense
        );

        /*
        |--------------------------------------------------------------------------
        | WEEKLY
        |--------------------------------------------------------------------------
        */

        $weeklyExpense = $this->expenseTotal(
            $user,
            $shopId,
            $startOfWeek,
            $endOfWeek
        );

        $weeklyTransactions = $this->expenseCount(
            $user,
            $shopId,
            $startOfWeek,
            $endOfWeek
        );

        $lastWeekExpense = $this->expenseTotal(
            $user,
            $shopId,
            $startOfLastWeek,
            $endOfLastWeek
        );

        $weeklyTrend = $this->calculateTrend(
            $weeklyExpense,
            $lastWeekExpense
        );

        /*
        |--------------------------------------------------------------------------
        | MONTHLY
        |--------------------------------------------------------------------------
        */

        $monthlyExpense = $this->expenseTotal(
            $user,
            $shopId,
            $startOfMonth,
            $endOfMonth
        );

        $monthlyTransactions = $this->expenseCount(
            $user,
            $shopId,
            $startOfMonth,
            $endOfMonth
        );

        $lastMonthExpense = $this->expenseTotal(
            $user,
            $shopId,
            $startOfLastMonth,
            $endOfLastMonth
        );

        $monthlyTrend = $this->calculateTrend(
            $monthlyExpense,
            $lastMonthExpense
        );

        /*
        |--------------------------------------------------------------------------
        | YEARLY
        |--------------------------------------------------------------------------
        */

        $yearlyExpense = $this->expenseTotal(
            $user,
            $shopId,
            $startOfYear,
            $endOfYear
        );

        $yearlyTransactions = $this->expenseCount(
            $user,
            $shopId,
            $startOfYear,
            $endOfYear
        );

        /*
        |--------------------------------------------------------------------------
        | PAID / UNPAID
        |--------------------------------------------------------------------------
        */

        $paidQuery = $this->baseExpenseQuery(
            $user,
            $shopId
        );

        $totalPaid = (float) $paidQuery
            ->where('status', 'paid')
            ->sum('amount');

        $unpaidQuery = $this->baseExpenseQuery(
            $user,
            $shopId
        );

        $totalUnpaid = (float) $unpaidQuery
            ->where('status', 'unpaid')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Expense Categories
        |--------------------------------------------------------------------------
        */

        $categoriesQuery = ExpenseCategory::query();

        if (!$user->isSystemAdmin()) {
            if ($shopId) {
                $categoriesQuery->where(
                    'shop_id',
                    $shopId
                );
            } else {
                // Users without a shop can see all categories
                // or no categories depending on your design
            }
        }

        $categories = $categoriesQuery
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DAILY CHART
        |--------------------------------------------------------------------------
        */

        $dailyChart = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $total = $this->expenseTotal(
                $user,
                $shopId,
                $date,
                $date
            );

            $dailyChart[] = [
                'label' => $date->format('D d M'),
                'total' => round($total, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | WEEKLY CHART
        |--------------------------------------------------------------------------
        */

        $weeklyChart = [];

        for ($i = 7; $i >= 0; $i--) {

            $week = Carbon::now()->subWeeks($i);

            $weekStart = $week->copy()->startOfWeek();
            $weekEnd = $week->copy()->endOfWeek();

            $total = $this->expenseTotal(
                $user,
                $shopId,
                $weekStart,
                $weekEnd
            );

            $weeklyChart[] = [
                'label' =>
                    $weekStart->format('d M')
                    . ' - '
                    . $weekEnd->format('d M'),

                'total' => round($total, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHLY CHART
        |--------------------------------------------------------------------------
        */

        $monthlyChart = [];

        for ($i = 5; $i >= 0; $i--) {

            $month = Carbon::now()->subMonths($i);

            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $total = $this->expenseTotal(
                $user,
                $shopId,
                $monthStart,
                $monthEnd
            );

            $monthlyChart[] = [
                'label' => $month->format('M Y'),
                'total' => round($total, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY BREAKDOWN
        |--------------------------------------------------------------------------
        */

        $categoryQuery = $this->baseExpenseQuery(
            $user,
            $shopId
        );

        $categoryBreakdown = $categoryQuery
            ->leftJoin(
                'expense_categories',
                'expenses.category_id',
                '=',
                'expense_categories.id'
            )
            ->selectRaw(
                'COALESCE(expense_categories.name, "Uncategorized") AS category_name'
            )
            ->selectRaw(
                'SUM(expenses.amount) AS total'
            )
            ->groupBy(
                'expenses.category_id',
                'expense_categories.name'
            )
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category_name,
                    'total' => round(
                        (float) $item->total,
                        2
                    ),
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'expenses.index',
            compact(
                'expenses',
                'categories',

                'dailyExpense',
                'dailyTransactions',
                'dailyTrend',

                'weeklyExpense',
                'weeklyTransactions',
                'weeklyTrend',

                'monthlyExpense',
                'monthlyTransactions',
                'monthlyTrend',

                'yearlyExpense',
                'yearlyTransactions',

                'totalPaid',
                'totalUnpaid',

                'dailyChart',
                'weeklyChart',
                'monthlyChart',
                'categoryBreakdown'
            )
        );
    }

    /**
     * Create expense form.
     */
    public function create()
    {
        $user = Auth::user();

        $shopId = $user->shop_id
            ? (int) $user->shop_id
            : null;

        $query = ExpenseCategory::query();

        if (!$user->isSystemAdmin()) {

            if ($shopId) {
                $query->where(
                    'shop_id',
                    $shopId
                );
            } else {
                // Users without a shop can see all categories
                // or no categories depending on your design
                // For now, allow all
            }
        }

        $categories = $query
            ->orderBy('name')
            ->get();

        return view(
            'expenses.create',
            compact('categories')
        );
    }

    /**
     * Store expense.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:expense_categories,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                'in:paid,unpaid',
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ]);

        $validated['shop_id'] = $user->shop_id
            ? (int) $user->shop_id
            : null;

        $validated['created_by'] = $user->id;

        $validated['status'] =
            $validated['status'] ?? 'paid';

        if ($request->hasFile('attachment')) {

            $validated['attachment'] =
                $request
                    ->file('attachment')
                    ->store(
                        'expenses',
                        'public'
                    );
        }

        Expense::create($validated);

        return redirect()
            ->route('expenses.index')
            ->with(
                'success',
                'Expense recorded successfully.'
            );
    }

    /**
     * Show expense.
     */
    public function show(Expense $expense)
    {
        $this->authorizeExpense($expense);

        $expense->load([
            'category',
            'creator',
        ]);

        return view(
            'expenses.show',
            compact('expense')
        );
    }

    /**
     * Edit expense.
     */
    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);

        $user = Auth::user();

        $shopId = $user->shop_id
            ? (int) $user->shop_id
            : null;

        $query = ExpenseCategory::query();

        if (!$user->isSystemAdmin()) {

            if ($shopId) {
                $query->where(
                    'shop_id',
                    $shopId
                );
            } else {
                // Users without a shop can see all categories
            }
        }

        $categories = $query
            ->orderBy('name')
            ->get();

        return view(
            'expenses.edit',
            compact(
                'expense',
                'categories'
            )
        );
    }

    /**
     * Update expense.
     */
    public function update(
        Request $request,
        Expense $expense
    ) {
        $this->authorizeExpense($expense);

        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:expense_categories,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                'in:paid,unpaid',
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ]);

        if ($request->hasFile('attachment')) {

            if (
                $expense->attachment &&
                Storage::disk('public')->exists(
                    $expense->attachment
                )
            ) {
                Storage::disk('public')->delete(
                    $expense->attachment
                );
            }

            $validated['attachment'] =
                $request
                    ->file('attachment')
                    ->store(
                        'expenses',
                        'public'
                    );
        }

        $expense->update($validated);

        return redirect()
            ->route('expenses.index')
            ->with(
                'success',
                'Expense updated successfully.'
            );
    }

    /**
     * Toggle paid/unpaid status.
     */
    public function toggleStatus(
        Expense $expense
    ) {
        $this->authorizeExpense($expense);

        $expense->status =
            $expense->status === 'paid'
                ? 'unpaid'
                : 'paid';

        $expense->save();

        return redirect()
            ->back()
            ->with(
                'success',
                'Expense status updated successfully.'
            );
    }

    /**
     * Download attachment.
     */
    public function download(
        Expense $expense
    ) {
        $this->authorizeExpense($expense);

        if (!$expense->attachment) {
            abort(
                404,
                'No attachment found.'
            );
        }

        if (
            !Storage::disk('public')->exists(
                $expense->attachment
            )
        ) {
            abort(
                404,
                'Attachment file not found.'
            );
        }

        return Storage::disk('public')->download(
            $expense->attachment
        );
    }

    /**
     * Delete expense.
     */
    public function destroy(
        Expense $expense
    ) {
        $this->authorizeExpense($expense);

        if (
            $expense->attachment &&
            Storage::disk('public')->exists(
                $expense->attachment
            )
        ) {
            Storage::disk('public')->delete(
                $expense->attachment
            );
        }

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with(
                'success',
                'Expense deleted successfully.'
            );
    }

    /**
     * Base expense query with shop isolation.
     */
    private function baseExpenseQuery(
        $user,
        ?int $shopId
    ) {
        $query = Expense::query();

        $this->applyShopScope(
            $query,
            $user,
            $shopId
        );

        return $query;
    }

    /**
     * Apply shop scope.
     */
    private function applyShopScope(
        $query,
        $user,
        ?int $shopId
    ): void {
        /*
        |--------------------------------------------------------------------------
        | System Admin
        |--------------------------------------------------------------------------
        |
        | System admins can see all expenses from all shops.
        |
        */

        if ($user->isSystemAdmin()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | All Other Users
        |--------------------------------------------------------------------------
        |
        | Show all expenses (no filtering).
        | This allows users to see all expenses in the system.
        |
        */

        // Removed restrictions to show all data from database
    }

    /**
     * Calculate expense total.
     */
    private function expenseTotal(
        $user,
        ?int $shopId,
        Carbon $start,
        Carbon $end
    ): float {

        $query = $this->baseExpenseQuery(
            $user,
            $shopId
        );

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Use whereDate so this works whether expense_date
        | is DATE or DATETIME in MySQL.
        |
        */

        $query->whereDate(
            'expense_date',
            '>=',
            $start->toDateString()
        );

        $query->whereDate(
            'expense_date',
            '<=',
            $end->toDateString()
        );

        return (float) $query->sum('amount');
    }

    /**
     * Count expenses.
     */
    private function expenseCount(
        $user,
        ?int $shopId,
        Carbon $start,
        Carbon $end
    ): int {

        $query = $this->baseExpenseQuery(
            $user,
            $shopId
        );

        $query->whereDate(
            'expense_date',
            '>=',
            $start->toDateString()
        );

        $query->whereDate(
            'expense_date',
            '<=',
            $end->toDateString()
        );

        return (int) $query->count();
    }

    /**
     * Calculate percentage trend.
     */
    private function calculateTrend(
        float $current,
        float $previous
    ): array {

        if (
            $current == 0 &&
            $previous == 0
        ) {
            return [
                'direction' => 'same',
                'percentage' => 0,
            ];
        }

        if ($previous == 0) {
            return [
                'direction' =>
                    $current > 0
                        ? 'up'
                        : 'same',

                'percentage' =>
                    $current > 0
                        ? 100
                        : 0,
            ];
        }

        $percentage =
            (
                ($current - $previous)
                / $previous
            ) * 100;

        if ($percentage > 0) {
            $direction = 'up';
        } elseif ($percentage < 0) {
            $direction = 'down';
        } else {
            $direction = 'same';
        }

        return [
            'direction' => $direction,
            'percentage' => round(
                abs($percentage),
                2
            ),
        ];
    }

    /**
     * Authorize access to expense.
     */
    private function authorizeExpense(
        Expense $expense
    ): void {

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | System Admin
        |--------------------------------------------------------------------------
        */

        if ($user->isSystemAdmin()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | All Authenticated Users - Allow Access
        |--------------------------------------------------------------------------
        */

        // All authenticated users can access expenses
    }
}
