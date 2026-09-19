<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $cart = \App\Models\Cart::with(['items.product.images' => function($q) { $q->where('is_primary', true); }])->where('user_id', $user->id)->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('catalog.index')->with('error', 'Your cart is empty.');
        }

        return \Inertia\Inertia::render('User/Checkout/Index', [
            'cart' => $cart,
            'addresses' => $user->addresses
        ]);
    }

    public function store(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $cart = \App\Models\Cart::with(['items.product'])->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id'
        ]);

        // Ensure address belongs to user
        $address = $user->addresses()->where('id', $request->address_id)->first();
        if (!$address) {
            return redirect()->back()->with('error', 'Invalid address selected.');
        }

        // Calculate total amount
        $total = 0;
        foreach ($cart->items as $item) {
            $itemPrice = $item->product->price * (1 - $item->product->discount_percentage / 100);
            $total += $itemPrice * $item->quantity;
        }

        // For simplicity, we just create one order per checkout right now
        // A multi-vendor system might split this by vendor, but we'll use the first item's vendor
        $firstItemVendorId = $cart->items->first()->product->vendor_id;
        
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'vendor_id' => $firstItemVendorId,
            'total_amount' => $total,
            'status' => 'pending',
            'shipping_address_id' => $address->id
        ]);

        foreach ($cart->items as $item) {
            $itemPrice = $item->product->price * (1 - $item->product->discount_percentage / 100);
            
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $itemPrice,
            ]);
            
            // Deduct stock
            $item->product->decrement('stock', $item->quantity);
        }

        // Clear cart
        $cart->items()->delete();

        return redirect()->route('user.orders')->with('success', 'Order placed successfully!');
    }
}
