<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherAnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $announcements = Announcement::where('author_id', $request->user()->uuid)
            ->with('classroom')
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => $announcements->items(),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => 'nullable|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'priority' => 'nullable|in:normal,important,urgent',
            'is_pinned' => 'nullable|boolean',
        ]);

        $validated['author_id'] = $request->user()->uuid;

        $announcement = Announcement::create($validated);
        $announcement->load('classroom');

        return response()->json([
            'data' => $announcement,
            'message' => 'Pengumuman berhasil dibuat.',
        ], 201);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        if ($announcement->author_id !== $request->user()->uuid) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'priority' => 'nullable|in:normal,important,urgent',
            'is_pinned' => 'nullable|boolean',
        ]);

        $announcement->update($validated);

        return response()->json([
            'data' => $announcement->fresh('classroom'),
            'message' => 'Pengumuman berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, Announcement $announcement): JsonResponse
    {
        if ($announcement->author_id !== $request->user()->uuid) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $announcement->delete();

        return response()->json(['message' => 'Pengumuman berhasil dihapus.']);
    }
}
