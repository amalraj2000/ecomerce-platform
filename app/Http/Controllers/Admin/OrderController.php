<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $order = Order::with(['user', 'vendor', 'address', 'items.product.images'])->findOrFail($id);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,accepted,processing,shipped,out_for_delivery,delivered,cancelled',
        ]);

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

        if (! $order->stripe_payment_intent_id) {
            return redirect()->back()->with('error', 'This order has no Stripe payment to refund.');
        }

        if ($order->refund_status === 'refunded') {
            return redirect()->back()->with('error', 'This order has already been refunded.');
        }

        Stripe::setApiKey(config('stripe.secret'));

        try {
            Refund::create([
                'payment_intent' => $order->stripe_payment_intent_id,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Refund failed: '.$e->getMessage());
        }

        $order->update(['refund_status' => 'requested']);

        return redirect()->back()->with('success', 'Refund initiated successfully. Stripe will confirm shortly.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
