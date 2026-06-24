<?php

namespace App\Livewire\Admin\MasterData\Pegawai;

use App\Models\Employee;
use App\Models\JobDuty;
use Livewire\Component;

class ManageDuties extends Component
{
    public Employee $employee;

    public array $selectedDuties = [];

    public function mount(Employee $employee): void
    {
        $this->employee = $employee->load(['unit', 'position', 'duties']);

        $this->selectedDuties = $this->employee->duties
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    public function save(): void
    {
        $this->validate([
            'selectedDuties' => ['array'],
            'selectedDuties.*' => ['exists:duties,id'],
        ]);

        $syncData = [];

        foreach ($this->selectedDuties as $dutyId) {
            $syncData[$dutyId] = [
                'is_primary' => false,
                'notes' => null,
            ];
        }

        $this->employee->duties()->sync($syncData);

        $this->employee->load('duties');

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Tupoksi pegawai berhasil diperbarui.'
        );
    }

    public function render()
    {
        $duties = JobDuty::query()
            ->with('unit')
            ->when($this->employee->unit_id, function ($query) {
                $query->where(function ($q) {
                    $q->where('unit_id', $this->employee->unit_id)
                        ->orWhereNull('unit_id');
                });
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.master-data.pegawai.manage-duties', [
            'duties' => $duties,
        ]);
    }
}