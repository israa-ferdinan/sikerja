<div class="space-y-6">
    <x-page-hero
        badge="Master Sistem"
        title="Master Data Aplikasi"
        description="Kelola referensi aplikasi yang dapat dipilih pada laporan harian sesuai kategori objek pekerjaan."
        icon="app-window"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-slate-300">Total Aplikasi</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $applications->total() }}</p>
                        <p class="mt-1 text-xs text-slate-300">Sesuai pencarian aktif</p>
                    </div>
                    <div class="rounded-xl bg-white/10 p-3 text-cyan-200">
                        <x-icon name="app-window" class="h-6 w-6" />
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/80 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-white/70"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Aplikasi
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                        <x-icon name="filter" class="h-5 w-5 text-slate-500" />
                        Daftar Aplikasi
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">Cari berdasarkan nama aplikasi, URL, unit, server, hostname, atau IP address.</p>
                </div>

                <div class="relative w-full md:w-80">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                        placeholder="Cari aplikasi..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3">Aplikasi</th>
                        <th class="px-5 py-3">URL</th>
                        <th class="px-5 py-3">Unit</th>
                        <th class="px-5 py-3">Server</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $application)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4 text-slate-500">{{ $applications->firstItem() + $loop->index }}</td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-xl bg-slate-100 p-2 text-slate-600">
                                        <x-icon name="app-window" class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $application->name }}</p>
                                        <p class="mt-0.5 max-w-xs truncate text-xs text-slate-500">{{ $application->description ?: 'Tidak ada deskripsi.' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                @if ($application->url)
                                    <a href="{{ $application->url }}" target="_blank" class="inline-flex max-w-xs items-center gap-1.5 truncate font-medium text-cyan-700 hover:text-cyan-900">
                                        <x-icon name="external-link" class="h-3.5 w-3.5" />
                                        <span class="truncate">{{ $application->url }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-slate-600">{{ $application->unit?->name ?? '-' }}</td>

                            <td class="px-5 py-4 text-slate-600">
                                @if ($application->server)
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $application->server->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $application->server->ip_address ?: $application->server->hostname ?: 'Detail server belum diisi' }}
                                        </p>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                @if ($application->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                        <x-icon name="x-circle" class="h-3.5 w-3.5" />
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $application->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-100 transition hover:bg-amber-100"
                                    >
                                        <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:confirm="Yakin mau hapus aplikasi ini?"
                                        wire:click="delete({{ $application->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 ring-1 ring-rose-100 transition hover:bg-rose-100"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="rounded-2xl bg-slate-100 p-4 text-slate-500">
                                        <x-icon name="app-window" class="h-8 w-8" />
                                    </div>
                                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Data aplikasi belum tersedia</h3>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan aplikasi agar bisa dipilih pada laporan harian sesuai tupoksi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-5">
            {{ $applications->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <form wire:submit.prevent="save">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                        <div>
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                                <x-icon name="app-window" class="h-5 w-5 text-cyan-600" />
                                {{ $isEdit ? 'Edit Aplikasi' : 'Tambah Aplikasi' }}
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">Hubungkan aplikasi ke unit pengelola dan server jika diperlukan.</p>
                        </div>

                        <button type="button" wire:click="closeModal" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="grid gap-4 px-6 py-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Nama Aplikasi <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.defer="name" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" placeholder="Contoh: SIAKAD / PMB / LMS">
                            @error('name') <div class="mt-1 text-sm text-rose-600">{{ $message }}</div> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">URL</label>
                            <input type="text" wire:model.defer="url" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" placeholder="Contoh: https://siakad.domain.ac.id">
                            @error('url') <div class="mt-1 text-sm text-rose-600">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Unit Pengelola</label>
                            <select wire:model.defer="unit_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="mt-1 text-sm text-rose-600">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Server</label>
                            <select wire:model.defer="server_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih Server</option>
                                @foreach ($servers as $server)
                                    <option value="{{ $server->id }}">
                                        {{ $server->name }}
                                        @if ($server->ip_address)
                                            - {{ $server->ip_address }}
                                        @elseif ($server->hostname)
                                            - {{ $server->hostname }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('server_id') <div class="mt-1 text-sm text-rose-600">{{ $message }}</div> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                            <textarea rows="3" wire:model.defer="description" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" placeholder="Keterangan fungsi aplikasi..."></textarea>
                            @error('description') <div class="mt-1 text-sm text-rose-600">{{ $message }}</div> @enderror
                        </div>

                        <label class="md:col-span-2 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model.defer="is_active" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            Aplikasi aktif dan bisa dipilih pada laporan harian
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <button type="button" wire:click="closeModal" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">Batal</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Simpan Aplikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
