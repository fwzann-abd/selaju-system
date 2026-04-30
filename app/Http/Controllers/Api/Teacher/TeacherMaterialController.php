<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseMaterialRequest;
use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherMaterialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Guru belum dikonfigurasi.'], 403);
        }

        $query = CourseMaterial::where('teacher_id', $teacher->id)
            ->with(['teacher', 'classroom', 'schedule'])
            ->latest();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $materials = $query->paginate(15);

        return response()->json([
            'data' => CourseMaterialResource::collection($materials),
            'meta' => [
                'current_page' => $materials->currentPage(),
                'last_page' => $materials->lastPage(),
                'total' => $materials->total(),
            ],
        ]);
    }

    public function store(StoreCourseMaterialRequest $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Guru belum dikonfigurasi.'], 403);
        }

        if (! $request->hasFile('file')) {
            return response()->json(['message' => 'File materi wajib diunggah.'], 400);
        }

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $category = CourseMaterial::resolveCategory($mimeType);

        // Store file in category-based subdirectory
        $filePath = $file->store("materials/{$category}", 'public');

        $materialData = [
            'teacher_id' => $teacher->id,
            'classroom_id' => $request->classroom_id,
            'schedule_id' => $request->schedule_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $mimeType,
            'category' => $category,
            'is_published' => $request->is_published ?? true,
        ];

        // Handle subtitle upload for video materials
        if ($category === 'video' && $request->hasFile('subtitle')) {
            $subtitle = $request->file('subtitle');
            $subtitlePath = $subtitle->store('materials/subtitles', 'public');
            $materialData['subtitle_path'] = $subtitlePath;
        }

        $material = CourseMaterial::create($materialData);
        $material->load(['teacher', 'classroom']);

        return response()->json([
            'data' => new CourseMaterialResource($material),
            'message' => 'Materi berhasil diunggah.',
        ], 201);
    }

    public function show(Request $request, CourseMaterial $material): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $teacher || $material->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $material->load(['teacher', 'classroom', 'schedule']);

        return response()->json(['data' => new CourseMaterialResource($material)]);
    }

    public function update(StoreCourseMaterialRequest $request, CourseMaterial $material): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $teacher || $material->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $category = CourseMaterial::resolveCategory($mimeType);
            $filePath = $file->store("materials/{$category}", 'public');

            $material->update([
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $mimeType,
                'category' => $category,
            ]);

            // Clear subtitle if category changed from video
            if ($category !== 'video' && $material->subtitle_path) {
                Storage::disk('public')->delete($material->subtitle_path);
                $material->update(['subtitle_path' => null]);
            }
        }

        // Handle subtitle update
        if ($request->hasFile('subtitle') && $material->category === 'video') {
            if ($material->subtitle_path && Storage::disk('public')->exists($material->subtitle_path)) {
                Storage::disk('public')->delete($material->subtitle_path);
            }
            $subtitle = $request->file('subtitle');
            $material->update(['subtitle_path' => $subtitle->store('materials/subtitles', 'public')]);
        }

        // Update metadata
        $material->update($request->only(['title', 'description', 'classroom_id', 'schedule_id', 'is_published']));
        $material->load(['teacher', 'classroom', 'schedule']);

        return response()->json([
            'data' => new CourseMaterialResource($material),
            'message' => 'Materi berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, CourseMaterial $material): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $teacher || $material->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Delete files
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }
        if ($material->subtitle_path && Storage::disk('public')->exists($material->subtitle_path)) {
            Storage::disk('public')->delete($material->subtitle_path);
        }

        $material->delete();

        return response()->json(['message' => 'Materi berhasil dihapus.']);
    }

    /**
     * Batch upload multiple files at once.
     */
    public function storeBatch(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Guru belum dikonfigurasi.'], 403);
        }

        $request->validate([
            'files' => 'required|array|min:1|max:20',
            'files.*' => 'required|file|max:204800', // 200MB each
            'classroom_id' => 'required|uuid|exists:classrooms,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $created = [];

        foreach ($request->file('files') as $index => $file) {
            $mimeType = $file->getMimeType();
            $category = CourseMaterial::resolveCategory($mimeType);
            $filePath = $file->store("materials/{$category}", 'public');
            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            // Use provided title with index suffix, or fallback to filename
            $title = $request->title
                ? ($request->title . (count($request->file('files')) > 1 ? ' (' . ($index + 1) . ')' : ''))
                : $baseName;

            $material = CourseMaterial::create([
                'teacher_id' => $teacher->id,
                'classroom_id' => $request->classroom_id,
                'title' => $title,
                'description' => $request->description,
                'file_path' => $filePath,
                'original_filename' => $originalName,
                'file_size' => $file->getSize(),
                'file_type' => $mimeType,
                'category' => $category,
                'is_published' => true,
            ]);

            $material->load(['teacher', 'classroom']);
            $created[] = new CourseMaterialResource($material);
        }

        return response()->json([
            'data' => $created,
            'message' => count($created) . ' materi berhasil diunggah.',
        ], 201);
    }
}
