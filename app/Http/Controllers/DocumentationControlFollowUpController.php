<?php

namespace App\Http\Controllers;

use App\Models\ControlFollowUp;
use App\Models\EvaluationRecord;
use App\Models\Unit;
use App\Models\User;
use App\Models\ControlLetter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogger;
use App\Services\AppNotifier;

class DocumentationControlFollowUpController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ControlFollowUp::query()
            ->with(['unit', 'evaluationRecord', 'picUser.employee'])
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            $query->where('unit_id', $unitId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('unit_id') && $user->isAdmin()) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('pic_user_id')) {
            $query->where('pic_user_id', $request->pic_user_id);
        }

        if ($request->filled('evaluation_record_id')) {
            if ($request->evaluation_record_id === 'none') {
                $query->whereNull('evaluation_record_id');
            } else {
                $query->where('evaluation_record_id', $request->evaluation_record_id);
            }
        }

        if ($request->filled('due_from')) {
            $query->whereDate('due_date', '>=', $request->due_from);
        }

        if ($request->filled('due_to')) {
            $query->whereDate('due_date', '<=', $request->due_to);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('recommendation', 'like', "%{$search}%")
                    ->orWhere('progress_note', 'like', "%{$search}%");
            });
        }

        $followUps = $query->paginate(10)->withQueryString();

        if ($user->isAdmin()) {
            $units = Unit::query()
                ->orderBy('name')
                ->get();

            $picUsers = User::query()
                ->with('employee')
                ->whereHas('employee')
                ->orderBy('name')
                ->get();

            $evaluationRecords = EvaluationRecord::query()
                ->where('status', 'published')
                ->latest('evaluation_date')
                ->get();
        } else {
            $unitId = $user->employee?->unit_id;

            $units = collect();

            $picUsers = User::query()
                ->with('employee')
                ->whereHas('employee', function ($query) use ($unitId) {
                    $query->where('unit_id', $unitId);
                })
                ->orderBy('name')
                ->get();

            $evaluationRecords = EvaluationRecord::query()
                ->where('unit_id', $unitId)
                ->where('status', 'published')
                ->latest('evaluation_date')
                ->get();
        }

        return view('documentation.control.follow-ups.index', [
            'followUps' => $followUps,
            'units' => $units,
            'picUsers' => $picUsers,
            'evaluationRecords' => $evaluationRecords,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        abort_if($user->role?->name === 'pegawai', 403, 'Pegawai tidak dapat membuat tindak lanjut evaluasi.');

        $unitId = null;

        if ($user->isAdmin()) {
            $units = Unit::query()
                ->orderBy('name')
                ->get();
        } else {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            $units = Unit::query()
                ->where('id', $unitId)
                ->get();
        }

        $evaluationRecords = EvaluationRecord::query()
            ->when(! $user->isAdmin(), function ($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->where('status', 'published')
            ->latest('evaluation_date')
            ->get();

        $picUsers = User::query()
            ->with('employee')
            ->whereHas('employee', function ($query) use ($user, $unitId) {
                if (! $user->isAdmin()) {
                    $query->where('unit_id', $unitId);
                }
            })
            ->whereHas('role', function ($query) {
                $query->whereIn('name', ['pegawai', 'gkm', 'kanit']);
            })
            ->orderBy('name')
            ->get();

        $selectedEvaluationRecord = null;

        if ($request->filled('evaluation_record_id')) {
            $selectedEvaluationRecord = EvaluationRecord::query()
                ->whereKey($request->evaluation_record_id)
                ->where('status', 'published')
                ->firstOrFail();

            if (! $user->isAdmin()) {
                abort_if(
                    (int) $selectedEvaluationRecord->unit_id !== (int) $user->employee?->unit_id,
                    403,
                    'Anda tidak memiliki akses ke hasil evaluasi ini.'
                );
            }

            $hasCompletedFollowUp = ControlFollowUp::query()
                ->where('evaluation_record_id', $selectedEvaluationRecord->id)
                ->where('status', ControlFollowUp::STATUS_DONE)
                ->exists();

            abort_if(
                $hasCompletedFollowUp,
                422,
                'Hasil evaluasi ini sudah memiliki tindak lanjut yang selesai.'
            );
        }

        return view('documentation.control.follow-ups.create', [
            'units' => $units,
            'evaluationRecords' => $evaluationRecords,
            'picUsers' => $picUsers,
            'selectedEvaluationRecord' => $selectedEvaluationRecord,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_if($user->role?->name === 'pegawai', 403, 'Pegawai tidak dapat membuat tindak lanjut evaluasi.');

        $userUnitId = $user->employee?->unit_id;

        if (! $user->isAdmin()) {
            abort_if(blank($userUnitId), 403, 'User belum terhubung dengan unit pegawai.');
        }

        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::when(! $user->isAdmin(), Rule::in([$userUnitId])),
            ],
            'evaluation_record_id' => [
                'nullable',
                'exists:evaluation_records,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'recommendation' => [
                'nullable',
                'string',
            ],
            'pic_user_id' => [
                'nullable',
                'exists:users,id',
            ],
            'due_date' => [
                'nullable',
                'date',
            ],
            'progress_note' => [
                'nullable',
                'string',
            ],
        ]);

        if (! empty($validated['evaluation_record_id'])) {
            $evaluationRecord = EvaluationRecord::query()
                ->whereKey($validated['evaluation_record_id'])
                ->firstOrFail();

            abort_if(
                (int) $evaluationRecord->unit_id !== (int) $validated['unit_id'],
                422,
                'Hasil evaluasi tidak sesuai dengan unit tindak lanjut.'
            );

            abort_if(
                $evaluationRecord->status !== 'published',
                422,
                'Tindak lanjut hanya bisa dibuat dari hasil evaluasi yang sudah Published.'
            );
        }

        if (! empty($validated['pic_user_id'])) {
            $picUser = User::query()
                ->with('employee')
                ->whereKey($validated['pic_user_id'])
                ->firstOrFail();

            abort_if(
                (int) $picUser->employee?->unit_id !== (int) $validated['unit_id'],
                422,
                'PIC harus berasal dari unit yang sama.'
            );
        }

        $followUp = ControlFollowUp::create([
            'evaluation_record_id' => $validated['evaluation_record_id'] ?? null,
            'unit_id' => $validated['unit_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'recommendation' => $validated['recommendation'] ?? null,
            'pic_user_id' => $validated['pic_user_id'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => ControlFollowUp::STATUS_OPEN,
            'progress_note' => $validated['progress_note'] ?? null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        ActivityLogger::log(
            'Pengendalian',
            'Create Control Follow Up',
            'Membuat tindak lanjut evaluasi: ' . $followUp->title
        );

        if (filled($followUp->pic_user_id)) {
            AppNotifier::notifyUser(
                user: (int) $followUp->pic_user_id,
                module: 'Pengendalian',
                title: 'Anda ditunjuk sebagai PIC tindak lanjut evaluasi',
                message: 'Anda ditunjuk sebagai PIC untuk tindak lanjut evaluasi: ' . $followUp->title,
                url: route('documentation.control.follow-ups.show', $followUp),
                data: [
                    'control_follow_up_id' => $followUp->id,
                    'assigned_by_user_id' => $user->id,
                    'type' => 'assigned',
                ],
            );
        }

        return redirect()
            ->route('documentation.control.follow-ups.index')
            ->with('success', 'Tindak lanjut evaluasi berhasil dibuat.');
    }

    public function show(Request $request, ControlFollowUp $followUp)
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            abort_if(
                (int) $followUp->unit_id !== (int) $unitId,
                403,
                'Anda tidak memiliki akses ke tindak lanjut ini.'
            );
        }

        $followUp->load([
            'unit',
            'evaluationRecord',
            'picUser.employee',
            'creator.employee',
            'updater.employee',
            'letters' => function ($query) use ($user) {
                $query
                    ->with(['uploader.employee'])
                    ->latest();

                if ($user->role?->name === 'pegawai') {
                    $query->where('visibility', \App\Models\ControlLetter::VISIBILITY_UNIT);
                }
            },
        ]);

        return view('documentation.control.follow-ups.show', [
            'followUp' => $followUp,
            'isPic' => (int) $followUp->pic_user_id === (int) $user->id,
        ]);
    }

    public function edit(Request $request, ControlFollowUp $followUp)
    {
        $this->ensureCanManageFollowUp($request, $followUp);
        $this->ensureFollowUpIsNotDone($followUp);

        $user = $request->user();

        if ($user->isAdmin()) {
            $units = Unit::query()
                ->orderBy('name')
                ->get();

            $evaluationRecords = EvaluationRecord::query()
                ->where('status', 'published')
                ->latest('evaluation_date')
                ->get();

            $picUsers = User::query()
                ->with('employee')
                ->whereHas('employee')
                ->whereHas('role', function ($query) {
                    $query->whereIn('name', ['pegawai', 'gkm', 'kanit']);
                })
                ->orderBy('name')
                ->get();
        } else {
            $unitId = $user->employee?->unit_id;

            $units = Unit::query()
                ->where('id', $unitId)
                ->get();

            $evaluationRecords = EvaluationRecord::query()
                ->where('unit_id', $unitId)
                ->where('status', 'published')
                ->latest('evaluation_date')
                ->get();

            $picUsers = User::query()
                ->with('employee')
                ->whereHas('employee', function ($query) use ($unitId) {
                    $query->where('unit_id', $unitId);
                })
                ->whereHas('role', function ($query) {
                    $query->whereIn('name', ['pegawai', 'gkm', 'kanit']);
                })
                ->orderBy('name')
                ->get();
        }

        return view('documentation.control.follow-ups.edit', [
            'followUp' => $followUp,
            'units' => $units,
            'evaluationRecords' => $evaluationRecords,
            'picUsers' => $picUsers,
        ]);
    }

    public function update(Request $request, ControlFollowUp $followUp)
    {
        $this->ensureCanManageFollowUp($request, $followUp);
        $this->ensureFollowUpIsNotDone($followUp);

        $user = $request->user();
        $userUnitId = $user->employee?->unit_id;

        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::when(! $user->isAdmin(), Rule::in([$userUnitId])),
            ],
            'evaluation_record_id' => [
                'nullable',
                'exists:evaluation_records,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'recommendation' => [
                'nullable',
                'string',
            ],
            'pic_user_id' => [
                'nullable',
                'exists:users,id',
            ],
            'due_date' => [
                'nullable',
                'date',
            ],
            'progress_note' => [
                'nullable',
                'string',
            ],
        ]);

        if (! empty($validated['evaluation_record_id'])) {
            $evaluationRecord = EvaluationRecord::query()
                ->whereKey($validated['evaluation_record_id'])
                ->firstOrFail();

            abort_if(
                (int) $evaluationRecord->unit_id !== (int) $validated['unit_id'],
                422,
                'Hasil evaluasi tidak sesuai dengan unit tindak lanjut.'
            );

            abort_if(
                $evaluationRecord->status !== 'published',
                422,
                'Tindak lanjut hanya bisa dikaitkan dengan hasil evaluasi yang sudah Published.'
            );
        }

        if (! empty($validated['pic_user_id'])) {
            $picUser = User::query()
                ->with('employee')
                ->whereKey($validated['pic_user_id'])
                ->firstOrFail();

            abort_if(
                (int) $picUser->employee?->unit_id !== (int) $validated['unit_id'],
                422,
                'PIC harus berasal dari unit yang sama.'
            );
        }

        $oldPicUserId = $followUp->pic_user_id;
        $newPicUserId = $validated['pic_user_id'] ?? null;

        $followUp->update([
            'evaluation_record_id' => $validated['evaluation_record_id'] ?? null,
            'unit_id' => $validated['unit_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'recommendation' => $validated['recommendation'] ?? null,
            'pic_user_id' => $newPicUserId,
            'due_date' => $validated['due_date'] ?? null,
            'progress_note' => $validated['progress_note'] ?? null,
            'updated_by' => $user->id,
        ]);

        $followUp->refresh();

        ActivityLogger::log(
            'Pengendalian',
            'Update Control Follow Up',
            'Memperbarui tindak lanjut evaluasi: ' . $followUp->title
        );

        $picChanged = (int) $newPicUserId !== (int) $oldPicUserId;

        if ($picChanged) {
            if (filled($newPicUserId)) {
                AppNotifier::notifyUser(
                    user: (int) $newPicUserId,
                    module: 'Pengendalian',
                    title: 'Anda ditunjuk sebagai PIC tindak lanjut evaluasi',
                    message: 'Anda ditunjuk sebagai PIC untuk tindak lanjut evaluasi: ' . $followUp->title,
                    url: route('documentation.control.follow-ups.show', $followUp),
                    data: [
                        'control_follow_up_id' => $followUp->id,
                        'assigned_by_user_id' => $user->id,
                        'type' => 'assigned',
                    ],
                );
            }

            if (filled($oldPicUserId)) {
                AppNotifier::notifyUser(
                    user: (int) $oldPicUserId,
                    module: 'Pengendalian',
                    title: 'Penugasan PIC tindak lanjut evaluasi diperbarui',
                    message: 'Anda sudah tidak menjadi PIC untuk tindak lanjut evaluasi: ' . $followUp->title,
                    url: route('documentation.control.follow-ups.show', $followUp),
                    data: [
                        'control_follow_up_id' => $followUp->id,
                        'updated_by_user_id' => $user->id,
                        'type' => 'unassigned',
                    ],
                );
            }
        }

        return redirect()
            ->route('documentation.control.follow-ups.show', $followUp)
            ->with('success', 'Tindak lanjut evaluasi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, ControlFollowUp $followUp)
    {
        $this->ensureCanManageFollowUp($request, $followUp);
        $this->ensureFollowUpIsNotDone($followUp);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    ControlFollowUp::STATUS_OPEN,
                    ControlFollowUp::STATUS_IN_PROGRESS,
                    ControlFollowUp::STATUS_DONE,
                    ControlFollowUp::STATUS_CANCELLED,
                ]),
            ],
            'progress_note' => [
                'nullable',
                'string',
            ],
            'completed_note' => [
                'nullable',
                'string',
                'required_if:status,' . ControlFollowUp::STATUS_DONE,
            ],
            'cancelled_note' => [
                'nullable',
                'string',
                'required_if:status,' . ControlFollowUp::STATUS_CANCELLED,
            ],
        ]);

        $status = $validated['status'];

        $data = [
            'status' => $status,
            'progress_note' => $validated['progress_note'] ?? $followUp->progress_note,
            'updated_by' => $request->user()->id,
        ];

        if ($status === ControlFollowUp::STATUS_DONE) {
            $data['completed_note'] = $validated['completed_note'];
            $data['completed_at'] = now();
            $data['cancelled_note'] = null;
        } elseif ($status === ControlFollowUp::STATUS_CANCELLED) {
            $data['cancelled_note'] = $validated['cancelled_note'];
            $data['completed_note'] = null;
            $data['completed_at'] = null;
        } else {
            $data['completed_note'] = null;
            $data['completed_at'] = null;
            $data['cancelled_note'] = null;
        }

        $followUp->forceFill($data)->save();

        $followUp->refresh();

        ActivityLogger::log(
            'Pengendalian',
            'Update Control Follow Up Status',
            'Memperbarui status tindak lanjut evaluasi menjadi ' . $followUp->statusLabel() . ': ' . $followUp->title
        );

        return redirect()
            ->route('documentation.control.follow-ups.show', $followUp)
            ->with('success', 'Status tindak lanjut berhasil diperbarui.');
    }

    public function updateProgress(Request $request, ControlFollowUp $followUp)
    {
        $this->ensureCanUpdateProgress($request, $followUp);

        abort_if(
            in_array($followUp->status, [
                ControlFollowUp::STATUS_DONE,
                ControlFollowUp::STATUS_CANCELLED,
            ], true),
            422,
            'Progres tidak dapat diperbarui karena tindak lanjut sudah selesai atau dibatalkan.'
        );

        $validated = $request->validate([
            'progress_note' => [
                'required',
                'string',
            ],
        ]);

        $followUp->update([
            'progress_note' => $validated['progress_note'],
            'status' => $followUp->status === ControlFollowUp::STATUS_OPEN
                ? ControlFollowUp::STATUS_IN_PROGRESS
                : $followUp->status,
            'updated_by' => $request->user()->id,
        ]);

        ActivityLogger::log(
            'Pengendalian',
            'Update Control Follow Up Progress',
            'Memperbarui progres tindak lanjut evaluasi: ' . $followUp->title
        );

        return redirect()
            ->route('documentation.control.follow-ups.show', $followUp)
            ->with('success', 'Catatan progres berhasil diperbarui.');
    }

    public function storeLetter(Request $request, ControlFollowUp $followUp)
    {
        $user = $request->user();
        $this->ensureFollowUpIsNotDone($followUp);

        abort_if(
            $user->role?->name === 'pegawai',
            403,
            'Pegawai tidak dapat upload surat resmi pengendalian.'
        );

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            abort_if(
                (int) $followUp->unit_id !== (int) $unitId,
                403,
                'Anda tidak memiliki akses ke tindak lanjut ini.'
            );
        }

        $validated = $request->validate([
            'letter_type' => [
                'required',
                Rule::in([
                    ControlLetter::TYPE_INCOMING,
                    ControlLetter::TYPE_OUTGOING,
                ]),
            ],
            'letter_number' => [
                'nullable',
                'string',
                'max:255',
            ],
            'letter_date' => [
                'nullable',
                'date',
            ],
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'sender' => [
                'nullable',
                'string',
                'max:255',
            ],
            'recipient' => [
                'nullable',
                'string',
                'max:255',
            ],
            'summary' => [
                'nullable',
                'string',
            ],
            'visibility' => [
                'required',
                Rule::in([
                    ControlLetter::VISIBILITY_UNIT,
                    ControlLetter::VISIBILITY_RESTRICTED,
                ]),
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
                'max:10240',
            ],
        ]);

        $file = $request->file('file');

        $path = $file->store('control/letters', 'local');

        $letter = ControlLetter::create([
            'control_follow_up_id' => $followUp->id,
            'unit_id' => $followUp->unit_id,
            'letter_type' => $validated['letter_type'],
            'letter_number' => $validated['letter_number'] ?? null,
            'letter_date' => $validated['letter_date'] ?? null,
            'subject' => $validated['subject'],
            'sender' => $validated['sender'] ?? null,
            'recipient' => $validated['recipient'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'visibility' => $validated['visibility'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $user->id,
        ]);

        ActivityLogger::log(
            'Pengendalian',
            'Upload Control Letter',
            'Upload surat pengendalian dari tindak lanjut: ' . $letter->subject
        );

        return redirect()
            ->route('documentation.control.follow-ups.show', $followUp)
            ->with('success', 'Surat pengendalian berhasil ditambahkan.');
    }

    public function destroy(Request $request, ControlFollowUp $followUp)
    {
        $this->ensureCanManageFollowUp($request, $followUp);

        abort_if(
            ! in_array($followUp->status, [
                ControlFollowUp::STATUS_OPEN,
                ControlFollowUp::STATUS_CANCELLED,
            ], true),
            422,
            'Tindak lanjut hanya dapat dihapus jika statusnya Open atau Dibatalkan.'
        );
        $title = $followUp->title;

        $followUp->delete();

        ActivityLogger::log(
            'Pengendalian',
            'Delete Control Follow Up',
            'Menghapus tindak lanjut evaluasi: ' . $title
        );

        return redirect()
            ->route('documentation.control.follow-ups.index')
            ->with('success', 'Tindak lanjut evaluasi berhasil dihapus.');
    }

    private function ensureCanManageFollowUp(Request $request, ControlFollowUp $followUp): void
    {
        $user = $request->user();

        abort_if(
            $user->role?->name === 'pegawai',
            403,
            'Pegawai tidak dapat mengelola tindak lanjut evaluasi.'
        );

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            abort_if(
                (int) $followUp->unit_id !== (int) $unitId,
                403,
                'Anda tidak memiliki akses ke tindak lanjut ini.'
            );
        }
    }

    private function ensureCanUpdateProgress(Request $request, ControlFollowUp $followUp): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        $unitId = $user->employee?->unit_id;

        abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

        abort_if(
            (int) $followUp->unit_id !== (int) $unitId,
            403,
            'Anda tidak memiliki akses ke tindak lanjut ini.'
        );

        if (in_array($user->role?->name, ['kanit', 'gkm'], true)) {
            return;
        }

        abort_if(
            $user->role?->name !== 'pegawai' || (int) $followUp->pic_user_id !== (int) $user->id,
            403,
            'Anda hanya dapat memperbarui progres tindak lanjut yang ditugaskan kepada Anda.'
        );
    }

    private function ensureFollowUpIsNotDone(ControlFollowUp $followUp): void
    {
        abort_if(
            $followUp->status === ControlFollowUp::STATUS_DONE,
            422,
            'Tindak lanjut yang sudah selesai tidak dapat diubah lagi.'
        );
    }
}