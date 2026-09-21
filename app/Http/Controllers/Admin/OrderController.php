<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Events\StockUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Exception\ApiErrorException;
use Stripe\Refund;
use Stripe\Stripe;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'vendor'])->latest()->paginate(10);

        return Inertia::render('Admin/Orders/Index', ['orders' => $orders]);
    }

    public function show(string $id)
    {
        $order = Order::with(['user', 'vendor', 'address', 'items.product.images'])->findOrFail($id);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,accepted,processing,shipped,out_for_delivery,delivered,cancelled',
        ]);

        if ($request->status === 'cancelled') {
            $result = $this->performRefundAndCancel($order);
            if (! $result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return redirect()->back()->with('success', $result['message']);
        }

        $order->update(['status' => $request->status]);
        broadcast(new OrderStatusUpdated($order->fresh()));

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Issue a full Stripe refund for the order.
     */
    public function refund(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $result = $this->performRefundAndCancel($order);
        if (! $result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Helper to perform Stripe refund, restock items, set refund_status to refunded, and broadcast events.
     */
    public function performRefundAndCancel(Order $order): array
    {
        if ($order->refund_status === 'refunded' && $order->status === 'cancelled') {
            return ['success' => true, 'message' => 'Order is already cancelled and refunded.'];
        }

        // Attempt Stripe Refund if payment intent exists
        if ($order->stripe_payment_intent_id && $order->refund_status !== 'refunded') {
            Stripe::setApiKey(config('stripe.secret'));

            try {
                Refund::create([
                    'payment_intent' => $order->stripe_payment_intent_id,
                ]);
            } catch (ApiErrorException $e) {
                Log::error('Stripe refund failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

                return ['success' => false, 'message' => 'Stripe refund failed: '.$e->getMessage()];
            }
        }

        // Restock items if cancelling an active order
        if ($order->status !== 'cancelled') {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
                try {
                    broadcast(new StockUpdated($item->product_id, $item->product->fresh()->stock));
                } catch (\Exception $e) {
                    Log::warning('Reverb broadcast warning for stock update: '.$e->getMessage());
                }
            }
        }

        $order->update([
            'status' => 'cancelled',
            'refund_status' => 'refunded',
            'refunded_at' => now(),
        ]);

        try {
            broadcast(new OrderStatusUpdated($order->fresh()));
        } catch (\Exception $e) {
            Log::warning('Reverb broadcast warning for order status: '.$e->getMessage());
        }

        return ['success' => true, 'message' => 'Order cancelled and payment refunded successfully via Stripe.'];
    }
}
