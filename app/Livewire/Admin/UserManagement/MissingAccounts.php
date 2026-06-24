<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Unit;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class MissingAccounts extends Component
{
    use WithPagination;

    public bool $showCreateUserModal = false;

    public ?int $selectedEmployeeId = null;
    public ?int $role_id = null;

    public string $user_name = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $search = '';
    public string $unitFilter = '';
    public string $positionFilter = '';

    protected string $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingUnitFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPositionFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'unitFilter',
            'positionFilter',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $employees = Employee::query()
            ->with(['unit', 'jobPosition'])
            ->whereDoesntHave('user')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhereHas('unit', function ($unitQuery) {
                            $unitQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('jobPosition', function ($positionQuery) {
                            $positionQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->unitFilter, function ($query) {
                $query->where('unit_id', $this->unitFilter);
            })
            ->when($this->positionFilter, function ($query) {
                $query->where('position_id', $this->positionFilter);
            })
            ->latest()
            ->paginate(10);

        $totalMissingAccounts = Employee::query()
            ->whereDoesntHave('user')
            ->count();

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('livewire.admin.user-management.missing-accounts', [
            'employees' => $employees,
            'totalMissingAccounts' => $totalMissingAccounts,
            'units' => $units,
            'positions' => $positions,
            'roles' => $roles,
        ]);
    }

    public function openCreateUserModal(int $employeeId): void
    {
        $employee = Employee::with(['unit', 'jobPosition', 'user'])->findOrFail($employeeId);

        if ($employee->user) {
            $this->dispatch('toast', type: 'error', message: 'Pegawai ini sudah memiliki akun user.');
            return;
        }

        $defaultRole = Role::query()
            ->where('name', 'pegawai')
            ->first();

        $this->selectedEmployeeId = $employee->id;
        $this->role_id = $defaultRole?->id;

        $this->user_name = $employee->name;
        $this->email = $employee->email ?: $this->generateEmailFromEmployee($employee);
        $this->username = $this->generateUsernameFromEmployee($employee);

        $this->password = 'password123';
        $this->password_confirmation = 'password123';

        $this->showCreateUserModal = true;

        $this->resetValidation();
    }

    private function generateUsernameFromEmployee(Employee $employee): string
    {
        if (! empty($employee->nip)) {
            return preg_replace('/[^0-9A-Za-z]/', '', $employee->nip);
        }

        if (! empty($employee->email)) {
            return Str::before($employee->email, '@');
        }

        $baseUsername = Str::slug($employee->name, '.');

        $username = $baseUsername;
        $counter = 1;

        while (User::query()->where('username', $username)->exists()) {
            $username = $baseUsername . '.' . $counter;
            $counter++;
        }

        return $username;
    }

    private function generateEmailFromEmployee(Employee $employee): string
    {
        $base = Str::slug($employee->name, '.');

        return $base . '@example.local';
    }

    public function createUser(): void
    {
        $employee = Employee::with('user')->findOrFail($this->selectedEmployeeId);

        if ($employee->user) {
            $this->dispatch('toast', type: 'error', message: 'Pegawai ini sudah memiliki akun user.');
            $this->closeCreateUserModal();
            return;
        }

        $validated = $this->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username'),
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'user_name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'role_id.required' => 'Role aplikasi wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['user_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'role_id' => $validated['role_id'],
            'employee_id' => $employee->id,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        ActivityLogger::log(
            module: 'user_management',
            action: 'create',
            description: 'Membuat akun user untuk pegawai ' . $employee->name,
            subject: $user,
            newValues: $user->fresh(['role', 'employee'])
                ->makeHidden(['password', 'remember_token'])
                ->toArray()
        );

        $this->closeCreateUserModal();
        $this->resetPage();

        $this->dispatch('toast', type: 'success', message: 'Akun user berhasil dibuat untuk pegawai: ' . $employee->name);
    }

    public function closeCreateUserModal(): void
    {
        $this->reset([
            'showCreateUserModal',
            'selectedEmployeeId',
            'role_id',
            'user_name',
            'email',
            'username',
            'password',
            'password_confirmation',
        ]);

        $this->resetValidation();
    }
}
