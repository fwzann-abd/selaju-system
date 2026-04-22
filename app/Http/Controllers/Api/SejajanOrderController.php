<?php

namespace App\Http\Controllers\Api;

use App\Events\NewOrderReceived;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Sejajan;
use App\Models\SejajanOrder;
use App\Models\SejajanOrderItem;
use App\Models\SejajanProduct;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SejajanOrderController extends Controller
{
    /**
     * Get orders for a specific store (seller view).
     */
    public function index(Request $request, string $sejajanSlug): JsonResponse
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();

        // Verify ownership
        if ($sejajan->account_id !== $request->user()->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $orders = SejajanOrder::where('sejajan_id', $sejajan->id)
            ->with(['items.product', 'participant'])
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    /**
     * Get orders for authenticated user (buyer view).
     */
    public function myOrders(Request $request): JsonResponse
    {
        $orders = SejajanOrder::where('account_id', $request->user()->getKey())
            ->with(['items.product', 'sejajan'])
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    /**
     * Create a new order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $user = $request->user();
            $sejajan = Sejajan::findOrFail($data['sejajan_id']);

            // Prepare order items and calculate total
            [$itemsPayload, $total] = $this->prepareOrderItems($sejajan, $data['items']);

            // Create order
            $order = $this->createOrder($sejajan, $user, $data, $total);

            // Create order items and update stock
            $this->createOrderItems($order, $itemsPayload);

            // Add delivery address to notes if applicable
            $this->addDeliveryAddress($order, $data);

            // Broadcast event
            $this->broadcastNewOrder($order);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order->load(['items.product', 'participant', 'sejajan']),
            ], 201);
        });
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, string $sejajanSlug, string $orderId): JsonResponse
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();

        // Verify ownership
        if ($sejajan->account_id !== $request->user()->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = SejajanOrder::where('sejajan_id', $sejajan->id)
            ->where('id', $orderId)
            ->firstOrFail();

        $validated = $request->validated();

        $order->update(['status' => $validated['status']]);

        // Broadcast status update
        broadcast(new OrderStatusUpdated($order->load(['items.product', 'participant', 'sejajan'])));

        return response()->json([
            'message' => 'Status pesanan berhasil diperbarui',
            'data' => $order,
        ]);
    }

    /**
     * Prepare order items payload and calculate total.
     */
    private function prepareOrderItems(Sejajan $sejajan, array $items): array
    {
        $itemsPayload = [];
        $total = 0;

        foreach ($items as $item) {
            $product = SejajanProduct::where('sejajan_id', $sejajan->id)
                ->where('id', $item['product_id'])
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'items' => ['Produk tidak ditemukan dalam toko ini'],
                ]);
            }

            if ($product->stock < $item['qty']) {
                throw ValidationException::withMessages([
                    'items' => ["Stok {$product->name} tidak mencukupi"],
                ]);
            }

            $subtotal = $product->price * $item['qty'];
            $total += $subtotal;

            $itemsPayload[] = [
                'product' => $product,
                'qty' => $item['qty'],
                'price' => $product->price,
                'subtotal' => $subtotal,
            ];
        }

        return [$itemsPayload, $total];
    }

    /**
     * Create order record.
     */
    private function createOrder(Sejajan $sejajan, $user, array $data, float $total): SejajanOrder
    {
        return SejajanOrder::create([
            'sejajan_id' => $sejajan->id,
            'account_id' => $user->getKey(),
            'status' => 'pending',
            'total_price' => $total,
            'notes' => $data['note'] ?? null,
            'pickup_time' => ! empty($data['pickup_time']) ? Carbon::parse($data['pickup_time']) : null,
            'location_pickup' => $data['delivery_method'] === 'pickup' ? ($data['location_pickup'] ?? null) : null,
        ]);
    }

    /**
     * Create order items and update product stock.
     */
    private function createOrderItems(SejajanOrder $order, array $itemsPayload): void
    {
        foreach ($itemsPayload as $item) {
            SejajanOrderItem::create([
                'sejajan_order_id' => $order->getKey(),
                'sejajan_product_id' => $item['product']->getKey(),
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);

            $item['product']->decrement('stock', $item['qty']);
        }
    }

    /**
     * Add delivery address to order notes.
     */
    private function addDeliveryAddress(SejajanOrder $order, array $data): void
    {
        if ($data['delivery_method'] === 'delivery' && ! empty($data['address'])) {
            $notes = trim(($order->notes ? $order->notes.PHP_EOL : '').'Alamat: '.$data['address']);
            $order->update(['notes' => $notes]);
        }
    }

    /**
     * Broadcast new order event.
     */
    private function broadcastNewOrder(SejajanOrder $order): void
    {
        $order->load(['items.product', 'participant', 'sejajan']);

        Log::info('Broadcasting new order', [
            'order_id' => $order->id,
            'seller_channel' => 'orders.seller.'.$order->sejajan->account_id,
            'buyer_id' => $order->account_id,
        ]);

        broadcast(new NewOrderReceived($order));
    }
}
