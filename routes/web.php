<?php

use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Web\CatalogController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProductController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// Stripe webhook — must be excluded from CSRF
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
require __DIR__.'/channels.php';
