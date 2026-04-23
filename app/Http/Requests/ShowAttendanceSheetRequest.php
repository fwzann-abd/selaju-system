<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowAttendanceSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.date' => 'Tanggal absensi harus berupa tanggal yang valid.',
        ];
    }
}
