<?php

namespace App\Livewire\Admin\MasterData\Tupoksi;

use App\Models\Tupoksi;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $tupoksiId;
    public $unit_id;
    public $name;
    public $description;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:150',
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
        $tupoksi = Tupoksi::findOrFail($id);

        $this->tupoksiId = $tupoksi->id;
        $this->unit_id = $tupoksi->unit_id;
        $this->name = $tupoksi->name;
        $this->description = $tupoksi->description;
        $this->is_active = (bool) $tupoksi->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Tupoksi::updateOrCreate(
            ['id' => $this->tupoksiId],
            [
                'unit_id' => $this->unit_id,
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->isEdit ? 'Tupoksi berhasil diperbarui.' : 'Tupoksi berhasil ditambahkan.');

        $this->closeModal();
    }

    public function delete($id)
    {
        Tupoksi::findOrFail($id)->delete();

        session()->flash('success', 'Tupoksi berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'tupoksiId',
            'unit_id',
            'name',
            'description',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $tupoksis = Tupoksi::query()
            ->with('unit')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('unit', function ($unitQuery) {
                            $unitQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.master-data.tupoksi.index', [
            'tupoksis' => $tupoksis,
            'units' => $units,
        ])->layout('layouts.app');
    }
}