<?php

namespace App\Livewire\Admin\MasterData\Server;

use App\Models\Server;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $serverId;
    public $name;
    public $ip_address;
    public $domain;
    public $location;
    public $description;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'ip_address' => 'nullable|string|max:100',
            'domain' => 'nullable|string|max:150',
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
        $this->name = $server->name;
        $this->ip_address = $server->ip_address;
        $this->domain = $server->domain;
        $this->location = $server->location;
        $this->description = $server->description;
        $this->is_active = (bool) $server->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Server::updateOrCreate(
            ['id' => $this->serverId],
            [
                'name' => $this->name,
                'ip_address' => $this->ip_address,
                'domain' => $this->domain,
                'location' => $this->location,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->isEdit ? 'Server berhasil diperbarui.' : 'Server berhasil ditambahkan.');

        $this->closeModal();
    }

    public function delete($id)
    {
        Server::findOrFail($id)->delete();

        session()->flash('success', 'Server berhasil dihapus.');
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
            'name',
            'ip_address',
            'domain',
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
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                        ->orWhere('domain', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.master-data.server.index', [
            'servers' => $servers,
        ])->layout('layouts.app');
    }
}