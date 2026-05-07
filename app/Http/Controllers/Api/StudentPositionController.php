<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentPositionResource;
use App\Models\StudentPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPositionController extends Controller
{
    /**
     * Display a listing of all student positions.
     */
    public function index(): JsonResponse
    {
        $positions = StudentPosition::query()
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => StudentPositionResource::collection($positions),
            'total' => $positions->count(),
        ]);
    }

    /**
     * Display the specified student position.
     */
    public function show(StudentPosition $studentPosition): JsonResponse
    {
        return response()->json(['data' => new StudentPositionResource($studentPosition)]);
    }

    /**
     * Store a newly created student position.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:student_positions,name',
            'slug' => 'nullable|string|max:100|unique:student_positions,slug',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $position = StudentPosition::create($validated);

        return response()->json([
            'message' => 'Jabatan siswa berhasil ditambahkan.',
            'data' => new StudentPositionResource($position),
        ], 201);
    }

    /**
     * Update the specified student position.
     */
    public function update(Request $request, StudentPosition $studentPosition): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:student_positions,name,'.$studentPosition->id,
            'slug' => 'nullable|string|max:100|unique:student_positions,slug,'.$studentPosition->id,
        ]);

        $studentPosition->update($validated);

        return response()->json([
            'message' => 'Jabatan siswa berhasil diperbarui.',
            'data' => new StudentPositionResource($studentPosition),
        ]);
    }

    /**
     * Remove the specified student position.
     */
    public function destroy(StudentPosition $studentPosition): JsonResponse
    {
        $studentPosition->delete();

        return response()->json(['message' => 'Jabatan siswa berhasil dihapus.']);
    }
}
