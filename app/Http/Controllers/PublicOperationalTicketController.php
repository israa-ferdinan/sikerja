<?php

namespace App\Http\Controllers;

use App\Models\OperationalTicket;
use App\Models\OperationalTicketNote;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicOperationalTicketController extends Controller
{
    public function create()
    {
        return view('operations.public.tickets.create', [
            'categoryOptions' => OperationalTicket::categoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $isZoomRequest = $request->input('category') === OperationalTicket::CATEGORY_ZOOM;

        $validated = $request->validate([
            'requester_name' => ['required', 'string', 'max:255'],

            'requester_contact' => [
                $isZoomRequest ? 'nullable' : 'required',
                'string',
                'max:50',
            ],

            'requester_unit' => [
                $isZoomRequest ? 'required' : 'nullable',
                'string',
                'max:255',
            ],

            'category' => [
                'required',
                'string',
                Rule::in(array_keys(OperationalTicket::categoryOptions())),
            ],

            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1500'],

            'event_time' => [
                $isZoomRequest ? 'required' : 'nullable',
                'date',
            ],

            'participant_capacity' => [
                $isZoomRequest ? 'required' : 'nullable',
                'integer',
                'min:1',
                'max:10000',
            ],
        ], [
            'requester_name.required' => 'Nama pemohon wajib diisi.',
            'requester_contact.required' => 'Kontak/WhatsApp wajib diisi agar petugas bisa menghubungi pemohon.',
            'requester_unit.required' => 'Unit/Bagian wajib diisi untuk permintaan Zoom.',
            'category.required' => 'Jenis permintaan wajib dipilih.',
            'category.in' => 'Jenis permintaan tidak valid.',
            'title.required' => $isZoomRequest
                ? 'Nama kegiatan wajib diisi.'
                : 'Keluhan atau permintaan singkat wajib diisi.',
            'event_time.required' => 'Waktu kegiatan wajib diisi untuk permintaan Zoom.',
            'event_time.date' => 'Format waktu kegiatan tidak valid.',
            'participant_capacity.required' => 'Kapasitas peserta wajib diisi untuk permintaan Zoom.',
            'participant_capacity.integer' => 'Kapasitas peserta harus berupa angka.',
            'participant_capacity.min' => 'Kapasitas peserta minimal 1 orang.',
        ]);

        $description = $validated['description'] ?? null;

        if ($isZoomRequest) {
            $descriptionLines = [
                'Waktu Kegiatan: ' . $validated['event_time'],
                'Kapasitas Peserta: ' . $validated['participant_capacity'] . ' orang',
            ];

            if (filled($description)) {
                $descriptionLines[] = '';
                $descriptionLines[] = 'Catatan Tambahan:';
                $descriptionLines[] = $description;
            }

            $description = implode("\n", $descriptionLines);
        }

        $ticket = OperationalTicket::create([
            'source' => OperationalTicket::SOURCE_PUBLIC,
            'requester_name' => $validated['requester_name'],
            'requester_contact' => $validated['requester_contact'] ?? null,
            'requester_unit' => $validated['requester_unit'] ?? null,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $description,
            'priority' => OperationalTicket::PRIORITY_NORMAL,
            'status' => OperationalTicket::STATUS_BARU,
            'unit_id' => null,
            'created_by_user_id' => null,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Create Public Operational Ticket',
            'Pemohon membuat tiket public: ' . $ticket->ticket_code . ' - ' . $ticket->title,
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
            ]
        );

        return redirect()->route('public.tickets.success', [
            'ticket' => $ticket->ticket_code,
            'token' => $ticket->public_token,
        ]);
    }

    public function success(Request $request, OperationalTicket $ticket)
    {
        abort_unless(
            hash_equals((string) $ticket->public_token, (string) $request->query('token')),
            404
        );

        return view('operations.public.tickets.success', [
            'ticket' => $ticket,
            'trackingUrl' => route('public.tickets.show-tracking', [
                'ticket' => $ticket->ticket_code,
                'token' => $ticket->public_token,
            ]),
        ]);
    }

    public function trackForm()
    {
        return view('operations.public.tickets.track');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'ticket_code' => ['required', 'string', 'max:40'],
        ], [
            'ticket_code.required' => 'Kode tiket wajib diisi.',
        ]);

        $ticket = OperationalTicket::where('ticket_code', strtoupper(trim($validated['ticket_code'])))
            ->first();

        if (! $ticket) {
            return back()
                ->withInput()
                ->withErrors([
                    'ticket_code' => 'Kode tiket tidak ditemukan.',
                ]);
        }

        return redirect()->route('public.tickets.show-tracking', [
            'ticket' => $ticket->ticket_code,
            'token' => $ticket->public_token,
        ]);
    }

    public function showTracking(OperationalTicket $ticket, string $token)
    {
        abort_unless(
            hash_equals(
                (string) $ticket->public_token,
                (string) $token
            ),
            404
        );

        $ticket->forceFill([
            'last_public_viewed_at' => now(),
        ])->save();

        ActivityLogger::log(
            'Operasional SIM/TI',
            'View Public Operational Ticket Tracking',
            'Pemohon membuka tracking tiket: ' . $ticket->ticket_code,
            $ticket,
            null,
            [
                'ticket_code' => $ticket->ticket_code,
                'source' => $ticket->source,
                'status' => $ticket->status,
                'last_public_viewed_at' => $ticket->last_public_viewed_at,
            ]
        );

        $ticket->load([
            'notes' => function ($query) {
                $query
                    ->where(
                        'visibility',
                        OperationalTicketNote::VISIBILITY_PUBLIC
                    )
                    ->with('createdByUser')
                    ->oldest();
            },
        ]);

        return view('operations.public.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function kiosk()
    {
        $activeStatuses = [
            OperationalTicket::STATUS_BARU,
            OperationalTicket::STATUS_DIPROSES,
            OperationalTicket::STATUS_MENUNGGU_PEMOHON,
        ];

        $summary = [
            'baru' => OperationalTicket::query()
                ->where('status', OperationalTicket::STATUS_BARU)
                ->count(),

            'diproses' => OperationalTicket::query()
                ->where('status', OperationalTicket::STATUS_DIPROSES)
                ->count(),

            'menunggu_pemohon' => OperationalTicket::query()
                ->where('status', OperationalTicket::STATUS_MENUNGGU_PEMOHON)
                ->count(),

            'selesai_hari_ini' => OperationalTicket::query()
                ->where('status', OperationalTicket::STATUS_SELESAI)
                ->whereDate('closed_at', today())
                ->count(),
        ];

        $tickets = OperationalTicket::query()
            ->where(function ($query) use ($activeStatuses) {
                $query->whereIn('status', $activeStatuses)
                    ->orWhere(function ($doneQuery) {
                        $doneQuery
                            ->where('status', OperationalTicket::STATUS_SELESAI)
                            ->whereDate('closed_at', today());
                    });
            })
            ->latest('updated_at')
            ->latest()
            ->limit(30)
            ->get();

        return view('operations.public.tickets.kiosk', [
            'tickets' => $tickets,
            'summary' => $summary,
            'generatedAt' => now(),
        ]);
    }
}