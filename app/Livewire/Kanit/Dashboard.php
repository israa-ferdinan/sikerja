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

        $this->unitEmployees = Employee::query()
            ->where('unit_id', $unitId)
            ->count();

        $this->employeesReportedThisMonth = DailyReport::query()
            ->where('unit_id', $unitId)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('employee_id')
            ->distinct('employee_id')
            ->count('employee_id');

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