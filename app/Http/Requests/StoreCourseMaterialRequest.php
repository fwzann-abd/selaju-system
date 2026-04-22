<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'classroom_id' => 'required|exists:classrooms,id',
            'schedule_id' => 'nullable|exists:schedules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xlsx,xls,mp4,mov,avi|max:102400', // 100MB
            'is_published' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'classroom_id.required' => 'Kelas harus dipilih.',
            'classroom_id.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'title.required' => 'Judul materi wajib diisi.',
            'title.max' => 'Judul materi maksimal 255 karakter.',
            'file.required' => 'File materi wajib diunggah.',
            'file.mimes' => 'File harus berupa PDF, DOC, DOCX, PPT, PPTX, XLSX, XLS, MP4, MOV, atau AVI.',
            'file.max' => 'Ukuran file maksimal 100MB.',
        ];
    }
}
