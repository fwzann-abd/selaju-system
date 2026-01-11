<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebexAttendance extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'webex_attendances';

    protected $fillable = [
        'ekskul_id',
        'pengurus_id',
        'attendance_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(WebexEkskul::class, 'ekskul_id');
    }

    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(WebexPengurus::class, 'pengurus_id');
    }
}
