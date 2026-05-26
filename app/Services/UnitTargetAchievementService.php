<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\UnitTarget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UnitTargetAchievementService
{
    public function periodDateRange(UnitTarget $target): array
    {
        $year = (int) $target->target_year;

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

                    if ($target->object_type === 'server') {
                        $dutyQuery->where('server_id', $target->server_id);
                    }

                    if ($target->object_type === 'application') {
                        $dutyQuery->where('application_id', $target->application_id);
                    }

                    if ($target->object_type === 'manual') {
                        $objectName = Str::lower(trim((string) $target->object_name));

                        $dutyQuery->whereRaw('LOWER(TRIM(object_name)) = ?', [
                            $objectName,
                        ]);
                    }
                });
            });
    }

    public function achievementCount(UnitTarget $target): int
    {
        return $this->matchingDailyReportsQuery($target)->count();
    }

    public function achievementPercentage(UnitTarget $target): float
    {
        $targetQuantity = (int) $target->target_quantity;

        if ($targetQuantity <= 0) {
            return 0;
        }

        $percentage = ($this->achievementCount($target) / $targetQuantity) * 100;

        return round(min($percentage, 100), 2);
    }

    public function statusLabel(UnitTarget $target): string
    {
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
        $achievementCount = $this->achievementCount($target);
        $targetQuantity = (int) $target->target_quantity;

        $achievementPercentage = 0;

        if ($targetQuantity > 0) {
            $achievementPercentage = round(min(($achievementCount / $targetQuantity) * 100, 100), 2);
        }

        $remainingTarget = max($targetQuantity - $achievementCount, 0);

        [$startDate, $endDate] = $this->periodDateRange($target);

        return [
            'target_quantity' => $targetQuantity,
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
}