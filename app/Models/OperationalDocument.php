<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OperationalDocument extends Model
{
    public const CATEGORY_NETWORK = 'rekap_jaringan';
    public const CATEGORY_LAB_INVENTORY = 'inventaris_lab';
    public const CATEGORY_LAB_CHECK = 'pemeriksaan_lab';
    public const CATEGORY_RPK_USAGE = 'pemakaian_rpk';
    public const CATEGORY_ZOOM = 'permintaan_zoom';
    public const CATEGORY_OTHER = 'lainnya';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const VISIBILITY_UNIT = 'unit';
    public const VISIBILITY_RESTRICTED = 'restricted';

    protected $fillable = [
        'unit_id',
        'category',
        'title',
        'document_number',
        'period_month',
        'period_year',
        'document_date',
        'description',
        'visibility',
        'status',
        'file_path',
        'file_name',
        'file_original_name',
        'file_mime_type',
        'file_size',
        'uploaded_by_user_id',
        'updated_by_user_id',
        'published_by_user_id',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'file_size' => 'integer',
        'period_month' => 'integer',
        'period_year' => 'integer',
    ];

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_NETWORK => 'Rekap Jaringan',
            self::CATEGORY_LAB_INVENTORY => 'Inventaris Lab',
            self::CATEGORY_LAB_CHECK => 'Pemeriksaan Lab',
            self::CATEGORY_RPK_USAGE => 'Pemakaian RPK',
            self::CATEGORY_ZOOM => 'Permintaan Zoom',
            self::CATEGORY_OTHER => 'Lainnya',
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

    public static function visibilityOptions(): array
    {
        return [
            self::VISIBILITY_UNIT => 'Unit',
            self::VISIBILITY_RESTRICTED => 'Restricted',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->category] ?? $this->category;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function getVisibilityLabelAttribute(): string
    {
        return self::visibilityOptions()[$this->visibility] ?? $this->visibility;
    }

    public function getPeriodLabelAttribute(): string
    {
        if (! $this->period_month || ! $this->period_year) {
            return '-';
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return ($months[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }

    public function getFileSizeLabelAttribute(): string
    {
        if (! $this->file_size) {
            return '-';
        }

        if ($this->file_size >= 1024 * 1024) {
            return number_format($this->file_size / 1024 / 1024, 2) . ' MB';
        }

        return number_format($this->file_size / 1024, 2) . ' KB';
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

    public function isEditable(): bool
    {
        return $this->isDraft();
    }

    public function isDeletable(): bool
    {
        return $this->isDraft();
    }

    public function canPublish(): bool
    {
        return $this->isDraft();
    }

    public function canArchive(): bool
    {
        return $this->isPublished();
    }

    public function scopeForUnit(Builder $query, ?int $unitId): Builder
    {
        if (! $unitId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('unit_id', $unitId);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeVisibleForEmployee(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where('visibility', self::VISIBILITY_UNIT);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by_user_id');
    }
}