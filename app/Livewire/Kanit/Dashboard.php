<?php

namespace App\Livewire\Kanit;

use App\Models\DailyReport;
use App\Models\Employee;
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
    }

    public function render()
    {
        return view('livewire.kanit.dashboard');
    }
}