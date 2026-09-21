<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Order $order) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.'.$this->order->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => $this->statusMessage(),
        ];
    }

    private function statusMessage(): string
    {
        return match ($this->order->status) {
            'paid' => 'Payment confirmed! Your order is being reviewed.',
            'accepted' => 'Your order has been accepted.',
            'processing' => 'Your order is being prepared.',
            'shipped' => 'Your order has been shipped!',
            'out_for_delivery' => 'Your order is out for delivery!',
            'delivered' => 'Your order has been delivered. Enjoy!',
            'cancelled' => 'Your order has been cancelled.',
            default => 'Order status updated.',
        };
    }
}
