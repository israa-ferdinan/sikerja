<?php

namespace App\Http\Controllers;

use App\Models\DocumentationDocument;
use App\Models\JobDuty;
use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentationPenetapanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $categories = DocumentationDocument::penetapanCategories();
        $statuses = DocumentationDocument::statuses();
        $selectedCategory = null;

        if ($request->filled('category') && array_key_exists($request->category, $categories)) {
            $selectedCategory = $request->category;
        }

        $query = DocumentationDocument::query()
            ->penetapan()
            ->with('uploader')
            ->latest();

        if (! $user->canManageDocumentation()) {
            $query->published();
        }

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        $currentCategoryLabel = null;

        if ($request->filled('category') && array_key_exists($request->category, $categories)) {
            $currentCategoryLabel = $categories[$request->category];
        }

        if ($user->canManageDocumentation() && $request->filled('status') && array_key_exists($request->status, $statuses)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('revision', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(10)->withQueryString();

        $activeFilters = [];

        if ($user->canManageDocumentation() && $request->filled('status') && array_key_exists($request->status, $statuses)) {
            $activeFilters[] = [
                'label' => 'Status',
                'value' => $statuses[$request->status],
            ];
        }

        if ($request->filled('search')) {
            $activeFilters[] = [
                'label' => 'Pencarian',
                'value' => $request->search,
            ];
        }

        $tupoksiItems = collect();
        $organizationHead = null;
        $organizationMembers = collect();

        if ($selectedCategory === DocumentationDocument::CATEGORY_STRUKTUR_ORGANISASI) {
            $employees = Employee::query()
                ->with(['unit', 'jobPosition', 'user.role'])
                ->withCount('duties')
                ->whereHas('unit', function ($query) {
                    $query->where('name', 'like', '%Teknologi Informasi%')
                        ->orWhere('name', 'like', '%SIM TI%')
                        ->orWhere('name', 'like', '%Sistem Manajemen Informasi%');
                })
                ->latest()
                ->get()
                ->reject(function ($employee) {
                    return $employee->user?->isAdmin();
                })
                ->values();

            $organizationHead = $employees->first(function ($employee) {
                return $employee->user?->isKanit();
            });

            $organizationMembers = $employees
                ->reject(function ($employee) use ($organizationHead) {
                    return $organizationHead && $employee->id === $organizationHead->id;
                })
                ->values();
        }

        if ($selectedCategory === DocumentationDocument::CATEGORY_TUPOKSI_SIM_TI) {
            $tupoksiItems = JobDuty::query()
                ->with([
                    'unit',
                    'classification',
                    'server',
                    'application',
                    'employees' => function ($query) {
                        $query
                            ->with(['unit', 'jobPosition'])
                            ->orderBy('name');
                    },
                ])
                ->whereHas('unit', function ($query) {
                    $query->where('name', 'like', '%Teknologi Informasi%')
                        ->orWhere('name', 'like', '%SIM TI%')
                        ->orWhere('name', 'like', '%Sistem Manajemen Informasi%');
                })
                ->latest()
                ->get();
        }

        return view('documentation.penetapan.index', [
            'documents' => $documents,
            'categories' => $categories,
            'statuses' => $statuses,
            'canManage' => $user->canManageDocumentation(),
            'selectedCategory' => $selectedCategory,
            'selectedStatus' => $request->status,
            'search' => $request->search,
            'activeFilters' => $activeFilters,
            'currentCategoryLabel' => $currentCategoryLabel,
            'tupoksiItems' => $tupoksiItems,
            'organizationHead' => $organizationHead,
            'organizationMembers' => $organizationMembers,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        if (! $user->canManageDocumentation()) {
            abort(403, 'Anda tidak memiliki akses untuk menambah dokumen Penetapan.');
        }

        return view('documentation.penetapan.create', [
            'categories' => DocumentationDocument::penetapanCategories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->canManageDocumentation()) {
            abort(403, 'Anda tidak memiliki akses untuk menambah dokumen Penetapan.');
        }

        $validated = $request->validate([
            ...$this->validationRules(),
            ...$this->fileValidationRules(),
        ]);

        $fileData = [];

        $document = DocumentationDocument::create([
            ...collect($validated)->except('document_file')->toArray(),
            'section' => DocumentationDocument::SECTION_PENETAPAN,
            'status' => DocumentationDocument::STATUS_DRAFT,
            'uploaded_by' => $user->id,
        ]);

        $fileData = $this->storeDocumentFile($request, $document);

        if (! empty($fileData)) {
            $document->update($fileData);
        }

        ActivityLogger::log(
            module: 'documentation_penetapan',
            action: 'create',
            description: 'Menambahkan dokumen Penetapan: ' . $document->title,
            subject: $document,
            newValues: $document->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.penetapan.show', $document)
            ->with('success', 'Dokumen Penetapan berhasil dibuat sebagai draft.');
    }

    public function show(Request $request, DocumentationDocument $document): View
    {
        $user = $request->user();

        $this->ensureCanViewDocument($request, $document);

        $document->load('uploader');

        return view('documentation.penetapan.show', [
            'document' => $document,
            'canManage' => $user->canManageDocumentation(),
        ]);
    }

    public function edit(Request $request, DocumentationDocument $document): View
    {
        $this->ensureCanManageDocumentation($request);
        $this->ensurePenetapanDocument($document);
        $this->ensureDraftDocument($document);

        return view('documentation.penetapan.edit', [
            'document' => $document,
            'categories' => DocumentationDocument::penetapanCategories(),
        ]);
    }

    public function update(Request $request, DocumentationDocument $document): RedirectResponse
    {
        $this->ensureCanManageDocumentation($request);
        $this->ensurePenetapanDocument($document);
        $this->ensureDraftDocument($document);

        $oldValues = $document->toArray();

        $validated = $request->validate([
            ...$this->validationRules(),
            ...$this->fileValidationRules(),
        ]);

        $metadata = collect($validated)->except('document_file')->toArray();

        $document->update($metadata);

        $fileData = $this->storeDocumentFile($request, $document);

        if (! empty($fileData)) {
            $this->deleteDocumentFileIfExists($document);

            $document->update($fileData);

            ActivityLogger::log(
                module: 'documentation_penetapan',
                action: 'replace_file',
                description: 'Mengganti file dokumen Penetapan: ' . $document->title,
                subject: $document,
                oldValues: $oldValues,
                newValues: $document->fresh()->toArray()
            );
        }

        ActivityLogger::log(
            module: 'documentation_penetapan',
            action: 'update',
            description: 'Memperbarui dokumen Penetapan: ' . $document->title,
            subject: $document,
            oldValues: $oldValues,
            newValues: $document->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.penetapan.show', $document)
            ->with('success', 'Dokumen Penetapan berhasil diperbarui.');
    }

    public function destroy(Request $request, DocumentationDocument $document): RedirectResponse
    {
        $this->ensureCanManageDocumentation($request);
        $this->ensurePenetapanDocument($document);
        $this->ensureDraftDocument($document);

        $oldValues = $document->toArray();
        $title = $document->title;

        $this->deleteDocumentFileIfExists($document);

        $document->delete();

        ActivityLogger::log(
            module: 'documentation_penetapan',
            action: 'delete',
            description: 'Menghapus dokumen Penetapan: ' . $title,
            subject: $document,
            oldValues: $oldValues
        );

        return redirect()
            ->route('documentation.penetapan.index')
            ->with('success', 'Dokumen Penetapan berhasil dihapus.');
    }

    public function publish(Request $request, DocumentationDocument $document): RedirectResponse
    {
        $this->ensureCanManageDocumentation($request);
        $this->ensurePenetapanDocument($document);
        $this->ensureDraftDocument($document);

        $oldValues = $document->toArray();

        $document->update([
            'status' => DocumentationDocument::STATUS_PUBLISHED,
            'published_at' => now(),
            'archived_at' => null,
        ]);

        ActivityLogger::log(
            module: 'documentation_penetapan',
            action: 'publish',
            description: 'Mempublish dokumen Penetapan: ' . $document->title,
            subject: $document,
            oldValues: $oldValues,
            newValues: $document->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.penetapan.show', $document)
            ->with('success', 'Dokumen Penetapan berhasil dipublish.');
    }

    public function archive(Request $request, DocumentationDocument $document): RedirectResponse
    {
        $this->ensureCanManageDocumentation($request);
        $this->ensurePenetapanDocument($document);
        $this->ensurePublishedDocument($document);

        $oldValues = $document->toArray();

        $document->update([
            'status' => DocumentationDocument::STATUS_ARCHIVED,
            'archived_at' => now(),
        ]);

        ActivityLogger::log(
            module: 'documentation_penetapan',
            action: 'archive',
            description: 'Mengarsipkan dokumen Penetapan: ' . $document->title,
            subject: $document,
            oldValues: $oldValues,
            newValues: $document->fresh()->toArray()
        );

        return redirect()
            ->route('documentation.penetapan.show', $document)
            ->with('success', 'Dokumen Penetapan berhasil diarsipkan.');
    }

    public function download(Request $request, DocumentationDocument $document): StreamedResponse
    {
        $this->ensureCanViewDocument($request, $document);

        if (! $document->file_path) {
            abort(404, 'File dokumen belum tersedia.');
        }

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File dokumen tidak ditemukan di storage.');
        }

        ActivityLogger::log(
            module: 'documentation_penetapan',
            action: 'download',
            description: 'Mengunduh file dokumen Penetapan: ' . $document->title,
            subject: $document,
            newValues: [
                'file_path' => $document->file_path,
                'original_filename' => $document->original_filename,
                'downloaded_by' => $request->user()->id,
            ]
        );

        return Storage::disk('local')->download(
            $document->file_path,
            $document->original_filename ?: basename($document->file_path)
        );
    }

    private function ensurePenetapanDocument(DocumentationDocument $document): void
    {
        if ($document->section !== DocumentationDocument::SECTION_PENETAPAN) {
            throw new NotFoundHttpException();
        }
    }

    private function ensureCanManageDocumentation(Request $request): void
    {
        if (! $request->user()->canManageDocumentation()) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola dokumen Penetapan.');
        }
    }

    private function validationRules(): array
    {
        return [
            'category' => ['required', 'string', Rule::in(array_keys(DocumentationDocument::penetapanCategories()))],
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'document_date' => ['nullable', 'date'],
            'effective_date' => ['nullable', 'date'],
            'revision' => ['nullable', 'string', 'max:50'],
        ];
    }

    private function fileValidationRules(): array
    {
        return [
            'document_file' => [
                'nullable',
                'file',
                'max:10240',
                'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,image/jpeg,image/png,application/octet-stream',
            ],
        ];
    }

    private function storeDocumentFile(Request $request, DocumentationDocument $document): array
    {
        if (! $request->hasFile('document_file')) {
            return [];
        }

        $file = $request->file('document_file');

        $path = $file->store(
            'documentation/penetapan/' . $document->category,
            'local'
        );

        return [
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_mime' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ];
    }

    private function deleteDocumentFileIfExists(DocumentationDocument $document): void
    {
        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }
    }

    private function ensureCanViewDocument(Request $request, DocumentationDocument $document): void
    {
        $this->ensurePenetapanDocument($document);

        if ($request->user()->canManageDocumentation()) {
            return;
        }

        if (! $document->isPublished()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }
    }

    private function ensureDraftDocument(DocumentationDocument $document): void
    {
        if (! $document->isDraft()) {
            abort(403, 'Dokumen yang sudah dipublish atau diarsipkan tidak dapat diubah.');
        }
    }

    private function ensurePublishedDocument(DocumentationDocument $document): void
    {
        if (! $document->isPublished()) {
            abort(403, 'Hanya dokumen published yang dapat diarsipkan.');
        }
    }
}