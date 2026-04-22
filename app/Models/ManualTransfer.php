<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ManualTransfer extends Model
{
    use HasUuids;

    /**
     * Primary key is UUID string
     */
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'donation_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'transfer_amount',
        'transfer_proof',
        'transfer_date',
        'verification_status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'transfer_amount' => 'decimal:2',
            'transfer_date' => 'datetime',
            'verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function donation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function verifier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function verify(int $verifiedBy, ?string $notes = null): void
    {
        $this->update([
            'verification_status' => 'verified',
            'verification_notes' => $notes,
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ]);

        $this->donation->markAsPaid();
    }

    public function reject(int $verifiedBy, ?string $notes = null): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'verification_notes' => $notes,
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ]);

        $this->donation->markAsFailed();
    }
}
