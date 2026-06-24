<?php

namespace App\Livewire\Admin\UserManagement\Users;

use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public bool $showResetPasswordModal = false;

    public ?int $selectedUserId = null;
    public string $selectedUserName = '';

    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $must_change_password = true;

    protected string $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'roleFilter',
        ]);

        $this->resetPage();
    }

    public function openResetPasswordModal(int $userId): void
    {
        $user = User::findOrFail($userId);

        if (auth()->id() === $user->id) {
            $this->dispatch('toast', type: 'error', message: 'Password akun sendiri hanya bisa diubah melalui menu Profil Saya.');
            return;
        }

        if ($user->role?->name === 'admin') {
            $this->dispatch('toast', type: 'error', message: 'Password akun Admin lain tidak bisa direset dari User Management.');
            return;
        }

        $this->selectedUserId = $user->id;
        $this->selectedUserName = $user->name;

        $this->new_password = 'password123';
        $this->new_password_confirmation = 'password123';
        $this->must_change_password = true;

        $this->showResetPasswordModal = true;

        $this->resetValidation();
    }

    public function resetPassword(): void
    {
        $validated = $this->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'must_change_password' => [
                'boolean',
            ],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::with('role')->findOrFail($this->selectedUserId);

        if (auth()->id() === $user->id) {
            $this->dispatch('toast', type: 'error', message: 'Password akun sendiri hanya bisa diubah melalui menu Profil Saya.');
            $this->closeResetPasswordModal();
            return;
        }

        if ($user->role?->name === 'admin') {
            $this->dispatch('toast', type: 'error', message: 'Password akun Admin lain tidak bisa direset dari User Management.');
            $this->closeResetPasswordModal();
            return;
        }

        $oldValues = $user->makeHidden(['password', 'remember_token'])->toArray();

        $user->update([
            'password' => Hash::make($validated['new_password']),
            'must_change_password' => $this->must_change_password,
            'password_changed_at' => null,
        ]);

        ActivityLogger::log(
            module: 'user_management',
            action: 'reset_password',
            description: 'Reset password user ' . $user->name,
            subject: $user,
            oldValues: $oldValues,
            newValues: [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username ?? null,
                'password_reset' => true,
                'must_change_password' => $this->must_change_password,
                'password_changed_at' => null,
                'reset_at' => now()->toDateTimeString(),
            ]
        );

        $this->closeResetPasswordModal();

        $this->dispatch('toast', type: 'success', message: 'Password user ' . $user->name . ' berhasil direset.');
    }

    public function closeResetPasswordModal(): void
    {
        $this->reset([
            'showResetPasswordModal',
            'selectedUserId',
            'selectedUserName',
            'new_password',
            'new_password_confirmation',
            'must_change_password',
        ]);

        $this->must_change_password = true;

        $this->resetValidation();
    }

    public function toggleUserStatus(int $userId): void
    {
        $user = User::with('role')->findOrFail($userId);

        if (auth()->id() === $user->id && $user->is_active) {
            $this->dispatch('toast', type: 'error', message: 'Akun yang sedang digunakan tidak bisa dinonaktifkan.');
            return;
        }

        if ($user->role?->name === 'admin') {
            $this->dispatch('toast', type: 'error', message: 'Akun Admin tidak bisa diaktifkan/nonaktifkan dari User Management.');
            return;
        }

        $oldValues = $user->makeHidden(['password', 'remember_token'])->toArray();

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $freshUser = $user->fresh();

        ActivityLogger::log(
            module: 'user_management',
            action: $freshUser->is_active ? 'activate' : 'deactivate',
            description: $freshUser->is_active
                ? 'Mengaktifkan user ' . $freshUser->name
                : 'Menonaktifkan user ' . $freshUser->name,
            subject: $freshUser,
            oldValues: $oldValues,
            newValues: $freshUser->makeHidden(['password', 'remember_token'])->toArray()
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: $freshUser->is_active
                ? 'User berhasil diaktifkan.'
                : 'User berhasil dinonaktifkan.'
        );
    }

    public function render()
    {
        $users = User::query()
            ->with([
                'role',
                'employee',
                'employee.unit',
                'employee.jobPosition',
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employee', function ($employeeQuery) {
                            $employeeQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('nip', 'like', '%' . $this->search . '%')
                                ->orWhereHas('unit', function ($unitQuery) {
                                    $unitQuery->where('name', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('jobPosition', function ($positionQuery) {
                                    $positionQuery->where('name', 'like', '%' . $this->search . '%');
                                });
                        });
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role_id', $this->roleFilter);
            })
            ->latest()
            ->paginate(10);

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        $totalUsers = User::query()->count();
        $activeUsers = User::query()->where('is_active', true)->count();
        $inactiveUsers = User::query()->where('is_active', false)->count();

        return view('livewire.admin.user-management.users.index', [
            'users' => $users,
            'roles' => $roles,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ]);
    }
}
