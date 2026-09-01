<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use App\Models\OtherIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtherIncomeController extends Controller
{
    /**
     * Display a listing of other incomes.
     */
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;

        /*
        |--------------------------------------------------------------------------
        | Other Income Records
        |--------------------------------------------------------------------------
        */

        $query = OtherIncome::with(['category', 'creator'])
            ->where('shop_id', $shopId)
            ->latest('income_date')
            ->latest('id');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Category filter
        if ($request->filled('income_category_id')) {
            $query->where(
                'income_category_id',
                $request->income_category_id
            );
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // From date
        if ($request->filled('from_date')) {
            $query->whereDate(
                'income_date',
                '>=',
                $request->from_date
            );
        }

        // To date
        if ($request->filled('to_date')) {
            $query->whereDate(
                'income_date',
                '<=',
                $request->to_date
            );
        }

        $otherIncomes = $query
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Income Categories
        |--------------------------------------------------------------------------
        */

        $categories = IncomeCategory::where('shop_id', $shopId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Date Ranges
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        /*
        |--------------------------------------------------------------------------
        | DAILY INCOME
        |--------------------------------------------------------------------------
        */

        $dailyIncome = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereDate('income_date', $today)
            ->sum('amount');

        $dailyTransactions = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereDate('income_date', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | WEEKLY INCOME
        |--------------------------------------------------------------------------
        */

        $weeklyIncome = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereBetween('income_date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->sum('amount');

        $weeklyTransactions = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereBetween('income_date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->count();

        /*
        /*
|--------------------------------------------------------------------------
| MONTHLY CHART - LAST 6 MONTHS
|--------------------------------------------------------------------------
*/

$sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

$rawMonthlyData = OtherIncome::where('shop_id', $shopId)
    ->where('status', 'received')
    ->where('income_date', '>=', $sixMonthsAgo->toDateString())
    ->selectRaw("DATE_FORMAT(income_date, '%Y-%m') as ym, SUM(amount) as total")
    ->groupBy('ym')
    ->get()
    ->mapWithKeys(fn ($row) => [$row->ym => (float) $row->total]);

$sixMonthChart = collect(range(5, 0))->map(function ($monthsAgo) use ($rawMonthlyData) {
    $month = Carbon::now()->subMonths($monthsAgo);
    $key = $month->format('Y-m');

    return [
        'label' => $month->format('M Y'),
        'total' => $rawMonthlyData[$key] ?? 0,
    ];
});

        $monthlyTransactions = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereBetween('income_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | YEARLY INCOME
        |--------------------------------------------------------------------------
        */

        $yearlyIncome = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereBetween('income_date', [
                $startOfYear->toDateString(),
                $endOfYear->toDateString(),
            ])
            ->sum('amount');

        $yearlyTransactions = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereBetween('income_date', [
                $startOfYear->toDateString(),
                $endOfYear->toDateString(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TOTAL RECEIVED
        |--------------------------------------------------------------------------
        */

        $totalReceived = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | TOTAL PENDING
        |--------------------------------------------------------------------------
        */

        $totalPending = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'pending')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | TOTAL CANCELLED
        |--------------------------------------------------------------------------
        */

        $totalCancelled = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'cancelled')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | CATEGORY BREAKDOWN
        |--------------------------------------------------------------------------
        */

        $categoryBreakdown = OtherIncome::where('other_incomes.shop_id', $shopId)
            ->where('other_incomes.status', 'received')
            ->leftJoin(
                'income_categories',
                'other_incomes.income_category_id',
                '=',
                'income_categories.id'
            )
            ->selectRaw(
                'COALESCE(income_categories.name, "Uncategorized") as category_name,
                 SUM(other_incomes.amount) as total'
            )
            ->groupBy('income_categories.id', 'income_categories.name')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DAILY CHART - LAST 7 DAYS
        |--------------------------------------------------------------------------
        */

        $dailyChart = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereDate(
                'income_date',
                '>=',
                Carbon::today()->subDays(6)
            )
            ->selectRaw('income_date, SUM(amount) as total')
            ->groupBy('income_date')
            ->orderBy('income_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MONTHLY CHART - CURRENT YEAR
        |--------------------------------------------------------------------------
        */

        $monthlyChart = OtherIncome::where('shop_id', $shopId)
            ->where('status', 'received')
            ->whereYear('income_date', Carbon::now()->year)
            ->selectRaw(
                'MONTH(income_date) as month,
                 SUM(amount) as total'
            )
            ->groupByRaw('MONTH(income_date)')
            ->orderByRaw('MONTH(income_date)')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('other_incomes.index', compact(
            'otherIncomes',
            'categories',

            // Statistics
            'dailyIncome',
            'weeklyIncome',
            'monthlyIncome',
            'yearlyIncome',

            // Transaction counts
            'dailyTransactions',
            'weeklyTransactions',
            'monthlyTransactions',
            'yearlyTransactions',

            // Totals
            'totalReceived',
            'totalPending',
            'totalCancelled',

            // Reports
            'categoryBreakdown',
            'dailyChart',
            'monthlyChart'
        ));
    }

    /**
     * Show the form for creating a new other income.
     */
    public function create()
    {
        $shopId = Auth::user()->shop_id;

        $categories = IncomeCategory::where('shop_id', $shopId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('other_incomes.create', compact('categories'));
    }

    /**
     * Store a newly created other income.
     */
    public function store(Request $request)
    {
        $shopId = Auth::user()->shop_id;

        $validated = $request->validate([
            'income_category_id' => [
                'nullable',
                'integer',
                'exists:income_categories,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'income_date' => [
                'required',
                'date',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:received,pending,cancelled',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Category Belongs To Current Shop
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['income_category_id'])) {

            $categoryExists = IncomeCategory::where('id', $validated['income_category_id'])
                ->where('shop_id', $shopId)
                ->where('is_active', true)
                ->exists();

            if (!$categoryExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'income_category_id' => 'The selected income category is invalid.',
                    ]);
            }
        }

        $validated['shop_id'] = $shopId;
        $validated['created_by'] = Auth::id();

        OtherIncome::create($validated);

        return redirect()
            ->route('other_incomes.index')
            ->with(
                'success',
                'Other income recorded successfully.'
            );
    }

    /**
     * Display the specified other income.
     */
    public function show(OtherIncome $otherIncome)
    {
        $this->ensureShopAccess($otherIncome);

        $otherIncome->load([
            'category',
            'creator',
            'shop',
        ]);

        return view(
            'other_incomes.show',
            compact('otherIncome')
        );
    }

    /**
     * Show the form for editing the specified other income.
     */
    public function edit(OtherIncome $otherIncome)
    {
        $this->ensureShopAccess($otherIncome);

        $shopId = Auth::user()->shop_id;

        $categories = IncomeCategory::where('shop_id', $shopId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'other_incomes.edit',
            compact(
                'otherIncome',
                'categories'
            )
        );
    }

    /**
     * Update the specified other income.
     */
    public function update(
        Request $request,
        OtherIncome $otherIncome
    ) {
        $this->ensureShopAccess($otherIncome);

        $shopId = Auth::user()->shop_id;

        $validated = $request->validate([
            'income_category_id' => [
                'nullable',
                'integer',
                'exists:income_categories,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'income_date' => [
                'required',
                'date',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:received,pending,cancelled',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Category Belongs To Current Shop
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['income_category_id'])) {

            $categoryExists = IncomeCategory::where('id', $validated['income_category_id'])
                ->where('shop_id', $shopId)
                ->where('is_active', true)
                ->exists();

            if (!$categoryExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'income_category_id' => 'The selected income category is invalid.',
                    ]);
            }
        }

        $otherIncome->update($validated);

        return redirect()
            ->route('other_incomes.index')
            ->with(
                'success',
                'Other income updated successfully.'
            );
    }

    /**
     * Delete the specified other income.
     */
    public function destroy(OtherIncome $otherIncome)
    {
        $this->ensureShopAccess($otherIncome);

        $otherIncome->delete();

        return redirect()
            ->route('other_incomes.index')
            ->with(
                'success',
                'Other income deleted successfully.'
            );
    }

    /**
     * Ensure the income belongs to the current user's shop.
     */
    private function ensureShopAccess(
        OtherIncome $otherIncome
    ): void {
        if (
            (int) $otherIncome->shop_id !==
            (int) Auth::user()->shop_id
        ) {
            abort(
                403,
                'You are not authorized to access this income record.'
            );
        }
    }
}
