<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Jabatan</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola jabatan pekerjaan pegawai. Jabatan ini terpisah dari role akses aplikasi.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateForm"
            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            + Tambah Jabatan
        </button>
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

    {{-- Form --}}
    @if ($showForm)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $isEditing ? 'Edit Jabatan' : 'Tambah Jabatan' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Isi data jabatan pekerjaan pegawai.
                </p>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Nama Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.live="name"
                            placeholder="Contoh: Pranata Komputer"
                            class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Kode Jabatan
                        </label>
                        <input
                            type="text"
                            wire:model.live="code"
                            placeholder="Contoh: PRKOM"
                            class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Deskripsi
                    </label>
                    <textarea
                        wire:model.live="description"
                        rows="3"
                        placeholder="Deskripsi singkat jabatan..."
                        class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        wire:model.live="is_active"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Jabatan aktif</span>
                </label>

                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="cancel"
                        class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        {{ $isEditing ? 'Update Jabatan' : 'Simpan Jabatan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- List --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Jabatan</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Total data jabatan yang terdaftar di aplikasi.
                    </p>
                </div>

                <div class="w-full sm:max-w-xs">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Cari jabatan..."
                        class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jabatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Deskripsi</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Pegawai</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($positions as $position)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">
                                    {{ $position->name }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                {{ $position->code ?: '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                <div class="max-w-md line-clamp-2">
                                    {{ $position->description ?: '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $position->employees_count }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($position->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $position->id }})"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $position->id }})"
                                        class="rounded-lg border border-yellow-300 px-3 py-1.5 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-50"
                                    >
                                        {{ $position->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $position->id }})"
                                        wire:confirm="Yakin ingin menghapus jabatan ini?"
                                        class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                        @disabled($position->employees_count > 0)
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                Belum ada data jabatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-4">
            {{ $positions->links() }}
        </div>
    </div>
</div>