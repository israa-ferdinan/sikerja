<div class="space-y-6">
    <x-page-hero
        badge="Aktivasi Akun"
        title="Buat akun untuk pegawai yang belum aktif"
        description="Pantau pegawai yang belum memiliki akun login dan buatkan akun aplikasi langsung dari data pegawai yang tersedia."
        icon="user-check"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Belum Punya Akun</p>
                <p class="mt-1 text-4xl font-bold text-white">{{ $totalMissingAccounts }}</p>
                <p class="mt-2 text-xs text-slate-300">Pegawai perlu dibuatkan akses login.</p>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                <x-icon name="filter" class="h-4 w-4" />
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">Filter Pegawai</h2>
                <p class="text-xs text-slate-500">Cari pegawai berdasarkan nama, NIP, kontak, unit, atau jabatan.</p>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Cari Pegawai</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Cari nama, NIP, email, unit, jabatan..."
                        class="w-full rounded-2xl border-slate-300 py-2.5 pl-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Unit</label>
                <select wire:model.live="unitFilter" class="w-full rounded-2xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Jabatan</label>
                <select wire:model.live="positionFilter" class="w-full rounded-2xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Jabatan</option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <x-icon name="rotate-ccw" class="h-4 w-4" />
                Reset Filter
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-orange-50 text-orange-700">
                    <x-icon name="user-check" class="h-4 w-4" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pegawai Belum Punya Akun</h2>
                    <p class="text-sm text-slate-500">Data diambil dari tabel employees yang belum memiliki relasi ke tabel users.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Pegawai</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">NIP</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Unit</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Jabatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Kontak</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($employees as $employee)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-4 align-top">
                                <div class="font-semibold text-slate-900">{{ $employee->name }}</div>
                                <div class="mt-1 text-xs text-slate-500">ID Pegawai: {{ $employee->id }}</div>
                            </td>

                            <td class="px-4 py-4 align-top text-slate-700">{{ $employee->nip ?: '-' }}</td>
                            <td class="px-4 py-4 align-top text-slate-700">{{ $employee->unit?->name ?? '-' }}</td>
                            <td class="px-4 py-4 align-top text-slate-700">{{ $employee->jobPosition?->name ?? $employee->position ?? '-' }}</td>

                            <td class="px-4 py-4 align-top text-slate-700">
                                <div>{{ $employee->email ?: '-' }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $employee->phone ?: '-' }}</div>
                            </td>

                            <td class="px-4 py-4 text-right align-top">
                                <button type="button" wire:click="openCreateUserModal({{ $employee->id }})" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                    <x-icon name="plus" class="h-3.5 w-3.5" />
                                    Buat Akun
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                    <x-icon name="check-circle" class="h-6 w-6" />
                                </div>
                                <div class="mt-3 font-semibold text-slate-700">Semua pegawai sudah memiliki akun.</div>
                                <p class="mt-1 text-sm text-slate-500">Tidak ada data pegawai yang perlu dibuatkan akun saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 p-4">
            {{ $employees->links() }}
        </div>
    </div>

    @if ($showCreateUserModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4 backdrop-blur-sm">
            <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                    <div class="flex gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                            <x-icon name="user-check" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Buat Akun User</h2>
                            <p class="mt-1 text-sm text-slate-500">Buat akun login aplikasi dari data pegawai yang dipilih.</p>
                        </div>
                    </div>

                    <button type="button" wire:click="closeCreateUserModal" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <form wire:submit.prevent="createUser">
                    <div class="space-y-4 p-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Nama User <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="user_name" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('user_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Role Aplikasi <span class="text-red-500">*</span></label>
                                <select wire:model.live="role_id" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                @error('role_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Email <span class="text-red-500">*</span></label>
                                <input type="email" wire:model.live="email" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Username <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="username" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="password" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Konfirmasi Password <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="password_confirmation" class="w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            Password default yang disarankan adalah <strong>password123</strong>. Admin bisa menggantinya sebelum menyimpan.
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end">
                        <button type="button" wire:click="closeCreateUserModal" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
