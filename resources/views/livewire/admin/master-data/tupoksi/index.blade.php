<div class="space-y-6">
    <x-page-hero
        badge="Master Data"
        title="Kelola Master Data Tupoksi"
        description="Atur daftar tugas pokok dan fungsi sebagai referensi pegawai saat membuat laporan kerja harian."
        icon="clipboard-list"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Total Tupoksi</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $tupoksis->total() }}</p>
                        <p class="mt-1 text-xs text-slate-300">
                            {{ $search ? 'Sesuai pencarian aktif' : 'Semua data tupoksi' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-cyan-400/15 p-3 text-cyan-200">
                        <x-icon name="clipboard-list" class="h-5 w-5" />
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Tupoksi
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-cyan-50 p-2 text-cyan-700">
                        <x-icon name="search" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Daftar Tupoksi</h2>
                        <p class="mt-1 text-xs text-slate-500">Cari berdasarkan nama tupoksi, deskripsi, unit, atau klasifikasi.</p>
                    </div>
                </div>

                <div class="relative w-full md:w-96">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                        placeholder="Cari tupoksi, deskripsi, unit..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="border-b border-slate-100 px-4 py-3">No</th>
                        <th class="border-b border-slate-100 px-4 py-3">Unit</th>
                        <th class="border-b border-slate-100 px-4 py-3">Nama Tupoksi</th>
                        <th class="border-b border-slate-100 px-4 py-3">Deskripsi</th>
                        <th class="border-b border-slate-100 px-4 py-3">Klasifikasi</th>
                        <th class="border-b border-slate-100 px-4 py-3">Objek Pekerjaan</th>
                        <th class="border-b border-slate-100 px-4 py-3">Status</th>
                        <th class="border-b border-slate-100 px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($tupoksis as $tupoksi)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-4 text-slate-500">
                                {{ $tupoksis->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-4 text-slate-700">
                                {{ $tupoksi->unit?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $tupoksi->name }}</div>
                            </td>

                            <td class="max-w-xs px-4 py-4 text-slate-600">
                                <div class="line-clamp-2">
                                    {{ $tupoksi->description ?? '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if ($tupoksi->classification)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                        {{ $tupoksi->classification->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-700">
                                    {{ $tupoksi->object_type_label }}
                                </div>
                                <div class="mt-0.5 text-xs text-slate-500">
                                    {{ $tupoksi->work_object_label }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if ($tupoksi->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $tupoksi->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:confirm="Yakin mau hapus tupoksi ini?"
                                        wire:click="delete({{ $tupoksi->id }})"
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
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="rounded-2xl bg-slate-100 p-4 text-slate-500">
                                        <x-icon name="clipboard-list" class="h-8 w-8" />
                                    </div>
                                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Data tupoksi belum tersedia</h3>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan tupoksi agar pegawai bisa memilih referensi pekerjaan saat membuat laporan.</p>
                                    <button
                                        type="button"
                                        wire:click="openCreateModal"
                                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                    >
                                        <x-icon name="plus" class="h-4 w-4" />
                                        Tambah Tupoksi
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-5 py-4">
            {{ $tupoksis->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm">
            <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <form wire:submit.prevent="save">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                        <div class="flex items-start gap-3">
                            <div class="rounded-xl bg-cyan-50 p-2 text-cyan-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">
                                    {{ $isEdit ? 'Edit Tupoksi' : 'Tambah Tupoksi' }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Master tupoksi hanya menyimpan kategori besar objek pekerjaan. Detail server/aplikasi dipilih saat input laporan.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        >
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="max-h-[70vh] space-y-5 overflow-y-auto p-6">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                Unit <span class="text-red-500">*</span>
                            </label>

                            <select
                                wire:model.defer="unit_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                            >
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>

                            @error('unit_id')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                Nama Tupoksi <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                                placeholder="Contoh: Monitoring dan pemeliharaan server"
                            >

                            @error('name')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>

                            <textarea
                                rows="4"
                                wire:model.defer="description"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                                placeholder="Tuliskan detail tupoksi..."
                            ></textarea>

                            @error('description')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Klasifikasi Tupoksi</label>

                                <select
                                    wire:model.defer="duty_classification_id"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                                >
                                    <option value="">Pilih Klasifikasi</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                    @endforeach
                                </select>

                                @error('duty_classification_id')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Objek Pekerjaan</label>

                                <select
                                    wire:model.live="object_type"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                                >
                                    @foreach ($this->objectTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>

                                @error('object_type')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                wire:model.defer="is_active"
                                class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"
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
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
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
