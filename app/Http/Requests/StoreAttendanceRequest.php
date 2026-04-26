<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'exists:schedules,id'],
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'uuid', 'exists:students,id', 'distinct'],
            'attendances.*.status' => ['required', 'string', 'in:present,absent,late,excused'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Jadwal wajib dipilih.',
            'schedule_id.exists' => 'Jadwal yang dipilih tidak ditemukan.',
            'date.required' => 'Tanggal absensi wajib diisi.',
            'date.date' => 'Tanggal absensi harus berupa tanggal yang valid.',
            'attendances.required' => 'Data absensi siswa wajib dikirim.',
            'attendances.array' => 'Format data absensi tidak valid.',
            'attendances.min' => 'Minimal satu siswa harus diisi status kehadirannya.',
            'attendances.*.student_id.required' => 'Siswa wajib dipilih.',
            'attendances.*.student_id.uuid' => 'ID siswa tidak valid.',
            'attendances.*.student_id.exists' => 'Siswa yang dipilih tidak ditemukan.',
            'attendances.*.student_id.distinct' => 'Satu siswa tidak boleh diinput lebih dari sekali.',
            'attendances.*.status.required' => 'Status kehadiran wajib diisi.',
            'attendances.*.status.in' => 'Status kehadiran harus present, absent, late, atau excused.',
        ];
    }
}
