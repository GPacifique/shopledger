<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopAdminController;
use App\Http\Controllers\ShopController;
Use App\Http\Controllers\SubscriptionPlanController;
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
Use App\Http\Controllers\CategoryController;
Route::resource('products', ProductController::class);
Route::get('products/{product}/qr-code', [ProductController::class, 'qrCode'])->name('products.qr-code');
Route::resource('categories', CategoryController::class)
    ->except(['show'])
    ->middleware(['auth', 'verified']);
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

Route::resource('expenses', ExpenseController::class);
Route::resource('expensecategories',ExpenseCategoryController::class);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Language Switch Route
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('admin')->middleware(['auth','system.admin'])->group(function () {

    Route::get('/dashboard',
        [SystemAdminController::class,'dashboard']
    )->name('admin.dashboard');

    Route::post('/shops/{shop}/approve',
        [SystemAdminController::class,'approveShop']
    );

    Route::post('/shops/{shop}/reject',
        [SystemAdminController::class,'rejectShop']
    );

    Route::post('/shops/{shop}/suspend',
        [SystemAdminController::class,'suspendShop']
    );

    Route::post('/shops/{shop}/reactivate',
        [SystemAdminController::class,'reactivateShop']
    );

    Route::get('/subscriptions',
        [SystemAdminController::class,'subscriptions']
    );

    Route::get('/payments',
        [SystemAdminController::class,'payments']
    );

    Route::post('/payments/{id}/approve',
        [SystemAdminController::class,'approvePayment']
    );

    Route::post('/payments/{id}/reject',
        [SystemAdminController::class,'rejectPayment']
    );

    Route::get('/analytics',
        [SystemAdminController::class,'analytics']
    );

    Route::get('/revenue-report',
        [SystemAdminController::class,'revenueReport']
    );

    Route::get('/revenue-pdf',
        [SystemAdminController::class,'downloadRevenuePdf']
    );

    Route::get('/expired-subscriptions',
        [SystemAdminController::class,'expiredSubscriptions']
    );
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    // System admin dashboard
    if ($user->role === 'system_admin') {
        return redirect()->route('admin.dashboard');
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

    // Shop Admin Routes - Only accessible by shop_admin
    Route::middleware(RoleMiddleware::class.':shop_admin')->group(function () {
        Route::get('/shop/dashboard', [ShopAdminController::class, 'dashboard'])->name('shop.dashboard');
    });

    // Seller Routes - Only accessible by seller
    Route::middleware(RoleMiddleware::class.':seller')->group(function () {
        Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    });

    // Accountant Routes - Only accessible by accountant
    Route::middleware(RoleMiddleware::class.':accountant')->group(function () {
        Route::get('/accountant/dashboard', [AccountantController::class, 'dashboard'])->name('accountant.dashboard');
    });

    // Resource routes for shop admin
    Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('products/{product}/qr-code', [ProductController::class, 'qrCode'])->name('products.qr-code');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('purchases/{purchase}/download', [PurchaseController::class, 'downloadPdf'])->name('purchases.download');
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::get('sales/{sale}/export', [SaleController::class, 'export'])->name('sales.export');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::resource('staff', StaffController::class);

    // Stats
    Route::get('/stats', [StatsController::class, 'summary'])->name('stats.summary');
});

require __DIR__.'/auth.php';
