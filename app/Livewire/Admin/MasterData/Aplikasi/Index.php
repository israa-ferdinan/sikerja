<?php

namespace App\Livewire\Admin\MasterData\Aplikasi;

use App\Models\Application;
use App\Models\Unit;
use App\Models\Server;
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
        $this->name = $application->name;
        $this->url = $application->url;
        $this->description = $application->description;
        $this->is_active = (bool) $application->is_active;
        $this->server_id = $application->server_id;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Application::updateOrCreate(
            ['id' => $this->applicationId],
            [
                'unit_id' => $this->unit_id ?: null,
                'server_id' => $this->server_id ?: null,
                'name' => $this->name,
                'url' => $this->url,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->isEdit ? 'Aplikasi berhasil diperbarui.' : 'Aplikasi berhasil ditambahkan.');

        $this->closeModal();
    }

    public function delete($id)
    {
        Application::findOrFail($id)->delete();

        session()->flash('success', 'Aplikasi berhasil dihapus.');
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
                                ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                                ->orWhere('domain', 'like', '%' . $this->search . '%');
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