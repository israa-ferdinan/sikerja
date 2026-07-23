<div class="relative z-10 p-4 sm:p-6">
    <div class="mx-auto max-w-6xl space-y-6">
        <x-page-hero
            badge="Detail Laporan"
            title="{{ $report->title }}"
            description="{{ \Illuminate\Support\Str::limit($report->description, 180) }}"
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
                        </div>

                        @if($report->operationalTicket)
                            <span class="inline-flex rounded-full border border-amber-300/20 bg-amber-400/15 px-3 py-1 text-xs font-semibold text-amber-100">
                                Tiket Operasional
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('pegawai.reports.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-white/15 focus:outline-none focus:ring-4 focus:ring-cyan-300/20"
                        >
                            <x-icon name="arrow-left" class="h-4 w-4" />
                            Kembali
                        </a>

                        @if($report->operationalTicket)
                            <a
                                href="{{ route('operations.tickets.show', $report->operationalTicket) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-300/20 bg-amber-400/15 px-3 py-2 text-xs font-semibold text-amber-100 shadow-sm transition hover:bg-amber-400/20"
                            >
                                <x-icon name="ticket" class="h-4 w-4" />
                                Lihat Tiket Sumber
                            </a>
                        @endif

                        @if (! $isLocked)
                            <a
                                href="{{ route('pegawai.reports.edit', $report) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-300/20 bg-cyan-400/15 px-3 py-2 text-xs font-semibold text-cyan-100 shadow-sm transition hover:bg-cyan-400/20 focus:outline-none focus:ring-4 focus:ring-cyan-300/20"
                            >
                                <x-icon name="edit-3" class="h-4 w-4" />
                                Edit
                            </a>

                            @if(! $report->operational_ticket_id)
                                <button
                                    type="button"
                                    x-data
                                    x-on:click="$dispatch('open-confirm-modal', {
                                        title: 'Hapus laporan?',
                                        message: 'Laporan ini beserta foto terkait akan dihapus. Tindakan ini tidak bisa dibatalkan.',
                                        confirmText: 'Ya, Hapus',
                                        cancelText: 'Batal',
                                        variant: 'danger',
                                        onConfirm: () => $wire.delete()
                                    })"
                                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-rose-400/40 bg-rose-500/10 px-5 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/20 hover:text-white"
                                >
                                    <x-icon name="trash-2" class="h-4 w-4" />
                                    <span>Hapus</span>
                                </button>
                            @endif
                        @else
                            <span class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-emerald-300/20 bg-emerald-400/15 px-5 text-sm font-semibold text-emerald-100">
                                <x-icon name="lock" class="h-4 w-4" />
                                Sudah Final
                            </span>
                        @endif
                    </div>
                </div>
            </x-slot:aside>
        </x-page-hero>

        @if (session()->has('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($isLocked)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                Laporan ini berada pada periode yang sudah difinalisasi oleh Kanit. Data laporan dan foto tidak dapat diubah.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <x-ui.card>
                    <div class="mb-5 flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                            <x-icon name="file-text" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-900">Deskripsi Kegiatan</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Uraian pekerjaan yang dilakukan pada laporan harian.
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="whitespace-pre-line text-sm leading-7 text-slate-700">
                            {{ $report->description }}
                        </div>
                    </div>
                </x-ui.card>

                @if ($report->notes)
                    <x-ui.card>
                        <div class="mb-5 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                                <x-icon name="message-square" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-bold text-slate-900">Catatan / Hasil Pekerjaan</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Catatan tambahan, hasil pekerjaan, kendala, atau tindak lanjut.
                                </p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                            <div class="whitespace-pre-line text-sm leading-7 text-amber-900">
                                {{ $report->notes }}
                            </div>
                        </div>
                    </x-ui.card>
                @endif

                <x-ui.card>
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                                <x-icon name="image" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-bold text-slate-900">Foto Dokumentasi</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Bukti kegiatan yang dilampirkan bersama laporan.
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                            {{ $report->photos->count() }} foto
                        </span>
                    </div>

                    @if ($report->photos->count() > 0)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($report->photos as $index => $photo)
                                <a
                                    href="{{ asset('storage/' . $photo->file_path) }}"
                                    target="_blank"
                                    class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-cyan-200 hover:shadow-md"
                                >
                                    <div class="relative">
                                        <img
                                            src="{{ asset('storage/' . $photo->file_path) }}"
                                            class="h-44 w-full object-cover transition duration-300 group-hover:scale-105"
                                            alt="Foto laporan {{ $index + 1 }}"
                                        >

                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 to-transparent px-4 pb-3 pt-10">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-xs font-bold text-white">Foto {{ $index + 1 }}</span>
                                                <span class="inline-flex rounded-full bg-white/15 px-2 py-1 text-[11px] font-semibold text-white backdrop-blur">Lihat</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            icon="image"
                            title="Tidak ada foto dokumentasi"
                            message="Laporan ini belum memiliki foto pendukung."
                        />
                    @endif
                </x-ui.card>
            </div>

            <div class="space-y-6 xl:col-span-4">
                {{-- Tiket sumber --}}
                @if($report->operationalTicket)
                    <x-ui.card>
                        <div class="mb-5 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                                <x-icon name="ticket" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-bold text-slate-900">
                                    Tiket Operasional
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Laporan ini dibuat dari aktivitas penanganan tiket operasional.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-amber-700">
                                    Kode Tiket
                                </p>

                                <p class="mt-1 break-words text-sm font-bold text-amber-950">
                                    {{ $report->operationalTicket->ticket_code }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Status Tiket
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $report->operationalTicket->status_label }}
                                </p>
                            </div>

                            <a
                                href="{{ route('operations.tickets.show', $report->operationalTicket) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-700"
                            >
                                <x-icon name="arrow-up-right" class="h-4 w-4" />
                                Buka Detail Tiket
                            </a>
                        </div>
                    </x-ui.card>
                @endif

                {{-- Informasi laporan --}}
                <x-ui.card>
                    <div class="mb-5 flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                            <x-icon name="clock" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Informasi Laporan
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Ringkasan data utama laporan kerja.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $report->report_date?->translatedFormat('d F Y') ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Unit
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $report->unit?->name ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Pelapor
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $report->reportedByEmployee?->name
                                    ?? $report->employee?->name
                                    ?? '-' }}
                            </p>
                        </div>
                    </div>
                </x-ui.card>

                {{-- Tupoksi dan objek --}}
                <x-ui.card>
                    <div class="mb-5 flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                            <x-icon name="check-square" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Tupoksi & Objek
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Informasi tupoksi yang digunakan pada laporan.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                @if($report->is_delegated)
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                        Delegasi
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-200">
                                        Normal
                                    </span>
                                @endif

                                @if($report->duty?->classification)
                                    <span class="inline-flex rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-cyan-100">
                                        {{ $report->duty->classification->name }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm font-bold leading-6 text-slate-900">
                                {{ $report->duty?->name ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Jenis Objek
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $report->duty?->object_type_label ?? '-' }}
                            </p>
                        </div>

                        @if($report->server)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Server
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $report->server->name }}
                                </p>
                            </div>
                        @endif

                        @if($report->application)
                            <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">
                                    Aplikasi
                                </p>

                                <p class="mt-1 text-sm font-semibold text-cyan-950">
                                    {{ $report->application->name }}
                                </p>
                            </div>
                        @endif

                        @if(!$report->server && !$report->application)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Objek Detail
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    Tidak menggunakan server atau aplikasi khusus.
                                </p>
                            </div>
                        @endif

                        @if($report->is_delegated)
                            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-indigo-700">
                                    Informasi Delegasi
                                </p>

                                <div class="mt-2 space-y-2 text-sm leading-6 text-indigo-900">
                                    <div>
                                        Pemilik Tupoksi:
                                        <span class="font-bold">
                                            {{ $report->dutyOwnerEmployee?->name ?? '-' }}
                                        </span>
                                    </div>

                                    <div>
                                        Dilaporkan Oleh:
                                        <span class="font-bold">
                                            {{ $report->reportedByEmployee?->name ?? '-' }}
                                        </span>
                                    </div>

                                    @if($report->delegation)
                                        <div>
                                            Periode:
                                            <span class="font-bold">
                                                {{ $report->delegation->start_date?->format('d/m/Y') ?? '-' }}
                                                s.d.
                                                {{ $report->delegation->end_date?->format('d/m/Y') ?? 'Tidak ditentukan' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</div>
