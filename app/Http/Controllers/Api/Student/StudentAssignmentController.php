<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;
        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }
        $classroomIds = $student->classrooms()->pluck('classrooms.id');

        $assignments = Assignment::whereIn('classroom_id', $classroomIds)
            ->where('is_published', true)
            ->with(['subject', 'classroom', 'teacher'])
            ->withCount('submissions')
            ->latest('due_date')
            ->paginate(15);

        // Attach current student's submission status
        $submissionMap = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->pluck('status', 'assignment_id');

        $data = $assignments->through(function ($item) use ($submissionMap) {
            $item->my_status = $submissionMap[$item->id] ?? 'pending';
            return $item;
        });

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'total' => $assignments->total(),
            ],
        ]);
    }

    public function show(Request $request, Assignment $assignment): JsonResponse
    {
        $student = $request->user()->student;
        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }
        $isInClassroom = $student->classrooms()->where('classrooms.id', $assignment->classroom_id)->exists();

        if (! $isInClassroom || ! $assignment->is_published) {
            return response()->json(['message' => 'Tugas tidak ditemukan.'], 404);
        }

        $assignment->load(['subject', 'classroom', 'teacher']);
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        return response()->json([
            'data' => $assignment,
            'submission' => $submission,
        ]);
    }

    public function submit(Request $request, Assignment $assignment): JsonResponse
    {
        $student = $request->user()->student;
        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }
        $isInClassroom = $student->classrooms()->where('classrooms.id', $assignment->classroom_id)->exists();

        if (! $isInClassroom) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('submissions', 'public');

        $isLate = $assignment->due_date->isPast();

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'notes' => $validated['notes'] ?? null,
                'status' => $isLate ? 'late' : 'submitted',
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'data' => $submission,
            'message' => $isLate ? 'Tugas terlambat dikumpulkan.' : 'Tugas berhasil dikumpulkan.',
        ], 201);
    }
}
