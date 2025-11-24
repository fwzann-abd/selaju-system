<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_group_id',
        'module_access_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }

    public function moduleAccess()
    {
        return $this->belongsTo(ModuleAccess::class);
    }
}
