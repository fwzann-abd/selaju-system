<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentMaterialController extends Controller
{
    /**
     * Display a listing of materials for the student's classrooms.
     */
    public function index(Request $request): object
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->firstOrFail();

        // Get all classrooms for this student
        $classroomIds = $student->classrooms()->pluck('classroom_id');

        $materials = CourseMaterial::whereIn('classroom_id', $classroomIds)
            ->where('is_published', true)
            ->with(['teacher', 'classroom', 'schedule'])
            ->latest()
            ->paginate(20);

        return response()->paginate(
            CourseMaterialResource::collection($materials),
            'Materials retrieved successfully'
        );
    }

    /**
     * Display the specified material.
     */
    public function show(CourseMaterial $material, Request $request): object
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->firstOrFail();

        // Check if student is in the classroom
        $isInClassroom = $student->classrooms()
            ->where('classroom_id', $material->classroom_id)
            ->exists();

        if (! $isInClassroom || ! $material->is_published) {
            return response()->error('Material not found or access denied', 404);
        }

        $material->load(['teacher', 'classroom', 'schedule']);

        return response()->success(
            new CourseMaterialResource($material),
            'Material retrieved successfully'
        );
    }

    /**
     * Download the specified material.
     */
    public function download(CourseMaterial $material, Request $request)
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->firstOrFail();

        // Check if student is in the classroom
        $isInClassroom = $student->classrooms()
            ->where('classroom_id', $material->classroom_id)
            ->exists();

        if (! $isInClassroom || ! $material->is_published) {
            return response()->error('Material not found or access denied', 404);
        }

        if (! Storage::disk('public')->exists($material->file_path)) {
            return response()->error('File not found', 404);
        }

        return Storage::disk('public')->download(
            $material->file_path,
            $material->original_filename
        );
    }

    /**
     * Get materials filtered by classroom.
     */
    public function byClassroom(Request $request, $classroomId): object
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->firstOrFail();

        // Verify student is in this classroom
        $isInClassroom = $student->classrooms()
            ->where('classroom_id', $classroomId)
            ->exists();

        if (! $isInClassroom) {
            return response()->error('Access denied', 403);
        }

        $materials = CourseMaterial::where('classroom_id', $classroomId)
            ->where('is_published', true)
            ->with(['teacher', 'classroom', 'schedule'])
            ->latest()
            ->paginate(15);

        return response()->paginate(
            CourseMaterialResource::collection($materials),
            'Materials retrieved successfully'
        );
    }
}
