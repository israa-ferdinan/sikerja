<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Monitoring Laporan Unit
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Pantau laporan kerja harian pegawai dalam unit Anda.
            </p>
        </div>

        <button
            type="button"
            wire:click="exportMonthly"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="exportMonthly">
                Export Bulanan
            </span>

            <span wire:loading wire:target="exportMonthly">
                Menyiapkan Export...
            </span>
        </button>
    </div>

    <div class="mb-4 rounded-xl border border-yellow-300 bg-yellow-50 p-4">

    {{-- Filter --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Bulan
                </label>

                <select
                    wire:model.change="month"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Tahun
                </label>

                <select
                    wire:model.change="year"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($years as $item)
                        <option value="{{ $item }}">
                            {{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Pegawai
                </label>

                <select
                    wire:model.change="employeeId"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Tupoksi
                </label>

                <select
                    wire:model.change="dutyId"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Server
                </label>

                <select
                    wire:model.change="serverId"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Aplikasi
                </label>

                <select
                    wire:model.change="applicationId"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Aplikasi</option>

                    @foreach($applications as $application)
                        <option value="{{ $application->id }}">
                            {{ $application->name }}
                        </option>
                    @endforeach
                </select>

                @if($serverId)
                    <p class="mt-1 text-xs text-gray-500">
                        Aplikasi difilter berdasarkan server terpilih.
                    </p>
                @endif
            </div>

            <div class="xl:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Search
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari judul, deskripsi, hasil, pegawai, tupoksi, server, aplikasi..."
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
        </div>

        <div class="mt-4 flex flex-col gap-3 rounded-xl bg-gray-50 px-4 py-3 text-xs text-gray-600 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span>
                    Filter aktif:
                </span>

                <span class="rounded-full bg-white px-3 py-1 font-semibold text-gray-800 shadow-sm">
                    {{ $months[(int) $month] ?? '-' }} {{ $year }}
                </span>

                @if($employeeId)
                    <span class="rounded-full bg-white px-3 py-1 text-gray-700 shadow-sm">
                        Pegawai: {{ $employees->firstWhere('id', (int) $employeeId)?->name }}
                    </span>
                @endif

                @if($dutyId)
                    <span class="rounded-full bg-white px-3 py-1 text-gray-700 shadow-sm">
                        Tupoksi: {{ $duties->firstWhere('id', (int) $dutyId)?->name }}
                    </span>
                @endif

                @if($serverId)
                    <span class="rounded-full bg-white px-3 py-1 text-gray-700 shadow-sm">
                        Server: {{ $servers->firstWhere('id', (int) $serverId)?->name }}
                    </span>
                @endif

                @if($applicationId)
                    <span class="rounded-full bg-white px-3 py-1 text-gray-700 shadow-sm">
                        Aplikasi: {{ $applications->firstWhere('id', (int) $applicationId)?->name }}
                    </span>
                @endif

                @if($search)
                    <span class="rounded-full bg-white px-3 py-1 text-gray-700 shadow-sm">
                        Search: "{{ $search }}"
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div wire:loading class="text-blue-600">
                    Memuat data laporan...
                </div>

                <button
                    type="button"
                    wire:click="resetFilter"
                    class="rounded-lg bg-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-300"
                >
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Rekap --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Laporan
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($recap['total_reports']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    📄
                </div>
            </div>

            <p class="mt-4 text-xs text-gray-500">
                Total laporan pada {{ $months[(int) $month] ?? '-' }} {{ $year }}.
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Pegawai Unit
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($recap['total_employees']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-2xl">
                    👥
                </div>
            </div>

            <p class="mt-4 text-xs text-gray-500">
                Pegawai aktif dalam unit Kanit.
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
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

            <p class="mt-4 text-xs text-gray-500">
                Pegawai yang sudah membuat minimal satu laporan.
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
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

            <p class="mt-4 text-xs text-gray-500">
                Pegawai yang belum membuat laporan pada periode ini.
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="text-base font-semibold text-gray-900">
                Daftar Laporan Pegawai
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Data laporan hanya menampilkan pegawai dalam unit Kanit.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tanggal
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Pegawai
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Laporan
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tupoksi
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Server / Aplikasi
                        </th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Foto
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ optional($report->report_date)->format('d/m/Y') }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $report->employee->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $report->employee->position ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="max-w-sm">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $report->title }}
                                    </div>
                                    <div class="mt-1 line-clamp-2 text-xs leading-5 text-gray-500">
                                        {{ $report->description }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-700">
                                {{ $report->duty->name ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="text-sm text-gray-800">
                                    {{ $report->server->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $report->application->name ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                    {{ $report->photos->count() }} foto
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                <a href="{{ route('kanit.reports.detail', $report) }}"
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">
                                        Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="text-base font-semibold text-gray-900">
                                        Belum ada laporan
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Tidak ada laporan pada bulan dan tahun yang dipilih.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>