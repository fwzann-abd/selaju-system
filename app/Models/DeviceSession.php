<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSession extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'account_id',
        'device_id',
        'device_name',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'user_agent',
        'location',
        'last_activity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_activity' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'uuid');
    }

    /**
     * Update the last activity timestamp
     */
    public function touch($attribute = null): bool
    {
        $this->last_activity = now();
        return parent::touch($attribute);
    }
}
