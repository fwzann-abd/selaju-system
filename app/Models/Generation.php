<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Generation extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'start_years', 'end_years', 'is_active'];
    protected $casts = [
        'start_years' => 'integer',
        'end_years' => 'integer',
        'is_active' => 'boolean',
    ];
}
