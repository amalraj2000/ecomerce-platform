<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'orders' => Order::count(),
                'vendors' => Vendor::count(),
                'users' => User::where('role', 'user')->count(),
                'products' => Product::count(),
            ],
        ]);
    })->name('dashboard');

    Route::get('vendors/pending', [VendorController::class, 'pending'])->name('vendors.pending');
    Route::post('vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('coupons', CouponController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('orders/{order}/refund', [OrderController::class, 'refund'])->name('orders.refund');
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
});
