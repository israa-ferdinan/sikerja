<x-app-layout>
    @php
        $user = auth()->user();
    @endphp

    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[220px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="archive" class="h-4 w-4" />
                        Operasional SIM/TI
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Upload Arsip Operasional
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Unggah file final rekap operasional SIM/TI dalam format Excel,
                        PDF, atau dokumen pendukung lainnya.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="lock" class="h-3.5 w-3.5" />
                            Storage Private
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="edit-3" class="h-3.5 w-3.5" />
                            Status Awal Draft
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="download" class="h-3.5 w-3.5" />
                            Protected Download
                        </span>
                    </div>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ route('operations.documents.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
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
                            Arsip belum dapat disimpan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-rose-700">
                            Periksa kembali field yang ditandai dan lengkapi data wajib.
                        </p>
                    </div>
                </div>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('operations.documents.store') }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

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
                                Lengkapi unit, kategori, judul, dan akses dokumen.
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
                                            (string) old('unit_id')
                                            === (string) $unit->id
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
                                        {{ $user->employee?->unit?->name ?? 'Unit tidak ditemukan' }}
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Unit ditentukan otomatis dari akun pengelola.
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
                                        @selected(old('category') === $value)
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
                                                \App\Models\OperationalDocument::VISIBILITY_UNIT
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
                            value="{{ old('title') }}"
                            required
                            maxlength="255"
                            placeholder="Contoh: Rekap Jaringan Bulan Juli 2026"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
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
                                value="{{ old('document_number') }}"
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
                                value="{{ old('document_date', now()->toDateString()) }}"
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
                                Isi bulan dan tahun bila arsip berkaitan dengan periode tertentu.
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
                                            now()->month
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
                                            now()->year
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

            {{-- FILE DAN KETERANGAN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <x-icon name="upload-cloud" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                File Arsip
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Unggah file final yang akan disimpan sebagai arsip private.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    <div>
                        <label
                            for="file"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Pilih File
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="file"
                            type="file"
                            name="file"
                            accept=".xlsx,.xls,.pdf,.doc,.docx"
                            required
                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-sky-700 focus:border-sky-500 focus:ring-sky-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Format didukung: XLSX, XLS, PDF, DOC, dan DOCX.
                            Ukuran maksimal 20 MB.
                        </p>

                        @error('file')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="description"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Keterangan
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Tambahkan keterangan singkat bila diperlukan."
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- LIFECYCLE INFO --}}
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                        <x-icon name="activity" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-amber-900">
                            Dokumen disimpan sebagai Draft
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Draft masih dapat diedit, diganti file, dipublikasikan,
                            atau dihapus. Pegawai belum dapat melihat dokumen sebelum
                            statusnya Dipublikasikan dan visibilitasnya Unit.
                        </p>
                    </div>
                </div>
            </section>

            {{-- ACTION --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-slate-500">
                        Field bertanda
                        <span class="font-semibold text-rose-500">*</span>
                        wajib diisi.
                    </p>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row">
                        <a
                            href="{{ route('operations.documents.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <x-icon name="arrow-left" class="h-4 w-4" />
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="upload-cloud" class="h-4 w-4" />
                            Upload Arsip
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>