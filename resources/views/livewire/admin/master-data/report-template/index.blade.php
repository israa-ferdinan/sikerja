<div class="space-y-6">
    <x-page-hero
        badge="Master Data"
        title="Master Template Laporan"
        description="Kelola template deskripsi dan hasil laporan kerja agar pegawai bisa mengisi laporan harian lebih cepat dan konsisten."
        icon="file-text"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-white">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-300">Total Template</p>
                        <p class="text-2xl font-semibold text-white">{{ $reportTemplates->total() }}</p>
                        <p class="mt-1 text-xs text-slate-300">Sesuai pencarian aktif</p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Template
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-4 sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Daftar Template Laporan</h2>
                    <p class="mt-1 text-sm text-slate-500">Cari template berdasarkan judul, kategori, unit, tupoksi, atau isi template.</p>
                </div>

                <div class="relative w-full sm:max-w-md">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        placeholder="Cari template laporan..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3">Tupoksi</th>
                        <th class="px-4 py-3">Template Deskripsi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($reportTemplates as $template)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-500">
                                {{ $reportTemplates->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $template->title }}</div>
                                @if ($template->result_template)
                                    <div class="mt-1 text-xs text-slate-500">Memiliki template hasil</div>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if ($template->category)
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                        {{ $template->category }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $template->unit?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $template->jobDuty?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                <div class="max-w-md leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit($template->description_template, 100) }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @if ($template->is_active)
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
                                        wire:click="openEditModal({{ $template->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:confirm="Yakin mau hapus template ini?"
                                        wire:click="delete({{ $template->id }})"
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
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="file-text" class="h-6 w-6" />
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Template laporan belum tersedia</p>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan template agar pegawai bisa mengisi laporan lebih cepat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">
            {{ $reportTemplates->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <form wire:submit.prevent="save">
                    <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <x-icon name="file-text" class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">
                                    {{ $isEdit ? 'Edit Template Laporan' : 'Tambah Template Laporan' }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">Template akan muncul sebagai pilihan cepat saat input laporan harian.</p>
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

                    <div class="max-h-[70vh] space-y-5 overflow-y-auto px-6 py-5">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Unit</label>
                                <select
                                    wire:model.defer="unit_id"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="">Pilih Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Tupoksi</label>
                                <select
                                    wire:model.defer="job_duty_id"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="">Pilih Tupoksi</option>
                                    @foreach ($jobDuties as $jobDuty)
                                        <option value="{{ $jobDuty->id }}">{{ $jobDuty->name }}</option>
                                    @endforeach
                                </select>
                                @error('job_duty_id')
                                    <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Judul Template <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.defer="title"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    placeholder="Contoh: Backup Data Aplikasi"
                                >
                                @error('title')
                                    <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Kategori</label>
                                <input
                                    type="text"
                                    wire:model.defer="category"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    placeholder="Contoh: Server / Aplikasi / Backup"
                                >
                                @error('category')
                                    <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Template Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                rows="5"
                                wire:model.defer="description_template"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                placeholder="Contoh: Melakukan backup data terhadap aplikasi {aplikasi} pada tanggal {tanggal} di storage lokal."
                            ></textarea>
                            @error('description_template')
                                <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">Template Hasil</label>
                            <textarea
                                rows="4"
                                wire:model.defer="result_template"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                placeholder="Contoh: Backup berjalan lancar dan tidak ditemukan kendala."
                            ></textarea>
                            @error('result_template')
                                <div class="mt-1.5 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <input
                                    type="checkbox"
                                    wire:model.defer="is_active"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                >
                                Template aktif
                            </label>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                Placeholder yang bisa dipakai: <span class="font-semibold">{unit}</span>, <span class="font-semibold">{tupoksi}</span>, <span class="font-semibold">{server}</span>, <span class="font-semibold">{aplikasi}</span>, <span class="font-semibold">{tanggal}</span>.
                            </p>
                        </div>
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
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Simpan Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
