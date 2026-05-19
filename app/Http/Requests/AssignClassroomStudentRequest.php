<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignClassroomStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classroom_id' => 'required|uuid|exists:classrooms,id',
            'students' => 'required|array',
            'students.*.student_id' => 'required|uuid|exists:students,id',
            'students.*.student_position_id' => 'nullable|exists:student_positions,id',
        ];
    }
}
