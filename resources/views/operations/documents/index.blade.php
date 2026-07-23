<x-app-layout>
    @php
        $user = auth()->user();

        $isManager =
            $user->isAdmin()
            || $user->isKanit()
            || $user->isGkm();

        $hasActiveFilter =
            request()->filled('unit_id')
            || request()->filled('category')
            || request()->filled('status')
            || request()->filled('period_month')
            || request()->filled('period_year')
            || request()->filled('search');

        $statusClasses = [
            \App\Models\OperationalDocument::STATUS_DRAFT
                => 'border-amber-200 bg-amber-50 text-amber-700',

            \App\Models\OperationalDocument::STATUS_PUBLISHED
                => 'border-emerald-200 bg-emerald-50 text-emerald-700',

            \App\Models\OperationalDocument::STATUS_ARCHIVED
                => 'border-slate-200 bg-slate-100 text-slate-600',
        ];

        $statusIcons = [
            \App\Models\OperationalDocument::STATUS_DRAFT
                => 'edit-3',

            \App\Models\OperationalDocument::STATUS_PUBLISHED
                => 'check-circle',

            \App\Models\OperationalDocument::STATUS_ARCHIVED
                => 'archive',
        ];
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
                        Arsip Operasional
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Kelola dokumen final operasional SIM/TI seperti rekap jaringan,
                        inventaris laboratorium, pemeriksaan perangkat, pemakaian ruangan,
                        dan dukungan kegiatan.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="lock" class="h-3.5 w-3.5" />
                            File Private
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="activity" class="h-3.5 w-3.5" />
                            Lifecycle Dokumen
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="download" class="h-3.5 w-3.5" />
                            Protected Download
                        </span>
                    </div>
                </div>

                @if ($isManager)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('operations.documents.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 sm:w-auto"
                        >
                            <x-icon name="archive" class="h-4 w-4" />
                            Upload Arsip
                        </a>
                    </div>
                @endif
            </div>
        </section>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-inset ring-emerald-200">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>

                    <p class="text-sm font-medium leading-6 text-emerald-800">
                        {{ session('success') }}
                    </p>
                </div>
            </section>
        @endif

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

        {{-- SUMMARY --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 {{ $isManager ? 'xl:grid-cols-4' : 'xl:grid-cols-2' }}">
            <article class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-sky-700">
                            Total Arsip
                        </p>

                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                            {{ $summary['total'] }}
                        </p>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Seluruh arsip yang dapat Anda akses.
                        </p>
                    </div>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="archive" class="h-5 w-5" />
                    </div>
                </div>
            </article>

            @if ($isManager)
                <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-amber-700">
                                Draft
                            </p>

                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                                {{ $summary['draft'] }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Masih dapat diedit dan dihapus.
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">
                            <x-icon name="edit-3" class="h-5 w-5" />
                        </div>
                    </div>
                </article>
            @endif

            <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-emerald-700">
                            Dipublikasikan
                        </p>

                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                            {{ $summary['published'] }}
                        </p>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Tersedia sesuai aturan visibility.
                        </p>
                    </div>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>
                </div>
            </article>

            @if ($isManager)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-600">
                                Diarsipkan
                            </p>

                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                                {{ $summary['archived'] }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Dokumen selesai dan read-only.
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                            <x-icon name="archive" class="h-5 w-5" />
                        </div>
                    </div>
                </article>
            @endif
        </section>

        {{-- FILTER --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <x-icon name="filter" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Filter Arsip
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Cari arsip berdasarkan kategori, periode, status, atau kata kunci.
                        </p>
                    </div>
                </div>
            </div>

            <form method="GET" class="p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
                    @if ($user->isAdmin())
                        <div>
                            <label
                                for="unit_id"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Unit
                            </label>

                            <select
                                id="unit_id"
                                name="unit_id"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">
                                    Semua Unit
                                </option>

                                @foreach ($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        @selected(
                                            (string) request('unit_id')
                                            === (string) $unit->id
                                        )
                                    >
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label
                            for="category"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Kategori
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Semua Kategori
                            </option>

                            @foreach ($categoryOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(request('category') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($isManager)
                        <div>
                            <label
                                for="status"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">
                                    Semua Status
                                </option>

                                @foreach ($statusOptions as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(request('status') === $value)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

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
                                Semua Bulan
                            </option>

                            @foreach ($monthOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        (string) request('period_month')
                                        === (string) $value
                                    )
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
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
                                Semua Tahun
                            </option>

                            @foreach ($yearOptions as $year)
                                <option
                                    value="{{ $year }}"
                                    @selected(
                                        (string) request('period_year')
                                        === (string) $year
                                    )
                                >
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="{{ $user->isAdmin() ? '' : 'xl:col-span-2' }}">
                        <label
                            for="search"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Pencarian
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Judul, nomor dokumen, keterangan, atau nama file"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >
                    </div>
                </div>

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-slate-500">
                        Ringkasan di atas tidak berubah saat filter diterapkan.
                    </p>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row">
                        <a
                            href="{{ route('operations.documents.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                            Reset
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="filter" class="h-4 w-4" />
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </section>

        {{-- ACTIVE FILTER --}}
        @if ($hasActiveFilter)
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="filter" class="h-4 w-4" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-sky-900">
                            Filter aktif
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if ($user->isAdmin() && request()->filled('unit_id'))
                                @php
                                    $selectedUnit = $units->firstWhere(
                                        'id',
                                        (int) request('unit_id')
                                    );
                                @endphp

                                @if ($selectedUnit)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-sky-700">
                                        Unit: {{ $selectedUnit->name }}
                                    </span>
                                @endif
                            @endif

                            @if (request()->filled('category'))
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-sky-700">
                                    Kategori:
                                    {{ $categoryOptions[request('category')] ?? request('category') }}
                                </span>
                            @endif

                            @if ($isManager && request()->filled('status'))
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-sky-700">
                                    Status:
                                    {{ $statusOptions[request('status')] ?? request('status') }}
                                </span>
                            @endif

                            @if (request()->filled('period_month'))
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-sky-700">
                                    Bulan:
                                    {{ $monthOptions[(int) request('period_month')] ?? request('period_month') }}
                                </span>
                            @endif

                            @if (request()->filled('period_year'))
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-sky-700">
                                    Tahun: {{ request('period_year') }}
                                </span>
                            @endif

                            @if (request()->filled('search'))
                                <span class="inline-flex max-w-full items-center gap-1.5 rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-sky-700">
                                    Pencarian:
                                    <span class="truncate">
                                        {{ request('search') }}
                                    </span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <a
                        href="{{ route('operations.documents.index') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Hapus Filter
                    </a>
                </div>
            </section>
        @endif

        {{-- DOCUMENT LIST --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <x-icon name="clipboard-list" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Daftar Arsip
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Menampilkan {{ $documents->firstItem() ?? 0 }}
                            sampai {{ $documents->lastItem() ?? 0 }}
                            dari {{ $documents->total() }} arsip.
                        </p>
                    </div>
                </div>

                @if ($isManager)
                    <a
                        href="{{ route('operations.documents.create') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 sm:w-auto"
                    >
                        <x-icon name="archive" class="h-4 w-4" />
                        Upload Arsip
                    </a>
                @endif
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto xl:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Dokumen
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Kategori
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Periode
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Unit
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                File
                            </th>

                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($documents as $document)
                            @php
                                $statusClass =
                                    $statusClasses[$document->status]
                                    ?? 'border-slate-200 bg-slate-50 text-slate-700';

                                $statusIcon =
                                    $statusIcons[$document->status]
                                    ?? 'info';
                            @endphp

                            <tr class="transition hover:bg-slate-50/80">
                                <td class="max-w-md px-5 py-4 align-top">
                                    <p class="text-sm font-semibold leading-6 text-slate-900">
                                        {{ $document->title }}
                                    </p>

                                    @if ($document->document_number)
                                        <p class="mt-1 text-xs text-slate-500">
                                            Nomor: {{ $document->document_number }}
                                        </p>
                                    @endif

                                    @if ($document->description)
                                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                            {{ $document->description }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $document->category_label }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <p class="text-sm font-medium leading-6 text-slate-700">
                                        {{ $document->period_label }}
                                    </p>

                                    @if ($document->document_date)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $document->document_date->format('d M Y') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <p class="max-w-[200px] text-sm font-medium leading-6 text-slate-700">
                                        {{ $document->unit?->name ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        <x-icon
                                            name="{{ $statusIcon }}"
                                            class="h-3.5 w-3.5"
                                        />

                                        {{ $document->status_label }}
                                    </span>

                                    <p class="mt-2 text-xs font-medium text-slate-500">
                                        {{ $document->visibility_label }}
                                    </p>
                                </td>

                                <td class="max-w-[240px] px-5 py-4 align-top">
                                    <p class="truncate text-sm font-medium text-slate-800">
                                        {{ $document->file_original_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $document->file_size_label }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-right align-top">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a
                                            href="{{ route('operations.documents.show', $document) }}"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            Detail
                                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                                        </a>

                                        @if ($isManager && $document->isEditable())
                                            <a
                                                href="{{ route('operations.documents.edit', $document) }}"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                                            >
                                                <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                                Edit
                                            </a>
                                        @endif

                                        <a
                                            href="{{ route('operations.documents.download', $document) }}"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-950 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                        >
                                            <x-icon name="download" class="h-3.5 w-3.5" />
                                            Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="archive" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        Tidak ada arsip ditemukan
                                    </h3>

                                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($hasActiveFilter)
                                            Tidak ada arsip yang sesuai dengan filter saat ini.
                                        @elseif ($isManager)
                                            Upload dokumen operasional final agar dapat dikelola dan diunduh melalui sistem.
                                        @else
                                            Belum ada arsip yang dipublikasikan untuk unit Anda.
                                        @endif
                                    </p>

                                    @if ($hasActiveFilter)
                                        <a
                                            href="{{ route('operations.documents.index') }}"
                                            class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                                            Reset Filter
                                        </a>
                                    @elseif ($isManager)
                                        <a
                                            href="{{ route('operations.documents.create') }}"
                                            class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="archive" class="h-4 w-4" />
                                            Upload Arsip
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE/TABLET CARDS --}}
            <div class="divide-y divide-slate-100 xl:hidden">
                @forelse ($documents as $document)
                    @php
                        $statusClass =
                            $statusClasses[$document->status]
                            ?? 'border-slate-200 bg-slate-50 text-slate-700';

                        $statusIcon =
                            $statusIcons[$document->status]
                            ?? 'info';
                    @endphp

                    <article class="space-y-5 p-5 sm:p-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $document->category_label }}
                                </span>

                                <h3 class="mt-3 text-base font-semibold leading-7 text-slate-900">
                                    {{ $document->title }}
                                </h3>

                                @if ($document->document_number)
                                    <p class="mt-1 text-xs text-slate-500">
                                        Nomor: {{ $document->document_number }}
                                    </p>
                                @endif
                            </div>

                            <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                <x-icon
                                    name="{{ $statusIcon }}"
                                    class="h-3.5 w-3.5"
                                />

                                {{ $document->status_label }}
                            </span>
                        </div>

                        @if ($document->description)
                            <p class="line-clamp-3 text-sm leading-6 text-slate-600">
                                {{ $document->description }}
                            </p>
                        @endif

                        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1.5 text-sm font-medium leading-6 text-slate-800">
                                    {{ $document->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Periode
                                </dt>

                                <dd class="mt-1.5 text-sm font-medium leading-6 text-slate-800">
                                    {{ $document->period_label }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Visibility
                                </dt>

                                <dd class="mt-1.5 text-sm font-medium leading-6 text-slate-800">
                                    {{ $document->visibility_label }}
                                </dd>
                            </div>

                            <div class="min-w-0 rounded-xl bg-slate-50 p-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    File
                                </dt>

                                <dd class="mt-1.5 truncate text-sm font-medium leading-6 text-slate-800">
                                    {{ $document->file_original_name }}
                                </dd>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $document->file_size_label }}
                                </p>
                            </div>
                        </dl>

                        <div class="flex flex-col gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:flex-wrap">
                            <a
                                href="{{ route('operations.documents.show', $document) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                Detail
                                <x-icon name="chevron-right" class="h-4 w-4" />
                            </a>

                            @if ($isManager && $document->isEditable())
                                <a
                                    href="{{ route('operations.documents.edit', $document) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                                >
                                    <x-icon name="edit-3" class="h-4 w-4" />
                                    Edit
                                </a>
                            @endif

                            <a
                                href="{{ route('operations.documents.download', $document) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                <x-icon name="download" class="h-4 w-4" />
                                Download
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="archive" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-slate-900">
                            Tidak ada arsip ditemukan
                        </h3>

                        <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                            @if ($hasActiveFilter)
                                Tidak ada arsip yang sesuai dengan filter saat ini.
                            @elseif ($isManager)
                                Upload dokumen operasional final agar dapat dikelola melalui sistem.
                            @else
                                Belum ada arsip yang dipublikasikan untuk unit Anda.
                            @endif
                        </p>

                        @if ($hasActiveFilter)
                            <a
                                href="{{ route('operations.documents.index') }}"
                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                <x-icon name="rotate-ccw" class="h-4 w-4" />
                                Reset Filter
                            </a>
                        @elseif ($isManager)
                            <a
                                href="{{ route('operations.documents.create') }}"
                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="archive" class="h-4 w-4" />
                                Upload Arsip
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($documents->hasPages())
                <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                    {{ $documents->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>