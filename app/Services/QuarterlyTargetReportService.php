<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\UnitTarget;
use App\Models\UnitTargetProgressUpdate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class QuarterlyTargetReportService
{
    public function quarterLabel(int $quarter): string
    {
        return match ($quarter) {
            1 => 'Triwulan I',
            2 => 'Triwulan II',
            3 => 'Triwulan III',
            4 => 'Triwulan IV',
            default => 'Triwulan',
        };
    }

    public function quarterPeriodLabel(int $year, int $quarter): string
    {
        [$startDate, $endDate] = $this->quarterRange($year, $quarter);

        return strtoupper($startDate->translatedFormat('F')) . ' - ' . strtoupper($endDate->translatedFormat('F')) . ' ' . $year;
    }

    public function quarterRange(int $year, int $quarter): array
    {
        return match ($quarter) {
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
                Carbon::createFromDate($year, 3, 31)->endOfDay(),
            ],
        };
    }

    public function cumulativeRange(int $year, int $quarter): array
    {
        [, $endDate] = $this->quarterRange($year, $quarter);

        return [
            Carbon::createFromDate($year, 1, 1)->startOfDay(),
            $endDate,
        ];
    }

    public function buildRows(?int $unitId, int $year, int $quarter): Collection
    {
        [$periodStart, $periodEnd] = $this->quarterRange($year, $quarter);
        [$cumulativeStart, $cumulativeEnd] = $this->cumulativeRange($year, $quarter);

        return UnitTarget::query()
            ->with([
                'unit',
                'classification',
                'server',
                'application',
                'activeSupports.uploader',
                'progressUpdates.updater',
                'manualProgressUpdater',
            ])
            ->where('target_year', $year)
            ->where('period_type', 'annual')
            ->where('is_active', true)
            ->when($unitId, function ($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->orderBy('unit_id')
            ->orderBy('duty_classification_id')
            ->orderBy('target_name')
            ->get()
            ->map(function (UnitTarget $target) use ($periodStart, $periodEnd, $cumulativeStart, $cumulativeEnd) {
                return $this->buildTargetRow(
                    target: $target,
                    periodStart: $periodStart,
                    periodEnd: $periodEnd,
                    cumulativeStart: $cumulativeStart,
                    cumulativeEnd: $cumulativeEnd
                );
            })
            ->values();
    }

    private function buildTargetRow(
        UnitTarget $target,
        Carbon $periodStart,
        Carbon $periodEnd,
        Carbon $cumulativeStart,
        Carbon $cumulativeEnd
    ): array {
        $method = $target->achievement_method ?: 'auto_report';

        return match ($method) {
            'manual_progress' => $this->buildManualProgressRow($target, $periodStart, $periodEnd, $cumulativeEnd),
            'manual_status' => $this->buildManualStatusRow($target, $periodStart, $periodEnd, $cumulativeEnd),
            default => $this->buildAutoReportRow($target, $periodStart, $periodEnd, $cumulativeStart, $cumulativeEnd),
        };
    }

    private function buildAutoReportRow(
        UnitTarget $target,
        Carbon $periodStart,
        Carbon $periodEnd,
        Carbon $cumulativeStart,
        Carbon $cumulativeEnd
    ): array {
        $periodReports = $this->matchingDailyReportsQueryBetween($target, $periodStart, $periodEnd)
            ->get();

        $cumulativeReports = $this->matchingDailyReportsQueryBetween($target, $cumulativeStart, $cumulativeEnd)
            ->get();

        $targetQuantity = max((int) $target->target_quantity, 0);
        $periodAchievement = $periodReports->count();
        $cumulativeAchievement = $cumulativeReports->count();

        $percentage = $this->percentage($cumulativeAchievement, $targetQuantity);
        $remaining = max($targetQuantity - $cumulativeAchievement, 0);

        return $this->baseRow($target, [
            'achievement_method' => 'auto_report',
            'achievement_method_label' => 'Otomatis dari Laporan Harian',
            'target_value' => $targetQuantity,
            'target_unit' => $target->target_unit ?: 'kegiatan',
            'target_tahunan' => $this->formatValue($targetQuantity, $target->target_unit ?: 'kegiatan'),
            'capaian_periode_value' => $periodAchievement,
            'capaian_periode' => $this->formatValue($periodAchievement, $target->target_unit ?: 'kegiatan'),
            'capaian_kumulatif_value' => $cumulativeAchievement,
            'capaian_kumulatif' => $this->formatValue($cumulativeAchievement, $target->target_unit ?: 'kegiatan'),
            'selisih_value' => $remaining,
            'selisih' => $this->formatValue($remaining, $target->target_unit ?: 'kegiatan'),
            'persentase_kumulatif' => $percentage,
            'persentase_kumulatif_label' => $this->formatPercentage($percentage),
            'status' => $this->statusLabel($percentage),
            'catatan' => '-',
            'tindakan_perbaikan' => '-',
            'pegawai_pelaksana' => $this->employeesFromReports($cumulativeReports),
            'monitoring' => $this->monitoringText($percentage, $cumulativeEnd),
        ]);
    }

    private function buildManualProgressRow(
        UnitTarget $target,
        Carbon $periodStart,
        Carbon $periodEnd,
        Carbon $cumulativeEnd
    ): array {
        $currentSnapshot = $this->latestManualSnapshot($target, $cumulativeEnd);
        $previousSnapshot = $this->latestManualSnapshot($target, $periodStart->copy()->subSecond());

        $currentProgress = (int) ($currentSnapshot['progress_value'] ?? 0);
        $previousProgress = (int) ($previousSnapshot['progress_value'] ?? 0);

        $periodProgress = max($currentProgress - $previousProgress, 0);
        $remaining = max(100 - $currentProgress, 0);

        return $this->baseRow($target, [
            'achievement_method' => 'manual_progress',
            'achievement_method_label' => 'Manual Progress',
            'target_value' => 100,
            'target_unit' => '%',
            'target_tahunan' => '100 %',
            'capaian_periode_value' => $periodProgress,
            'capaian_periode' => $this->formatPercentage($periodProgress),
            'capaian_kumulatif_value' => $currentProgress,
            'capaian_kumulatif' => $this->formatPercentage($currentProgress),
            'selisih_value' => $remaining,
            'selisih' => $this->formatPercentage($remaining),
            'persentase_kumulatif' => $currentProgress,
            'persentase_kumulatif_label' => $this->formatPercentage($currentProgress),
            'status' => $this->statusLabel($currentProgress),
            'catatan' => $currentSnapshot['note'] ?: '-',
            'tindakan_perbaikan' => '-',
            'pegawai_pelaksana' => $currentSnapshot['updater_name'] ?: '-',
            'monitoring' => $this->manualMonitoringText($currentSnapshot, $cumulativeEnd),
        ]);
    }

    private function buildManualStatusRow(
        UnitTarget $target,
        Carbon $periodStart,
        Carbon $periodEnd,
        Carbon $cumulativeEnd
    ): array {
        $currentSnapshot = $this->latestManualSnapshot($target, $cumulativeEnd);
        $previousSnapshot = $this->latestManualSnapshot($target, $periodStart->copy()->subSecond());

        $currentStatus = $currentSnapshot['status'] ?? 'not_started';
        $previousStatus = $previousSnapshot['status'] ?? 'not_started';

        $currentProgress = $this->statusProgressValue($currentStatus);
        $previousProgress = $this->statusProgressValue($previousStatus);
        $periodProgress = max($currentProgress - $previousProgress, 0);
        $remaining = max(100 - $currentProgress, 0);

        $periodLabel = $previousStatus === $currentStatus
            ? $this->manualStatusLabel($currentStatus)
            : $this->manualStatusLabel($previousStatus) . ' → ' . $this->manualStatusLabel($currentStatus);

        return $this->baseRow($target, [
            'achievement_method' => 'manual_status',
            'achievement_method_label' => 'Manual Status',
            'target_value' => 100,
            'target_unit' => '%',
            'target_tahunan' => '100 %',
            'capaian_periode_value' => $periodProgress,
            'capaian_periode' => $periodLabel,
            'capaian_kumulatif_value' => $currentProgress,
            'capaian_kumulatif' => $this->manualStatusLabel($currentStatus) . ' (' . $this->formatPercentage($currentProgress) . ')',
            'selisih_value' => $remaining,
            'selisih' => $this->formatPercentage($remaining),
            'persentase_kumulatif' => $currentProgress,
            'persentase_kumulatif_label' => $this->formatPercentage($currentProgress),
            'status' => $this->manualStatusLabel($currentStatus),
            'catatan' => $currentSnapshot['note'] ?: '-',
            'tindakan_perbaikan' => '-',
            'pegawai_pelaksana' => $currentSnapshot['updater_name'] ?: '-',
            'monitoring' => $this->manualMonitoringText($currentSnapshot, $cumulativeEnd),
        ]);
    }

    private function baseRow(UnitTarget $target, array $data): array
    {
        return array_merge([
            'target_id' => $target->id,
            'unit_id' => $target->unit_id,
            'unit_name' => $target->unit?->name ?? '-',
            'sasaran_mutu' => $target->classification?->name ?? '-',
            'kegiatan' => $this->targetActivityText($target),
            'nama_target' => $target->target_name,
            'deskripsi_target' => $target->target_description,
            'objek_pekerjaan' => $target->object_summary,
            'bukti_dokumen' => $this->supportText($target),
            'supports_count' => $target->activeSupports?->count() ?? 0,
        ], $data);
    }

    private function matchingDailyReportsQueryBetween(UnitTarget $target, Carbon $startDate, Carbon $endDate): Builder
    {
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

    private function latestManualSnapshot(UnitTarget $target, Carbon $until): array
    {
        $update = UnitTargetProgressUpdate::query()
            ->with('updater')
            ->where('unit_target_id', $target->id)
            ->where('created_at', '<=', $until)
            ->latest('created_at')
            ->latest('id')
            ->first();

        if ($update) {
            return [
                'progress_value' => (int) $update->progress_value,
                'status' => $update->status ?: 'not_started',
                'note' => $update->note,
                'updated_at' => $update->created_at,
                'updater_name' => $update->updater?->name,
            ];
        }

        if ($target->manual_progress_updated_at && $target->manual_progress_updated_at->lte($until)) {
            return [
                'progress_value' => (int) $target->manual_progress,
                'status' => $target->manual_status ?: 'not_started',
                'note' => $target->manual_progress_note,
                'updated_at' => $target->manual_progress_updated_at,
                'updater_name' => $target->manualProgressUpdater?->name,
            ];
        }

        return [
            'progress_value' => 0,
            'status' => 'not_started',
            'note' => null,
            'updated_at' => null,
            'updater_name' => null,
        ];
    }

    private function targetActivityText(UnitTarget $target): string
    {
        $parts = [
            $target->target_name,
        ];

        if ($target->target_description) {
            $parts[] = $target->target_description;
        }

        return collect($parts)
            ->filter()
            ->implode("\n");
    }

    private function supportText(UnitTarget $target): string
    {
        $supports = $target->activeSupports ?? collect();

        if ($supports->isEmpty()) {
            return '-';
        }

        return $supports
            ->map(function ($support) {
                if ($support->support_type === 'link' && $support->url) {
                    return $support->title . ' (' . $support->url . ')';
                }

                if ($support->support_type === 'file' && $support->file_original_name) {
                    return $support->title . ' (' . $support->file_original_name . ')';
                }

                return $support->title;
            })
            ->filter()
            ->unique()
            ->implode("\n");
    }

    private function employeesFromReports(Collection $reports): string
    {
        $employees = $reports
            ->map(function ($report) {
                return $report->employee?->name
                    ?? $report->employee?->full_name
                    ?? null;
            })
            ->filter()
            ->unique()
            ->values();

        if ($employees->isEmpty()) {
            return '-';
        }

        return $employees->implode(', ');
    }

    private function percentage(int|float $value, int|float $target): float
    {
        if ($target <= 0) {
            return 0;
        }

        return round(min(($value / $target) * 100, 100), 2);
    }

    private function formatValue(int|float $value, ?string $unit): string
    {
        return number_format($value, 0, ',', '.') . ' ' . ($unit ?: '');
    }

    private function formatPercentage(int|float $value): string
    {
        return number_format($value, 0, ',', '.') . ' %';
    }

    private function statusLabel(int|float $percentage): string
    {
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

    private function statusProgressValue(?string $status): int
    {
        return match ($status) {
            'completed' => 100,
            'in_progress' => 50,
            default => 0,
        };
    }

    private function manualStatusLabel(?string $status): string
    {
        return match ($status) {
            'completed' => 'Selesai',
            'in_progress' => 'Berjalan',
            default => 'Belum Mulai',
        };
    }

    private function monitoringText(int|float $percentage, Carbon $date): string
    {
        return $this->statusLabel($percentage) . ' per ' . $date->format('d/m/Y');
    }

    private function manualMonitoringText(array $snapshot, Carbon $date): string
    {
        if (! $snapshot['updated_at']) {
            return 'Belum ada update progress sampai ' . $date->format('d/m/Y');
        }

        return 'Update terakhir ' . $snapshot['updated_at']->format('d/m/Y H:i');
    }
}