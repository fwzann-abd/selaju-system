<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
    /**
     * Boot function from Laravel.
     */
    protected static function bootUuidTrait()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Scope a query to find by UUID.
     */
    public function scopeFindByUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}
