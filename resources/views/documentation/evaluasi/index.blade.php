<x-app-layout>
    @php
        $user = auth()->user();

        $canManage =
            $user->isAdmin()
            || $user->isKanit()
            || $user->role?->name === 'gkm';

        $hasActiveFilters =
            request()->filled('search')
            || request()->filled('evaluation_type')
            || request()->filled('status')
            || request()->filled('unit_id')
            || request()->filled('follow_up_status');

        $followUpStatusLabels = [
            'none' => 'Belum ada tindak lanjut',
            'open' => 'Ada status Open',
            'in_progress' => 'Ada status Dalam Proses',
            'done' => 'Sudah Terkendali / Selesai',
            'cancelled' => 'Ada status Dibatalkan',
        ];
    @endphp

    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="search-check" class="h-4 w-4" />
                        Evaluasi
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Hasil Evaluasi
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Kelola hasil evaluasi rapat, kegiatan, target, temuan,
                        rekomendasi, dokumen pendukung, dan tindak lanjut Unit SIM TI.
                    </p>

                    @if ($canManage)
                        <div class="mt-5 flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                Draft dapat diedit
                            </span>

                            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                Published menjadi catatan resmi
                            </span>
                        </div>
                    @endif
                </div>

                @if ($canManage)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('documentation.evaluasi.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900 sm:w-auto"
                        >
                            <x-icon name="search-check" class="h-4 w-4" />
                            Tambah Evaluasi
                        </a>
                    </div>
                @endif
            </div>
        </section>

        {{-- RINGKASAN --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Evaluasi
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ $summary['total'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <x-icon name="search-check" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-amber-700">
                            Draft
                        </p>

                        <p class="mt-2 text-2xl font-bold text-amber-900">
                            {{ $summary['draft'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-amber-700">
                        <x-icon name="file-edit" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-emerald-700">
                            Published
                        </p>

                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ $summary['published'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-emerald-700">
                        <x-icon name="badge-check" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-violet-700">
                            Diarsipkan
                        </p>

                        <p class="mt-2 text-2xl font-bold text-violet-900">
                            {{ $summary['archived'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-violet-700">
                        <x-icon name="archive" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-sky-700">
                            Dokumen
                        </p>

                        <p class="mt-2 text-2xl font-bold text-sky-900">
                            {{ $summary['documents'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-sky-700">
                        <x-icon name="file-output" class="h-5 w-5" />
                    </div>
                </div>
            </div>
        </section>

        {{-- FILTER --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <x-icon name="filter" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Filter Hasil Evaluasi
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500">
                        Cari evaluasi berdasarkan judul, jenis, status, unit, atau tindak lanjut.
                    </p>
                </div>
            </div>

            <form
                method="GET"
                action="{{ route('documentation.evaluasi.index') }}"
                class="space-y-5"
            >
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="{{ $user->isAdmin() ? 'xl:col-span-2' : 'xl:col-span-2' }}">
                        <label
                            for="search"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Pencarian
                        </label>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-icon name="search" class="h-4 w-4 text-slate-400" />
                            </div>

                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari judul, sumber, temuan, atau rekomendasi"
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="evaluation_type"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Jenis Evaluasi
                        </label>

                        <select
                            id="evaluation_type"
                            name="evaluation_type"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Jenis</option>

                            @foreach ($typeOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(request('evaluation_type') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($canManage)
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
                                <option value="">Semua Status</option>

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
                                <option value="">Semua Unit</option>

                                @foreach ($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        @selected((string) request('unit_id') === (string) $unit->id)
                                    >
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="{{ $user->isAdmin() ? 'xl:col-span-2' : 'xl:col-span-1' }}">
                        <label
                            for="follow_up_status"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Status Tindak Lanjut
                        </label>

                        <select
                            id="follow_up_status"
                            name="follow_up_status"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Tindak Lanjut</option>

                            @foreach ($followUpStatusLabels as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(request('follow_up_status') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('documentation.evaluasi.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                        Reset Filter
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                    >
                        <x-icon name="filter" class="h-4 w-4" />
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </section>

        {{-- FILTER AKTIF --}}
        @if ($hasActiveFilters)
            <section class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-sky-900">
                            Filter aktif
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">
                            @if (request()->filled('search'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Pencarian: {{ request('search') }}
                                </span>
                            @endif

                            @if (request()->filled('evaluation_type'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Jenis: {{ $typeOptions[request('evaluation_type')] ?? request('evaluation_type') }}
                                </span>
                            @endif

                            @if ($canManage && request()->filled('status'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Status: {{ $statusOptions[request('status')] ?? request('status') }}
                                </span>
                            @endif

                            @if ($user->isAdmin() && request()->filled('unit_id'))
                                @php
                                    $selectedUnit = $units->firstWhere(
                                        'id',
                                        (int) request('unit_id')
                                    );
                                @endphp

                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Unit: {{ $selectedUnit?->name ?? '-' }}
                                </span>
                            @endif

                            @if (request()->filled('follow_up_status'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Tindak Lanjut:
                                    {{ $followUpStatusLabels[request('follow_up_status')] ?? request('follow_up_status') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <a
                        href="{{ route('documentation.evaluasi.index') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Hapus Filter
                    </a>
                </div>
            </section>
        @endif

        {{-- DAFTAR HASIL EVALUASI --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon name="search-check" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Daftar Hasil Evaluasi
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Menampilkan {{ $records->count() }} dari {{ $records->total() }} data.
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                        {{ $records->total() }} evaluasi
                    </span>
                </div>
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Evaluasi
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Jenis
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Unit
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Dokumen
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tindak Lanjut
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($records as $record)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $record->evaluation_date?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td class="min-w-[280px] px-5 py-4">
                                    <p class="font-semibold text-slate-900">
                                        {{ $record->title }}
                                    </p>

                                    <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">
                                        {{ $record->source ?: 'Sumber atau kegiatan belum diisi.' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $record->evaluation_type_badge_class }}">
                                        {{ $record->evaluation_type_label }}
                                    </span>
                                </td>

                                <td class="min-w-[180px] px-5 py-4 text-sm text-slate-600">
                                    {{ $record->unit?->name ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700">
                                        <x-icon name="paperclip" class="h-4 w-4 text-slate-400" />
                                        {{ $record->documents_count }} file
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $record->status_badge_class }}">
                                        {{ $record->status_label }}
                                    </span>
                                </td>

                                <td class="min-w-[220px] px-5 py-4">
                                    @if ($record->control_follow_ups_count > 0)
                                        <div class="space-y-2">
                                            @if ($record->control_follow_ups_done_count > 0)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                    <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                                    Terkendali
                                                </span>
                                            @endif

                                            <div class="flex flex-wrap gap-1.5">
                                                @if ($record->control_follow_ups_open_count > 0)
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                                        Open: {{ $record->control_follow_ups_open_count }}
                                                    </span>
                                                @endif

                                                @if ($record->control_follow_ups_in_progress_count > 0)
                                                    <span class="inline-flex rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                        Proses: {{ $record->control_follow_ups_in_progress_count }}
                                                    </span>
                                                @endif

                                                @if ($record->control_follow_ups_done_count > 0)
                                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                        Selesai: {{ $record->control_follow_ups_done_count }}
                                                    </span>
                                                @endif

                                                @if ($record->control_follow_ups_cancelled_count > 0)
                                                    <span class="inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 ring-1 ring-inset ring-rose-200">
                                                        Batal: {{ $record->control_follow_ups_cancelled_count }}
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-500">
                                                Total {{ $record->control_follow_ups_count }} tindak lanjut
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            Belum ada
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a
                                        href="{{ route('documentation.evaluasi.show', $record) }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                    >
                                        Detail
                                        <x-icon name="chevron-right" class="h-4 w-4" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="search-check" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        @if ($hasActiveFilters)
                                            Tidak ada hasil evaluasi yang cocok
                                        @else
                                            Belum ada hasil evaluasi
                                        @endif
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($hasActiveFilters)
                                            Ubah filter untuk menemukan hasil evaluasi yang sesuai.
                                        @elseif ($canManage)
                                            Tambahkan hasil evaluasi pertama untuk Unit SIM TI.
                                        @else
                                            Hasil evaluasi Published akan tampil di halaman ini.
                                        @endif
                                    </p>

                                    @if ($hasActiveFilters)
                                        <a
                                            href="{{ route('documentation.evaluasi.index') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                                            Reset Filter
                                        </a>
                                    @elseif ($canManage)
                                        <a
                                            href="{{ route('documentation.evaluasi.create') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="search-check" class="h-4 w-4" />
                                            Tambah Evaluasi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARDS --}}
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($records as $record)
                    <article class="p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $record->evaluation_type_badge_class }}">
                                {{ $record->evaluation_type_label }}
                            </span>

                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $record->status_badge_class }}">
                                {{ $record->status_label }}
                            </span>

                            @if ($record->control_follow_ups_done_count > 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                    Terkendali
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-3 text-base font-semibold leading-6 text-slate-900">
                            {{ $record->title }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            {{ $record->source ?: 'Sumber atau kegiatan belum diisi.' }}
                        </p>

                        <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $record->evaluation_date?->format('d/m/Y') ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Dokumen
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $record->documents_count }} file
                                </dd>
                            </div>

                            <div class="col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $record->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div class="col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tindak Lanjut
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $record->control_follow_ups_count }} tindak lanjut
                                </dd>
                            </div>
                        </dl>

                        @if ($record->control_follow_ups_count > 0)
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @if ($record->control_follow_ups_open_count > 0)
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                        Open: {{ $record->control_follow_ups_open_count }}
                                    </span>
                                @endif

                                @if ($record->control_follow_ups_in_progress_count > 0)
                                    <span class="inline-flex rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700">
                                        Proses: {{ $record->control_follow_ups_in_progress_count }}
                                    </span>
                                @endif

                                @if ($record->control_follow_ups_done_count > 0)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                        Selesai: {{ $record->control_follow_ups_done_count }}
                                    </span>
                                @endif

                                @if ($record->control_follow_ups_cancelled_count > 0)
                                    <span class="inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700">
                                        Batal: {{ $record->control_follow_ups_cancelled_count }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <a
                            href="{{ route('documentation.evaluasi.show', $record) }}"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Detail
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </a>
                    </article>
                @empty
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="search-check" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-slate-900">
                            Belum ada hasil evaluasi
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            Data hasil evaluasi akan tampil di halaman ini.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($records->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $records->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>