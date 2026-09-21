<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\TransactionController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', function () {
        $vendor = Auth::user()->vendorProfile;

        return Inertia::render('Vendor/Dashboard', [
            'stats' => [
                'products' => $vendor ? Product::where('vendor_id', $vendor->id)->count() : 0,
                'orders' => $vendor ? Order::where('vendor_id', $vendor->id)->count() : 0,
            ],
        ]);
    })->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('chat', [ChatController::class, 'vendorIndex'])->name('chat.index');
});
