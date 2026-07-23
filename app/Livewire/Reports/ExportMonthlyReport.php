<?php

namespace App\Livewire\Reports;

use App\Exports\Reports\MonthlyReportSingleSheetExport;
use App\Models\DailyReport;
use App\Models\MonthlyReportApproval;
use App\Models\Unit;
use App\Models\Employee;
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

    public string $export_mode = 'unit';

    public ?int $employee_id = null;

    public string $cancel_reason = '';

    public $units = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $user = Auth::user();

        if (! in_array($user->role?->name, ['admin', 'kanit', 'gkm'], true)) {
            abort(403);
        }

        if ($user->role?->name === 'admin') {
            $this->units = Unit::orderBy('name')->get();
        }

        if (in_array($user->role?->name, ['kanit', 'gkm'], true)) {
            $this->unit_id = $user->employee?->unit_id;

            if (! $this->unit_id) {
                abort(403, 'Akun Anda belum terhubung dengan unit pegawai.');
            }

            $this->units = Unit::whereKey($this->unit_id)->get();
        }
    }

    public function export()
    {
        $this->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'export_mode' => ['required', 'in:unit,employee'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ], [
            'export_mode.required' => 'Jenis export wajib dipilih.',
            'export_mode.in' => 'Jenis export tidak valid.',
            'employee_id.exists' => 'Pegawai yang dipilih tidak valid.',
        ]);

        $user = Auth::user();

        if (! in_array($user->role?->name, ['admin', 'kanit', 'gkm'], true)) {
            abort(403);
        }

        $this->enforceUnitAccess();

        $unitName = 'semua-unit';

        if (
            in_array($user->role?->name, ['kanit', 'gkm'], true)
            && ! $this->unit_id
        ) {
            session()->flash(
                'error',
                'Akun Anda belum terhubung dengan unit pegawai. Silakan hubungi admin.'
            );

            return null;
        }

        if ($this->summary['total_reports'] === 0) {
            session()->flash('error', 'Tidak ada data laporan untuk filter yang dipilih.');
            return null;
        }

        if ($this->export_mode === 'employee') {
        if (! $this->unit_id) {
            session()->flash('error', 'Export per pegawai wajib memilih unit terlebih dahulu.');
            return null;
        }

        if (! $this->employee_id) {
            session()->flash('error', 'Silakan pilih pegawai yang akan diexport.');
            return null;
        }

        $employeeBelongsToUnit = Employee::query()
            ->where('id', $this->employee_id)
            ->where('unit_id', $this->unit_id)
            ->exists();

        if (! $employeeBelongsToUnit) {
            session()->flash('error', 'Pegawai yang dipilih tidak sesuai dengan unit aktif.');
            return null;
        }

        $employeeHasReports = DailyReport::query()
            ->where('employee_id', $this->employee_id)
            ->where('unit_id', $this->unit_id)
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->exists();

        if (! $employeeHasReports) {
            session()->flash('error', 'Pegawai yang dipilih belum memiliki laporan pada periode ini.');
            return null;
        }
    }

        if ($this->unit_id) {
            $unitName = Unit::where('id', $this->unit_id)->value('name') ?? 'unit';
        }

        $employeeName = null;

        if ($this->export_mode === 'employee' && $this->employee_id) {
            $employeeName = Employee::where('id', $this->employee_id)->value('name') ?? 'pegawai';
        }

        $fileNamePrefix = $this->export_mode === 'employee'
            ? 'laporan-pegawai-' . Str::slug($employeeName ?? 'pegawai')
            : 'rekap-laporan-' . Str::slug($unitName);

        $fileName = $fileNamePrefix
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
                'export_mode' => $this->export_mode,
                'employee_id' => $this->employee_id,
                'employee_name' => $employeeName,
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
                printedBy: $user->name ?? $user->email ?? '-',
                employeeId: $this->export_mode === 'employee' ? $this->employee_id : null,
                exportMode: $this->export_mode
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

        $employeesWithoutSignatureCount = $this->summary['total_employees_without_signature'] ?? 0;

        if ($employeesWithoutSignatureCount > 0) {
            session()->flash(
                'warning',
                'Catatan: ada '
                    . $employeesWithoutSignatureCount
                    . ' pegawai pelapor yang belum upload tanda tangan. Finalisasi tetap dilanjutkan karena tanda tangan pegawai belum menjadi syarat blocking.'
            );
        }

        if (($this->summary['total_employees_without_signature'] ?? 0) > 0) {
            session()->flash(
                'warning',
                'Catatan: ada '
                    . $this->summary['total_employees_without_signature']
                    . ' pegawai pelapor yang belum upload tanda tangan. Finalisasi tetap dilanjutkan karena tanda tangan pegawai belum dibuat blocking.'
            );
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

        $employeesWithoutSignatureCount = $this->summary['total_employees_without_signature'] ?? 0;

        $successMessage = 'Laporan bulanan berhasil difinalisasi.';

        if ($employeesWithoutSignatureCount > 0) {
            $successMessage .= ' Catatan: ada '
                . $employeesWithoutSignatureCount
                . ' pegawai pelapor yang belum upload tanda tangan.';
        }

        session()->flash('success', $successMessage);
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

        if (in_array($property, ['month', 'year', 'unit_id', 'export_mode', 'employee_id'], true)) {
            $this->resetValidation();
            $this->cancel_reason = '';
        }

        if (in_array($property, ['month', 'year', 'unit_id'], true)) {
            $this->employee_id = null;
        }

        if ($property === 'export_mode' && $this->export_mode === 'unit') {
            $this->employee_id = null;
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
                $employee = $first?->employee;

                return [
                        'employee_id' => $employee?->id,
                        'employee_name' => $employee?->name ?? '-',
                        'position_name' => $employee?->jobPosition?->name
                            ?? $employee?->position
                            ?? '-',
                        'unit_name' => $first?->unit?->name ?? '-',
                        'total_reports' => $items->count(),
                        'total_photos' => $items->sum('photos_count'),
                        'has_signature' => filled($employee?->signature_path),
                    ];
                })
                ->sortByDesc('total_reports')
                ->values();

        $employeesWithoutSignature = $employeeSummaries
            ->filter(fn ($employee) => ! $employee['has_signature'])
            ->values();

        return [
            'total_reports' => $reports->count(),
            'total_photos' => $reports->sum('photos_count'),
            'total_employees' => $employeeSummaries->count(),
            'total_employees_with_signature' => $employeeSummaries
                ->filter(fn ($employee) => $employee['has_signature'])
                ->count(),
            'total_employees_without_signature' => $employeesWithoutSignature->count(),
            'employees_without_signature' => $employeesWithoutSignature,
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

        if (in_array($user->role?->name, ['kanit', 'gkm'], true)) {
            $this->unit_id = $user->employee?->unit_id;
        }
    }

    public function getEmployeeOptionsProperty()
    {
        if (! $this->unit_id) {
            return collect();
        }

        return DailyReport::query()
            ->with('employee')
            ->where('unit_id', $this->unit_id)
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->whereNotNull('employee_id')
            ->get()
            ->pluck('employee')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    public function render()
    {
        return view('livewire.reports.export-monthly-report');
    }
}