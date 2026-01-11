<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EplinOfficer extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['student_id', 'role', 'start_date', 'end_date', 'is_active'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function recordedViolations()
    {
        return $this->hasMany(EplinViolation::class, 'recorded_by_officer_id');
    }
}
