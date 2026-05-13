<div class="relative z-10 p-4 sm:p-6">
    <div class="relative z-10 mx-auto max-w-5xl space-y-6">

        <x-ui.page-header
            title="Edit Laporan Kerja Harian"
            subtitle="Perbarui laporan kerja harian dan foto dokumentasi yang sudah tersimpan."
        />

        @if (session()->has('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="update" class="space-y-6">

            {{-- Informasi Laporan --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Informasi Laporan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Perbarui tanggal, tupoksi, server, dan aplikasi yang berkaitan dengan laporan.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tanggal Laporan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            wire:model="report_date"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('report_date')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tupoksi
                        </label>

                        <select
                            wire:model="duty_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >
                            <option value="">-- Pilih Tupoksi --</option>

                            @foreach ($duties as $duty)
                                <option value="{{ $duty->id }}">
                                    {{ $duty->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('duty_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Server
                        </label>

                        <select
                            wire:model.live="server_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >
                            <option value="">-- Pilih Server --</option>

                            @foreach ($servers as $server)
                                <option value="{{ $server->id }}">
                                    {{ $server->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('server_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Aplikasi
                        </label>

                        <select
                            wire:model="application_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500"
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
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            {{-- Detail Pekerjaan --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Detail Pekerjaan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Perbarui ringkasan kegiatan dan catatan hasil pekerjaan.
                    </p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Judul Kegiatan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            wire:model="title"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('title')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Deskripsi Kegiatan <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            wire:model="description"
                            rows="5"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        ></textarea>

                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Catatan Tambahan
                        </label>

                        <textarea
                            wire:model="notes"
                            rows="3"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        ></textarea>

                        @error('notes')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            {{-- Dokumentasi Foto --}}
            <x-ui.card>
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Dokumentasi Foto
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Kelola foto lama atau tambahkan foto dokumentasi baru.
                        </p>
                    </div>

                    <x-ui.badge variant="neutral">
                        Total: {{ $report->photos->count() + count($photos) }}/5 foto
                    </x-ui.badge>
                </div>

                {{-- Foto Lama --}}
                <div>
                    <h3 class="mb-3 text-sm font-bold text-slate-800">
                        Foto Dokumentasi Saat Ini
                    </h3>

                    @if ($report->photos->count() > 0)
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                            @foreach ($report->photos as $photo)
                                <div
                                    wire:key="existing-photo-{{ $photo->id }}"
                                    class="relative overflow-visible"
                                >
                                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                        <img
                                            src="{{ asset('storage/' . $photo->file_path) }}"
                                            class="block h-32 w-full object-cover"
                                            alt="Foto laporan"
                                        >

                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                            <span class="text-xs font-semibold text-white">
                                                Foto tersimpan
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeExistingPhoto({{ $photo->id }})"
                                            wire:confirm="Hapus foto ini?"
                                            class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-lg ring-2 ring-white transition hover:bg-red-700 sm:opacity-0 sm:group-hover:opacity-100"
                                            title="Hapus foto"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                            <p class="text-sm font-medium text-slate-600">
                                Belum ada foto tersimpan.
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Tambahkan foto baru melalui upload di bawah.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Upload Foto Baru --}}
                <div class="mt-6">
                    <h3 class="mb-3 text-sm font-bold text-slate-800">
                        Tambah Foto Baru
                    </h3>

                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                        <label class="flex cursor-pointer flex-col items-center justify-center text-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5" />
                                </svg>
                            </div>

                            <span class="text-sm font-semibold text-slate-700">
                                Klik untuk tambah foto baru
                            </span>

                            <span class="mt-1 text-xs leading-5 text-slate-500">
                                Maksimal total 5 foto. Foto baru akan dikompres otomatis.
                            </span>

                            <input
                                wire:key="edit-photo-input-{{ $photoInputKey }}"
                                type="file"
                                wire:model="newPhotos"
                                multiple
                                accept="image/*"
                                class="hidden"
                            >
                        </label>

                        <div class="mt-4">
                            <x-ui.loading target="newPhotos" text="Menambahkan foto..." />
                        </div>

                        @error('photos')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @error('photos.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @error('newPhotos.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if (count($photos) > 0)
                        <div class="mt-5">
                            <h3 class="mb-3 text-sm font-bold text-slate-800">
                                Preview Foto Baru
                            </h3>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                @foreach ($photos as $index => $photo)
                                    <div
                                        wire:key="edit-new-photo-{{ $index }}-{{ $photo->getFilename() }}"
                                        class="relative overflow-visible"
                                    >
                                        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                            <img
                                                src="{{ $photo->temporaryUrl() }}"
                                                class="block h-32 w-full object-cover"
                                                alt="Preview foto baru {{ $index + 1 }}"
                                            >

                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                                <span class="text-xs font-semibold text-white">
                                                    Foto baru {{ $index + 1 }}
                                                </span>
                                            </div>

                                            <button
                                                type="button"
                                                wire:click="removeNewPhoto({{ $index }})"
                                                wire:confirm="Hapus foto ini?"
                                                class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-lg ring-2 ring-white transition hover:bg-red-700 sm:opacity-0 sm:group-hover:opacity-100"
                                                title="Hapus foto"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Action --}}
            <div class="sticky bottom-0 z-20 -mx-4 border-t border-slate-200 bg-white/90 px-4 py-4 backdrop-blur sm:static sm:mx-0 sm:rounded-2xl sm:border sm:border-slate-200 sm:bg-white sm:shadow-sm">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('pegawai.reports.show', $report) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="update,newPhotos"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="update">
                            Simpan Perubahan
                        </span>

                        <span wire:loading.flex wire:target="update" class="items-center gap-2">
                            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>