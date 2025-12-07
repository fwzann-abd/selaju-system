<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SejajanCategory extends Model
{
    use HasUuids;

    protected $fillable = ['sejajan_id', 'name', 'slug', 'description', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The store this category belongs to.
     */
    public function sejajan(): BelongsTo
    {
        return $this->belongsTo(Sejajan::class);
    }

    /**
     * Products in this category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(SejajanProduct::class, 'category_id');
    }
}
