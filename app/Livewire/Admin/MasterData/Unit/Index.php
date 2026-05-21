<?php

namespace App\Livewire\Admin\MasterData\Unit;

use App\Models\Unit;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $unitId;
    public $name;
    public $code;
    public $description;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $unit = Unit::findOrFail($id);

        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->code = $unit->code;
        $this->description = $unit->description;
        $this->is_active = (bool) $unit->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit && $this->unitId) {
            $unit = Unit::findOrFail($this->unitId);

            $oldValues = $unit->toArray();

            $unit->update($data);

            ActivityLogger::log(
                module: 'master_unit',
                action: 'update',
                description: 'Mengubah data unit ' . $unit->name,
                subject: $unit,
                oldValues: $oldValues,
                newValues: $unit->fresh()->toArray()
            );

            session()->flash('success', 'Unit berhasil diperbarui.');
        } else {
            $unit = Unit::create($data);

            ActivityLogger::log(
                module: 'master_unit',
                action: 'create',
                description: 'Menambahkan data unit ' . $unit->name,
                subject: $unit,
                newValues: $unit->fresh()->toArray()
            );

            session()->flash('success', 'Unit berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $unit = Unit::findOrFail($id);

        $oldValues = $unit->toArray();

        ActivityLogger::log(
            module: 'master_unit',
            action: 'delete',
            description: 'Menghapus data unit ' . $unit->name,
            subject: $unit,
            oldValues: $oldValues
        );

        $unit->delete();

        session()->flash('success', 'Unit berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'unitId',
            'name',
            'code',
            'description',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $units = Unit::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.master-data.unit.index', [
            'units' => $units,
        ])->layout('layouts.app');
    }
}