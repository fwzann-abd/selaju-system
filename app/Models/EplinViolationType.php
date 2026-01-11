<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EplinViolationType extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description'];

    public function violations()
    {
        return $this->hasMany(EplinViolation::class, 'violation_type_id');
    }
}
