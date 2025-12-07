<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'sejajan_id' => ['required', 'uuid', 'exists:sejajans,id'],
            'delivery_method' => ['required', Rule::in(['pickup', 'delivery'])],
            'pickup_time' => ['nullable', 'date'],
            'location_pickup' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:sejajan_products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'sejajan_id.required' => 'ID toko wajib diisi',
            'sejajan_id.exists' => 'Toko tidak ditemukan',
            'delivery_method.required' => 'Metode pengambilan wajib dipilih',
            'delivery_method.in' => 'Metode pengambilan tidak valid',
            'items.required' => 'Item pesanan wajib diisi',
            'items.min' => 'Minimal 1 item harus dipilih',
            'items.*.product_id.required' => 'ID produk wajib diisi',
            'items.*.product_id.exists' => 'Produk tidak ditemukan',
            'items.*.qty.required' => 'Jumlah produk wajib diisi',
            'items.*.qty.min' => 'Jumlah minimal 1',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->delivery_method === 'delivery' && empty($this->address)) {
                $validator->errors()->add('address', 'Alamat diperlukan untuk pengantaran');
            }
        });
    }
}
