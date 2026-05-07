<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of all subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::query()->select('id', 'name', 'code', 'type');

        if ($request->has('search')) {
            $search = strtolower($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(code) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->query('type'));
        }

        $subjects = $query->orderBy('name')->get();

        return response()->json([
            'data' => SubjectResource::collection($subjects),
            'total' => $subjects->count(),
        ]);
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject): JsonResponse
    {
        return response()->json(['data' => new SubjectResource($subject)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:subjects,name',
            'code' => 'nullable|string|max:20|unique:subjects,code',
            'type' => 'nullable|in:vocational,general',
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'message' => 'Mata pelajaran berhasil ditambahkan.',
            'data' => new SubjectResource($subject),
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:subjects,name,'.$subject->id,
            'code' => 'nullable|string|max:20|unique:subjects,code,'.$subject->id,
            'type' => 'nullable|in:vocational,general',
        ]);

        $subject->update($validated);

        return response()->json([
            'message' => 'Mata pelajaran berhasil diperbarui.',
            'data' => new SubjectResource($subject),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return response()->json(['message' => 'Mata pelajaran berhasil dihapus.']);
    }
}
