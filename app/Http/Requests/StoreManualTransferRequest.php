<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualTransferRequest extends FormRequest
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
            'donation_id' => ['required', 'exists:donations,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'transfer_amount' => ['required', 'numeric', 'min:1000'],
            'transfer_proof' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            'transfer_date' => ['required', 'date'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'donation_id.required' => 'ID donasi wajib diisi',
            'donation_id.exists' => 'Donasi tidak ditemukan',
            'bank_name.required' => 'Nama bank wajib diisi',
            'account_number.required' => 'Nomor rekening wajib diisi',
            'account_holder_name.required' => 'Nama pemilik rekening wajib diisi',
            'transfer_amount.required' => 'Jumlah transfer wajib diisi',
            'transfer_amount.numeric' => 'Jumlah transfer harus berupa angka',
            'transfer_amount.min' => 'Jumlah transfer minimal Rp 1.000',
            'transfer_proof.required' => 'Bukti transfer wajib diupload',
            'transfer_proof.image' => 'Bukti transfer harus berupa gambar',
            'transfer_proof.max' => 'Ukuran maksimal 2MB',
            'transfer_proof.mimes' => 'Format yang diizinkan: jpg, jpeg, png',
            'transfer_date.required' => 'Tanggal transfer wajib diisi',
            'transfer_date.date' => 'Format tanggal tidak valid',
        ];
    }
}
