<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Akun Pegawai</h1>
            <p class="mt-1 text-sm text-gray-500">
                Pantau data pegawai yang belum memiliki akun login aplikasi.
            </p>
        </div>

        <div class="rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3">
            <p class="text-xs font-medium uppercase tracking-wide text-orange-600">
                Belum Punya Akun
            </p>
            <p class="mt-1 text-2xl font-bold text-orange-700">
                {{ $totalMissingAccounts }}
            </p>
        </div>
    </div>

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
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Cari Pegawai
                </label>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Cari nama, NIP, email, unit, jabatan..."
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Unit
                </label>
                <select
                    wire:model.live="unitFilter"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Jabatan
                </label>
                <select
                    wire:model.live="positionFilter"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Jabatan</option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
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
            <h2 class="text-lg font-semibold text-gray-900">Pegawai Belum Punya Akun</h2>
            <p class="mt-1 text-sm text-gray-500">
                Data ini diambil dari tabel employees yang belum memiliki relasi ke tabel users.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Pegawai</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">NIP</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jabatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Kontak</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">
                                    {{ $employee->name }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    ID Pegawai: {{ $employee->id }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $employee->nip ?: '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $employee->unit?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $employee->positionData?->name ?? $employee->position ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                <div>{{ $employee->email ?: '-' }}</div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $employee->phone ?: '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <button
                                    type="button"
                                    wire:click="openCreateUserModal({{ $employee->id }})"
                                    class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                >
                                    Buat Akun
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                <div class="font-semibold text-gray-700">
                                    Semua pegawai sudah memiliki akun.
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Tidak ada data pegawai yang perlu dibuatkan akun saat ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-4">
            {{ $employees->links() }}
        </div>
    </div>

    @if ($showCreateUserModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Buat Akun User
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Buat akun login aplikasi dari data pegawai yang dipilih.
                    </p>
                </div>

                <form wire:submit.prevent="createUser">
                    <div class="space-y-4 p-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Nama User <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="user_name"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                @error('user_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Role Aplikasi <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model.live="role_id"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    wire:model.live="email"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Username <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="username"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                @error('username')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Password Default <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="password"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="password_confirmation"
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>
                        </div>

                        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            Password default saat ini adalah <strong>password123</strong>.
                            Setelah akun dibuat, admin bisa menyampaikan username dan password ini ke pegawai terkait.
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-gray-200 px-5 py-4 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            wire:click="closeCreateUserModal"
                            class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                        >
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>