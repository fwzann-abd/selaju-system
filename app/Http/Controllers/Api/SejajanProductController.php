<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sejajan;
use App\Models\SejajanProduct;
use App\Helpers\Helper;

class SejajanProductController extends Controller
{
    public function store(Request $request, Sejajan $sejajan)
    {
        $user = $request->user();
        if (! $user) return response()->json(['message' => 'Unauthenticated'], 401);

        // Only owner can add products
        if ($sejajan->account_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();
        $data['sejajan_id'] = $sejajan->getKey();

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Handle uploaded photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $dest = public_path('assets/modules/sejajan/mart');
            if (! \Illuminate\Support\Facades\File::exists($dest)) {
                \Illuminate\Support\Facades\File::makeDirectory($dest, 0755, true);
            }

            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\\._-]/', '_', $file->getClientOriginalName());
            $file->move($dest, $filename);
            $data['photo'] = $filename;
        }

        $product = SejajanProduct::create($data);

        // Ensure photo is filename-only in response
        if (! empty($product->photo)) {
            $product->photo = preg_replace('/.*[\/\\\\]/', '', $product->photo);
        }

        return response()->json([
            'data' => $product,
            'path' => Helper::getPhotoBasePath(),
        ], 201);
    }

    public function update(Request $request, Sejajan $sejajan, SejajanProduct $product)
    {
        $user = $request->user();
        if (! $user) return response()->json(['message' => 'Unauthenticated'], 401);

        // Only owner can update products
        if ($sejajan->account_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Handle uploaded photo
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($product->photo) {
                $oldFile = public_path('assets/modules/sejajan/mart/' . $product->photo);
                if (\Illuminate\Support\Facades\File::exists($oldFile)) {
                    \Illuminate\Support\Facades\File::delete($oldFile);
                }
            }

            $file = $request->file('photo');
            $dest = public_path('assets/modules/sejajan/mart');
            if (! \Illuminate\Support\Facades\File::exists($dest)) {
                \Illuminate\Support\Facades\File::makeDirectory($dest, 0755, true);
            }

            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\\._-]/', '_', $file->getClientOriginalName());
            $file->move($dest, $filename);
            $data['photo'] = $filename;
        }

        $product->update($data);

        // Ensure photo is filename-only in response
        if (! empty($product->photo)) {
            $product->photo = preg_replace('/.*[\/\\\\]/', '', $product->photo);
        }

        return response()->json([
            'data' => $product,
            'path' => Helper::getPhotoBasePath(),
        ], 200);
    }
}
