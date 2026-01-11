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
        $query = Student::select('id', 'nama', 'nisn');

        // Search by name or NIS
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%");
        }

        $students = $query->limit(100)->get();

        return response()->json([
            'data' => $students,
        ]);
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json($student->select('id', 'nama', 'nisn'));
    }
}
