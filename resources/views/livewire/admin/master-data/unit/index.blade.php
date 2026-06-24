<div class="space-y-6">
    <x-page-hero
        badge="Master Data"
        title="Master Data Unit"
        description="Kelola data unit kerja sebagai referensi pegawai, laporan kerja harian, monitoring Kanit, dan target unit."
        icon="building-2"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-white">
                        <x-icon name="building-2" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-300">Total Unit</p>
                        <p class="text-2xl font-semibold text-white">{{ $units->total() }}</p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Unit
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-4 sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Daftar Unit Kerja</h2>
                    <p class="mt-1 text-sm text-slate-500">Cari dan kelola unit kerja yang digunakan di modul pegawai dan laporan.</p>
                </div>

                <div class="relative w-full sm:max-w-sm">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        placeholder="Cari nama unit, kode unit, atau deskripsi..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Kode Unit</th>
                        <th class="px-4 py-3">Nama Unit</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($units as $unit)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-500">
                                {{ $units->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $unit->code ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 font-semibold text-slate-900">
                                {{ $unit->name }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $unit->description ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                @if ($unit->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $unit->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:confirm="Yakin mau hapus unit ini?"
                                        wire:click="delete({{ $unit->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100"
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
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="building-2" class="h-6 w-6" />
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Data unit belum tersedia</p>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan unit kerja agar bisa digunakan pada data pegawai dan laporan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">
            {{ $units->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <form wire:submit.prevent="save">
                    <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <x-icon name="building-2" class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">
                                    {{ $isEdit ? 'Edit Unit' : 'Tambah Unit' }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">Lengkapi data unit kerja dengan benar.</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        >
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="space-y-5 px-6 py-5">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Nama Unit <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                placeholder="Contoh: Unit Teknologi Informasi"
                            >

                            @error('name')
                                <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Kode Unit
                            </label>

                            <input
                                type="text"
                                wire:model.defer="code"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                placeholder="Contoh: TI"
                            >

                            @error('code')
                                <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Deskripsi
                            </label>

                            <textarea
                                rows="3"
                                wire:model.defer="description"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                placeholder="Keterangan unit kerja..."
                            ></textarea>

                            @error('description')
                                <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                            <input
                                type="checkbox"
                                wire:model.defer="is_active"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            Aktif
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                        >
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
