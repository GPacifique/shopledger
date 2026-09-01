<?php

// Add this temporary route to debug.php for debugging
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

Route::get('/debug/expenses', function () {
    $user = Auth::user();
    
    return [
        'user_id' => $user->id,
        'user_shop_id' => $user->shop_id,
        'expenses_count' => Expense::count(),
        'user_expenses' => Expense::where('created_by', $user->id)->get(['id', 'shop_id', 'amount', 'expense_date'])->toArray(),
        'expenses_by_shop' => Expense::where('shop_id', $user->shop_id)->count(),
        'expenses_null_shop' => Expense::whereNull('shop_id')->count(),
        'today_expenses' => Expense::whereDate('expense_date', now()->toDateString())
            ->where('created_by', $user->id)->get(['id', 'shop_id', 'amount', 'expense_date'])->toArray(),
    ];
})->middleware('auth');
