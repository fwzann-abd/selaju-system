<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');

        return [
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('accounts', 'username')->ignore($teacher->account_id, 'uuid')],
            'email' => ['required', 'email', 'max:255', Rule::unique('accounts', 'email')->ignore($teacher->account_id, 'uuid')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
