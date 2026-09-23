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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Check if user has purchased this product
        $hasPurchased = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'paid', 'accepted', 'processing', 'shipped', 'out_for_delivery', 'delivered'])
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->exists();

        if (! $hasPurchased && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Only verified buyers who have ordered this product can write a review.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('reviews', 'public');
                $imagePaths[] = '/storage/'.$path;
            }
        }

        $review = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($review) {
            $existingImages = $review->images ?? [];
            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => ! empty($imagePaths) ? array_merge($existingImages, $imagePaths) : $existingImages,
            ]);
        } else {
            Review::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $imagePaths,
                'helpful_votes' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Thank you! Your review has been published.');
    }

    public function vote(Review $review)
    {
        $review->increment('helpful_votes');

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
}
