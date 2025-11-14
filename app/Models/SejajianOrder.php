<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SejajianOrder extends Model
{
    use HasUuids;

    protected $fillable = ['sejajan_id', 'participant_id', 'status', 'total_price', 'notes', 'pickup_time'];

    protected $casts = [
        'total_price' => 'decimal:2',
        'pickup_time' => 'datetime',
    ];

    /**
     * The store this order is for.
     */
    public function sejajan(): BelongsTo
    {
        return $this->belongsTo(Sejajan::class);
    }

    /**
     * The customer who placed the order.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SejajianOrderItem::class);
    }
}
