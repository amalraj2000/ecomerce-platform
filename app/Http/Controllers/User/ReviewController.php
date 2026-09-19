<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Check if user bought the product (Verified Buyer logic)
        $user = \Illuminate\Support\Facades\Auth::user();
        $hasBought = \App\Models\OrderItem::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('product_id', $request->product_id)->exists();

        // Even if they didn't buy, let's let them review for now but we will use this logic in the frontend to show the badge

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $imagePaths[] = '/storage/' . $path;
            }
        }

        \App\Models\Review::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $imagePaths
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    public function vote(Request $request, $id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->increment('helpful_votes');
        return redirect()->back();
    }
}
