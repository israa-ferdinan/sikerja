<div class="relative z-10 p-4 sm:p-6">
    @if ($missingEmployee)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Akun Anda belum terhubung dengan data pegawai. Silakan hubungi admin.
        </div>
    @elseif ($missingUnit)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Data pegawai Anda belum memiliki unit kerja. Silakan hubungi admin.
        </div>
    @else
        <div class="relative z-10 mx-auto max-w-6xl space-y-6">
            <x-ui.page-header
                title="Input Laporan Kerja Harian"
                subtitle="Catat aktivitas kerja, pilih tupoksi, dan lampirkan dokumentasi foto dalam satu form."
            />

            @if (session()->has('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <x-page-hero
                badge="Laporan Harian Pegawai"
                title="Buat laporan kerja harian dengan lebih rapi"
                description="Isi tanggal laporan, pilih tupoksi pekerjaan, tuliskan aktivitas dan hasil kerja, lalu tambahkan foto sebagai bukti kegiatan."
                icon="file-text"
            >
                <x-slot:aside>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-cyan-100">
                                    Status Form
                                </p>
                                <p class="mt-2 text-2xl font-bold text-white">
                                    Siap Diisi
                                </p>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-200">
                                <x-icon name="file-text" class="h-6 w-6" />
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                                Tanggal: {{ ! empty($form['report_date']) ? \Carbon\Carbon::parse($form['report_date'])->translatedFormat('d M Y') : '-' }}
                            </span>

                            <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                                Foto: {{ count($photos) }}/5
                            </span>
                        </div>
                    </div>
                </x-slot:aside>
            </x-page-hero>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                    {{-- Main Form --}}
                    <div class="space-y-6 xl:col-span-8">
                        {{-- Informasi Laporan --}}
                        <x-ui.card>
                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M8 2v4" />
                                        <path d="M16 2v4" />
                                        <rect width="18" height="18" x="3" y="4" rx="2" />
                                        <path d="M3 10h18" />
                                    </svg>
                                </div>

                                <div>
                                    <h2 class="text-base font-bold text-slate-900">
                                        Informasi Laporan
                                    </h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Pilih tanggal dan tupoksi pekerjaan. Field server atau aplikasi hanya muncul jika sesuai dengan objek pekerjaan tupoksi.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        Tanggal Laporan <span class="text-rose-500">*</span>
                                    </label>

                                    <input
                                        type="date"
                                        wire:model.change="form.report_date"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    >

                                    @error('form.report_date')
                                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        Tupoksi <span class="text-rose-500">*</span>
                                    </label>

                                    <select
                                        wire:model.live="selected_duty"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    >
                                        <option value="">Pilih Tupoksi</option>

                                        @if (count($personalDuties) > 0)
                                            <optgroup label="Tupoksi Pribadi">
                                                @foreach ($personalDuties as $duty)
                                                    <option value="personal:{{ $duty['id'] }}">
                                                        {{ $duty['name'] }}
                                                        @if (! empty($duty['classification_name']))
                                                            — {{ $duty['classification_name'] }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif

                                        @if (count($delegatedDuties) > 0)
                                            <optgroup label="Tupoksi Delegasi">
                                                @foreach ($delegatedDuties as $delegation)
                                                    <option value="delegation:{{ $delegation['id'] }}">
                                                        {{ $delegation['duty_name'] }} — dari {{ $delegation['owner_name'] }}
                                                        @if (! empty($delegation['classification_name']))
                                                            — {{ $delegation['classification_name'] }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>

                                    @error('selected_duty')
                                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    @if (count($personalDuties) === 0 && count($delegatedDuties) === 0)
                                        <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                            Belum ada tupoksi pribadi atau tupoksi delegasi aktif untuk Anda pada tanggal laporan ini.
                                            Silakan hubungi Admin atau Kanit.
                                        </div>
                                    @endif
                                </div>

                                @if ($this->selectedDutyInfo)
                                    <div class="md:col-span-2">
                                        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4 text-sm text-cyan-950">
                                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-cyan-700 shadow-sm">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M9 12l2 2 4-4" />
                                                        <circle cx="12" cy="12" r="10" />
                                                    </svg>
                                                </span>

                                                <span class="font-bold">
                                                    Info Tupoksi
                                                </span>

                                                <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                                                    {{ $this->selectedDutyInfo['source'] }}
                                                </span>

                                                @if (! empty($this->selectedDutyInfo['owner_name']))
                                                    <span class="text-xs font-semibold text-cyan-700">
                                                        dari {{ $this->selectedDutyInfo['owner_name'] }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="grid gap-3 sm:grid-cols-3">
                                                <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-cyan-100">
                                                    <div class="text-xs font-bold uppercase tracking-wide text-cyan-700">
                                                        Klasifikasi
                                                    </div>
                                                    <div class="mt-1 text-sm font-semibold text-slate-800">
                                                        {{ $this->selectedDutyInfo['classification_name'] }}
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-cyan-100">
                                                    <div class="text-xs font-bold uppercase tracking-wide text-cyan-700">
                                                        Jenis Objek
                                                    </div>
                                                    <div class="mt-1 text-sm font-semibold text-slate-800">
                                                        {{ $this->selectedDutyInfo['object_type_label'] }}
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-cyan-100">
                                                    <div class="text-xs font-bold uppercase tracking-wide text-cyan-700">
                                                        Objek
                                                    </div>
                                                    <div class="mt-1 text-sm font-semibold text-slate-800">
                                                        {{ $this->selectedDutyInfo['work_object_label'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($this->shouldShowServerField)
                                    <div>
                                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                            Server <span class="text-rose-500">*</span>
                                        </label>

                                        <select
                                            wire:model.change="form.server_id"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                        >
                                            <option value="">-- Pilih Server --</option>

                                            @foreach ($servers as $server)
                                                <option value="{{ $server->id }}">
                                                    {{ $server->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('form.server_id')
                                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror

                                        @if ($this->shouldShowApplicationField)
                                            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                                Server digunakan sebagai filter untuk menampilkan daftar aplikasi.
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if ($this->shouldShowApplicationField)
                                    <div>
                                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                            Aplikasi <span class="text-rose-500">*</span>
                                        </label>

                                        <select
                                            wire:model.change="form.application_id"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 disabled:bg-slate-100 disabled:text-slate-500"
                                            @disabled(empty($form['server_id']))
                                        >
                                            <option value="">
                                                {{ !empty($form['server_id']) ? '-- Pilih Aplikasi --' : 'Pilih server terlebih dahulu' }}
                                            </option>

                                            @foreach ($applications as $application)
                                                <option value="{{ $application->id }}">
                                                    {{ $application->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('form.application_id')
                                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                @if ($this->selectedDutyInfo && ! $this->shouldShowServerField && ! $this->shouldShowApplicationField)
                                    <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                                        Tupoksi ini tidak memerlukan pilihan server atau aplikasi. Detail objek pekerjaan cukup ditulis pada uraian laporan harian.
                                    </div>
                                @endif
                            </div>
                        </x-ui.card>

                        {{-- Detail Pekerjaan --}}
                        <x-ui.card>
                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </div>

                                <div>
                                    <h2 class="text-base font-bold text-slate-900">
                                        Detail Pekerjaan
                                    </h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Tulis ringkasan kegiatan dan catatan hasil pekerjaan secara jelas.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        Judul Kegiatan <span class="text-rose-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        wire:model.blur="form.title"
                                        placeholder="Contoh: Monitoring layanan aplikasi internal"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    >

                                    @error('form.title')
                                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        Deskripsi Kegiatan <span class="text-rose-500">*</span>
                                    </label>

                                    <textarea
                                        wire:model.blur="form.description"
                                        rows="5"
                                        placeholder="Tuliskan detail pekerjaan yang dilakukan hari ini..."
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    ></textarea>

                                    @error('form.description')
                                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        Catatan / Hasil Pekerjaan
                                    </label>

                                    <textarea
                                        wire:model.blur="form.notes"
                                        rows="3"
                                        placeholder="Opsional. Contoh: hasil pekerjaan, kendala, tindak lanjut, atau informasi tambahan."
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    ></textarea>

                                    @error('form.notes')
                                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </x-ui.card>
                    </div>

                    {{-- Sidebar --}}
                    <div class="space-y-6 xl:col-span-4">
                        {{-- Template Cepat --}}
                        <x-ui.card>
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-base font-bold text-slate-900">
                                        Template Cepat
                                    </h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Gunakan template atau clone laporan terakhir.
                                    </p>
                                </div>

                                <span class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                                    Opsional
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        Template Deskripsi
                                    </label>

                                    <select
                                        wire:model.change="form.template_id"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
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

                                    <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                        Template akan mengisi judul, deskripsi, dan hasil secara otomatis.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    wire:click="cloneLastReport"
                                    wire:loading.attr="disabled"
                                    wire:target="cloneLastReport"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 disabled:cursor-not-allowed disabled:opacity-70"
                                >
                                    <svg wire:loading.remove wire:target="cloneLastReport" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M8 8H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2" />
                                        <path d="M16 2h6v6" />
                                        <path d="m22 2-10 10" />
                                    </svg>

                                    <span wire:loading.remove wire:target="cloneLastReport">
                                        Clone Laporan Terakhir
                                    </span>

                                    <span wire:loading.flex wire:target="cloneLastReport" class="items-center gap-2">
                                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                                        Mengambil...
                                    </span>
                                </button>
                            </div>
                        </x-ui.card>

                        {{-- Dokumentasi Foto --}}
                        <x-ui.card>
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-base font-bold text-slate-900">
                                        Dokumentasi Foto
                                    </h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Upload foto pendukung laporan.
                                    </p>
                                </div>

                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                                    {{ count($photos) }}/5
                                </span>
                            </div>

                            <div class="rounded-2xl border border-dashed border-cyan-300 bg-cyan-50/60 p-5">
                                <label class="flex cursor-pointer flex-col items-center justify-center text-center">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-cyan-700 shadow-sm ring-1 ring-cyan-100">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <path d="M17 8l-5-5-5 5" />
                                            <path d="M12 3v12" />
                                        </svg>
                                    </div>

                                    <span class="text-sm font-bold text-slate-800">
                                        Klik untuk upload foto
                                    </span>

                                    <span class="mt-1 text-xs leading-5 text-slate-500">
                                        Maksimal 5 foto, ukuran 5 MB per foto.
                                    </span>

                                    <input
                                        wire:key="photo-input-{{ $photoInputKey }}"
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
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror

                                @error('photos.*')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror

                                @error('newPhotos.*')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if (count($photos) > 0)
                                <div class="mt-5">
                                    <h3 class="mb-3 text-sm font-bold text-slate-800">
                                        Preview Foto
                                    </h3>

                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach ($photos as $index => $photo)
                                            <div
                                                wire:key="photo-preview-{{ $index }}-{{ $photo->getFilename() }}"
                                                class="relative overflow-visible"
                                            >
                                                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                                    <img
                                                        src="{{ $photo->temporaryUrl() }}"
                                                        class="block h-28 w-full object-cover"
                                                        alt="Preview foto {{ $index + 1 }}"
                                                    >

                                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                                        <span class="text-xs font-semibold text-white">
                                                            Foto {{ $index + 1 }}
                                                        </span>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        x-data
                                                        x-on:click="$dispatch('open-confirm-modal', {
                                                            title: 'Hapus foto?',
                                                            message: 'Foto ini akan dihapus dari daftar upload sementara. File belum tersimpan ke laporan.',
                                                            confirmText: 'Ya, Hapus',
                                                            cancelText: 'Batal',
                                                            variant: 'danger',
                                                            onConfirm: () => $wire.removePhoto({{ $index }})
                                                        })"
                                                        class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white shadow-lg ring-2 ring-white transition hover:bg-rose-700 sm:opacity-0 sm:group-hover:opacity-100"
                                                        title="Hapus foto"
                                                    >
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                            <path d="M18 6 6 18" />
                                                            <path d="m6 6 12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </x-ui.card>
                    </div>
                </div>

                {{-- Action --}}
                <div class="sticky bottom-0 z-20 -mx-4 border-t border-slate-200 bg-white/90 px-4 py-4 backdrop-blur sm:static sm:mx-0 sm:rounded-2xl sm:border sm:border-slate-200 sm:bg-white sm:shadow-sm">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-slate-500">
                            Pastikan laporan, tupoksi, dan dokumentasi sudah sesuai sebelum disimpan.
                        </p>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                            <a
                                href="{{ route('pegawai.dashboard') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Kembali
                            </a>

                            <button
                                type="submit"
                                @disabled(count($personalDuties) === 0 && count($delegatedDuties) === 0)
                                wire:loading.attr="disabled"
                                wire:target="save,newPhotos"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <svg wire:loading.remove wire:target="save" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" />
                                    <path d="M17 21v-8H7v8" />
                                    <path d="M7 3v5h8" />
                                </svg>

                                <span wire:loading.remove wire:target="save">
                                    Simpan Laporan
                                </span>

                                <span wire:loading.flex wire:target="save" class="items-center gap-2">
                                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>