<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Master Data Pegawai</h1>
            <p class="text-sm text-gray-500">Kelola data pegawai untuk laporan kerja harian.</p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            + Tambah Pegawai
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
                    placeholder="Cari nama, NIP, No HP, email, jabatan, atau unit..."
                >
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="border-b px-3 py-3">No</th>
                            <th class="border-b px-3 py-3">Nama</th>
                            <th class="border-b px-3 py-3">NIP</th>
                            <th class="border-b px-3 py-3">Unit</th>
                            <th class="border-b px-3 py-3">Jabatan</th>
                            <th class="border-b px-3 py-3">No HP</th>
                            <th class="border-b px-3 py-3">Email</th>
                            <th class="border-b px-3 py-3">Status</th>
                            <th class="border-b px-3 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pegawais as $pegawai)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b px-3 py-3">
                                    {{ $pegawais->firstItem() + $loop->index }}
                                </td>

                                <td class="border-b px-3 py-3 font-medium text-gray-800">
                                    {{ $pegawai->name }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $pegawai->nip ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $pegawai->unit?->name ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $pegawai->jobPosition?->name ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $pegawai->phone ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $pegawai->email ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    @if ($pegawai->is_active)
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
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button
                                            type="button"
                                            wire:click="openEditModal({{ $pegawai->id }})"
                                            class="rounded bg-yellow-500 px-3 py-1 text-xs font-semibold text-white hover:bg-yellow-600">
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            wire:confirm="Yakin mau hapus pegawai ini?"
                                            wire:click="delete({{ $pegawai->id }})"
                                            class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">
                                            Hapus
                                        </button>

                                        <a
                                            href="{{ route('admin.master-data.pegawai.duties', $pegawai) }}"
                                            class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                                        >
                                            Kelola Tupoksi
                                        </a>

                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            {{ $pegawai->duties_count ?? 0 }} Tupoksi
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9 class="px-3 py-6 text-center text-gray-500">
                                    Data pegawai belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pegawais->links() }}
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="mx-4 w-full max-w-2xl rounded-xl bg-white shadow-lg">
                <form wire:submit.prevent="save">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $isEdit ? 'Edit Pegawai' : 'Tambah Pegawai' }}
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
                                Nama Pegawai <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Budi Santoso"
                            >

                            @error('name')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                NIP
                            </label>

                            <input
                                type="text"
                                wire:model.defer="nip"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: 198XXXXXXXXXXXXX"
                            >

                            @error('nip')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Jabatan
                            </label>

                            <select
                                wire:model.live="position_id"
                                class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Pilih Jabatan</option>

                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}">
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('position_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                No HP
                            </label>

                            <input
                                type="text"
                                wire:model.defer="phone"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: 08123456789"
                            >

                            @error('phone')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Email Pegawai
                            </label>

                            <input
                                type="email"
                                wire:model.defer="email"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: pegawai@domain.go.id"
                            >

                            @error('email')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
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