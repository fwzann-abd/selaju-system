<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sejajan;
use Illuminate\Support\Facades\Validator;

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
        return response()->json($items);
    }

    public function show(Sejajan $sejajan)
    {
        $sejajan->load('products');
        return response()->json($sejajan);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sejajans,slug',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            // ensure unique
            $orig = $data['slug'];
            $i = 1;
            while (Sejajan::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $orig . '-' . $i++;
            }
        }

        $data['participant_id'] = $user->getKey();

        $sejajan = Sejajan::create($data);
        return response()->json($sejajan, 201);
    }

    public function update(Request $request, Sejajan $sejajan)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);
        if ($sejajan->participant_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:sejajans,slug,' . $sejajan->id . ',id',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $sejajan->update($v->validated());
        return response()->json($sejajan->fresh());
    }

    public function destroy(Request $request, Sejajan $sejajan)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);
        if ($sejajan->participant_id !== $user->getKey()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $sejajan->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
