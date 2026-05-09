<div class="p-6">
    <div class="max-w-7xl mx-auto">

        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Riwayat Laporan Saya
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar laporan kerja harian yang sudah Anda input.
                </p>
            </div>

            <a
                href="{{ route('pegawai.reports.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700"
            >
                + Input Laporan
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-5">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-6">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Cari Laporan
                        </label>
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari judul, deskripsi, atau catatan..."
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Bulan
                        </label>
                        <select
                            wire:model.live="month"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Tahun
                        </label>
                        <div class="flex gap-2">
                            <select
                                wire:model.live="year"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>

                            <button
                                type="button"
                                wire:click="resetFilter"
                                class="px-3 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-y-3">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[110px]">
                                Tanggal
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Laporan
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[240px]">
                                Kategori
                            </th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-[120px]">
                                Foto
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-[90px]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-4 py-3 align-top bg-white border-y border-l border-gray-200 rounded-l-xl">
                                    <div class="inline-flex flex-col items-center justify-center rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 min-w-[72px]">
                                        <div class="text-lg font-bold text-blue-700 leading-none">
                                            {{ $report->report_date?->format('d') }}
                                        </div>
                                        <div class="text-[11px] font-medium text-blue-600 mt-1">
                                            {{ $report->report_date?->format('M Y') }}
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top bg-white border-y border-gray-200 text-center">
                                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <a
                                                    href="{{ route('pegawai.reports.show', $report) }}"
                                                    class="block text-sm font-bold text-gray-900 hover:text-blue-700"
                                                >
                                                    {{ $report->title }}
                                                </a>

                                                <p class="mt-1 text-sm text-gray-600 leading-relaxed line-clamp-2">
                                                    {{ $report->description }}
                                                </p>
                                            </div>

                                            <span class="shrink-0 inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-semibold text-green-700">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </div>

                                        <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                                            <span class="inline-flex items-center rounded-full bg-white border border-gray-200 px-2 py-1 text-gray-500">
                                                Dibuat: {{ $report->created_at?->format('d M Y, H:i') }}
                                            </span>

                                            @if ($report->notes)
                                                <span class="inline-flex items-center rounded-full bg-yellow-50 border border-yellow-200 px-2 py-1 text-yellow-700">
                                                    Ada catatan
                                                </span>
                                            @endif

                                            @if ($report->photos->count() > 0)
                                                <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-200 px-2 py-1 text-blue-700">
                                                    {{ $report->photos->count() }} foto
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top bg-white border-y border-gray-200">
                                    <div class="space-y-2 text-xs">
                                        <div>
                                            <div class="text-gray-400">Tupoksi</div>
                                            <div class="font-medium text-gray-700">
                                                {{ $report->duty->name ?? '-' }}
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-gray-400">Server</div>
                                            <div class="font-medium text-gray-700">
                                                {{ $report->server->name ?? '-' }}
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-gray-400">Aplikasi</div>
                                            <div class="font-medium text-gray-700">
                                                {{ $report->application->name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top bg-white border-y border-gray-200">
                                    @if ($report->photos->count() > 0)
                                        <div class="inline-flex flex-col items-center rounded-xl border border-gray-200 bg-gray-50 p-2 shadow-sm">
                                            <div class="flex items-center gap-1">
                                                @foreach ($report->photos->take(3) as $photo)
                                                    <a
                                                        href="{{ asset('storage/' . $photo->file_path) }}"
                                                        target="_blank"
                                                        class="block rounded-md border border-white bg-white p-0.5 shadow-sm hover:border-blue-400"
                                                        title="Lihat foto"
                                                        style="width: 34px; height: 34px;"
                                                    >
                                                        <img
                                                            src="{{ asset('storage/' . $photo->file_path) }}"
                                                            alt="Foto laporan"
                                                            style="width: 30px; height: 30px; object-fit: cover; display: block; border-radius: 6px;"
                                                        >
                                                    </a>
                                                @endforeach

                                                @if ($report->photos->count() > 3)
                                                    <div
                                                        class="flex items-center justify-center rounded-md border border-white bg-gray-200 text-[11px] font-semibold text-gray-700 shadow-sm"
                                                        style="width: 34px; height: 34px;"
                                                    >
                                                        +{{ $report->photos->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-1.5 text-center text-[11px] font-medium text-gray-500 leading-none">
                                                {{ $report->photos->count() }} foto
                                            </div>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
                                            <span class="text-[11px] font-medium text-gray-400">
                                                Tidak ada
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 align-top bg-white border-y border-r border-gray-200 rounded-r-xl">
                                    <a
                                        href="{{ route('pegawai.reports.show', $report) }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500 bg-white rounded-xl border border-gray-200">
                                    Belum ada laporan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $reports->links() }}
            </div>
        </div>

    </div>
</div>