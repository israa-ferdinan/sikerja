<div class="p-6">
    <div class="max-w-5xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Edit Laporan Kerja Harian
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Perbarui laporan kerja harian dan foto dokumentasi.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form wire:submit.prevent="update" class="p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Laporan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            wire:model="report_date"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('report_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tupoksi
                        </label>
                        <select
                            wire:model="duty_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">-- Pilih Tupoksi --</option>
                            @foreach ($duties as $duty)
                                <option value="{{ $duty->id }}">
                                    {{ $duty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('duty_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Server
                        </label>
                        <select
                            wire:model.live="server_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">-- Pilih Server --</option>
                            @foreach ($servers as $server)
                                <option value="{{ $server->id }}">
                                    {{ $server->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('server_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Aplikasi
                        </label>
                        <select
                            wire:model="application_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500"
                            @if (!$server_id) disabled @endif
                        >
                            <option value="">
                                {{ $server_id ? '-- Pilih Aplikasi --' : 'Pilih server terlebih dahulu' }}
                            </option>

                            @foreach ($applications as $application)
                                <option value="{{ $application->id }}">
                                    {{ $application->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('application_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="title"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('title')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        wire:model="description"
                        rows="5"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan Tambahan
                    </label>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('notes')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Dokumentasi Saat Ini
                    </label>

                    @if ($report->photos->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            @foreach ($report->photos as $photo)
                                <div
                                    wire:key="existing-photo-{{ $photo->id }}"
                                    class="rounded-lg border border-gray-200 bg-white overflow-hidden shadow-sm"
                                >
                                    <img
                                        src="{{ asset('storage/' . $photo->file_path) }}"
                                        class="block w-full h-28 object-cover"
                                        alt="Foto laporan"
                                    >

                                    <div class="px-2 py-2 border-t border-gray-100 bg-gray-50">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs text-gray-500">
                                                Foto
                                            </span>

                                            <button
                                                type="button"
                                                wire:click="removeExistingPhoto({{ $photo->id }})"
                                                wire:confirm="Hapus foto ini?"
                                                class="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-red-700"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">
                            Belum ada foto.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tambah Foto Baru
                    </label>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <input
                            wire:key="edit-photo-input-{{ $photoInputKey }}"
                            type="file"
                            wire:model="newPhotos"
                            multiple
                            accept="image/*"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        <div class="text-sm text-gray-500 whitespace-nowrap">
                            Total: {{ $report->photos->count() + count($photos) }}/5 foto
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                        Maksimal total 5 foto. Foto baru akan dikompres otomatis.
                    </p>

                    @error('photos')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    @error('photos.*')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="newPhotos" class="text-sm text-blue-600 mt-2">
                        Menambahkan foto...
                    </div>

                    @if (count($photos) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4">
                            @foreach ($photos as $index => $photo)
                                <div
                                    wire:key="edit-new-photo-{{ $index }}-{{ $photo->getFilename() }}"
                                    class="rounded-lg border border-gray-200 bg-white overflow-hidden shadow-sm"
                                >
                                    <img
                                        src="{{ $photo->temporaryUrl() }}"
                                        class="block w-full h-28 object-cover"
                                        alt="Preview foto baru {{ $index + 1 }}"
                                    >

                                    <div class="px-2 py-2 border-t border-gray-100 bg-gray-50">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs text-gray-500">
                                                Foto baru {{ $index + 1 }}
                                            </span>

                                            <button
                                                type="button"
                                                wire:click="removeNewPhoto({{ $index }})"
                                                wire:confirm="Hapus foto ini?"
                                                class="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-red-700"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('pegawai.reports.show', $report) }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="update, newPhotos"
                        class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="update">
                            Simpan Perubahan
                        </span>
                        <span wire:loading wire:target="update">
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>