<?php

namespace App\Models;

use App\Services\UnitTargetAchievementService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UnitTargetSupport;
use App\Models\UnitTargetProgressUpdate;

class UnitTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'duty_classification_id',
        'target_name',
        'target_description',
        'target_year',
        'period_type',
        'quarter',
        'object_type',
        'server_id',
        'application_id',
        'object_name',
        'target_quantity',
        'target_unit',
        'achievement_method',
        'manual_progress',
        'manual_status',
        'manual_progress_note',
        'manual_progress_updated_by',
        'manual_progress_updated_at',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_year' => 'integer',
        'quarter' => 'integer',
        'target_quantity' => 'integer',
        'manual_progress' => 'integer',
        'manual_progress_updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function classification()
    {
        return $this->belongsTo(DutyClassification::class, 'duty_classification_id');
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function manualProgressUpdater()
    {
        return $this->belongsTo(User::class, 'manual_progress_updated_by');
    }

    public function progressUpdates()
    {
        return $this->hasMany(UnitTargetProgressUpdate::class, 'unit_target_id')
            ->latest();
    }

    public function latestProgressUpdate()
    {
        return $this->hasOne(UnitTargetProgressUpdate::class, 'unit_target_id')
            ->latestOfMany();
    }

    public function getAchievementMethodLabelAttribute(): string
    {
        return match ($this->achievement_method ?? 'auto_report') {
            'auto_report' => 'Otomatis dari Laporan Harian',
            'manual_progress' => 'Manual Progress',
            'manual_status' => 'Manual Status',
            default => 'Metode Capaian',
        };
    }

    public function getAchievementHelpTextAttribute(): string
    {
        return match ($this->achievement_method ?? 'auto_report') {
            'manual_progress' => 'Capaian diperbarui manual dalam bentuk progress persen.',
            'manual_status' => 'Capaian diperbarui manual berdasarkan status pekerjaan.',
            default => 'Capaian dihitung otomatis dari laporan harian yang cocok.',
        };
    }

    public function getManualStatusLabelAttribute(): string
    {
        return match ($this->manual_status ?? 'not_started') {
            'not_started' => 'Belum Mulai',
            'in_progress' => 'Berjalan',
            'completed' => 'Selesai',
            default => '-',
        };
    }

    public function getManualStatusBadgeClassAttribute(): string
    {
        return match ($this->manual_status ?? 'not_started') {
            'completed' => 'bg-green-100 text-green-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            'not_started' => 'bg-gray-100 text-gray-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        if ($this->period_type === 'annual') {
            return 'Tahunan';
        }

        if ($this->period_type === 'quarterly') {
            return 'Triwulan ' . $this->quarter;
        }

        return '-';
    }

    public function getObjectTypeLabelAttribute(): string
    {
        return match ($this->object_type) {
            'server' => 'Server',
            'application' => 'Aplikasi',
            'manual' => 'Manual',
            'none', null => 'Umum',
            default => ucfirst($this->object_type),
        };
    }

    public function getWorkObjectLabelAttribute(): string
    {
        if ($this->object_type === 'server') {
            return $this->server?->name ?? '-';
        }

        if ($this->object_type === 'application') {
            return $this->application?->name ?? '-';
        }

        if ($this->object_type === 'manual') {
            return $this->object_name ?: '-';
        }

        return 'Umum';
    }

    public function getPeriodBadgeClassAttribute(): string
    {
        return match ($this->period_type) {
            'annual' => 'bg-blue-100 text-blue-700',
            'quarterly' => 'bg-purple-100 text-purple-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_active
            ? 'bg-green-100 text-green-700'
            : 'bg-gray-100 text-gray-600';
    }

    public function getTargetSummaryAttribute(): string
    {
        if (in_array($this->achievement_method, ['manual_progress', 'manual_status'], true)) {
            return '100 %';
        }

        return number_format($this->target_quantity, 0, ',', '.') . ' ' . $this->target_unit;
    }

    public function getObjectSummaryAttribute(): string
    {
        $type = $this->object_type_label;
        $object = $this->work_object_label;

        if ($object === 'Umum' || $object === '-') {
            return $type;
        }

        return $type . ' — ' . $object;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('target_year', $year);
    }

    public function scopeForUnit($query, int $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function getPeriodDateRange(): array
    {
        return app(UnitTargetAchievementService::class)
            ->periodDateRange($this);
    }

    public function matchingDailyReportsQuery(): Builder
    {
        return app(UnitTargetAchievementService::class)
            ->matchingDailyReportsQuery($this);
    }

    public function getAchievementCountAttribute(): int
    {
        return app(UnitTargetAchievementService::class)
            ->achievementCount($this);
    }

    public function getAchievementPercentageAttribute(): float
    {
        return app(UnitTargetAchievementService::class)
            ->achievementPercentage($this);
    }

    public function getAchievementStatusLabelAttribute(): string
    {
        return app(UnitTargetAchievementService::class)
            ->statusLabel($this);
    }

    public function getAchievementStatusBadgeClassAttribute(): string
    {
        return app(UnitTargetAchievementService::class)
            ->statusBadgeClass($this);
    }

    public function supports()
    {
        return $this->hasMany(UnitTargetSupport::class);
    }

    public function activeSupports()
    {
        return $this->hasMany(UnitTargetSupport::class)
            ->where('is_active', true)
            ->latest();
    }

    public function getSupportsCountAttribute(): int
    {
        if ($this->relationLoaded('supports')) {
            return $this->supports->count();
        }

        return $this->supports()->count();
    }
}