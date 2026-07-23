<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use App\Services\ActivityLogger;
use App\Services\MonthlyReportApprovalService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class MyDailyReports extends Component
{
    use WithPagination;

    public $month;
    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->month = now()->format('Y-m');
    }

    public function updatedMonth()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->month = now()->format('Y-m');
        $this->search = '';

        $this->resetPage();
    }

    public function delete($id)
    {
        $user = auth()->user();

        if (! $user->employee_id) {
            abort(403, 'Akun belum terhubung dengan data pegawai.');
        }

        $report = DailyReport::query()
            ->with([
                'photos',
                'duty',
                'server',
                'application',
                'delegation',
                'dutyOwnerEmployee',
                'reportedByEmployee',
                'operationalTicket',
            ])
            ->where('employee_id', $user->employee_id)
            ->findOrFail($id);

        if (app(MonthlyReportApprovalService::class)->isReportDateLocked(
            unitId: (int) $report->unit_id,
            reportDate: $report->report_date
        )) {
            session()->flash('error', 'Laporan periode ini sudah difinalisasi oleh Kanit dan tidak dapat dihapus.');
            return;
        }

                if ($report->operational_ticket_id) {
            session()->flash(
                'error',
                'Laporan yang berasal dari Tiket Operasional tidak dapat dihapus.'
            );

            return;
        }

        $oldValues = $report->toArray();

        $oldValues['photo_count'] = $report->photos?->count() ?? 0;

        $oldValues['photo_paths'] = $report->photos
            ? $report->photos->pluck('file_path')->toArray()
            : [];

        ActivityLogger::log(
            module: 'daily_report',
            action: 'delete',
            description: $report->is_delegated
                ? 'Menghapus laporan kerja harian delegasi'
                : 'Menghapus laporan kerja harian',
            subject: $report,
            oldValues: $oldValues
        );

        foreach ($report->photos as $photo) {
            if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }

            $photo->delete();
        }

        $report->delete();

        session()->flash('success', 'Laporan berhasil dihapus.');
    }

    public function render()
    {
        $user = auth()->user();

        if (! $user->employee_id) {
            return view('livewire.reports.my-daily-reports', [
                'reports' => DailyReport::query()->whereRaw('1 = 0')->paginate(10),
                'missingEmployee' => true,
            ])->layout('layouts.app');
        }

        try {
            $selectedMonth = Carbon::createFromFormat('Y-m', $this->month);
        } catch (\Throwable $e) {
            $selectedMonth = now();
            $this->month = now()->format('Y-m');
        }

        $reports = DailyReport::query()
            ->with([
                'duty.classification',
                'server',
                'application',
                'photos',
                'delegation',
                'dutyOwnerEmployee',
                'reportedByEmployee',
                'operationalTicket',
            ])
            ->where('employee_id', $user->employee_id)
            ->whereMonth('report_date', $selectedMonth->format('m'))
            ->whereYear('report_date', $selectedMonth->format('Y'))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%')
                        ->orWhereHas('duty', function ($dutyQuery) {
                            $dutyQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('object_type', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('duty.classification', function ($classificationQuery) {
                            $classificationQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('server', function ($serverQuery) {
                            $serverQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('application', function ($applicationQuery) {
                            $applicationQuery->where(
                                'name',
                                'like',
                                '%' . $this->search . '%'
                            );
                        })
                        ->orWhereHas('operationalTicket', function ($ticketQuery) {
                            $ticketQuery
                                ->where(
                                    'ticket_code',
                                    'like',
                                    '%' . $this->search . '%'
                                )
                                ->orWhere(
                                    'title',
                                    'like',
                                    '%' . $this->search . '%'
                                );
                        });
                });
            })
            ->latest('report_date')
            ->latest('id')
            ->paginate(10);

        return view('livewire.reports.my-daily-reports', [
            'reports' => $reports,
        ])->layout('layouts.app');
    }
}