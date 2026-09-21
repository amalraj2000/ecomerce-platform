<?php

namespace App\Http\Controllers\User;

use App\Events\OrderStatusUpdated;
use App\Events\StockUpdated;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StripeWebhookController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = Cart::with(['items.product.images' => function ($q) {
            $q->where('is_primary', true);
        }])->where('user_id', $user->id)->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('catalog.index')->with('error', 'Your cart is empty.');
        }

        return Inertia::render('User/Checkout/Index', [
            'cart' => $cart,
            'addresses' => $user->addresses,
            'stripeKey' => config('stripe.key'),
        ]);
    }

    /**
     * Handle order placement (COD or Stripe Online Payment).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::with(['items.product'])->where('user_id', $user->id)->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:stripe,cod',
        ]);

        $address = $user->addresses()->where('id', $request->address_id)->first();
        if (! $address) {
            return redirect()->back()->with('error', 'Invalid address selected.');
        }

        // Cash on Delivery (COD)
        if ($request->payment_method === 'cod') {
            DB::transaction(function () use ($cart, $user, $address) {
                $total = $cart->items->sum(function ($item) {
                    return $item->product->price
                        * (1 - $item->product->discount_percentage / 100)
                        * $item->quantity;
                });

                $firstItemVendorId = $cart->items->first()->product->vendor_id;

                $order = Order::create([
                    'user_id' => $user->id,
                    'vendor_id' => $firstItemVendorId,
                    'total_amount' => $total,
                    'status' => 'pending',
                    'payment_method' => 'cod',
                    'shipping_address_id' => $address->id,
                ]);

                foreach ($cart->items as $item) {
                    $itemPrice = $item->product->price * (1 - $item->product->discount_percentage / 100);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $itemPrice,
                    ]);

                    // Deduct stock and broadcast live update
                    $item->product->decrement('stock', $item->quantity);
                    try {
                        broadcast(new StockUpdated($item->product_id, $item->product->fresh()->stock));
                    } catch (\Exception $e) {
                        Log::warning('Reverb broadcast warning for stock update: '.$e->getMessage());
                    }
                }

                // Clear cart
                $cart->items()->delete();

                // Broadcast order status update
                try {
                    broadcast(new OrderStatusUpdated($order));
                } catch (\Exception $e) {
                    Log::warning('Reverb broadcast warning for order status: '.$e->getMessage());
                }
            });

            return redirect()->route('checkout.success')->with('success', 'Order placed successfully with Cash on Delivery!');
        }

        // Stripe Online Payment
        Stripe::setApiKey(config('stripe.secret'));

        $lineItems = $cart->items->map(function ($item) {
            $unitAmount = (int) round(
                $item->product->price * (1 - $item->product->discount_percentage / 100) * 100
            );

            return [
                'price_data' => [
                    'currency' => config('stripe.currency', 'usd'),
                    'product_data' => [
                        'name' => $item->product->title,
                    ],
                    'unit_amount' => $unitAmount,
                ],
                'quantity' => $item->quantity,
            ];
        })->values()->toArray();

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'payment_method' => 'stripe',
            ],
        ]);

        session(['stripe_checkout_session_id' => $session->id]);

        return Inertia::location($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            try {
                Stripe::setApiKey(config('stripe.secret'));
                $session = StripeSession::retrieve($sessionId);

                if ($session && $session->payment_status === 'paid') {
                    app(StripeWebhookController::class)->fulfillCheckoutSession($session);
                }
            } catch (\Exception $e) {
                Log::error('Error fulfilling checkout session on success page', ['error' => $e->getMessage()]);
            }
        }

        return Inertia::render('User/Checkout/Success', [
            'sessionId' => $sessionId,
        ]);
    }

    public function cancel()
    {
        return Inertia::render('User/Checkout/Cancel');
    }
}
