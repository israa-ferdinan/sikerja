<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="edit-3" class="h-4 w-4" />
                        Evaluasi
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Edit Hasil Evaluasi
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Perbarui informasi evaluasi, temuan, rekomendasi,
                        serta tautan rapat dan rekaman selama status masih Draft.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-200 ring-1 ring-inset ring-amber-300/20">
                            <x-icon name="edit-3" class="h-3.5 w-3.5" />
                            Status: {{ $record->status_label }}
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            Hanya Draft yang dapat diedit
                        </span>
                    </div>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ route('documentation.evaluasi.show', $record) }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali ke Detail
                    </a>
                </div>
            </div>
        </section>

        {{-- VALIDATION SUMMARY --}}
        @if ($errors->any())
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-rose-900">
                            Perubahan belum dapat disimpan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-rose-700">
                            Periksa kembali kolom yang ditandai di bawah.
                        </p>
                    </div>
                </div>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('documentation.evaluasi.update', $record) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- INFORMASI UTAMA --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="search-check" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Informasi Utama
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Perbarui unit, judul, jenis, tanggal, dan sumber evaluasi.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    @if (auth()->user()->isAdmin())
                        <div class="lg:col-span-2">
                            <label
                                for="unit_id"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Unit
                                <span class="text-rose-500">*</span>
                            </label>

                            <select
                                id="unit_id"
                                name="unit_id"
                                required
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">Pilih Unit</option>

                                @foreach ($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        @selected(
                                            (string) old('unit_id', $record->unit_id)
                                            === (string) $unit->id
                                        )
                                    >
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('unit_id')
                                <p class="mt-2 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif

                    <div class="lg:col-span-2">
                        <label
                            for="title"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Judul Evaluasi
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $record->title) }}"
                            required
                            maxlength="255"
                            placeholder="Contoh: Evaluasi Rapat Internal Unit SIM TI Bulan Januari"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('title')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="evaluation_type"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Jenis Evaluasi
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            id="evaluation_type"
                            name="evaluation_type"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            @foreach ($typeOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        old('evaluation_type', $record->evaluation_type)
                                        === $value
                                    )
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('evaluation_type')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="evaluation_date"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tanggal Evaluasi
                        </label>

                        <input
                            id="evaluation_date"
                            type="date"
                            name="evaluation_date"
                            value="{{ old(
                                'evaluation_date',
                                $record->evaluation_date?->toDateString()
                            ) }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('evaluation_date')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label
                            for="source"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Sumber / Kegiatan
                        </label>

                        <input
                            id="source"
                            type="text"
                            name="source"
                            value="{{ old('source', $record->source) }}"
                            maxlength="255"
                            placeholder="Contoh: Rapat Internal, Monitoring Target Tahunan, atau Evaluasi Kegiatan"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Isi sumber kegiatan atau forum yang menghasilkan evaluasi ini.
                        </p>

                        @error('source')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- TAUTAN RAPAT DAN REKAMAN --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <x-icon name="external-link" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Tautan Rapat dan Rekaman
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Perbarui tautan Zoom atau Google Drive bila tersedia.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label
                            for="zoom_link"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tautan Zoom
                        </label>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-icon name="video" class="h-4 w-4 text-slate-400" />
                            </div>

                            <input
                                id="zoom_link"
                                type="url"
                                name="zoom_link"
                                value="{{ old('zoom_link', $record->zoom_link) }}"
                                maxlength="255"
                                placeholder="https://zoom.us/..."
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Opsional. Isi bila evaluasi atau rapat dilakukan melalui Zoom.
                        </p>

                        @error('zoom_link')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="google_drive_link"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tautan Google Drive / Rekaman
                        </label>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-icon name="folder" class="h-4 w-4 text-slate-400" />
                            </div>

                            <input
                                id="google_drive_link"
                                type="url"
                                name="google_drive_link"
                                value="{{ old(
                                    'google_drive_link',
                                    $record->google_drive_link
                                ) }}"
                                maxlength="255"
                                placeholder="https://drive.google.com/..."
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Opsional. Dapat diisi folder bukti, rekaman Zoom, atau dokumen eksternal.
                        </p>

                        @error('google_drive_link')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- HASIL DAN REKOMENDASI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <x-icon name="sticky-note" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Hasil dan Rekomendasi
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Perbarui temuan, kendala, kesimpulan, dan rekomendasi.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label
                            for="findings"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Temuan / Hasil Evaluasi
                        </label>

                        <textarea
                            id="findings"
                            name="findings"
                            rows="7"
                            placeholder="Tuliskan hasil evaluasi, temuan, kendala, atau catatan penting"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('findings', $record->findings) }}</textarea>

                        @error('findings')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="recommendation"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Rekomendasi
                        </label>

                        <textarea
                            id="recommendation"
                            name="recommendation"
                            rows="7"
                            placeholder="Tuliskan rekomendasi berdasarkan hasil evaluasi"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('recommendation', $record->recommendation) }}</textarea>

                        @error('recommendation')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- INFORMASI DOKUMEN PENDUKUNG --}}
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="paperclip" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-sky-900">
                            Dokumen pendukung dikelola dari halaman detail
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-sky-700">
                            Perubahan metadata di halaman ini tidak menghapus atau mengganti
                            dokumen pendukung yang sudah terlampir.
                        </p>
                    </div>
                </div>
            </section>

            {{-- AKSI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('documentation.evaluasi.show', $record) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                    >
                        <x-icon name="check-circle" class="h-4 w-4" />
                        Simpan Perubahan
                    </button>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>