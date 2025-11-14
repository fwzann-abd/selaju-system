<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SejajanOrderItem extends Model
{
    use HasUuids;

    protected $fillable = ['sejajan_order_id', 'sejajan_product_id', 'qty', 'price', 'subtotal'];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * The order this item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SejajanOrder::class);
    }

    /**
     * The product in this order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(SejajanProduct::class);
    }
}
