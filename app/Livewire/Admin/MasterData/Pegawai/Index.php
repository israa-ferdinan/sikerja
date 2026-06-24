<?php

namespace App\Livewire\Admin\MasterData\Pegawai;

use App\Models\Employee;
use App\Models\Unit;
use App\Services\ActivityLogger;
use App\Models\Position;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $employeeId;
    public $unit_id;
    public $name;
    public $nip;
    public $phone;
    public $email;
    public $is_active = true;
    public ?int $position_id = null;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:150',
            'nip' => 'nullable|string|max:50',
            'position_id' => ['nullable', 'exists:positions,id'],
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
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
        $pegawai = Employee::findOrFail($id);

        $this->employeeId = $pegawai->id;
        $this->unit_id = $pegawai->unit_id;
        $this->name = $pegawai->name;
        $this->nip = $pegawai->nip;
        $this->position_id = $pegawai->position_id;
        $this->phone = $pegawai->phone;
        $this->email = $pegawai->email;
        $this->is_active = (bool) $pegawai->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'unit_id' => $this->unit_id,
            'name' => $this->name,
            'nip' => $this->nip,
            'position_id' => $this->position_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit && $this->employeeId) {
            $employee = Employee::findOrFail($this->employeeId);

            $oldValues = $employee->toArray();

            $employee->update($data);

            ActivityLogger::log(
                module: 'master_employee',
                action: 'update',
                description: 'Mengubah data pegawai ' . $employee->name,
                subject: $employee,
                oldValues: $oldValues,
                newValues: $employee->fresh()->toArray()
            );

            $message = 'Pegawai berhasil diperbarui.';
        } else {
            $employee = Employee::create($data);

            ActivityLogger::log(
                module: 'master_employee',
                action: 'create',
                description: 'Menambahkan data pegawai ' . $employee->name,
                subject: $employee,
                newValues: $employee->fresh()->toArray()
            );

            $message = 'Pegawai berhasil ditambahkan.';
        }

        $this->closeModal();

        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function delete($id)
    {
        $employee = Employee::with(['unit', 'jobPosition', 'duties'])->findOrFail($id);

        $oldValues = $employee->toArray();

        ActivityLogger::log(
            module: 'master_employee',
            action: 'delete',
            description: 'Menghapus data pegawai ' . $employee->name,
            subject: $employee,
            oldValues: $oldValues
        );

        $employee->delete();

        $this->dispatch('toast', type: 'success', message: 'Pegawai berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'employeeId',
            'unit_id',
            'name',
            'nip',
            'position_id',
            'phone',
            'email',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $pegawais = Employee::query()
            ->with(['unit', 'jobPosition', 'duties'])
            ->withCount('duties')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('unit', function ($unitQuery) {
                            $unitQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('jobPosition', function ($positionQuery) {
                            $positionQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.master-data.pegawai.index', [
            'pegawais' => $pegawais,
            'units' => $units,
            'positions' => $positions,
        ])->layout('layouts.app');
    }
}