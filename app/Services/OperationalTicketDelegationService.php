<?php

namespace App\Services;

use App\Models\DutyDelegation;
use App\Models\Employee;
use App\Models\OperationalTicket;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class OperationalTicketDelegationService
{
    /**
     * Menggunakan delegasi existing atau membuat delegasi otomatis dari tiket.
     *
     * @param array{
     *     found: bool,
     *     type: string,
     *     duty: mixed,
     *     owner_employee: mixed,
     *     score: int,
     *     matched_keywords: array,
     *     reason: string
     * } $resolution
     *
     * @return array{
     *     delegation: DutyDelegation,
     *     created: bool,
     *     reused: bool,
     *     source: string
     * }
     */
    public function resolveOrCreate(
        OperationalTicket $ticket,
        Employee $pic,
        array $resolution,
        User $assignedBy,
        CarbonInterface|string|null $delegationDate = null
    ): array {
        if (
            ! ($resolution['found'] ?? false)
            || ($resolution['type'] ?? null) !== 'delegation_required'
        ) {
            throw new InvalidArgumentException(
                'Hasil resolver tidak membutuhkan delegasi tupoksi.'
            );
        }

        $duty = $resolution['duty'] ?? null;
        $owner = $resolution['owner_employee'] ?? null;

        if (! $duty || ! $owner) {
            throw new RuntimeException(
                'Tupoksi atau pemilik tupoksi hasil resolver tidak tersedia.'
            );
        }

        if ((int) $owner->id === (int) $pic->id) {
            throw new RuntimeException(
                'Pemilik tupoksi dan penerima delegasi tidak boleh sama.'
            );
        }

        if (
            ! $owner->is_active
            || ! $pic->is_active
        ) {
            throw new RuntimeException(
                'Pemilik tupoksi dan PIC harus berstatus aktif.'
            );
        }

        if (
            ! $owner->unit_id
            || ! $pic->unit_id
            || (int) $owner->unit_id !== (int) $pic->unit_id
        ) {
            throw new RuntimeException(
                'Pemilik tupoksi dan PIC harus berada dalam unit yang sama.'
            );
        }

        $date = $delegationDate
            ? \Carbon\Carbon::parse($delegationDate)->toDateString()
            : now()->toDateString();

        return DB::transaction(function () use (
            $ticket,
            $pic,
            $resolution,
            $assignedBy,
            $duty,
            $owner,
            $date
        ) {
            /*
             * Cegah tiket yang sama membuat delegasi khusus lebih dari sekali.
             */
            $ticketDelegation = DutyDelegation::query()
                ->where('operational_ticket_id', $ticket->id)
                ->where('duty_id', $duty->id)
                ->where('owner_employee_id', $owner->id)
                ->where('delegate_employee_id', $pic->id)
                ->activeForDate($date)
                ->first();

            if ($ticketDelegation) {
                return [
                    'delegation' => $ticketDelegation,
                    'created' => false,
                    'reused' => true,
                    'source' => 'ticket_existing',
                ];
            }

            /*
             * Gunakan delegasi existing apabila kombinasi tupoksi, owner,
             * penerima, dan periode sudah aktif.
             *
             * operational_ticket_id tidak diisi karena delegasi ini tidak
             * dibuat khusus oleh tiket tersebut.
             */
            $existingDelegation = DutyDelegation::query()
                ->where('duty_id', $duty->id)
                ->where('owner_employee_id', $owner->id)
                ->where('delegate_employee_id', $pic->id)
                ->activeForDate($date)
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->first();

            if ($existingDelegation) {
                ActivityLogger::log(
                    module: 'duty_delegation',
                    action: 'reuse_for_operational_ticket',
                    description: sprintf(
                        'Menggunakan delegasi tupoksi existing untuk tiket %s',
                        $ticket->ticket_code
                    ),
                    subject: $existingDelegation,
                    newValues: [
                        'operational_ticket_id' => $ticket->id,
                        'ticket_code' => $ticket->ticket_code,
                        'delegation_id' => $existingDelegation->id,
                        'duty_id' => $existingDelegation->duty_id,
                        'owner_employee_id' => $existingDelegation->owner_employee_id,
                        'delegate_employee_id' => $existingDelegation->delegate_employee_id,
                        'resolution_score' => $resolution['score'] ?? 0,
                        'matched_keywords' => $resolution['matched_keywords'] ?? [],
                        'reused_existing_delegation' => true,
                    ]
                );

                return [
                    'delegation' => $existingDelegation,
                    'created' => false,
                    'reused' => true,
                    'source' => 'active_existing',
                ];
            }

            /*
             * Buat delegasi khusus Tiket Operasional.
             */
            $delegation = DutyDelegation::create([
                'operational_ticket_id' => $ticket->id,
                'duty_id' => $duty->id,
                'owner_employee_id' => $owner->id,
                'delegate_employee_id' => $pic->id,
                'start_date' => $date,
                'end_date' => null,
                'is_active' => true,
                'notes' => sprintf(
                    'Delegasi otomatis dari Tiket Operasional %s — %s',
                    $ticket->ticket_code,
                    $ticket->title
                ),
                'created_by' => $assignedBy->id,
            ]);

            ActivityLogger::log(
                module: 'duty_delegation',
                action: 'create_from_operational_ticket',
                description: sprintf(
                    'Membuat delegasi tupoksi otomatis dari tiket %s untuk PIC %s',
                    $ticket->ticket_code,
                    $pic->name
                ),
                subject: $delegation,
                newValues: [
                    ...$delegation->fresh()->toArray(),
                    'ticket_code' => $ticket->ticket_code,
                    'resolution_score' => $resolution['score'] ?? 0,
                    'matched_keywords' => $resolution['matched_keywords'] ?? [],
                    'automatic' => true,
                ]
            );

            return [
                'delegation' => $delegation,
                'created' => true,
                'reused' => false,
                'source' => 'automatic_ticket',
            ];
        });
    }
}