<div class="space-y-6">
    <x-page-hero
        badge="Manajemen User"
        title="Kelola akun login aplikasi"
        description="Pantau akun user, role aplikasi, status aktivasi, dan reset password tanpa mengubah data pegawai utama."
        icon="users"
    >
        <x-slot:aside>
            <div class="grid gap-3 text-sm">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Total User</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ $totalUsers }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-3">
                        <p class="text-xs text-slate-300">Aktif</p>
                        <p class="mt-1 text-xl font-bold text-emerald-200">{{ $activeUsers }}</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/10 p-3">
                        <p class="text-xs text-slate-300">Nonaktif</p>
                        <p class="mt-1 text-xl font-bold text-slate-200">{{ $inactiveUsers }}</p>
                    </div>
                </div>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                <x-icon name="filter" class="h-4 w-4" />
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">Filter User</h2>
                <p class="text-xs text-slate-500">Cari berdasarkan akun, pegawai, unit, jabatan, atau role.</p>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm font-medium text-slate-700">Cari User</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Cari nama, username, email, pegawai, NIP, unit, jabatan..."
                        class="w-full rounded-2xl border-slate-300 py-2.5 pl-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                <select
                    wire:model.live="roleFilter"
                    class="w-full rounded-2xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button
                type="button"
                wire:click="resetFilters"
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                <x-icon name="rotate-ccw" class="h-4 w-4" />
                Reset Filter
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                    <x-icon name="shield-check" class="h-4 w-4" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Akun User Aplikasi</h2>
                    <p class="text-sm text-slate-500">Data user, role aplikasi, relasi pegawai, dan status login.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Login</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Pegawai</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Unit</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Jabatan</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($users as $user)
                        @php
                            $isCurrentUser = auth()->id() === $user->id;
                            $isAdminUser = $user->role?->name === 'admin';
                            $canManageUser = ! $isCurrentUser && ! $isAdminUser;
                        @endphp

                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-4 align-top">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-semibold text-slate-900">{{ $user->name }}</div>

                                    @if ($isCurrentUser)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                                            <x-icon name="user-check" class="h-3 w-3" />
                                            Akun Anda
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-slate-500">ID User: {{ $user->id }}</div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <div class="font-medium text-slate-800">{{ $user->username ?? '-' }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $user->email ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                @if ($user->role)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ ucfirst($user->role->name) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        Belum Ada Role
                                    </span>
                                @endif

                                @if ($isAdminUser)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">
                                            <x-icon name="shield-check" class="h-3 w-3" />
                                            Dilindungi
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4 align-top">
                                @if ($user->employee)
                                    <div class="font-medium text-slate-900">{{ $user->employee->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">NIP: {{ $user->employee->nip ?: '-' }}</div>
                                @else
                                    <span class="text-slate-400">Tidak terhubung</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 align-top text-slate-700">{{ $user->employee?->unit?->name ?? '-' }}</td>
                            <td class="px-4 py-4 align-top text-slate-700">{{ $user->employee?->jobPosition?->name ?? $user->employee?->position ?? '-' }}</td>

                            <td class="px-4 py-4 text-center align-top">
                                <div class="flex flex-col items-center gap-1.5">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            <x-icon name="x-circle" class="h-3.5 w-3.5" />
                                            Nonaktif
                                        </span>
                                    @endif

                                    @if ($user->must_change_password)
                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                            Wajib Ganti Password
                                        </span>
                                    @endif

                                    <span class="text-[11px] text-slate-400">
                                        @if ($user->last_login_at)
                                            Login: {{ $user->last_login_at->format('d/m/Y H:i') }}
                                        @else
                                            Belum login
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-right align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($canManageUser)
                                        <button
                                            type="button"
                                            wire:click="openResetPasswordModal({{ $user->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-50"
                                        >
                                            <x-icon name="shield-check" class="h-3.5 w-3.5" />
                                            Reset
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="toggleUserStatus({{ $user->id }})"
                                            wire:confirm="{{ $user->is_active ? 'Yakin ingin menonaktifkan user ini?' : 'Yakin ingin mengaktifkan user ini?' }}"
                                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold transition
                                                {{ $user->is_active
                                                    ? 'border border-slate-300 text-slate-700 hover:bg-slate-50'
                                                    : 'border border-emerald-300 text-emerald-700 hover:bg-emerald-50' }}"
                                        >
                                            <x-icon name="{{ $user->is_active ? 'x-circle' : 'check-circle' }}" class="h-3.5 w-3.5" />
                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    @else
                                        <span class="inline-flex rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500">
                                            Dilindungi
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <x-icon name="users" class="h-6 w-6" />
                                </div>
                                <div class="mt-3 font-semibold text-slate-700">Belum ada user.</div>
                                <p class="mt-1 text-sm text-slate-500">User yang sudah dibuat akan tampil di halaman ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 p-4">
            {{ $users->links() }}
        </div>
    </div>

    @if ($showResetPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4 backdrop-blur-sm">
            <div class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                    <div class="flex gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                            <x-icon name="shield-check" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Reset Password User</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Reset password untuk <span class="font-semibold text-slate-800">{{ $selectedUserName }}</span>.
                            </p>
                        </div>
                    </div>

                    <button type="button" wire:click="closeResetPasswordModal" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <form wire:submit.prevent="resetPassword">
                    <div class="space-y-4 p-5">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Password Baru <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="new_password" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('new_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="new_password_confirmation" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            Password default yang disarankan adalah <strong>password123</strong>. Admin bisa menggantinya sebelum menyimpan.
                        </div>

                        <label class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3">
                            <input type="checkbox" wire:model.defer="must_change_password" class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>
                                <span class="block text-sm font-semibold text-blue-800">Wajib ganti password saat login berikutnya</span>
                                <span class="mt-1 block text-xs leading-5 text-blue-700">Jika aktif, user akan diarahkan ke halaman Profil Saya dan wajib mengganti password sebelum menggunakan menu aplikasi.</span>
                            </span>
                        </label>
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end">
                        <button type="button" wire:click="closeResetPasswordModal" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700">
                            <x-icon name="shield-check" class="h-4 w-4" />
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
