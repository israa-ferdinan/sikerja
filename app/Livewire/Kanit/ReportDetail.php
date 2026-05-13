<?php

namespace App\Livewire\Kanit;

use App\Models\DailyReport;
use Livewire\Component;

class ReportDetail extends Component
{
    public DailyReport $report;

    public function mount(DailyReport $report)
    {
        $user = auth()->user()->load('employee.unit');

        abort_if(!$user->employee, 403, 'User belum terhubung ke data pegawai.');
        abort_if(!$user->employee->unit_id, 403, 'Pegawai belum terhubung ke unit.');

        $unitId = $user->employee->unit_id;

        $report->load([
            'employee.unit',
            'duty',
            'server',
            'application',
            'photos',
        ]);

        abort_if(
            (int) $report->unit_id !== (int) $unitId,
            403,
            'Anda tidak memiliki akses ke laporan ini.'
        );

        $this->report = $report;
    }

    public function render()
    {
        return view('livewire.kanit.report-detail')
            ->layout('layouts.app');
    }
}