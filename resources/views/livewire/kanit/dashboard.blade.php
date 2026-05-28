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
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
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
                Laporan tupoksi pribadi pegawai unit bulan ini.
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
                Laporan unit yang berasal dari tupoksi delegasi.
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

        {{-- Progress Target Unit --}}
        <x-ui.card>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Progress Target Unit
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan capaian target unit tahun berjalan berdasarkan laporan harian yang cocok.
                    </p>
                </div>

                <a
                    href="{{ route('kanit.unit-targets.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                >
                    Lihat Target Unit
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-500">
                        Target Aktif
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ number_format($activeTargets) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Target aktif pada tahun {{ now()->year }}.
                    </p>
                </div>

                <div class="rounded-2xl border border-green-200 bg-green-50 p-4">
                    <p class="text-sm font-semibold text-green-700">
                        Tercapai
                    </p>
                    <p class="mt-2 text-2xl font-bold text-green-800">
                        {{ number_format($achievedTargets) }}
                    </p>
                    <p class="mt-1 text-xs text-green-700">
                        Target dengan capaian 100% atau lebih.
                    </p>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-700">
                        Berjalan
                    </p>
                    <p class="mt-2 text-2xl font-bold text-amber-800">
                        {{ number_format($runningTargets) }}
                    </p>
                    <p class="mt-1 text-xs text-amber-700">
                        Target yang masih perlu dikejar.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-700">
                        Rata-rata Capaian
                    </p>
                    <p class="mt-2 text-2xl font-bold text-blue-800">
                        {{ number_format($averageTargetAchievement, 2) }}%
                    </p>
                    <p class="mt-1 text-xs text-blue-700">
                        Rata-rata progress seluruh target aktif.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <div class="mb-3 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">
                            Target Perlu Perhatian
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Diurutkan dari capaian terendah.
                        </p>
                    </div>
                </div>

                @if (count($targetAttentionItems) > 0)
                    <div class="space-y-3">
                        @foreach ($targetAttentionItems as $targetItem)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="truncate text-sm font-bold text-slate-900">
                                                {{ $targetItem['title'] }}
                                            </h4>

                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $targetItem['status_badge_class'] }}">
                                                {{ $targetItem['status_label'] }}
                                            </span>
                                        </div>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Periode: {{ $targetItem['period_label'] }}
                                        </p>
                                    </div>

                                    <div class="w-full lg:w-64">
                                        <div class="mb-1 flex items-center justify-between text-xs">
                                            <span class="font-semibold text-slate-600">
                                                {{ number_format($targetItem['achievement_count']) }}
                                                /
                                                {{ number_format($targetItem['target_quantity']) }}
                                            </span>
                                            <span class="font-bold text-slate-900">
                                                {{ number_format($targetItem['achievement_percentage'], 2) }}%
                                            </span>
                                        </div>

                                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                            <div
                                                class="h-full rounded-full bg-blue-600"
                                                style="width: {{ min($targetItem['achievement_percentage'], 100) }}%"
                                            ></div>
                                        </div>

                                        <p class="mt-1 text-[11px] text-slate-500">
                                            Sisa target: {{ number_format($targetItem['remaining_target']) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                        @if ($activeTargets > 0)
                            <p class="text-sm font-semibold text-green-700">
                                Semua target aktif sudah tercapai atau tidak ada target yang perlu perhatian.
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Tetap pantau target unit secara berkala melalui menu Target Unit.
                            </p>
                        @else
                            <p class="text-sm font-semibold text-slate-700">
                                Belum ada target aktif tahun ini.
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Target unit yang aktif akan tampil di dashboard Kanit.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </x-ui.card>

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