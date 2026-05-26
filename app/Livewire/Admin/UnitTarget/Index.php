<?php

namespace App\Livewire\Admin\UnitTarget;

use App\Models\DutyClassification;
use App\Models\Unit;
use App\Models\UnitTarget;
use App\Models\Application;
use App\Models\Server;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;


class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterYear = null;
    public string $filterPeriodType = '';
    public ?int $filterQuarter = null;
    public ?int $filterUnitId = null;
    public ?int $filterClassificationId = null;
    public string $filterStatus = '';

    public ?int $targetId = null;

    public ?int $unit_id = null;
    public ?int $duty_classification_id = null;

    public string $target_name = '';
    public ?string $target_description = null;

    public ?int $target_year = null;
    public string $period_type = 'annual';
    public ?int $quarter = null;

    public string $object_type = 'none';
    public ?int $server_id = null;
    public ?int $application_id = null;
    public ?string $object_name = null;

    public int $target_quantity = 1;
    public string $target_unit = 'kali';

    public bool $is_active = true;

    public bool $showForm = false;
    public bool $isEdit = false;
    public bool $showDetail = false;
    public ?int $detailTargetId = null;
    public int $detailReportsLimit = 10;

    public function mount(): void
    {
        $this->filterYear = now()->year;
        $this->target_year = now()->year;

        if ($this->isKanit()) {
            $this->unit_id = $this->kanitUnitId();
            $this->filterUnitId = $this->kanitUnitId();
        }
    }

    protected function rules(): array
    {
        return [
            'unit_id' => [
                'required',
                'exists:units,id',
            ],
            'duty_classification_id' => [
                'nullable',
                'exists:duty_classifications,id',
            ],
            'target_name' => [
                'required',
                'string',
                'max:255',
            ],
            'target_description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'target_year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
            ],
            'period_type' => [
                'required',
                'in:annual,quarterly',
            ],
            'quarter' => [
                'nullable',
                'integer',
                'in:1,2,3,4',
                'required_if:period_type,quarterly',
            ],
            'object_type' => [
                'required',
                'in:none,server,application,manual',
            ],
            'server_id' => [
                'nullable',
                'exists:servers,id',
                'required_if:object_type,server',
            ],
            'application_id' => [
                'nullable',
                'exists:applications,id',
                'required_if:object_type,application',
            ],
            'object_name' => [
                'nullable',
                'string',
                'max:255',
                'required_if:object_type,manual',
            ],
            'target_quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'target_unit' => [
                'required',
                'string',
                'max:50',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',

            'target_name.required' => 'Nama target wajib diisi.',
            'target_name.max' => 'Nama target maksimal 255 karakter.',

            'target_description.max' => 'Deskripsi maksimal 1000 karakter.',

            'target_year.required' => 'Tahun target wajib diisi.',
            'target_year.integer' => 'Tahun target harus berupa angka.',
            'target_year.min' => 'Tahun target tidak valid.',
            'target_year.max' => 'Tahun target tidak valid.',

            'period_type.required' => 'Periode target wajib dipilih.',
            'period_type.in' => 'Periode target tidak valid.',

            'quarter.required_if' => 'Triwulan wajib dipilih jika periode target adalah triwulan.',
            'quarter.in' => 'Triwulan tidak valid.',

            'object_type.required' => 'Jenis objek wajib dipilih.',
            'object_type.in' => 'Jenis objek tidak valid.',

            'server_id.required_if' => 'Server wajib dipilih jika jenis objek adalah server.',
            'server_id.exists' => 'Server tidak valid.',

            'application_id.required_if' => 'Aplikasi wajib dipilih jika jenis objek adalah aplikasi.',
            'application_id.exists' => 'Aplikasi tidak valid.',

            'object_name.required_if' => 'Nama objek manual wajib diisi.',
            'object_name.max' => 'Nama objek manual maksimal 255 karakter.',

            'target_quantity.required' => 'Jumlah target wajib diisi.',
            'target_quantity.integer' => 'Jumlah target harus berupa angka.',
            'target_quantity.min' => 'Jumlah target minimal 1.',

            'target_unit.required' => 'Satuan target wajib diisi.',
            'target_unit.max' => 'Satuan target maksimal 50 karakter.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterYear(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPeriodType(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPeriodType(): void
    {
        if ($this->filterPeriodType !== 'quarterly') {
            $this->filterQuarter = null;
        }
    }

    public function updatingFilterUnitId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClassificationId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterQuarter(): void
    {
        $this->resetPage();
    }

    public function updatedPeriodType(): void
    {
        if ($this->period_type === 'annual') {
            $this->quarter = null;
        }
    }

    public function updatedObjectType(): void
    {
        if ($this->object_type === 'none') {
            $this->server_id = null;
            $this->application_id = null;
            $this->object_name = null;
        }

        if ($this->object_type === 'server') {
            $this->application_id = null;
            $this->object_name = null;
        }

        if ($this->object_type === 'application') {
            $this->server_id = null;
            $this->object_name = null;
        }

        if ($this->object_type === 'manual') {
            $this->server_id = null;
            $this->application_id = null;
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->closeDetail();

        $this->showForm = true;
        $this->isEdit = false;
        $this->is_active = true;
        $this->target_year = now()->year;
        $this->period_type = 'annual';
        $this->object_type = 'none';
        $this->target_quantity = 1;
        $this->target_unit = 'kali';

        if ($this->isKanit()) {
            $this->unit_id = $this->kanitUnitId();
        }
    }

    public function edit(int $id): void
    {
        $target = $this->targetQuery()
            ->whereKey($id)
            ->firstOrFail();

        $this->closeDetail();

        $this->targetId = $target->id;
        $this->unit_id = $target->unit_id;
        $this->duty_classification_id = $target->duty_classification_id;

        $this->target_name = $target->target_name;
        $this->target_description = $target->target_description;

        $this->target_year = (int) $target->target_year;
        $this->period_type = $target->period_type;
        $this->quarter = $target->quarter;

        $this->object_type = $target->object_type ?: 'none';
        $this->server_id = $target->server_id;
        $this->application_id = $target->application_id;
        $this->object_name = $target->object_name;

        $this->target_quantity = (int) $target->target_quantity;
        $this->target_unit = $target->target_unit ?: 'kali';

        $this->is_active = (bool) $target->is_active;

        $this->showForm = true;
        $this->isEdit = true;
    }

    public function openDetail(int $id): void
    {
        $target = $this->targetQuery()
            ->whereKey($id)
            ->firstOrFail();

        $this->detailTargetId = $target->id;
        $this->detailReportsLimit = 10;
        $this->showDetail = true;
        $this->showForm = false;

        $this->dispatch('scroll-to-target-detail');
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detailTargetId = null;
        $this->detailReportsLimit = 10;
    }

    public function loadMoreMatchingReports(): void
    {
        $this->detailReportsLimit += 10;
    }

    public function save(): void
    {
        if ($this->isKanit()) {
            $this->unit_id = $this->kanitUnitId();
        }

        if ($this->isKanit() && ! $this->kanitUnitId()) {
            $this->addError('unit_id', 'Akun Kanit belum terhubung dengan unit.');
            return;
        }

        $validated = $this->validate();

        if ($validated['period_type'] === 'annual') {
            $validated['quarter'] = null;
        }

        if ($validated['object_type'] === 'none') {
            $validated['server_id'] = null;
            $validated['application_id'] = null;
            $validated['object_name'] = null;
        }

        if ($validated['object_type'] === 'server') {
            $validated['application_id'] = null;
            $validated['object_name'] = null;
        }

        if ($validated['object_type'] === 'application') {
            $validated['server_id'] = null;
            $validated['object_name'] = null;
        }

        if ($validated['object_type'] === 'manual') {
            $validated['server_id'] = null;
            $validated['application_id'] = null;
        }

        $this->ensureTargetIsUnique($validated);

        if ($this->isEdit && $this->targetId) {
            $target = $this->targetQuery()
                ->whereKey($this->targetId)
                ->firstOrFail();

            $oldData = $target->toArray();

            $validated['updated_by'] = Auth::id();

            $target->update($validated);

            ActivityLogger::log(
                'unit_target',
                'update',
                'Mengubah target unit: ' . $target->target_name,
                $target,
                [
                    'old' => $oldData,
                    'new' => $target->fresh()->toArray(),
                ]
            );

            session()->flash('success', 'Target unit berhasil diperbarui.');
        } else {
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            $target = UnitTarget::create($validated);

            ActivityLogger::log(
                'unit_target',
                'create',
                'Menambahkan target unit: ' . $target->target_name,
                $target,
                [
                    'new' => $target->toArray(),
                ]
            );

            session()->flash('success', 'Target unit berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    private function ensureTargetIsUnique(array $validated): void
    {
        $exists = UnitTarget::query()
            ->where('unit_id', $validated['unit_id'])
            ->where('target_year', $validated['target_year'])
            ->where('period_type', $validated['period_type'])
            ->where(function ($query) use ($validated) {
                if ($validated['period_type'] === 'quarterly') {
                    $query->where('quarter', $validated['quarter']);
                } else {
                    $query->whereNull('quarter');
                }
            })
            ->where(function ($query) use ($validated) {
                if (! empty($validated['duty_classification_id'])) {
                    $query->where('duty_classification_id', $validated['duty_classification_id']);
                } else {
                    $query->whereNull('duty_classification_id');
                }
            })
            ->where('object_type', $validated['object_type'])
            ->where(function ($query) use ($validated) {
                if ($validated['object_type'] === 'server') {
                    $query->where('server_id', $validated['server_id']);
                } else {
                    $query->whereNull('server_id');
                }
            })
            ->where(function ($query) use ($validated) {
                if ($validated['object_type'] === 'application') {
                    $query->where('application_id', $validated['application_id']);
                } else {
                    $query->whereNull('application_id');
                }
            })
            ->where(function ($query) use ($validated) {
                if ($validated['object_type'] === 'manual') {
                    $query->where('object_name', $validated['object_name']);
                } else {
                    $query->whereNull('object_name');
                }
            })
            ->when($this->isEdit && $this->targetId, function ($query) {
                $query->whereKeyNot($this->targetId);
            })
            ->exists();

        if ($exists) {
            $this->addError(
                'target_name',
                'Target dengan kombinasi unit, tahun, periode, klasifikasi, dan objek tersebut sudah ada.'
            );

            throw \Illuminate\Validation\ValidationException::withMessages([
                'target_name' => 'Target dengan kombinasi unit, tahun, periode, klasifikasi, dan objek tersebut sudah ada.',
            ]);
        }
    }

    public function toggleActive(int $id): void
    {
        $target = $this->targetQuery()
            ->whereKey($id)
            ->firstOrFail();

        $oldData = $target->toArray();

        $target->update([
            'is_active' => ! $target->is_active,
            'updated_by' => Auth::id(),
        ]);

        ActivityLogger::log(
            'unit_target',
            'update',
            ($target->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . ' target unit: ' . $target->target_name,
            $target,
            [
                'old' => $oldData,
                'new' => $target->fresh()->toArray(),
            ]
        );

        session()->flash('success', 'Status target unit berhasil diperbarui.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterYear = now()->year;
        $this->filterPeriodType = '';
        $this->filterClassificationId = '';
        $this->filterStatus = '';
        $this->filterQuarter = null;

        if ($this->isAdmin()) {
            $this->filterUnitId = null;
        }

        if ($this->isKanit()) {
            $this->filterUnitId = $this->kanitUnitId();
        }

        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->reset([
        'targetId',
        'unit_id',
        'duty_classification_id',
        'target_name',
        'target_description',
        'target_year',
        'period_type',
        'quarter',
        'object_type',
        'server_id',
        'application_id',
        'object_name',
        'target_quantity',
        'target_unit',
        'is_active',
        'showForm',
        'isEdit',
    ]);

        $this->target_year = now()->year;
        $this->period_type = 'annual';
        $this->object_type = 'none';
        $this->target_quantity = 1;
        $this->target_unit = 'kali';
        $this->is_active = true;

        if ($this->isKanit()) {
            $this->unit_id = $this->kanitUnitId();
        }

        $this->resetValidation();
    }

    private function targetQuery()
    {
        return UnitTarget::query()
            ->with([
                'unit',
                'classification',
                'server',
                'application',
                'creator',
                'updater',
            ])
            ->when($this->isKanit(), function ($query) {
                $query->where('unit_id', $this->kanitUnitId());
            });
    }

    private function isAdmin(): bool
    {
        return (bool) Auth::user()?->isAdmin();
    }

    private function isKanit(): bool
    {
        return (bool) Auth::user()?->isKanit();
    }

    private function kanitUnitId(): ?int
    {
        return Auth::user()?->employee?->unit_id;
    }

    public function render()
    {
        $targets = $this->targetQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('target_name', 'like', '%' . $this->search . '%')
                        ->orWhere('target_description', 'like', '%' . $this->search . '%')
                        ->orWhere('object_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterYear, function ($query) {
                $query->where('target_year', $this->filterYear);
            })
            ->when($this->filterPeriodType, function ($query) {
                $query->where('period_type', $this->filterPeriodType);
            })
            ->when($this->filterPeriodType === 'quarterly' && $this->filterQuarter, function ($query) {
                $query->where('quarter', $this->filterQuarter);
            })
            ->when($this->filterUnitId && $this->isAdmin(), function ($query) {
                $query->where('unit_id', $this->filterUnitId);
            })
            ->when($this->filterClassificationId, function ($query) {
                $query->where('duty_classification_id', $this->filterClassificationId);
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->latest()
            ->paginate(10);

        $units = Unit::query()
            ->when($this->isKanit(), function ($query) {
                $query->whereKey($this->kanitUnitId());
            })
            ->orderBy('name')
            ->get();

        $classifications = DutyClassification::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $servers = Server::query()
            ->where('is_active', true)
            ->when($this->isKanit(), function ($query) {
                $query->where('unit_id', $this->kanitUnitId());
            })
            ->orderBy('name')
            ->get();

        $applications = Application::query()
            ->where('is_active', true)
            ->when($this->isKanit(), function ($query) {
                $query->where('unit_id', $this->kanitUnitId());
            })
            ->orderBy('name')
            ->get();

        $detailTarget = null;
        $matchingReports = collect();
        $matchingReportsTotal = 0;

        if ($this->showDetail && $this->detailTargetId) {
            $detailTarget = $this->targetQuery()
                ->whereKey($this->detailTargetId)
                ->first();

            if ($detailTarget) {
                $matchingReportsTotal = $detailTarget->matchingDailyReportsQuery()->count();

                $matchingReports = $detailTarget->matchingDailyReportsQuery()
                    ->with([
                        'employee',
                        'duty',
                    ])
                    ->latest('report_date')
                    ->limit($this->detailReportsLimit)
                    ->get();
            }
        }

        return view('livewire.admin.unit-target.index', [
            'targets' => $targets,
            'units' => $units,
            'classifications' => $classifications,
            'servers' => $servers,
            'applications' => $applications,
            'isAdmin' => $this->isAdmin(),
            'isKanit' => $this->isKanit(),
            'detailTarget' => $detailTarget,
            'matchingReports' => $matchingReports,
            'matchingReportsTotal' => $matchingReportsTotal,
        ]);
    }
}