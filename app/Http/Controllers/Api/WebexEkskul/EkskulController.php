<?php

namespace App\Http\Controllers\Api\WebexEkskul;

use App\Http\Controllers\Controller;
use App\Models\WebexEkskul;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkskulController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Support filtering by slug
        if ($request->has('slug')) {
            $ekskul = WebexEkskul::where('slug', $request->query('slug'))
                ->with([
                    'pengurus' => fn ($q) => $q->with('student:id,name,nis,email'),
                    'participants' => fn ($q) => $q->with('student:id,name,nis,email'),
                    'reports' => fn ($q) => $q->with('createdBy:id,name,nis,email')->latest(),
                ])
                ->first();

            return response()->json($ekskul ? [$ekskul] : []);
        }

        $ekskuls = WebexEkskul::with([
            'pengurus' => fn ($q) => $q->with('student:id,name,nis,email'),
            'participants' => fn ($q) => $q->count(),
        ])
            ->paginate(15);

        return response()->json($ekskuls);
    }

    public function show(WebexEkskul $ekskul): JsonResponse
    {
        $ekskul->load([
            'pengurus' => fn ($q) => $q->with('student:id,name,nis,email'),
            'participants' => fn ($q) => $q->with('student:id,name,nis,email'),
            'reports' => fn ($q) => $q->with('createdBy:id,name,nis,email')->latest(),
        ]);

        return response()->json($ekskul);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:webex_ekskuls',
            'bio' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);

        $ekskul = WebexEkskul::create($validated);

        return response()->json($ekskul, 201);
    }

    public function update(WebexEkskul $ekskul, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:webex_ekskuls,slug,'.$ekskul->id,
            'bio' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);

        $ekskul->update($validated);

        return response()->json($ekskul);
    }

    public function destroy(WebexEkskul $ekskul): JsonResponse
    {
        $ekskul->delete();

        return response()->json(['message' => 'Ekskul deleted successfully']);
    }
}
