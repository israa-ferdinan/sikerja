<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentPlan;
use App\Models\Employee;
use App\Models\Unit;
use App\Services\AppNotifier;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DevelopmentPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DevelopmentPlan::query()
            ->with(['unit', 'picEmployee', 'creator'])
            ->latest();

        if (! $this->isAdmin($user)) {
            $employee = $user->employee;

            if (! $employee || ! $employee->unit_id) {
                abort(403, 'Akun ini belum terhubung dengan unit.');
            }

            $query->where('unit_id', $employee->unit_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($this->isAdmin($user) && $request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('objective', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $plans = $query->paginate(10)->withQueryString();

        $units = $this->isAdmin($user)
            ? Unit::orderBy('name')->get()
            : collect();

        return view('developments.plans.index', [
            'plans' => $plans,
            'units' => $units,
            'statuses' => DevelopmentPlan::statuses(),
            'categories' => DevelopmentPlan::categories(),
            'priorities' => DevelopmentPlan::priorities(),
            'isAdmin' => $this->isAdmin($user),
            'canManage' => $this->canManage($user),
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $this->authorizeManage($user);

        return view('developments.plans.create', [
            'units' => $this->availableUnits($user),
            'employees' => $this->availableEmployees($user),
            'categories' => DevelopmentPlan::categories(),
            'priorities' => DevelopmentPlan::priorities(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $this->authorizeManage($user);

        $data = $this->validatedData($request, $user);

        $data['status'] = DevelopmentPlan::STATUS_USULAN;
        $data['progress_percentage'] = 0;
        $data['created_by'] = $user->id;

        $developmentPlan = DevelopmentPlan::create($data);

        ActivityLogger::log(
            'Pengembangan',
            'Create Development Plan',
            'Membuat rencana pengembangan: ' . $developmentPlan->title
        );

        if (filled($developmentPlan->pic_employee_id)) {
            AppNotifier::notifyEmployee(
                employee: (int) $developmentPlan->pic_employee_id,
                module: 'Pengembangan',
                title: 'Anda ditunjuk sebagai PIC rencana pengembangan',
                message: 'Anda ditunjuk sebagai PIC untuk rencana pengembangan: ' . $developmentPlan->title,
                url: route('developments.plans.show', $developmentPlan),
                data: [
                    'development_plan_id' => $developmentPlan->id,
                    'assigned_by_user_id' => $user->id,
                    'type' => 'assigned',
                ],
            );
        }

        return redirect()
            ->route('developments.plans.index')
            ->with('success', 'Rencana pengembangan berhasil dibuat.');
    }

    public function show(DevelopmentPlan $developmentPlan)
    {
        $user = Auth::user();

        $this->authorizeView($developmentPlan, $user);

        $developmentPlan->load([
            'unit',
            'picEmployee',
            'creator',
            'updater',
            'documents.uploader',
        ]);

        return view('developments.plans.show', [
            'plan' => $developmentPlan,
            'canManage' => $this->canManage($user),
            'canEdit' => $this->canManage($user) && ! $developmentPlan->isReadOnly(),
            'canDelete' => $this->canManage($user) && $developmentPlan->canBeDeleted(),
            'canUpdateStatus' => $this->canManage($user) && ! $developmentPlan->isReadOnly(),
            'canUpdateProgress' => $this->canUpdateProgress($developmentPlan, $user) && $developmentPlan->canUpdateProgress(),
            'nextStatuses' => $this->allowedNextStatuses($developmentPlan),
            'isPic' => $this->isPic($developmentPlan, $user),
            'canUploadDocument' => $this->canManage($user) && $developmentPlan->canUploadDocument(),
        ]);
    }

    public function edit(DevelopmentPlan $developmentPlan)
    {
        $user = Auth::user();

        $this->authorizeManagePlan($developmentPlan, $user);

        if ($developmentPlan->isReadOnly()) {
            return redirect()
                ->route('developments.plans.show', $developmentPlan)
                ->with('success', 'Rencana pengembangan sudah read-only dan tidak bisa diedit.');
        }

        return view('developments.plans.edit', [
            'plan' => $developmentPlan,
            'units' => $this->availableUnits($user),
            'employees' => $this->availableEmployees($user),
            'categories' => DevelopmentPlan::categories(),
            'priorities' => DevelopmentPlan::priorities(),
        ]);
    }

    public function update(Request $request, DevelopmentPlan $developmentPlan)
    {
        $user = Auth::user();

        $this->authorizeManagePlan($developmentPlan, $user);

        if ($developmentPlan->isReadOnly()) {
            return redirect()
                ->route('developments.plans.show', $developmentPlan)
                ->with('success', 'Rencana pengembangan sudah read-only dan tidak bisa diedit.');
        }

        $oldPicEmployeeId = $developmentPlan->pic_employee_id;

        $data = $this->validatedData($request, $user);
        $data['updated_by'] = $user->id;

        $newPicEmployeeId = $data['pic_employee_id'] ?? null;

        $developmentPlan->update($data);
        $developmentPlan->refresh();

        ActivityLogger::log(
            'Pengembangan',
            'Update Development Plan',
            'Memperbarui rencana pengembangan: ' . $developmentPlan->title
        );

        $picChanged = (int) $newPicEmployeeId !== (int) $oldPicEmployeeId;

        if ($picChanged) {
            if (filled($newPicEmployeeId)) {
                AppNotifier::notifyEmployee(
                    employee: (int) $newPicEmployeeId,
                    module: 'Pengembangan',
                    title: 'Anda ditunjuk sebagai PIC rencana pengembangan',
                    message: 'Anda ditunjuk sebagai PIC untuk rencana pengembangan: ' . $developmentPlan->title,
                    url: route('developments.plans.show', $developmentPlan),
                    data: [
                        'development_plan_id' => $developmentPlan->id,
                        'assigned_by_user_id' => $user->id,
                        'type' => 'assigned',
                    ],
                );
            }

            if (filled($oldPicEmployeeId)) {
                AppNotifier::notifyEmployee(
                    employee: (int) $oldPicEmployeeId,
                    module: 'Pengembangan',
                    title: 'Penugasan PIC rencana pengembangan diperbarui',
                    message: 'Anda sudah tidak menjadi PIC untuk rencana pengembangan: ' . $developmentPlan->title,
                    url: route('developments.plans.show', $developmentPlan),
                    data: [
                        'development_plan_id' => $developmentPlan->id,
                        'updated_by_user_id' => $user->id,
                        'type' => 'unassigned',
                    ],
                );
            }
        }

        return redirect()
            ->route('developments.plans.show', $developmentPlan)
            ->with('success', 'Rencana pengembangan berhasil diperbarui.');
    }

    public function destroy(DevelopmentPlan $developmentPlan)
    {
        $user = Auth::user();

        $this->authorizeManagePlan($developmentPlan, $user);

        if (! $developmentPlan->canBeDeleted()) {
            return redirect()
                ->route('developments.plans.show', $developmentPlan)
                ->with('success', 'Rencana pengembangan dengan status ini tidak bisa dihapus.');
        }

        $title = $developmentPlan->title;

        $developmentPlan->delete();

        ActivityLogger::log(
            'Pengembangan',
            'Delete Development Plan',
            'Menghapus rencana pengembangan: ' . $title
        );

        return redirect()
            ->route('developments.plans.index')
            ->with('success', 'Rencana pengembangan berhasil dihapus.');
    }

    public function updateStatus(Request $request, DevelopmentPlan $developmentPlan)
    {
        $user = Auth::user();

        $this->authorizeManagePlan($developmentPlan, $user);

        if ($developmentPlan->isReadOnly()) {
            return redirect()
                ->route('developments.plans.show', $developmentPlan)
                ->with('success', 'Rencana pengembangan sudah read-only dan status tidak bisa diubah.');
        }

        $data = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in($this->allowedNextStatuses($developmentPlan)),
            ],
        ]);

        $updateData = [
            'status' => $data['status'],
            'updated_by' => $user->id,
        ];

        if ($data['status'] === DevelopmentPlan::STATUS_DALAM_PROSES && ! $developmentPlan->actual_start_date) {
            $updateData['actual_start_date'] = now()->toDateString();
        }

        if ($data['status'] === DevelopmentPlan::STATUS_SELESAI) {
            $updateData['progress_percentage'] = 100;

            if (! $developmentPlan->actual_end_date) {
                $updateData['actual_end_date'] = now()->toDateString();
            }
        }

        $oldStatus = $developmentPlan->status;

        $developmentPlan->update($updateData);

        ActivityLogger::log(
            'Pengembangan',
            'Update Development Plan Status',
            'Memperbarui status rencana pengembangan "' . $developmentPlan->title . '" dari ' . $oldStatus . ' menjadi ' . $developmentPlan->status
        );

        return redirect()
            ->route('developments.plans.show', $developmentPlan)
            ->with('success', 'Status rencana pengembangan berhasil diperbarui.');
    }

    public function updateProgress(Request $request, DevelopmentPlan $developmentPlan)
    {
        $user = Auth::user();

        $this->authorizeProgressAccess($developmentPlan, $user);

        if (! $developmentPlan->canUpdateProgress()) {
            return redirect()
                ->route('developments.plans.show', $developmentPlan)
                ->with('success', 'Progress hanya bisa diperbarui pada status Disetujui atau Dalam Proses.');
        }

        $data = $request->validate([
            'progress_percentage' => [
                'required',
                'integer',
                'min:' . (int) $developmentPlan->progress_percentage,
                'max:100',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ], [
            'progress_percentage.min' => 'Progress tidak boleh lebih kecil dari progress saat ini.',
        ]);

        $updateData = [
            'progress_percentage' => $data['progress_percentage'],
            'updated_by' => $user->id,
        ];

        if (array_key_exists('notes', $data)) {
            $updateData['notes'] = $data['notes'];
        }

        if (
            $developmentPlan->status === DevelopmentPlan::STATUS_DISETUJUI
            && (int) $data['progress_percentage'] > 0
        ) {
            $updateData['status'] = DevelopmentPlan::STATUS_DALAM_PROSES;

            if (! $developmentPlan->actual_start_date) {
                $updateData['actual_start_date'] = now()->toDateString();
            }
        }

        $oldProgress = $developmentPlan->progress_percentage;

        $developmentPlan->update($updateData);

        ActivityLogger::log(
            'Pengembangan',
            'Update Development Plan Progress',
            'Memperbarui progress rencana pengembangan "' . $developmentPlan->title . '" dari ' . $oldProgress . '% menjadi ' . $developmentPlan->progress_percentage . '%'
        );

        return redirect()
            ->route('developments.plans.show', $developmentPlan)
            ->with('success', 'Progress rencana pengembangan berhasil diperbarui.');
    }

    private function validatedData(Request $request, $user): array
    {
        $unitIds = $this->availableUnits($user)->pluck('id')->toArray();
        $employeeIds = $this->availableEmployees($user)->pluck('id')->toArray();

        return $request->validate([
            'unit_id' => [
                'required',
                'integer',
                Rule::in($unitIds),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'category' => [
                'required',
                'string',
                Rule::in(DevelopmentPlan::categories()),
            ],
            'priority' => [
                'required',
                'string',
                Rule::in(DevelopmentPlan::priorities()),
            ],
            'description' => [
                'required',
                'string',
            ],
            'objective' => [
                'nullable',
                'string',
            ],
            'pic_employee_id' => [
                'nullable',
                'integer',
                Rule::in($employeeIds),
            ],
            'target_start_date' => [
                'nullable',
                'date',
            ],
            'target_end_date' => [
                'nullable',
                'date',
                'after_or_equal:target_start_date',
            ],
            'actual_start_date' => [
                'nullable',
                'date',
            ],
            'actual_end_date' => [
                'nullable',
                'date',
                'after_or_equal:actual_start_date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    private function allowedNextStatuses(DevelopmentPlan $plan): array
    {
        return match ($plan->status) {
            DevelopmentPlan::STATUS_USULAN => [
                DevelopmentPlan::STATUS_DISETUJUI,
                DevelopmentPlan::STATUS_DITUNDA,
                DevelopmentPlan::STATUS_DIBATALKAN,
            ],

            DevelopmentPlan::STATUS_DISETUJUI => [
                DevelopmentPlan::STATUS_DALAM_PROSES,
                DevelopmentPlan::STATUS_DITUNDA,
                DevelopmentPlan::STATUS_DIBATALKAN,
            ],

            DevelopmentPlan::STATUS_DALAM_PROSES => [
                DevelopmentPlan::STATUS_SELESAI,
                DevelopmentPlan::STATUS_DITUNDA,
                DevelopmentPlan::STATUS_DIBATALKAN,
            ],

            DevelopmentPlan::STATUS_DITUNDA => [
                DevelopmentPlan::STATUS_DISETUJUI,
                DevelopmentPlan::STATUS_DALAM_PROSES,
                DevelopmentPlan::STATUS_DIBATALKAN,
            ],

            default => [],
        };
    }

    private function authorizeProgressAccess(DevelopmentPlan $plan, $user): void
    {
        if (! $this->canUpdateProgress($plan, $user)) {
            abort(403);
        }

        $this->authorizeView($plan, $user);
    }

    private function canUpdateProgress(DevelopmentPlan $plan, $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->canManage($user)) {
            return true;
        }

        $employee = $user->employee;

        if (! $employee) {
            return false;
        }

        return (int) $plan->pic_employee_id === (int) $employee->id;
    }

    private function isPic(DevelopmentPlan $plan, $user): bool
    {
        if (! $user || ! $user->employee) {
            return false;
        }

        return (int) $plan->pic_employee_id === (int) $user->employee->id;
    }

    private function availableUnits($user)
    {
        if ($this->isAdmin($user)) {
            return Unit::orderBy('name')->get();
        }

        $employee = $user->employee;

        if (! $employee || ! $employee->unit_id) {
            abort(403, 'Akun ini belum terhubung dengan unit.');
        }

        return Unit::where('id', $employee->unit_id)->get();
    }

    private function availableEmployees($user)
    {
        if ($this->isAdmin($user)) {
            return Employee::with('unit')
                ->orderBy('name')
                ->get();
        }

        $employee = $user->employee;

        if (! $employee || ! $employee->unit_id) {
            abort(403, 'Akun ini belum terhubung dengan unit.');
        }

        return Employee::with('unit')
            ->where('unit_id', $employee->unit_id)
            ->orderBy('name')
            ->get();
    }

    private function authorizeView(DevelopmentPlan $plan, $user): void
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

        if ((int) $plan->unit_id !== (int) $employee->unit_id) {
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

    private function authorizeManagePlan(DevelopmentPlan $plan, $user): void
    {
        $this->authorizeManage($user);
        $this->authorizeView($plan, $user);
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