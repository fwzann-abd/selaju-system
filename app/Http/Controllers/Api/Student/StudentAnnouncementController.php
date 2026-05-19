<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentAnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }

        $classroomIds = $student->classrooms()->pluck('classrooms.id')->toArray();

        $announcements = Announcement::forClassrooms($classroomIds)
            ->with(['author', 'classroom'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $announcements->items(),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }
}
