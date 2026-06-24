<?php

namespace App\Livewire\Admin\MasterData\Aplikasi;

use App\Models\Application;
use App\Models\Unit;
use App\Models\Server;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $applicationId;
    public $unit_id;
    public $server_id;
    public $name;
    public $url;
    public $description;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'unit_id' => 'nullable|exists:units,id',
            'server_id' => 'nullable|exists:servers,id',
            'name' => 'required|string|max:150',
            'url' => 'nullable|string|max:255',
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
        $application = Application::findOrFail($id);

        $this->applicationId = $application->id;
        $this->unit_id = $application->unit_id;
        $this->server_id = $application->server_id;
        $this->name = $application->name;
        $this->url = $application->url;
        $this->description = $application->description;
        $this->is_active = (bool) $application->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        $data = [
            'unit_id' => $validated['unit_id'] ?: null,
            'server_id' => $validated['server_id'] ?: null,
            'name' => $validated['name'],
            'url' => $validated['url'] ?: null,
            'description' => $validated['description'] ?: null,
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($this->isEdit && $this->applicationId) {
            $application = Application::findOrFail($this->applicationId);
            $oldValues = $application->toArray();

            $application->update($data);

            ActivityLogger::log(
                module: 'master_application',
                action: 'update',
                description: 'Mengubah data aplikasi ' . $application->name,
                subject: $application,
                oldValues: $oldValues,
                newValues: $application->fresh(['unit', 'server'])->toArray()
            );

            $this->dispatch('toast', type: 'success', message: 'Aplikasi berhasil diperbarui.');
        } else {
            $application = Application::create($data);

            ActivityLogger::log(
                module: 'master_application',
                action: 'create',
                description: 'Menambahkan data aplikasi ' . $application->name,
                subject: $application,
                newValues: $application->fresh(['unit', 'server'])->toArray()
            );

            $this->dispatch('toast', type: 'success', message: 'Aplikasi berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $application = Application::with(['unit', 'server'])->findOrFail($id);
        $oldValues = $application->toArray();

        ActivityLogger::log(
            module: 'master_application',
            action: 'delete',
            description: 'Menghapus data aplikasi ' . $application->name,
            subject: $application,
            oldValues: $oldValues
        );

        $application->delete();

        $this->dispatch('toast', type: 'success', message: 'Aplikasi berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'applicationId',
            'unit_id',
            'server_id',
            'name',
            'url',
            'description',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $applications = Application::query()
            ->with(['unit', 'server'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('url', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('unit', function ($unitQuery) {
                            $unitQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('server', function ($serverQuery) {
                            $serverQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('hostname', 'like', '%' . $this->search . '%')
                                ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $servers = Server::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.master-data.aplikasi.index', [
            'applications' => $applications,
            'units' => $units,
            'servers' => $servers,
        ])->layout('layouts.app');
    }
}
