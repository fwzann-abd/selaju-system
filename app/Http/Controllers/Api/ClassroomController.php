<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $classrooms = Classroom::with('teacher')
            ->latest()
            ->get();

        return response()->json([
            'data' => $classrooms,
            'total' => $classrooms->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'rombel' => ['nullable', 'string', 'max:255'],
            'teacher_id' => ['required', 'string', 'exists:teachers,id'],
            'academic_year' => ['required', 'string', 'max:255'],
        ]);

        $classroom = Classroom::create($validated);

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
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $classroom = Classroom::find($id);

        if (! $classroom) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'tingkat' => ['sometimes', 'string', 'max:255'],
            'jurusan' => ['sometimes', 'string', 'max:255'],
            'rombel' => ['nullable', 'string', 'max:255'],
            'teacher_id' => ['sometimes', 'string', 'exists:teachers,id'],
            'academic_year' => ['sometimes', 'string', 'max:255'],
        ]);

        $classroom->update($validated);

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
