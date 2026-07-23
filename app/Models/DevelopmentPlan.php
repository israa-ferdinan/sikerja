<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentPlan extends Model
{
    use HasFactory;

    public const STATUS_USULAN = 'Usulan';
    public const STATUS_DISETUJUI = 'Disetujui';
    public const STATUS_DALAM_PROSES = 'Dalam Proses';
    public const STATUS_SELESAI = 'Selesai';
    public const STATUS_DITUNDA = 'Ditunda';
    public const STATUS_DIBATALKAN = 'Dibatalkan';

    public const CATEGORY_APLIKASI = 'Aplikasi';
    public const CATEGORY_INFRASTRUKTUR = 'Infrastruktur';
    public const CATEGORY_LAYANAN = 'Layanan';
    public const CATEGORY_KEAMANAN = 'Keamanan';
    public const CATEGORY_INTEGRASI = 'Integrasi';
    public const CATEGORY_DOKUMENTASI = 'Dokumentasi';
    public const CATEGORY_LAINNYA = 'Lainnya';

    public const PRIORITY_RENDAH = 'Rendah';
    public const PRIORITY_SEDANG = 'Sedang';
    public const PRIORITY_TINGGI = 'Tinggi';
    public const PRIORITY_MENDESAK = 'Mendesak';

    protected $fillable = [
        'unit_id',
        'evaluation_record_id',
        'control_follow_up_id',
        'title',
        'category',
        'priority',
        'description',
        'objective',
        'pic_employee_id',
        'target_start_date',
        'target_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'progress_percentage',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_start_date' => 'date',
        'target_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'progress_percentage' => 'integer',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_USULAN,
            self::STATUS_DISETUJUI,
            self::STATUS_DALAM_PROSES,
            self::STATUS_SELESAI,
            self::STATUS_DITUNDA,
            self::STATUS_DIBATALKAN,
        ];
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_APLIKASI,
            self::CATEGORY_INFRASTRUKTUR,
            self::CATEGORY_LAYANAN,
            self::CATEGORY_KEAMANAN,
            self::CATEGORY_INTEGRASI,
            self::CATEGORY_DOKUMENTASI,
            self::CATEGORY_LAINNYA,
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_RENDAH,
            self::PRIORITY_SEDANG,
            self::PRIORITY_TINGGI,
            self::PRIORITY_MENDESAK,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_DIBATALKAN;
    }

    public function isReadOnly(): bool
    {
        return in_array($this->status, [
            self::STATUS_SELESAI,
            self::STATUS_DIBATALKAN,
        ], true);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_USULAN,
            self::STATUS_DITUNDA,
            self::STATUS_DIBATALKAN,
        ], true);
    }

    public function canUpdateProgress(): bool
    {
        return in_array($this->status, [
            self::STATUS_DISETUJUI,
            self::STATUS_DALAM_PROSES,
        ], true);
    }

    public function canUploadDocument(): bool
    {
        return ! $this->isReadOnly();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function picEmployee()
    {
        return $this->belongsTo(Employee::class, 'pic_employee_id');
    }

    public function documents()
    {
        return $this->hasMany(DevelopmentDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function evaluationRecord()
    {
        return $this->belongsTo(EvaluationRecord::class);
    }

    public function controlFollowUp()
    {
        return $this->belongsTo(ControlFollowUp::class);
    }
}