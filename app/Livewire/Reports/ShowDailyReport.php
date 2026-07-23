<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;

use App\Services\ActivityLogger;
use App\Services\MonthlyReportApprovalService;

use Illuminate\Support\Facades\Storage;

use Livewire\Component;

class ShowDailyReport extends Component
{
    public DailyReport $report;
    public bool $isLocked = false;

    public function mount(DailyReport $report)
    {
        $user = auth()->user();

        if (! $user->employee_id || $report->employee_id !== $user->employee_id) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $this->report = $report->load([
            'duty.classification',
            'server',
            'application',
            'photos',
            'employee',
            'unit',
            'delegation',
            'dutyOwnerEmployee',
            'reportedByEmployee',
            'operationalTicket',
        ]);

        $this->isLocked = app(MonthlyReportApprovalService::class)->isReportDateLocked(
            unitId: (int) $this->report->unit_id,
            reportDate: $this->report->report_date
        );
    }

    public function delete()
    {
        $user = auth()->user();

        if (! $user->employee_id || $this->report->employee_id !== $user->employee_id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        if (app(MonthlyReportApprovalService::class)->isReportDateLocked(
            unitId: (int) $this->report->unit_id,
            reportDate: $this->report->report_date
        )) {
            session()->flash('error', 'Laporan periode ini sudah difinalisasi oleh Kanit dan tidak dapat dihapus.');
            return;
        }

        if ($this->report->operational_ticket_id) {
            session()->flash(
                'error',
                'Laporan yang berasal dari Tiket Operasional tidak dapat dihapus.'
            );

            return;
        }

        $this->report->load([
            'photos',
            'duty.classification',
            'server',
            'application',
            'delegation',
            'dutyOwnerEmployee',
            'reportedByEmployee',
            'operationalTicket'
        ]);

        $oldValues = $this->report->toArray();

        $oldValues['photo_count'] = $this->report->photos?->count() ?? 0;

        $oldValues['photo_paths'] = $this->report->photos
            ? $this->report->photos->pluck('file_path')->toArray()
            : [];

        ActivityLogger::log(
            module: 'daily_report',
            action: 'delete',
            description: $this->report->is_delegated
                ? 'Menghapus laporan kerja harian delegasi'
                : 'Menghapus laporan kerja harian',
            subject: $this->report,
            oldValues: $oldValues
        );

        foreach ($this->report->photos as $photo) {
            if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }

            $photo->delete();
        }

        $this->report->delete();

        session()->flash('success', 'Laporan berhasil dihapus.');

        return redirect()->route('pegawai.reports.index');
    }

    public function render()
    {
        return view('livewire.reports.show-daily-report')
            ->layout('layouts.app');
    }
}