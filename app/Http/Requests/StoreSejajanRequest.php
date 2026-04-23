<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSejajanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sejajans,slug',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama toko wajib diisi.',
            'name.max' => 'Nama toko maksimal 255 karakter.',
            'slug.unique' => 'Slug toko sudah digunakan.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
