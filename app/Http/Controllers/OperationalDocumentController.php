<?php

namespace App\Http\Controllers;

use App\Models\OperationalDocument;
use App\Models\Unit;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class OperationalDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm()
                || $user->canAccessEmployeeArea(),
            403
        );

        $query = OperationalDocument::query()
            ->with(['unit', 'uploadedBy'])
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            $query->forUnit($unitId);

            if ($this->isEmployeeOnly($user)) {
                $query->visibleForEmployee();
            }
        }

        if ($request->filled('unit_id') && $user->isAdmin()) {
            $query->where('unit_id', $request->integer('unit_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('period_month')) {
            $query->where('period_month', $request->integer('period_month'));
        }

        if ($request->filled('period_year')) {
            $query->where('period_year', $request->integer('period_year'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('document_number', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('file_original_name', 'like', '%' . $search . '%');
            });
        }

        $documents = $query
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = OperationalDocument::query();

        if (! $user->isAdmin()) {
            $summaryQuery->forUnit($user->employee?->unit_id);

            if ($this->isEmployeeOnly($user)) {
                $summaryQuery->visibleForEmployee();
            }
        }

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'draft' => $this->isEmployeeOnly($user)
                ? 0
                : (clone $summaryQuery)->where('status', OperationalDocument::STATUS_DRAFT)->count(),
            'published' => (clone $summaryQuery)->where('status', OperationalDocument::STATUS_PUBLISHED)->count(),
            'archived' => $this->isEmployeeOnly($user)
                ? 0
                : (clone $summaryQuery)->where('status', OperationalDocument::STATUS_ARCHIVED)->count(),
        ];

        return view('operations.documents.index', [
            'documents' => $documents,
            'summary' => $summary,
            'categoryOptions' => OperationalDocument::categoryOptions(),
            'statusOptions' => OperationalDocument::statusOptions(),
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
            'units' => $user->isAdmin()
                ? Unit::query()->orderBy('name')->get()
                : collect(),
        ]);
    }

    private function isEmployeeOnly($user): bool
    {
        return $user->canAccessEmployeeArea()
            && ! $user->isAdmin()
            && ! $user->isKanit()
            && ! $user->isGkm();
    }

    private function monthOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function yearOptions(): array
    {
        $currentYear = now()->year;

        return range($currentYear + 1, $currentYear - 5);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.documents.create', [
            'units' => $units,
            'categoryOptions' => OperationalDocument::categoryOptions(),
            'visibilityOptions' => OperationalDocument::visibilityOptions(),
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf($user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'category' => ['required', 'string', Rule::in(array_keys(OperationalDocument::categoryOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'period_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'period_year' => ['nullable', 'integer', 'min:2020', 'max:' . (now()->year + 2)],
            'document_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'visibility' => ['required', 'string', Rule::in(array_keys(OperationalDocument::visibilityOptions()))],
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,pdf,doc,docx',
                'max:20480',
            ],
        ], [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'title.required' => 'Judul dokumen wajib diisi.',
            'period_month.min' => 'Bulan tidak valid.',
            'period_month.max' => 'Bulan tidak valid.',
            'period_year.min' => 'Tahun tidak valid.',
            'period_year.max' => 'Tahun tidak valid.',
            'document_date.date' => 'Tanggal dokumen tidak valid.',
            'visibility.required' => 'Visibility wajib dipilih.',
            'visibility.in' => 'Visibility tidak valid.',
            'file.required' => 'File wajib diupload.',
            'file.mimes' => 'File harus berformat xlsx, xls, pdf, doc, atau docx.',
            'file.max' => 'Ukuran file maksimal 20MB.',
        ]);

        if (! $user->isAdmin()) {
            $validated['unit_id'] = $user->employee?->unit_id;
        }

        if (! $validated['unit_id']) {
            return back()
                ->withInput()
                ->with('error', 'Unit tidak ditemukan untuk user ini.');
        }

        $uploadedFile = $request->file('file');

        $storedPath = $uploadedFile->store(
            'operational-documents',
            'private'
        );

        try {
            $document = DB::transaction(function () use (
                $validated,
                $uploadedFile,
                $storedPath,
                $user
            ) {
                $document = OperationalDocument::create([
                    'unit_id' => $validated['unit_id'],
                    'category' => $validated['category'],
                    'title' => $validated['title'],
                    'document_number' => $validated['document_number'] ?? null,
                    'period_month' => $validated['period_month'] ?? null,
                    'period_year' => $validated['period_year'] ?? null,
                    'document_date' => $validated['document_date'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'visibility' => $validated['visibility'],
                    'status' => OperationalDocument::STATUS_DRAFT,
                    'file_path' => $storedPath,
                    'file_name' => basename($storedPath),
                    'file_original_name' => $uploadedFile->getClientOriginalName(),
                    'file_mime_type' => $uploadedFile->getClientMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'uploaded_by_user_id' => $user->id,
                    'updated_by_user_id' => $user->id,
                ]);

                ActivityLogger::log(
                    'Operasional SIM/TI',
                    'Create Operational Document',
                    'Upload arsip operasional: ' . $document->title,
                    $document,
                    null,
                    [
                        'unit_id' => $document->unit_id,
                        'category' => $document->category,
                        'status' => $document->status,
                        'visibility' => $document->visibility,
                        'file_original_name' => $document->file_original_name,
                    ]
                );

                return $document;
            });
        } catch (\Throwable $exception) {
            if (
                $storedPath
                && Storage::disk('private')->exists($storedPath)
            ) {
                Storage::disk('private')->delete($storedPath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Arsip gagal disimpan. File yang sempat diupload sudah dibersihkan.'
                );
        }

        return redirect()
            ->route('operations.documents.index')
            ->with('success', 'Arsip operasional berhasil diupload sebagai Draft.');
    }

    public function show(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentAccess($user, $document);

        $document->load([
            'unit',
            'uploadedBy',
            'updatedBy',
            'publishedBy',
        ]);

        return view('operations.documents.show', [
            'document' => $document,
        ]);
    }

    public function download(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentAccess($user, $document);

        if (! Storage::disk('private')->exists($document->file_path)) {
            return back()->with('error', 'File arsip tidak ditemukan di storage.');
        }

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Download Operational Document',
            'Download arsip operasional: ' . $document->title,
            $document,
            null,
            [
                'file_original_name' => $document->file_original_name,
                'category' => $document->category,
                'status' => $document->status,
            ]
        );

        return Storage::disk('private')->download(
            $document->file_path,
            $document->file_original_name
        );
    }

    public function publish(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);
        $this->authorizeDocumentUnitManage($user, $document);

        if (! $document->canPublish()) {
            return back()->with('error', 'Hanya dokumen Draft yang bisa dipublish.');
        }

        $oldValues = [
            'status' => $document->status,
            'published_at' => $document->published_at,
            'published_by_user_id' => $document->published_by_user_id,
        ];

        $document->update([
            'status' => OperationalDocument::STATUS_PUBLISHED,
            'published_at' => now(),
            'published_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Publish Operational Document',
            'Publish arsip operasional: ' . $document->title,
            $document,
            $oldValues,
            [
                'status' => $document->status,
                'published_at' => $document->published_at,
                'published_by_user_id' => $document->published_by_user_id,
            ]
        );

        return back()->with('success', 'Arsip operasional berhasil dipublish.');
    }

    public function archive(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);
        $this->authorizeDocumentUnitManage($user, $document);

        if (! $document->canArchive()) {
            return back()->with('error', 'Hanya dokumen Published yang bisa diarsipkan.');
        }

        $oldValues = [
            'status' => $document->status,
            'archived_at' => $document->archived_at,
        ];

        $document->update([
            'status' => OperationalDocument::STATUS_ARCHIVED,
            'archived_at' => now(),
            'updated_by_user_id' => $user->id,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Archive Operational Document',
            'Archive arsip operasional: ' . $document->title,
            $document,
            $oldValues,
            [
                'status' => $document->status,
                'archived_at' => $document->archived_at,
            ]
        );

        return back()->with('success', 'Arsip operasional berhasil diarsipkan.');
    }

    public function destroy(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);
        $this->authorizeDocumentUnitManage($user, $document);

        if (! $document->isDeletable()) {
            return back()->with('error', 'Hanya dokumen Draft yang bisa dihapus.');
        }

        $filePath = $document->file_path;

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Delete Operational Document',
            'Menghapus arsip operasional: ' . $document->title,
            $document,
            [
                'title' => $document->title,
                'category' => $document->category,
                'status' => $document->status,
                'file_original_name' => $document->file_original_name,
            ],
            null
        );

        $document->delete();

        if ($filePath && Storage::disk('private')->exists($filePath)) {
            Storage::disk('private')->delete($filePath);
        }

        return redirect()
            ->route('operations.documents.index')
            ->with('success', 'Arsip operasional berhasil dihapus.');
    }

    public function edit(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);
        $this->authorizeDocumentUnitManage($user, $document);

        if (! $document->isEditable()) {
            return redirect()
                ->route('operations.documents.show', $document)
                ->with('error', 'Hanya arsip Draft yang bisa diedit.');
        }

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.documents.edit', [
            'document' => $document,
            'units' => $units,
            'categoryOptions' => OperationalDocument::categoryOptions(),
            'visibilityOptions' => OperationalDocument::visibilityOptions(),
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function update(Request $request, OperationalDocument $document)
    {
        $user = $request->user();

        $this->authorizeDocumentManage($user);
        $this->authorizeDocumentUnitManage($user, $document);

        if (! $document->isEditable()) {
            return redirect()
                ->route('operations.documents.show', $document)
                ->with('error', 'Hanya arsip Draft yang bisa diedit.');
        }

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf($user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'category' => ['required', 'string', Rule::in(array_keys(OperationalDocument::categoryOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'period_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'period_year' => ['nullable', 'integer', 'min:2020', 'max:' . (now()->year + 2)],
            'document_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'visibility' => ['required', 'string', Rule::in(array_keys(OperationalDocument::visibilityOptions()))],
            'file' => [
                'nullable',
                'file',
                'mimes:xlsx,xls,pdf,doc,docx',
                'max:20480',
            ],
        ], [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'title.required' => 'Judul dokumen wajib diisi.',
            'period_month.min' => 'Bulan tidak valid.',
            'period_month.max' => 'Bulan tidak valid.',
            'period_year.min' => 'Tahun tidak valid.',
            'period_year.max' => 'Tahun tidak valid.',
            'document_date.date' => 'Tanggal dokumen tidak valid.',
            'visibility.required' => 'Visibility wajib dipilih.',
            'visibility.in' => 'Visibility tidak valid.',
            'file.mimes' => 'File harus berformat xlsx, xls, pdf, doc, atau docx.',
            'file.max' => 'Ukuran file maksimal 20MB.',
        ]);

        if (! $user->isAdmin()) {
            $validated['unit_id'] = $document->unit_id;
        }

        if (! $validated['unit_id']) {
            return back()
                ->withInput()
                ->with('error', 'Unit tidak ditemukan.');
        }

        $oldValues = [
            'unit_id' => $document->unit_id,
            'category' => $document->category,
            'title' => $document->title,
            'document_number' => $document->document_number,
            'period_month' => $document->period_month,
            'period_year' => $document->period_year,
            'document_date' => $document->document_date?->toDateString(),
            'description' => $document->description,
            'visibility' => $document->visibility,
            'file_original_name' => $document->file_original_name,
        ];

        $oldFilePath = $document->file_path;
        $newFilePath = null;
        $uploadedFile = null;

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');

            $newFilePath = $uploadedFile->store(
                'operational-documents',
                'private'
            );
        }

        try {
            DB::transaction(function () use (
                $validated,
                $document,
                $user,
                $uploadedFile,
                $newFilePath
            ) {
                $updateData = [
                    'unit_id' => $validated['unit_id'],
                    'category' => $validated['category'],
                    'title' => $validated['title'],
                    'document_number' => $validated['document_number'] ?? null,
                    'period_month' => $validated['period_month'] ?? null,
                    'period_year' => $validated['period_year'] ?? null,
                    'document_date' => $validated['document_date'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'visibility' => $validated['visibility'],
                    'updated_by_user_id' => $user->id,
                ];

                if ($uploadedFile && $newFilePath) {
                    $updateData = array_merge($updateData, [
                        'file_path' => $newFilePath,
                        'file_name' => basename($newFilePath),
                        'file_original_name' => $uploadedFile->getClientOriginalName(),
                        'file_mime_type' => $uploadedFile->getClientMimeType(),
                        'file_size' => $uploadedFile->getSize(),
                    ]);
                }

                $document->update($updateData);
            });
        } catch (\Throwable $exception) {
            if (
                $newFilePath
                && Storage::disk('private')->exists($newFilePath)
            ) {
                Storage::disk('private')->delete($newFilePath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Perubahan arsip gagal disimpan. File pengganti yang sempat diupload sudah dibersihkan.'
                );
        }

        if (
            $newFilePath
            && $oldFilePath
            && $oldFilePath !== $newFilePath
            && Storage::disk('private')->exists($oldFilePath)
        ) {
            Storage::disk('private')->delete($oldFilePath);
        }

        $document->refresh();

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Update Operational Document',
            'Memperbarui arsip operasional: ' . $document->title,
            $document,
            $oldValues,
            [
                'unit_id' => $document->unit_id,
                'category' => $document->category,
                'title' => $document->title,
                'document_number' => $document->document_number,
                'period_month' => $document->period_month,
                'period_year' => $document->period_year,
                'document_date' => $document->document_date?->toDateString(),
                'description' => $document->description,
                'visibility' => $document->visibility,
                'file_original_name' => $document->file_original_name,
            ]
        );

        return redirect()
            ->route('operations.documents.show', $document)
            ->with('success', 'Arsip operasional berhasil diperbarui.');
    }

    private function authorizeDocumentAccess($user, OperationalDocument $document): void
    {
        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm()
                || $user->canAccessEmployeeArea(),
            403,
            'Anda tidak memiliki akses ke arsip operasional.'
        );

        if ($user->isAdmin()) {
            return;
        }

        abort_unless(
            (int) $document->unit_id === (int) $user->employee?->unit_id,
            403,
            'Anda tidak memiliki akses ke arsip unit lain.'
        );

        if ($user->isKanit() || $user->isGkm()) {
            return;
        }

        abort_unless(
            $document->status === OperationalDocument::STATUS_PUBLISHED
                && $document->visibility === OperationalDocument::VISIBILITY_UNIT,
            403,
            'Dokumen ini belum tersedia untuk pegawai.'
        );
    }

    private function authorizeDocumentUnitManage($user, OperationalDocument $document): void
    {
        if ($user->isAdmin()) {
            return;
        }

        abort_unless(
            (int) $document->unit_id === (int) $user->employee?->unit_id,
            403,
            'Anda tidak memiliki akses mengelola arsip unit lain.'
        );
    }

    private function authorizeDocumentManage($user): void
    {
        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm(),
            403,
            'Anda tidak memiliki akses untuk mengelola arsip operasional.'
        );
    }
}