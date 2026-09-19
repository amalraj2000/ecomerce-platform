<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $cart = \App\Models\Cart::with(['items.product.images' => function($q) { $q->where('is_primary', true); }])->firstOrCreate(['user_id' => $user->id]);
        
        return \Inertia\Inertia::render('User/Cart/Index', [
            'cart' => $cart
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string',
            'size' => 'nullable|string'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $cart = \App\Models\Cart::firstOrCreate(['user_id' => $user->id]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'This product is out of stock.');
        }
        
        if ($request->quantity > $product->stock) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $price = $product->price * (1 - $product->discount_percentage / 100);

        // Find existing item with SAME variations, or create new
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('color', $request->color)
            ->where('size', $request->size)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $price,
                'color' => $request->color,
                'size' => $request->size,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function destroy($itemId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $cart = \App\Models\Cart::where('user_id', $user->id)->first();
        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
        }
        return redirect()->back()->with('success', 'Product removed from cart successfully!');
    }
}
