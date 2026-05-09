<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Master Template Laporan</h1>
            <p class="text-sm text-gray-500">
                Kelola template deskripsi dan hasil laporan kerja agar input laporan lebih cepat.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            + Tambah Template
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
                    placeholder="Cari judul, kategori, unit, tupoksi, atau isi template..."
                >
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="border-b px-3 py-3">No</th>
                            <th class="border-b px-3 py-3">Judul</th>
                            <th class="border-b px-3 py-3">Kategori</th>
                            <th class="border-b px-3 py-3">Unit</th>
                            <th class="border-b px-3 py-3">Tupoksi</th>
                            <th class="border-b px-3 py-3">Template Deskripsi</th>
                            <th class="border-b px-3 py-3">Status</th>
                            <th class="border-b px-3 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($reportTemplates as $template)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b px-3 py-3">
                                    {{ $reportTemplates->firstItem() + $loop->index }}
                                </td>

                                <td class="border-b px-3 py-3 font-medium text-gray-800">
                                    {{ $template->title }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $template->category ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $template->unit?->name ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ $template->jobDuty?->name ?? '-' }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    {{ \Illuminate\Support\Str::limit($template->description_template, 100) }}
                                </td>

                                <td class="border-b px-3 py-3">
                                    @if ($template->is_active)
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
                                            wire:click="openEditModal({{ $template->id }})"
                                            class="rounded bg-yellow-500 px-3 py-1 text-xs font-semibold text-white hover:bg-yellow-600">
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            wire:confirm="Yakin mau hapus template ini?"
                                            wire:click="delete({{ $template->id }})"
                                            class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-gray-500">
                                    Data template laporan belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reportTemplates->links() }}
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="mx-4 w-full max-w-3xl rounded-xl bg-white shadow-lg">
                <form wire:submit.prevent="save">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $isEdit ? 'Edit Template Laporan' : 'Tambah Template Laporan' }}
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
                                Unit
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
                                Tupoksi
                            </label>

                            <select
                                wire:model.defer="job_duty_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option value="">Pilih Tupoksi</option>
                                @foreach ($jobDuties as $jobDuty)
                                    <option value="{{ $jobDuty->id }}">
                                        {{ $jobDuty->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('job_duty_id')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Judul Template <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                wire:model.defer="title"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Backup Data Aplikasi"
                            >

                            @error('title')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Kategori
                            </label>

                            <input
                                type="text"
                                wire:model.defer="category"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Server / Aplikasi / Backup / Monitoring"
                            >

                            @error('category')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Template Deskripsi <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                rows="5"
                                wire:model.defer="description_template"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Melakukan backup data terhadap aplikasi {aplikasi} pada tanggal {tanggal} di storage lokal."
                            ></textarea>

                            @error('description_template')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Template Hasil
                            </label>

                            <textarea
                                rows="4"
                                wire:model.defer="result_template"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                placeholder="Contoh: Backup berjalan lancar dan tidak ditemukan kendala."
                            ></textarea>

                            @error('result_template')
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

                        <p class="text-xs text-gray-500">
                            Placeholder yang bisa dipakai nanti: {unit}, {tupoksi}, {server}, {aplikasi}, {tanggal}.
                        </p>
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