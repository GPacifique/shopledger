<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopAdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\SystemAdminController;
use App\Http\Controllers\LanguageController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WaiterController;

Route::get('/shops/{shop}/orders/{order}/bill', [WaiterController::class, 'downloadBill'])
    ->name('shops.orders.bill.download');

Route::get('/shops/{shop}/orders/{order}/print', [WaiterController::class, 'printOrder'])
    ->name('shops.orders.print');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('shops/{shop}')->name('shops.')->group(function () {
Route::get('orders/take', [OrderController::class, 'create'])
    ->name('orders.waiter');
        Route::get('waiter-dashboard', [WaiterController::class, 'dashboard'])
            ->name('waiter.dashboard');

        Route::get('orders', [OrderController::class, 'index'])
            ->name('orders.index');
           

        Route::get('orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::post('orders', [OrderController::class, 'store'])
            ->name('orders.store');

        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');

        Route::post('orders/{order}/approve', [OrderController::class, 'approve'])
            ->name('orders.approve');

        Route::post('orders/{order}/reject', [OrderController::class, 'reject'])
            ->name('orders.reject');
    });

});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('shops/{shop}')->name('shops.')->group(function () {

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // waiter actions
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // seller actions
        Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    });
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('shops/{shop}')->name('shops.')->group(function () {

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status');

        Route::post('orders/{order}/payments', [OrderController::class, 'recordPayment'])
            ->name('orders.payments.store');
    });
});



Route::middleware(['auth', 'verified', 'role:system_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SystemAdminController::class, 'settings'])->name('settings');
    Route::get('/shops', [SystemAdminController::class, 'shopsIndex'])->name('shops.index');
Route::get('/shops/create', [SystemAdminController::class, 'createShop'])->name('shops.create');
Route::post('/shops', [SystemAdminController::class, 'storeShop'])->name('shops.store');
    Route::post('/settings', [SystemAdminController::class, 'updateSettings'])->name('settings.update');
});
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Language Switch Route
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    // System admin dashboard
    if ($user->role === 'system_admin') {
        return redirect()->route('admin.dashboard');
    }
//waiter dashboard
if ($user->role==='waiter'){
    return redirect()->route('shops.waiter.dashboard', ['shop' => auth()->user()->shop]);
    }
    // Shop admin dashboard
    if ($user->role === 'shop_admin') {
        return redirect()->route('shop.dashboard');
    }

    // Seller dashboard
    if ($user->role === 'seller') {
        return redirect()->route('seller.dashboard');
    }

    // Accountant dashboard
    if ($user->role === 'accountant') {
        return redirect()->route('accountant.dashboard');
    }

    // User with pending shop or no shop - show under review
    $shop = $user->shop;
    return view('dashboard.user', compact('shop'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shop Admin Routes - Only accessible by shop_admin
    Route::middleware(RoleMiddleware::class.':shop_admin')->group(function () {
        Route::get('/shop/dashboard', [ShopAdminController::class, 'dashboard'])->name('shop.dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('staff', StaffController::class);
        Route::resource('products',ProductController::class);
        Route::resource('expensecategories', ExpenseCategoryController::class);
        Route::get('/stats', [StatsController::class, 'summary'])->name('stats.summary');
    });

    Route::middleware(RoleMiddleware::class.':shop_admin,seller')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        Route::get('products/{product}/qr-code', [ProductController::class, 'qrCode'])->name('products.qr-code');
        Route::resource('expenses', ExpenseController::class)->only(['index']);
    });

    Route::middleware(RoleMiddleware::class.':shop_admin')->group(function () {
        Route::resource('expenses', ExpenseController::class)->except(['index', 'show']);
        Route::resource('products', ProductController::class)->except(['index', 'edit','create','show']);
    
    });

    // Sales and purchases are accessible to both shop_admin and seller
    Route::middleware(RoleMiddleware::class.':shop_admin,seller')->group(function () {
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::get('purchases/{purchase}/download', [PurchaseController::class, 'downloadPdf'])->name('purchases.download');
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
        Route::get('sales/{sale}/export', [SaleController::class, 'export'])->name('sales.export');
    });

    // System Admin Routes - Only accessible by system_admin
    Route::middleware(RoleMiddleware::class.':system_admin')->group(function () {
        Route::get('/admin/dashboard', [SystemAdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/admin/shops/{shop}/approve', [SystemAdminController::class, 'approveShop'])->name('admin.shops.approve');
        Route::post('/admin/shops/{shop}/reject', [SystemAdminController::class, 'rejectShop'])->name('admin.shops.reject');
        Route::get('/admin/shops/create', [SystemAdminController::class, 'createShop'])->name('admin.shops.create');
        Route::post('/admin/shops', [SystemAdminController::class, 'storeShop'])->name('admin.shops.store');
        Route::get('/admin/users', [SystemAdminController::class, 'listUsers'])->name('admin.users.index');
        Route::get('/admin/users/{user}/edit', [SystemAdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [SystemAdminController::class, 'updateUser'])->name('admin.users.update');
    });

    Route::middleware(RoleMiddleware::class.':seller')->group(function () {
        Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    });

    // Accountant Routes - Only accessible by accountant
    Route::middleware(RoleMiddleware::class.':accountant')->group(function () {
        Route::get('/accountant/dashboard', [AccountantController::class, 'dashboard'])->name('accountant.dashboard');
    });
});

require __DIR__.'/auth.php';
