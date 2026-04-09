<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:accounts,username'],
            'email' => ['required', 'email', 'max:255', 'unique:accounts,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
