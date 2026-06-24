<div class="space-y-6">
    <x-page-hero
        badge="Laporan Capaian Target"
        title="Laporan capaian target 3 bulanan"
        description="Preview capaian target tahunan berdasarkan periode triwulan, metode capaian, laporan harian, dan progress manual."
        icon="file-spreadsheet"
    >
        <x-slot name="aside">
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200 ring-1 ring-white/10">
                    {{ $quarterLabel }}
                </span>
                <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200 ring-1 ring-white/10">
                    {{ $periodLabel }}
                </span>
            </div>
        </x-slot>
    </x-page-hero>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-4 flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                <x-icon name="filter" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Filter Laporan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Target tetap tahunan. Triwulan hanya digunakan sebagai periode baca capaian laporan.
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-{{ $isAdmin ? '4' : '3' }}">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Tahun
                </label>

                <input
                    type="number"
                    wire:model.live.debounce.500ms="year"
                    min="2020"
                    max="2100"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Periode
                </label>

                <select
                    wire:model.live="quarter"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                >
                    <option value="1">Triwulan I</option>
                    <option value="2">Triwulan II</option>
                    <option value="3">Triwulan III</option>
                    <option value="4">Triwulan IV</option>
                </select>
            </div>

            @if ($isAdmin)
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Unit
                    </label>

                    <select
                        wire:model.live="unit_id"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                    >
                        <option value="">Semua Unit</option>

                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Unit
                    </label>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                        {{ $selectedUnitName }}
                    </div>
                </div>
            @endif

            <div class="flex items-end">
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    <x-icon name="rotate-ccw" class="h-4 w-4" />
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Preview Laporan Capaian Target
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $selectedUnitName }} • {{ $quarterLabel }} • {{ $periodLabel }}
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        {{ $rows->count() }} target
                    </div>

                    <button
                        type="button"
                        wire:click="downloadExcel"
                        wire:loading.attr="disabled"
                        wire:target="downloadExcel"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <x-icon name="download" class="h-4 w-4" />

                        <span wire:loading.remove wire:target="downloadExcel">
                            Download Excel
                        </span>

                        <span wire:loading wire:target="downloadExcel">
                            Menyiapkan...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1400px] divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            No.
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Unit
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Sasaran Mutu / Klasifikasi
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Kegiatan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Metode
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Target Tahunan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Periode Ini
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Kumulatif
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Selisih
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            %
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Catatan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Tindakan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Bukti Dokumen
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Pegawai
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Monitoring
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($rows as $row)
                        <tr class="align-top hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-700">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $row['unit_name'] }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $row['sasaran_mutu'] }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                <div class="font-semibold text-slate-900">
                                    {{ $row['nama_target'] }}
                                </div>

                                @if (! empty($row['deskripsi_target']))
                                    <div class="mt-1 whitespace-pre-line text-xs leading-5 text-slate-500">
                                        {{ $row['deskripsi_target'] }}
                                    </div>
                                @endif

                                <div class="mt-2 text-xs text-slate-400">
                                    Objek: {{ $row['objek_pekerjaan'] }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                    {{ $row['achievement_method_label'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm font-semibold text-slate-700">
                                {{ $row['target_tahunan'] }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $row['capaian_periode'] }}
                            </td>

                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ $row['capaian_kumulatif'] }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $row['selisih'] }}
                            </td>

                            <td class="px-4 py-3 text-sm font-bold text-slate-900">
                                {{ $row['persentase_kumulatif_label'] }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold
                                    @if (($row['persentase_kumulatif'] ?? 0) >= 100)
                                        bg-green-100 text-green-700
                                    @elseif (($row['persentase_kumulatif'] ?? 0) >= 75)
                                        bg-blue-100 text-blue-700
                                    @elseif (($row['persentase_kumulatif'] ?? 0) > 0)
                                        bg-amber-100 text-amber-700
                                    @else
                                        bg-gray-100 text-gray-600
                                    @endif
                                ">
                                    {{ $row['status'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3 whitespace-pre-line text-sm text-slate-700">
                                {{ $row['catatan'] }}
                            </td>

                            <td class="px-4 py-3 whitespace-pre-line text-sm text-slate-700">
                                {{ $row['tindakan_perbaikan'] }}
                            </td>

                            <td class="px-4 py-3 whitespace-pre-line text-sm text-slate-700">
                                {{ $row['bukti_dokumen'] }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $row['pegawai_pelaksana'] }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $row['monitoring'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-4 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <x-icon name="file-search" class="h-6 w-6" />
                                </div>

                                <h3 class="mt-3 text-sm font-bold text-slate-900">
                                    Belum ada target tahunan
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Target tahunan aktif untuk filter ini belum tersedia.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 text-xs leading-5 text-slate-500">
            <p>
                Catatan: capaian periode ini membaca data dalam rentang triwulan, sedangkan capaian kumulatif membaca data dari 1 Januari sampai akhir triwulan terpilih.
            </p>
        </div>
    </div>
</div>