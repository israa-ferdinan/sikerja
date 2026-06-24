<div class="space-y-6">

    <x-page-hero
        badge="Monitoring Unit"
        title="Pantau laporan pegawai dalam satu halaman"
        description="Gunakan filter periode, pegawai, tupoksi, server, aplikasi, dan pencarian untuk melihat laporan kerja harian secara lebih cepat dan terstruktur."
        icon="bar-chart-3"
    >
        <x-slot:aside>
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-cyan-100">
                            Periode Aktif
                        </p>
                        <p class="mt-2 text-2xl font-bold text-white">
                            {{ $months[(int) $month] ?? '-' }} {{ $year }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-200">
                        <x-icon name="calendar-days" class="h-6 w-6" />
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                        Total: {{ number_format($recap['total_reports']) }} laporan
                    </span>

                    <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                        Pegawai: {{ number_format($recap['total_employees']) }}
                    </span>
                </div>

                <button
                    type="button"
                    wire:click="exportMonthly"
                    wire:loading.attr="disabled"
                    wire:target="exportMonthly"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-sm transition hover:bg-cyan-50 focus:outline-none focus:ring-4 focus:ring-white/20 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <x-icon wire:loading.remove wire:target="exportMonthly" name="file-spreadsheet" class="h-4 w-4" />

                    <span wire:loading.remove wire:target="exportMonthly">
                        Export Bulanan
                    </span>

                    <span wire:loading.flex wire:target="exportMonthly" class="items-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-slate-950 border-t-transparent"></span>
                        Menyiapkan...
                    </span>
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    {{-- Filter Compact --}}
    <x-ui.card padding="p-5">
        <div x-data="{ showAdvancedFilter: false }">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <x-icon name="list-filter" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Filter Laporan
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Gunakan filter utama untuk periode dan pencarian. Filter detail dapat dibuka jika diperlukan.
                        </p>
                    </div>
                </div>

                <x-ui.loading
                    target="month,year,employeeId,dutyId,serverId,applicationId,search,resetFilter"
                    text="Memuat laporan..."
                />
            </div>

            {{-- Filter Utama --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">
                <div class="xl:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Bulan
                    </label>

                    <select
                        wire:model.change="month"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                    >
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="xl:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Tahun
                    </label>

                    <select
                        wire:model.change="year"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                    >
                        @foreach($years as $item)
                            <option value="{{ $item }}">
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 xl:col-span-6">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Pencarian
                    </label>

                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </div>

                        <input
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari judul, deskripsi, pegawai, tupoksi, server, atau aplikasi..."
                            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>
                </div>

                <div class="flex items-end gap-2 xl:col-span-2">
                    <button
                        type="button"
                        x-on:click="showAdvancedFilter = !showAdvancedFilter"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-100"
                    >
                        <x-icon name="sliders-horizontal" class="h-4 w-4" />

                        <span x-text="showAdvancedFilter ? 'Tutup' : 'Detail'"></span>
                    </button>

                    <button
                        type="button"
                        wire:click="resetFilter"
                        wire:loading.attr="disabled"
                        wire:target="resetFilter"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-70"
                        title="Reset Filter"
                    >
                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                    </button>
                </div>
            </div>

            {{-- Filter Detail --}}
            <div
                x-show="showAdvancedFilter"
                x-collapse
                class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4"
            >
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">
                            Filter Detail
                        </h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Gunakan filter tambahan untuk mempersempit data berdasarkan pegawai, tupoksi, server, atau aplikasi.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Pegawai
                        </label>

                        <select
                            wire:model.change="employeeId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Semua Pegawai</option>

                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tupoksi
                        </label>

                        <select
                            wire:model.change="dutyId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Semua Tupoksi</option>

                            @foreach($duties as $duty)
                                <option value="{{ $duty->id }}">
                                    {{ $duty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Server
                        </label>

                        <select
                            wire:model.change="serverId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Semua Server</option>

                            @foreach($servers as $server)
                                <option value="{{ $server->id }}">
                                    {{ $server->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Aplikasi
                        </label>

                        <select
                            wire:model.change="applicationId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Semua Aplikasi</option>

                            @foreach($applications as $application)
                                <option value="{{ $application->id }}">
                                    {{ $application->name }}
                                </option>
                            @endforeach
                        </select>

                        @if($serverId)
                            <p class="mt-1.5 text-xs text-slate-500">
                                Aplikasi difilter berdasarkan server terpilih.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Active Filter --}}
            <div class="mt-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-slate-600">
                        Filter aktif:
                    </span>

                    <span class="rounded-full bg-white px-3 py-1 font-semibold text-slate-800 shadow-sm ring-1 ring-slate-200">
                        {{ $months[(int) $month] ?? '-' }} {{ $year }}
                    </span>

                    @if($employeeId)
                        <span class="rounded-full bg-white px-3 py-1 text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Pegawai: {{ $employees->firstWhere('id', (int) $employeeId)?->name }}
                        </span>
                    @endif

                    @if($dutyId)
                        <span class="rounded-full bg-white px-3 py-1 text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Tupoksi: {{ $duties->firstWhere('id', (int) $dutyId)?->name }}
                        </span>
                    @endif

                    @if($serverId)
                        <span class="rounded-full bg-white px-3 py-1 text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Server: {{ $servers->firstWhere('id', (int) $serverId)?->name }}
                        </span>
                    @endif

                    @if($applicationId)
                        <span class="rounded-full bg-white px-3 py-1 text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Aplikasi: {{ $applications->firstWhere('id', (int) $applicationId)?->name }}
                        </span>
                    @endif

                    @if($search)
                        <span class="rounded-full bg-white px-3 py-1 text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Search: "{{ $search }}"
                        </span>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="resetFilter"
                    wire:loading.attr="disabled"
                    wire:target="resetFilter"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <x-icon name="rotate-ccw" class="h-3.5 w-3.5" />
                    Reset Filter
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- Rekap --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Laporan
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($recap['total_reports']) }}
                    </p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Total laporan periode {{ $months[(int) $month] ?? '-' }} {{ $year }}.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                    <x-icon name="file-text" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Pegawai Unit
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($recap['total_employees']) }}
                    </p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Pegawai aktif dalam unit Kanit.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <x-icon name="users" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Sudah Input
                    </p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">
                        {{ number_format($recap['submitted_employees']) }}
                    </p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Pegawai yang sudah membuat minimal satu laporan.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                    <x-icon name="user-check" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Belum Input
                    </p>
                    <p class="mt-2 text-3xl font-bold text-rose-700">
                        {{ number_format($recap['not_submitted_employees']) }}
                    </p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Pegawai yang belum membuat laporan pada periode ini.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-700">
                    <x-icon name="user-x" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Table --}}
    <div class="relative">
        {{-- Loading overlay --}}
        <div
            wire:loading.flex
            wire:target="month,year,employeeId,dutyId,serverId,applicationId,search,gotoPage,nextPage,previousPage,resetFilter"
            class="absolute inset-0 z-20 hidden items-start justify-center rounded-2xl bg-white/60 pt-20 backdrop-blur-[1px]"
        >
            <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm">
                <span class="h-4 w-4 animate-spin rounded-full border-2 border-cyan-600 border-t-transparent"></span>
                Memuat data laporan...
            </div>
        </div>

        @if($reports->count())
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8 6h13" />
                                <path d="M8 12h13" />
                                <path d="M8 18h13" />
                                <path d="M3 6h.01" />
                                <path d="M3 12h.01" />
                                <path d="M3 18h.01" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Daftar Laporan Pegawai
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Data laporan hanya menampilkan pegawai dalam unit Kanit.
                            </p>
                        </div>
                    </div>

                    <div class="rounded-full bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                        {{ number_format($reports->total()) }} data ditemukan
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="w-[120px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Tanggal
                                </th>
                                <th class="min-w-[180px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Pegawai
                                </th>
                                <th class="min-w-[320px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Laporan
                                </th>
                                <th class="min-w-[220px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Tupoksi
                                </th>
                                <th class="min-w-[180px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Objek Laporan
                                </th>
                                <th class="w-[100px] px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Foto
                                </th>
                                <th class="w-[100px] px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            wire:loading.class="opacity-50"
                            wire:target="month,year,employeeId,dutyId,serverId,applicationId,search,gotoPage,nextPage,previousPage,resetFilter"
                            class="divide-y divide-slate-100 bg-white transition"
                        >
                            @foreach($reports as $report)
                                <tr class="transition hover:bg-cyan-50/30">
                                    <td class="whitespace-nowrap px-5 py-4 align-top">
                                        <div class="text-sm font-semibold text-slate-800">
                                            {{ optional($report->report_date)->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $report->employee->name ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $report->employee?->jobPosition?->name ?? $report->employee?->position ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                                            <div class="text-sm font-bold text-slate-900">
                                                {{ $report->title }}
                                            </div>

                                            <div class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                                {{ $report->description }}
                                            </div>

                                            @if(!empty($report->notes))
                                                <div class="mt-2 line-clamp-1 text-xs leading-5 text-slate-500">
                                                    <span class="font-semibold text-slate-600">Catatan:</span>
                                                    {{ $report->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="space-y-2">
                                            @if($report->duty)
                                                <div class="text-sm font-bold text-slate-900">
                                                    {{ $report->duty->name }}
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-400">-</span>
                                            @endif

                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($report->is_delegated)
                                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                                        Delegasi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                                                        Normal
                                                    </span>
                                                @endif

                                                @if($report->duty?->classification)
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                                        {{ $report->duty->classification->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($report->is_delegated)
                                                <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs leading-5 text-indigo-800">
                                                    <div>
                                                        Pemilik:
                                                        <span class="font-semibold">
                                                            {{ $report->dutyOwnerEmployee?->name ?? '-' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        Pelapor:
                                                        <span class="font-semibold">
                                                            {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($report->duty)
                                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs leading-5 text-slate-600">
                                                    <div>
                                                        <span class="font-semibold text-slate-700">
                                                            Jenis Objek:
                                                        </span>
                                                        {{ $report->duty?->object_type_label ?? '-' }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        @if ($report->server || $report->application)
                                            <div class="space-y-2">
                                                @if ($report->server)
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                            Server
                                                        </div>
                                                        <div class="mt-1 text-sm font-semibold text-slate-800">
                                                            {{ $report->server?->name }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($report->application)
                                                    <div class="rounded-xl border border-cyan-100 bg-cyan-50 px-3 py-2">
                                                        <div class="text-xs font-bold uppercase tracking-wide text-cyan-700">
                                                            Aplikasi
                                                        </div>
                                                        <div class="mt-1 text-sm font-semibold text-cyan-900">
                                                            {{ $report->application?->name }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">
                                                Non Server/Aplikasi
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-center align-top">
                                        <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $report->photos->count() > 0 ? 'bg-emerald-50 text-emerald-700 ring-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-200' }}">
                                            {{ $report->photos->count() }} foto
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right align-top text-sm">
                                        <a
                                            href="{{ route('kanit.reports.detail', $report) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200"
                                        >
                                            Detail
                                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="border-t border-slate-200 bg-white px-5 py-4">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                        <path d="M14 2v6h6" />
                        <path d="M8 13h8" />
                        <path d="M8 17h5" />
                    </svg>
                </div>

                <h3 class="mt-4 text-base font-bold text-slate-900">
                    Belum ada laporan
                </h3>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Tidak ada laporan pada filter bulan, tahun, atau pegawai yang dipilih.
                </p>

                <div class="mt-5">
                    <button
                        type="button"
                        wire:click="resetFilter"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                        Reset Filter
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>