<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
    Route::get('/orders', [\App\Http\Controllers\User\OrderController::class, 'index'])->name('user.orders');
    Route::resource('addresses', \App\Http\Controllers\User\AddressController::class);
    
    // Cart and Checkout
    Route::get('/cart', [\App\Http\Controllers\User\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\User\CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{item}', [\App\Http\Controllers\User\CartController::class, 'destroy'])->name('cart.destroy');
    
    Route::get('/checkout', [\App\Http\Controllers\User\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [\App\Http\Controllers\User\CheckoutController::class, 'store'])->name('checkout.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\User\ProfileController::class, 'destroy'])->name('profile.destroy');
});
