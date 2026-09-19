<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\Web\HomeController::class, 'index'])->name('home');
Route::get('/catalog', [\App\Http\Controllers\Web\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/product/{slug}', [\App\Http\Controllers\Web\ProductController::class, 'show'])->name('product.show');

require __DIR__.'/auth.php';
