<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'classroom_id' => 'required|exists:classrooms,id',
            'schedule_id' => 'nullable|exists:schedules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',

            // Main file — documents, images, videos, and safe "other" formats
            'file' => [
                $isUpdate ? 'nullable' : 'required',
                'file',
                'max:204800', // 200MB max
            ],

            // Optional subtitle for video materials
            'subtitle' => 'nullable|file|mimes:vtt,srt|max:2048', // 2MB max

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
            'file.max' => 'Ukuran file maksimal 200MB.',
            'subtitle.mimes' => 'Subtitle harus berupa file .vtt atau .srt.',
            'subtitle.max' => 'Ukuran subtitle maksimal 2MB.',
        ];
    }
}
