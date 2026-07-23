<?php

namespace App\Http\Controllers;

use App\Models\OperationalTicket;
use App\Models\OperationalTicketNote;
use App\Models\Employee;
use App\Services\ActivityLogger;
use App\Services\AppNotifier;
use App\Services\OperationalTicketDailyReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class OperationalTicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->canAccessEmployeeArea(),
            403
        );

        $query = OperationalTicket::query()
            ->with(['unit', 'assignedToEmployee', 'createdByUser'])
            ->withCount('dailyReports')
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;
            $employeeId = $user->employee?->id;

            if ($unitId) {
                $query->where(function ($scope) use ($unitId, $employeeId, $user) {
                    $scope
                        ->where('unit_id', $unitId)
                        ->orWhere('created_by_user_id', $user->id);

                    if ($employeeId) {
                        $scope->orWhere('assigned_to_employee_id', $employeeId);
                    }

                    if ($user->isKanit() || $user->isGkm()) {
                        $scope->orWhere(function ($publicQuery) {
                            $publicQuery
                                ->where('source', OperationalTicket::SOURCE_PUBLIC)
                                ->whereNull('unit_id');
                        });
                    }
                });
            } else {
                $query->where('created_by_user_id', $user->id);
            }
        }

        $baseQuery = clone $query;

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'baru' => (clone $baseQuery)->where('status', OperationalTicket::STATUS_BARU)->count(),
            'diproses' => (clone $baseQuery)->where('status', OperationalTicket::STATUS_DIPROSES)->count(),
            'menunggu_pemohon' => (clone $baseQuery)->where('status', OperationalTicket::STATUS_MENUNGGU_PEMOHON)->count(),
            'selesai' => (clone $baseQuery)->where('status', OperationalTicket::STATUS_SELESAI)->count(),
            'public' => (clone $baseQuery)->where('source', OperationalTicket::SOURCE_PUBLIC)->count(),
        ];

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to_employee_id')) {
            if ($request->assigned_to_employee_id === 'none') {
                $query->whereNull('assigned_to_employee_id');
            } else {
                $query->where('assigned_to_employee_id', $request->assigned_to_employee_id);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%")
                    ->orWhere('requester_unit', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $employeesQuery = Employee::query()
            ->with('unit')
            ->orderBy('name');

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            if ($unitId) {
                $employeesQuery->where('unit_id', $unitId);
            } else {
                $employeesQuery->whereRaw('1 = 0');
            }
        }

        $employees = $employeesQuery->get();

        $tickets = $query
            ->paginate(10)
            ->withQueryString();

        return view('operations.tickets.index', [
            'tickets' => $tickets,
            'summary' => $summary,
            'employees' => $employees,
            'statusOptions' => OperationalTicket::statusOptions(),
            'categoryOptions' => OperationalTicket::categoryOptions(),
            'sourceOptions' => OperationalTicket::sourceOptions(),
            'priorityOptions' => OperationalTicket::priorityOptions(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->canAccessEmployeeArea(),
            403
        );

        return view('operations.tickets.create', [
            'categoryOptions' => OperationalTicket::categoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->canAccessEmployeeArea(),
            403
        );

        $validated = $request->validate([
            'requester_name' => ['required', 'string', 'max:255'],
            'requester_contact' => ['nullable', 'string', 'max:50'],
            'requester_unit' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'requester_name.required' => 'Nama pemohon wajib diisi.',
            'category.required' => 'Jenis permintaan wajib dipilih.',
            'title.required' => 'Judul atau keluhan singkat wajib diisi.',
        ]);

        $employee = $user->employee;

        $ticket = OperationalTicket::create([
            'source' => OperationalTicket::SOURCE_INTERNAL,
            'requester_name' => $validated['requester_name'],
            'requester_contact' => $validated['requester_contact'] ?? null,
            'requester_unit' => $validated['requester_unit'] ?? null,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => OperationalTicket::PRIORITY_NORMAL,
            'status' => OperationalTicket::STATUS_BARU,
            'unit_id' => $employee?->unit_id,
            'created_by_user_id' => $user->id,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Create Operational Ticket',
            'Membuat tiket operasional internal: ' . $ticket->ticket_code . ' - ' . $ticket->title,
            $ticket,
            null,
            [
                'ticket_code' => $ticket->ticket_code,
                'source' => $ticket->source,
                'requester_name' => $ticket->requester_name,
                'requester_unit' => $ticket->requester_unit,
                'category' => $ticket->category,
                'title' => $ticket->title,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'unit_id' => $ticket->unit_id,
                'created_by_user_id' => $ticket->created_by_user_id,
            ]
        );

        return redirect()
            ->route('operations.tickets.index')
            ->with('success', "Tiket {$ticket->ticket_code} berhasil dibuat.");
    }

    public function show(Request $request, OperationalTicket $ticket)
    {
        $user = $request->user();

        $this->authorizeTicketAccess($user, $ticket);

        $ticket->load([
            'unit',
            'assignedToEmployee',
            'createdByUser',
            'closedByUser',
            'notes.createdByUser',

            'dailyReports' => fn ($query) => $query
                ->with(['employee', 'duty'])
                ->orderByDesc('report_date')
                ->orderByDesc('id'),

            'automaticDelegations' => fn ($query) => $query
                ->with([
                    'duty',
                    'ownerEmployee',
                    'delegateEmployee',
                ])
                ->orderByDesc('start_date')
                ->orderByDesc('id'),
        ]);

        $employeesQuery = Employee::query()
            ->with('unit')
            ->orderBy('name');

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            if ($unitId) {
                $employeesQuery->where('unit_id', $unitId);
            } else {
                $employeesQuery->whereRaw('1 = 0');
            }
        }

        $employees = $employeesQuery->get();

        return view('operations.tickets.show', [
            'ticket' => $ticket,
            'employees' => $employees,
            'statusOptions' => OperationalTicket::statusOptions(),
            'priorityOptions' => OperationalTicket::priorityOptions(),
            'noteVisibilityOptions' => OperationalTicketNote::visibilityOptions(),
        ]);
    }

    public function updateStatus(
        Request $request,
        OperationalTicket $ticket,
        OperationalTicketDailyReportService $dailyReportService
    ) {
        $user = $request->user();

        $this->authorizeTicketManageAccess($user, $ticket);

        abort_if(
            $ticket->isClosed(),
            403,
            'Tiket yang sudah selesai atau dibatalkan tidak bisa diubah statusnya.'
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    array_keys(OperationalTicket::statusOptions())
                ),
            ],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $newStatus = $validated['status'];

        /*
        * Menyimpan status yang sama tidak perlu menjalankan integrasi ulang.
        */
        if ($newStatus === $ticket->status) {
            return redirect()
                ->route('operations.tickets.show', $ticket)
                ->with('success', 'Status tiket tidak mengalami perubahan.');
        }

        try {
            $result = DB::transaction(function () use (
                $ticket,
                $user,
                $newStatus,
                $dailyReportService
            ) {
                /*
                * Kunci tiket agar dua request tidak menyelesaikan tiket
                * yang sama secara bersamaan.
                */
                $lockedTicket = OperationalTicket::query()
                    ->with('assignedToEmployee.user')
                    ->whereKey($ticket->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedTicket->isClosed()) {
                    throw ValidationException::withMessages([
                        'status' => 'Tiket sudah selesai atau dibatalkan oleh proses lain.',
                    ]);
                }

                $oldStatus = $lockedTicket->status;

                $oldValues = [
                    'status' => $lockedTicket->status,
                    'closed_at' => $lockedTicket->closed_at,
                    'closed_by_user_id' => $lockedTicket->closed_by_user_id,
                    'assigned_to_employee_id' => $lockedTicket->assigned_to_employee_id,
                ];

                $completionReport = null;
                $completionReportStatus = null;
                $completionDate = null;

                /*
                * Rule khusus ketika tiket akan diselesaikan.
                */
                if ($newStatus === OperationalTicket::STATUS_SELESAI) {
                    if (! $lockedTicket->assigned_to_employee_id) {
                        throw ValidationException::withMessages([
                            'status' => 'Tiket tidak dapat diselesaikan karena PIC belum ditentukan.',
                        ]);
                    }

                    $pic = $lockedTicket->assignedToEmployee;

                    if (! $pic) {
                        throw ValidationException::withMessages([
                            'status' => 'Data PIC tiket tidak ditemukan.',
                        ]);
                    }

                    /*
                    * Tanggal laporan penyelesaian harus sama dengan tanggal
                    * tiket dinyatakan selesai.
                    */
                    $completionDate = now()->toDateString();

                    $integrationResult = $dailyReportService->createForAssignedPic(
                        ticket: $lockedTicket,
                        pic: $pic,
                        assignedBy: $user,
                        previousPic: null,
                        reportDate: $completionDate,
                        isContinuation: false,
                        isCompletion: true
                    );

                    if (! ($integrationResult['success'] ?? false)) {
                        throw ValidationException::withMessages([
                            'status' => $integrationResult['message']
                                ?? 'Laporan penyelesaian gagal dibuat. Status tiket tidak diubah.',
                        ]);
                    }

                    $completionReport = $integrationResult['report'] ?? null;
                    $completionReportStatus = $integrationResult['status'] ?? null;

                    if (! $completionReport) {
                        throw ValidationException::withMessages([
                            'status' => 'Laporan penyelesaian tidak ditemukan. Status tiket tidak diubah.',
                        ]);
                    }
                }

                $payload = [
                    'status' => $newStatus,
                ];

                if ($newStatus === OperationalTicket::STATUS_SELESAI) {
                    $payload['closed_at'] = now();
                    $payload['closed_by_user_id'] = $user->id;
                } else {
                    /*
                    * Status selain Selesai tidak boleh menyimpan metadata
                    * penyelesaian.
                    */
                    $payload['closed_at'] = null;
                    $payload['closed_by_user_id'] = null;
                }

                /*
                * Status Dibatalkan langsung diperbarui tanpa membuat laporan.
                */
                $lockedTicket->update($payload);
                $lockedTicket->refresh();

                /*
                * Buat catatan lifecycle otomatis ketika tiket memasuki status akhir.
                *
                * Catatan ini disimpan sebagai internal agar timeline tiket tidak kosong
                * walaupun pengelola tidak menulis catatan manual sebelum menutup tiket.
                */
                $lifecycleNote = null;

                if (
                    in_array($newStatus, [
                        OperationalTicket::STATUS_SELESAI,
                        OperationalTicket::STATUS_DIBATALKAN,
                    ], true)
                ) {
                    $actorName = $user->employee?->name
                        ?? $user->name
                        ?? 'Pengelola';

                    $lifecycleNoteText = match ($newStatus) {
                        OperationalTicket::STATUS_SELESAI => sprintf(
                            'Tiket dinyatakan selesai oleh %s. Seluruh proses penanganan tiket telah diselesaikan.',
                            $actorName
                        ),

                        OperationalTicket::STATUS_DIBATALKAN => sprintf(
                            'Tiket dibatalkan oleh %s. Proses penanganan tiket dihentikan dan tiket ditutup.',
                            $actorName
                        ),

                        default => null,
                    };

                    if ($lifecycleNoteText) {
                        $lifecycleNote = OperationalTicketNote::create([
                            'operational_ticket_id' => $lockedTicket->id,
                            'created_by_user_id' => $user->id,
                            'visibility' => OperationalTicketNote::VISIBILITY_INTERNAL,
                            'note' => $lifecycleNoteText,
                        ]);
                    }
                }

                ActivityLogger::log(
                    module: 'Operasional SIM/TI',
                    action: match ($newStatus) {
                        OperationalTicket::STATUS_SELESAI =>
                            'Complete Operational Ticket',

                        OperationalTicket::STATUS_DIBATALKAN =>
                            'Cancel Operational Ticket',

                        default =>
                            'Update Operational Ticket Status',
                    },
                    description: match ($newStatus) {
                        OperationalTicket::STATUS_SELESAI => sprintf(
                            'Menyelesaikan tiket %s dari status %s',
                            $lockedTicket->ticket_code,
                            $oldStatus
                        ),

                        OperationalTicket::STATUS_DIBATALKAN => sprintf(
                            'Membatalkan tiket %s dari status %s',
                            $lockedTicket->ticket_code,
                            $oldStatus
                        ),

                        default => sprintf(
                            'Memperbarui status tiket %s dari %s menjadi %s',
                            $lockedTicket->ticket_code,
                            $oldStatus,
                            $lockedTicket->status
                        ),
                    },
                    subject: $lockedTicket,
                    oldValues: $oldValues,
                    newValues: [
                        'status' => $lockedTicket->status,
                        'closed_at' => $lockedTicket->closed_at,
                        'closed_by_user_id' => $lockedTicket->closed_by_user_id,
                        'assigned_to_employee_id' => $lockedTicket->assigned_to_employee_id,
                        'completion_report_id' => $completionReport?->id,
                        'completion_report_date' => $completionDate,
                        'completion_report_status' => $completionReportStatus,
                        'lifecycle_note_id' => $lifecycleNote?->id,
                    ]
                );

                return [
                    'ticket' => $lockedTicket,
                    'completion_report' => $completionReport,
                    'completion_report_status' => $completionReportStatus,
                ];
            });

            /** @var OperationalTicket $updatedTicket */
            $updatedTicket = $result['ticket'];

            /*
            * Notifikasi lifecycle dikirim setelah transaction berhasil commit.
            *
            * Jangan letakkan notifikasi ini di dalam transaction karena notifikasi
            * tidak boleh memengaruhi keberhasilan penyimpanan status tiket.
            */
            if (
                in_array($newStatus, [
                    OperationalTicket::STATUS_SELESAI,
                    OperationalTicket::STATUS_DIBATALKAN,
                ], true)
                && filled($updatedTicket->assigned_to_employee_id)
            ) {
                if ($newStatus === OperationalTicket::STATUS_SELESAI) {
                    AppNotifier::notifyEmployee(
                        employee: (int) $updatedTicket->assigned_to_employee_id,
                        module: 'Operasional SIM/TI',
                        title: 'Tiket telah diselesaikan',
                        message: sprintf(
                            'Tiket %s: %s telah dinyatakan selesai.',
                            $updatedTicket->ticket_code,
                            $updatedTicket->title
                        ),
                        url: route(
                            'operations.tickets.show',
                            $updatedTicket,
                            false
                        ),
                        data: [
                            'type' => 'ticket_completed',
                            'ticket_id' => $updatedTicket->id,
                            'ticket_code' => $updatedTicket->ticket_code,
                            'completed_by_user_id' => $user->id,
                            'completion_report_id' =>
                                $result['completion_report']?->id,
                            'completion_report_status' =>
                                $result['completion_report_status'],
                        ],
                    );
                }

                if ($newStatus === OperationalTicket::STATUS_DIBATALKAN) {
                    AppNotifier::notifyEmployee(
                        employee: (int) $updatedTicket->assigned_to_employee_id,
                        module: 'Operasional SIM/TI',
                        title: 'Tiket dibatalkan',
                        message: sprintf(
                            'Tiket %s: %s telah dibatalkan oleh pengelola.',
                            $updatedTicket->ticket_code,
                            $updatedTicket->title
                        ),
                        url: route(
                            'operations.tickets.show',
                            $updatedTicket,
                            false
                        ),
                        data: [
                            'type' => 'ticket_cancelled',
                            'ticket_id' => $updatedTicket->id,
                            'ticket_code' => $updatedTicket->ticket_code,
                            'cancelled_by_user_id' => $user->id,
                        ],
                    );
                }
            }

            if ($newStatus === OperationalTicket::STATUS_SELESAI) {
                $message = $result['completion_report_status'] === 'created'
                    ? 'Tiket berhasil diselesaikan dan laporan penyelesaian hari ini berhasil dibuat.'
                    : 'Tiket berhasil diselesaikan. Laporan PIC untuk tanggal penyelesaian sudah tersedia.';

                return redirect()
                    ->route('operations.tickets.show', $updatedTicket)
                    ->with('success', $message);
            }

            if ($newStatus === OperationalTicket::STATUS_DIBATALKAN) {
                return redirect()
                    ->route('operations.tickets.show', $updatedTicket)
                    ->with(
                        'success',
                        'Tiket berhasil dibatalkan tanpa membuat laporan baru.'
                    );
            }

            return redirect()
                ->route('operations.tickets.show', $updatedTicket)
                ->with('success', 'Status tiket berhasil diperbarui.');
        } catch (ValidationException $exception) {
    $errorMessage = collect($exception->errors())
        ->flatten()
        ->filter()
        ->first()
        ?? 'Validasi penyelesaian tiket gagal.';

    /*
     * Catat kegagalan lifecycle untuk kebutuhan audit.
     */
    ActivityLogger::log(
        module: 'Operasional SIM/TI',
        action: $newStatus === OperationalTicket::STATUS_SELESAI
            ? 'Complete Operational Ticket Failed'
            : 'Update Operational Ticket Status Failed',
        description: sprintf(
            'Gagal mengubah status tiket %s dari %s menjadi %s: %s',
            $ticket->ticket_code,
            $ticket->status,
            $newStatus,
            $errorMessage
        ),
        subject: $ticket,
        oldValues: [
            'status' => $ticket->status,
            'closed_at' => $ticket->closed_at,
            'closed_by_user_id' => $ticket->closed_by_user_id,
            'assigned_to_employee_id' =>
                $ticket->assigned_to_employee_id,
        ],
        newValues: [
            'requested_status' => $newStatus,
            'success' => false,
            'reason' => $errorMessage,
            'updated_by_user_id' => $user->id,
        ]
    );

    /*
     * Pengelola yang melakukan aksi diberi notifikasi pribadi.
     * Notifikasi ini terutama berguna jika kegagalan terjadi karena
     * periode finalized, tupoksi, atau data PIC.
     */
    AppNotifier::notifyUser(
        user: $user,
        module: 'Operasional SIM/TI',
        title: 'Tiket gagal diselesaikan',
        message: sprintf(
            'Tiket %s tidak dapat diselesaikan: %s',
            $ticket->ticket_code,
            $errorMessage
        ),
        url: route(
            'operations.tickets.show',
            $ticket,
            false
        ),
        data: [
            'type' => 'ticket_completion_failed',
            'ticket_id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'requested_status' => $newStatus,
            'reason' => $errorMessage,
        ],
    );

    throw $exception;
    } catch (Throwable $exception) {
        report($exception);

        ActivityLogger::log(
            module: 'Operasional SIM/TI',
            action: 'Update Operational Ticket Status Error',
            description: sprintf(
                'Terjadi error sistem ketika mengubah status tiket %s menjadi %s',
                $ticket->ticket_code,
                $newStatus
            ),
            subject: $ticket,
            oldValues: [
                'status' => $ticket->status,
                'closed_at' => $ticket->closed_at,
                'closed_by_user_id' => $ticket->closed_by_user_id,
            ],
            newValues: [
                'requested_status' => $newStatus,
                'success' => false,
                'exception' => class_basename($exception),
                'message' => $exception->getMessage(),
                'updated_by_user_id' => $user->id,
            ]
        );

        AppNotifier::notifyUser(
            user: $user,
            module: 'Operasional SIM/TI',
            title: 'Update status tiket gagal',
            message: sprintf(
                'Status tiket %s gagal diperbarui karena terjadi kesalahan sistem.',
                $ticket->ticket_code
            ),
            url: route(
                'operations.tickets.show',
                $ticket,
                false
            ),
            data: [
                'type' => 'ticket_status_error',
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'requested_status' => $newStatus,
            ],
        );

        return redirect()
            ->route('operations.tickets.show', $ticket)
            ->withInput()
            ->with(
                'warning',
                'Status tiket gagal diperbarui karena terjadi kesalahan sistem. Tidak ada perubahan yang disimpan.'
            );
    }
    }

    public function updateAssignment(
        Request $request,
        OperationalTicket $ticket,
        OperationalTicketDailyReportService $dailyReportService
    ) {
        $user = $request->user();

        $this->authorizeTicketManageAccess($user, $ticket);

        abort_if(
            $ticket->isClosed(),
            403,
            'Tiket yang sudah selesai atau dibatalkan tidak bisa diubah.'
        );

        $validated = $request->validate([
            'assigned_to_employee_id' => [
                'nullable',
                'exists:employees,id',
            ],
            'priority' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    array_keys(OperationalTicket::priorityOptions())
                ),
            ],
        ], [
            'assigned_to_employee_id.exists' => 'PIC tidak valid.',
            'priority.required' => 'Prioritas wajib dipilih.',
            'priority.in' => 'Prioritas tidak valid.',
        ]);

        $oldAssignedEmployeeId = $ticket->assigned_to_employee_id;
        $newAssignedEmployeeId = $validated['assigned_to_employee_id'] ?? null;

        /*
        * Simpan model PIC lama sebelum tiket diperbarui.
        * Model ini dipakai untuk keterangan handover.
        */
        $previousPic = filled($oldAssignedEmployeeId)
            ? Employee::query()->find($oldAssignedEmployeeId)
            : null;

        $selectedPic = null;

        if (filled($newAssignedEmployeeId)) {
            $selectedPic = Employee::query()
                ->with('user')
                ->findOrFail($newAssignedEmployeeId);

            abort_unless(
                $selectedPic->is_active,
                422,
                'PIC yang dipilih sudah tidak aktif.'
            );

            if (! $user->isAdmin()) {
                abort_unless(
                    (int) $selectedPic->unit_id
                        === (int) $user->employee?->unit_id,
                    403,
                    'PIC harus berasal dari unit yang sama.'
                );
            }
        }

        $oldValues = [
            'assigned_to_employee_id' => $ticket->assigned_to_employee_id,
            'priority' => $ticket->priority,
            'unit_id' => $ticket->unit_id,
            'daily_report_count' => $ticket->dailyReports()->count(),
            'automatic_delegation_count' => $ticket
                ->automaticDelegations()
                ->count(),
        ];

        $updateData = [
            'assigned_to_employee_id' => $newAssignedEmployeeId,
            'priority' => $validated['priority'],
        ];

        /*
        * Tiket public yang belum punya unit akan masuk ke unit pengelola
        * ketika pertama kali dikelola.
        */
        if (blank($ticket->unit_id) && $user->employee?->unit_id) {
            $updateData['unit_id'] = $user->employee->unit_id;
        }

        $ticket->update($updateData);
        $ticket->refresh();

        ActivityLogger::log(
            'Operasional SIM/TI',
            match (true) {
                $oldAssignedEmployeeId && ! $newAssignedEmployeeId
                    => 'Unassign Operational Ticket PIC',

                $oldAssignedEmployeeId
                    && $newAssignedEmployeeId
                    && (int) $oldAssignedEmployeeId !== (int) $newAssignedEmployeeId
                    => 'Reassign Operational Ticket PIC',

                ! $oldAssignedEmployeeId && $newAssignedEmployeeId
                    => 'Assign Operational Ticket PIC',

                default
                    => 'Update Operational Ticket Assignment',
            },
            match (true) {
                $oldAssignedEmployeeId && ! $newAssignedEmployeeId
                    => sprintf(
                        'Melepas PIC tiket %s tanpa menghapus laporan dan delegasi lama',
                        $ticket->ticket_code
                    ),

                $oldAssignedEmployeeId
                    && $newAssignedEmployeeId
                    && (int) $oldAssignedEmployeeId !== (int) $newAssignedEmployeeId
                    => sprintf(
                        'Mengalihkan PIC tiket %s dari pegawai ID %s ke pegawai ID %s',
                        $ticket->ticket_code,
                        $oldAssignedEmployeeId,
                        $newAssignedEmployeeId
                    ),

                ! $oldAssignedEmployeeId && $newAssignedEmployeeId
                    => sprintf(
                        'Menetapkan PIC pegawai ID %s pada tiket %s',
                        $newAssignedEmployeeId,
                        $ticket->ticket_code
                    ),

                default
                    => 'Memperbarui PIC/prioritas tiket: '
                        . $ticket->ticket_code,
            },
            $ticket,
            $oldValues,
            [
                'assigned_to_employee_id' => $ticket->assigned_to_employee_id,
                'priority' => $ticket->priority,
                'unit_id' => $ticket->unit_id,
                'daily_report_count' => $ticket->dailyReports()->count(),
                'automatic_delegation_count' => $ticket
                    ->automaticDelegations()
                    ->count(),
                'history_preserved' => true,
            ]
        );

        $picChanged = (int) $newAssignedEmployeeId
            !== (int) $oldAssignedEmployeeId;

        $integrationResult = null;

        /*
        * Laporan otomatis hanya dibuat ketika PIC benar-benar berubah
        * dan PIC baru tidak kosong.
        */
        if ($picChanged && $selectedPic) {
            $integrationResult = $dailyReportService->createForAssignedPic(
                ticket: $ticket,
                pic: $selectedPic,
                assignedBy: $user,
                previousPic: $previousPic
            );
        }

        /*
        * Notifikasi kepada PIC baru.
        */
        if ($picChanged && filled($newAssignedEmployeeId)) {
            AppNotifier::notifyEmployee(
                employee: (int) $newAssignedEmployeeId,
                module: 'Operasional SIM/TI',
                title: $previousPic
                    ? 'Tiket dialihkan kepada Anda'
                    : 'Anda ditunjuk sebagai PIC tiket',
                message: $previousPic
                    ? sprintf(
                        'Tiket %s: %s dialihkan dari %s kepada Anda.',
                        $ticket->ticket_code,
                        $ticket->title,
                        $previousPic->name
                    )
                    : sprintf(
                        'Anda ditunjuk sebagai PIC untuk tiket %s: %s',
                        $ticket->ticket_code,
                        $ticket->title
                    ),
                url: route(
                    'operations.tickets.show',
                    $ticket,
                    false
                ),
                data: [
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->ticket_code,
                    'assigned_by_user_id' => $user->id,
                    'previous_pic_employee_id' => $previousPic?->id,
                    'type' => $previousPic
                        ? 'reassigned'
                        : 'assigned',
                ],
            );
        }

        /*
        * Notifikasi kepada PIC lama.
        */
        if ($picChanged && filled($oldAssignedEmployeeId)) {
            AppNotifier::notifyEmployee(
                employee: (int) $oldAssignedEmployeeId,
                module: 'Operasional SIM/TI',
                title: 'Penugasan PIC tiket diperbarui',
                message: filled($newAssignedEmployeeId)
                    ? sprintf(
                        'Tiket %s: %s telah dialihkan kepada %s.',
                        $ticket->ticket_code,
                        $ticket->title,
                        $selectedPic?->name ?? 'PIC baru'
                    )
                    : sprintf(
                        'Anda sudah tidak menjadi PIC untuk tiket %s: %s.',
                        $ticket->ticket_code,
                        $ticket->title
                    ),
                url: route(
                    'operations.tickets.show',
                    $ticket,
                    false
                ),
                data: [
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->ticket_code,
                    'updated_by_user_id' => $user->id,
                    'old_pic_employee_id' => $oldAssignedEmployeeId,
                    'new_pic_employee_id' => $selectedPic?->id,
                    'history_preserved' => true,
                    'daily_report_count' => $ticket
                        ->dailyReports()
                        ->count(),
                    'automatic_delegation_count' => $ticket
                        ->automaticDelegations()
                        ->count(),
                    'type' => filled($newAssignedEmployeeId)
                        ? 'handed_over'
                        : 'unassigned',
                ],
            );
        }

        $redirect = redirect()
            ->route('operations.tickets.show', $ticket);

        /*
        * Hanya prioritas berubah atau PIC tidak berubah.
        */
        if (! $picChanged) {
            return $redirect->with(
                'success',
                'PIC dan prioritas tiket berhasil diperbarui.'
            );
        }

        /*
        * PIC dilepas.
        */
        if (! $selectedPic) {
            return $redirect->with(
                'success',
                'PIC berhasil dilepas dan prioritas tiket diperbarui.'
            );
        }

        /*
        * Laporan berhasil dibuat atau sudah tersedia.
        */
        if ($integrationResult && $integrationResult['success']) {
            $message = match ($integrationResult['status']) {
                'already_exists' =>
                    'PIC berhasil diperbarui. Laporan tiket untuk PIC hari ini sudah tersedia.',

                'created' => $previousPic
                    ? 'PIC berhasil dialihkan dan laporan lanjutan untuk PIC baru berhasil dibuat.'
                    : 'PIC berhasil ditunjuk dan laporan awal tiket berhasil dibuat.',

                default =>
                    'PIC berhasil diperbarui dan laporan tiket tersedia.',
            };

            return $redirect->with('success', $message);
        }

        /*
        * Assignment tetap berhasil, tetapi laporan gagal dibuat.
        */
        return $redirect
            ->with(
                'success',
                $previousPic
                    ? 'PIC tiket berhasil dialihkan.'
                    : 'PIC tiket berhasil ditunjuk.'
            )
            ->with(
                'warning',
                $integrationResult['message']
                    ?? 'Laporan otomatis belum berhasil dibuat.'
            );
    }

    public function createContinuationReport(
        Request $request,
        OperationalTicket $ticket,
        OperationalTicketDailyReportService $dailyReportService
    ) {
        $user = $request->user();
        $employee = $user->employee;

        abort_unless(
            $user->canAccessEmployeeArea(),
            403,
            'Hanya pegawai PIC yang dapat membuat laporan lanjutan.'
        );

        abort_unless(
            $employee
            && (int) $ticket->assigned_to_employee_id === (int) $employee->id,
            403,
            'Anda bukan PIC aktif untuk tiket ini.'
        );

        abort_if(
            $ticket->isClosed(),
            422,
            'Tiket yang sudah selesai atau dibatalkan tidak dapat dibuatkan laporan lanjutan.'
        );

        $result = $dailyReportService->createForAssignedPic(
            ticket: $ticket,
            pic: $employee,
            assignedBy: $user,
            previousPic: null,
            reportDate: now()->toDateString(),
            isContinuation: true
        );

        if ($result['success'] && $result['report']) {
            return redirect()
                ->route('pegawai.reports.edit', $result['report'])
                ->with(
                    'success',
                    $result['status'] === 'already_exists'
                        ? 'Laporan tiket untuk hari ini sudah tersedia.'
                        : 'Laporan lanjutan hari ini berhasil dibuat. Silakan lengkapi aktivitas pekerjaan.'
                );
        }

        return redirect()
            ->route('operations.tickets.show', $ticket)
            ->with(
                'warning',
                $result['message']
                    ?? 'Laporan lanjutan belum berhasil dibuat.'
            );
    }

    public function storeNote(Request $request, OperationalTicket $ticket)
    {
        $user = $request->user();

        $this->authorizeTicketAccess($user, $ticket);

        $isManager = $user->isAdmin() || $user->isKanit() || $user->isGkm();
        $isPic = (int) $ticket->assigned_to_employee_id === (int) $user->employee?->id;

        abort_unless(
            $isManager || $isPic,
            403,
            'Anda hanya dapat menambahkan catatan pada tiket yang menjadi tanggung jawab Anda.'
        );

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:3000'],
            'visibility' => ['required', 'string', 'in:' . implode(',', array_keys(OperationalTicketNote::visibilityOptions()))],
        ], [
            'note.required' => 'Catatan wajib diisi.',
            'visibility.required' => 'Visibility catatan wajib dipilih.',
            'visibility.in' => 'Visibility catatan tidak valid.',
        ]);

        // Pegawai non-pengelola hanya boleh tambah catatan internal jika dia PIC tiket.
        if (! $user->isAdmin() && ! $user->isKanit() && ! $user->isGkm()) {
            abort_unless(
                $ticket->assigned_to_employee_id === $user->employee?->id,
                403,
                'Anda hanya bisa menambahkan catatan pada tiket yang menjadi tanggung jawab Anda.'
            );

            $validated['visibility'] = OperationalTicketNote::VISIBILITY_INTERNAL;
        }

        $note = OperationalTicketNote::create([
            'operational_ticket_id' => $ticket->id,
            'created_by_user_id' => $user->id,
            'visibility' => $validated['visibility'],
            'note' => $validated['note'],
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Create Operational Ticket Note',
            'Menambahkan catatan ' . $note->visibility . ' pada tiket: ' . $ticket->ticket_code,
            $note,
            null,
            [
                'operational_ticket_id' => $note->operational_ticket_id,
                'ticket_code' => $ticket->ticket_code,
                'visibility' => $note->visibility,
                'created_by_user_id' => $note->created_by_user_id,
            ]
        );

        return redirect()
            ->route('operations.tickets.show', $ticket)
            ->with('success', 'Catatan tiket berhasil ditambahkan.');
    }

    public function destroy(Request $request, OperationalTicket $ticket)
    {
        $user = $request->user();

        $this->authorizeTicketManageAccess($user, $ticket);

        /*
        * Laporan tiket merupakan arsip pekerjaan pegawai.
        * Tiket sumber tidak boleh dihapus setelah laporan tercipta.
        */
        abort_if(
            $ticket->dailyReports()->exists(),
            422,
            'Tiket tidak dapat dihapus karena sudah mempunyai laporan harian terkait.'
        );

        abort_unless(
            in_array($ticket->status, [
                OperationalTicket::STATUS_BARU,
                OperationalTicket::STATUS_DIBATALKAN,
            ], true),
            422,
            'Tiket hanya bisa dihapus jika status Baru atau Dibatalkan.'
        );

        $oldValues = [
            'ticket_code' => $ticket->ticket_code,
            'source' => $ticket->source,
            'requester_name' => $ticket->requester_name,
            'requester_unit' => $ticket->requester_unit,
            'category' => $ticket->category,
            'title' => $ticket->title,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'unit_id' => $ticket->unit_id,
            'assigned_to_employee_id' => $ticket->assigned_to_employee_id,
            'created_by_user_id' => $ticket->created_by_user_id,
            'automatic_delegation_count' => $ticket
                ->automaticDelegations()
                ->count(),
        ];

        $ticketCode = $ticket->ticket_code;
        $ticketTitle = $ticket->title;

        /*
        * Delegasi otomatis tanpa laporan boleh tetap menjadi histori,
        * tetapi foreign key operational_ticket_id harus mendukung SET NULL.
        *
        * Jika database belum memakai nullOnDelete, P7B testing akan
        * memperlihatkan error constraint dan kita benahi migrasinya.
        */
        ActivityLogger::log(
            'Operasional SIM/TI',
            'Delete Operational Ticket',
            'Menghapus tiket operasional: '
                . $ticketCode
                . ' - '
                . $ticketTitle,
            $ticket,
            $oldValues,
            null
        );

        $ticket->delete();

        return redirect()
            ->route('operations.tickets.index')
            ->with(
                'success',
                'Tiket operasional berhasil dihapus.'
            );
    }

    private function authorizeTicketAccess($user, OperationalTicket $ticket): void
    {
        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->canAccessEmployeeArea(),
            403
        );

        if ($user->isAdmin()) {
            return;
        }

        $employeeId = $user->employee?->id;
        $unitId = $user->employee?->unit_id;

        abort_unless(
            // Tiket unit sendiri
            ($unitId && (int) $ticket->unit_id === (int) $unitId)

            // Tiket yang dibuat user login
            || (int) $ticket->created_by_user_id === (int) $user->id

            // Tiket yang ditugaskan ke pegawai login
            || ($employeeId && (int) $ticket->assigned_to_employee_id === (int) $employeeId)

            // Tiket public yang belum masuk unit hanya boleh dilihat pengelola
            || (
                $ticket->source === OperationalTicket::SOURCE_PUBLIC
                && blank($ticket->unit_id)
                && ($user->isKanit() || $user->isGkm())
            ),
            403
        );
    }

    private function authorizeTicketManageAccess($user, OperationalTicket $ticket): void
    {
        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm(),
            403
        );

        if ($user->isAdmin()) {
            return;
        }

        $unitId = $user->employee?->unit_id;

        abort_unless(
            $unitId && (
                (int) $ticket->unit_id === (int) $unitId
                || blank($ticket->unit_id)
            ),
            403
        );
    }
}