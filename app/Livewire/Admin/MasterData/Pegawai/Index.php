<?php

namespace App\Livewire\Admin\MasterData\Pegawai;

use App\Models\Pegawai;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $pegawaiId;
    public $unit_id;
    public $name;
    public $nip;
    public $position;
    public $phone;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:150',
            'nip' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
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
        $pegawai = Pegawai::findOrFail($id);

        $this->pegawaiId = $pegawai->id;
        $this->unit_id = $pegawai->unit_id;
        $this->name = $pegawai->name;
        $this->nip = $pegawai->nip;
        $this->position = $pegawai->position;
        $this->phone = $pegawai->phone;
        $this->is_active = (bool) $pegawai->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Pegawai::updateOrCreate(
            ['id' => $this->pegawaiId],
            [
                'unit_id' => $this->unit_id,
                'name' => $this->name,
                'nip' => $this->nip,
                'position' => $this->position,
                'phone' => $this->phone,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->isEdit ? 'Pegawai berhasil diperbarui.' : 'Pegawai berhasil ditambahkan.');

        $this->closeModal();
    }

    public function delete($id)
    {
        Pegawai::findOrFail($id)->delete();

        session()->flash('success', 'Pegawai berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'pegawaiId',
            'unit_id',
            'name',
            'nip',
            'position',
            'phone',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $pegawais = Pegawai::query()
            ->with('unit')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('position', 'like', '%' . $this->search . '%')
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

        return view('livewire.admin.master-data.pegawai.index', [
            'pegawais' => $pegawais,
            'units' => $units,
        ])->layout('layouts.app');
    }
}