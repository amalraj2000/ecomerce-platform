<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user has purchased this product
        $hasPurchased = Order::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'accepted', 'processing', 'shipped', 'out_for_delivery', 'delivered'])
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->exists();

        if (! $hasPurchased && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'You can only review products you have purchased.');
        }

        Review::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return redirect()->back()->with('success', 'Thank you! Your review has been published.');
    }
}
