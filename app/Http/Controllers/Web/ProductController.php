<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with(['vendor', 'category', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(5)
            ->get();

        $user = \Illuminate\Support\Facades\Auth::user();
        $hasBought = false;
        if ($user) {
            $hasBought = \App\Models\OrderItem::whereHas('order', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('product_id', $product->id)->exists();
        }

        return Inertia::render('Web/ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'hasBought' => $hasBought
        ]);
    }
}
