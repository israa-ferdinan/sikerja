<?php

namespace App\Livewire\Profile;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $oldSignaturePath = $employee->signature_path;
        $hadSignature = filled($oldSignaturePath);

        /*
        * R11B:
        * Jangan hapus file tanda tangan lama saat replace.
        * monthly_report_approvals.approver_signature_path menyimpan path tanda tangan saat finalisasi.
        * Kalau file lama dihapus, export periode lama bisa kehilangan gambar tanda tangan.
        */
        $path = $this->signature->store('employee-signatures', 'public');

        $employee->forceFill([
            'signature_path' => $path,
        ])->save();

        ActivityLogger::log(
            module: 'profile',
            action: $hadSignature ? 'replace_signature' : 'upload_signature',
            description: $hadSignature
                ? 'Mengganti tanda tangan digital profil.'
                : 'Mengupload tanda tangan digital profil.',
            subject: $employee,
            oldValues: [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'signature_path' => $oldSignaturePath,
            ],
            newValues: [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'signature_path' => $path,
            ],
        );

        $this->reset('signature');

        session()->flash(
            'success',
            $hadSignature
                ? 'Tanda tangan berhasil diganti. Tanda tangan lama tetap disimpan untuk menjaga arsip export periode sebelumnya.'
                : 'Tanda tangan berhasil diupload.'
        );
    }

    public function deleteSignature(): void
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            session()->flash('warning', 'Data pegawai belum terhubung dengan akun Anda.');
            return;
        }

        $oldSignaturePath = $employee->signature_path;

        /*
        * R11B:
        * Jangan hapus file fisik tanda tangan dari storage.
        * Cukup lepas dari profil pegawai agar export lama yang sudah menyimpan path tanda tangan
        * tetap bisa menampilkan gambar historis.
        */
        $employee->forceFill([
            'signature_path' => null,
        ])->save();

        ActivityLogger::log(
            module: 'profile',
            action: 'delete_signature',
            description: 'Melepas tanda tangan digital dari profil.',
            subject: $employee,
            oldValues: [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'signature_path' => $oldSignaturePath,
            ],
            newValues: [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'signature_path' => null,
            ],
        );

        session()->flash('success', 'Tanda tangan berhasil dilepas dari profil. File lama tetap disimpan untuk menjaga arsip export periode sebelumnya.');
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