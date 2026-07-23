<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentDocument extends Model
{
    use HasFactory;

    public const VISIBILITY_UNIT = 'Unit';
    public const VISIBILITY_RESTRICTED = 'Restricted';

    public const TYPE_DOKUMEN_PENDUKUNG = 'Dokumen Pendukung';
    public const TYPE_PROPOSAL = 'Proposal';
    public const TYPE_ROADMAP = 'Roadmap';
    public const TYPE_DESAIN = 'Desain';
    public const TYPE_LAPORAN_PROGRESS = 'Laporan Progress';
    public const TYPE_BERITA_ACARA = 'Berita Acara';
    public const TYPE_DOKUMENTASI = 'Dokumentasi';
    public const TYPE_LAINNYA = 'Lainnya';

    protected $fillable = [
        'development_plan_id',
        'unit_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'visibility',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public static function visibilities(): array
    {
        return [
            self::VISIBILITY_UNIT,
            self::VISIBILITY_RESTRICTED,
        ];
    }

    public static function documentTypes(): array
    {
        return [
            self::TYPE_DOKUMEN_PENDUKUNG,
            self::TYPE_PROPOSAL,
            self::TYPE_ROADMAP,
            self::TYPE_DESAIN,
            self::TYPE_LAPORAN_PROGRESS,
            self::TYPE_BERITA_ACARA,
            self::TYPE_DOKUMENTASI,
            self::TYPE_LAINNYA,
        ];
    }

    public function isRestricted(): bool
    {
        return $this->visibility === self::VISIBILITY_RESTRICTED;
    }

    public function developmentPlan()
    {
        return $this->belongsTo(DevelopmentPlan::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}