<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EplinAttendance extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['student_id', 'attendance_date', 'status', 'arrival_time', 'notes'];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'arrival_time' => 'datetime:H:i',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
