<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\UnitTarget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UnitTargetAchievementService
{
    public function periodDateRange(UnitTarget $target): array
    {
        $year = (int) $target->target_year;

        // NOTE:
        // R7 final memakai target tahunan.
        // Logic quarterly tetap dipertahankan sementara untuk backward compatibility
        // jika masih ada data lama yang period_type-nya quarterly.
        if ($target->period_type === 'quarterly') {
            return match ((int) $target->quarter) {
                1 => [
                    Carbon::createFromDate($year, 1, 1)->startOfDay(),
                    Carbon::createFromDate($year, 3, 31)->endOfDay(),
                ],
                2 => [
                    Carbon::createFromDate($year, 4, 1)->startOfDay(),
                    Carbon::createFromDate($year, 6, 30)->endOfDay(),
                ],
                3 => [
                    Carbon::createFromDate($year, 7, 1)->startOfDay(),
                    Carbon::createFromDate($year, 9, 30)->endOfDay(),
                ],
                4 => [
                    Carbon::createFromDate($year, 10, 1)->startOfDay(),
                    Carbon::createFromDate($year, 12, 31)->endOfDay(),
                ],
                default => [
                    Carbon::createFromDate($year, 1, 1)->startOfDay(),
                    Carbon::createFromDate($year, 12, 31)->endOfDay(),
                ],
            };
        }

        return [
            Carbon::createFromDate($year, 1, 1)->startOfDay(),
            Carbon::createFromDate($year, 12, 31)->endOfDay(),
        ];
    }

    public function matchingDailyReportsQuery(UnitTarget $target): Builder
    {
        [$startDate, $endDate] = $this->periodDateRange($target);

        return DailyReport::query()
            ->with([
                'duty',
                'unit',
                'employee',
                'server',
                'application',
            ])
            ->where('unit_id', $target->unit_id)
            ->whereBetween('report_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->when($target->duty_classification_id, function ($query) use ($target) {
                $query->whereHas('duty', function ($dutyQuery) use ($target) {
                    $dutyQuery->where('duty_classification_id', $target->duty_classification_id);
                });
            })
            ->when($target->object_type && $target->object_type !== 'none', function ($query) use ($target) {
                $query->whereHas('duty', function ($dutyQuery) use ($target) {
                    $dutyQuery->where('object_type', $target->object_type);
                });

                if ($target->object_type === 'server' && $target->server_id) {
                    $query->where('server_id', $target->server_id);
                }

                if ($target->object_type === 'application' && $target->application_id) {
                    $query->where('application_id', $target->application_id);
                }
            });
    }

    public function achievementMethod(UnitTarget $target): string
    {
        return $target->achievement_method ?: 'auto_report';
    }

    public function achievementCount(UnitTarget $target): int
    {
        return match ($this->achievementMethod($target)) {
            'manual_progress' => (int) $target->manual_progress,
            'manual_status' => $this->manualStatusProgressValue($target),
            default => $this->matchingDailyReportsQuery($target)->count(),
        };
    }

    public function targetQuantity(UnitTarget $target): int
    {
        return match ($this->achievementMethod($target)) {
            'manual_progress',
            'manual_status' => 100,
            default => max((int) $target->target_quantity, 0),
        };
    }

    public function targetUnit(UnitTarget $target): string
    {
        return match ($this->achievementMethod($target)) {
            'manual_progress',
            'manual_status' => '%',
            default => $target->target_unit ?: 'kegiatan',
        };
    }

    public function achievementPercentage(UnitTarget $target): float
    {
        $targetQuantity = $this->targetQuantity($target);

        if ($targetQuantity <= 0) {
            return 0;
        }

        $percentage = ($this->achievementCount($target) / $targetQuantity) * 100;

        return round(min($percentage, 100), 2);
    }

    public function statusLabel(UnitTarget $target): string
    {
        if ($this->achievementMethod($target) === 'manual_status') {
            return match ($target->manual_status ?? 'not_started') {
                'completed' => 'Selesai',
                'in_progress' => 'Berjalan',
                default => 'Belum Mulai',
            };
        }

        $percentage = $this->achievementPercentage($target);

        if ($percentage >= 100) {
            return 'Tercapai';
        }

        if ($percentage >= 75) {
            return 'Hampir Tercapai';
        }

        if ($percentage > 0) {
            return 'Dalam Proses';
        }

        return 'Belum Ada Capaian';
    }

    public function statusBadgeClass(UnitTarget $target): string
    {
        if ($this->achievementMethod($target) === 'manual_status') {
            return match ($target->manual_status ?? 'not_started') {
                'completed' => 'bg-green-100 text-green-700',
                'in_progress' => 'bg-amber-100 text-amber-700',
                default => 'bg-gray-100 text-gray-600',
            };
        }

        $percentage = $this->achievementPercentage($target);

        if ($percentage >= 100) {
            return 'bg-green-100 text-green-700';
        }

        if ($percentage >= 75) {
            return 'bg-blue-100 text-blue-700';
        }

        if ($percentage > 0) {
            return 'bg-amber-100 text-amber-700';
        }

        return 'bg-gray-100 text-gray-600';
    }

    public function summary(UnitTarget $target): array
    {
        [$startDate, $endDate] = $this->periodDateRange($target);

        $achievementMethod = $this->achievementMethod($target);
        $targetQuantity = $this->targetQuantity($target);
        $achievementCount = $this->achievementCount($target);
        $achievementPercentage = $this->achievementPercentage($target);
        $remainingTarget = max($targetQuantity - $achievementCount, 0);

        return [
            'achievement_method' => $achievementMethod,
            'achievement_method_label' => $this->achievementMethodLabel($achievementMethod),
            'target_quantity' => $targetQuantity,
            'target_unit' => $this->targetUnit($target),
            'achievement_count' => $achievementCount,
            'remaining_target' => $remainingTarget,
            'achievement_percentage' => $achievementPercentage,
            'status_label' => $this->statusLabel($target),
            'status_badge_class' => $this->statusBadgeClass($target),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'period_label' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
        ];
    }

    private function manualStatusProgressValue(UnitTarget $target): int
    {
        return match ($target->manual_status ?? 'not_started') {
            'completed' => 100,
            'in_progress' => 50,
            default => 0,
        };
    }

    private function achievementMethodLabel(string $method): string
    {
        return match ($method) {
            'manual_progress' => 'Manual Progress',
            'manual_status' => 'Manual Status',
            default => 'Otomatis dari Laporan Harian',
        };
    }
}