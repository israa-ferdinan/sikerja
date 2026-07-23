<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentationDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const SECTION_PENETAPAN = 'penetapan';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const CATEGORY_TUPOKSI_SIM_TI = 'tupoksi-sim-ti';
    public const CATEGORY_STRUKTUR_ORGANISASI = 'struktur-organisasi';
    public const CATEGORY_SK_SDM_UNIT = 'sk-sdm-unit';
    public const CATEGORY_STANDAR_UNIT = 'standar-unit';
    public const CATEGORY_SOP_UNIT = 'sop-unit';
    public const CATEGORY_FORMULIR = 'formulir';

    protected $fillable = [
        'section',
        'category',
        'title',
        'document_number',
        'description',
        'document_date',
        'effective_date',
        'revision',
        'status',
        'file_path',
        'original_filename',
        'file_mime',
        'file_size',
        'uploaded_by',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'effective_date' => 'date',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public static function sections(): array
    {
        return [
            self::SECTION_PENETAPAN => 'Penetapan',
        ];
    }

    public static function penetapanCategories(): array
    {
        return [
            self::CATEGORY_TUPOKSI_SIM_TI => 'Tupoksi SIM TI',
            self::CATEGORY_STRUKTUR_ORGANISASI => 'Struktur Organisasi SIM TI',
            self::CATEGORY_SK_SDM_UNIT => 'SK SDM Unit SIM TI',
            self::CATEGORY_STANDAR_UNIT => 'Standar Unit SIM TI',
            self::CATEGORY_SOP_UNIT => 'SOP Unit SIM TI',
            self::CATEGORY_FORMULIR => 'Formulir SIM TI',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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

    public function getCategoryLabelAttribute(): string
    {
        return self::penetapanCategories()[$this->category] ?? $this->category;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getSectionLabelAttribute(): string
    {
        return self::sections()[$this->section] ?? $this->section;
    }

    public function scopePenetapan($query)
    {
        return $query->where('section', self::SECTION_PENETAPAN);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }
}