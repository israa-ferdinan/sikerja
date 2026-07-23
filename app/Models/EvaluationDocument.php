<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDocument extends Model
{
    protected $fillable = [
        'evaluation_record_id',
        'unit_id',
        'title',
        'document_type',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public const TYPE_NOTULEN = 'notulen';
    public const TYPE_UNDANGAN = 'undangan';
    public const TYPE_BERITA_ACARA = 'berita_acara';
    public const TYPE_LAPORAN_EVALUASI = 'laporan_evaluasi';
    public const TYPE_DOKUMENTASI = 'dokumentasi';
    public const TYPE_LAINNYA = 'lainnya';

    public static function documentTypeOptions(): array
    {
        return [
            self::TYPE_NOTULEN => 'Notulen',
            self::TYPE_UNDANGAN => 'Undangan',
            self::TYPE_BERITA_ACARA => 'Berita Acara',
            self::TYPE_LAPORAN_EVALUASI => 'Laporan Evaluasi',
            self::TYPE_DOKUMENTASI => 'Dokumentasi',
            self::TYPE_LAINNYA => 'Lainnya',
        ];
    }

    public function evaluationRecord(): BelongsTo
    {
        return $this->belongsTo(EvaluationRecord::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return self::documentTypeOptions()[$this->document_type] ?? 'Lainnya';
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (blank($this->file_size)) {
            return '-';
        }

        $bytes = (int) $this->file_size;

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}