<div>
    <x-page-hero
        badge="Master Data"
        title="Master Data Pegawai"
        description="Kelola data pegawai, unit kerja, jabatan, kontak, status aktif, dan akses kelola tupoksi personal."
        icon="users"
    >
        <x-slot:aside>
            <div class="space-y-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 text-white">
                            <x-icon name="users" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">Total Pegawai</p>
                            <p class="mt-1 text-2xl font-semibold text-white">{{ $pegawais->total() }}</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-300">
                        Berdasarkan hasil pencarian/filter aktif saat ini.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Pegawai
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                        <x-icon name="search" class="h-4 w-4 text-slate-500" />
                        Pencarian Pegawai
                    </div>
                    <p class="mt-1 text-xs text-slate-500">
                        Cari berdasarkan nama, NIP, nomor HP, email, jabatan, atau unit.
                    </p>
                </div>

                <div class="w-full md:w-96">
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                        placeholder="Cari pegawai..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="border-b border-slate-100 px-4 py-3">No</th>
                        <th class="border-b border-slate-100 px-4 py-3">Nama</th>
                        <th class="border-b border-slate-100 px-4 py-3">NIP</th>
                        <th class="border-b border-slate-100 px-4 py-3">Unit</th>
                        <th class="border-b border-slate-100 px-4 py-3">Jabatan</th>
                        <th class="border-b border-slate-100 px-4 py-3">No HP</th>
                        <th class="border-b border-slate-100 px-4 py-3">Email</th>
                        <th class="border-b border-slate-100 px-4 py-3">Status</th>
                        <th class="border-b border-slate-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($pegawais as $pegawai)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-4 text-slate-500">
                                {{ $pegawais->firstItem() + $loop->index }}
                            </td>

                            <td class="min-w-56 px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                        <x-icon name="users" class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $pegawai->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $pegawai->duties_count ?? 0 }} tupoksi personal</p>
                                    </div>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $pegawai->nip ?? '-' }}
                            </td>

                            <td class="min-w-48 px-4 py-4 text-slate-600">
                                <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    <x-icon name="building-2" class="h-3.5 w-3.5" />
                                    {{ $pegawai->unit?->name ?? '-' }}
                                </div>
                            </td>

                            <td class="min-w-44 px-4 py-4 text-slate-600">
                                <div class="inline-flex items-center gap-2 text-slate-600">
                                    <x-icon name="briefcase" class="h-4 w-4 text-slate-400" />
                                    {{ $pegawai->jobPosition?->name ?? '-' }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $pegawai->phone ?? '-' }}
                            </td>

                            <td class="min-w-52 px-4 py-4 text-slate-600">
                                {{ $pegawai->email ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @if ($pegawai->is_active)
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

                            <td class="min-w-72 px-4 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $pegawai->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <a
                                        href="{{ route('admin.master-data.pegawai.duties', $pegawai) }}"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100"
                                    >
                                        <x-icon name="list-checks" class="h-3.5 w-3.5" />
                                        Kelola Tupoksi
                                    </a>

                                    <button
                                        type="button"
                                        wire:confirm="Yakin mau hapus pegawai ini?"
                                        wire:click="delete({{ $pegawai->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-500">
                                        <x-icon name="users" class="h-7 w-7" />
                                    </div>
                                    <h3 class="mt-4 text-sm font-semibold text-slate-800">Data pegawai belum tersedia</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Tambahkan data pegawai agar bisa mengatur tupoksi personal dan laporan harian.
                                    </p>
                                    <button
                                        type="button"
                                        wire:click="openCreateModal"
                                        class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                                    >
                                        <x-icon name="plus" class="h-4 w-4" />
                                        Tambah Pegawai
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">
            {{ $pegawais->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm">
            <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                <form wire:submit.prevent="save">
                    <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                <x-icon name="users" class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">
                                    {{ $isEdit ? 'Edit Pegawai' : 'Tambah Pegawai' }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Lengkapi identitas pegawai, unit, jabatan, kontak, dan status aktif.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-2xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        >
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="max-h-[70vh] space-y-4 overflow-y-auto p-6">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Unit <span class="text-red-500">*</span>
                            </label>

                            <select
                                wire:model.defer="unit_id"
                                class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>

                            @error('unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Nama Pegawai <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Contoh: Budi Santoso"
                            >

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">NIP</label>
                                <input
                                    type="text"
                                    wire:model.defer="nip"
                                    class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Contoh: 198XXXXXXXXXXXXX"
                                >
                                @error('nip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Jabatan</label>
                                <select
                                    wire:model.live="position_id"
                                    class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Pilih Jabatan</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">No HP</label>
                                <input
                                    type="text"
                                    wire:model.defer="phone"
                                    class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Contoh: 08123456789"
                                >
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Email Pegawai</label>
                                <input
                                    type="email"
                                    wire:model.defer="email"
                                    class="w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Contoh: pegawai@domain.go.id"
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                            <input
                                type="checkbox"
                                wire:model.defer="is_active"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            Pegawai aktif dan dapat digunakan dalam laporan kerja.
                        </label>
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
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
