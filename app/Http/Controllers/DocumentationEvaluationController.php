<?php

namespace App\Http\Controllers;

use App\Models\EvaluationRecord;
use App\Models\Unit;
use App\Models\EvaluationDocument;

use App\Services\ActivityLogger;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class DocumentationEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = EvaluationRecord::query()
            ->with(['unit', 'creator'])
            ->withCount('documents')
            ->latest('evaluation_date')
            ->latest()
            ->withCount([
                'controlFollowUps',
                'controlFollowUps as control_follow_ups_open_count' => function ($query) {
                    $query->where('status', \App\Models\ControlFollowUp::STATUS_OPEN);
                },
                'controlFollowUps as control_follow_ups_in_progress_count' => function ($query) {
                    $query->where('status', \App\Models\ControlFollowUp::STATUS_IN_PROGRESS);
                },
                'controlFollowUps as control_follow_ups_done_count' => function ($query) {
                    $query->where('status', \App\Models\ControlFollowUp::STATUS_DONE);
                },
                'controlFollowUps as control_follow_ups_cancelled_count' => function ($query) {
                    $query->where('status', \App\Models\ControlFollowUp::STATUS_CANCELLED);
                },
            ]);

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'Akun belum terhubung dengan data pegawai/unit.');

            $query->where('unit_id', $unitId);

            if (! $user->isKanit() && $user->role?->name !== 'gkm') {
                $query->where('status', EvaluationRecord::STATUS_PUBLISHED);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhere('findings', 'like', "%{$search}%")
                    ->orWhere('recommendation', 'like', "%{$search}%");
            });
        }

        if ($request->filled('evaluation_type')) {
            $query->where('evaluation_type', $request->evaluation_type);
        }

        if ($request->filled('status')) {
            if ($user->isAdmin() || $user->isKanit() || $user->role?->name === 'gkm') {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('unit_id') && $user->isAdmin()) {
            $query->where('unit_id', $request->unit_id);
        }

        $summaryQuery = EvaluationRecord::query();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'Akun belum terhubung dengan data pegawai/unit.');

            $summaryQuery->where('unit_id', $unitId);

            if (! $user->isKanit() && $user->role?->name !== 'gkm') {
                $summaryQuery->where('status', EvaluationRecord::STATUS_PUBLISHED);
            }
        }

        if ($request->filled('unit_id') && $user->isAdmin()) {
            $summaryQuery->where('unit_id', $request->unit_id);
        }

        if ($request->filled('follow_up_status')) {
            if ($request->follow_up_status === 'none') {
                $query->doesntHave('controlFollowUps');
            }

            if ($request->follow_up_status === 'open') {
                $query->whereHas('controlFollowUps', function ($subQuery) {
                    $subQuery->where('status', \App\Models\ControlFollowUp::STATUS_OPEN);
                });
            }

            if ($request->follow_up_status === 'in_progress') {
                $query->whereHas('controlFollowUps', function ($subQuery) {
                    $subQuery->where('status', \App\Models\ControlFollowUp::STATUS_IN_PROGRESS);
                });
            }

            if ($request->follow_up_status === 'done') {
                $query->whereHas('controlFollowUps', function ($subQuery) {
                    $subQuery->where('status', \App\Models\ControlFollowUp::STATUS_DONE);
                });
            }

            if ($request->follow_up_status === 'cancelled') {
                $query->whereHas('controlFollowUps', function ($subQuery) {
                    $subQuery->where('status', \App\Models\ControlFollowUp::STATUS_CANCELLED);
                });
            }
        }

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'draft' => (clone $summaryQuery)->where('status', EvaluationRecord::STATUS_DRAFT)->count(),
            'published' => (clone $summaryQuery)->where('status', EvaluationRecord::STATUS_PUBLISHED)->count(),
            'archived' => (clone $summaryQuery)->where('status', EvaluationRecord::STATUS_ARCHIVED)->count(),
            'documents' => EvaluationDocument::query()
                ->when(! $user->isAdmin(), function ($q) use ($user) {
                    $q->where('unit_id', $user->employee?->unit_id);

                    if (! $user->isKanit() && $user->role?->name !== 'gkm') {
                        $q->whereHas('evaluationRecord', function ($recordQuery) {
                            $recordQuery->where('status', EvaluationRecord::STATUS_PUBLISHED);
                        });
                    }
                })
                ->when($request->filled('unit_id') && $user->isAdmin(), function ($q) use ($request) {
                    $q->where('unit_id', $request->unit_id);
                })
                ->count(),
        ];

        $records = $query->paginate(10)->withQueryString();

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('documentation.evaluasi.index', [
            'records' => $records,
            'units' => $units,
            'summary' => $summary,
            'typeOptions' => EvaluationRecord::evaluationTypeOptions(),
            'statusOptions' => EvaluationRecord::statusOptions(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $user->isKanit() || $user->role?->name === 'gkm',
            403,
            'Anda tidak memiliki akses untuk membuat hasil evaluasi.'
        );

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('documentation.evaluasi.create', [
            'units' => $units,
            'typeOptions' => EvaluationRecord::evaluationTypeOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $user->isKanit() || $user->role?->name === 'gkm',
            403,
            'Anda tidak memiliki akses untuk menyimpan hasil evaluasi.'
        );

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf(fn () => $user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'title' => ['required', 'string', 'max:255'],
            'evaluation_type' => [
                'required',
                'string',
                Rule::in(array_keys(EvaluationRecord::evaluationTypeOptions())),
            ],
            'evaluation_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'findings' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
            'zoom_link' => ['nullable', 'url', 'max:255'],
            'google_drive_link' => ['nullable', 'url', 'max:255'],
        ]);

        if ($user->isAdmin()) {
            $unitId = $validated['unit_id'];
        } else {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'Akun belum terhubung dengan data pegawai/unit.');
        }

        $record = EvaluationRecord::create([
            'unit_id' => $unitId,
            'title' => $validated['title'],
            'evaluation_type' => $validated['evaluation_type'],
            'evaluation_date' => $validated['evaluation_date'] ?? null,
            'source' => $validated['source'] ?? null,
            'findings' => $validated['findings'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'status' => EvaluationRecord::STATUS_DRAFT,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'zoom_link' => $validated['zoom_link'] ?? null,
            'google_drive_link' => $validated['google_drive_link'] ?? null,
        ]);

        ActivityLogger::log(
            'evaluasi',
            'create',
            'Membuat hasil evaluasi: ' . $record->title,
            $record,
            null,
            $record->toArray()
        );

        return redirect()
            ->route('documentation.evaluasi.index')
            ->with('success', 'Hasil evaluasi berhasil dibuat.');
    }

    public function show(Request $request, EvaluationRecord $record)
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'Akun belum terhubung dengan data pegawai/unit.');
            abort_if((int) $record->unit_id !== (int) $unitId, 403, 'Anda tidak memiliki akses ke hasil evaluasi ini.');

            if (! $user->isKanit() && $user->role?->name !== 'gkm') {
                abort_unless(
                    $record->isPublished(),
                    403,
                    'Anda hanya dapat melihat hasil evaluasi yang sudah dipublish.'
                );
            }
        }

        $record->load([
            'unit',
            'documents',
            'creator.employee',
            'updater.employee',
            'controlFollowUps' => function ($query) {
                $query
                    ->with(['unit', 'picUser.employee'])
                    ->latest();
            },
        ]);

        return view('documentation.evaluasi.show', [
            'record' => $record,
        ]);
    }

    public function edit(Request $request, EvaluationRecord $record)
    {
        $this->authorizeManage($request, $record);

        abort_unless(
            $record->isDraft(),
            403,
            'Hanya hasil evaluasi berstatus Draft yang bisa diedit.'
        );

        $user = $request->user();

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('documentation.evaluasi.edit', [
            'record' => $record,
            'units' => $units,
            'typeOptions' => EvaluationRecord::evaluationTypeOptions(),
        ]);
    }

    public function update(Request $request, EvaluationRecord $record)
    {
        $this->authorizeManage($request, $record);

        abort_unless(
            $record->isDraft(),
            403,
            'Hanya hasil evaluasi berstatus Draft yang bisa diubah.'
        );

        $user = $request->user();

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf(fn () => $user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'title' => ['required', 'string', 'max:255'],
            'evaluation_type' => [
                'required',
                'string',
                Rule::in(array_keys(EvaluationRecord::evaluationTypeOptions())),
            ],
            'evaluation_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'findings' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
            'zoom_link' => ['nullable', 'url', 'max:255'],
            'google_drive_link' => ['nullable', 'url', 'max:255'],
        ]);

        $oldValues = $record->toArray();

        $unitId = $user->isAdmin()
            ? $validated['unit_id']
            : $record->unit_id;

        $record->update([
            'unit_id' => $unitId,
            'title' => $validated['title'],
            'evaluation_type' => $validated['evaluation_type'],
            'evaluation_date' => $validated['evaluation_date'] ?? null,
            'source' => $validated['source'] ?? null,
            'findings' => $validated['findings'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'updated_by' => $user->id,
            'zoom_link' => $validated['zoom_link'] ?? null,
            'google_drive_link' => $validated['google_drive_link'] ?? null,
        ]);

        ActivityLogger::log(
            'evaluasi',
            'update',
            'Mengubah hasil evaluasi: ' . $record->title,
            $record,
            $oldValues,
            $record->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.evaluasi.show', $record)
            ->with('success', 'Hasil evaluasi berhasil diperbarui.');
    }

    public function publish(Request $request, EvaluationRecord $record)
    {
        $this->authorizeManage($request, $record);

        abort_unless(
            $record->isDraft(),
            422,
            'Hanya hasil evaluasi berstatus Draft yang bisa dipublish.'
        );

        $missingFields = $record->publishMissingFields();

        abort_if(
            ! empty($missingFields),
            422,
            'Hasil evaluasi belum bisa dipublish. Lengkapi: ' . implode(', ', $missingFields) . '.'
        );

        $oldValues = $record->toArray();

        $record->update([
            'status' => EvaluationRecord::STATUS_PUBLISHED,
            'published_at' => now(),
            'archived_at' => null,
            'updated_by' => $request->user()->id,
        ]);

        ActivityLogger::log(
            'evaluasi',
            'publish',
            'Publish hasil evaluasi: ' . $record->title,
            $record,
            $oldValues,
            $record->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.evaluasi.show', $record)
            ->with('success', 'Hasil evaluasi berhasil dipublish.');
    }

    public function archive(Request $request, EvaluationRecord $record)
    {
        $this->authorizeManage($request, $record);

        abort_unless(
            $record->isPublished(),
            422,
            'Hanya hasil evaluasi berstatus Published yang bisa diarsipkan.'
        );

        $oldValues = $record->toArray();

        $record->update([
            'status' => EvaluationRecord::STATUS_ARCHIVED,
            'archived_at' => now(),
            'updated_by' => $request->user()->id,
        ]);

        ActivityLogger::log(
            'evaluasi',
            'archive',
            'Archive hasil evaluasi: ' . $record->title,
            $record,
            $oldValues,
            $record->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.evaluasi.show', $record)
            ->with('success', 'Hasil evaluasi berhasil diarsipkan.');
    }

    public function destroy(Request $request, EvaluationRecord $record)
    {
        $this->authorizeManage($request, $record);

        abort_unless(
            $record->isDraft(),
            422,
            'Hanya hasil evaluasi berstatus Draft yang bisa dihapus.'
        );

        $oldValues = $record->toArray();
        $title = $record->title;

        ActivityLogger::log(
            'evaluasi',
            'delete',
            'Menghapus hasil evaluasi draft: ' . $title,
            $record,
            $oldValues,
            null
        );

        $record->delete();

        return redirect()
            ->route('documentation.evaluasi.index')
            ->with('success', 'Hasil evaluasi draft berhasil dihapus.');
    }

    public function storeDocument(Request $request, EvaluationRecord $record)
    {
        $this->authorizeManage($request, $record);

        abort_unless(
            $record->isDraft(),
            403,
            'Dokumen hanya bisa ditambahkan pada hasil evaluasi berstatus Draft.'
        );

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => [
                'required',
                'string',
                Rule::in(array_keys(EvaluationDocument::documentTypeOptions())),
            ],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('file');

        $path = $file->store('evaluation-documents');

        $document = EvaluationDocument::create([
            'evaluation_record_id' => $record->id,
            'unit_id' => $record->unit_id,
            'title' => $validated['title'],
            'document_type' => $validated['document_type'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        ActivityLogger::log(
            'evaluasi',
            'upload_document',
            'Upload dokumen evaluasi: ' . $document->title,
            $document,
            null,
            $document->toArray()
        );

        return redirect()
            ->route('documentation.evaluasi.show', $record)
            ->with('success', 'Dokumen pendukung evaluasi berhasil diupload.');
    }

    public function downloadDocument(Request $request, EvaluationDocument $document)
    {
        $user = $request->user();

        $document->load('evaluationRecord');

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            abort_if(blank($unitId), 403, 'Akun belum terhubung dengan data pegawai/unit.');
            abort_if((int) $document->unit_id !== (int) $unitId, 403, 'Anda tidak memiliki akses ke dokumen evaluasi ini.');

            if (! $user->isKanit() && $user->role?->name !== 'gkm') {
                abort_unless(
                    $document->evaluationRecord?->isPublished(),
                    403,
                    'Anda hanya dapat mengunduh dokumen evaluasi yang sudah dipublish.'
                );
            }
        }

        abort_unless(
            Storage::exists($document->file_path),
            404,
            'File dokumen tidak ditemukan.'
        );

        ActivityLogger::log(
            'evaluasi',
            'download_document',
            'Download dokumen evaluasi: ' . $document->title,
            $document
        );

        return Storage::download(
            $document->file_path,
            $document->original_name ?: basename($document->file_path)
        );
    }

    public function destroyDocument(Request $request, EvaluationDocument $document)
    {
        $document->load('evaluationRecord');

        abort_if(
            blank($document->evaluationRecord),
            404,
            'Data hasil evaluasi tidak ditemukan.'
        );

        $this->authorizeManage($request, $document->evaluationRecord);

        abort_unless(
            $document->evaluationRecord->isDraft(),
            403,
            'Dokumen hanya bisa dihapus pada hasil evaluasi berstatus Draft.'
        );

        $oldValues = $document->toArray();
        $record = $document->evaluationRecord;
        $title = $document->title;
        $filePath = $document->file_path;

        ActivityLogger::log(
            'evaluasi',
            'delete_document',
            'Menghapus dokumen evaluasi: ' . $title,
            $document,
            $oldValues,
            null
        );

        $document->delete();

        if ($filePath && Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        return redirect()
            ->route('documentation.evaluasi.show', $record)
            ->with('success', 'Dokumen pendukung evaluasi berhasil dihapus.');
    }

    private function authorizeManage(Request $request, ?EvaluationRecord $record = null): int
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $user->isKanit() || $user->role?->name === 'gkm',
            403,
            'Anda tidak memiliki akses mengelola hasil evaluasi.'
        );

        if ($user->isAdmin()) {
            if ($record) {
                return (int) $record->unit_id;
            }

            return 0;
        }

        $unitId = $user->employee?->unit_id;

        abort_if(blank($unitId), 403, 'Akun belum terhubung dengan data pegawai/unit.');

        if ($record) {
            abort_if((int) $record->unit_id !== (int) $unitId, 403, 'Anda tidak memiliki akses ke hasil evaluasi ini.');
        }

        return (int) $unitId;
    }
}