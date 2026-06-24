<div class="space-y-6">
    <x-page-hero
        badge="Struktur Pegawai"
        title="Master Jabatan"
        description="Kelola jabatan pekerjaan pegawai. Jabatan ini terpisah dari role akses aplikasi agar struktur organisasi dan hak akses tetap rapi."
        icon="briefcase"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-slate-300">Total Jabatan</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $positions->total() }}</p>
                        <p class="mt-1 text-xs text-slate-300">
                            {{ $search ? 'Sesuai pencarian aktif' : 'Data jabatan terdaftar' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-white/10 p-2 text-white">
                        <x-icon name="briefcase" class="h-5 w-5" />
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="openCreateForm"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-white/60"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Jabatan
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    {{-- Form --}}
    @if ($showForm)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50/80 px-5 py-4">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-blue-50 p-2 text-blue-700">
                        <x-icon name="briefcase" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">
                            {{ $isEditing ? 'Edit Jabatan' : 'Tambah Jabatan' }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Isi data jabatan pekerjaan pegawai yang akan dipakai pada data pegawai dan laporan.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="cancel"
                    class="rounded-xl p-2 text-slate-400 transition hover:bg-white hover:text-slate-700"
                    aria-label="Tutup form jabatan"
                >
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-5 p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Nama Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.live="name"
                            placeholder="Contoh: Pranata Komputer"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Kode Jabatan
                        </label>
                        <input
                            type="text"
                            wire:model.live="code"
                            placeholder="Contoh: PRKOM"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Deskripsi
                    </label>
                    <textarea
                        wire:model.live="description"
                        rows="3"
                        placeholder="Deskripsi singkat jabatan..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <input
                        type="checkbox"
                        wire:model.live="is_active"
                        class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    >
                    <span class="text-sm font-medium text-slate-700">Jabatan aktif</span>
                </label>

                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="cancel"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <x-icon name="check-circle" class="h-4 w-4" />
                        {{ $isEditing ? 'Update Jabatan' : 'Simpan Jabatan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- List --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-slate-100 p-2 text-slate-700">
                        <x-icon name="search" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Filter Jabatan</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Cari berdasarkan nama, kode, atau deskripsi jabatan.
                        </p>
                    </div>
                </div>

                <div class="w-full sm:max-w-xs">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Cari jabatan..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Jabatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Deskripsi</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Pegawai</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($positions as $position)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-xl bg-blue-50 p-2 text-blue-700">
                                        <x-icon name="briefcase" class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">
                                            {{ $position->name }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-slate-500">
                                            Jabatan pekerjaan pegawai
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                @if ($position->code)
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $position->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                <div class="max-w-md line-clamp-2">
                                    {{ $position->description ?: '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center text-slate-700">
                                <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    {{ $position->employees_count }} pegawai
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($position->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        <x-icon name="x-circle" class="h-3.5 w-3.5" />
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $position->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $position->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-yellow-300 px-3 py-1.5 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-50"
                                    >
                                        <x-icon name="{{ $position->is_active ? 'x-circle' : 'check-circle' }}" class="h-3.5 w-3.5" />
                                        {{ $position->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $position->id }})"
                                        wire:confirm="Yakin ingin menghapus jabatan ini?"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                        @disabled($position->employees_count > 0)
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="rounded-2xl bg-slate-100 p-3 text-slate-500">
                                        <x-icon name="briefcase" class="h-7 w-7" />
                                    </div>
                                    <h3 class="mt-3 text-sm font-semibold text-slate-900">Belum ada data jabatan</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Tambahkan jabatan pekerjaan pegawai agar data struktur pegawai lebih lengkap.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 p-4">
            {{ $positions->links() }}
        </div>
    </div>
</div>
