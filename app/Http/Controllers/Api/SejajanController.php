<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSejajanRequest;
use App\Http\Requests\UpdateSejajanRequest;
use App\Models\Sejajan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SejajanController extends Controller
{
    public function index(Request $request)
    {
        $query = Sejajan::query()->with('products');

        // simple search by name
        if ($q = $request->query('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        $items = $query->get();

        // Normalize photo to filename only for each item (in case older records contain full paths)
        $items->transform(function ($item) {
            if (! empty($item->photo)) {
                $item->photo = preg_replace('/.*[\/\\\\]/', '', $item->photo);
            }

            return $item;
        });

        return response()->json([
            'data' => $items,
            'path' => Helper::getPhotoBasePath(),
        ]);
    }

    public function myStores(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $stores = Sejajan::where('account_id', $user->getKey())
            ->withCount('products')
            ->get();

        // Normalize photo to filename only
        $stores->transform(function ($item) {
            if (! empty($item->photo)) {
                $item->photo = preg_replace('/.*[\/\\\\]/', '', $item->photo);
            }

            return $item;
        });

        return response()->json([
            'data' => $stores,
            'path' => Helper::getPhotoBasePath(),
        ]);
    }

    public function show(Sejajan $sejajan)
    {
        $sejajan->load('products');
        // Normalize photo to filename only (strip any stored path)
        if (! empty($sejajan->photo)) {
            $sejajan->photo = preg_replace('/.*[\/\\\\]/', '', $sejajan->photo);
        }

        return response()->json([
            'data' => $sejajan,
            'path' => Helper::getPhotoBasePath(),
        ]);
    }

    public function store(StoreSejajanRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            // ensure unique
            $orig = $data['slug'];
            $i = 1;
            while (Sejajan::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $orig.'-'.$i++;
            }
        }

        $data['account_id'] = $request->user()->getKey();

        // Handle uploaded photo if present
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // ensure destination exists inside public
            $dest = public_path('assets/modules/sejajan/mart');
            if (! \Illuminate\Support\Facades\File::exists($dest)) {
                \Illuminate\Support\Facades\File::makeDirectory($dest, 0755, true);
            }

            $filename = time().'_'.preg_replace('/[^A-Za-z0-9\._-]/', '_', $file->getClientOriginalName());
            $file->move($dest, $filename);
            // store only the filename; the accessor will build the full path
            $data['photo'] = $filename;
        }

        $sejajan = Sejajan::create($data);

        // Ensure response contains filename only
        if (! empty($sejajan->photo)) {
            $sejajan->photo = preg_replace('/.*[\/\\\\]/', '', $sejajan->photo);
        }

        return response()->json([
            'data' => $sejajan,
            'path' => Helper::getPhotoBasePath(),
        ], 201);
    }

    public function update(UpdateSejajanRequest $request, Sejajan $sejajan)
    {
        $data = $request->validated();

        // Handle uploaded photo if present
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // ensure destination exists inside public
            $dest = public_path('assets/modules/sejajan/mart');
            if (! \Illuminate\Support\Facades\File::exists($dest)) {
                \Illuminate\Support\Facades\File::makeDirectory($dest, 0755, true);
            }

            // Delete old photo if exists
            if (! empty($sejajan->photo)) {
                $oldPath = $dest.'/'.preg_replace('/.*[\/\\\\]/', '', $sejajan->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $filename = time().'_'.preg_replace('/[^A-Za-z0-9\._-]/', '_', $file->getClientOriginalName());
            $file->move($dest, $filename);
            // store only the filename
            $data['photo'] = $filename;
        }

        $sejajan->update($data);

        // Normalize photo to filename only
        $updated = $sejajan->fresh();
        if (! empty($updated->photo)) {
            $updated->photo = preg_replace('/.*[\/\\\\]/', '', $updated->photo);
        }

        return response()->json([
            'data' => $updated,
            'path' => Helper::getPhotoBasePath(),
        ]);
    }

    public function destroy(Request $request, Sejajan $sejajan)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if ($sejajan->account_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $sejajan->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
