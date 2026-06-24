<?php

namespace App\Livewire\Reports;

use App\Exports\Reports\MonthlyReportSingleSheetExport;
use App\Models\DailyReport;
use App\Models\MonthlyReportApproval;
use App\Models\Unit;
use App\Services\ActivityLogger;
use App\Services\MonthlyReportApprovalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportMonthlyReport extends Component
{
    public int $month;
    public int $year;
    public ?int $unit_id = null;

    public string $cancel_reason = '';

    public $units = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $user = Auth::user();

        if (! in_array($user->role?->name, ['admin', 'kanit'], true)) {
            abort(403);
        }

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

        if (! in_array($user->role?->name, ['admin', 'kanit'], true)) {
            abort(403);
        }

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

        ActivityLogger::log(
            module: 'monthly_export',
            action: 'export',
            description: 'Export laporan bulanan periode '
                . str_pad($this->month, 2, '0', STR_PAD_LEFT)
                . '/'
                . $this->year,
            newValues: [
                'month' => $this->month,
                'year' => $this->year,
                'unit_id' => $this->unit_id,
                'unit_name' => $unitName,
                'file_name' => $fileName,
                'total_reports' => $this->summary['total_reports'] ?? 0,
                'total_photos' => $this->summary['total_photos'] ?? 0,
                'total_employees' => $this->summary['total_employees'] ?? 0,
                'approval_status' => $this->approvalStatus['label'] ?? '-',
                'exported_by' => [
                    'user_id' => $user?->id,
                    'name' => $user?->name,
                    'email' => $user?->email,
                    'role' => $user?->role?->name,
                ],
            ]
        );

        return Excel::download(
            new MonthlyReportSingleSheetExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unit_id,
                printedBy: $user->name ?? $user->email ?? '-'
            ),
            $fileName
        );
    }

    public function approveMonthlyReport(MonthlyReportApprovalService $approvalService): void
    {
        $this->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'unit_id' => ['required', 'exists:units,id'],
        ]);

        $user = Auth::user();

        $this->enforceUnitAccess();

        if (! $this->unit_id) {
            session()->flash('error', 'Unit approval tidak valid.');
            return;
        }

        if (! $approvalService->canUserApprove($user, $this->unit_id)) {
            abort(403);
        }

        $employee = $user->employee?->loadMissing(['unit', 'jobPosition']);

        if (! $employee) {
            session()->flash('error', 'Akun Kanit belum terhubung dengan data pegawai.');
            return;
        }

        if (! $employee->signature_path) {
            session()->flash('error', 'Silakan upload tanda tangan di halaman Profil Saya sebelum finalisasi laporan bulanan.');
            return;
        }

        if ($this->summary['total_reports'] === 0) {
            session()->flash('error', 'Tidak ada data laporan untuk difinalisasi.');
            return;
        }

        $approval = MonthlyReportApproval::query()
            ->where('unit_id', $this->unit_id)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->first();

        if ($approval?->status === 'approved') {
            session()->flash('warning', 'Laporan bulanan periode ini sudah difinalisasi.');
            return;
        }

        MonthlyReportApproval::query()->updateOrCreate(
            [
                'unit_id' => $this->unit_id,
                'month' => $this->month,
                'year' => $this->year,
            ],
            [
                'status' => 'approved',
                'approved_by_user_id' => $user->id,
                'approved_by_employee_id' => $employee->id,
                'approved_at' => now(),
                'approver_name' => $employee->name,
                'approver_nip' => $employee->nip,
                'approver_position' => $employee->jobPosition?->name
                    ?? $employee->position
                    ?? 'Kepala Unit',
                'approver_unit_name' => $employee->unit?->name,
                'approver_signature_path' => $employee->signature_path,
                'cancelled_by_user_id' => null,
                'cancelled_at' => null,
                'cancel_reason' => null,
            ]
        );

        ActivityLogger::log(
            module: 'monthly_report_approval',
            action: 'approve',
            description: 'Finalisasi laporan bulanan periode '
                . str_pad($this->month, 2, '0', STR_PAD_LEFT)
                . '/'
                . $this->year,
            newValues: [
                'unit_id' => $this->unit_id,
                'unit_name' => $employee->unit?->name,
                'month' => $this->month,
                'year' => $this->year,
                'approved_by_user_id' => $user->id,
                'approved_by_employee_id' => $employee->id,
                'approver_name' => $employee->name,
                'total_reports' => $this->summary['total_reports'] ?? 0,
            ]
        );

        session()->flash('success', 'Laporan bulanan berhasil difinalisasi. Periode ini sekarang terkunci.');
    }

    public function cancelMonthlyApproval(): void
    {
        $this->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'unit_id' => ['required', 'exists:units,id'],
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancel_reason.required' => 'Alasan batal finalisasi wajib diisi.',
            'cancel_reason.min' => 'Alasan batal finalisasi minimal 5 karakter.',
            'cancel_reason.max' => 'Alasan batal finalisasi maksimal 500 karakter.',
        ]);

        $user = Auth::user();

        $this->enforceUnitAccess();

        if (! $this->unit_id) {
            session()->flash('error', 'Unit approval tidak valid.');
            return;
        }

        if ($user->role?->name !== 'kanit') {
            abort(403);
        }

        if ((int) $user->employee?->unit_id !== (int) $this->unit_id) {
            abort(403);
        }

        $approval = MonthlyReportApproval::query()
            ->where('unit_id', $this->unit_id)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->first();

        if (! $approval || $approval->status !== 'approved') {
            session()->flash('warning', 'Laporan bulanan periode ini belum dalam status final.');
            return;
        }

        $approval->forceFill([
            'status' => 'cancelled',
            'cancelled_by_user_id' => $user->id,
            'cancelled_at' => now(),
            'cancel_reason' => $this->cancel_reason,
        ])->save();

        ActivityLogger::log(
            module: 'monthly_report_approval',
            action: 'cancel',
            description: 'Pembatalan finalisasi laporan bulanan periode '
                . str_pad($this->month, 2, '0', STR_PAD_LEFT)
                . '/'
                . $this->year,
            newValues: [
                'unit_id' => $this->unit_id,
                'month' => $this->month,
                'year' => $this->year,
                'cancelled_by_user_id' => $user->id,
                'cancel_reason' => $this->cancel_reason,
            ]
        );

        $this->reset('cancel_reason');

        session()->flash('success', 'Finalisasi laporan bulanan berhasil dibatalkan. Periode ini bisa diedit kembali.');
    }

    public function updated($property): void
    {
        $this->enforceUnitAccess();

        if (in_array($property, ['month', 'year', 'unit_id'], true)) {
            $this->resetValidation();
            $this->cancel_reason = '';
        }
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

    public function getApprovalProperty(): ?MonthlyReportApproval
    {
        if (! $this->unit_id) {
            return null;
        }

        return MonthlyReportApproval::query()
            ->with(['unit', 'approvedByEmployee', 'cancelledByUser'])
            ->where('unit_id', $this->unit_id)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->first();
    }

    public function getApprovalStatusProperty(): array
    {
        $approval = $this->approval;

        if (! $approval) {
            return [
                'status' => 'none',
                'label' => 'Belum Difinalisasi',
                'class' => 'bg-yellow-50 text-yellow-700 ring-yellow-100',
                'icon' => 'clock',
                'description' => 'Laporan bulan ini belum dikunci dan belum memiliki tanda tangan final.',
            ];
        }

        if ($approval->status === 'approved') {
            return [
                'status' => 'approved',
                'label' => 'Sudah Difinalisasi',
                'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                'icon' => 'badge-check',
                'description' => 'Laporan bulan ini sudah terkunci dan export akan menampilkan tanda tangan Kanit.',
            ];
        }

        return [
            'status' => 'cancelled',
            'label' => 'Finalisasi Dibatalkan',
            'class' => 'bg-rose-50 text-rose-700 ring-rose-100',
            'icon' => 'x-circle',
            'description' => 'Finalisasi pernah dibatalkan. Laporan bulan ini dapat diedit kembali sampai difinalisasi ulang.',
        ];
    }

    public function getCanApproveProperty(): bool
    {
        $user = Auth::user();

        return $user->role?->name === 'kanit'
            && $this->unit_id
            && (int) $user->employee?->unit_id === (int) $this->unit_id
            && $this->approvalStatus['status'] !== 'approved'
            && $this->summary['total_reports'] > 0;
    }

    public function getCanCancelApprovalProperty(): bool
    {
        $user = Auth::user();

        return $user->role?->name === 'kanit'
            && $this->unit_id
            && (int) $user->employee?->unit_id === (int) $this->unit_id
            && $this->approvalStatus['status'] === 'approved';
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