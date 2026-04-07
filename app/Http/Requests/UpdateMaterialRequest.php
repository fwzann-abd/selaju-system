<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateMaterialRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'title' => 'string|max:255',
            'file' => 'nullable|file|max:10240',
        ];
    }
}