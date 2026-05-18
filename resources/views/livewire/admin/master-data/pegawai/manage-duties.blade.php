<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Kelola Tupoksi Pegawai
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Atur tupoksi yang melekat pada pegawai.
            </p>
        </div>

        <a href="{{ route('admin.master-data.pegawai.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Nama Pegawai</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">
                    {{ $employee->name }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Unit</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">
                    {{ $employee->unit?->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Jabatan</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">
                    {{ $employee->positionData?->name ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-base font-bold text-gray-900">
                    Daftar Tupoksi
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Centang tupoksi yang menjadi tanggung jawab pegawai ini.
                </p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($duties as $duty)
                    <label class="flex cursor-pointer items-start gap-4 px-5 py-4 hover:bg-gray-50">
                        <input
                            type="checkbox"
                            wire:model="selectedDuties"
                            value="{{ $duty->id }}"
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <p class="font-semibold text-gray-900">
                                    {{ $duty->name }}
                                </p>

                                <span class="inline-flex w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                    {{ $duty->unit?->name ?? 'Umum' }}
                                </span>
                            </div>

                            @if (!empty($duty->description))
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $duty->description }}
                                </p>
                            @endif
                        </div>
                    </label>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">
                        Belum ada data tupoksi.
                    </div>
                @endforelse
            </div>
        </div>

        @error('selectedDuties.*')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.master-data.pegawai.index') }}"
               class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Batal
            </a>

            <button type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                Simpan Tupoksi
            </button>
        </div>
    </form>
</div>