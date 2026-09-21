<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wishlist = Wishlist::with(['items.product.images' => function ($q) {
            $q->where('is_primary', true);
        }])->firstOrCreate(['user_id' => $user->id]);

        return Inertia::render('User/Wishlist/Index', [
            'wishlist' => $wishlist,
        ]);
    }

    public function toggle(Request $request, Product $product)
    {
        $user = Auth::user();
        $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id]);

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->delete();
            $added = false;
            $message = 'Item removed from your wishlist.';
        } else {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
                'added_price' => $product->price * (1 - $product->discount_percentage / 100),
            ]);
            $added = true;
            $message = 'Item added to your wishlist!';
        }

        if ($request->wantsJson()) {
            return response()->json(['added' => $added, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }
}
