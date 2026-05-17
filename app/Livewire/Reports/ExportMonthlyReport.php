<?php

namespace App\Livewire\Reports;

use App\Models\Unit;
use App\Models\DailyReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Exports\Reports\MonthlyReportWorkbookExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ExportMonthlyReport extends Component
{
    public int $month;
    public int $year;
    public ?int $unit_id = null;

    public $units = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $user = Auth::user();

        if ($user->role?->name === 'admin') {
            $this->units = Unit::orderBy('name')->get();
        }

        if ($user->role?->name === 'kanit') {
            $this->unit_id = $user->employee?->unit_id;
            $this->units = Unit::where('id', $this->unit_id)->get();
        }
    }

    public function export()
    {
        $this->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'unit_id' => ['nullable', 'exists:units,id'],
        ]);

        $user = Auth::user();

        $this->enforceUnitAccess();

        $unitName = 'semua-unit';

        if ($user->role?->name === 'kanit' && ! $this->unit_id) {
            session()->flash('error', 'Akun Kanit belum terhubung dengan unit pegawai. Silakan hubungi admin.');
            return null;
        }

        if ($this->summary['total_reports'] === 0) {
            session()->flash('error', 'Tidak ada data laporan untuk filter yang dipilih.');
            return null;
        }        

        if ($this->unit_id) {
            $unitName = Unit::where('id', $this->unit_id)->value('name') ?? 'unit';
        }

        $fileName = 'rekap-laporan-'
            . Str::slug($unitName)
            . '-'
            . str_pad($this->month, 2, '0', STR_PAD_LEFT)
            . '-'
            . $this->year
            . '.xlsx';

       return Excel::download(
            new MonthlyReportWorkbookExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unit_id,
                printedBy: $user->name ?? $user->email ?? '-'
            ),
            $fileName
        );
    }

    public function updated($property): void
    {
        $this->enforceUnitAccess();
    }

    public function getSummaryProperty()
    {
        $query = DailyReport::query()
            ->with([
                'employee',
                'employee.position',
                'unit',
            ])
            ->withCount('photos')
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year);

        if ($this->unit_id) {
            $query->where('unit_id', $this->unit_id);
        }

        $reports = $query->get();

        $employeeSummaries = $reports
            ->groupBy('employee_id')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'employee_name' => $first?->employee?->name ?? '-',
                    'position_name' => $first?->employee?->position?->name ?? '-',
                    'unit_name' => $first?->unit?->name ?? '-',
                    'total_reports' => $items->count(),
                    'total_photos' => $items->sum('photos_count'),
                ];
            })
            ->sortByDesc('total_reports')
            ->values();

        return [
            'total_reports' => $reports->count(),
            'total_photos' => $reports->sum('photos_count'),
            'total_employees' => $employeeSummaries->count(),
            'employees' => $employeeSummaries,
        ];
    }

    private function enforceUnitAccess(): void
    {
        $user = Auth::user();

        if ($user->role?->name === 'kanit') {
            $this->unit_id = $user->employee?->unit_id;
        }
    }

    public function render()
    {
        return view('livewire.reports.export-monthly-report');
    }
}