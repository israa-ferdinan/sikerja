<div class="space-y-6">
    <x-dashboard-hero
        badge="Dashboard Admin"
        title="Selamat datang, {{ auth()->user()->name ?? 'Admin' }}"
        description="Control center untuk memantau data utama aplikasi, laporan bulan berjalan, master data, dan konfigurasi pendukung SIPALING KERJA."
        icon="layout-dashboard"
    >
        <x-slot:meta>
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                <x-icon name="calendar-days" class="h-3.5 w-3.5 text-cyan-200" />
                Bulan berjalan
            </div>

            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                <x-icon name="database" class="h-3.5 w-3.5 text-cyan-200" />
                Master data & konfigurasi
            </div>
        </x-slot:meta>

        <x-slot:aside>
            <div class="rounded-3xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-slate-900 shadow-sm">
                        <x-icon name="shield-check" class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-sm font-bold text-white">
                            Fokus Admin
                        </p>
                        <p class="mt-1 text-xs leading-5 text-slate-300">
                            Pastikan master data rapi agar input laporan pegawai tetap cepat dan minim kesalahan.
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    <a
                        href="{{ route('admin.master-data.pegawai.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-3 py-2.5 text-xs font-bold text-slate-900 shadow-sm transition hover:bg-cyan-50"
                    >
                        <x-icon name="users" class="h-4 w-4" />
                        Master Pegawai
                    </a>

                    <a
                        href="{{ route('admin.master-data.tupoksi.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-3 py-2.5 text-xs font-bold text-slate-900 shadow-sm transition hover:bg-cyan-50"
                    >
                        <x-icon name="list-checks" class="h-4 w-4" />
                        Master Tupoksi
                    </a>
                </div>
            </div>
        </x-slot:aside>
    </x-dashboard-hero>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Pegawai
                    </p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($totalEmployees) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50">
                    <x-icon name="users" class="h-6 w-6 text-blue-600" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Pegawai yang terdaftar dan menjadi dasar relasi laporan kerja.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Unit
                    </p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($totalUnits) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100">
                    <x-icon name="building-2" class="h-6 w-6 text-slate-600" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Unit kerja aktif untuk pengelompokan pegawai dan monitoring laporan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Laporan Bulan Ini
                    </p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($monthlyReports) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50">
                    <x-icon name="bar-chart-3" class="h-6 w-6 text-amber-600" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Total laporan kerja yang masuk pada bulan berjalan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Master Tupoksi
                    </p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($totalDuties) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50">
                    <x-icon name="list-checks" class="h-6 w-6 text-emerald-600" />
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Tupoksi yang tersedia sebagai pilihan input laporan pegawai.
            </p>
        </x-ui.card>
    </div>

    {{-- Ringkasan Data Pendukung --}}
    <x-ui.card>
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Ringkasan Data Pendukung
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Gambaran cepat laporan dan master data teknis yang mendukung input laporan harian.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Laporan Normal
                        </p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($normalReports) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-purple-600 shadow-sm">
                        <x-icon name="repeat-2" class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Laporan Delegasi
                        </p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($delegatedReports) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-violet-600 shadow-sm">
                        <x-icon name="server" class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Total Server
                        </p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($totalServers) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <x-icon name="package" class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Total Aplikasi
                        </p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($totalApplications) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-cyan-600 shadow-sm">
                        <x-icon name="file-pen-line" class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Template Laporan
                        </p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($totalTemplates) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Modul Admin --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Modul Admin
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Akses cepat untuk mengelola master data utama aplikasi.
                    </p>
                </div>

                <div class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 sm:inline-flex">
                    Master data
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <a
                    href="{{ route('admin.master-data.unit.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 transition group-hover:bg-white">
                            <x-icon name="building-2" class="h-5 w-5 text-blue-600" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Master Unit
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Kelola unit kerja pegawai.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('admin.master-data.pegawai.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 transition group-hover:bg-white">
                            <x-icon name="users" class="h-5 w-5 text-slate-700" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Master Pegawai
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Kelola data pegawai dan unitnya.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('admin.master-data.tupoksi.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 transition group-hover:bg-white">
                            <x-icon name="list-checks" class="h-5 w-5 text-emerald-600" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Master Tupoksi
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Kelola daftar tupoksi pekerjaan.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('admin.master-data.server.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 transition group-hover:bg-white">
                            <x-icon name="server" class="h-5 w-5 text-violet-600" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Master Server
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Kelola data server aplikasi.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('admin.master-data.aplikasi.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 transition group-hover:bg-white">
                            <x-icon name="package" class="h-5 w-5 text-indigo-600" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Master Aplikasi
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Kelola aplikasi dan relasi server.
                            </p>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ route('admin.master-data.report-template.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 transition group-hover:bg-white">
                            <x-icon name="file-pen-line" class="h-5 w-5 text-cyan-600" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Template Laporan
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Kelola template input laporan cepat.
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Checklist Admin
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Data yang sebaiknya dicek berkala.
                </p>
            </div>

            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <x-icon name="users" class="h-4 w-4" />
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pastikan data pegawai sudah terhubung ke unit yang benar.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <x-icon name="server-cog" class="h-4 w-4" />
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pastikan aplikasi sudah terhubung ke server yang sesuai.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-600">
                        <x-icon name="file-pen-line" class="h-4 w-4" />
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Perbarui template laporan agar input pegawai tetap cepat dan konsisten.
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>