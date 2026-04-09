<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassroomStudent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::creating(function (Classroom $classroom): void {
            if (! $classroom->slug) {
                $slug = Str::slug($classroom->name ?: Str::random(8));
                $originalSlug = $slug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = sprintf('%s-%s', $originalSlug, Str::lower(Str::random(4)));
                    $counter++;
                }

                $classroom->slug = $slug;
            }
        });

        static::updating(function (Classroom $classroom): void {
            if ($classroom->isDirty('name')) {
                $slug = Str::slug($classroom->name ?: Str::random(8));
                $originalSlug = $slug;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $classroom->id)->exists()) {
                    $slug = sprintf('%s-%s', $originalSlug, Str::lower(Str::random(4)));
                    $counter++;
                }

                $classroom->slug = $slug;
            }
        });
    }

    protected $fillable = [
        'name',
        'tingkat',
        'jurusan',
        'rombel',
        'slug',
        'teacher_id',
        'academic_year',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'classroom_students')
            ->using(ClassroomStudent::class)
            ->withPivot('student_position_id')
            ->withTimestamps();
    }

    public function classroomStudents(): HasMany
    {
        return $this->hasMany(ClassroomStudent::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
