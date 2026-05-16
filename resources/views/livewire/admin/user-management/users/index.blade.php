<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar User</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola dan pantau akun login aplikasi yang terhubung dengan data pegawai.
            </p>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3">
            <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
                Total User
            </p>
            <p class="mt-1 text-2xl font-bold text-blue-700">
                {{ $totalUsers }}
            </p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-3">
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Cari User
                </label>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Cari nama, username, email, pegawai, NIP, unit, jabatan..."
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Role
                </label>
                <select
                    wire:model.live="roleFilter"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3 flex justify-end">
            <button
                type="button"
                wire:click="resetFilters"
                class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
                Reset Filter
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-900">Akun User Aplikasi</h2>
            <p class="mt-1 text-sm text-gray-500">
                Data user, role aplikasi, dan relasi pegawai.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Login</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Pegawai</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jabatan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">
                                    {{ $user->name }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    ID User: {{ $user->id }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="text-gray-800">
                                    {{ $user->username ?? '-' }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $user->email ?? '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @if ($user->role)
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ ucfirst($user->role->name) }}
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Belum Ada Role
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if ($user->employee)
                                    <div class="font-medium text-gray-900">
                                        {{ $user->employee->name }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        NIP: {{ $user->employee->nip ?: '-' }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Tidak terhubung</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $user->employee?->unit?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $user->employee?->positionData?->name ?? $user->employee?->position ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($user->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openResetPasswordModal({{ $user->id }})"
                                        class="rounded-lg border border-yellow-300 px-3 py-1.5 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-50"
                                    >
                                        Reset Password
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleUserStatus({{ $user->id }})"
                                        wire:confirm="{{ $user->is_active ? 'Yakin ingin menonaktifkan user ini?' : 'Yakin ingin mengaktifkan user ini?' }}"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold transition
                                            {{ $user->is_active
                                                ? 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                                                : 'border border-green-300 text-green-700 hover:bg-green-50' }}"
                                    >
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center">
                                <div class="font-semibold text-gray-700">
                                    Belum ada user.
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    User yang sudah dibuat akan tampil di halaman ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-4">
            {{ $users->links() }}
        </div>
    </div>
    
    @if ($showResetPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Reset Password User
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Reset password untuk user:
                        <span class="font-semibold text-gray-800">
                            {{ $selectedUserName }}
                        </span>
                    </p>
                </div>

                <form wire:submit.prevent="resetPassword">
                    <div class="space-y-4 p-5">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.live="new_password"
                                class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('new_password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.live="new_password_confirmation"
                                class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>

                        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            Password default yang disarankan adalah <strong>password123</strong>.
                            Admin bisa menggantinya sebelum menyimpan.
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-gray-200 px-5 py-4 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            wire:click="closeResetPasswordModal"
                            class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-yellow-600 px-4 py-2 text-sm font-semibold text-gray shadow-sm transition hover:bg-yellow-700"
                        >
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>