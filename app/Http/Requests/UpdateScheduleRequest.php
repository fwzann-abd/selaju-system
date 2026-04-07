<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateScheduleRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'classroom_id' => 'uuid|exists:classrooms,id',
            'teacher_id' => 'uuid|exists:teachers,id',
            'subject_id' => 'exists:subjects,id',
            'day' => 'string',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
        ];
    }
}