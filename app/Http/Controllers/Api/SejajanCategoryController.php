<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sejajan;
use App\Models\SejajanCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SejajanCategoryController extends Controller
{
    /**
     * Display categories for a specific store.
     */
    public function index($sejajanSlug)
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();

        $categories = $sejajan->categories()
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request, $sejajanSlug)
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('sejajan_categories')->where('sejajan_id', $sejajan->id),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['sejajan_id'] = $sejajan->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? 0;

        $category = SejajanCategory::create($validated);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($sejajanSlug, $categoryId)
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();
        $category = SejajanCategory::where('sejajan_id', $sejajan->id)
            ->findOrFail($categoryId);

        return response()->json([
            'data' => $category,
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $sejajanSlug, $categoryId)
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();
        $category = SejajanCategory::where('sejajan_id', $sejajan->id)
            ->findOrFail($categoryId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('sejajan_categories')
                    ->where('sejajan_id', $sejajan->id)
                    ->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active', $category->is_active);
        $validated['order'] = $validated['order'] ?? $category->order;

        $category->update($validated);

        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($sejajanSlug, $categoryId)
    {
        $sejajan = Sejajan::where('slug', $sejajanSlug)->firstOrFail();
        $category = SejajanCategory::where('sejajan_id', $sejajan->id)
            ->findOrFail($categoryId);

        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki produk',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
