<?php

namespace App\Livewire\Admin\DutyDelegations;

use App\Models\DutyDelegation;
use App\Models\Employee;
use App\Models\JobDuty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

use App\Services\ActivityLogger;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $date = '';

    public $showFormModal = false;
    public $editingId = null;

    public $owner_employee_id = null;
    public $delegate_employee_id = null;
    public $duty_id = null;
    public $start_date = null;
    public $end_date = null;
    public $notes = null;
    public $is_active = '1';

    public array $ownerDuties = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'date' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $delegations = DutyDelegation::query()
            ->with([
                'duty',
                'ownerEmployee.unit',
                'ownerEmployee.jobPosition',
                'delegateEmployee.unit',
                'delegateEmployee.jobPosition',
                'createdBy',
            ])

            ->when($this->isKanit(), function ($query) {
                $unitId = $this->currentEmployeeUnitId();

                $query->whereHas('ownerEmployee', function ($ownerQuery) use ($unitId) {
                    $ownerQuery->where('unit_id', $unitId);
                })
                ->whereHas('delegateEmployee', function ($delegateQuery) use ($unitId) {
                    $delegateQuery->where('unit_id', $unitId);
                });
            })

            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('duty', function ($dq) {
                        $dq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('ownerEmployee', function ($eq) {
                        $eq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('delegateEmployee', function ($eq) {
                        $eq->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_active', (bool) $this->status);
            })
            ->when($this->date, function ($query) {
                $query->whereDate('start_date', '<=', $this->date)
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $this->date);
                    });
            })
            ->latest()
            ->paginate(10);

        $employees = Employee::query()
            ->with(['unit', 'jobPosition'])
            ->where('is_active', true)
            ->when($this->isKanit(), function ($query) {
                $query->where('unit_id', $this->currentEmployeeUnitId());
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.duty-delegations.index', [
            'delegations' => $delegations,
            'employees' => $employees,
        ]);
    }

    protected function rules()
    {
        return [
            'owner_employee_id' => ['required', 'exists:employees,id'],
            'delegate_employee_id' => ['required', 'exists:employees,id', 'different:owner_employee_id'],
            'duty_id' => ['required', 'exists:duties,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'in:0,1'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();

        $this->ownerDuties = [];
        $this->start_date = now()->toDateString();
        $this->is_active = '1';
        $this->showFormModal = true;
    }

    public function updatedOwnerEmployeeId($value): void
    {
        $this->owner_employee_id = $value ?: null;
        $this->duty_id = null;

        $this->loadOwnerDuties();
    }

    private function loadOwnerDuties(): void
    {
        if (!$this->owner_employee_id) {
            $this->ownerDuties = [];
            return;
        }

        $dutyIds = DB::table('employee_duty')
            ->where('employee_id', $this->owner_employee_id)
            ->pluck('duty_id')
            ->toArray();

        $this->ownerDuties = JobDuty::query()
            ->whereIn('id', $dutyIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($duty) => [
                'id' => $duty->id,
                'name' => $duty->name,
            ])
            ->toArray();
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->isKanit()) {
            $unitId = $this->currentEmployeeUnitId();

            $ownerInUnit = Employee::query()
                ->where('id', $this->owner_employee_id)
                ->where('unit_id', $unitId)
                ->exists();

            $delegateInUnit = Employee::query()
                ->where('id', $this->delegate_employee_id)
                ->where('unit_id', $unitId)
                ->exists();

            if (! $ownerInUnit || ! $delegateInUnit) {
                $this->addError('owner_employee_id', 'Kanit hanya dapat membuat delegasi untuk pegawai dalam unitnya.');
                return;
            }
        }

        $ownerHasDuty = Employee::where('id', $this->owner_employee_id)
            ->whereHas('duties', function ($query) {
                $query->where('duties.id', $this->duty_id);
            })
            ->exists();

        if (! $ownerHasDuty) {
            $this->addError('duty_id', 'Tupoksi ini bukan milik pegawai pemilik yang dipilih.');
            return;
        }

        if ($this->is_active === '1') {
            $duplicateActiveDelegation = DutyDelegation::query()
                ->where('duty_id', $this->duty_id)
                ->where('owner_employee_id', $this->owner_employee_id)
                ->where('delegate_employee_id', $this->delegate_employee_id)
                ->where('is_active', true)
                ->when($this->editingId, function ($query) {
                    $query->where('id', '!=', $this->editingId);
                })
                ->exists();

            if ($duplicateActiveDelegation) {
                $this->addError('delegate_employee_id', 'Delegasi aktif untuk pegawai dan tupoksi ini sudah ada.');
                return;
            }
        }

        $data = [
            'duty_id' => $this->duty_id,
            'owner_employee_id' => $this->owner_employee_id,
            'delegate_employee_id' => $this->delegate_employee_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active === '1',
            'notes' => $this->notes,
        ];

        $isEditing = (bool) $this->editingId;

        if ($isEditing) {
            $delegation = DutyDelegation::findOrFail($this->editingId);

            if (! $this->canManageDelegation($delegation)) {
                abort(403);
            }

            $oldValues = $delegation->toArray();

            $delegation->update($data);

            ActivityLogger::log(
                module: 'duty_delegation',
                action: 'update',
                description: 'Mengubah delegasi tupoksi',
                subject: $delegation,
                oldValues: $oldValues,
                newValues: $delegation->fresh()->toArray()
            );
        } else {
            $data['created_by'] = auth()->id();

            $delegation = DutyDelegation::create($data);

            ActivityLogger::log(
                module: 'duty_delegation',
                action: 'create',
                description: 'Membuat delegasi tupoksi',
                subject: $delegation,
                newValues: $delegation->fresh()->toArray()
            );
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();

        session()->flash(
            'success',
            $isEditing
                ? 'Delegasi tupoksi berhasil diperbarui.'
                : 'Delegasi tupoksi berhasil disimpan.'
        );
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'owner_employee_id',
            'delegate_employee_id',
            'duty_id',
            'start_date',
            'end_date',
            'notes',
            'is_active',
        ]);

        $this->ownerDuties = [];
        $this->is_active = '1';

        $this->resetValidation();
    }

    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }
    public function openEditModal($id): void
    {
        $delegation = DutyDelegation::findOrFail($id);

        $delegation->load(['ownerEmployee', 'delegateEmployee']);

        if (! $this->canManageDelegation($delegation)) {
            abort(403);
        }

        $this->resetForm();

        $this->editingId = $delegation->id;
        $this->owner_employee_id = $delegation->owner_employee_id;
        $this->delegate_employee_id = $delegation->delegate_employee_id;
        $this->duty_id = $delegation->duty_id;
        $this->start_date = $delegation->start_date?->format('Y-m-d');
        $this->end_date = $delegation->end_date?->format('Y-m-d');
        $this->notes = $delegation->notes;
        $this->is_active = $delegation->is_active ? '1' : '0';

        $this->loadOwnerDuties();

        $this->showFormModal = true;
    }

    public function toggleStatus($id): void
    {
        $delegation = DutyDelegation::with(['ownerEmployee', 'delegateEmployee'])->findOrFail($id);

        if (! $this->canManageDelegation($delegation)) {
            abort(403);
        }

        $oldValues = $delegation->toArray();

        $delegation->update([
            'is_active' => ! $delegation->is_active,
        ]);

        $freshDelegation = $delegation->fresh();

        ActivityLogger::log(
            module: 'duty_delegation',
            action: $freshDelegation->is_active ? 'activate' : 'deactivate',
            description: $freshDelegation->is_active
                ? 'Mengaktifkan delegasi tupoksi'
                : 'Menonaktifkan delegasi tupoksi',
            subject: $freshDelegation,
            oldValues: $oldValues,
            newValues: $freshDelegation->toArray()
        );

        session()->flash(
            'success',
            $freshDelegation->is_active
                ? 'Delegasi tupoksi berhasil diaktifkan.'
                : 'Delegasi tupoksi berhasil dinonaktifkan.'
        );
    }

    public function delete($id): void
    {
        $delegation = DutyDelegation::with(['ownerEmployee', 'delegateEmployee'])->findOrFail($id);

        if (! $this->canManageDelegation($delegation)) {
            abort(403);
        }

        /* $oldValues = $delegation->toArray(); */

        $delegation->delete();

        session()->flash('success', 'Delegasi tupoksi berhasil dihapus.');
    }

    private function currentUserRole(): ?string
    {
        return auth()->user()?->role?->name;
    }

    private function isKanit(): bool
    {
        return $this->currentUserRole() === 'kanit';
    }

    private function currentEmployeeUnitId(): ?int
    {
        return auth()->user()?->employee?->unit_id;
    }

    private function canManageDelegation(DutyDelegation $delegation): bool
    {
        if (! $this->isKanit()) {
            return true;
        }

        $unitId = $this->currentEmployeeUnitId();

        return $delegation->ownerEmployee?->unit_id === $unitId
            && $delegation->delegateEmployee?->unit_id === $unitId;
    }
}