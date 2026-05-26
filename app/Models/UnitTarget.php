<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_year' => 'integer',
        'quarter' => 'integer',
        'target_quantity' => 'integer',
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
        $year = (int) $this->target_year;

        if ($this->period_type === 'quarterly') {
            return match ((int) $this->quarter) {
                1 => [
                    now()->setDate($year, 1, 1)->startOfDay(),
                    now()->setDate($year, 3, 31)->endOfDay(),
                ],
                2 => [
                    now()->setDate($year, 4, 1)->startOfDay(),
                    now()->setDate($year, 6, 30)->endOfDay(),
                ],
                3 => [
                    now()->setDate($year, 7, 1)->startOfDay(),
                    now()->setDate($year, 9, 30)->endOfDay(),
                ],
                4 => [
                    now()->setDate($year, 10, 1)->startOfDay(),
                    now()->setDate($year, 12, 31)->endOfDay(),
                ],
                default => [
                    now()->setDate($year, 1, 1)->startOfDay(),
                    now()->setDate($year, 12, 31)->endOfDay(),
                ],
            };
        }

        return [
            now()->setDate($year, 1, 1)->startOfDay(),
            now()->setDate($year, 12, 31)->endOfDay(),
        ];
    }

    public function matchingDailyReportsQuery(): Builder
    {
        [$startDate, $endDate] = $this->getPeriodDateRange();

        return DailyReport::query()
            ->with([
                'duty',
                'unit',
                'employee',
            ])
            ->where('unit_id', $this->unit_id)
            ->whereBetween('report_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->when($this->duty_classification_id, function ($query) {
                $query->whereHas('duty', function ($dutyQuery) {
                    $dutyQuery->where('duty_classification_id', $this->duty_classification_id);
                });
            })
            ->when($this->object_type && $this->object_type !== 'none', function ($query) {
                $query->whereHas('duty', function ($dutyQuery) {
                    $dutyQuery->where('object_type', $this->object_type);

                    if ($this->object_type === 'server') {
                        $dutyQuery->where('server_id', $this->server_id);
                    }

                    if ($this->object_type === 'application') {
                        $dutyQuery->where('application_id', $this->application_id);
                    }

                    if ($this->object_type === 'manual') {
                        $dutyQuery->where('object_name', $this->object_name);
                    }
                });
            });
    }

    public function getAchievementCountAttribute(): int
    {
        return $this->matchingDailyReportsQuery()->count();
    }

    public function getAchievementPercentageAttribute(): float
    {
        if ((int) $this->target_quantity <= 0) {
            return 0;
        }

        return round(($this->achievement_count / $this->target_quantity) * 100, 2);
    }

    public function getAchievementStatusLabelAttribute(): string
    {
        if ($this->achievement_percentage >= 100) {
            return 'Tercapai';
        }

        if ($this->achievement_percentage >= 75) {
            return 'Hampir Tercapai';
        }

        if ($this->achievement_percentage > 0) {
            return 'Berjalan';
        }

        return 'Belum Ada Capaian';
    }

    public function getAchievementStatusBadgeClassAttribute(): string
    {
        if ($this->achievement_percentage >= 100) {
            return 'bg-green-100 text-green-700';
        }

        if ($this->achievement_percentage >= 75) {
            return 'bg-yellow-100 text-yellow-700';
        }

        if ($this->achievement_percentage > 0) {
            return 'bg-blue-100 text-blue-700';
        }

        return 'bg-gray-100 text-gray-600';
    }
}