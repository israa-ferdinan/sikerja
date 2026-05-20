<div class="space-y-6">
    {{-- Page Header --}}
    <x-ui.page-header
        title="Riwayat Laporan Saya"
        subtitle="Daftar laporan kerja harian yang sudah Anda buat."
    >
        <x-slot:action>
            @if (empty($missingEmployee))
            <a
                href="{{ route('pegawai.reports.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                </svg>
                Buat Laporan
            </a>
            @endif
        </x-slot:action>
    </x-ui.page-header>

    @if (!empty($missingEmployee))
        <div class="mb-4 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            Akun Anda belum terhubung dengan data pegawai. Silakan hubungi admin untuk melengkapi data pegawai terlebih dahulu.
        </div>
    @endif

    {{-- Filter --}}
    <x-ui.card padding="p-4">
        <div class="grid gap-4 md:grid-cols-12 md:items-end">
            {{-- Search --}}
            <div class="md:col-span-6">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">
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
                        class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        placeholder="Cari laporan, tupoksi, aplikasi, server..."
                    >
                </div>
            </div>

            {{-- Month --}}
            <div class="md:col-span-3">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">
                    Bulan Laporan
                </label>

                <input
                    type="month"
                    wire:model.live="month"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
            </div>

            {{-- Loading --}}
            <div class="md:col-span-3">
                <div class="flex min-h-[42px] items-center justify-start md:justify-end">
                    <x-ui.loading target="search,month" text="Memuat laporan..." />
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Table / Content --}}
    <div class="relative">
        {{-- Soft loading overlay for table --}}
        <div
            wire:loading.flex
            wire:target="search,month,gotoPage,nextPage,previousPage"
            class="absolute inset-0 z-20 hidden items-start justify-center rounded-2xl bg-white/60 pt-20 backdrop-blur-[1px]"
        >
            <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm">
                <span class="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></span>
                Memuat data...
            </div>
        </div>

        @if ($reports->count())
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="w-[140px] px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Tanggal
                                </th>

                                <th class="min-w-[360px] px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Laporan
                                </th>

                                <th class="w-[160px] px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Foto
                                </th>

                                <th class="w-[120px] px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Status
                                </th>

                                <th class="w-[150px] px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            wire:loading.class="opacity-50"
                            wire:target="search,month,gotoPage,nextPage,previousPage"
                            class="divide-y divide-slate-100 bg-white transition"
                        >
                            @foreach ($reports as $report)
                                <tr class="transition hover:bg-slate-50/80">
                                    {{-- Tanggal --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="text-sm font-semibold text-slate-800">
                                            {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l') }}
                                        </div>
                                    </td>

                                    {{-- Laporan Compact Card --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($report->duty)
                                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
                                                        {{ $report->duty->name }}
                                                    </span>
                                                @endif

                                                @if ($report->application)
                                                    <span class="inline-flex items-center rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700 ring-1 ring-violet-200">
                                                        {{ $report->application->name }}
                                                    </span>
                                                @endif

                                                @if ($report->server)
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                                        {{ $report->server->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <p class="line-clamp-2 text-sm leading-6 text-slate-700">
                                                    {{ $report->description }}
                                                </p>

                                                @if (!empty($report->result))
                                                    <p class="mt-2 line-clamp-1 text-xs leading-5 text-slate-500">
                                                        <span class="font-semibold text-slate-600">Hasil:</span>
                                                        {{ $report->result }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Foto --}}
                                    <td class="px-4 py-4 align-top">
                                        @if ($report->photos && $report->photos->count())
                                            <div class="flex items-center gap-2">
                                                @foreach ($report->photos->take(3) as $photo)
                                                    <div class="h-12 w-12 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-sm">
                                                        <img
                                                            src="{{ asset('storage/' . $photo->file_path) }}"
                                                            alt="Foto laporan"
                                                            class="h-full w-full object-cover"
                                                        >
                                                    </div>
                                                @endforeach

                                                @if ($report->photos->count() > 3)
                                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">
                                                        +{{ $report->photos->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Tidak ada foto
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-4 align-top">
                                        @php
                                            $status = $report->status ?? 'draft';

                                            $variant = match ($status) {
                                                'submitted', 'menunggu', 'pending' => 'warning',
                                                'approved', 'selesai', 'accepted' => 'success',
                                                'rejected', 'ditolak' => 'danger',
                                                default => 'neutral',
                                            };

                                            $label = match ($status) {
                                                'submitted' => 'Dikirim',
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'accepted' => 'Diterima',
                                                'rejected' => 'Ditolak',
                                                'draft' => 'Draft',
                                                default => ucfirst($status),
                                            };
                                        @endphp

                                        <x-ui.badge :variant="$variant">
                                            {{ $label }}
                                        </x-ui.badge>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-4 align-top text-right">
                                        <div class="flex justify-end gap-2">
                                            <a
                                                href="{{ route('pegawai.reports.show', $report) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('pegawai.reports.edit', $report) }}"
                                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                            >
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-slate-200 bg-white px-4 py-3">
                    {{ $reports->links() }}
                </div>
            </div>
        @else
            <x-ui.empty-state
                icon="📝"
                title="Belum ada laporan"
                message="Laporan kerja harian Anda untuk filter yang dipilih belum tersedia."
            >
                <x-slot:action>
                    <a
                        href="{{ route('pegawai.reports.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Buat Laporan Pertama
                    </a>
                </x-slot:action>
            </x-ui.empty-state>
        @endif
    </div>
</div>