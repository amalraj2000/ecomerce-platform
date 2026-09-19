<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $topDeals = Product::with('vendor', 'images')->orderByDesc('discount_percentage')->take(5)->get();
        $trending = Product::with('vendor', 'images')->inRandomOrder()->take(5)->get();
        $categories = Category::whereNull('parent_id')->get();

        return Inertia::render('Web/Home', [
            'topDeals' => $topDeals,
            'trending' => $trending,
            'categories' => $categories
        ]);
    }
}
