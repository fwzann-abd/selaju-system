<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentMaterialController extends Controller
{
    /**
     * Display a listing of materials for the student's classrooms.
     */
    public function index(Request $request): JsonResponse
    {
        // Get the authenticated student safely: NEVER throw 404
        $student = auth()->user()?->student;

        if (! $student) {
            return response()->json(['data' => []]);
        }

        // Get the IDs of the classrooms the student is enrolled in
        $classroomIds = $student->classrooms()->pluck('classroom_id');

        if ($classroomIds->isEmpty()) {
            return response()->json(['data' => []]);
        }

        // Fetch the materials
        $materials = CourseMaterial::whereIn('classroom_id', $classroomIds)
            ->with(['teacher', 'classroom'])
            ->latest()
            ->get();

        // Return the JSON response properly
        return response()->json([
            'data' => CourseMaterialResource::collection($materials),
        ]);
    }

    /**
     * Display the specified material.
     */
    public function show(Request $request, CourseMaterial $material): JsonResponse
    {
        $student = auth()->user()?->student;

        if (! $student) {
            return response()->json(['error' => 'Student record not found'], 404);
        }

        // Get enrolled classroom IDs
        $classroomIds = $student->classrooms()->pluck('classroom_id');

        // Safely ensure the requested material belongs to one of the student's classrooms
        $material = CourseMaterial::whereIn('classroom_id', $classroomIds)
            ->where('id', $material->id)
            ->with(['teacher', 'classroom'])
            ->first();

        if (! $material) {
            return response()->json(['error' => 'Material not found or access denied'], 404);
        }

        return response()->json(new CourseMaterialResource($material));
    }

    /**
     * Download the specified material.
     */
    public function download(Request $request, CourseMaterial $material)
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->first();

        if (! $student) {
            return response()->json(['error' => 'Student record not found'], 404);
        }

        // Check if student is in the classroom
        $isInClassroom = $student->classrooms()
            ->where('classroom_id', $material->classroom_id)
            ->exists();

        if (! $isInClassroom || ! $material->is_published) {
            return response()->json(['error' => 'Material not found or access denied'], 404);
        }

        if (! Storage::disk('public')->exists($material->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Storage::disk('public')->download(
            $material->file_path,
            $material->original_filename
        );
    }

    /**
     * Get materials filtered by classroom.
     */
    public function byClassroom(Request $request, $classroomId): JsonResponse
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->first();

        if (! $student) {
            return response()->json(['error' => 'Student record not found'], 404);
        }

        // Verify student is in this classroom
        $isInClassroom = $student->classrooms()
            ->where('classroom_id', $classroomId)
            ->exists();

        if (! $isInClassroom) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $materials = CourseMaterial::where('classroom_id', $classroomId)
            ->where('is_published', true)
            ->with(['teacher', 'classroom', 'schedule'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => CourseMaterialResource::collection($materials),
            'meta' => $materials->toArray()['meta'] ?? null,
        ]);
    }

    /**
     * Get raw text content for previewable text files (txt, md, csv).
     */
    public function content(Request $request, CourseMaterial $material): JsonResponse
    {
        $user = $request->user();
        $student = Student::where('account_id', $user->id)->first();

        if (! $student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $isInClassroom = $student->classrooms()
            ->where('classroom_id', $material->classroom_id)
            ->exists();

        if (! $isInClassroom || ! $material->is_published) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Only allow text-based files
        $ext = strtolower(pathinfo($material->original_filename, PATHINFO_EXTENSION));
        $allowedExtensions = ['txt', 'md', 'markdown', 'csv', 'json', 'xml', 'html', 'css', 'js'];

        if (! in_array($ext, $allowedExtensions)) {
            return response()->json(['error' => 'Preview not available for this file type'], 422);
        }

        if (! Storage::disk('public')->exists($material->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $content = Storage::disk('public')->get($material->file_path);

        // Limit to 500KB for safety
        if (strlen($content) > 512000) {
            $content = substr($content, 0, 512000)."\n\n--- File terlalu besar, hanya menampilkan 500KB pertama ---";
        }

        return response()->json([
            'content' => $content,
            'filename' => $material->original_filename,
            'extension' => $ext,
        ]);
    }
}
