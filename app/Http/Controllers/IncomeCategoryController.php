<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of income categories.
     */
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;

        $query = IncomeCategory::where('shop_id', $shopId)
            ->orderBy('name');

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter active/inactive
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->paginate(15)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalCategories = IncomeCategory::where('shop_id', $shopId)
            ->count();

        $activeCategories = IncomeCategory::where('shop_id', $shopId)
            ->where('is_active', true)
            ->count();

        $inactiveCategories = IncomeCategory::where('shop_id', $shopId)
            ->where('is_active', false)
            ->count();

        return view('income_categories.index', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'inactiveCategories'
        ));
    }

    /**
     * Show the form for creating a new income category.
     */
    public function create()
    {
        return view('income_categories.create');
    }

    /**
     * Store a newly created income category.
     */
    public function store(Request $request)
    {
        $shopId = Auth::user()->shop_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('income_categories', 'name')
                    ->where(function ($query) use ($shopId) {
                        return $query->where('shop_id', $shopId);
                    }),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.unique' => 'This income category already exists for this shop.',
        ]);

        $validated['shop_id'] = $shopId;
        $validated['is_active'] = $request->boolean('is_active', true);

        IncomeCategory::create($validated);

        return redirect()
            ->route('income_categories.index')
            ->with(
                'success',
                'Income category created successfully.'
            );
    }

    /**
     * Display the specified income category.
     */
    public function show(IncomeCategory $incomeCategory)
    {
        $this->ensureShopAccess($incomeCategory);

        return view(
            'income_categories.show',
            compact('incomeCategory')
        );
    }

    /**
     * Show the form for editing the specified income category.
     */
    public function edit(IncomeCategory $incomeCategory)
    {
        $this->ensureShopAccess($incomeCategory);

        return view(
            'income_categories.edit',
            compact('incomeCategory')
        );
    }

    /**
     * Update the specified income category.
     */
    public function update(
        Request $request,
        IncomeCategory $incomeCategory
    ) {
        $this->ensureShopAccess($incomeCategory);

        $shopId = Auth::user()->shop_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('income_categories', 'name')
                    ->where(function ($query) use ($shopId) {
                        return $query->where('shop_id', $shopId);
                    })
                    ->ignore($incomeCategory->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.unique' => 'This income category already exists for this shop.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $incomeCategory->update($validated);

        return redirect()
            ->route('income_categories.index')
            ->with(
                'success',
                'Income category updated successfully.'
            );
    }

    /**
     * Remove the specified income category.
     */
    public function destroy(IncomeCategory $incomeCategory)
    {
        $this->ensureShopAccess($incomeCategory);

        /*
        |--------------------------------------------------------------------------
        | Prevent deletion when income records use this category
        |--------------------------------------------------------------------------
        */

        if ($incomeCategory->otherIncomes()->exists()) {
            return redirect()
                ->route('income_categories.index')
                ->with(
                    'error',
                    'This income category cannot be deleted because it is already being used by other income records.'
                );
        }

        $incomeCategory->delete();

        return redirect()
            ->route('income_categories.index')
            ->with(
                'success',
                'Income category deleted successfully.'
            );
    }

    /**
     * Toggle category active/inactive status.
     */
    public function toggleStatus(IncomeCategory $incomeCategory)
    {
        $this->ensureShopAccess($incomeCategory);

        $incomeCategory->update([
            'is_active' => !$incomeCategory->is_active,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Income category status updated successfully.'
            );
    }

    /**
     * Ensure the category belongs to the current user's shop.
     */
    private function ensureShopAccess(
        IncomeCategory $incomeCategory
    ): void {
        if (
            (int) $incomeCategory->shop_id !==
            (int) Auth::user()->shop_id
        ) {
            abort(
                403,
                'You are not authorized to access this income category.'
            );
        }
    }
}

