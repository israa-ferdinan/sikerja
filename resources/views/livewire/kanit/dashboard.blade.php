@php
    $reportCompletionPercentage = $unitEmployees > 0
        ? round(($employeesReportedThisMonth / $unitEmployees) * 100)
        : 0;

    $safeReportCompletionPercentage = min($reportCompletionPercentage, 100);
    $safeAverageTargetAchievement = min($averageTargetAchievement, 100);

    $visibleTargetAttentionItems = array_slice($targetAttentionItems, 0, 3);
    $hasMoreTargetAttentionItems = count($targetAttentionItems) > 3;
@endphp

<div class="space-y-6">
    {{-- Hero Dashboard --}}
    <x-dashboard-hero
        badge="Dashboard Kanit"
        title="Selamat datang, {{ auth()->user()->name ?? 'Kanit' }}"
        description="Pantau laporan kerja pegawai, kelengkapan input bulanan, dan progress target unit melalui satu dashboard monitoring yang ringkas dan mudah dipahami."
        icon="line-chart"
    >
        <x-slot:meta>
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                <x-icon name="building-2" class="h-3.5 w-3.5 text-cyan-200" />
                Unit: {{ $unitName ?? 'Unit Kerja' }}
            </div>

            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                <x-icon name="calendar-days" class="h-3.5 w-3.5 text-cyan-200" />
                Periode: {{ now()->translatedFormat('F Y') }}
            </div>
        </x-slot:meta>

        <x-slot:aside>
            <div class="rounded-3xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-cyan-100">
                            Kelengkapan Input Bulan Ini
                        </p>

                        <p class="mt-2 text-4xl font-bold text-white">
                            {{ number_format($reportCompletionPercentage) }}%
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-slate-900 shadow-sm">
                        <x-icon name="bar-chart-3" class="h-6 w-6" />
                    </div>
                </div>

                <div class="mt-5">
                    <div class="mb-2 flex items-center justify-between gap-4 text-xs font-semibold text-slate-300">
                        <span>{{ number_format($employeesReportedThisMonth) }} sudah input</span>
                        <span>{{ number_format($unitEmployees) }} pegawai</span>
                    </div>

                    <div class="h-2.5 overflow-hidden rounded-full bg-white/10">
                        <div
                            class="h-full rounded-full bg-cyan-300"
                            style="width: {{ $safeReportCompletionPercentage }}%"
                        ></div>
                    </div>
                </div>

                <p class="mt-3 text-xs leading-5 text-slate-300">
                    Membantu Kanit memantau kedisiplinan input laporan pegawai pada bulan berjalan.
                </p>

                <div class="mt-4">
                    <a
                        href="{{ route('kanit.reports.monitoring') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm transition hover:bg-cyan-50"
                    >
                        <x-icon name="list-checks" class="h-4 w-4" />
                        Buka Monitoring Unit
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
                    <p class="text-sm font-semibold text-slate-500">Laporan Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($todayReports) }}</p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Laporan pegawai unit pada hari ini.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                    <x-icon name="file-text" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Laporan Bulan Ini</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($monthlyReports) }}</p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Total laporan pada bulan berjalan.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <x-icon name="bar-chart-3" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Pegawai Sudah Input</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($employeesReportedThisMonth) }}</p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Pegawai unik yang sudah input bulan ini.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                    <x-icon name="user-check" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Pegawai Belum Input</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($employeesNotReportedThisMonth) }}</p>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        Pegawai yang belum memiliki laporan bulan ini.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-700">
                    <x-icon name="user-x" class="h-6 w-6" />
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Ringkasan Tambahan --}}
    <x-ui.card padding="p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Ringkasan Laporan Unit
                </h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Informasi pendukung untuk melihat komposisi laporan dan jumlah pegawai unit.
                </p>
            </div>

            <a
                href="{{ route('kanit.reports.monitoring') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-100"
            >
                <x-icon name="arrow-right" class="h-4 w-4" />
                Detail Monitoring
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-700 shadow-sm">
                        <x-icon name="users" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pegawai Unit</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($unitEmployees) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-cyan-700 shadow-sm">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Laporan Normal</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($normalReports) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-indigo-700 shadow-sm">
                        <x-icon name="repeat-2" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Laporan Delegasi</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($delegatedReports) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Target + Quick Actions --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        {{-- Progress Target Unit --}}
        <x-ui.card class="xl:col-span-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                            <x-icon name="target" class="h-5 w-5" />
                        </div>
                        <h2 class="text-base font-bold text-slate-900">
                            Progress Target Unit
                        </h2>
                    </div>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                        Ringkasan capaian target tahunan berdasarkan metode capaian yang sudah ditentukan.
                    </p>
                </div>

                <a
                    href="{{ route('kanit.unit-targets.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700"
                >
                    Lihat Semua Target
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($activeTargets) }}</p>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Tercapai</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-800">{{ number_format($achievedTargets) }}</p>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Berjalan</p>
                    <p class="mt-2 text-2xl font-bold text-amber-800">{{ number_format($runningTargets) }}</p>
                </div>

                <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Rata-rata</p>
                    <p class="mt-2 text-2xl font-bold text-cyan-800">{{ number_format($averageTargetAchievement, 2) }}%</p>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                        <div
                            class="h-full rounded-full bg-cyan-600"
                            style="width: {{ $safeAverageTargetAchievement }}%"
                        ></div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="mb-3 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">
                            Top 3 Target Perlu Perhatian
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Target dengan capaian terendah agar mudah diprioritaskan.
                        </p>
                    </div>
                </div>

                @if (count($visibleTargetAttentionItems) > 0)
                    <div class="space-y-3">
                        @foreach ($visibleTargetAttentionItems as $targetItem)
                            @php
                                $achievementPercentage = min($targetItem['achievement_percentage'], 100);
                                $statusLabel = $targetItem['status_label'] ?? 'Berjalan';

                                $statusClass = 'bg-amber-50 text-amber-700 ring-amber-200';

                                if (str($statusLabel)->lower()->contains('tercapai')) {
                                    $statusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-200';
                                } elseif (str($statusLabel)->lower()->contains('belum')) {
                                    $statusClass = 'bg-slate-50 text-slate-700 ring-slate-200';
                                } elseif (str($statusLabel)->lower()->contains('risiko') || str($statusLabel)->lower()->contains('perhatian')) {
                                    $statusClass = 'bg-rose-50 text-rose-700 ring-rose-200';
                                }
                            @endphp

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-center">
                                    <div class="min-w-0 lg:col-span-7">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="max-w-full truncate text-sm font-bold text-slate-900">
                                                {{ $targetItem['title'] }}
                                            </h4>

                                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>

                                        <span class="inline-flex shrink-0 rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">
                                            {{ $targetItem['achievement_method_label'] ?? 'Otomatis dari Laporan Harian' }}
                                        </span>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Periode: {{ $targetItem['period_label'] }}
                                        </p>

                                        @if (in_array($targetItem['achievement_method'] ?? 'auto_report', ['manual_progress', 'manual_status'], true))
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                @if (($targetItem['achievement_method'] ?? 'auto_report') === 'manual_progress')
                                                    Progress diperbarui manual dalam bentuk persen.
                                                @else
                                                    Progress diperbarui manual berdasarkan status pekerjaan.
                                                @endif

                                                @if (! empty($targetItem['manual_progress_updated_at']))
                                                    Update terakhir: {{ $targetItem['manual_progress_updated_at'] }}.
                                                @endif
                                            </p>
                                        @endif

                                    </div>

                                    <div class="lg:col-span-5">
                                        <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                                            <span class="font-semibold text-slate-600">
                                                {{ number_format($targetItem['achievement_count'], 0, ',', '.') }}
                                                /
                                                {{ $targetItem['target_summary'] }}
                                            </span>

                                            <span class="font-bold text-slate-900">
                                                {{ number_format($targetItem['achievement_percentage'], 2) }}%
                                            </span>
                                        </div>

                                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                            <div
                                                class="h-full rounded-full bg-cyan-600"
                                                style="width: {{ $achievementPercentage }}%"
                                            ></div>
                                        </div>

                                        <p class="mt-1 text-[11px] text-slate-500">
                                            Sisa target:
                                            {{ number_format($targetItem['remaining_target'], 0, ',', '.') }}
                                            {{ $targetItem['target_unit'] ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($hasMoreTargetAttentionItems)
                        <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                            <p class="text-xs font-semibold text-slate-600">
                                Masih ada target lain yang perlu dipantau. Buka halaman Target Unit untuk melihat daftar lengkap.
                            </p>
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                        @if ($activeTargets > 0)
                            <p class="text-sm font-semibold text-emerald-700">
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

        {{-- Quick Actions --}}
        <div class="space-y-4 xl:col-span-4">
            <x-ui.card padding="p-5">
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Akses Cepat
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Menu utama untuk monitoring, rekap laporan, dan target unit.
                    </p>
                </div>

                <div class="space-y-3">
                    <a
                        href="{{ route('kanit.reports.monitoring') }}"
                        class="group flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-cyan-200 hover:bg-cyan-50"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700 transition group-hover:bg-white">
                            <x-icon name="list-checks" class="h-5 w-5" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Monitoring Laporan
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Pantau laporan pegawai berdasarkan periode dan status.
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('reports.export.monthly') }}"
                        class="group flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition group-hover:bg-white">
                            <x-icon name="file-spreadsheet" class="h-5 w-5" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Rekap dan Export
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Buka halaman export laporan bulanan unit.
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('kanit.unit-targets.index') }}"
                        class="group flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-amber-200 hover:bg-amber-50"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 transition group-hover:bg-white">
                            <x-icon name="target" class="h-5 w-5" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Target Unit
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Lihat progress target tahunan.
                            </p>
                        </div>
                    </a>
                </div>
            </x-ui.card>

            <x-ui.card padding="p-5">
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Alur Monitoring
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan proses kerja Kanit.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-white">
                            <x-icon name="edit-3" class="h-4 w-4" />
                        </div>
                        <p class="text-sm leading-6 text-slate-600">
                            Pegawai mengisi laporan kerja harian.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-white">
                            <x-icon name="clipboard-check" class="h-4 w-4" />
                        </div>
                        <p class="text-sm leading-6 text-slate-600">
                            Kanit memantau kelengkapan laporan unit.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-white">
                            <x-icon name="download" class="h-4 w-4" />
                        </div>
                        <p class="text-sm leading-6 text-slate-600">
                            Data digunakan untuk rekap, evaluasi, dan export laporan.
                        </p>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>