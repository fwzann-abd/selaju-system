<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSheetStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $attendanceRecord = $this->resource->relationLoaded('attendanceRecord')
            ? $this->resource->getRelation('attendanceRecord')
            : null;

        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'name' => $this->name,
            'student_number' => $this->student_number,
            'national_id' => $this->national_id,
            'gender' => $this->gender,
            'status' => $attendanceRecord?->status,
            'attendance_id' => $attendanceRecord?->id,
        ];
    }
}
