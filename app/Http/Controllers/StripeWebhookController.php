<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Events\StockUpdated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        Stripe::setApiKey(config('stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed.', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    /**
     * Create order after successful Stripe Checkout payment.
     */
    private function handleCheckoutCompleted(object $session): void
    {
        $userId = (int) $session->metadata->user_id;
        $addressId = (int) $session->metadata->address_id;

        // Guard against duplicate processing
        if (Order::where('stripe_checkout_session_id', $session->id)->exists()) {
            return;
        }

        DB::transaction(function () use ($session, $userId, $addressId) {
            $cart = Cart::with(['items.product'])->where('user_id', $userId)->first();

            if (! $cart || $cart->items->isEmpty()) {
                Log::warning('Stripe webhook: cart empty for user', ['user_id' => $userId]);

                return;
            }

            $total = $cart->items->sum(function ($item) {
                return $item->product->price
                    * (1 - $item->product->discount_percentage / 100)
                    * $item->quantity;
            });

            $firstItemVendorId = $cart->items->first()->product->vendor_id;

            $order = Order::create([
                'user_id' => $userId,
                'vendor_id' => $firstItemVendorId,
                'total_amount' => $total,
                'status' => 'paid',
                'shipping_address_id' => $addressId,
                'stripe_checkout_session_id' => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent,
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
                broadcast(new StockUpdated($item->product_id, $item->product->fresh()->stock));
            }

            // Clear cart
            $cart->items()->delete();

            // Broadcast order confirmation to user
            broadcast(new OrderStatusUpdated($order));
        });
    }

    /**
     * Mark order as refunded when Stripe confirms the charge refund.
     */
    private function handleChargeRefunded(object $charge): void
    {
        $order = Order::where('stripe_payment_intent_id', $charge->payment_intent)->first();

        if (! $order) {
            Log::warning('Stripe refund webhook: order not found for payment intent', [
                'payment_intent' => $charge->payment_intent,
            ]);

            return;
        }

        $order->update([
            'refund_status' => 'refunded',
            'refunded_at' => now(),
        ]);

        broadcast(new OrderStatusUpdated($order->fresh()));
    }
}
