<div class="relative z-10 p-6">
    <div class="relative z-10 max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Input Laporan Kerja Harian
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Isi laporan pekerjaan harian berdasarkan kegiatan, tupoksi, server, atau aplikasi yang dikerjakan.
            </p>
        </div>

        @if (session()->has('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form wire:submit.prevent="save" class="p-6 space-y-6">

                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <div class="flex flex-col md:flex-row md:items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Template Deskripsi
                            </label>
                            <select
                                wire:model.live="template_id"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-white"
                            >
                                <option value="">-- Pilih Template Laporan --</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">
                                        {{ $template->title }}
                                        @if($template->category)
                                            - {{ $template->category }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih template untuk mengisi judul dan deskripsi secara otomatis.
                            </p>
                        </div>

                        <div>
                            <button
                                type="button"
                                wire:click="cloneLastReport"
                                class="w-full md:w-auto px-4 py-2 rounded-lg bg-gray-800 text-white text-sm font-medium hover:bg-gray-900"
                            >
                                Clone Laporan Terakhir
                            </button>
                        </div>
                    </div>
                </div>

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
                        placeholder="Contoh: Pengecekan server aplikasi SIAKAD"
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
                        placeholder="Tuliskan detail pekerjaan yang dilakukan hari ini..."
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
                        placeholder="Opsional. Contoh: kendala, tindak lanjut, atau informasi tambahan."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('notes')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Foto Dokumentasi
                    </label>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <input
                            wire:key="photo-input-{{ $photoInputKey }}"
                            type="file"
                            wire:model="newPhotos"
                            multiple
                            accept="image/*"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        <div class="text-sm text-gray-500 whitespace-nowrap">
                            {{ count($photos) }}/5 foto
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                        Bisa pilih beberapa foto sekaligus, atau tambah foto satu per satu. Maksimal 5 foto, 5 MB per foto.
                    </p>

                    @error('photos')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    @error('photos.*')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    @error('newPhotos.*')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="newPhotos" class="text-sm text-blue-600 mt-2">
                        Menambahkan foto...
                    </div>

                    @if (count($photos) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4">
                            @foreach ($photos as $index => $photo)
                                <div
                                    wire:key="photo-preview-{{ $index }}-{{ $photo->getFilename() }}"
                                    class="rounded-lg border border-gray-200 bg-white overflow-hidden shadow-sm"
                                >
                                    <img
                                        src="{{ $photo->temporaryUrl() }}"
                                        class="block w-full h-28 object-cover"
                                        alt="Preview foto {{ $index + 1 }}"
                                    >

                                    <div class="px-2 py-2 border-t border-gray-100 bg-gray-50">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs text-gray-500">
                                                Foto {{ $index + 1 }}
                                            </span>

                                            <button
                                                type="button"
                                                wire:click="removePhoto({{ $index }})"
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
                        href="{{ route('pegawai.dashboard') }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save, newPhotos"
                        class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="save">
                            Simpan Laporan
                        </span>
                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>