<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Master Data Server</h1>
            <p class="text-sm text-gray-500">Kelola data server sebagai referensi laporan kerja harian.</p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            + Tambah Server
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
                    placeholder="Cari nama server, IP, domain, lokasi..."
                >
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="border-b px-3 py-3">No</th>
                            <th class="border-b px-3 py-3">Nama Server</th>
                            <th class="border-b px-3 py-3">IP Address</th>
                            <th class="border-b px-3 py-3">Domain</th>
                            <th class="border-b px-3 py-3">Lokasi</th>
                            <th class="border-b px-3 py-3">Status</th>
                            <th class="border-b px-3 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($servers as $server)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b px-3 py-3">
                                    {{ $servers->firstItem() + $loop->index }}
                                </td>

                                <td class="border-b px-3 py-3 font-medium text-gray-800">
                                    {{ $server->name }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $server->ip_address ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $server->domain ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $server->location ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    @if ($server->is_active)
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
                                            wire:click="openEditModal({{ $server->id }})"
                                            class="rounded bg-yellow-500 px-3 py-1 text-xs font-semibold text-white hover:bg-yellow-600">
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            wire:confirm="Yakin mau hapus server ini?"
                                            wire:click="delete({{ $server->id }})"
                                            class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                                    Data server belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $servers->links() }}
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="mx-4 w-full max-w-2xl rounded-xl bg-white shadow-lg">
                <form wire:submit.prevent="save">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $isEdit ? 'Edit Server' : 'Tambah Server' }}
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
                                Nama Server <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Server SIAKAD"
                            >

                            @error('name')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                IP Address
                            </label>

                            <input
                                type="text"
                                wire:model.defer="ip_address"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: 192.168.1.10"
                            >

                            @error('ip_address')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Domain
                            </label>

                            <input
                                type="text"
                                wire:model.defer="domain"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: siakad.domain.ac.id"
                            >

                            @error('domain')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Lokasi
                            </label>

                            <input
                                type="text"
                                wire:model.defer="location"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Data Center / Cloud / VM"
                            >

                            @error('location')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Deskripsi
                            </label>

                            <textarea
                                rows="3"
                                wire:model.defer="description"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Keterangan fungsi server..."
                            ></textarea>

                            @error('description')
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