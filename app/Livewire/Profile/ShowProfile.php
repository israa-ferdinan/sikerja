<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ShowProfile extends Component
{
    use WithFileUploads;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public ?TemporaryUploadedFile $signature = null;

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

    public function updateSignature(): void
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            session()->flash('warning', 'Data pegawai belum terhubung dengan akun Anda.');
            return;
        }

        $this->validate([
            'signature' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
        ], [
            'signature.required' => 'File tanda tangan wajib dipilih.',
            'signature.image' => 'File tanda tangan harus berupa gambar.',
            'signature.mimes' => 'Format tanda tangan harus PNG, JPG, JPEG, atau WEBP.',
            'signature.max' => 'Ukuran tanda tangan maksimal 1 MB.',
        ]);

        if ($employee->signature_path && Storage::disk('public')->exists($employee->signature_path)) {
            Storage::disk('public')->delete($employee->signature_path);
        }

        $path = $this->signature->store('employee-signatures', 'public');

        $employee->forceFill([
            'signature_path' => $path,
        ])->save();

        $this->reset('signature');

        session()->flash('success', 'Tanda tangan berhasil diperbarui.');
    }

    public function deleteSignature(): void
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            session()->flash('warning', 'Data pegawai belum terhubung dengan akun Anda.');
            return;
        }

        if ($employee->signature_path && Storage::disk('public')->exists($employee->signature_path)) {
            Storage::disk('public')->delete($employee->signature_path);
        }

        $employee->forceFill([
            'signature_path' => null,
        ])->save();

        session()->flash('success', 'Tanda tangan berhasil dihapus.');
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