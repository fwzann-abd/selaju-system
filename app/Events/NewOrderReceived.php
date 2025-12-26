<?php

namespace App\Events;

use App\Models\SejajanOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SejajanOrder $order
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Seller channel - notif untuk owner toko ada order baru
            new PrivateChannel('orders.seller.'.$this->order->sejajan->participant_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.new';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $this->order->load('items.product', 'participant');

        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->participant->name ?? $this->order->customer_name,
            'total_price' => $this->order->total_price,
            'items_count' => $this->order->items->count(),
            'store_slug' => $this->order->sejajan->slug,
            'created_at' => $this->order->created_at->toISOString(),
        ];
    }
}
