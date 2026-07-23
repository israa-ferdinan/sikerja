<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\OperationalTicket;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class OperationalTicketDailyReportService
{
    public function __construct(
        private readonly OperationalTicketDutyResolver $dutyResolver,
        private readonly OperationalTicketDelegationService $delegationService,
        private readonly MonthlyReportApprovalService $approvalService,
    ) {
    }

    /**
     * Membuat laporan otomatis saat PIC ditunjuk atau diganti.
     *
     * Maksimal satu laporan untuk kombinasi:
     * tiket + PIC + tanggal.
     */
    public function createForAssignedPic(
        OperationalTicket $ticket,
        Employee $pic,
        User $assignedBy,
        ?Employee $previousPic = null,
        CarbonInterface|string|null $reportDate = null,
        bool $isContinuation = false,
        bool $isCompletion = false
    ): array {
        $date = $reportDate
            ? \Carbon\Carbon::parse($reportDate)->toDateString()
            : now()->toDateString();

        $existingReport = $this->findExistingReport(
            ticket: $ticket,
            pic: $pic,
            reportDate: $date
        );

        if ($existingReport) {
            return $this->success(
                status: 'already_exists',
                message: 'Laporan tiket untuk PIC dan tanggal tersebut sudah tersedia.',
                report: $existingReport
            );
        }

        $validationResult = $this->validatePic(
            ticket: $ticket,
            pic: $pic,
            reportDate: $date
        );

        if ($validationResult !== null) {
            return $validationResult;
        }

        $resolution = $this->dutyResolver->resolve($ticket, $pic);

        if (! ($resolution['found'] ?? false)) {
            ActivityLogger::log(
                module: 'daily_report',
                action: 'create_from_operational_ticket_failed',
                description: sprintf(
                    'Laporan tiket %s untuk PIC %s tidak dibuat karena tupoksi tidak ditemukan',
                    $ticket->ticket_code,
                    $pic->name
                ),
                subject: $ticket,
                newValues: [
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->ticket_code,
                    'assigned_to_employee_id' => $pic->id,
                    'previous_pic_employee_id' => $previousPic?->id,
                    'report_date' => $date,
                    'status' => 'duty_not_found',
                    'reason' => $resolution['reason'] ?? null,
                ]
            );

            return [
                'success' => false,
                'status' => 'duty_not_found',
                'message' => $resolution['reason']
                    ?? 'Tupoksi yang sesuai tidak ditemukan.',
                'report' => null,
                'resolution' => $resolution,
            ];
        }

        try {
            return DB::transaction(function () use (
                $ticket,
                $pic,
                $assignedBy,
                $previousPic,
                $date,
                $resolution,
                $isContinuation,
                $isCompletion
            ) {
                $lockedTicket = OperationalTicket::query()
                    ->whereKey($ticket->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $existingReport = $this->findExistingReport(
                    ticket: $lockedTicket,
                    pic: $pic,
                    reportDate: $date
                );

                if ($existingReport) {
                    return $this->success(
                        status: 'already_exists',
                        message: 'Laporan tiket untuk PIC dan tanggal tersebut sudah tersedia.',
                        report: $existingReport,
                        resolution: $resolution
                    );
                }

                $delegation = null;
                $delegationResult = null;

                if (($resolution['type'] ?? null) === 'delegation_required') {
                    $delegationResult = $this->delegationService
                        ->resolveOrCreate(
                            ticket: $lockedTicket,
                            pic: $pic,
                            resolution: $resolution,
                            assignedBy: $assignedBy,
                            delegationDate: $date
                        );

                    $delegation = $delegationResult['delegation'];
                }

                $isDelegated = ($resolution['type'] ?? null)
                    === 'delegation_required';

                $picUser = $pic->user;

                $report = DailyReport::create([
                    'operational_ticket_id' => $lockedTicket->id,

                    'user_id' => $picUser->id,
                    'employee_id' => $pic->id,
                    'unit_id' => $pic->unit_id,

                    'duty_id' => $resolution['duty']->id,

                    'server_id' => null,
                    'application_id' => null,

                    'report_date' => $date,

                    'title' => $this->buildTitle(
                        ticket: $lockedTicket,
                        previousPic: $previousPic,
                        isContinuation: $isContinuation,
                        isCompletion: $isCompletion
                    ),

                    'description' => $this->buildDescription(
                        ticket: $lockedTicket,
                        pic: $pic,
                        previousPic: $previousPic,
                        reportDate: $date,
                        isContinuation: $isContinuation,
                        isCompletion: $isCompletion
                    ),

                    'notes' => null,
                    'status' => 'submitted',

                    'is_delegated' => $isDelegated,
                    'delegation_id' => $delegation?->id,

                    'duty_owner_employee_id' => $isDelegated
                        ? $resolution['owner_employee']->id
                        : $pic->id,

                    'reported_by_employee_id' => $pic->id,
                ]);

                ActivityLogger::log(
                    module: 'daily_report',
                    action: match (true) {
                        $isCompletion => 'create_ticket_completion_report',
                        $previousPic !== null => 'create_from_ticket_pic_handover',
                        $isContinuation => 'create_ticket_continuation_report',
                        default => 'create_from_operational_ticket',
                    },
                    description: match (true) {
                        $isCompletion => sprintf(
                            'Membuat laporan penyelesaian tiket %s untuk PIC %s',
                            $lockedTicket->ticket_code,
                            $pic->name
                        ),

                        $previousPic !== null => sprintf(
                            'Membuat laporan lanjutan tiket %s untuk PIC %s setelah dialihkan dari %s',
                            $lockedTicket->ticket_code,
                            $pic->name,
                            $previousPic->name
                        ),

                        $isContinuation => sprintf(
                            'Membuat laporan lanjutan hari ini untuk tiket %s dan PIC %s',
                            $lockedTicket->ticket_code,
                            $pic->name
                        ),

                        default => sprintf(
                            'Membuat laporan otomatis dari tiket %s untuk PIC %s',
                            $lockedTicket->ticket_code,
                            $pic->name
                        ),
                    },
                    subject: $report,
                    newValues: [
                        ...$report->fresh()->toArray(),
                        'ticket_code' => $lockedTicket->ticket_code,
                        'ticket_status' => $lockedTicket->status,
                        'category' => $lockedTicket->category,
                        'category_label' => $lockedTicket->category_label,
                        'previous_pic_employee_id' => $previousPic?->id,
                        'previous_pic_name' => $previousPic?->name,
                        'resolution_type' => $resolution['type'],
                        'resolution_score' => $resolution['score'] ?? 0,
                        'matched_keywords' => $resolution['matched_keywords'] ?? [],
                        'delegation_source' => $delegationResult['source'] ?? null,
                        'is_continuation' => $isContinuation,
                        'is_completion' => $isCompletion,
                    ]
                );

                return $this->success(
                    status: 'created',
                    message: match (true) {
                        $isCompletion =>
                            'Laporan penyelesaian tiket berhasil dibuat.',

                        $previousPic !== null =>
                            'Laporan lanjutan untuk PIC baru berhasil dibuat.',

                        $isContinuation =>
                            'Laporan lanjutan hari ini berhasil dibuat.',

                        default =>
                            'Laporan awal tiket berhasil dibuat.',
                    },
                    report: $report,
                    resolution: $resolution
                );
            });
        } catch (QueryException $exception) {
            $existingReport = $this->findExistingReport(
                ticket: $ticket,
                pic: $pic,
                reportDate: $date
            );

            if ($existingReport) {
                return $this->success(
                    status: 'already_exists',
                    message: 'Laporan tiket untuk PIC dan tanggal tersebut sudah tersedia.',
                    report: $existingReport,
                    resolution: $resolution
                );
            }

            report($exception);

            return $this->integrationFailure(
                ticket: $ticket,
                pic: $pic,
                previousPic: $previousPic,
                reportDate: $date,
                exception: $exception
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->integrationFailure(
                ticket: $ticket,
                pic: $pic,
                previousPic: $previousPic,
                reportDate: $date,
                exception: $exception
            );
        }
    }

    private function validatePic(
        OperationalTicket $ticket,
        Employee $pic,
        string $reportDate
    ): ?array {
        if (! $pic->is_active) {
            return $this->failure(
                status: 'inactive_pic',
                message: 'PIC tidak aktif sehingga laporan tidak dapat dibuat.'
            );
        }

        if (! $pic->unit_id) {
            return $this->failure(
                status: 'missing_unit',
                message: 'PIC belum memiliki unit kerja.'
            );
        }

        $pic->loadMissing('user');

        if (! $pic->user) {
            return $this->failure(
                status: 'missing_user',
                message: 'PIC belum memiliki akun user yang terhubung.'
            );
        }

        if (! $pic->user->is_active) {
            return $this->failure(
                status: 'inactive_user',
                message: 'Akun user PIC tidak aktif.'
            );
        }

        if (
            $ticket->unit_id
            && (int) $ticket->unit_id !== (int) $pic->unit_id
        ) {
            return $this->failure(
                status: 'unit_mismatch',
                message: 'Unit tiket dan unit PIC tidak sesuai.'
            );
        }

        if ($this->approvalService->isReportDateLocked(
            unitId: (int) $pic->unit_id,
            reportDate: $reportDate
        )) {
            return $this->failure(
                status: 'period_locked',
                message: 'Periode laporan PIC sudah difinalisasi oleh Kanit.'
            );
        }

        return null;
    }

    private function findExistingReport(
        OperationalTicket $ticket,
        Employee $pic,
        string $reportDate
    ): ?DailyReport {
        return DailyReport::query()
            ->where('operational_ticket_id', $ticket->id)
            ->where('employee_id', $pic->id)
            ->whereDate('report_date', $reportDate)
            ->first();
    }

    private function buildTitle(
        OperationalTicket $ticket,
        ?Employee $previousPic,
        bool $isContinuation = false,
        bool $isCompletion = false
    ): string {
        $prefix = match (true) {
            $isCompletion => 'Penyelesaian Tiket',
            $previousPic !== null => 'Lanjutan Penanganan Tiket',
            $isContinuation => 'Lanjutan Penanganan Tiket',
            default => 'Penanganan Tiket',
        };

        return sprintf(
            '%s %s — %s',
            $prefix,
            $ticket->ticket_code,
            $ticket->title
        );
    }

    private function buildDescription(
        OperationalTicket $ticket,
        Employee $pic,
        ?Employee $previousPic,
        string $reportDate,
        bool $isContinuation = false,
        bool $isCompletion = false
    ): string {
        $lines = [
            'Sumber pekerjaan: Tiket Operasional ' . $ticket->ticket_code,
            'Kategori: ' . $ticket->category_label,
            'Status tiket saat laporan dibuat: ' . $ticket->status_label,
            'Tanggal laporan: ' . \Carbon\Carbon::parse($reportDate)->format('d-m-Y'),
            'PIC saat ini: ' . $pic->name,
        ];

        if ($isCompletion) {
            $lines[] = 'Jenis aktivitas: Penyelesaian tiket operasional';
        } elseif ($previousPic) {
            $lines[] = 'PIC sebelumnya: ' . $previousPic->name;
            $lines[] = 'Jenis aktivitas: Lanjutan penanganan setelah perpindahan PIC';
        } elseif ($isContinuation) {
            $lines[] = 'Jenis aktivitas: Lanjutan penanganan pada hari berbeda';
        } else {
            $lines[] = 'Jenis aktivitas: Penanganan awal tiket';
        }

        $lines[] = 'Pemohon: ' . $ticket->requester_name;

        if (filled($ticket->requester_unit)) {
            $lines[] = 'Unit pemohon: ' . $ticket->requester_unit;
        }

        $lines[] = '';
        $lines[] = 'Keluhan/permintaan:';
        $lines[] = filled($ticket->description)
            ? $ticket->description
            : $ticket->title;

        if ($previousPic && ! $isCompletion) {
            $lines[] = '';
            $lines[] = sprintf(
                'Pekerjaan ini merupakan kelanjutan penanganan dari %s.',
                $previousPic->name
            );
        }

        if ($isContinuation && ! $previousPic && ! $isCompletion) {
            $lines[] = '';
            $lines[] = 'Pekerjaan ini merupakan lanjutan penanganan tiket dari laporan pada hari sebelumnya.';
        }

        $lines[] = '';

        if ($isCompletion) {
            $lines[] = 'Aktivitas penyelesaian:';
            $lines[] = 'Silakan lengkapi tindakan akhir yang dilakukan, hasil penyelesaian, dan keterangan penutupan tiket.';
        } else {
            $lines[] = 'Aktivitas hari ini:';
            $lines[] = 'Silakan lengkapi pekerjaan yang dilakukan, hasil sementara, kendala, dan rencana tindak lanjut.';
        }

        return implode(PHP_EOL, $lines);
    }

    private function integrationFailure(
        OperationalTicket $ticket,
        Employee $pic,
        ?Employee $previousPic,
        string $reportDate,
        Throwable $exception
    ): array {
        ActivityLogger::log(
            module: 'daily_report',
            action: 'create_from_operational_ticket_failed',
            description: sprintf(
                'Gagal membuat laporan otomatis dari tiket %s untuk PIC %s',
                $ticket->ticket_code,
                $pic->name
            ),
            subject: $ticket,
            newValues: [
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'assigned_to_employee_id' => $pic->id,
                'previous_pic_employee_id' => $previousPic?->id,
                'report_date' => $reportDate,
                'status' => 'integration_error',
                'exception' => class_basename($exception),
                'message' => $exception->getMessage(),
            ]
        );

        return $this->failure(
            status: 'integration_error',
            message: 'PIC berhasil disimpan, tetapi laporan otomatis gagal dibuat.'
        );
    }

    private function success(
        string $status,
        string $message,
        DailyReport $report,
        ?array $resolution = null
    ): array {
        return [
            'success' => true,
            'status' => $status,
            'message' => $message,
            'report' => $report,
            'resolution' => $resolution,
        ];
    }

    private function failure(string $status, string $message): array
    {
        return [
            'success' => false,
            'status' => $status,
            'message' => $message,
            'report' => null,
            'resolution' => null,
        ];
    }
}