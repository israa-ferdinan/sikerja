<?php

namespace App\Livewire\Admin\MasterData\Server;

use App\Models\Server;
use App\Models\Unit;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $serverId;
    public $unit_id;
    public $name;
    public $hostname;
    public $ip_address;
    public $server_type;
    public $location;
    public $description;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'unit_id' => 'nullable|exists:units,id',
            'name' => 'required|string|max:150',
            'hostname' => 'nullable|string|max:150',
            'ip_address' => 'nullable|string|max:100',
            'server_type' => 'nullable|string|max:150',
            'location' => 'nullable|string|max:150',
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
        $server = Server::findOrFail($id);

        $this->serverId = $server->id;
        $this->unit_id = $server->unit_id;
        $this->name = $server->name;
        $this->hostname = $server->hostname;
        $this->ip_address = $server->ip_address;
        $this->server_type = $server->server_type;
        $this->location = $server->location;
        $this->description = $server->description;
        $this->is_active = (bool) $server->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        $data = [
            'unit_id' => $validated['unit_id'] ?: null,
            'name' => $validated['name'],
            'hostname' => $validated['hostname'] ?: null,
            'ip_address' => $validated['ip_address'] ?: null,
            'server_type' => $validated['server_type'] ?: null,
            'location' => $validated['location'] ?: null,
            'description' => $validated['description'] ?: null,
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($this->isEdit && $this->serverId) {
            $server = Server::findOrFail($this->serverId);
            $oldValues = $server->toArray();

            $server->update($data);

            ActivityLogger::log(
                module: 'master_server',
                action: 'update',
                description: 'Mengubah data server ' . $server->name,
                subject: $server,
                oldValues: $oldValues,
                newValues: $server->fresh(['unit'])->toArray()
            );

            $this->dispatch('toast', type: 'success', message: 'Server berhasil diperbarui.');
        } else {
            $server = Server::create($data);

            ActivityLogger::log(
                module: 'master_server',
                action: 'create',
                description: 'Menambahkan data server ' . $server->name,
                subject: $server,
                newValues: $server->fresh(['unit'])->toArray()
            );

            $this->dispatch('toast', type: 'success', message: 'Server berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $server = Server::with('unit')->findOrFail($id);
        $oldValues = $server->toArray();

        ActivityLogger::log(
            module: 'master_server',
            action: 'delete',
            description: 'Menghapus data server ' . $server->name,
            subject: $server,
            oldValues: $oldValues
        );

        $server->delete();

        $this->dispatch('toast', type: 'success', message: 'Server berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'serverId',
            'unit_id',
            'name',
            'hostname',
            'ip_address',
            'server_type',
            'location',
            'description',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $servers = Server::query()
            ->with('unit')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('hostname', 'like', '%' . $this->search . '%')
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                        ->orWhere('server_type', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%')
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

        return view('livewire.admin.master-data.server.index', [
            'servers' => $servers,
            'units' => $units,
        ])->layout('layouts.app');
    }
}
