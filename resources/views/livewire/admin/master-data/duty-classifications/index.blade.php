<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Klasifikasi Tupoksi
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola klasifikasi pekerjaan untuk mengelompokkan tupoksi pegawai.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
        >
            + Tambah Klasifikasi
        </button>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($showForm)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Klasifikasi Tupoksi' : 'Tambah Klasifikasi Tupoksi' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Isi nama dan deskripsi klasifikasi agar tupoksi lebih mudah dikelompokkan.
                </p>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Nama Klasifikasi
                    </label>
                    <input
                        type="text"
                        wire:model.defer="name"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Aplikasi, Database, Jaringan"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Deskripsi
                    </label>
                    <textarea
                        wire:model.defer="description"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tuliskan deskripsi singkat klasifikasi ini"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        wire:model.defer="is_active"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>

                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        wire:click="cancel"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
                    >
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Daftar Klasifikasi
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Data ini akan digunakan pada Master Tupoksi dan rekap target kerja.
                    </p>
                </div>

                <div class="w-full sm:w-72">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Cari klasifikasi..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                        <th class="border-b px-3 py-3">Deskripsi</th>
                        <th class="border-b px-3 py-3">Klasifikasi</th>
                        <th class="border-b px-3 py-3">Objek Pekerjaan</th>
                        <th class="border-b px-3 py-3">Status</th>
                        <th class="border-b px-3 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($classifications as $classification)
                        <tr wire:key="classification-{{ $classification->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $classification->name }}
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="max-w-xl text-gray-600">
                                    {{ $classification->description ?: '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center align-top">
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                    {{ $classification->duties_count }} tupoksi
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center align-top">
                                @if ($classification->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right align-top">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $classification->id }})"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $classification->id }})"
                                        wire:confirm="Yakin ingin mengubah status klasifikasi ini?"
                                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition
                                            {{ $classification->is_active
                                                ? 'border-red-200 text-red-700 hover:bg-red-50'
                                                : 'border-green-200 text-green-700 hover:bg-green-50' }}"
                                    >
                                        {{ $classification->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                                Belum ada data klasifikasi tupoksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3">
            {{ $classifications->links() }}
        </div>
    </div>
</div>