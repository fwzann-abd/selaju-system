<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreScheduleRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'classroom_id' => 'required|uuid|exists:classrooms,id',
            'teacher_id' => 'required|uuid|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }
}