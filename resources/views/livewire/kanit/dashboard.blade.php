<div class="space-y-6">
    {{-- Header --}}
    <x-ui.page-header
        title="Dashboard Kanit"
        subtitle="Pantau laporan kerja pegawai dalam unit dan akses rekap laporan bulanan."
    >
        <x-slot:action>
            <a
                href="{{ route('kanit.reports.monitoring') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
            >
                Buka Monitoring Unit
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
                    Selamat datang, {{ auth()->user()->name ?? 'Kanit' }}
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Gunakan dashboard ini untuk memantau aktivitas laporan pegawai dalam unit,
                    melihat rekap input laporan, dan mengakses monitoring laporan bulanan.
                </p>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <div class="font-bold">
                    Fokus Kanit
                </div>
                <div class="mt-1 leading-5">
                    Cek monitoring unit secara berkala untuk memastikan laporan pegawai lengkap.
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5">
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
                Jumlah laporan pegawai unit pada hari ini.
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
                Total laporan pegawai unit pada bulan berjalan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Pegawai Unit
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($unitEmployees) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                    👥
                </div>
            </div>

            <p class="mt-4 line-clamp-2 text-xs leading-5 text-slate-500">
                {{ $unitName ? 'Pegawai pada unit ' . $unitName . '.' : 'Jumlah pegawai dalam unit Kanit.' }}
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Sudah Input
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($employeesReportedThisMonth) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                    ✅
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Pegawai unik yang sudah input laporan bulan ini.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Belum Input
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($employeesNotReportedThisMonth) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-2xl">
                    ⚠️
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Pegawai unit yang belum input laporan bulan ini.
            </p>
        </x-ui.card>
    </div>

    {{-- Main Actions --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Monitoring Unit
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Akses cepat untuk melihat laporan pegawai berdasarkan bulan, tahun, pegawai, tupoksi, server, aplikasi, dan pencarian.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                    href="{{ route('kanit.reports.monitoring') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xl transition group-hover:bg-white">
                            📋
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Monitoring Laporan Unit
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Pantau laporan kerja pegawai dalam unit secara detail.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('kanit.reports.monitoring') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-xl transition group-hover:bg-white">
                            📥
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Rekap Bulanan
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Buka monitoring lalu gunakan tombol export bulanan.
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Alur Monitoring
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Cara cepat memantau laporan unit.
                </p>
            </div>

            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        1
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Buka halaman Monitoring Unit.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        2
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pilih filter bulan, pegawai, tupoksi, server, atau aplikasi.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        3
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Buka detail laporan atau export rekap bulanan.
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>