<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'schedule_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'file_type',
        'category',
        'subtitle_path',
        'is_published',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_published' => 'boolean',
    ];

    // ── MIME → Category mapping ──────────────────────────────────────
    protected static array $documentMimes = [
        'application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-powerpoint',
        'text/plain', 'text/markdown', 'text/csv',
    ];

    protected static array $imageMimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'image/heic', 'image/heif', 'image/bmp', 'image/tiff',
    ];

    protected static array $videoMimes = [
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv',
        'video/webm', 'video/x-matroska', 'video/3gpp', 'video/x-flv',
    ];

    /**
     * Resolve category from MIME type.
     */
    public static function resolveCategory(string $mimeType): string
    {
        if (in_array($mimeType, static::$documentMimes)) {
            return 'document';
        }
        if (in_array($mimeType, static::$imageMimes)) {
            return 'image';
        }
        if (in_array($mimeType, static::$videoMimes)) {
            return 'video';
        }
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'other';
    }

    // ── Relationships ────────────────────────────────────────────────

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
