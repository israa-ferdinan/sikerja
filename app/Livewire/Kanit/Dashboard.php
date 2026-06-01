<?php

namespace App\Livewire\Kanit;

use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\UnitTarget;
use App\Services\UnitTargetAchievementService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public int $todayReports = 0;

    public int $monthlyReports = 0;

    public int $normalReports = 0;

    public int $delegatedReports = 0;

    public int $unitEmployees = 0;

    public int $employeesReportedThisMonth = 0;

    public int $employeesNotReportedThisMonth = 0;

    public ?string $unitName = null;

    public int $activeTargets = 0;

    public int $achievedTargets = 0;

    public int $runningTargets = 0;

    public float $averageTargetAchievement = 0;

    public array $targetAttentionItems = [];

    public function mount(): void
    {
        $user = Auth::user()->loadMissing('employee.unit');

        $unitId = $user->employee?->unit_id;

        $this->unitName = $user->employee?->unit?->name;

        if (! $unitId) {
            return;
        }

        $today = today()->toDateString();

        $startOfMonth = now()->startOfMonth()->toDateString();

        $endOfMonth = now()->endOfMonth()->toDateString();

        $this->todayReports = DailyReport::query()
            ->where('unit_id', $unitId)
            ->whereDate('report_date', $today)
            ->count();

        $this->monthlyReports = DailyReport::query()
            ->where('unit_id', $unitId)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->count();

        $this->normalReports = DailyReport::query()
            ->where('unit_id', $unitId)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->where('is_delegated', false)
            ->count();

        $this->delegatedReports = DailyReport::query()
            ->where('unit_id', $unitId)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->where('is_delegated', true)
            ->count();

        $this->unitEmployees = Employee::query()
            ->where('unit_id', $unitId)
            ->count();

        $reportedEmployeeIds = DailyReport::query()
            ->where('unit_id', $unitId)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->get(['employee_id', 'reported_by_employee_id'])
            ->map(fn ($report) => $report->reported_by_employee_id ?: $report->employee_id)
            ->filter()
            ->unique();

        $this->employeesReportedThisMonth = $reportedEmployeeIds->count();

        $this->employeesNotReportedThisMonth = max(
            $this->unitEmployees - $this->employeesReportedThisMonth,
            0
        );

        $this->loadTargetProgress($unitId);
    }

    private function loadTargetProgress(int $unitId): void
    {
        $achievementService = app(UnitTargetAchievementService::class);

        $targets = UnitTarget::query()
            ->with([
                'classification',
                'server',
                'application',
            ])
            ->where('unit_id', $unitId)
            ->where('target_year', now()->year)
            ->where('is_active', true)
            ->get();

        $this->activeTargets = $targets->count();

        if ($targets->isEmpty()) {
            $this->achievedTargets = 0;
            $this->runningTargets = 0;
            $this->averageTargetAchievement = 0;
            $this->targetAttentionItems = [];

            return;
        }

        $targetSummaries = $targets
            ->map(function (UnitTarget $target) use ($achievementService) {
                $summary = $achievementService->summary($target);

                return [
                    'id' => $target->id,
                    'title' => $this->targetDisplayTitle($target),
                    'period_label' => $summary['period_label'],
                    'target_quantity' => $summary['target_quantity'],
                    'achievement_count' => $summary['achievement_count'],
                    'remaining_target' => $summary['remaining_target'],
                    'achievement_percentage' => $summary['achievement_percentage'],
                    'status_label' => $summary['status_label'],
                    'status_badge_class' => $summary['status_badge_class'],
                ];
            })
            ->values();

        $this->achievedTargets = $targetSummaries
            ->where('achievement_percentage', '>=', 100)
            ->count();

        $this->runningTargets = $this->activeTargets - $this->achievedTargets;

        $this->averageTargetAchievement = round(
            $targetSummaries->avg('achievement_percentage') ?? 0,
            2
        );

        $this->targetAttentionItems = $targetSummaries
            ->filter(fn ($target) => $target['achievement_percentage'] < 100)
            ->sortBy('achievement_percentage')
            ->take(5)
            ->values()
            ->toArray();
    }

    private function targetDisplayTitle($target): string
    {
        if (! empty($target->target_name)) {
            return $target->target_name;
        }

        if (! empty($target->name)) {
            return $target->name;
        }

        if (! empty($target->title)) {
            return $target->title;
        }

        if ($target->classification?->name) {
            return $target->classification->name;
        }

        if ($target->application?->name) {
            return 'Target Aplikasi: ' . $target->application->name;
        }

        if ($target->server?->name) {
            return 'Target Server: ' . $target->server->name;
        }

        return 'Target Unit #' . $target->id;
    }

    public function render()
    {
        return view('livewire.kanit.dashboard');
    }
}