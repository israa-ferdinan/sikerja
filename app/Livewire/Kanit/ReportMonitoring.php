<?php

namespace App\Livewire\Kanit;

use App\Models\DailyReport;
use Livewire\Component;
use Livewire\WithPagination;

class ReportMonitoring extends Component
{
    use WithPagination;

    public $month;
    public $year;

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

    public function resetFilter()
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;

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
            ->latest('report_date')
            ->latest('id')
            ->paginate(10);

        return view('livewire.kanit.report-monitoring', [
            'reports' => $reports,
            'months' => $this->getMonths(),
            'years' => $this->getYears(),
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
}