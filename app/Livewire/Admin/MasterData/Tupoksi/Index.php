<?php

namespace App\Livewire\Admin\MasterData\Tupoksi;

use App\Models\DutyClassification;
use App\Models\JobDuty;
use App\Models\Unit;
use App\Services\ActivityLogger;
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

    public ?int $duty_classification_id = null;
    public string $object_type = 'none';

    protected function rules()
    {
        return [
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],

            'duty_classification_id' => ['nullable', 'exists:duty_classifications,id'],
            'object_type' => ['required', 'in:none,server,application,facility,document,user_service,other'],
        ];
    }

    protected function messages(): array
    {
        return [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit yang dipilih tidak valid.',
            'name.required' => 'Nama tupoksi wajib diisi.',
            'name.max' => 'Nama tupoksi maksimal 150 karakter.',

            'duty_classification_id.exists' => 'Klasifikasi tupoksi tidak valid.',
            'object_type.required' => 'Jenis objek pekerjaan wajib dipilih.',
            'object_type.in' => 'Jenis objek pekerjaan tidak valid.',
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
        $tupoksi = JobDuty::findOrFail($id);

        $this->tupoksiId = $tupoksi->id;
        $this->unit_id = $tupoksi->unit_id;
        $this->name = $tupoksi->name;
        $this->description = $tupoksi->description;
        $this->is_active = (bool) $tupoksi->is_active;

        $this->duty_classification_id = $tupoksi->duty_classification_id;
        $this->object_type = $tupoksi->object_type ?: 'none';

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        $validated['unit_id'] = $validated['unit_id'] ?: null;
        $validated['is_active'] = (bool) $validated['is_active'];

        // Konsep baru:
        // Master Tupoksi hanya menyimpan kategori objek pekerjaan.
        // Detail server/aplikasi/nama objek dicatat saat input laporan harian.
        $validated['server_id'] = null;
        $validated['application_id'] = null;
        $validated['object_name'] = null;

        if ($this->isEdit && $this->tupoksiId) {
            $duty = JobDuty::findOrFail($this->tupoksiId);

            $oldValues = $duty->toArray();

            $duty->update($validated);
            $duty->refresh();

            ActivityLogger::log(
                module: 'master_duty',
                action: 'update',
                description: 'Mengubah data tupoksi ' . $duty->name,
                subject: $duty,
                oldValues: $oldValues,
                newValues: $duty->toArray()
            );

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'Tupoksi berhasil diperbarui.',
            ]);
        } else {
            $duty = JobDuty::create($validated);

            ActivityLogger::log(
                module: 'master_duty',
                action: 'create',
                description: 'Menambahkan data tupoksi ' . $duty->name,
                subject: $duty,
                newValues: $duty->fresh()->toArray()
            );

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'Tupoksi berhasil ditambahkan.',
            ]);
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $duty = JobDuty::findOrFail($id);

        $oldValues = $duty->toArray();

        ActivityLogger::log(
            module: 'master_duty',
            action: 'delete',
            description: 'Menghapus data tupoksi ' . $duty->name,
            subject: $duty,
            oldValues: $oldValues
        );

        $duty->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Tupoksi berhasil dihapus.',
        ]);
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
            'duty_classification_id',
            'object_type',
        ]);

        $this->is_active = true;
        $this->object_type = 'none';

        $this->resetValidation();
    }

    public function getObjectTypeOptionsProperty(): array
    {
        return [
            'none' => 'Tidak Ada Objek Khusus',
            'server' => 'Server',
            'application' => 'Aplikasi',
            'facility' => 'Perangkat / Fasilitas',
            'document' => 'Dokumen / Administrasi',
            'user_service' => 'Layanan Pengguna',
            'other' => 'Lainnya',
        ];
    }

    public function render()
    {
        $tupoksis = JobDuty::query()
            ->with(['unit', 'classification'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('unit', function ($unitQuery) {
                            $unitQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('classification', function ($classificationQuery) {
                            $classificationQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $classifications = DutyClassification::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.master-data.tupoksi.index', [
            'tupoksis' => $tupoksis,
            'units' => $units,
            'classifications' => $classifications,
        ])->layout('layouts.app');
    }
}