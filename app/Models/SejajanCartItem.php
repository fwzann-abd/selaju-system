<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SejajanCartItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'account_id',
        'sejajan_id',
        'sejajan_product_id',
        'qty',
    ];

    public function sejajan(): BelongsTo
    {
        return $this->belongsTo(Sejajan::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'uuid');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SejajanProduct::class, 'sejajan_product_id');
    }
}
