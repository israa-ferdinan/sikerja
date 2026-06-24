<div class="space-y-6">
    <x-dashboard-hero
        badge="Dashboard Pegawai"
        title="Selamat datang, {{ auth()->user()->name ?? 'Pegawai' }}"
        description="Input laporan kerja harian, pantau riwayat laporan pribadi, dan percepat pengisian menggunakan template atau clone laporan terakhir."
        icon="layout-dashboard"
    >
        <x-slot:meta>
            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                <x-icon name="calendar-days" class="h-3.5 w-3.5 text-cyan-200" />
                {{ now()->translatedFormat('F Y') }}
            </span>

            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                <x-icon name="file-text" class="h-3.5 w-3.5 text-cyan-200" />
                {{ number_format($monthlyReports) }} laporan bulan ini
            </span>
        </x-slot:meta>

        <x-slot:aside>
            <div class="rounded-3xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-100">
                            Status Laporan Hari Ini
                        </p>
                        <p class="mt-2 text-3xl font-bold text-white">
                            {{ number_format($todayReports) }}
                        </p>
                        <p class="mt-1 text-xs leading-5 text-slate-300">
                            laporan tercatat hari ini.
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-cyan-200">
                        <x-icon name="check-square" class="h-6 w-6" />
                    </div>
                </div>

                <a
                    href="{{ route('pegawai.reports.create') }}"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-white/20"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Input Laporan
                </a>
            </div>
        </x-slot:aside>
    </x-dashboard-hero>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Laporan Hari Ini
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($todayReports) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <x-icon name="file-text" class="h-6 w-6" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Jumlah laporan yang dibuat pada hari ini.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Laporan Bulan Ini
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($monthlyReports) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <x-icon name="bar-chart-3" class="h-6 w-6" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Total laporan pribadi pada bulan berjalan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Laporan Normal
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($normalReports) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <x-icon name="clipboard-list" class="h-6 w-6" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Laporan dari tupoksi pribadi bulan ini.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Laporan Delegasi
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($delegatedReports) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600">
                    <x-icon name="repeat-2" class="h-6 w-6" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Laporan dari tupoksi yang didelegasikan kepada Anda.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Foto Terunggah
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalPhotos) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <x-icon name="camera" class="h-6 w-6" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Jumlah dokumentasi foto pada laporan pribadi.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Laporan Terakhir
                    </p>

                    @if ($latestReport)
                        <p class="mt-2 text-lg font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($latestReport->report_date)->translatedFormat('d M Y') }}
                        </p>
                    @else
                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            -
                        </p>
                    @endif
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                    <x-icon name="clock" class="h-6 w-6" />
                </div>
            </div>

            <p class="mt-4 line-clamp-2 text-xs leading-5 text-slate-500">
                @if ($latestReport)
                    {{ $latestReport->duty?->name ?? 'Laporan kerja terakhir' }}

                    @if ($latestReport->is_delegated)
                        • Delegasi dari {{ $latestReport->dutyOwnerEmployee?->name ?? '-' }}
                    @else
                        • Normal
                    @endif
                @else
                    Belum ada laporan yang dibuat.
                @endif
            </p>
        </x-ui.card>
    </div>

    {{-- Quick Action --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2">
            <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Aksi Cepat
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Pilih menu yang paling sering digunakan untuk laporan kerja harian.
                    </p>
                </div>

                <div class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 sm:flex">
                    <x-icon name="list-checks" class="h-5 w-5" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                    href="{{ route('pegawai.reports.create') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-white">
                            <x-icon name="plus" class="h-5 w-5" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Input Laporan Baru
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Buat laporan kerja harian dengan template dan upload foto.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('pegawai.reports.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition group-hover:bg-white">
                            <x-icon name="history" class="h-5 w-5" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Riwayat Laporan Saya
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Lihat, edit, atau buka detail laporan yang sudah dibuat.
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Alur Singkat
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Cara cepat membuat laporan.
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                    <x-icon name="check-square" class="h-5 w-5" />
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        1
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pilih tanggal, tupoksi, server, dan aplikasi jika dibutuhkan.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        2
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Gunakan template atau clone laporan terakhir untuk mempercepat input.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        3
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Tambahkan foto dokumentasi lalu simpan laporan.
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
