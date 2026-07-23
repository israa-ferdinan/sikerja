<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentDocument;
use App\Models\DevelopmentPlan;
use App\Models\Unit;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DevelopmentDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DevelopmentDocument::query()
            ->with(['unit', 'developmentPlan', 'uploader'])
            ->latest();

        if (! $this->isAdmin($user)) {
            $employee = $user->employee;

            if (! $employee || ! $employee->unit_id) {
                abort(403, 'Akun ini belum terhubung dengan unit.');
            }

            $query->where('unit_id', $employee->unit_id);

            if (
                method_exists($user, 'canAccessEmployeeArea')
                && $user->canAccessEmployeeArea()
                && ! $this->canManage($user)
            ) {
                $query->where('visibility', DevelopmentDocument::VISIBILITY_UNIT);
            }
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        if ($this->isAdmin($user) && $request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('development_plan_id')) {
            if ($request->development_plan_id === 'none') {
                $query->whereNull('development_plan_id');
            } else {
                $query->where('development_plan_id', $request->development_plan_id);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(10)->withQueryString();

        $units = $this->isAdmin($user)
            ? Unit::orderBy('name')->get()
            : collect();

        $plans = $this->availablePlans($user, includeReadOnly: true);

        return view('developments.documents.index', [
            'documents' => $documents,
            'units' => $units,
            'plans' => $plans,
            'documentTypes' => DevelopmentDocument::documentTypes(),
            'visibilities' => DevelopmentDocument::visibilities(),
            'isAdmin' => $this->isAdmin($user),
            'canManage' => $this->canManage($user),
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $this->authorizeManage($user);

        $selectedPlan = null;

        if ($request->filled('development_plan_id')) {
            $selectedPlan = DevelopmentPlan::query()
                ->with('unit')
                ->whereKey($request->development_plan_id)
                ->firstOrFail();

            $this->authorizePlanAccess($selectedPlan, $user);

            abort_if(
                ! $selectedPlan->canUploadDocument(),
                422,
                'Rencana pengembangan ini sudah read-only dan tidak bisa ditambah dokumen.'
            );
        }

        return view('developments.documents.create', [
            'document' => null,
            'selectedPlan' => $selectedPlan,
            'units' => $this->availableUnits($user),
            'plans' => $this->availablePlans($user),
            'documentTypes' => DevelopmentDocument::documentTypes(),
            'visibilities' => DevelopmentDocument::visibilities(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $this->authorizeManage($user);

        $data = $this->validatedData($request, $user, requireFile: true);

        $file = $request->file('file');
        $path = $file->store('development/documents', 'local');

        $developmentDocument = DevelopmentDocument::create([
            'development_plan_id' => $data['development_plan_id'] ?? null,
            'unit_id' => $data['unit_id'],
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'visibility' => $data['visibility'],
            'uploaded_by' => $user->id,
        ]);

        ActivityLogger::log(
            'Pengembangan',
            'Upload Development Document',
            'Upload dokumen pengembangan: ' . $developmentDocument->title
        );

        if (! empty($data['development_plan_id'])) {
            return redirect()
                ->route('developments.plans.show', $data['development_plan_id'])
                ->with('success', 'Dokumen pengembangan berhasil diupload.');
        }

        return redirect()
            ->route('developments.documents.index')
            ->with('success', 'Dokumen pengembangan berhasil diupload.');
    }

    public function edit(DevelopmentDocument $developmentDocument)
    {
        $user = Auth::user();

        $this->authorizeManageDocument($developmentDocument, $user);
        $this->ensureDocumentPlanAllowsModification($developmentDocument);

        return view('developments.documents.edit', [
            'document' => $developmentDocument,
            'selectedPlan' => $developmentDocument->developmentPlan,
            'units' => $this->availableUnits($user),
            'plans' => $this->availablePlans($user, includeReadOnly: true),
            'documentTypes' => DevelopmentDocument::documentTypes(),
            'visibilities' => DevelopmentDocument::visibilities(),
        ]);
    }

    public function update(Request $request, DevelopmentDocument $developmentDocument)
    {
        $user = Auth::user();

        $this->authorizeManageDocument($developmentDocument, $user);
        $this->ensureDocumentPlanAllowsModification($developmentDocument);

        $data = $this->validatedData($request, $user, requireFile: false, currentDocument: $developmentDocument);

        $updateData = [
            'development_plan_id' => $data['development_plan_id'] ?? null,
            'unit_id' => $data['unit_id'],
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'visibility' => $data['visibility'],
        ];

        if ($request->hasFile('file')) {
            if ($developmentDocument->file_path && Storage::disk('local')->exists($developmentDocument->file_path)) {
                Storage::disk('local')->delete($developmentDocument->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('development/documents', 'local');

            $updateData['file_path'] = $path;
            $updateData['original_name'] = $file->getClientOriginalName();
            $updateData['mime_type'] = $file->getClientMimeType();
            $updateData['file_size'] = $file->getSize();
        }

        $developmentDocument->update($updateData);

        ActivityLogger::log(
            'Pengembangan',
            'Update Development Document',
            'Memperbarui dokumen pengembangan: ' . $developmentDocument->title
        );

        if ($developmentDocument->development_plan_id) {
            return redirect()
                ->route('developments.plans.show', $developmentDocument->development_plan_id)
                ->with('success', 'Dokumen pengembangan berhasil diperbarui.');
        }

        return redirect()
            ->route('developments.documents.index')
            ->with('success', 'Dokumen pengembangan berhasil diperbarui.');
    }

    public function destroy(DevelopmentDocument $developmentDocument)
    {
        $user = Auth::user();

        $this->authorizeManageDocument($developmentDocument, $user);

        $this->ensureDocumentPlanAllowsModification(
            $developmentDocument,
            allowCancelledDelete: true
        );

        $title = $developmentDocument->title;

        if ($developmentDocument->file_path && Storage::disk('local')->exists($developmentDocument->file_path)) {
            Storage::disk('local')->delete($developmentDocument->file_path);
        }

        $developmentDocument->delete();

        ActivityLogger::log(
            'Pengembangan',
            'Delete Development Document',
            'Menghapus dokumen pengembangan: ' . $title
        );

        return redirect()
            ->route('developments.documents.index')
            ->with('success', 'Dokumen pengembangan berhasil dihapus.');
    }

    public function download(DevelopmentDocument $developmentDocument)
    {
        $user = Auth::user();

        $this->authorizeAccess($developmentDocument, $user);

        if (! Storage::disk('local')->exists($developmentDocument->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        ActivityLogger::log(
            'Pengembangan',
            'Download Development Document',
            'Download dokumen pengembangan: ' . $developmentDocument->title
        );

        return Storage::disk('local')->download(
            $developmentDocument->file_path,
            $developmentDocument->original_name
        );
    }

    private function validatedData(
        Request $request,
        $user,
        bool $requireFile,
        ?DevelopmentDocument $currentDocument = null
    ): array {
        $unitIds = $this->availableUnits($user)->pluck('id')->toArray();
        $planIds = $this->availablePlans($user, includeReadOnly: true)->pluck('id')->toArray();

        $data = $request->validate([
            'development_plan_id' => [
                'nullable',
                'integer',
                Rule::in($planIds),
            ],
            'unit_id' => [
                'required',
                'integer',
                Rule::in($unitIds),
            ],
            'document_type' => [
                'required',
                'string',
                Rule::in(DevelopmentDocument::documentTypes()),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'visibility' => [
                'required',
                'string',
                Rule::in(DevelopmentDocument::visibilities()),
            ],
            'file' => [
                $requireFile ? 'required' : 'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png',
                'max:10240',
            ],
        ], [
            'file.required' => 'File dokumen wajib diupload.',
            'file.mimes' => 'Format file harus PDF, Word, Excel, PowerPoint, JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        if (! empty($data['development_plan_id'])) {
            $plan = DevelopmentPlan::query()
                ->whereKey($data['development_plan_id'])
                ->firstOrFail();

            $this->authorizePlanAccess($plan, $user);

            abort_if(
                (int) $plan->unit_id !== (int) $data['unit_id'],
                422,
                'Unit dokumen harus sama dengan unit rencana pengembangan.'
            );

            if (! $currentDocument || (int) $currentDocument->development_plan_id !== (int) $plan->id) {
                abort_if(
                    ! $plan->canUploadDocument(),
                    422,
                    'Rencana pengembangan ini sudah read-only dan tidak bisa ditambah dokumen.'
                );
            }
        }

        return $data;
    }

    private function availableUnits($user)
    {
        if ($this->isAdmin($user)) {
            return Unit::query()
                ->orderBy('name')
                ->get();
        }

        $employee = $user->employee;

        if (! $employee || ! $employee->unit_id) {
            abort(403, 'Akun ini belum terhubung dengan unit.');
        }

        return Unit::query()
            ->where('id', $employee->unit_id)
            ->get();
    }

    private function availablePlans($user, bool $includeReadOnly = false)
    {
        $query = DevelopmentPlan::query()
            ->with('unit')
            ->orderByDesc('created_at');

        if (! $this->isAdmin($user)) {
            $employee = $user->employee;

            if (! $employee || ! $employee->unit_id) {
                abort(403, 'Akun ini belum terhubung dengan unit.');
            }

            $query->where('unit_id', $employee->unit_id);
        }

        if (! $includeReadOnly) {
            $query->whereNotIn('status', [
                DevelopmentPlan::STATUS_SELESAI,
                DevelopmentPlan::STATUS_DIBATALKAN,
            ]);
        }

        return $query->get();
    }

    private function authorizeAccess(DevelopmentDocument $document, $user): void
    {
        if (! $user) {
            abort(403);
        }

        if ($this->isAdmin($user)) {
            return;
        }

        $employee = $user->employee;

        if (! $employee || ! $employee->unit_id) {
            abort(403, 'Akun ini belum terhubung dengan unit.');
        }

        if ((int) $document->unit_id !== (int) $employee->unit_id) {
            abort(403);
        }

        if (
            method_exists($user, 'canAccessEmployeeArea')
            && $user->canAccessEmployeeArea()
            && ! $this->canManage($user)
            && $document->visibility !== DevelopmentDocument::VISIBILITY_UNIT
        ) {
            abort(403);
        }
    }

    private function authorizeManage($user): void
    {
        if (! $this->canManage($user)) {
            abort(403);
        }

        if (! $this->isAdmin($user)) {
            $employee = $user->employee;

            if (! $employee || ! $employee->unit_id) {
                abort(403, 'Akun ini belum terhubung dengan unit.');
            }
        }
    }

    private function authorizeManageDocument(DevelopmentDocument $document, $user): void
    {
        $this->authorizeManage($user);

        if ($this->isAdmin($user)) {
            return;
        }

        $employee = $user->employee;

        if ((int) $document->unit_id !== (int) $employee->unit_id) {
            abort(403);
        }
    }

    private function authorizePlanAccess(DevelopmentPlan $plan, $user): void
    {
        if ($this->isAdmin($user)) {
            return;
        }

        $employee = $user->employee;

        if (! $employee || ! $employee->unit_id) {
            abort(403, 'Akun ini belum terhubung dengan unit.');
        }

        if ((int) $plan->unit_id !== (int) $employee->unit_id) {
            abort(403);
        }
    }

    private function ensureDocumentPlanAllowsModification(
        DevelopmentDocument $document,
        bool $allowCancelledDelete = false
    ): void {
        $document->loadMissing('developmentPlan');

        $plan = $document->developmentPlan;

        if (! $plan) {
            return;
        }

        if ($plan->status === DevelopmentPlan::STATUS_SELESAI) {
            abort(
                422,
                'Dokumen tidak dapat diubah karena rencana pengembangan sudah Selesai.'
            );
        }

        if (
            $plan->status === DevelopmentPlan::STATUS_DIBATALKAN
            && ! $allowCancelledDelete
        ) {
            abort(
                422,
                'Dokumen tidak dapat diubah karena rencana pengembangan sudah Dibatalkan.'
            );
        }
    }

    private function isAdmin($user): bool
    {
        return $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    private function canManage($user): bool
    {
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        if (method_exists($user, 'isKanit') && $user->isKanit()) {
            return true;
        }

        if (method_exists($user, 'isGkm') && $user->isGkm()) {
            return true;
        }

        return false;
    }
}