<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SejajianProduct extends Model
{
    use HasUuids;

    protected $fillable = ['sejajan_id', 'name', 'slug', 'description', 'price', 'stock', 'photo', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The store this product belongs to.
     */
    public function sejajan(): BelongsTo
    {
        return $this->belongsTo(Sejajan::class);
    }

    /**
     * Order items containing this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(SejajianOrderItem::class);
    }
}
