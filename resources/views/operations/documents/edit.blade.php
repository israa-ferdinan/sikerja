<x-app-layout>
    @php
        $user = auth()->user();
    @endphp

    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[230px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="edit-3" class="h-4 w-4" />
                        Edit Arsip Operasional
                    </div>

                    <h1 class="mt-5 max-w-4xl text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $document->title }}
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Perbarui metadata dokumen atau ganti file arsip. Perubahan hanya
                        tersedia selama dokumen masih berstatus Draft.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-100">
                            <x-icon name="edit-3" class="h-3.5 w-3.5" />
                            {{ $document->status_label }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="lock" class="h-3.5 w-3.5" />
                            {{ $document->visibility_label }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="building" class="h-3.5 w-3.5" />
                            {{ $document->unit?->name ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row lg:flex-col lg:pl-8">
                    <a
                        href="{{ route('operations.documents.show', $document) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                    >
                        <x-icon name="chevron-right" class="h-4 w-4" />
                        Lihat Detail
                    </a>

                    <a
                        href="{{ route('operations.documents.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </section>

        {{-- FLASH ERROR --}}
        @if (session('error'))
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-700 ring-1 ring-inset ring-rose-200">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </div>

                    <p class="text-sm font-medium leading-6 text-rose-800">
                        {{ session('error') }}
                    </p>
                </div>
            </section>
        @endif

        {{-- ERROR SUMMARY --}}
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
                            Periksa kembali field yang ditandai dan lengkapi data wajib.
                        </p>
                    </div>
                </div>
            </section>
        @endif

        {{-- READ-ONLY WARNING --}}
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                    <x-icon name="alert-circle" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-amber-900">
                        Edit hanya tersedia saat Draft
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        Setelah dokumen dipublikasikan, metadata dan file tidak dapat diubah lagi.
                        Pastikan seluruh informasi sudah benar sebelum proses publish.
                    </p>
                </div>
            </div>
        </section>

        <form
            method="POST"
            action="{{ route('operations.documents.update', $document) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- IDENTITAS DOKUMEN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon name="clipboard-list" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Identitas Dokumen
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Perbarui unit, kategori, visibilitas, dan identitas arsip.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    @if ($user->isAdmin())
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
                                <option value="">
                                    Pilih Unit
                                </option>

                                @foreach ($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        @selected(
                                            (string) old(
                                                'unit_id',
                                                $document->unit_id
                                            ) === (string) $unit->id
                                        )
                                    >
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('unit_id')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 ring-1 ring-inset ring-slate-200">
                                    <x-icon name="building" class="h-4 w-4" />
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Unit Dokumen
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ $document->unit?->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Unit tidak dapat diubah oleh Kanit atau GKM.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div>
                            <label
                                for="category"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Kategori
                                <span class="text-rose-500">*</span>
                            </label>

                            <select
                                id="category"
                                name="category"
                                required
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">
                                    Pilih Kategori
                                </option>

                                @foreach ($categoryOptions as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(
                                            old(
                                                'category',
                                                $document->category
                                            ) === $value
                                        )
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('category')
                                <p class="mt-2 text-xs font-medium text-rose-600">
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
                                @foreach ($visibilityOptions as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(
                                            old(
                                                'visibility',
                                                $document->visibility
                                            ) === $value
                                        )
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Unit dapat dilihat pegawai setelah dipublikasikan.
                                Restricted hanya dapat diakses pengelola.
                            </p>

                            @error('visibility')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label
                            for="title"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Judul Dokumen
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $document->title) }}"
                            required
                            maxlength="255"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('title')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div>
                            <label
                                for="document_number"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Nomor Dokumen
                            </label>

                            <input
                                id="document_number"
                                type="text"
                                name="document_number"
                                value="{{ old(
                                    'document_number',
                                    $document->document_number
                                ) }}"
                                maxlength="255"
                                placeholder="Opsional"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >

                            @error('document_number')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="document_date"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Tanggal Dokumen
                            </label>

                            <input
                                id="document_date"
                                type="date"
                                name="document_date"
                                value="{{ old(
                                    'document_date',
                                    $document->document_date?->toDateString()
                                ) }}"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >

                            @error('document_date')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- PERIODE DOKUMEN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <x-icon name="calendar" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Periode Dokumen
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Perbarui periode arsip bila dokumen berkaitan dengan bulan tertentu.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-2">
                    <div>
                        <label
                            for="period_month"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Bulan
                        </label>

                        <select
                            id="period_month"
                            name="period_month"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Tidak ada periode
                            </option>

                            @foreach ($monthOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        (string) old(
                                            'period_month',
                                            $document->period_month
                                        ) === (string) $value
                                    )
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('period_month')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="period_year"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tahun
                        </label>

                        <select
                            id="period_year"
                            name="period_year"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Tidak ada periode
                            </option>

                            @foreach ($yearOptions as $year)
                                <option
                                    value="{{ $year }}"
                                    @selected(
                                        (string) old(
                                            'period_year',
                                            $document->period_year
                                        ) === (string) $year
                                    )
                                >
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>

                        @error('period_year')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- FILE ARSIP --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <x-icon name="file-text" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                File Arsip
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Pertahankan file saat ini atau unggah file pengganti.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 ring-1 ring-inset ring-slate-200">
                                <x-icon name="file-text" class="h-5 w-5" />
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    File Saat Ini
                                </p>

                                <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-900">
                                    {{ $document->file_original_name }}
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    {{ $document->file_mime_type ?? '-' }}
                                    ·
                                    {{ $document->file_size_label }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            for="file"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Ganti File
                        </label>

                        <input
                            id="file"
                            type="file"
                            name="file"
                            accept=".xlsx,.xls,.pdf,.doc,.docx"
                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-sky-700 focus:border-sky-500 focus:ring-sky-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Kosongkan bila file tidak ingin diganti. Format didukung:
                            XLSX, XLS, PDF, DOC, dan DOCX. Maksimal 20 MB.
                        </p>

                        @error('file')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- KETERANGAN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <x-icon name="sticky-note" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Keterangan
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Tambahkan atau perbarui informasi pendukung dokumen.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <label
                        for="description"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Keterangan Dokumen
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Tambahkan keterangan singkat bila diperlukan."
                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                    >{{ old('description', $document->description) }}</textarea>

                    @error('description')
                        <p class="mt-2 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </section>

            {{-- ACTION --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-slate-500">
                        File lama tetap digunakan apabila tidak ada file baru yang dipilih.
                    </p>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row">
                        <a
                            href="{{ route('operations.documents.show', $document) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <x-icon name="arrow-left" class="h-4 w-4" />
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>