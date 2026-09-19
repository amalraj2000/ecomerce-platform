<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return \Inertia\Inertia::render('Admin/Dashboard', [
            'stats' => [
                'orders' => \App\Models\Order::count(),
                'vendors' => \App\Models\Vendor::count(),
                'users' => \App\Models\User::where('role', 'user')->count(),
                'products' => \App\Models\Product::count(),
            ]
        ]);
    })->name('dashboard');

    Route::get('vendors/pending', [\App\Http\Controllers\Admin\VendorController::class, 'pending'])->name('vendors.pending');
    Route::post('vendors/{vendor}/approve', [\App\Http\Controllers\Admin\VendorController::class, 'approve'])->name('vendors.approve');

    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('vendors', \App\Http\Controllers\Admin\VendorController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
});
