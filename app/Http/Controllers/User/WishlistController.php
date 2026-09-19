<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $wishlist = \App\Models\Wishlist::with(['items.product.images'])->firstOrCreate(['user_id' => $user->id]);

        return \Inertia\Inertia::render('User/Wishlist/Index', [
            'wishlist' => $wishlist
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $wishlist = \App\Models\Wishlist::firstOrCreate(['user_id' => $user->id]);
        $product = \App\Models\Product::findOrFail($request->product_id);
        $price = $product->price * (1 - $product->discount_percentage / 100);

        $existing = $wishlist->items()->where('product_id', $product->id)->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Product is already in your wishlist.');
        }

        $wishlist->items()->create([
            'product_id' => $product->id,
            'added_price' => $price
        ]);

        return redirect()->back()->with('success', 'Product added to wishlist!');
    }

    public function destroy($itemId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $wishlist = \App\Models\Wishlist::where('user_id', $user->id)->first();
        if ($wishlist) {
            $wishlist->items()->where('id', $itemId)->delete();
        }
        return redirect()->back()->with('success', 'Product removed from wishlist!');
    }
}
