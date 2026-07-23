<?php

namespace App\Http\Controllers;

use App\Models\ControlLetter;
use App\Models\ControlFollowUp;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogger;

class DocumentationControlLetterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ControlLetter::query()
            ->with(['unit', 'followUp', 'uploader.employee'])
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            $query->where('unit_id', $unitId);
        }

        if ($user->role?->name === 'pegawai') {
            $query->where('visibility', ControlLetter::VISIBILITY_UNIT);
        }

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }

        if ($request->filled('visibility') && $user->role?->name !== 'pegawai') {
            $query->where('visibility', $request->visibility);
        }

        if ($request->filled('unit_id') && $user->isAdmin()) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('letter_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('letter_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('subject', 'like', "%{$search}%")
                    ->orWhere('letter_number', 'like', "%{$search}%")
                    ->orWhere('sender', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $letters = $query->paginate(10)->withQueryString();

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('documentation.control.letters.index', [
            'letters' => $letters,
            'units' => $units,
        ]);
    }

    public function download(Request $request, ControlLetter $letter)
    {
        $this->ensureCanViewLetter($request, $letter);

        abort_if(
            blank($letter->file_path) || ! Storage::disk('local')->exists($letter->file_path),
            404,
            'File surat tidak ditemukan.'
        );

        ActivityLogger::log(
            'Pengendalian',
            'Download Control Letter',
            'Download surat pengendalian: ' . $letter->subject
        );

        return Storage::disk('local')->download(
            $letter->file_path,
            $letter->original_name
        );
    }

    public function create(Request $request)
    {
        $user = $request->user();

        abort_if(
            $user->role?->name === 'pegawai',
            403,
            'Pegawai tidak dapat upload surat resmi pengendalian.'
        );

        if ($user->isAdmin()) {
            $units = Unit::query()
                ->orderBy('name')
                ->get();

            $followUps = ControlFollowUp::query()
                ->with('unit')
                ->latest()
                ->get();
        } else {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

            $units = Unit::query()
                ->where('id', $unitId)
                ->get();

            $followUps = ControlFollowUp::query()
                ->with('unit')
                ->where('unit_id', $unitId)
                ->latest()
                ->get();
        }

        return view('documentation.control.letters.create', [
            'units' => $units,
            'followUps' => $followUps,
        ]);
    }

    public function show(Request $request, ControlLetter $letter)
    {
        $this->ensureCanViewLetter($request, $letter);

        $letter->load([
            'unit',
            'followUp.unit',
            'followUp.evaluationRecord',
            'uploader.employee',
        ]);

        return view('documentation.control.letters.show', [
            'letter' => $letter,
        ]);
    }

    public function edit(Request $request, ControlLetter $letter)
    {
        $this->ensureCanManageLetter($request, $letter);

        $user = $request->user();

        if ($user->isAdmin()) {
            $units = Unit::query()
                ->orderBy('name')
                ->get();

            $followUps = ControlFollowUp::query()
                ->with('unit')
                ->latest()
                ->get();
        } else {
            $unitId = $user->employee?->unit_id;

            $units = Unit::query()
                ->where('id', $unitId)
                ->get();

            $followUps = ControlFollowUp::query()
                ->with('unit')
                ->where('unit_id', $unitId)
                ->latest()
                ->get();
        }

        return view('documentation.control.letters.edit', [
            'letter' => $letter,
            'units' => $units,
            'followUps' => $followUps,
        ]);
    }

    public function update(Request $request, ControlLetter $letter)
    {
        $this->ensureCanManageLetter($request, $letter);

        $user = $request->user();
        $userUnitId = $user->employee?->unit_id;

        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::when(! $user->isAdmin(), Rule::in([$userUnitId])),
            ],
            'control_follow_up_id' => [
                'nullable',
                'exists:control_follow_ups,id',
            ],
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
        ]);

        if (! empty($validated['control_follow_up_id'])) {
            $followUp = ControlFollowUp::query()
                ->whereKey($validated['control_follow_up_id'])
                ->firstOrFail();

            abort_if(
                (int) $followUp->unit_id !== (int) $validated['unit_id'],
                422,
                'Tindak lanjut tidak sesuai dengan unit surat.'
            );
        }

        $letter->update([
            'control_follow_up_id' => $validated['control_follow_up_id'] ?? null,
            'unit_id' => $validated['unit_id'],
            'letter_type' => $validated['letter_type'],
            'letter_number' => $validated['letter_number'] ?? null,
            'letter_date' => $validated['letter_date'] ?? null,
            'subject' => $validated['subject'],
            'sender' => $validated['sender'] ?? null,
            'recipient' => $validated['recipient'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'visibility' => $validated['visibility'],
        ]);

        ActivityLogger::log(
            'Pengendalian',
            'Update Control Letter',
            'Memperbarui metadata surat pengendalian: ' . $letter->subject
        );

        return redirect()
            ->route('documentation.control.letters.show', $letter)
            ->with('success', 'Metadata surat pengendalian berhasil diperbarui.');
    }

    public function destroy(Request $request, ControlLetter $letter)
    {
        $this->ensureCanManageLetter($request, $letter);
        $subject = $letter->subject;

        if ($letter->file_path && Storage::disk('local')->exists($letter->file_path)) {
            Storage::disk('local')->delete($letter->file_path);
        }

        
        $letter->delete();

        ActivityLogger::log(
            'Pengendalian',
            'Delete Control Letter',
            'Menghapus surat pengendalian: ' . $subject
        );

        return redirect()
            ->route('documentation.control.letters.index')
            ->with('success', 'Surat pengendalian berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_if(
            $user->role?->name === 'pegawai',
            403,
            'Pegawai tidak dapat upload surat resmi pengendalian.'
        );

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
            'control_follow_up_id' => [
                'nullable',
                'exists:control_follow_ups,id',
            ],
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

        if (! empty($validated['control_follow_up_id'])) {
            $followUp = ControlFollowUp::query()
                ->whereKey($validated['control_follow_up_id'])
                ->firstOrFail();

            abort_if(
                (int) $followUp->unit_id !== (int) $validated['unit_id'],
                422,
                'Tindak lanjut tidak sesuai dengan unit surat.'
            );
        }

        $file = $request->file('file');

        $path = $file->store('control/letters', 'local');

        $letter = ControlLetter::create([
            'control_follow_up_id' => $validated['control_follow_up_id'] ?? null,
            'unit_id' => $validated['unit_id'],
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
            'Upload surat pengendalian manual: ' . $letter->subject
        );

        return redirect()
            ->route('documentation.control.letters.index')
            ->with('success', 'Surat pengendalian berhasil diupload.');
    }

    private function ensureCanViewLetter(Request $request, ControlLetter $letter): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        $unitId = $user->employee?->unit_id;

        abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

        abort_if(
            (int) $letter->unit_id !== (int) $unitId,
            403,
            'Anda tidak memiliki akses ke surat ini.'
        );

        if ($user->role?->name === 'pegawai') {
            abort_if(
                $letter->visibility !== ControlLetter::VISIBILITY_UNIT,
                403,
                'Anda tidak memiliki akses ke surat terbatas.'
            );
        }
    }

    private function ensureCanManageLetter(Request $request, ControlLetter $letter): void
    {
        $user = $request->user();

        abort_if(
            $user->role?->name === 'pegawai',
            403,
            'Pegawai tidak dapat mengelola surat pengendalian.'
        );

        if ($user->isAdmin()) {
            return;
        }

        $unitId = $user->employee?->unit_id;

        abort_if(blank($unitId), 403, 'User belum terhubung dengan unit pegawai.');

        abort_if(
            (int) $letter->unit_id !== (int) $unitId,
            403,
            'Anda tidak memiliki akses ke surat ini.'
        );
    }
}