<?php

namespace App\Livewire\Admin\TargetReport;

use App\Exports\QuarterlyTargetReportExport;
use App\Models\Employee;
use App\Models\Unit;
use App\Services\QuarterlyTargetReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    public int $year;
    public int $quarter = 1;
    public ?int $unit_id = null;

    public bool $isAdmin = false;
    public bool $isKanit = false;
    public bool $isGkm = false;
    public bool $isUnitManager = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->isAdmin = (bool) $user?->isAdmin();
        $this->isKanit = (bool) $user?->isKanit();
        $this->isGkm = (bool) $user?->isGkm();
        $this->isUnitManager = $this->isKanit || $this->isGkm;

        abort_unless(
            $this->isAdmin || $this->isUnitManager,
            403
        );

        $this->year = now()->year;
        $this->quarter = $this->resolveDefaultQuarter();

        if ($this->isUnitManager) {
            $this->unit_id = $user?->employee?->unit_id;

            abort_if(
                ! $this->unit_id,
                403,
                'Akun Anda belum terhubung dengan unit pegawai.'
            );
        }
    }

    public function updatedYear(): void
    {
        $this->year = max(2020, min((int) $this->year, 2100));
    }

    public function updatedQuarter(): void
    {
        if (! in_array((int) $this->quarter, [1, 2, 3, 4], true)) {
            $this->quarter = $this->resolveDefaultQuarter();
        }
    }

    public function updatedUnitId(): void
    {
        if (! $this->isAdmin) {
            $this->unit_id = Auth::user()?->employee?->unit_id;

            return;
        }

        if ($this->unit_id === '') {
            $this->unit_id = null;
        }
    }

    public function resetFilters(): void
    {
        $this->year = now()->year;
        $this->quarter = $this->resolveDefaultQuarter();

        if ($this->isAdmin) {
            $this->unit_id = null;
        }

        if ($this->isUnitManager) {
            $this->unit_id = Auth::user()?->employee?->unit_id;
        }
    }

    public function getReportRowsProperty(): Collection
    {
        return app(QuarterlyTargetReportService::class)
            ->buildRows(
                $this->resolvedUnitId(),
                $this->year,
                $this->quarter
            );
    }

    public function getUnitsProperty(): Collection
    {
        if (! $this->isAdmin) {
            return collect();
        }

        return Unit::query()
            ->orderBy('name')
            ->get();
    }

    public function getQuarterLabelProperty(): string
    {
        return app(QuarterlyTargetReportService::class)
            ->quarterLabel($this->quarter);
    }

    public function getPeriodLabelProperty(): string
    {
        return app(QuarterlyTargetReportService::class)
            ->quarterPeriodLabel($this->year, $this->quarter);
    }

    public function getSelectedUnitNameProperty(): string
    {
        if (! $this->unit_id) {
            return $this->isAdmin ? 'Semua Unit' : '-';
        }

        return Unit::query()
            ->find($this->unit_id)?->name ?? '-';
    }

    public function downloadExcel()
    {
        abort_unless(
            $this->isAdmin || $this->isUnitManager,
            403
        );

        $unitId = $this->resolvedUnitId();
        $unitName = $this->selectedUnitName;
        $kanit = $this->resolveKanit($unitId);

        $filename = 'laporan-capaian-target-'
            . $this->year
            . '-tw' . $this->quarter
            . '-' . str($unitName)->slug('-')
            . '.xlsx';

        return Excel::download(
            new QuarterlyTargetReportExport(
                unitId: $unitId,
                year: $this->year,
                quarter: $this->quarter,
                unitName: $unitName,
                kanitName: $kanit?->name,
                kanitNip: $kanit?->nip,
            ),
            $filename
        );
    }

    private function resolveKanit(?int $unitId): ?Employee
    {
        if (! $unitId) {
            return null;
        }

        return Employee::query()
            ->where('unit_id', $unitId)
            ->whereHas('user.role', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['kanit']);
            })
            ->first();
    }

    public function render()
    {
        abort_unless(
            $this->isAdmin || $this->isUnitManager,
            403
        );

        return view('livewire.admin.target-report.index', [
            'rows' => $this->reportRows,
            'units' => $this->units,
            'quarterLabel' => $this->quarterLabel,
            'periodLabel' => $this->periodLabel,
            'selectedUnitName' => $this->selectedUnitName,
        ]);
    }

    private function resolvedUnitId(): ?int
    {
        if ($this->isUnitManager) {
            return Auth::user()?->employee?->unit_id;
        }

        return $this->unit_id;
    }

    private function resolveDefaultQuarter(): int
    {
        return (int) ceil(now()->month / 3);
    }
}