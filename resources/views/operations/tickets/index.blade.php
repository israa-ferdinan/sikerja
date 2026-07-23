<x-app-layout>
    @php
        $user = auth()->user();

        $canManageTicket =
            $user->isAdmin()
            || $user->isKanit()
            || $user->isGkm();

        $hasActiveFilters =
            request()->filled('search')
            || request()->filled('status')
            || request()->filled('category')
            || request()->filled('source')
            || request()->filled('priority')
            || request()->filled('assigned_to_employee_id');

        $selectedStatusLabel = request()->filled('status')
            ? ($statusOptions[request('status')] ?? null)
            : null;

        $selectedCategoryLabel = request()->filled('category')
            ? ($categoryOptions[request('category')] ?? null)
            : null;

        $selectedSourceLabel = request()->filled('source')
            ? ($sourceOptions[request('source')] ?? null)
            : null;

        $selectedPriorityLabel = request()->filled('priority')
            ? ($priorityOptions[request('priority')] ?? null)
            : null;

        $selectedPicLabel = null;

        if (request('assigned_to_employee_id') === 'none') {
            $selectedPicLabel = 'Belum Ada PIC';
        } elseif (request()->filled('assigned_to_employee_id')) {
            $selectedPic = $employees->firstWhere(
                'id',
                (int) request('assigned_to_employee_id')
            );

            $selectedPicLabel = $selectedPic?->name;
        }
    @endphp
    <div class="w-full space-y-6">
    @if (session('success'))
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <x-icon name="check-circle" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-emerald-900">
                        Proses berhasil
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-emerald-700">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </section>
    @endif

    @if (session('warning'))
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <x-icon name="alert-circle" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-amber-900">
                        Perlu diperhatikan
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        {{ session('warning') }}
                    </p>
                </div>
            </div>
        </section>
    @endif

    {{-- HERO --}}
    <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
        <div class="flex min-h-[230px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
            <div class="min-w-0 flex-1">
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                    <x-icon name="ticket-check" class="h-4 w-4" />
                    Operasional SIM/TI
                </div>

                <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                    Tiket Operasional
                </h1>

                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                    Kelola tiket yang berasal dari form pemohon maupun pencatatan manual
                    petugas. Pantau status, prioritas, PIC, dan integrasi pekerjaan tiket
                    ke laporan harian.
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                        <x-icon name="inbox" class="h-3.5 w-3.5" />
                        Tiket Masuk
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                        <x-icon name="user-check" class="h-3.5 w-3.5" />
                        Penugasan PIC
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                        <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                        Integrasi Laporan
                    </span>
                </div>
            </div>

            <div class="grid shrink-0 grid-cols-1 gap-2 sm:grid-cols-2 lg:max-w-xl lg:pl-8">
                <a
                    href="{{ route('operations.tickets.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                >
                    <x-icon name="ticket" class="h-4 w-4" />
                    Input Tiket Manual
                </a>

                <a
                    href="{{ route('public.tickets.create') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                >
                    <x-icon name="external-link" class="h-4 w-4" />
                    Form Pemohon
                </a>

                <a
                    href="{{ route('public.tickets.track-form') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                >
                    <x-icon name="search" class="h-4 w-4" />
                    Cek Status Publik
                </a>

                <a
                    href="{{ route('public.tickets.kiosk') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                >
                    <x-icon name="monitor" class="h-4 w-4" />
                    Buka KIOSK
                </a>
            </div>
        </div>
    </section>
    {{-- RINGKASAN --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Total Tiket
                    </p>

                    <p class="mt-3 text-2xl font-bold text-slate-900">
                        {{ $summary['total'] }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                    <x-icon name="ticket" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">
                        Baru
                    </p>

                    <p class="mt-3 text-2xl font-bold text-sky-950">
                        {{ $summary['baru'] }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                    <x-icon name="circle-dot" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                        Diproses
                    </p>

                    <p class="mt-3 text-2xl font-bold text-amber-950">
                        {{ $summary['diproses'] }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                    <x-icon name="activity" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">
                        Menunggu Pemohon
                    </p>

                    <p class="mt-3 text-2xl font-bold text-violet-950">
                        {{ $summary['menunggu_pemohon'] }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-violet-700 ring-1 ring-inset ring-violet-200">
                    <x-icon name="clock" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                        Selesai
                    </p>

                    <p class="mt-3 text-2xl font-bold text-emerald-950">
                        {{ $summary['selesai'] }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    <x-icon name="check-circle" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">
                        Dari Publik
                    </p>

                    <p class="mt-3 text-2xl font-bold text-cyan-950">
                        {{ $summary['public'] }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-cyan-700 ring-1 ring-inset ring-cyan-200">
                    <x-icon name="external-link" class="h-5 w-5" />
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
                    Filter Tiket Operasional
                </h2>

                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                    Cari tiket berdasarkan identitas pemohon, status, jenis permintaan,
                    sumber, prioritas, atau PIC.
                </p>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('operations.tickets.index') }}"
            class="space-y-5"
        >
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
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
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="Kode, nama, unit, judul, atau deskripsi"
                            class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >
                    </div>
                </div>

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

                <div>
                    <label
                        for="category"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Jenis Permintaan
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                    >
                        <option value="">Semua Jenis</option>

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

                <div>
                    <label
                        for="source"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Sumber
                    </label>

                    <select
                        id="source"
                        name="source"
                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                    >
                        <option value="">Semua Sumber</option>

                        @foreach ($sourceOptions as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(request('source') === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="priority"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Prioritas
                    </label>

                    <select
                        id="priority"
                        name="priority"
                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                    >
                        <option value="">Semua Prioritas</option>

                        @foreach ($priorityOptions as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(request('priority') === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="assigned_to_employee_id"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        PIC
                    </label>

                    <select
                        id="assigned_to_employee_id"
                        name="assigned_to_employee_id"
                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                    >
                        <option value="">Semua PIC</option>

                        <option
                            value="none"
                            @selected(request('assigned_to_employee_id') === 'none')
                        >
                            Belum Ada PIC
                        </option>

                        @foreach ($employees as $employee)
                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    (string) request('assigned_to_employee_id')
                                    === (string) $employee->id
                                )
                            >
                                {{ $employee->name }}
                                @if ($employee->unit?->name)
                                    — {{ $employee->unit->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('operations.tickets.index') }}"
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
                    @if (request()->filled('search'))
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                            Pencarian: {{ request('search') }}
                        </span>
                    @endif

                    @if ($selectedStatusLabel)
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                            Status: {{ $selectedStatusLabel }}
                        </span>
                    @endif

                    @if ($selectedCategoryLabel)
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                            Jenis: {{ $selectedCategoryLabel }}
                        </span>
                    @endif

                    @if ($selectedSourceLabel)
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                            Sumber: {{ $selectedSourceLabel }}
                        </span>
                    @endif

                    @if ($selectedPriorityLabel)
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                            Prioritas: {{ $selectedPriorityLabel }}
                        </span>
                    @endif

                    @if ($selectedPicLabel)
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                            PIC: {{ $selectedPicLabel }}
                        </span>
                    @endif
                </div>
            </div>

            <a
                href="{{ route('operations.tickets.index') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 transition hover:text-sky-950"
            >
                <x-icon name="x" class="h-4 w-4" />
                Hapus Filter
            </a>
        </div>
    </section>
@endif
    {{-- DAFTAR TIKET --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="ticket-check" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Daftar Tiket Operasional
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Menampilkan {{ $tickets->count() }} dari
                            {{ $tickets->total() }} tiket sesuai akses dan filter.
                        </p>
                    </div>
                </div>

                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                    {{ $tickets->total() }} data
                </span>
            </div>
        </div>

        {{-- TABEL DESKTOP --}}
        <div class="hidden overflow-x-auto xl:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="min-w-[270px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Tiket
                        </th>

                        <th class="min-w-[190px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Pemohon
                        </th>

                        <th class="min-w-[150px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Jenis
                        </th>

                        <th class="min-w-[130px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status
                        </th>

                        <th class="min-w-[170px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            PIC
                        </th>

                        <th class="min-w-[120px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Prioritas
                        </th>

                        <th class="min-w-[110px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Sumber
                        </th>

                        <th class="min-w-[125px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Dibuat
                        </th>

                        <th class="min-w-[280px] px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tickets as $ticket)
                        @php
                            $statusClasses = match ($ticket->status) {
                                \App\Models\OperationalTicket::STATUS_BARU =>
                                    'border-sky-200 bg-sky-50 text-sky-700',

                                \App\Models\OperationalTicket::STATUS_DIPROSES =>
                                    'border-amber-200 bg-amber-50 text-amber-700',

                                \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON =>
                                    'border-violet-200 bg-violet-50 text-violet-700',

                                \App\Models\OperationalTicket::STATUS_SELESAI =>
                                    'border-emerald-200 bg-emerald-50 text-emerald-700',

                                \App\Models\OperationalTicket::STATUS_DIBATALKAN =>
                                    'border-rose-200 bg-rose-50 text-rose-700',

                                default =>
                                    'border-slate-200 bg-slate-50 text-slate-700',
                            };

                            $priorityClasses = match ($ticket->priority) {
                                \App\Models\OperationalTicket::PRIORITY_HIGH =>
                                    'border-rose-200 bg-rose-50 text-rose-700',

                                \App\Models\OperationalTicket::PRIORITY_LOW =>
                                    'border-slate-200 bg-slate-50 text-slate-600',

                                default =>
                                    'border-sky-200 bg-sky-50 text-sky-700',
                            };

                            $sourceClasses =
                                $ticket->source === \App\Models\OperationalTicket::SOURCE_PUBLIC
                                    ? 'border-cyan-200 bg-cyan-50 text-cyan-700'
                                    : 'border-slate-200 bg-slate-50 text-slate-700';

                            $canDeleteTicket =
                                $canManageTicket
                                && in_array(
                                    $ticket->status,
                                    [
                                        \App\Models\OperationalTicket::STATUS_BARU,
                                        \App\Models\OperationalTicket::STATUS_DIBATALKAN,
                                    ],
                                    true
                                )
                                && (int) $ticket->daily_reports_count === 0;
                        @endphp

                        <tr class="transition hover:bg-slate-50/80">
                            {{-- TIKET --}}
                            <td class="px-5 py-4 align-top">
                                <a
                                    href="{{ route('operations.tickets.show', $ticket) }}"
                                    class="font-semibold leading-6 text-slate-900 transition hover:text-sky-700"
                                >
                                    {{ $ticket->title }}
                                </a>

                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-semibold text-slate-500">
                                        {{ $ticket->ticket_code }}
                                    </span>

                                    @if ((int) $ticket->daily_reports_count > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                            <x-icon name="clipboard-list" class="h-3 w-3" />
                                            {{ $ticket->daily_reports_count }} laporan
                                        </span>
                                    @endif
                                </div>

                                @if ($ticket->description)
                                    <p class="mt-2 line-clamp-2 max-w-md text-xs leading-5 text-slate-500">
                                        {{ $ticket->description }}
                                    </p>
                                @endif
                            </td>

                            {{-- PEMOHON --}}
                            <td class="px-5 py-4 align-top">
                                <p class="text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->requester_name }}
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    {{ $ticket->requester_unit ?: 'Unit belum diisi' }}
                                </p>
                            </td>

                            {{-- JENIS --}}
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $ticket->category_label }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>

                            {{-- PIC --}}
                            <td class="px-5 py-4 align-top">
                                @if ($ticket->assignedToEmployee)
                                    <div class="flex items-start gap-2">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                                            <x-icon name="user-check" class="h-4 w-4" />
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold leading-5 text-slate-900">
                                                {{ $ticket->assignedToEmployee->name }}
                                            </p>

                                            @if ($ticket->assignedToEmployee->unit?->name)
                                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                                    {{ $ticket->assignedToEmployee->unit->name }}
                                                </p>
                                            @endif

                                            @if (
                                                auth()->user()->employee
                                                && (int) $ticket->assigned_to_employee_id
                                                    === (int) auth()->user()->employee->id
                                            )
                                                <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                    <x-icon name="user-check" class="h-3 w-3" />
                                                    Anda PIC
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        <x-icon name="alert-circle" class="h-3.5 w-3.5" />
                                        Belum Ada PIC
                                    </span>
                                @endif
                            </td>

                            {{-- PRIORITAS --}}
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $priorityClasses }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>

                            {{-- SUMBER --}}
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $sourceClasses }}">
                                    {{ $ticket->source_label }}
                                </span>
                            </td>

                            {{-- DIBUAT --}}
                            <td class="whitespace-nowrap px-5 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $ticket->created_at?->format('d M Y') }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $ticket->created_at?->format('H:i') }}
                                </p>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-5 py-4 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a
                                        href="{{ route('operations.tickets.show', $ticket) }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                    >
                                        Detail
                                        <x-icon name="chevron-right" class="h-4 w-4" />
                                    </a>

                                    @if ((int) $ticket->daily_reports_count > 0)
                                        <span
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-sm font-semibold text-emerald-700"
                                            title="Tiket tidak dapat dihapus karena sudah mempunyai laporan harian."
                                        >
                                            <x-icon name="clipboard-list" class="h-4 w-4" />
                                            {{ $ticket->daily_reports_count }} Laporan
                                        </span>
                                    @endif

                                    @if ($canDeleteTicket)
                                        <form
                                            x-data
                                            method="POST"
                                            action="{{ route('operations.tickets.destroy', $ticket) }}"
                                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Tiket Operasional?',
                                                message: 'Tiket {{ $ticket->ticket_code }} akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
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
                            <td colspan="9" class="px-6 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <x-icon name="ticket-check" class="h-7 w-7" />
                                </div>

                                <h3 class="mt-4 text-base font-semibold text-slate-900">
                                    @if ($hasActiveFilters)
                                        Tidak ada tiket yang cocok
                                    @else
                                        Belum ada tiket operasional
                                    @endif
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                    @if ($hasActiveFilters)
                                        Ubah atau hapus filter untuk menemukan tiket yang dicari.
                                    @else
                                        Tiket dari pemohon atau input manual petugas akan tampil di halaman ini.
                                    @endif
                                </p>

                                @if ($hasActiveFilters)
                                    <a
                                        href="{{ route('operations.tickets.index') }}"
                                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                    >
                                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                                        Reset Filter
                                    </a>
                                @else
                                    <a
                                        href="{{ route('operations.tickets.create') }}"
                                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon name="ticket" class="h-4 w-4" />
                                        Input Tiket Manual
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD MOBILE DAN TABLET --}}
        <div class="divide-y divide-slate-100 xl:hidden">
            @forelse ($tickets as $ticket)
                @php
                    $statusClasses = match ($ticket->status) {
                        \App\Models\OperationalTicket::STATUS_BARU =>
                            'border-sky-200 bg-sky-50 text-sky-700',

                        \App\Models\OperationalTicket::STATUS_DIPROSES =>
                            'border-amber-200 bg-amber-50 text-amber-700',

                        \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON =>
                            'border-violet-200 bg-violet-50 text-violet-700',

                        \App\Models\OperationalTicket::STATUS_SELESAI =>
                            'border-emerald-200 bg-emerald-50 text-emerald-700',

                        \App\Models\OperationalTicket::STATUS_DIBATALKAN =>
                            'border-rose-200 bg-rose-50 text-rose-700',

                        default =>
                            'border-slate-200 bg-slate-50 text-slate-700',
                    };

                    $priorityClasses = match ($ticket->priority) {
                        \App\Models\OperationalTicket::PRIORITY_HIGH =>
                            'border-rose-200 bg-rose-50 text-rose-700',

                        \App\Models\OperationalTicket::PRIORITY_LOW =>
                            'border-slate-200 bg-slate-50 text-slate-600',

                        default =>
                            'border-sky-200 bg-sky-50 text-sky-700',
                    };

                    $sourceClasses =
                        $ticket->source === \App\Models\OperationalTicket::SOURCE_PUBLIC
                            ? 'border-cyan-200 bg-cyan-50 text-cyan-700'
                            : 'border-slate-200 bg-slate-50 text-slate-700';

                    $canDeleteTicket =
                        $canManageTicket
                        && in_array(
                            $ticket->status,
                            [
                                \App\Models\OperationalTicket::STATUS_BARU,
                                \App\Models\OperationalTicket::STATUS_DIBATALKAN,
                            ],
                            true
                        )
                        && (int) $ticket->daily_reports_count === 0;
                @endphp

                <article class="p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                            {{ $ticket->status_label }}
                        </span>

                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $priorityClasses }}">
                            {{ $ticket->priority_label }}
                        </span>

                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $sourceClasses }}">
                            {{ $ticket->source_label }}
                        </span>

                        @if ((int) $ticket->daily_reports_count > 0)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                                {{ $ticket->daily_reports_count }} laporan
                            </span>
                        @endif
                    </div>

                    <p class="mt-3 font-mono text-xs font-semibold text-slate-500">
                        {{ $ticket->ticket_code }}
                    </p>

                    <h3 class="mt-1 text-base font-semibold leading-6 text-slate-900">
                        {{ $ticket->title }}
                    </h3>

                    @if ($ticket->description)
                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">
                            {{ $ticket->description }}
                        </p>
                    @endif

                    <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Pemohon
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $ticket->requester_name }}
                            </dd>

                            <dd class="mt-1 text-xs text-slate-500">
                                {{ $ticket->requester_unit ?: 'Unit belum diisi' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Jenis Permintaan
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $ticket->category_label }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                PIC
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $ticket->assignedToEmployee?->name ?? 'Belum Ada PIC' }}
                            </dd>

                            @if (
                                auth()->user()->employee
                                && (int) $ticket->assigned_to_employee_id
                                    === (int) auth()->user()->employee->id
                            )
                                <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                    <x-icon name="user-check" class="h-3 w-3" />
                                    Anda PIC
                                </span>
                            @endif
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Dibuat
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $ticket->created_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <a
                            href="{{ route('operations.tickets.show', $ticket) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Detail
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </a>

                        @if ($canDeleteTicket)
                            <form
                                x-data
                                method="POST"
                                action="{{ route('operations.tickets.destroy', $ticket) }}"
                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                    title: 'Hapus Tiket Operasional?',
                                    message: 'Tiket {{ $ticket->ticket_code }} akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
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
                        @elseif ((int) $ticket->daily_reports_count > 0)
                            <span
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700"
                                title="Tiket tidak dapat dihapus karena sudah mempunyai laporan harian."
                            >
                                <x-icon name="clipboard-list" class="h-4 w-4" />
                                Terhubung ke Laporan
                            </span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <x-icon name="ticket-check" class="h-7 w-7" />
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                        @if ($hasActiveFilters)
                            Tidak ada tiket yang cocok
                        @else
                            Belum ada tiket operasional
                        @endif
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        @if ($hasActiveFilters)
                            Ubah atau hapus filter untuk menemukan tiket yang dicari.
                        @else
                            Tiket dari pemohon atau input manual petugas akan tampil di halaman ini.
                        @endif
                    </p>

                    @if ($hasActiveFilters)
                        <a
                            href="{{ route('operations.tickets.index') }}"
                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                            Reset Filter
                        </a>
                    @else
                        <a
                            href="{{ route('operations.tickets.create') }}"
                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="ticket" class="h-4 w-4" />
                            Input Tiket Manual
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if ($tickets->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                {{ $tickets->links() }}
            </div>
        @endif
    </section>
    </div>
</x-app-layout>