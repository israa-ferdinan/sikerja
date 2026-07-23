<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="edit-3" class="h-4 w-4" />
                        Pengendalian
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Edit Metadata Surat
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Perbarui keterkaitan, identitas, visibilitas, dan informasi
                        koordinasi surat pengendalian tanpa mengganti file yang tersimpan.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="file-text" class="h-3.5 w-3.5" />
                            {{ $letter->original_name }}
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            {{ $letter->typeLabel() }}
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            {{ $letter->visibilityLabel() }}
                        </span>
                    </div>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ route('documentation.control.letters.show', $letter) }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali ke Detail
                    </a>
                </div>
            </div>
        </section>

        {{-- INFORMASI FILE --}}
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <x-icon name="info" class="h-5 w-5" />
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-amber-900">
                        File surat tidak diubah
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        Halaman ini hanya memperbarui metadata. File yang tetap digunakan:
                    </p>

                    <p class="mt-2 break-all text-sm font-semibold text-amber-950">
                        {{ $letter->original_name }}
                    </p>
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
            action="{{ route('documentation.control.letters.update', $letter) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- KETERKAITAN SURAT --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="clipboard-list" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Keterkaitan Surat
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Perbarui unit dan tindak lanjut yang berkaitan dengan surat.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
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
                                        (string) old('unit_id', $letter->unit_id)
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

                    <div>
                        <label
                            for="control_follow_up_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tindak Lanjut Terkait
                        </label>

                        <select
                            id="control_follow_up_id"
                            name="control_follow_up_id"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Arsip mandiri
                            </option>

                            @foreach ($followUps as $followUp)
                                <option
                                    value="{{ $followUp->id }}"
                                    @selected(
                                        (string) old(
                                            'control_follow_up_id',
                                            $letter->control_follow_up_id
                                        ) === (string) $followUp->id
                                    )
                                >
                                    {{ $followUp->title }}
                                    —
                                    {{ $followUp->unit?->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Kosongkan bila surat ingin disimpan sebagai arsip mandiri.
                        </p>

                        @error('control_follow_up_id')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- IDENTITAS SURAT --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <x-icon name="mail" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Identitas Surat
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Perbarui jenis, visibilitas, nomor, tanggal, dan perihal surat.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label
                            for="letter_type"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Jenis Surat
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            id="letter_type"
                            name="letter_type"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option
                                value="{{ \App\Models\ControlLetter::TYPE_INCOMING }}"
                                @selected(
                                    old('letter_type', $letter->letter_type)
                                    === \App\Models\ControlLetter::TYPE_INCOMING
                                )
                            >
                                Surat Masuk
                            </option>

                            <option
                                value="{{ \App\Models\ControlLetter::TYPE_OUTGOING }}"
                                @selected(
                                    old('letter_type', $letter->letter_type)
                                    === \App\Models\ControlLetter::TYPE_OUTGOING
                                )
                            >
                                Surat Keluar
                            </option>
                        </select>

                        @error('letter_type')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="visibility"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Visibilitas
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            id="visibility"
                            name="visibility"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option
                                value="{{ \App\Models\ControlLetter::VISIBILITY_UNIT }}"
                                @selected(
                                    old('visibility', $letter->visibility)
                                    === \App\Models\ControlLetter::VISIBILITY_UNIT
                                )
                            >
                                Unit — dapat dilihat Pegawai unit terkait
                            </option>

                            <option
                                value="{{ \App\Models\ControlLetter::VISIBILITY_RESTRICTED }}"
                                @selected(
                                    old('visibility', $letter->visibility)
                                    === \App\Models\ControlLetter::VISIBILITY_RESTRICTED
                                )
                            >
                                Terbatas — hanya Admin, Kanit, dan GKM
                            </option>
                        </select>

                        @error('visibility')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="letter_number"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Nomor Surat
                        </label>

                        <input
                            id="letter_number"
                            type="text"
                            name="letter_number"
                            value="{{ old('letter_number', $letter->letter_number) }}"
                            maxlength="255"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('letter_number')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="letter_date"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tanggal Surat
                        </label>

                        <input
                            id="letter_date"
                            type="date"
                            name="letter_date"
                            value="{{ old(
                                'letter_date',
                                $letter->letter_date?->format('Y-m-d')
                            ) }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('letter_date')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label
                            for="subject"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Perihal
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="subject"
                            type="text"
                            name="subject"
                            value="{{ old('subject', $letter->subject) }}"
                            required
                            maxlength="255"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('subject')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- INFORMASI KOORDINASI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <x-icon name="send" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Informasi Koordinasi
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Perbarui asal, tujuan, dan ringkasan isi surat.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label
                            for="sender"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Pengirim
                        </label>

                        <input
                            id="sender"
                            type="text"
                            name="sender"
                            value="{{ old('sender', $letter->sender) }}"
                            maxlength="255"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('sender')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="recipient"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Penerima
                        </label>

                        <input
                            id="recipient"
                            type="text"
                            name="recipient"
                            value="{{ old('recipient', $letter->recipient) }}"
                            maxlength="255"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('recipient')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label
                            for="summary"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Ringkasan Isi Surat
                        </label>

                        <textarea
                            id="summary"
                            name="summary"
                            rows="5"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('summary', $letter->summary) }}</textarea>

                        @error('summary')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- FILE AKTIF --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            File Surat Aktif
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Informasi file yang tetap digunakan setelah metadata diperbarui.
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nama File
                            </dt>

                            <dd class="mt-2 break-all text-sm font-semibold leading-6 text-slate-900">
                                {{ $letter->original_name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Jenis File
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $letter->mime_type ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Ukuran
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                @if ($letter->file_size)
                                    {{ number_format($letter->file_size / 1024, 1) }} KB
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <a
                        href="{{ route('documentation.control.letters.download', $letter) }}"
                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100"
                    >
                        <x-icon name="download" class="h-4 w-4" />
                        Unduh File Aktif
                    </a>
                </div>
            </section>

            {{-- INFORMASI AKSES --}}
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="lock" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-sky-900">
                            Perubahan visibilitas memengaruhi akses Pegawai
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-sky-700">
                            Visibilitas Unit mengizinkan Pegawai pada unit terkait melihat
                            dan mengunduh surat. Visibilitas Terbatas hanya dapat diakses
                            oleh Admin, Kanit, dan GKM.
                        </p>
                    </div>
                </div>
            </section>

            {{-- AKSI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('documentation.control.letters.show', $letter) }}"
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