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

        return Inertia::render('Web/ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}
