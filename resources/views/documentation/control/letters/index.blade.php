<x-app-layout>
        @php
            $user = auth()->user();
            $canManage = $user->role?->name !== 'pegawai';

            $hasActiveFilters =
                request()->filled('letter_type')
                || request()->filled('visibility')
                || request()->filled('unit_id')
                || request()->filled('search')
                || request()->filled('date_from')
                || request()->filled('date_to');

            $selectedUnit = $user->isAdmin() && request()->filled('unit_id')
                ? $units->firstWhere('id', (int) request('unit_id'))
                : null;

            $selectedLetterTypeLabel = match (request('letter_type')) {
                \App\Models\ControlLetter::TYPE_INCOMING => 'Surat Masuk',
                \App\Models\ControlLetter::TYPE_OUTGOING => 'Surat Keluar',
                default => null,
            };

            $selectedVisibilityLabel = match (request('visibility')) {
                \App\Models\ControlLetter::VISIBILITY_UNIT => 'Unit',
                \App\Models\ControlLetter::VISIBILITY_RESTRICTED => 'Terbatas',
                default => null,
            };
        @endphp
    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="mail" class="h-4 w-4" />
                        Pengendalian
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Arsip Surat Pengendalian
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Kelola surat masuk dan surat keluar sebagai bukti koordinasi,
                        tindak lanjut, dan dokumentasi proses pengendalian.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="inbox" class="h-3.5 w-3.5" />
                            Surat Masuk
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="send" class="h-3.5 w-3.5" />
                            Surat Keluar
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="lock" class="h-3.5 w-3.5" />
                            File protected
                        </span>
                    </div>
                </div>

                @if ($canManage)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('documentation.control.letters.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900 sm:w-auto"
                        >
                            <x-icon name="upload-cloud" class="h-4 w-4" />
                            Unggah Surat
                        </a>
                    </div>
                @endif
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
                        Filter Arsip Surat
                    </h2>

                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                        Cari surat berdasarkan jenis, visibilitas, unit, tanggal,
                        nomor, pengirim, penerima, atau perihal.
                    </p>
                </div>
            </div>

            <form
                method="GET"
                action="{{ route('documentation.control.letters.index') }}"
                class="space-y-5"
            >
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label
                            for="letter_type"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Jenis Surat
                        </label>

                        <select
                            id="letter_type"
                            name="letter_type"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Jenis</option>

                            <option
                                value="{{ \App\Models\ControlLetter::TYPE_INCOMING }}"
                                @selected(
                                    request('letter_type')
                                    === \App\Models\ControlLetter::TYPE_INCOMING
                                )
                            >
                                Surat Masuk
                            </option>

                            <option
                                value="{{ \App\Models\ControlLetter::TYPE_OUTGOING }}"
                                @selected(
                                    request('letter_type')
                                    === \App\Models\ControlLetter::TYPE_OUTGOING
                                )
                            >
                                Surat Keluar
                            </option>
                        </select>
                    </div>

                    @if ($canManage)
                        <div>
                            <label
                                for="visibility"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Visibilitas
                            </label>

                            <select
                                id="visibility"
                                name="visibility"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">Semua Visibilitas</option>

                                <option
                                    value="{{ \App\Models\ControlLetter::VISIBILITY_UNIT }}"
                                    @selected(
                                        request('visibility')
                                        === \App\Models\ControlLetter::VISIBILITY_UNIT
                                    )
                                >
                                    Unit
                                </option>

                                <option
                                    value="{{ \App\Models\ControlLetter::VISIBILITY_RESTRICTED }}"
                                    @selected(
                                        request('visibility')
                                        === \App\Models\ControlLetter::VISIBILITY_RESTRICTED
                                    )
                                >
                                    Terbatas
                                </option>
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

                    <div class="{{ $user->isAdmin() ? '' : 'xl:col-span-2' }}">
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
                                placeholder="Cari perihal, nomor, pengirim, penerima, atau ringkasan"
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="date_from"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tanggal Mulai
                        </label>

                        <input
                            id="date_from"
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                    </div>

                    <div>
                        <label
                            for="date_to"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tanggal Sampai
                        </label>

                        <input
                            id="date_to"
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('documentation.control.letters.index') }}"
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

        @if ($hasActiveFilters)
        <section class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-900">
                        Filter aktif
                    </p>

                    <div class="mt-2 flex flex-wrap gap-2">
                        @if ($selectedLetterTypeLabel)
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Jenis: {{ $selectedLetterTypeLabel }}
                            </span>
                        @endif

                        @if ($selectedVisibilityLabel && $canManage)
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Visibilitas: {{ $selectedVisibilityLabel }}
                            </span>
                        @endif

                        @if ($selectedUnit)
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Unit: {{ $selectedUnit->name }}
                            </span>
                        @endif

                        @if (request()->filled('search'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Pencarian: {{ request('search') }}
                            </span>
                        @endif

                        @if (request()->filled('date_from'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Mulai:
                                {{ \Illuminate\Support\Carbon::parse(request('date_from'))->format('d M Y') }}
                            </span>
                        @endif

                        @if (request()->filled('date_to'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Sampai:
                                {{ \Illuminate\Support\Carbon::parse(request('date_to'))->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>

                <a
                    href="{{ route('documentation.control.letters.index') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 transition hover:text-sky-950"
                >
                    <x-icon name="x" class="h-4 w-4" />
                    Hapus Filter
                </a>
            </div>
        </section>
        @endif

        {{-- DAFTAR ARSIP SURAT --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <x-icon name="mail" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Daftar Surat Pengendalian
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Menampilkan {{ $letters->count() }} dari
                                {{ $letters->total() }} arsip surat.
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                        {{ $letters->total() }} data
                    </span>
                </div>
            </div>

            {{-- TABEL DESKTOP --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Surat
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Nomor
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Perihal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Unit
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Visibilitas
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($letters as $letter)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->typeBadgeClass() }}">
                                        {{ $letter->typeLabel() }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $letter->letter_date?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="min-w-[170px] px-5 py-4 text-sm text-slate-600">
                                    {{ $letter->letter_number ?? '-' }}
                                </td>

                                <td class="min-w-[280px] px-5 py-4">
                                    <p class="font-semibold leading-6 text-slate-900">
                                        {{ $letter->subject }}
                                    </p>

                                    <p class="mt-1 max-w-sm truncate text-xs text-slate-500">
                                        {{ $letter->original_name }}
                                    </p>

                                    @if ($letter->followUp)
                                        <div class="mt-2 inline-flex items-center gap-1.5 text-xs text-sky-700">
                                            <x-icon name="clipboard-list" class="h-3.5 w-3.5" />

                                            <span class="max-w-[260px] truncate">
                                                {{ $letter->followUp->title }}
                                            </span>
                                        </div>
                                    @else
                                        <p class="mt-2 text-xs text-slate-400">
                                            Arsip mandiri
                                        </p>
                                    @endif
                                </td>

                                <td class="min-w-[170px] px-5 py-4 text-sm text-slate-600">
                                    {{ $letter->unit?->name ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->visibilityBadgeClass() }}">
                                        {{ $letter->visibilityLabel() }}
                                    </span>
                                </td>

                                <td class="min-w-[310px] px-5 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a
                                            href="{{ route('documentation.control.letters.show', $letter) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                        >
                                            Detail
                                            <x-icon name="chevron-right" class="h-4 w-4" />
                                        </a>

                                        @if ($canManage)
                                            <a
                                                href="{{ route('documentation.control.letters.edit', $letter) }}"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                                            >
                                                <x-icon name="edit-3" class="h-4 w-4" />
                                                Edit
                                            </a>

                                            <form
                                                x-data
                                                method="POST"
                                                action="{{ route('documentation.control.letters.destroy', $letter) }}"
                                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Surat Pengendalian?',
                                                    message: 'Metadata dan file surat akan dihapus permanen dari arsip.',
                                                    confirmText: 'Ya, Hapus',
                                                    cancelText: 'Batal',
                                                    variant: 'danger',
                                                    onConfirm: () => $el.submit()
                                                })"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-3.5 py-2 text-sm font-semibold text-rose-700 shadow-sm transition hover:bg-rose-50"
                                                >
                                                    <x-icon name="trash-2" class="h-4 w-4" />
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif

                                        <a
                                            href="{{ route('documentation.control.letters.download', $letter) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            <x-icon name="download" class="h-4 w-4" />
                                            Unduh
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="mail" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        @if ($hasActiveFilters)
                                            Tidak ada surat yang cocok
                                        @else
                                            Belum ada arsip surat pengendalian
                                        @endif
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($hasActiveFilters)
                                            Ubah atau hapus filter untuk menemukan surat yang dicari.
                                        @elseif ($canManage)
                                            Unggah surat masuk atau surat keluar pertama sebagai bukti koordinasi pengendalian.
                                        @else
                                            Surat dengan akses untuk unit lo akan tampil di halaman ini.
                                        @endif
                                    </p>

                                    @if ($hasActiveFilters)
                                        <a
                                            href="{{ route('documentation.control.letters.index') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                                            Reset Filter
                                        </a>
                                    @elseif ($canManage)
                                        <a
                                            href="{{ route('documentation.control.letters.create') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="upload-cloud" class="h-4 w-4" />
                                            Unggah Surat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARD MOBILE --}}
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($letters as $letter)
                    <article class="p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->typeBadgeClass() }}">
                                {{ $letter->typeLabel() }}
                            </span>

                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->visibilityBadgeClass() }}">
                                {{ $letter->visibilityLabel() }}
                            </span>

                            @if ($letter->followUp)
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                    <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                                    Terkait Tindak Lanjut
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    Arsip Mandiri
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-3 text-base font-semibold leading-6 text-slate-900">
                            {{ $letter->subject }}
                        </h3>

                        @if ($letter->summary)
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">
                                {{ $letter->summary }}
                            </p>
                        @endif

                        <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Surat
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $letter->letter_date?->format('d M Y') ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Nomor Surat
                                </dt>

                                <dd class="mt-1 break-words text-sm font-semibold text-slate-900">
                                    {{ $letter->letter_number ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $letter->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Nama File
                                </dt>

                                <dd class="mt-1 break-all text-sm font-semibold text-slate-900">
                                    {{ $letter->original_name }}
                                </dd>
                            </div>

                            @if ($letter->followUp)
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Tindak Lanjut Terkait
                                    </dt>

                                    <dd class="mt-1 text-sm font-semibold leading-6 text-slate-900">
                                        {{ $letter->followUp->title }}
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <a
                                href="{{ route('documentation.control.letters.show', $letter) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                Detail
                                <x-icon name="chevron-right" class="h-4 w-4" />
                            </a>

                            <a
                                href="{{ route('documentation.control.letters.download', $letter) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                <x-icon name="download" class="h-4 w-4" />
                                Unduh
                            </a>

                            @if ($canManage)
                                <a
                                    href="{{ route('documentation.control.letters.edit', $letter) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                                >
                                    <x-icon name="edit-3" class="h-4 w-4" />
                                    Edit
                                </a>

                                <form
                                    x-data
                                    method="POST"
                                    action="{{ route('documentation.control.letters.destroy', $letter) }}"
                                    x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                        title: 'Hapus Surat Pengendalian?',
                                        message: 'Metadata dan file surat akan dihapus permanen dari arsip.',
                                        confirmText: 'Ya, Hapus',
                                        cancelText: 'Batal',
                                        variant: 'danger',
                                        onConfirm: () => $el.submit()
                                    })"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 shadow-sm transition hover:bg-rose-50"
                                    >
                                        <x-icon name="trash-2" class="h-4 w-4" />
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="mail" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-slate-900">
                            @if ($hasActiveFilters)
                                Tidak ada surat yang cocok
                            @else
                                Belum ada arsip surat pengendalian
                            @endif
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            @if ($hasActiveFilters)
                                Ubah atau hapus filter untuk menemukan surat yang dicari.
                            @else
                                Arsip surat yang tersedia sesuai hak akses akan tampil di halaman ini.
                            @endif
                        </p>

                        @if ($hasActiveFilters)
                            <a
                                href="{{ route('documentation.control.letters.index') }}"
                                class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                <x-icon name="rotate-ccw" class="h-4 w-4" />
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($letters->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $letters->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>