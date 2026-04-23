<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('subjects', 'code')->ignore($this->route('subject'))],
            'type' => ['nullable', 'string', 'in:Vocational,Theory,Practical,Workshop'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama mata pelajaran harus diisi.',
            'name.max' => 'Nama mata pelajaran maksimal 255 karakter.',
            'code.max' => 'Kode mata pelajaran maksimal 50 karakter.',
            'code.unique' => 'Kode mata pelajaran sudah digunakan.',
            'type.in' => 'Tipe mata pelajaran tidak valid.',
        ];
    }
}
