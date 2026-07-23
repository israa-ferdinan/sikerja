<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\ControlFollowUp;

class EvaluationRecord extends Model
{
    protected $fillable = [
        'unit_id',
        'title',
        'evaluation_type',
        'evaluation_date',
        'source',
        'findings',
        'recommendation',
        'status',
        'created_by',
        'updated_by',
        'published_at',
        'archived_at',
        'zoom_link',
        'google_drive_link',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public const TYPE_INTERNAL_MEETING = 'internal_meeting';
    public const TYPE_EXTERNAL_MEETING = 'external_meeting';
    public const TYPE_ACTIVITY = 'activity';
    public const TYPE_TARGET = 'target';
    public const TYPE_OTHER = 'other';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public static function evaluationTypeOptions(): array
    {
        return [
            self::TYPE_INTERNAL_MEETING => 'Rapat Internal',
            self::TYPE_EXTERNAL_MEETING => 'Rapat Eksternal',
            self::TYPE_ACTIVITY => 'Evaluasi Kegiatan',
            self::TYPE_TARGET => 'Evaluasi Target',
            self::TYPE_OTHER => 'Lainnya',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EvaluationDocument::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getEvaluationTypeLabelAttribute(): string
    {
        return self::evaluationTypeOptions()[$this->evaluation_type] ?? 'Lainnya';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? 'Draft';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            self::STATUS_ARCHIVED => 'bg-slate-100 text-slate-600 ring-slate-200',
            default => 'bg-amber-50 text-amber-700 ring-amber-100',
        };
    }

    public function getEvaluationTypeBadgeClassAttribute(): string
    {
        return match ($this->evaluation_type) {
            self::TYPE_INTERNAL_MEETING => 'bg-blue-50 text-blue-700 ring-blue-100',
            self::TYPE_EXTERNAL_MEETING => 'bg-violet-50 text-violet-700 ring-violet-100',
            self::TYPE_ACTIVITY => 'bg-cyan-50 text-cyan-700 ring-cyan-100',
            self::TYPE_TARGET => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
            default => 'bg-slate-100 text-slate-600 ring-slate-200',
        };
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function publishMissingFields(): array
    {
        $missing = [];

        if (blank($this->title)) {
            $missing[] = 'Judul Evaluasi';
        }

        if (blank($this->evaluation_type)) {
            $missing[] = 'Jenis Evaluasi';
        }

        if (blank($this->evaluation_date)) {
            $missing[] = 'Tanggal Evaluasi';
        }

        if (blank($this->findings)) {
            $missing[] = 'Temuan / Hasil Evaluasi';
        }

        return $missing;
    }

    public function canBePublished(): bool
    {
        return $this->isDraft() && empty($this->publishMissingFields());
    }

    public function controlFollowUps(): HasMany
    {
        return $this->hasMany(ControlFollowUp::class);
    }
}