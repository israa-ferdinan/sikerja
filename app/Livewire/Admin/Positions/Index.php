<?php

namespace App\Livewire\Admin\Positions;

use App\Models\Position;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

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
                Rule::unique('positions', 'name')->ignore($this->positionId),
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
            'name.unique' => 'Nama jabatan sudah digunakan.',
            'code.unique' => 'Kode jabatan sudah digunakan.',
            'code.max' => 'Kode jabatan maksimal 50 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        Position::updateOrCreate(
            ['id' => $this->positionId],
            $validated
        );

        session()->flash(
            'success',
            $this->isEditing
                ? 'Data jabatan berhasil diperbarui.'
                : 'Data jabatan berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $position = Position::findOrFail($id);

        $position->update([
            'is_active' => ! $position->is_active,
        ]);

        session()->flash('success', 'Status jabatan berhasil diperbarui.');
    }

    public function delete(int $id): void
    {
        $position = Position::withCount('employees')->findOrFail($id);

        if ($position->employees_count > 0) {
            session()->flash('error', 'Jabatan tidak bisa dihapus karena sudah digunakan oleh pegawai.');
            return;
        }

        $position->delete();

        session()->flash('success', 'Data jabatan berhasil dihapus.');
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