<div class="space-y-6">
    {{-- Header --}}
    <x-ui.page-header
        title="Dashboard Pegawai"
        subtitle="Input laporan kerja harian dan pantau riwayat laporan pribadi."
    >
        <x-slot:action>
            <a
                href="{{ route('pegawai.reports.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
            >
                + Input Laporan
            </a>
        </x-slot:action>
    </x-ui.page-header>

    {{-- Welcome Card --}}
    <x-ui.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <x-ui.badge variant="primary">
                    SIPALING KERJA
                </x-ui.badge>

                <h2 class="mt-3 text-xl font-bold text-slate-900">
                    Selamat datang, {{ auth()->user()->name ?? 'Pegawai' }}
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Gunakan dashboard ini untuk membuat laporan kerja harian, melihat riwayat laporan,
                    dan mempercepat input menggunakan template atau clone laporan terakhir.
                </p>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <div class="font-bold">
                    Tips
                </div>
                <div class="mt-1 leading-5">
                    Isi laporan setelah pekerjaan selesai agar rekap bulanan lebih rapi.
                </div>
            </div>
        </div>
    </x-ui.card>

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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    📝
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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-2xl">
                    📊
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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    📄
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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-2xl">
                    🔁
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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-2xl">
                    📷
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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-2xl">
                    🕒
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
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Aksi Cepat
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Pilih menu yang paling sering digunakan untuk laporan kerja harian.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                    href="{{ route('pegawai.reports.create') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xl transition group-hover:bg-white">
                            +
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
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xl transition group-hover:bg-white">
                            📄
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
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Alur Singkat
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Cara cepat membuat laporan.
                </p>
            </div>

            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        1
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pilih tanggal, tupoksi, server, dan aplikasi.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        2
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Gunakan template atau clone laporan terakhir.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        3
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Tambahkan foto dokumentasi lalu simpan.
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>