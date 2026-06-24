<div class="relative z-10 p-4 sm:p-6">
    <div class="relative z-10 mx-auto max-w-5xl space-y-6">

        <x-page-hero
            badge="Laporan Harian Pegawai"
            title="Perbarui laporan kerja harian"
            description="Edit tanggal, tupoksi, detail kegiatan, dan dokumentasi foto laporan yang sudah tersimpan."
            icon="edit-3"
        >
            <x-slot:aside>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/15">
                            <x-icon name="calendar-days" class="h-5 w-5" />
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-100/80">
                                Tanggal Laporan
                            </p>
                            <p class="mt-1 text-lg font-bold text-white">
                                {{ $report_date ?: '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/10">
                            <p class="text-xs text-blue-100/75">Foto tersimpan</p>
                            <p class="mt-1 font-bold text-white">{{ $report->photos->count() }}</p>
                        </div>

                        <div class="rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/10">
                            <p class="text-xs text-blue-100/75">Foto baru</p>
                            <p class="mt-1 font-bold text-white">{{ count($photos) }}</p>
                        </div>
                    </div>
                </div>
            </x-slot:aside>
        </x-page-hero>

        @if ($isLocked)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                Laporan ini berada pada periode yang sudah difinalisasi oleh Kanit. Data laporan dan foto tidak dapat diubah.
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
                        Perbarui tanggal dan tupoksi pekerjaan. Field server atau aplikasi hanya muncul jika sesuai dengan objek pekerjaan tupoksi.
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
                            wire:model.live="duty_id"
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

                    @if ($this->shouldShowServerField)
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Server <span class="text-red-500">*</span>
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

                            @if ($this->shouldShowApplicationField)
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Server digunakan sebagai filter untuk menampilkan daftar aplikasi.
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ($this->shouldShowApplicationField)
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Aplikasi <span class="text-red-500">*</span>
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

                        @if ($duty_id && ! $this->shouldShowServerField && ! $this->shouldShowApplicationField)
                            <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                Tupoksi ini tidak memerlukan pilihan server atau aplikasi. Detail objek pekerjaan cukup ditulis pada uraian laporan harian.
                            </div>
                        @endif

                    @endif
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
                                            src="{{ route('reports.photos.show', $photo) }}"
                                            class="block h-32 w-full object-cover"
                                            alt="Foto laporan"
                                        >

                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                            <span class="text-xs font-semibold text-white">
                                                Foto tersimpan
                                            </span>
                                        </div>

                                        @if (! $isLocked)
                                            <button
                                                type="button"
                                                x-data
                                                x-on:click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus foto tersimpan?',
                                                    message: 'Foto ini akan dihapus dari laporan. Tindakan ini tidak bisa dibatalkan.',
                                                    confirmText: 'Ya, Hapus',
                                                    cancelText: 'Batal',
                                                    variant: 'danger',
                                                    onConfirm: () => $wire.removeExistingPhoto({{ $photo->id }})
                                                })"
                                                class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-lg ring-2 ring-white transition hover:bg-red-700 sm:opacity-0 sm:group-hover:opacity-100"
                                                title="Hapus foto"
                                            >
                                                <x-icon name="x" class="h-4 w-4" />
                                            </button>
                                        @else
                                            <div
                                                class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-slate-800/80 text-white shadow-lg ring-2 ring-white"
                                                title="Foto terkunci"
                                            >
                                                <x-icon name="lock" class="h-4 w-4" />
                                            </div>
                                        @endif
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
                                <x-icon name="upload-cloud" class="h-6 w-6" />
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
                                                x-data
                                                x-on:click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus foto baru?',
                                                    message: 'Foto ini akan dihapus dari daftar upload sementara. File belum tersimpan ke laporan.',
                                                    confirmText: 'Ya, Hapus',
                                                    cancelText: 'Batal',
                                                    variant: 'danger',
                                                    onConfirm: () => $wire.removeNewPhoto({{ $index }})
                                                })"
                                                class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-lg transition hover:bg-red-700"
                                                title="Hapus foto"
                                            >
                                                <x-icon name="x" class="h-4 w-4" />
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
                        @disabled($isLocked)
                        wire:loading.attr="disabled"
                        wire:target="update"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <x-icon wire:loading.remove wire:target="update" name="save" class="h-4 w-4" />

                        <span wire:loading.remove wire:target="update">
                            Simpan Perubahan
                        </span>

                        <span wire:loading wire:target="update">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>