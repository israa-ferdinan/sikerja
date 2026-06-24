<?php

namespace App\Services;

use App\Models\MonthlyReportApproval;
use App\Models\User;
use Carbon\CarbonInterface;

class MonthlyReportApprovalService
{
    public function isApproved(int $unitId, int $month, int $year): bool
    {
        return MonthlyReportApproval::query()
            ->where('unit_id', $unitId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'approved')
            ->exists();
    }

    public function isReportDateLocked(int $unitId, CarbonInterface|string $reportDate): bool
    {
        $date = is_string($reportDate)
            ? \Carbon\Carbon::parse($reportDate)
            : $reportDate;

        return $this->isApproved(
            unitId: $unitId,
            month: (int) $date->month,
            year: (int) $date->year
        );
    }

    public function getApproval(?int $unitId, int $month, int $year): ?MonthlyReportApproval
    {
        if (! $unitId) {
            return null;
        }

        return MonthlyReportApproval::query()
            ->with(['unit', 'approvedByUser', 'approvedByEmployee', 'cancelledByUser'])
            ->where('unit_id', $unitId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
    }

    public function getActiveApproval(?int $unitId, int $month, int $year): ?MonthlyReportApproval
    {
        if (! $unitId) {
            return null;
        }

        return MonthlyReportApproval::query()
            ->with(['unit', 'approvedByUser', 'approvedByEmployee'])
            ->where('unit_id', $unitId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'approved')
            ->first();
    }

    public function canUserApprove(User $user, int $unitId): bool
    {
        return $user->role?->name === 'kanit'
            && $user->employee
            && (int) $user->employee->unit_id === (int) $unitId;
    }
}