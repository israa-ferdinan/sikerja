<?php

namespace App\Livewire\Admin\UserManagement\Users;

use App\Models\Role;
use App\Models\User;
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

        $this->selectedUserId = $user->id;
        $this->selectedUserName = $user->name;

        $this->new_password = 'password123';
        $this->new_password_confirmation = 'password123';

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
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::findOrFail($this->selectedUserId);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        session()->flash('success', 'Password user ' . $user->name . ' berhasil direset.');

        $this->closeResetPasswordModal();
    }

    public function closeResetPasswordModal(): void
    {
        $this->reset([
            'showResetPasswordModal',
            'selectedUserId',
            'selectedUserName',
            'new_password',
            'new_password_confirmation',
        ]);

        $this->resetValidation();
    }

    public function toggleUserStatus(int $userId): void
    {
        $user = User::findOrFail($userId);

        if (auth()->id() === $user->id && $user->is_active) {
            session()->flash('error', 'Akun yang sedang digunakan tidak bisa dinonaktifkan.');
            return;
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        session()->flash(
            'success',
            $user->is_active
                ? 'User berhasil diaktifkan.'
                : 'User berhasil dinonaktifkan.'
        );
    }

    public function render()
    {
        $users = User::query()
            ->with([
                'role',
                'employee.unit',
                'employee.positionData',
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
                                ->orWhereHas('positionData', function ($positionQuery) {
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

        return view('livewire.admin.user-management.users.index', [
            'users' => $users,
            'roles' => $roles,
            'totalUsers' => $totalUsers,
        ]);
    }
}