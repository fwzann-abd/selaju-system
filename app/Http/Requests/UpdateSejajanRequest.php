<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSejajanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $sejajan = $this->route('sejajan');

        return $this->user() !== null
            && $sejajan
            && $sejajan->account_id === $this->user()->getKey();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $sejajanId = $this->route('sejajan')?->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:sejajans,slug,'.$sejajanId.',id',
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
