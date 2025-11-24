<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sejajan extends Model
{
    use HasUuids;

    protected $fillable = ['participant_id', 'name', 'slug', 'description', 'photo', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The owner (seller) of this store.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Products in this store.
     */
    public function products(): HasMany
    {
        return $this->hasMany(SejajanProduct::class);
    }

    /**
     * Orders placed in this store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(SejajanOrder::class);
    }
}
