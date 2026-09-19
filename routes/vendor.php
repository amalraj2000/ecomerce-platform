<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', function () {
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendorProfile;
        return Inertia::render('Vendor/Dashboard', [
            'stats' => [
                'products' => $vendor ? \App\Models\Product::where('vendor_id', $vendor->id)->count() : 0,
                'orders' => $vendor ? \App\Models\Order::where('vendor_id', $vendor->id)->count() : 0,
            ]
        ]);
    })->name('dashboard');
    Route::resource('products', \App\Http\Controllers\Vendor\ProductController::class);
    Route::resource('orders', \App\Http\Controllers\Vendor\OrderController::class);
});
