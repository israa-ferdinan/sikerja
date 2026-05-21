<?php

namespace App\Livewire\Admin\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $userId = '';
    public $roleId = '';
    public $module = '';
    public $action = '';
    public $startDate = '';
    public $endDate = '';

    public $showDetailModal = false;
    public $selectedLogId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'userId' => ['except' => ''],
        'roleId' => ['except' => ''],
        'module' => ['except' => ''],
        'action' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingUserId(): void
    {
        $this->resetPage();
    }

    public function updatingRoleId(): void
    {
        $this->resetPage();
    }

    public function updatingModule(): void
    {
        $this->resetPage();
    }

    public function updatingAction(): void
    {
        $this->resetPage();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'userId',
            'roleId',
            'module',
            'action',
            'startDate',
            'endDate',
        ]);

        $this->resetPage();
    }

    public function openDetail($id): void
    {
        $this->selectedLogId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->selectedLogId = null;
        $this->showDetailModal = false;
    }

    public function getSelectedLogProperty()
    {
        if (! $this->selectedLogId) {
            return null;
        }

        return ActivityLog::query()
            ->with(['user', 'role'])
            ->find($this->selectedLogId);
    }

    public function render()
    {
        $logs = ActivityLog::query()
            ->with(['user', 'role'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhere('module', 'like', '%' . $this->search . '%')
                        ->orWhere('action', 'like', '%' . $this->search . '%')
                        ->orWhere('subject_type', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->userId, function ($query) {
                $query->where('user_id', $this->userId);
            })
            ->when($this->roleId, function ($query) {
                $query->where('role_id', $this->roleId);
            })
            ->when($this->module, function ($query) {
                $query->where('module', $this->module);
            })
            ->when($this->action, function ($query) {
                $query->where('action', $this->action);
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->endDate);
            })
            ->latest()
            ->paginate(15);

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $modules = ActivityLog::query()
            ->select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = ActivityLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('livewire.admin.activity-logs.index', [
            'logs' => $logs,
            'users' => $users,
            'roles' => $roles,
            'modules' => $modules,
            'actions' => $actions,
            'selectedLog' => $this->selectedLog,
            'moduleLabels' => $this->moduleLabels(),
            'actionLabels' => $this->actionLabels(),
        ]);
    }

    public function moduleLabels(): array
    {
        return [
            'system' => 'Sistem',
            'duty_delegation' => 'Delegasi Tupoksi',
            'daily_report' => 'Laporan Harian',
            'daily_report_photo' => 'Foto Laporan',
            'monthly_export' => 'Export Bulanan',
            'user_management' => 'User Management',

            'master_employee' => 'Master Pegawai',
            'master_unit' => 'Master Unit',
            'master_position' => 'Master Jabatan',
            'master_duty' => 'Master Tupoksi',
            'master_server' => 'Master Server',
            'master_application' => 'Master Aplikasi',
            'master_report_template' => 'Template Laporan',
        ];
    }

    public function actionLabels(): array
    {
        return [
            'test' => 'Test',
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            'activate' => 'Aktifkan',
            'deactivate' => 'Nonaktifkan',
            'export' => 'Export',
            'reset_password' => 'Reset Password',
        ];
    }

    public function getModuleLabel(string $module): string
    {
        return $this->moduleLabels()[$module] ?? str($module)->replace('_', ' ')->title();
    }

    public function getActionLabel(string $action): string
    {
        return $this->actionLabels()[$action] ?? str($action)->replace('_', ' ')->title();
    }
}