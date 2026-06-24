<div class="space-y-6">
    <x-page-hero
        badge="Detail Laporan Pegawai"
        title="{{ $report->title }}"
        description="Detail laporan kerja harian pegawai dalam unit Anda."
        icon="file-text"
    >
        <x-slot:aside>
            <div class="space-y-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow-sm backdrop-blur">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-cyan-100">Pelapor</p>
                            <p class="mt-2 truncate text-xl font-bold text-white">
                                {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                            </p>
                            <p class="mt-1 text-xs leading-5 text-slate-300">
                                {{ $report->reportedByEmployee?->jobPosition?->name ?? $report->employee?->jobPosition?->name ?? '-' }}
                            </p>
                        </div>

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-200">
                            <x-icon name="users" class="h-6 w-6" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                            {{ $report->is_delegated ? 'Delegasi' : 'Normal' }}
                        </span>
                        <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                            {{ $report->photos->count() }} foto
                        </span>
                        <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                            {{ optional($report->report_date)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <a
                    href="{{ route('kanit.reports.monitoring') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-white/15 focus:outline-none focus:ring-4 focus:ring-cyan-300/20"
                >
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    Kembali ke Monitoring
                </a>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-ui.card>
                <div class="mb-5 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">Detail Pekerjaan</h2>
                        <p class="mt-1 text-sm text-slate-500">Uraian kegiatan yang dilaporkan oleh pegawai.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <h3 class="mb-2 text-sm font-bold text-slate-800">Deskripsi Pekerjaan</h3>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-700">
                            {!! nl2br(e($report->description ?? '-')) !!}
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-2 text-sm font-bold text-slate-800">Hasil / Keterangan</h3>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-700">
                            {!! nl2br(e($report->result ?? $report->notes ?? '-')) !!}
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                            <x-icon name="image" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-900">Foto Dokumentasi</h2>
                            <p class="mt-1 text-sm text-slate-500">Dokumentasi foto yang diunggah pegawai pada laporan ini.</p>
                        </div>
                    </div>

                    <x-ui.badge variant="{{ $report->photos->count() > 0 ? 'success' : 'neutral' }}">
                        {{ $report->photos->count() }} foto
                    </x-ui.badge>
                </div>

                @if($report->photos->count())
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach($report->photos as $index => $photo)
                            <a
                                href="{{ route('reports.photos.show', $photo) }}"
                                target="_blank"
                                class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm"
                            >
                                <img
                                    src="{{ route('reports.photos.show', $photo) }}"
                                    alt="Foto laporan {{ $index + 1 }}"
                                    class="h-36 w-full object-cover transition duration-200 group-hover:scale-105"
                                >

                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                    <span class="text-xs font-semibold text-white">Foto {{ $index + 1 }}</span>
                                </div>

                                <div class="absolute right-2 top-2 rounded-full bg-white/90 px-2 py-1 text-[11px] font-bold text-slate-700 opacity-0 shadow-sm transition group-hover:opacity-100">
                                    Lihat
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        icon="image"
                        title="Tidak ada foto"
                        message="Pegawai belum mengunggah foto dokumentasi untuk laporan ini."
                    />
                @endif
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card>
                <div class="mb-5 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <x-icon name="users" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">Informasi Pegawai</h2>
                        <p class="mt-1 text-sm text-slate-500">Identitas pegawai pembuat laporan.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Nama Pegawai</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Jabatan</div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $report->reportedByEmployee?->jobPosition?->name ?? $report->employee?->jobPosition?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Unit</div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $report->employee->unit->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="mb-5 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <x-icon name="clock" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">Informasi Laporan</h2>
                        <p class="mt-1 text-sm text-slate-500">Metadata laporan kerja harian.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Tanggal Laporan</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">{{ optional($report->report_date)->format('d/m/Y') }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Tupoksi</div>
                        <div class="mt-1">
                            @if($report->duty)
                                <x-ui.badge variant="primary">{{ $report->duty->name }}</x-ui.badge>
                            @else
                                <span class="text-sm text-slate-500">-</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Server</div>
                        <div class="mt-1 text-sm text-slate-700">{{ $report->server->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Aplikasi</div>
                        <div class="mt-1 text-sm text-slate-700">{{ $report->application->name ?? '-' }}</div>
                    </div>
                </div>
            </x-ui.card>

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-blue-700 shadow-sm">
                        <x-icon name="info" class="h-5 w-5" />
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-blue-900">Akses Kanit</h3>
                        <p class="mt-1 text-sm leading-6 text-blue-700">
                            Halaman ini hanya menampilkan detail laporan pegawai yang berada dalam unit Kanit yang sedang login.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
