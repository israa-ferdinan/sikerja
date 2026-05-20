<div class="space-y-6">
    {{-- Header --}}
    <x-ui.page-header
        title="Detail Laporan Pegawai"
        subtitle="Detail laporan kerja harian pegawai dalam unit Anda."
    >
        <x-slot:action>
            <a
                href="{{ route('kanit.reports.monitoring') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
            >
                ← Kembali
            </a>
        </x-slot:action>
    </x-ui.page-header>

    {{-- Summary Header --}}
    <x-ui.card>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge variant="primary">
                        Laporan Pegawai
                    </x-ui.badge>

                    <x-ui.badge variant="neutral">
                        {{ ucfirst($report->status ?? 'draft') }}
                    </x-ui.badge>
                </div>

                <h2 class="mt-3 text-xl font-bold leading-tight text-slate-900 sm:text-2xl">
                    {{ $report->title }}
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Dibuat oleh
                    <span class="font-semibold text-slate-700">
                        {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                    </span>
                    pada
                    <span class="font-semibold text-slate-700">
                        {{ optional($report->report_date)->format('d/m/Y') }}
                    </span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:min-w-[300px]">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        Foto
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $report->photos->count() }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        Tanggal
                    </p>
                    <p class="mt-2 text-sm font-bold text-slate-900">
                        {{ optional($report->report_date)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Status Pelaksanaan
                    </div>

                    <div class="mt-2">
                        @if ($report->is_delegated)
                            <span class="inline-flex rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                Laporan Delegasi
                            </span>

                            <div class="mt-3 space-y-1 text-sm text-gray-600">
                                <div>
                                    Pemilik Tupoksi:
                                    <span class="font-medium text-gray-900">
                                        {{ $report->dutyOwnerEmployee?->name ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    Dilaporkan Oleh:
                                    <span class="font-medium text-gray-900">
                                        {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                                    </span>
                                </div>

                                @if ($report->delegation)
                                    <div class="text-xs text-gray-500">
                                        Periode:
                                        {{ $report->delegation->start_date?->format('d/m/Y') ?? '-' }}
                                        s.d.
                                        {{ $report->delegation->end_date?->format('d/m/Y') ?? 'Tidak ditentukan' }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                Laporan Normal
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Content --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Main Content --}}
        <div class="space-y-6 xl:col-span-2">
            {{-- Detail Pekerjaan --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Detail Pekerjaan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Uraian kegiatan yang dilaporkan oleh pegawai.
                    </p>
                </div>

                <div class="space-y-5">
                    <div>
                        <h3 class="mb-2 text-sm font-bold text-slate-800">
                            Deskripsi Pekerjaan
                        </h3>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-700">
                            {!! nl2br(e($report->description ?? '-')) !!}
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-2 text-sm font-bold text-slate-800">
                            Hasil / Keterangan
                        </h3>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-700">
                            {!! nl2br(e($report->result ?? $report->notes ?? '-')) !!}
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Foto --}}
            <x-ui.card>
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Foto Dokumentasi
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Dokumentasi foto yang diunggah pegawai pada laporan ini.
                        </p>
                    </div>

                    <x-ui.badge variant="{{ $report->photos->count() > 0 ? 'success' : 'neutral' }}">
                        {{ $report->photos->count() }} foto
                    </x-ui.badge>
                </div>

                @if($report->photos->count())
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach($report->photos as $index => $photo)
                            <a
                                href="{{ Storage::url($photo->file_path) }}"
                                target="_blank"
                                class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm"
                            >
                                <img
                                    src="{{ Storage::url($photo->file_path) }}"
                                    alt="Foto laporan {{ $index + 1 }}"
                                    class="h-36 w-full object-cover transition duration-200 group-hover:scale-105"
                                >

                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                    <span class="text-xs font-semibold text-white">
                                        Foto {{ $index + 1 }}
                                    </span>
                                </div>

                                <div class="absolute right-2 top-2 rounded-full bg-white/90 px-2 py-1 text-[11px] font-bold text-slate-700 opacity-0 shadow-sm transition group-hover:opacity-100">
                                    Lihat
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        icon="📷"
                        title="Tidak ada foto"
                        message="Pegawai belum mengunggah foto dokumentasi untuk laporan ini."
                    />
                @endif
            </x-ui.card>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Informasi Pegawai --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Informasi Pegawai
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Identitas pegawai pembuat laporan.
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Nama Pegawai
                        </div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Jabatan
                        </div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $report->reportedByEmployee?->jobPosition?->name ?? $report->employee?->jobPosition?->name ?? $report->employee?->position ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Unit
                        </div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $report->reportedByEmployee?->unit?->name ?? $report->employee?->unit?->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Informasi Laporan --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Informasi Laporan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Metadata laporan kerja harian.
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Tanggal Laporan
                        </div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">
                            {{ optional($report->report_date)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Tupoksi
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            @if($report->duty)
                                <x-ui.badge variant="primary">
                                    {{ $report->duty->name }}
                                </x-ui.badge>
                            @else
                                <span class="text-sm text-slate-500">-</span>
                            @endif

                            @if ($report->is_delegated)
                                <span class="inline-flex rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                    Delegasi
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    Normal
                                </span>
                            @endif
                        </div>

                        @if ($report->is_delegated)
                            <div class="mt-2 space-y-1 text-xs text-slate-500">
                                <div>
                                    Pemilik Tupoksi:
                                    <span class="font-semibold text-slate-700">
                                        {{ $report->dutyOwnerEmployee?->name ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    Dilaporkan Oleh:
                                    <span class="font-semibold text-slate-700">
                                        {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Server
                        </div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $report->server->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Aplikasi
                        </div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $report->application->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Info Akses --}}
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-blue-700 shadow-sm">
                        ℹ️
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-blue-900">
                            Akses Kanit
                        </h3>
                        <p class="mt-1 text-sm leading-6 text-blue-700">
                            Halaman ini hanya menampilkan detail laporan pegawai yang berada dalam unit Kanit yang sedang login.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>