<x-app-layout>
        @php
            $user = auth()->user();
            $canManage = $user->role?->name !== 'pegawai';

            $hasActiveFilters =
                request()->filled('status')
                || request()->filled('unit_id')
                || request()->filled('pic_user_id')
                || request()->filled('evaluation_record_id')
                || request()->filled('search')
                || request()->filled('due_from')
                || request()->filled('due_to');

            $selectedPic = request()->filled('pic_user_id')
                ? $picUsers->firstWhere('id', (int) request('pic_user_id'))
                : null;

            $selectedEvaluation = request()->filled('evaluation_record_id')
                && request('evaluation_record_id') !== 'none'
                    ? $evaluationRecords->firstWhere(
                        'id',
                        (int) request('evaluation_record_id')
                    )
                    : null;

            $selectedUnit = $user->isAdmin() && request()->filled('unit_id')
                ? $units->firstWhere('id', (int) request('unit_id'))
                : null;
        @endphp
    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="clipboard-list" class="h-4 w-4" />
                        Pengendalian
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Tindak Lanjut Evaluasi
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Kelola tindak lanjut hasil evaluasi, penugasan PIC, tenggat waktu,
                        progres pekerjaan, status penyelesaian, dan bukti pengendalian.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            Pegawai PIC dapat memperbarui progres
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            Status Selesai dikunci
                        </span>
                    </div>
                </div>

                @if ($canManage)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('documentation.control.follow-ups.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900 sm:w-auto"
                        >
                            <x-icon name="clipboard-list" class="h-4 w-4" />
                            Tambah Tindak Lanjut
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
                        Filter Tindak Lanjut
                    </h2>

                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                        Cari berdasarkan status, unit, PIC, sumber evaluasi, tenggat waktu,
                        atau isi tindak lanjut.
                    </p>
                </div>
            </div>

            <form
                method="GET"
                action="{{ route('documentation.control.follow-ups.index') }}"
                class="space-y-5"
            >
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
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

                            @foreach (\App\Models\ControlFollowUp::statusOptions() as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(request('status') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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

                    <div>
                        <label
                            for="pic_user_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            PIC
                        </label>

                        <select
                            id="pic_user_id"
                            name="pic_user_id"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua PIC</option>

                            @foreach ($picUsers as $picUser)
                                <option
                                    value="{{ $picUser->id }}"
                                    @selected(
                                        (string) request('pic_user_id')
                                        === (string) $picUser->id
                                    )
                                >
                                    {{ $picUser->employee?->name ?? $picUser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            for="evaluation_record_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Sumber Evaluasi
                        </label>

                        <select
                            id="evaluation_record_id"
                            name="evaluation_record_id"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Sumber</option>

                            <option
                                value="none"
                                @selected(request('evaluation_record_id') === 'none')
                            >
                                Tindak Lanjut Mandiri
                            </option>

                            @foreach ($evaluationRecords as $record)
                                <option
                                    value="{{ $record->id }}"
                                    @selected(
                                        (string) request('evaluation_record_id')
                                        === (string) $record->id
                                    )
                                >
                                    {{ $record->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="{{ $user->isAdmin() ? 'xl:col-span-2' : 'xl:col-span-1' }}">
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
                                placeholder="Cari judul, deskripsi, rekomendasi, atau progres"
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="due_from"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tenggat Mulai
                        </label>

                        <input
                            id="due_from"
                            type="date"
                            name="due_from"
                            value="{{ request('due_from') }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                    </div>

                    <div>
                        <label
                            for="due_to"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tenggat Sampai
                        </label>

                        <input
                            id="due_to"
                            type="date"
                            name="due_to"
                            value="{{ request('due_to') }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('documentation.control.follow-ups.index') }}"
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
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-900">
                        Filter aktif
                    </p>

                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (request()->filled('status'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Status:
                                {{ \App\Models\ControlFollowUp::statusOptions()[request('status')] ?? request('status') }}
                            </span>
                        @endif

                        @if ($selectedUnit)
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Unit: {{ $selectedUnit->name }}
                            </span>
                        @endif

                        @if ($selectedPic)
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                PIC:
                                {{ $selectedPic->employee?->name ?? $selectedPic->name }}
                            </span>
                        @endif

                        @if (request('evaluation_record_id') === 'none')
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Sumber: Tindak Lanjut Mandiri
                            </span>
                        @elseif ($selectedEvaluation)
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Sumber: {{ $selectedEvaluation->title }}
                            </span>
                        @endif

                        @if (request()->filled('search'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Pencarian: {{ request('search') }}
                            </span>
                        @endif

                        @if (request()->filled('due_from'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Tenggat mulai:
                                {{ \Illuminate\Support\Carbon::parse(request('due_from'))->format('d M Y') }}
                            </span>
                        @endif

                        @if (request()->filled('due_to'))
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                Tenggat sampai:
                                {{ \Illuminate\Support\Carbon::parse(request('due_to'))->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>

                <a
                    href="{{ route('documentation.control.follow-ups.index') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950"
                >
                    <x-icon name="x" class="h-4 w-4" />
                    Hapus Filter
                </a>
            </div>
        </section>
        @endif

        {{-- DAFTAR TINDAK LANJUT --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon name="clipboard-list" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Daftar Tindak Lanjut
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Menampilkan {{ $followUps->count() }} dari
                                {{ $followUps->total() }} tindak lanjut.
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                        {{ $followUps->total() }} data
                    </span>
                </div>
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tindak Lanjut
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Sumber
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Unit
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                PIC
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tenggat
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($followUps as $followUp)
                            @php
                                $isCurrentUserPic =
                                    (int) $followUp->pic_user_id === (int) auth()->id();

                                $canDeleteFollowUp =
                                    $canManage
                                    && in_array(
                                        $followUp->status,
                                        [
                                            \App\Models\ControlFollowUp::STATUS_OPEN,
                                            \App\Models\ControlFollowUp::STATUS_CANCELLED,
                                        ],
                                        true
                                    );
                            @endphp

                            <tr class="transition hover:bg-slate-50/80">
                                <td class="min-w-[280px] px-5 py-4">
                                    <p class="font-semibold leading-6 text-slate-900">
                                        {{ $followUp->title }}
                                    </p>

                                    <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">
                                        {{ $followUp->description }}
                                    </p>

                                    @if ($followUp->progress_note)
                                        <div class="mt-2 inline-flex items-center gap-1.5 text-xs text-slate-500">
                                            <x-icon name="activity" class="h-3.5 w-3.5" />
                                            Progres sudah dicatat
                                        </div>
                                    @endif
                                </td>

                                <td class="min-w-[220px] px-5 py-4">
                                    @if ($followUp->evaluationRecord)
                                        <div class="flex items-start gap-2">
                                            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-700">
                                                <x-icon name="search-check" class="h-3.5 w-3.5" />
                                            </div>

                                            <div class="min-w-0">
                                                <p class="line-clamp-2 text-sm font-medium leading-5 text-slate-700">
                                                    {{ $followUp->evaluationRecord->title }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-400">
                                                    Hasil Evaluasi
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                            <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                                            Mandiri
                                        </span>
                                    @endif
                                </td>

                                <td class="min-w-[170px] px-5 py-4 text-sm text-slate-600">
                                    {{ $followUp->unit?->name ?? '-' }}
                                </td>

                                <td class="min-w-[190px] px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold uppercase text-slate-600">
                                            {{ \Illuminate\Support\Str::substr(
                                                $followUp->picUser?->employee?->name
                                                    ?? $followUp->picUser?->name
                                                    ?? '?',
                                                0,
                                                1
                                            ) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">
                                                {{ $followUp->picUser?->employee?->name
                                                    ?? $followUp->picUser?->name
                                                    ?? 'Belum ditentukan' }}
                                            </p>

                                            @if ($isCurrentUserPic)
                                                <span class="mt-1 inline-flex w-fit items-center rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                    Anda PIC
                                                </span>
                                            @elseif (! $followUp->pic_user_id)
                                                <p class="mt-1 text-xs text-slate-400">
                                                    Belum ada PIC
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($followUp->due_date)
                                        <div class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-700">
                                            <x-icon name="calendar" class="h-4 w-4 text-slate-400" />
                                            {{ $followUp->due_date->format('d M Y') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $followUp->statusBadgeClass() }}">
                                        {{ $followUp->statusLabel() }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('documentation.control.follow-ups.show', $followUp) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                        >
                                            Detail
                                            <x-icon name="chevron-right" class="h-4 w-4" />
                                        </a>

                                        @if ($canDeleteFollowUp)
                                            <form
                                                x-data
                                                method="POST"
                                                action="{{ route('documentation.control.follow-ups.destroy', $followUp) }}"
                                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Tindak Lanjut?',
                                                    message: 'Tindak lanjut ini akan dihapus. Surat yang sudah terkait tidak ikut dihapus, tetapi keterkaitannya akan dilepas.',
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="clipboard-list" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        @if ($hasActiveFilters)
                                            Tidak ada tindak lanjut yang cocok
                                        @else
                                            Belum ada tindak lanjut evaluasi
                                        @endif
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($hasActiveFilters)
                                            Ubah atau hapus filter untuk menemukan data yang sesuai.
                                        @elseif ($canManage)
                                            Tambahkan tindak lanjut pertama untuk mencatat proses pengendalian.
                                        @else
                                            Tindak lanjut pada unit lo akan tampil di halaman ini.
                                        @endif
                                    </p>

                                    @if ($hasActiveFilters)
                                        <a
                                            href="{{ route('documentation.control.follow-ups.index') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                                            Reset Filter
                                        </a>
                                    @elseif ($canManage)
                                        <a
                                            href="{{ route('documentation.control.follow-ups.create') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="clipboard-list" class="h-4 w-4" />
                                            Tambah Tindak Lanjut
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
                @forelse ($followUps as $followUp)
                    @php
                        $isCurrentUserPic =
                            (int) $followUp->pic_user_id === (int) auth()->id();

                        $canDeleteFollowUp =
                            $canManage
                            && in_array(
                                $followUp->status,
                                [
                                    \App\Models\ControlFollowUp::STATUS_OPEN,
                                    \App\Models\ControlFollowUp::STATUS_CANCELLED,
                                ],
                                true
                            );
                    @endphp

                    <article class="p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $followUp->statusBadgeClass() }}">
                                {{ $followUp->statusLabel() }}
                            </span>

                            @if ($isCurrentUserPic)
                                <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                    Anda PIC
                                </span>
                            @endif

                            @if ($followUp->evaluationRecord)
                                <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700 ring-1 ring-inset ring-violet-200">
                                    <x-icon name="search-check" class="h-3.5 w-3.5" />
                                    Dari Evaluasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    Mandiri
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-3 text-base font-semibold leading-6 text-slate-900">
                            {{ $followUp->title }}
                        </h3>

                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">
                            {{ $followUp->description }}
                        </p>

                        <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $followUp->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tenggat
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $followUp->due_date?->format('d M Y') ?? 'Belum ditentukan' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    PIC
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $followUp->picUser?->employee?->name
                                        ?? $followUp->picUser?->name
                                        ?? 'Belum ditentukan' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Sumber
                                </dt>

                                <dd class="mt-1 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $followUp->evaluationRecord?->title
                                        ?? 'Tindak lanjut mandiri' }}
                                </dd>
                            </div>
                        </dl>

                        @if ($followUp->progress_note)
                            <div class="mt-3 rounded-xl border border-sky-100 bg-sky-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">
                                    Catatan Progres
                                </p>

                                <p class="mt-1 line-clamp-3 text-sm leading-6 text-sky-900">
                                    {{ $followUp->progress_note }}
                                </p>
                            </div>
                        @endif

                        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                            <a
                                href="{{ route('documentation.control.follow-ups.show', $followUp) }}"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                Detail
                                <x-icon name="chevron-right" class="h-4 w-4" />
                            </a>

                            @if ($canDeleteFollowUp)
                                <form
                                    x-data
                                    method="POST"
                                    action="{{ route('documentation.control.follow-ups.destroy', $followUp) }}"
                                    class="sm:flex-1"
                                    x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                        title: 'Hapus Tindak Lanjut?',
                                        message: 'Tindak lanjut ini akan dihapus. Surat yang sudah terkait tidak ikut dihapus, tetapi keterkaitannya akan dilepas.',
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
                            <x-icon name="clipboard-list" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-slate-900">
                            @if ($hasActiveFilters)
                                Tidak ada tindak lanjut yang cocok
                            @else
                                Belum ada tindak lanjut evaluasi
                            @endif
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            @if ($hasActiveFilters)
                                Ubah atau hapus filter untuk menemukan data yang sesuai.
                            @else
                                Data tindak lanjut evaluasi akan tampil di halaman ini.
                            @endif
                        </p>

                        @if ($hasActiveFilters)
                            <a
                                href="{{ route('documentation.control.follow-ups.index') }}"
                                class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                <x-icon name="rotate-ccw" class="h-4 w-4" />
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($followUps->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $followUps->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>