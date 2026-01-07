<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:1000'],
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_ig' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(['qris', 'virtual_account', 'manual_transfer'])],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal donasi wajib diisi',
            'amount.numeric' => 'Nominal donasi harus berupa angka',
            'amount.min' => 'Nominal minimal Rp 1.000',
            'donor_name.required' => 'Nama donatur wajib diisi',
            'donor_name.max' => 'Nama donatur maksimal 255 karakter',
            'donor_ig.max' => 'User IG maksimal 255 karakter',
            'message.max' => 'Pesan maksimal 1000 karakter',
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'payment_method.in' => 'Metode pembayaran tidak valid',
        ];
    }
}
