<?php

namespace App\Http\Controllers\User;

use App\Events\OrderStatusUpdated;
use App\Events\StockUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Refund;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::with(['items.product.images' => function ($q) {
            $q->where('is_primary', true);
        }, 'vendor'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('User/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $order->load([
            'items.product.images' => function ($q) {
                $q->where('is_primary', true);
            },
            'vendor',
            'address',
            'user',
        ]);

        return Inertia::render('User/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function cancel(Order $order)
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return redirect()->back()->with('error', 'Order cannot be cancelled because it is already '.$order->status.'.');
        }

        $order->status = 'cancelled';

        // Stripe auto-refund if payment intent is recorded
        if ($order->stripe_payment_intent_id) {
            try {
                Stripe::setApiKey(config('stripe.secret'));
                Refund::create([
                    'payment_intent' => $order->stripe_payment_intent_id,
                ]);
                $order->refund_status = 'refunded';
                $order->refunded_at = now();
            } catch (\Exception $e) {
                Log::error('Error processing Stripe refund on cancellation: '.$e->getMessage());
                $order->refund_status = 'requested';
            }
        }

        $order->save();

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
                try {
                    broadcast(new StockUpdated($item->product_id, $item->product->fresh()->stock));
                } catch (\Exception $e) {
                    Log::warning('Reverb broadcast error on stock restore: '.$e->getMessage());
                }
            }
        }

        // Broadcast status update
        try {
            broadcast(new OrderStatusUpdated($order->fresh()));
        } catch (\Exception $e) {
            Log::warning('Reverb broadcast error on order status update: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Order cancelled successfully!');
    }
}
