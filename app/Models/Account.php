<?php

namespace App\Models;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $table = 'accounts';

    public $incrementing = false;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'nomor_participant',
        'username',
        'birth_date',
        'no_telp',
        'email',
        'photo',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function deviceSessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class, 'account_id', 'uuid');
    }

    public function activeDeviceSessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class, 'account_id', 'uuid')
            ->where('is_active', true);
    }

    public function sejajans(): HasMany
    {
        return $this->hasMany(Sejajan::class, 'account_id', 'uuid');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'account_id', 'uuid');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'account_id', 'uuid');
    }

    /**
     * Shortcut to access the student's school from the account.
     */
    public function school()
    {
        return $this->hasOneThrough(
            School::class,
            Student::class,
            'account_id', // Foreign key on students table...
            'id', // Foreign key on schools table (primary key)
            'uuid', // Local key on accounts table
            'school_id' // Local key on students table that references schools
        );
    }

    /**
     * Whether the user's email has been verified.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        if ($this->hasVerifiedEmail()) {
            return false;
        }

        $this->forceFill(['email_verified_at' => now()])->save();
        event(new Verified($this));

        return true;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        // Use a frontend-friendly verification notification that points to the
        // SPA and includes the signed backend verify URL as a query parameter.
        $this->notify(new \App\Notifications\FrontendVerifyEmail);
    }

    /**
     * Get the e-mail address where verification links are sent.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Check if the account has reached the maximum number of devices
     */
    public function hasReachedMaxDevices(): bool
    {
        $maxDevices = config('auth.max_devices_per_account', 4);

        return $this->activeDeviceSessions()->count() >= $maxDevices;
    }

    /**
     * Remove the oldest device session to make room for a new one
     */
    public function removeOldestDeviceSession(): void
    {
        $oldestSession = $this->activeDeviceSessions()
            ->orderBy('last_activity', 'asc')
            ->first();

        if ($oldestSession) {
            $oldestSession->update(['is_active' => false]);
        }
    }
}
