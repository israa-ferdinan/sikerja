<?php

namespace App\Livewire\Kanit;

use App\Models\Application;
use App\Models\DailyReport;
use App\Models\Duty;
use App\Models\Employee;
use App\Models\Server;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\KanitMonthlyReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportMonitoring extends Component
{
    use WithPagination;

    public $month;
    public $year;

    public $employeeId = '';
    public $dutyId = '';
    public $serverId = '';
    public $applicationId = '';
    public $search = '';

    public function mount()
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    public function updatedMonth()
    {
        $this->month = (int) $this->month;
        $this->resetPage();
    }

    public function updatedYear()
    {
        $this->year = (int) $this->year;
        $this->resetPage();
    }

    public function updatedEmployeeId()
    {
        $this->resetPage();
    }

    public function updatedDutyId()
    {
        $this->resetPage();
    }

    public function updatedServerId()
    {
        $this->serverId = $this->serverId ? (int) $this->serverId : '';
        $this->applicationId = '';

        $this->resetPage();
    }

    public function updatedApplicationId()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;

        $this->employeeId = '';
        $this->dutyId = '';
        $this->serverId = '';
        $this->applicationId = '';
        $this->search = '';

        $this->resetPage();
    }

    private function getKanitUnitId()
    {
        $user = auth()->user()->load('employee.unit');

        abort_if(!$user->employee, 403, 'User belum terhubung ke data pegawai.');
        abort_if(!$user->employee->unit_id, 403, 'Pegawai belum terhubung ke unit.');

        return $user->employee->unit_id;
    }

    public function render()
    {
        $unitId = $this->getKanitUnitId();

        //Rekap
        $totalEmployees = Employee::query()
            ->where('unit_id', $unitId)
            ->where('is_active', true)
            ->count();

        $totalReports = DailyReport::query()
            ->whereHas('employee', function ($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->whereMonth('report_date', (int) $this->month)
            ->whereYear('report_date', (int) $this->year)
            ->count();

        $submittedEmployeeIds = DailyReport::query()
            ->whereHas('employee', function ($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->whereMonth('report_date', (int) $this->month)
            ->whereYear('report_date', (int) $this->year)
            ->distinct()
            ->pluck('employee_id');

        $submittedEmployeesCount = $submittedEmployeeIds->count();

        $notSubmittedEmployeesCount = max($totalEmployees - $submittedEmployeesCount, 0);

        //Master Data
        $employees = Employee::query()
            ->where('unit_id', $unitId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $duties = Duty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $servers = Server::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $applications = Application::query()
            ->when($this->serverId, function ($query) {
                $query->where('server_id', $this->serverId);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $reports = DailyReport::query()
            ->with([
                'employee.unit',
                'duty',
                'server',
                'application',
                'photos',
            ])
            ->whereHas('employee', function ($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->whereMonth('report_date', (int) $this->month)
            ->whereYear('report_date', (int) $this->year)
            ->when($this->employeeId, function ($query) {
                $query->where('employee_id', $this->employeeId);
            })
            ->when($this->dutyId, function ($query) {
                $query->where('duty_id', $this->dutyId);
            })
            ->when($this->serverId, function ($query) {
                $query->where('server_id', $this->serverId);
            })
            ->when($this->applicationId, function ($query) {
                $query->where('application_id', $this->applicationId);
            })
            ->when($this->search, function ($query) {
                $keyword = '%' . trim($this->search) . '%';

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery
                        ->where('title', 'like', $keyword)
                        ->orWhere('description', 'like', $keyword)
                        ->orWhere('result', 'like', $keyword)
                        ->orWhereHas('employee', function ($employeeQuery) use ($keyword) {
                            $employeeQuery->where('name', 'like', $keyword);
                        })
                        ->orWhereHas('duty', function ($dutyQuery) use ($keyword) {
                            $dutyQuery->where('name', 'like', $keyword);
                        })
                        ->orWhereHas('server', function ($serverQuery) use ($keyword) {
                            $serverQuery->where('name', 'like', $keyword);
                        })
                        ->orWhereHas('application', function ($applicationQuery) use ($keyword) {
                            $applicationQuery->where('name', 'like', $keyword);
                        });
                });
            })
            ->latest('report_date')
            ->latest('id')
            ->paginate(10);

        return view('livewire.kanit.report-monitoring', [
            'reports' => $reports,
            'months' => $this->getMonths(),
            'years' => $this->getYears(),
            'employees' => $employees,
            'duties' => $duties,
            'servers' => $servers,
            'applications' => $applications,
            'recap' => [
            'total_reports' => $totalReports,
            'total_employees' => $totalEmployees,
            'submitted_employees' => $submittedEmployeesCount,
            'not_submitted_employees' => $notSubmittedEmployeesCount,
        ],
        ])->layout('layouts.app');
    }

    private function getMonths()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function getYears()
    {
        $currentYear = now()->year;

        return range($currentYear - 3, $currentYear + 1);
    }

    public function exportMonthly()
    {
        $unitId = $this->getKanitUnitId();

        $month = (int) $this->month;
        $year = (int) $this->year;

        $fileName = 'rekap-laporan-unit-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';

        return Excel::download(
            new KanitMonthlyReportsExport($unitId, $month, $year),
            $fileName
        );
    }
}