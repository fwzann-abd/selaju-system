<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EplinViolation extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['student_id', 'violation_type_id', 'recorded_by_officer_id', 'violation_date', 'description', 'evidence', 'status'];

    protected function casts(): array
    {
        return [
            'violation_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function violationType()
    {
        return $this->belongsTo(EplinViolationType::class, 'violation_type_id');
    }

    public function recordedByOfficer()
    {
        return $this->belongsTo(EplinOfficer::class, 'recorded_by_officer_id');
    }
}
