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
            'cart' => $cart
        ]);
    }

    public function store(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $cart = \App\Models\Cart::with(['items.product'])->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'Cart is empty');
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
        
        // Auto-create a mock address if the user doesn't have one
        $address = \App\Models\Address::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'phone' => '555-0100',
                'street' => '123 Main St',
                'city' => 'Metropolis',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA',
                'is_default' => true
            ]
        );

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
