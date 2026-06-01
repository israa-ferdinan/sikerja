<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Master Data Tupoksi</h1>
            <p class="text-sm text-gray-500">Kelola data tugas pokok dan fungsi sebagai referensi laporan kerja.</p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            + Tambah Tupoksi
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="p-4">
            <div class="mb-4">
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm md:w-1/3"
                    placeholder="Cari tupoksi, deskripsi, atau unit..."
                >
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="border-b px-3 py-3">No</th>
                            <th class="border-b px-3 py-3">Unit</th>
                            <th class="border-b px-3 py-3">Nama Tupoksi</th>
                            <th class="border-b px-3 py-3">Deskripsi</th>
                            <th class="border-b px-3 py-3">Klasifikasi</th>
                            <th class="border-b px-3 py-3">Objek Pekerjaan</th>
                            <th class="border-b px-3 py-3">Status</th>
                            <th class="border-b px-3 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($tupoksis as $tupoksi)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b px-3 py-3">
                                    {{ $tupoksis->firstItem() + $loop->index }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $tupoksi->unit?->name ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3 font-medium text-gray-800">
                                    {{ $tupoksi->name }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $tupoksi->description ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    @if ($tupoksi->classification)
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">
                                            {{ $tupoksi->classification->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="border-b px-3 py-3">
                                    <div class="font-medium text-gray-700">
                                        {{ $tupoksi->object_type_label }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $tupoksi->work_object_label }}
                                    </div>
                                </td>

                                <td class="border-b px-3 py-3">
                                    @if ($tupoksi->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="border-b px-3 py-3">
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            wire:click="openEditModal({{ $tupoksi->id }})"
                                            class="rounded bg-yellow-500 px-3 py-1 text-xs font-semibold text-white hover:bg-yellow-600">
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            wire:confirm="Yakin mau hapus tupoksi ini?"
                                            wire:click="delete({{ $tupoksi->id }})"
                                            class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-gray-500">
                                    Data tupoksi belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tupoksis->links() }}
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="mx-4 w-full max-w-3xl rounded-xl bg-white shadow-lg">
                <form wire:submit.prevent="save">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $isEdit ? 'Edit Tupoksi' : 'Tambah Tupoksi' }}
                        </h2>

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="text-gray-500 hover:text-gray-700">
                            ✕
                        </button>
                    </div>

                    <div class="space-y-4 p-6">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Unit <span class="text-red-500">*</span>
                            </label>

                            <select
                                wire:model.defer="unit_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('unit_id')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Nama Tupoksi <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Monitoring dan pemeliharaan server"
                            >

                            @error('name')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Deskripsi
                            </label>

                            <textarea
                                rows="4"
                                wire:model.defer="description"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Tuliskan detail tupoksi..."
                            ></textarea>

                            @error('description')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Klasifikasi Tupoksi
                                </label>

                                <select
                                    wire:model.defer="duty_classification_id"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">Pilih Klasifikasi</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}">
                                            {{ $classification->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('duty_classification_id')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Jenis Objek Pekerjaan
                                </label>

                                <select
                                    wire:model.live="object_type"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    @foreach ($this->objectTypeOptions as $value => $label)
                                        <option value="{{ $value }}">
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('object_type')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                wire:model.defer="is_active"
                                class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 rounded-b-xl border-t bg-gray-50 px-6 py-4">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>