<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = Teacher::where('account_id', $request->user()->uuid)->firstOrFail();

        $assignments = Assignment::where('teacher_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => $assignments->items(),
            'meta' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'total' => $assignments->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $teacher = Teacher::where('account_id', $request->user()->uuid)->firstOrFail();

        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:now',
            'max_score' => 'nullable|integer|min:1|max:1000',
            'type' => 'nullable|in:homework,project,exam,quiz',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = $teacher->id;

        $assignment = Assignment::create($validated);
        $assignment->load(['classroom', 'subject']);

        return response()->json([
            'data' => $assignment,
            'message' => 'Tugas berhasil dibuat.',
        ], 201);
    }

    public function show(Request $request, Assignment $assignment): JsonResponse
    {
        $teacher = Teacher::where('account_id', $request->user()->uuid)->firstOrFail();

        if ($assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $assignment->load(['classroom', 'subject', 'submissions.student']);

        return response()->json(['data' => $assignment]);
    }

    public function update(Request $request, Assignment $assignment): JsonResponse
    {
        $teacher = Teacher::where('account_id', $request->user()->uuid)->firstOrFail();

        if ($assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|date',
            'max_score' => 'nullable|integer|min:1|max:1000',
            'type' => 'nullable|in:homework,project,exam,quiz',
            'is_published' => 'nullable|boolean',
        ]);

        $assignment->update($validated);

        return response()->json([
            'data' => $assignment->fresh(['classroom', 'subject']),
            'message' => 'Tugas berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, Assignment $assignment): JsonResponse
    {
        $teacher = Teacher::where('account_id', $request->user()->uuid)->firstOrFail();

        if ($assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $assignment->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus.']);
    }

    /**
     * Grade a student submission.
     */
    public function grade(Request $request, AssignmentSubmission $submission): JsonResponse
    {
        $teacher = Teacher::where('account_id', $request->user()->uuid)->firstOrFail();

        if ($submission->assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:' . $submission->assignment->max_score,
        ]);

        $submission->update([
            'score' => $validated['score'],
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        return response()->json([
            'data' => $submission->fresh(['student', 'assignment']),
            'message' => 'Nilai berhasil disimpan.',
        ]);
    }
}
