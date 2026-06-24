<div class="space-y-6">
    <x-page-hero
        badge="Delegasi Tupoksi"
        title="Atur delegasi pekerjaan pegawai dengan lebih rapi"
        description="Kelola tupoksi yang didelegasikan dari pegawai pemilik kepada pegawai penerima tanpa mengubah tupoksi personal aslinya."
        icon="repeat-2"
    >
        <x-slot:aside>
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Delegasi Terdata</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ $delegations->total() }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-200 ring-1 ring-cyan-300/20">
                        <x-icon name="user-check" class="h-6 w-6" />
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-200">
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">
                        Status: {{ $status === '' ? 'Semua' : ((string) $status === '1' ? 'Aktif' : 'Nonaktif') }}
                    </span>
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">
                        Tanggal: {{ $date ?: 'Semua periode' }}
                    </span>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-sm transition hover:bg-cyan-50"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Delegasi
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                <x-icon name="filter" class="h-5 w-5" />
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Filter Delegasi</h2>
                <p class="text-xs text-gray-500">Cari berdasarkan tupoksi, pegawai, status, atau tanggal berlaku.</p>
            </div>
        </div>
        <div class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Cari</label>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari tupoksi / pegawai..."
                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select
                    wire:model.live="status"
                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal Berlaku</label>
                <input
                    type="date"
                    wire:model.live="date"
                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tupoksi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Pemilik</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Penerima Delegasi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Periode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($delegations as $delegation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 align-top">
                                <div class="font-semibold text-gray-900">
                                    {{ $delegation->duty?->name ?? '-' }}
                                </div>
                                @if ($delegation->notes)
                                    <div class="mt-1 line-clamp-2 text-xs text-gray-500">
                                        {{ $delegation->notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $delegation->ownerEmployee?->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $delegation->ownerEmployee?->jobPosition?->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $delegation->ownerEmployee?->unit?->name ?? '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $delegation->delegateEmployee?->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $delegation->delegateEmployee?->jobPosition?->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $delegation->delegateEmployee?->unit?->name ?? '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top text-gray-700">
                                <div>
                                    {{ $delegation->start_date?->format('d/m/Y') ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    s.d.
                                    {{ $delegation->end_date?->format('d/m/Y') ?? 'Tidak ditentukan' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top">
                                @if ($delegation->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 align-top text-gray-700">
                                {{ $delegation->createdBy?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $delegation->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-yellow-600"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $delegation->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $delegation->id }})"
                                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-semibold transition
                                            {{ $delegation->is_active
                                                ? 'bg-amber-100 text-amber-700 hover:bg-amber-200'
                                                : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}"
                                    >
                                        <span wire:loading.remove wire:target="toggleStatus({{ $delegation->id }})">
                                            {{ $delegation->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </span>

                                        <span wire:loading wire:target="toggleStatus({{ $delegation->id }})">
                                            Memproses...
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:confirm="Yakin mau hapus delegasi ini?"
                                        wire:click="delete({{ $delegation->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 text-gray-400 ring-1 ring-gray-200">
                                        <x-icon name="repeat-2" class="h-7 w-7" />
                                    </div>
                                    <h3 class="mt-4 text-sm font-bold text-gray-900">Belum ada data delegasi tupoksi</h3>
                                    <p class="mt-1 max-w-md text-sm text-gray-500">
                                        Delegasi yang dibuat akan muncul di sini dan bisa digunakan pegawai penerima saat mengisi laporan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-4 py-3">
            {{ $delegations->links() }}
        </div>
    </div>
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-3xl rounded-2xl bg-white shadow-xl">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                            <x-icon name="repeat-2" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">
                                {{ $editingId ? 'Edit Delegasi Tupoksi' : 'Tambah Delegasi Tupoksi' }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Pilih tupoksi milik pegawai tertentu untuk didelegasikan ke pegawai lain.
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="save" class="space-y-5 px-6 py-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Pegawai Pemilik Tupoksi
                            </label>
                            <select
                                wire:model.live="owner_employee_id"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Pilih pegawai pemilik</option>

                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->name }}
                                        @if ($employee->unit)
                                            — {{ $employee->unit->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <div class="mt-2 text-xs text-gray-500">
                                Owner ID: {{ $owner_employee_id ?? '-' }} |
                                Jumlah Tupoksi: {{ count($ownerDuties) }}
                            </div>

                            @error('owner_employee_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tupoksi yang Didelegasikan
                            </label>
                            <select
                                wire:model.live="duty_id"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                @disabled(!$owner_employee_id)
                            >
                                <option value="">
                                    {{ $owner_employee_id ? 'Pilih tupoksi' : 'Pilih pemilik dulu' }}
                                </option>

                                @foreach ($ownerDuties as $duty)
                                    <option value="{{ $duty['id'] }}">
                                        {{ $duty['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('duty_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($owner_employee_id && count($ownerDuties) === 0)
                                <p class="mt-1 text-xs text-yellow-700">
                                    Pegawai ini belum punya tupoksi.
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Pegawai Penerima Delegasi
                            </label>
                            <select
                                wire:model.live="delegate_employee_id"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Pilih penerima delegasi</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->name }}
                                        @if ($employee->unit)
                                            — {{ $employee->unit->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('delegate_employee_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Status
                            </label>
                            <select
                                wire:model.live="is_active"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            @error('is_active')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tanggal Mulai
                            </label>
                            <input
                                type="date"
                                wire:model.live="start_date"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('start_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tanggal Selesai
                            </label>
                            <input
                                type="date"
                                wire:model.live="end_date"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            <p class="mt-1 text-xs text-gray-400">
                                Kosongkan jika delegasi berlaku sampai dinonaktifkan.
                            </p>
                            @error('end_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Catatan
                        </label>
                        <textarea
                            wire:model.live="notes"
                            rows="3"
                            class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Didelegasikan karena pegawai pemilik sedang cuti."
                        ></textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                        <button
                            type="button"
                            wire:click="closeFormModal"
                            class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            <x-icon name="check-circle" class="h-4 w-4" />
                            {{ $editingId ? 'Update Delegasi' : 'Simpan Delegasi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>