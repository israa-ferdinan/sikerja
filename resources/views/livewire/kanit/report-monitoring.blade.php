<div class="space-y-6">
    {{-- Header --}}
    <x-ui.page-header
        title="Monitoring Laporan Unit"
        subtitle="Pantau laporan kerja harian pegawai dalam unit Anda."
    >
        <x-slot:action>
            <button
                type="button"
                wire:click="exportMonthly"
                wire:loading.attr="disabled"
                wire:target="exportMonthly"
                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span wire:loading.remove wire:target="exportMonthly">
                    Export Bulanan
                </span>

                <span wire:loading.flex wire:target="exportMonthly" class="items-center gap-2">
                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    Menyiapkan...
                </span>
            </button>
        </x-slot:action>
    </x-ui.page-header>

    {{-- Filter --}}
    <x-ui.card padding="p-5">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Filter Laporan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Gunakan filter untuk melihat laporan berdasarkan periode, pegawai, tupoksi, klasifikasi, objek, server, atau aplikasi.
                </p>
            </div>

            <x-ui.loading
                target="month,year,employeeId,dutyId,serverId,applicationId,search,resetFilter"
                text="Memuat laporan..."
            />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Bulan
                </label>

                <select
                    wire:model.change="month"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Tahun
                </label>

                <select
                    wire:model.change="year"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    @foreach($years as $item)
                        <option value="{{ $item }}">
                            {{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Pegawai
                </label>

                <select
                    wire:model.change="employeeId"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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

            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Pencarian
                </label>

                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                    </div>

                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Cari judul, deskripsi, pegawai, tupoksi, klasifikasi, objek, server, aplikasi..."
                        class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    >
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
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-70"
            >
                Reset Filter
            </button>
        </div>
    </x-ui.card>

    {{-- Rekap --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Laporan
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($recap['total_reports']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    📄
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Total laporan pada {{ $months[(int) $month] ?? '-' }} {{ $year }}.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Pegawai Unit
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($recap['total_employees']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                    👥
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Pegawai aktif dalam unit Kanit.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Sudah Input
                    </p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600">
                        {{ number_format($recap['submitted_employees']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-2xl">
                    ✅
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Pegawai yang sudah membuat minimal satu laporan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Belum Input
                    </p>
                    <p class="mt-2 text-3xl font-bold text-red-600">
                        {{ number_format($recap['not_submitted_employees']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-2xl">
                    ⚠️
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Pegawai yang belum membuat laporan pada periode ini.
            </p>
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
                <span class="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></span>
                Memuat data laporan...
            </div>
        </div>

        @if($reports->count())
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-bold text-slate-900">
                        Daftar Laporan Pegawai
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Data laporan hanya menampilkan pegawai dalam unit Kanit.
                    </p>
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
                                <th class="min-w-[340px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Laporan
                                </th>
                                <th class="min-w-[180px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Tupoksi
                                </th>
                                <th class="min-w-[180px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Server / Aplikasi Laporan
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
                                <tr class="transition hover:bg-slate-50/80">
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
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($report->duty)
                                                <x-ui.badge variant="primary">
                                                    {{ $report->duty->name }}
                                                </x-ui.badge>
                                            @else
                                                <span class="text-sm text-slate-400">-</span>
                                            @endif

                                            @if ($report->is_delegated)
                                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700">
                                                    Delegasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                                    Normal
                                                </span>
                                            @endif
                                        </div>

                                        @if ($report->is_delegated)
                                            <div class="mt-2 space-y-1 text-xs text-slate-500">
                                                <div>
                                                    Pemilik:
                                                    <span class="font-semibold text-slate-700">
                                                        {{ $report->dutyOwnerEmployee?->name ?? '-' }}
                                                    </span>
                                                </div>
                                                <div>
                                                    Pelapor:
                                                    <span class="font-semibold text-slate-700">
                                                        {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                        @if($report->duty)
                                        <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-semibold text-slate-700">
                                                    Klasifikasi:
                                                </span>

                                                @if($report->duty->classification)
                                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 font-semibold text-blue-700">
                                                        {{ $report->duty->classification->name }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">
                                                        Tanpa klasifikasi
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-2 grid gap-1">
                                                <div>
                                                    <span class="font-semibold text-slate-700">
                                                        Jenis Objek:
                                                    </span>
                                                    {{ $report->duty?->object_type_label ?? '-' }}
                                                </div>

                                                <div class="text-slate-500">
                                                    Detail objek pekerjaan dicatat pada laporan harian.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        @if ($report->server || $report->application)
                                            <div class="space-y-1">
                                                @if ($report->server)
                                                    <div class="text-sm font-semibold text-slate-800">
                                                        {{ $report->server?->name }}
                                                    </div>
                                                @endif

                                                @if ($report->application)
                                                    <div class="text-xs font-medium text-violet-700">
                                                        {{ $report->application?->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Tidak menggunakan server/aplikasi
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-center align-top">
                                        <x-ui.badge variant="{{ $report->photos->count() > 0 ? 'success' : 'neutral' }}">
                                            {{ $report->photos->count() }} foto
                                        </x-ui.badge>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right align-top text-sm">
                                        <a
                                            href="{{ route('kanit.reports.detail', $report) }}"
                                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                        >
                                            Detail
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
            <x-ui.empty-state
                icon="📄"
                title="Belum ada laporan"
                message="Tidak ada laporan pada filter bulan, tahun, atau pegawai yang dipilih."
            >
                <x-slot:action>
                    <button
                        type="button"
                        wire:click="resetFilter"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Reset Filter
                    </button>
                </x-slot:action>
            </x-ui.empty-state>
        @endif
    </div>
</div>