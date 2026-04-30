<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'account_id',
        'type',
        'title',
        'body',
        'link',
        'data',
        'read_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // ── Notification Types ──────────────────────────────────────────
    public const TYPE_ASSIGNMENT_NEW        = 'assignment_new';
    public const TYPE_ASSIGNMENT_GRADED     = 'assignment_graded';
    public const TYPE_ATTENDANCE_RECORDED   = 'attendance_recorded';
    public const TYPE_MATERIAL_UPLOADED     = 'material_uploaded';
    public const TYPE_ANNOUNCEMENT          = 'announcement';
    public const TYPE_SUBMISSION_RECEIVED   = 'submission_received';

    public static array $icons = [
        'assignment_new'      => '📝',
        'assignment_graded'   => '✅',
        'attendance_recorded' => '📋',
        'material_uploaded'   => '📁',
        'announcement'        => '📢',
        'submission_received' => '📨',
    ];

    // ── Helpers ─────────────────────────────────────────────────────

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Create a notification for a specific user.
     */
    public static function send(string $accountId, string $type, string $title, ?string $body = null, ?string $link = null, array $extra = []): self
    {
        return static::create([
            'account_id' => $accountId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'link'    => $link,
            'data'    => array_merge(['icon' => static::$icons[$type] ?? '🔔'], $extra),
        ]);
    }

    /**
     * Send the same notification to multiple users.
     */
    public static function broadcast(array $accountIds, string $type, string $title, ?string $body = null, ?string $link = null, array $extra = []): void
    {
        foreach ($accountIds as $uid) {
            static::send($uid, $type, $title, $body, $link, $extra);
        }
    }

    // ── Relationships ───────────────────────────────────────────────

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'uuid');
    }
}
