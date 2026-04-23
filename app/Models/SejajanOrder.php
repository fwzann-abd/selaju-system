<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SejajanOrder extends Model
{
    use HasUuids;

    protected $fillable = ['sejajan_id', 'account_id', 'status', 'total_price', 'notes', 'pickup_time', 'location_pickup'];

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
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'uuid');
    }

    /**
     * Items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SejajanOrderItem::class);
    }

    /**
     * Alias for account — backward compatibility with controllers/events using 'participant'.
     */
    public function participant(): BelongsTo
    {
        return $this->account();
    }
}
