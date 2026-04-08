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
        $query = Student::with(['account:id,email', 'classrooms:id,name'])
            ->select('id', 'name', 'student_number', 'account_id');

        // Search by name or student_number (case-insensitive)
        if ($request->has('search')) {
            $search = strtolower($request->query('search'));
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(student_number) LIKE ?', ["%{$search}%"]);
            });
        }

        $students = $query->limit(10)->get();

        return response()->json([
            'data' => $students->map(function (Student $student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_number' => $student->student_number,
                    'email' => $student->account->email ?? '-',
                    'classrooms' => $student->classrooms->pluck('name')->join(', ') ?: '-',
                ];
            }),
        ]);
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json($student->select('id', 'name', 'student_number'));
    }
}
