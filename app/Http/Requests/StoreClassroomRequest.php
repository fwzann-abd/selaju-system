<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'string', 'in:X,XI,XII'],
            'jurusan' => ['required', 'string', 'in:AKL,MPL,TLG,PM,TKF,TLM,DKV,PPL,TJK,TET'],
            'academic_year' => ['required', 'string', 'max:9'],
            'teacher_id' => ['nullable', 'uuid', 'exists:teachers,id'],
            'rombel' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kelas harus diisi.',
            'name.string' => 'Nama kelas harus berupa teks.',
            'name.max' => 'Nama kelas maksimal 255 karakter.',
            'tingkat.required' => 'Tingkatan harus dipilih.',
            'tingkat.in' => 'Tingkatan yang dipilih tidak valid.',
            'jurusan.required' => 'Jurusan harus dipilih.',
            'jurusan.in' => 'Jurusan yang dipilih tidak valid.',
            'academic_year.required' => 'Tahun ajaran harus dipilih.',
            'academic_year.max' => 'Tahun ajaran maksimal 9 karakter.',
            'teacher_id.uuid' => 'Format guru tidak valid.',
            'teacher_id.exists' => 'Guru yang dipilih tidak ditemukan.',
            'rombel.max' => 'Rombel maksimal 255 karakter.',
        ];
    }
}
