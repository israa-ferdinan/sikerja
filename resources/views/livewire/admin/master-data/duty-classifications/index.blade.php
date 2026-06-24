<div class="space-y-6">
    <x-page-hero
        badge="Master Data"
        title="Klasifikasi Tupoksi"
        description="Kelola klasifikasi pekerjaan untuk mengelompokkan tupoksi pegawai dan mempermudah pemetaan target kerja."
        icon="list-checks"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-300">
                            Total Klasifikasi
                        </p>
                        <p class="mt-2 text-3xl font-bold text-white">
                            {{ $classifications->total() }}
                        </p>
                        <p class="mt-1 text-xs text-slate-300">
                            Sesuai pencarian aktif
                        </p>
                    </div>

                    <div class="rounded-xl bg-white/10 p-3 text-cyan-200 ring-1 ring-white/10">
                        <x-icon name="clipboard-list" class="h-6 w-6" />
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="create"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-900"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Klasifikasi
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    @if ($showForm)
        <div
            id="duty-classification-form"
            x-data
            x-init="
                window.addEventListener('scroll-to-duty-classification-form', () => {
                    setTimeout(() => {
                        $el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 80);
                });
            "
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm scroll-mt-24"
        >
            <div class="mb-5 flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-cyan-50 p-2 text-cyan-700 ring-1 ring-cyan-100">
                        <x-icon name="edit-3" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">
                            {{ $isEdit ? 'Edit Klasifikasi Tupoksi' : 'Tambah Klasifikasi Tupoksi' }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Isi nama dan deskripsi klasifikasi agar tupoksi lebih mudah dikelompokkan.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="cancel"
                    class="rounded-xl border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                    aria-label="Tutup form"
                >
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-5">
                <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_220px]">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Nama Klasifikasi
                            </label>
                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                placeholder="Contoh: Aplikasi, Database, Jaringan"
                            >
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Deskripsi
                            </label>
                            <textarea
                                wire:model.defer="description"
                                rows="4"
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                placeholder="Tuliskan deskripsi singkat klasifikasi ini"
                            ></textarea>
                            @error('description')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            Status Data
                        </p>

                        <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                            <input
                                type="checkbox"
                                wire:model.defer="is_active"
                                class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500"
                            >
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Aktif</span>
                                <span class="block text-xs text-slate-500">Bisa dipakai pada Master Tupoksi.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <button
                        type="button"
                        wire:click="cancel"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        <x-icon name="check-circle" class="h-4 w-4" />
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-cyan-50 p-2 text-cyan-700 ring-1 ring-cyan-100">
                        <x-icon name="search" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">
                            Data Klasifikasi Tupoksi
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Data ini digunakan pada Master Tupoksi dan rekap target kerja.
                        </p>
                    </div>
                </div>

                <div class="w-full lg:w-80">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        placeholder="Cari klasifikasi..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Deskripsi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Tupoksi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($classifications as $classification)
                        <tr wire:key="classification-{{ $classification->id }}" class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="rounded-xl bg-slate-100 p-2 text-slate-600">
                                        <x-icon name="list-checks" class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-950">
                                            {{ $classification->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-400">
                                            ID: {{ $classification->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <div class="max-w-xl leading-6 text-slate-600">
                                    {{ $classification->description ?: '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ $classification->duties_count }} tupoksi
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                @if ($classification->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                        <x-icon name="x-circle" class="h-3.5 w-3.5" />
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right align-top">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $classification->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $classification->id }})"
                                        wire:confirm="Yakin ingin mengubah status klasifikasi ini?"
                                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition
                                            {{ $classification->is_active
                                                ? 'border-red-200 bg-white text-red-700 hover:bg-red-50'
                                                : 'border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50' }}"
                                    >
                                        <x-icon name="{{ $classification->is_active ? 'x-circle' : 'check-circle' }}" class="h-3.5 w-3.5" />
                                        {{ $classification->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="rounded-2xl bg-slate-100 p-4 text-slate-400">
                                        <x-icon name="list-checks" class="h-8 w-8" />
                                    </div>
                                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                                        Belum ada data klasifikasi tupoksi
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Tambahkan klasifikasi pertama agar Master Tupoksi lebih mudah dikelompokkan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $classifications->links() }}
        </div>
    </div>
</div>
