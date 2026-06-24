<div class="space-y-6">
    <x-page-hero
        badge="Tupoksi Personal"
        title="Kelola tupoksi pegawai"
        description="Atur daftar tupoksi yang melekat pada pegawai agar pilihan laporan harian tetap sesuai tanggung jawab masing-masing."
        icon="user-check"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Pegawai</p>
                        <p class="mt-1 text-lg font-bold text-white">
                            {{ $employee->name }}
                        </p>
                        <p class="mt-1 text-sm text-slate-300">
                            {{ $employee->positionData?->name ?? $employee->position?->name ?? 'Jabatan belum diisi' }}
                        </p>
                    </div>

                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-200 ring-1 ring-cyan-300/20">
                        <x-icon name="user-check" class="h-5 w-5" />
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-slate-200">
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">
                        Unit: {{ $employee->unit?->name ?? '-' }}
                    </span>
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">
                        Dipilih: {{ count($selectedDuties) }} tupoksi
                    </span>
                </div>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="flex justify-end">
        <a href="{{ route('admin.master-data.pegawai.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali ke Data Pegawai
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                    <x-icon name="user" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama Pegawai</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">{{ $employee->name }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                    <x-icon name="building-2" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Unit</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">{{ $employee->unit?->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <x-icon name="briefcase" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jabatan</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">
                        {{ $employee->positionData?->name ?? $employee->position?->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <x-icon name="clipboard-list" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Tupoksi</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Centang tupoksi yang menjadi tanggung jawab pegawai ini. Tupoksi yang tampil mengikuti unit pegawai dan tupoksi umum.
                        </p>
                    </div>
                </div>

                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $duties->count() }} tersedia
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($duties as $duty)
                    <label class="group flex cursor-pointer items-start gap-4 px-5 py-4 transition hover:bg-slate-50">
                        <input
                            type="checkbox"
                            wire:model="selectedDuties"
                            value="{{ $duty->id }}"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <p class="font-semibold text-slate-900">
                                    {{ $duty->name }}
                                </p>

                                <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 group-hover:bg-white">
                                    {{ $duty->unit?->name ?? 'Umum' }}
                                </span>
                            </div>

                            @if (!empty($duty->description))
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    {{ $duty->description }}
                                </p>
                            @endif
                        </div>
                    </label>
                @empty
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="clipboard-list" class="h-6 w-6" />
                        </div>
                        <h3 class="mt-4 text-sm font-bold text-slate-900">Belum ada data tupoksi</h3>
                        <p class="mt-1 text-sm text-slate-500">Tambahkan master tupoksi terlebih dahulu sebelum mengatur tupoksi pegawai.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @error('selectedDuties.*')
            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('admin.master-data.pegawai.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Batal
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                    wire:loading.attr="disabled"
                    wire:target="save">
                <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                    <x-icon name="check-circle" class="h-4 w-4" />
                    Simpan Tupoksi
                </span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
