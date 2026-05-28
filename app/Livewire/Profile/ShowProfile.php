<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ShowProfile extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user->forceFill([
            'password' => Hash::make($this->password),
            'password_changed_at' => now(),
            'must_change_password' => false,
        ])->save();

        $this->reset([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        session()->flash('success', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        $user = Auth::user()->load([
            'role',
            'employee.unit',
            'employee.jobPosition',
        ]);

        return view('livewire.profile.show-profile', [
            'user' => $user,
            'employee' => $user->employee,
        ]);
    }
}