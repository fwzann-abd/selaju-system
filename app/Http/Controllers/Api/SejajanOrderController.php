<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sejajan;
use App\Models\SejajanOrder;
use App\Models\SejajanOrderItem;
use App\Models\SejajanProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SejajanOrderController extends Controller
{
    public function index(Request $request, $sejajanSlug)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();
        if ($sejajan->participant_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $orders = SejajanOrder::where('sejajan_id', $sejajan->id)
            ->with(['items.product', 'participant'])
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function myOrders(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Get orders where the authenticated user is the buyer (participant_id)
        $orders = SejajanOrder::where('participant_id', $user->getKey())
            ->with(['items.product', 'sejajan'])
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'sejajan_id' => ['required', 'uuid', 'exists:sejajans,id'],
            'delivery_method' => ['required', Rule::in(['pickup', 'delivery'])],
            'pickup_time' => ['nullable', 'date'],
            'location_pickup' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:sejajan_products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($data['delivery_method'] === 'delivery' && empty($data['address'])) {
            return response()->json(['message' => 'Alamat diperlukan untuk pengantaran'], 422);
        }

        if ($data['delivery_method'] === 'pickup' && empty($data['location_pickup'])) {
            return response()->json(['message' => 'Lokasi penjemputan diperlukan'], 422);
        }

        return DB::transaction(function () use ($data, $user) {
            $sejajan = Sejajan::findOrFail($data['sejajan_id']);

            $itemsPayload = [];
            $total = 0;

            foreach ($data['items'] as $item) {
                $product = SejajanProduct::where('sejajan_id', $sejajan->id)
                    ->where('id', $item['product_id'])
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => ['Produk tidak ditemukan dalam toko ini.'],
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

            $order = SejajanOrder::create([
                'sejajan_id' => $sejajan->id,
                'participant_id' => $user->getKey(),
                'status' => 'pending',
                'total_price' => $total,
                'notes' => $data['note'] ?? null,
                'pickup_time' => ! empty($data['pickup_time']) ? Carbon::parse($data['pickup_time']) : null,
                'location_pickup' => $data['delivery_method'] === 'pickup'
                    ? ($data['location_pickup'] ?? null)
                    : null,
            ]);

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

            if ($data['delivery_method'] === 'delivery' && !empty($data['address'])) {
                $extraNotes = trim(($order->notes ? $order->notes . PHP_EOL : '') . 'Alamat: ' . $data['address']);
                $order->update(['notes' => $extraNotes]);
            }

            return response()->json([
                'message' => 'Order berhasil dibuat',
                'data' => $order->load(['items.product', 'participant']),
            ], 201);
        });
    }

    public function update(Request $request, $sejajanSlug, $orderId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();
        if ($sejajan->participant_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = SejajanOrder::where('sejajan_id', $sejajan->id)
            ->where('id', $orderId)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'ready', 'completed', 'cancelled'])],
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status pesanan diperbarui',
            'data' => $order->load(['items.product', 'participant']),
        ]);
    }
}
