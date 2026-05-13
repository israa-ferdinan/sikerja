<div class="space-y-6">
    {{-- Header --}}
    <x-ui.page-header
        title="Dashboard Admin"
        subtitle="Kelola master data, user, dan konfigurasi aplikasi laporan kerja."
    >
        <x-slot:action>
            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
            >
                Kelola Master Data
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
                    Selamat datang, {{ auth()->user()->name ?? 'Admin' }}
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Gunakan dashboard ini untuk mengelola data dasar aplikasi, seperti unit,
                    pegawai, tupoksi, server, aplikasi, dan template laporan.
                </p>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <div class="font-bold">
                    Fokus Admin
                </div>
                <div class="mt-1 leading-5">
                    Pastikan master data selalu rapi agar input laporan pegawai lebih cepat dan minim kesalahan.
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Pegawai
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalEmployees) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    👥
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Jumlah pegawai yang terdaftar di aplikasi.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Unit
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalUnits) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                    🏢
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Unit kerja yang tersedia untuk relasi pegawai dan laporan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Master Tupoksi
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalDuties) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-2xl">
                    ✅
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Daftar tupoksi yang dipakai untuk pengisian laporan.
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

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-2xl">
                    📊
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Total laporan yang masuk pada bulan berjalan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Server
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalServers) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-2xl">
                    🖥️
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Jumlah server yang terdaftar pada master data.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Total Aplikasi
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalApplications) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                    📦
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Jumlah aplikasi yang tersedia dan bisa dipilih pada laporan.
            </p>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Template Laporan
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalTemplates) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-50 text-2xl">
                    📝
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Template yang dipakai untuk mempercepat input laporan.
            </p>
        </x-ui.card>
    </div>

    {{-- Modul Admin --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Modul Admin
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Akses cepat untuk mengelola master data utama aplikasi.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <a
                    href="{{ route('admin.master-data.unit.index') }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xl transition group-hover:bg-white">
                            🏢
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
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xl transition group-hover:bg-white">
                            👥
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
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-xl transition group-hover:bg-white">
                            ✅
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
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-xl transition group-hover:bg-white">
                            🖥️
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
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-xl transition group-hover:bg-white">
                            📦
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
                    class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-xl transition group-hover:bg-white">
                            📝
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
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        1
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pastikan data pegawai sudah terhubung ke unit yang benar.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        2
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Pastikan aplikasi sudah terhubung ke server yang sesuai.
                    </p>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        3
                    </div>
                    <p class="text-sm leading-6 text-slate-600">
                        Perbarui template laporan agar input pegawai tetap cepat dan konsisten.
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>