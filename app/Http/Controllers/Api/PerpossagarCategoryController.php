<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarCategory;

class PerpossagarCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = PerpossagarCategory::orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Display the specified category.
     */
    public function show($uuid)
    {
        $category = PerpossagarCategory::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'data' => $category,
        ]);
    }
}
