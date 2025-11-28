<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SejajanCartItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'participant_id',
        'sejajan_id',
        'sejajan_product_id',
        'qty',
    ];

    public function sejajan(): BelongsTo
    {
        return $this->belongsTo(Sejajan::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SejajanProduct::class, 'sejajan_product_id');
    }
}
