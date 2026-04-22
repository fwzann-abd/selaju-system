<?php

namespace App\Http\Controllers\Api;

use App\Events\MaterialUploaded;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseMaterialRequest;
use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherMaterialController extends Controller
{
    /**
     * Display a listing of materials for the authenticated teacher.
     */
    public function index(Request $request): object
    {
        $user = $request->user();
        $teacher = Teacher::where('account_id', $user->id)->firstOrFail();

        $materials = CourseMaterial::where('teacher_id', $teacher->id)
            ->with(['teacher', 'classroom', 'schedule'])
            ->latest()
            ->paginate(15);

        return response()->paginate(
            CourseMaterialResource::collection($materials),
            'Materials retrieved successfully'
        );
    }

    /**
     * Store a newly created material in storage.
     */
    public function store(StoreCourseMaterialRequest $request): object
    {
        $user = $request->user();
        $teacher = Teacher::where('account_id', $user->id)->firstOrFail();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('materials', 'public');

            $material = CourseMaterial::create([
                'teacher_id' => $teacher->id,
                'classroom_id' => $request->classroom_id,
                'schedule_id' => $request->schedule_id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'is_published' => $request->is_published ?? true,
            ]);

            // Load relations for event broadcast
            $material->load(['teacher', 'classroom']);

            // Broadcast event to notify students in real-time
            MaterialUploaded::dispatch($material);

            return response()->success(
                new CourseMaterialResource($material),
                'Material uploaded successfully',
                201
            );
        }

        return response()->error('File upload failed', 400);
    }

    /**
     * Display the specified material.
     */
    public function show(CourseMaterial $material): object
    {
        $user = request()->user();
        $teacher = Teacher::where('account_id', $user->id)->firstOrFail();

        if ($material->teacher_id !== $teacher->id) {
            return response()->error('Unauthorized', 403);
        }

        $material->load(['teacher', 'classroom', 'schedule']);

        return response()->success(
            new CourseMaterialResource($material),
            'Material retrieved successfully'
        );
    }

    /**
     * Update the specified material in storage.
     */
    public function update(StoreCourseMaterialRequest $request, CourseMaterial $material): object
    {
        $user = $request->user();
        $teacher = Teacher::where('account_id', $user->id)->firstOrFail();

        if ($material->teacher_id !== $teacher->id) {
            return response()->error('Unauthorized', 403);
        }

        // Handle file update
        if ($request->hasFile('file')) {
            // Delete old file
            if (Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $filePath = $file->store('materials', 'public');

            $material->update([
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]);
        }

        // Update other fields
        $material->update($request->only(['title', 'description', 'classroom_id', 'schedule_id', 'is_published']));

        $material->load(['teacher', 'classroom', 'schedule']);

        return response()->success(
            new CourseMaterialResource($material),
            'Material updated successfully'
        );
    }

    /**
     * Remove the specified material from storage.
     */
    public function destroy(CourseMaterial $material): object
    {
        $user = request()->user();
        $teacher = Teacher::where('account_id', $user->id)->firstOrFail();

        if ($material->teacher_id !== $teacher->id) {
            return response()->error('Unauthorized', 403);
        }

        // Delete file
        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return response()->success(
            null,
            'Material deleted successfully'
        );
    }
}
