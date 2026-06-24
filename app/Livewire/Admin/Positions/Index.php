<?php

namespace App\Livewire\Admin\Positions;

use App\Models\Position;
use App\Services\ActivityLogger;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $positionId = null;

    public string $name = '';
    public ?string $code = null;
    public ?string $description = null;
    public bool $is_active = true;

    public bool $isEditing = false;
    public bool $showForm = false;

    protected string $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateForm(): void
    {
        $this->resetForm();

        $this->isEditing = false;
        $this->positionId = null;
        $this->showForm = true;
        $this->is_active = true;
    }

    public function edit(int $id): void
    {
        $position = Position::findOrFail($id);

        $this->positionId = $position->id;
        $this->name = $position->name;
        $this->code = $position->code;
        $this->description = $position->description;
        $this->is_active = (bool) $position->is_active;

        $this->isEditing = true;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('positions', 'code')->ignore($this->positionId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'is_active' => [
                'boolean',
            ],
        ], [
            'name.required' => 'Nama jabatan wajib diisi.',
            'name.max' => 'Nama jabatan maksimal 255 karakter.',
            'code.max' => 'Kode jabatan maksimal 50 karakter.',
            'code.unique' => 'Kode jabatan sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        $data = [
            'name' => $validated['name'],
            'code' => $validated['code'] ?: null,
            'description' => $validated['description'] ?: null,
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($this->isEditing && $this->positionId) {
            $position = Position::findOrFail($this->positionId);

            $oldValues = $position->toArray();

            $position->update($data);

            ActivityLogger::log(
                module: 'master_position',
                action: 'update',
                description: 'Mengubah data jabatan ' . $position->name,
                subject: $position,
                oldValues: $oldValues,
                newValues: $position->fresh()->toArray()
            );

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Jabatan berhasil diperbarui.'
            );
        } else {
            $position = Position::create($data);

            ActivityLogger::log(
                module: 'master_position',
                action: 'create',
                description: 'Menambahkan data jabatan ' . $position->name,
                subject: $position,
                newValues: $position->fresh()->toArray()
            );

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Jabatan berhasil ditambahkan.'
            );
        }

        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $position = Position::findOrFail($id);

        $oldValues = $position->toArray();

        $position->update([
            'is_active' => ! $position->is_active,
        ]);

        $freshPosition = $position->fresh();

        ActivityLogger::log(
            module: 'master_position',
            action: $freshPosition->is_active ? 'activate' : 'deactivate',
            description: $freshPosition->is_active
                ? 'Mengaktifkan jabatan ' . $freshPosition->name
                : 'Menonaktifkan jabatan ' . $freshPosition->name,
            subject: $freshPosition,
            oldValues: $oldValues,
            newValues: $freshPosition->toArray()
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: $freshPosition->is_active
                ? 'Jabatan berhasil diaktifkan.'
                : 'Jabatan berhasil dinonaktifkan.'
        );
    }

    public function delete($id): void
    {
        $position = Position::findOrFail($id);

        $oldValues = $position->toArray();

        ActivityLogger::log(
            module: 'master_position',
            action: 'delete',
            description: 'Menghapus data jabatan ' . $position->name,
            subject: $position,
            oldValues: $oldValues
        );

        $position->delete();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Jabatan berhasil dihapus.'
        );
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'positionId',
            'name',
            'code',
            'description',
            'is_active',
            'isEditing',
            'showForm',
        ]);

        $this->is_active = true;

        $this->resetValidation();
    }

    public function render()
    {
        $positions = Position::query()
            ->withCount('employees')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.positions.index', [
            'positions' => $positions,
        ]);
    }
}