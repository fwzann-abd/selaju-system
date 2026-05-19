<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Models\Classroom;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $classrooms = Classroom::with(['teacher', 'school'])
            ->latest()
            ->paginate(10);

        return response()->json($classrooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassroomRequest $request): \Illuminate\Http\JsonResponse
    {
        $classroom = Classroom::create($request->validated());

        return response()->json([
            'message' => 'Kelas berhasil dibuat',
            'data' => $classroom->load('teacher'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $classroom = Classroom::with('teacher', 'students', 'schedules')
            ->find($id);

        if (! $classroom) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $classroom,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassroomRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $classroom = Classroom::find($id);

        if (! $classroom) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $classroom->update($request->validated());

        return response()->json([
            'message' => 'Kelas berhasil diperbarui',
            'data' => $classroom->load('teacher'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $classroom = Classroom::find($id);

        if (! $classroom) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $classroom->delete();

        return response()->json([
            'message' => 'Kelas berhasil dihapus',
        ]);
    }
}
