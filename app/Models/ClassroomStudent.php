<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

// Extending Pivot instead of Model since it's a pivot table
class ClassroomStudent extends Pivot
{
    protected $table = 'classroom_students';

    public $incrementing = true;    

    protected $fillable = [
        'classroom_id',
        'student_id',
        'student_position_id',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function position()
    {
        return $this->belongsTo(StudentPosition::class, 'student_position_id');
    }
}