<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers with LMS context.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Teacher::with(['school:id,name', 'account:uuid,email', 'classrooms:id,name'])
            ->select('id', 'account_id', 'school_id', 'nip', 'name');

        if ($request->has('search')) {
            $search = strtolower($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(nip) LIKE ?', ["%{$search}%"]);
            });
        }

        $teachers = $query->orderBy('name')->get();

        return response()->json([
            'data' => $teachers->map(function (Teacher $teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'nip' => $teacher->nip,
                    'school' => $teacher->school->name ?? '-',
                    'email' => $teacher->account->email ?? '-',
                    'classrooms' => $teacher->classrooms->pluck('name')->join(', ') ?: '-',
                ];
            }),
        ]);
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): JsonResponse
    {
        $teacher->load(['school:id,name', 'account:uuid,email', 'classrooms:id,name', 'schedules']);

        return response()->json([
            'data' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'nip' => $teacher->nip,
                'school' => $teacher->school->name ?? '-',
                'email' => $teacher->account->email ?? '-',
                'classrooms' => $teacher->classrooms->pluck('name')->toArray(),
            ],
        ]);
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Use admin/teachers for creating teachers'], 501);
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json(['message' => 'Use admin/teachers for updating teachers'], 501);
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json(['message' => 'Use admin/teachers for deleting teachers'], 501);
    }
}
