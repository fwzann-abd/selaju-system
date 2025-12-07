<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Sejajan;
use App\Models\SejajanCartItem;
use App\Models\SejajanProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SejajanCartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $items = SejajanCartItem::where('participant_id', $user->getKey())
            ->with(['product', 'sejajan'])
            ->get();

        return response()->json([
            'data' => $this->transformItems($items),
            'tokoId' => $items->first()->sejajan_id ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'uuid', 'exists:sejajan_products,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $product = SejajanProduct::with('sejajan')->findOrFail($data['product_id']);
        $requestedQty = $data['qty'];
        if ($product->stock !== null && $product->stock < $requestedQty) {
            return response()->json(['message' => 'Stok produk tidak mencukupi'], 422);
        }

        return DB::transaction(function () use ($user, $product, $data) {
            $existingItems = SejajanCartItem::where('participant_id', $user->getKey())->get();
            if ($existingItems->isNotEmpty() && $existingItems->first()->sejajan_id !== $product->sejajan_id) {
                SejajanCartItem::where('participant_id', $user->getKey())->delete();
            }

            $item = SejajanCartItem::firstOrNew([
                'participant_id' => $user->getKey(),
                'sejajan_product_id' => $product->getKey(),
            ]);

            $item->sejajan_id = $product->sejajan_id;
            $maxQty = $item->exists ? $item->qty : 0;
            $newQty = $maxQty + $data['qty'];
            if ($product->stock !== null && $newQty > $product->stock) {
                return response()->json(['message' => 'Stok produk tidak mencukupi'], 422);
            }
            $item->qty = max(1, $newQty);
            $item->save();

            $items = SejajanCartItem::where('participant_id', $user->getKey())
                ->with(['product', 'sejajan'])
                ->get();

            return response()->json([
                'message' => 'Produk ditambahkan ke keranjang',
                'data' => $this->transformItems($items),
                'tokoId' => $items->first()->sejajan_id ?? null,
            ], 201);
        });
    }

    public function update(Request $request, $itemId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = SejajanCartItem::where('participant_id', $user->getKey())
            ->where('id', $itemId)
            ->with('product')
            ->firstOrFail();

        $requestedQty = $validator->validated()['qty'];
        $productStock = $item->product?->stock;
        if ($productStock !== null && $requestedQty > $productStock) {
            return response()->json(['message' => 'Stok produk tidak mencukupi'], 422);
        }

        $item->qty = $requestedQty;
        $item->save();

        return $this->respondCart($user);
    }

    public function destroy(Request $request, $itemId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $item = SejajanCartItem::where('participant_id', $user->getKey())
            ->where('id', $itemId)
            ->firstOrFail();
        $item->delete();

        return $this->respondCart($user);
    }

    public function destroyAll(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        SejajanCartItem::where('participant_id', $user->getKey())->delete();

        return response()->json(['data' => [], 'tokoId' => null]);
    }

    protected function respondCart($user)
    {
        $items = SejajanCartItem::where('participant_id', $user->getKey())
            ->with(['product', 'sejajan'])
            ->get();

        return response()->json([
            'data' => $this->transformItems($items),
            'tokoId' => $items->first()->sejajan_id ?? null,
        ]);
    }

    protected function transformItems($items)
    {
        return $items->map(function (SejajanCartItem $item) {
            $product = $item->product;
            $sejajan = $item->sejajan ?: new Sejajan();

            return [
                'id' => $item->getKey(),
                'product_id' => $item->sejajan_product_id,
                'sejajan_id' => $item->sejajan_id,
                'sejajan_name' => $sejajan->name,
                'sejajan_slug' => $sejajan->slug,
                'name' => $product?->name,
                'price' => $product?->price ?? 0,
                'qty' => $item->qty,
                'photo' => $product && $product->photo ? Helper::getPhotoBasePath() . $product->photo : null,
                'photo_path' => $product?->photo,
                'stock' => $product?->stock ?? $item->qty,
            ];
        });
    }
}
