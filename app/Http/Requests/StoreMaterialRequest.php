<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreMaterialRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'schedule_id' => 'required|exists:schedules,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
        ];
    }
}