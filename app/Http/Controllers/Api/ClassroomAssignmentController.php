<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignClassroomStudentRequest;
use App\Models\Classroom;

class ClassroomAssignmentController extends Controller
{
    public function assignStudent(AssignClassroomStudentRequest $request)
    {
        $validated = $request->validated();
        $classroom = Classroom::findOrFail($validated['classroom_id']);

        $syncData = [];
        foreach ($validated['students'] as $studentData) {
            $syncData[$studentData['student_id']] = [
                'student_position_id' => $studentData['student_position_id'] ?? null,
            ];
        }

        $classroom->students()->syncWithoutDetaching($syncData);

        return response()->json([
            'status' => 'success',
            'message' => 'Students successfully assigned to classroom',
        ]);
    }
}
