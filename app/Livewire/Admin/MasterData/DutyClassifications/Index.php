<?php

namespace App\Livewire\Admin\MasterData\DutyClassifications;

use App\Models\DutyClassification;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $classificationId = null;

    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;

    public bool $showForm = false;
    public bool $isEdit = false;

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:duty_classifications,name,' . $this->classificationId,
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama klasifikasi wajib diisi.',
            'name.unique' => 'Nama klasifikasi sudah digunakan.',
            'name.max' => 'Nama klasifikasi maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->showForm = true;
        $this->isEdit = false;
        $this->is_active = true;
    }

    public function edit(int $id): void
    {
        $classification = DutyClassification::findOrFail($id);

        $this->classificationId = $classification->id;
        $this->name = $classification->name;
        $this->description = $classification->description;
        $this->is_active = (bool) $classification->is_active;

        $this->showForm = true;
        $this->isEdit = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEdit && $this->classificationId) {
            $classification = DutyClassification::findOrFail($this->classificationId);

            $oldData = $classification->toArray();

            $classification->update($validated);

            ActivityLogger::log(
                'duty_classification',
                'update',
                'Mengubah klasifikasi tupoksi: ' . $classification->name,
                $classification,
                [
                    'old' => $oldData,
                    'new' => $classification->fresh()->toArray(),
                ]
            );

            session()->flash('success', 'Klasifikasi tupoksi berhasil diperbarui.');
        } else {
            $classification = DutyClassification::create($validated);

            ActivityLogger::log(
                'duty_classification',
                'create',
                'Menambahkan klasifikasi tupoksi: ' . $classification->name,
                $classification,
                [
                    'new' => $classification->toArray(),
                ]
            );

            session()->flash('success', 'Klasifikasi tupoksi berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $classification = DutyClassification::findOrFail($id);

        $oldData = $classification->toArray();

        $classification->update([
            'is_active' => ! $classification->is_active,
        ]);

        ActivityLogger::log(
            'duty_classification',
            'update',
            ($classification->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . ' klasifikasi tupoksi: ' . $classification->name,
            $classification,
            [
                'old' => $oldData,
                'new' => $classification->fresh()->toArray(),
            ]
        );

        session()->flash('success', 'Status klasifikasi tupoksi berhasil diperbarui.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'classificationId',
            'name',
            'description',
            'is_active',
            'showForm',
            'isEdit',
        ]);

        $this->is_active = true;

        $this->resetValidation();
    }

    public function render()
    {
        $classifications = DutyClassification::query()
            ->withCount('duties')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.master-data.duty-classifications.index', [
            'classifications' => $classifications,
        ]);
    }
}