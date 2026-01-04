<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopAdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\SystemAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // System Admin Routes
    Route::get('/admin/dashboard', [SystemAdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/shops/{shop}/approve', [SystemAdminController::class, 'approveShop'])->name('admin.shops.approve');
    Route::post('/admin/shops/{shop}/reject', [SystemAdminController::class, 'rejectShop'])->name('admin.shops.reject');
    Route::get('/admin/shops/create', [SystemAdminController::class, 'createShop'])->name('admin.shops.create');
    Route::post('/admin/shops', [SystemAdminController::class, 'storeShop'])->name('admin.shops.store');
    Route::get('/admin/users', [SystemAdminController::class, 'listUsers'])->name('admin.users.index');

    // Shop Admin Routes
    Route::get('/shop/dashboard', [ShopAdminController::class, 'dashboard'])->name('shop.dashboard');

    // Seller Routes
    Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');

    // Accountant Routes
    Route::get('/accountant/dashboard', [AccountantController::class, 'dashboard'])->name('accountant.dashboard');

    // Resource routes for shop admin
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('purchases/{purchase}/download', [PurchaseController::class, 'downloadPdf'])->name('purchases.download');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('staff', StaffController::class);

    // Stats
    Route::get('/stats', [StatsController::class, 'summary'])->name('stats.summary');
});

require __DIR__.'/auth.php';
