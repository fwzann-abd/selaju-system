<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (! $this->has('attendances') && $this->has('students')) {
            $students = $this->input('students', []);
            $input['attendances'] = array_map(static function ($student) {
                return [
                    'student_id' => $student['student_id'] ?? $student['id'] ?? $student['studentId'] ?? null,
                    'status' => $student['status'] ?? $student['attendance_status'] ?? null,
                ];
            }, is_array($students) ? $students : []);
        }

        if ($this->has('scheduleId') && ! $this->has('schedule_id')) {
            $input['schedule_id'] = $this->input('scheduleId');
        }

        if ($this->filled('date')) {
            $date = $this->input('date');

            if (is_string($date)) {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                    try {
                        $input['date'] = Carbon::createFromFormat('d/m/Y', $date)->toDateString();
                    } catch (\Exception $e) {
                        // Leave original date for validation.
                    }
                } else {
                    try {
                        $input['date'] = Carbon::parse($date)->toDateString();
                    } catch (\Exception $e) {
                        // Leave original date for validation.
                    }
                }
            }
        }

        $this->merge($input);
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'exists:schedules,id'],
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'uuid', 'exists:students,id', 'distinct'],
            'attendances.*.status' => ['required', 'string', 'in:present,absent,permit,sick'],
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
            'attendances.*.status.in' => 'Status kehadiran harus present, absent, permit, atau sick.',
        ];
    }
}
