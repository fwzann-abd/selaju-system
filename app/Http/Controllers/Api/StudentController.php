<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::select('id', 'name', 'student_number');

        // Search by name or student_number (case-insensitive)
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(student_number) LIKE ?', ["%{$search}%"]);
        }

        $students = $query->limit(10)->get();

        return response()->json([
            'data' => $students,
        ]);
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json($student->select('id', 'name', 'student_number'));
    }
}
