<div class="p-6">
    <div class="max-w-5xl mx-auto">

        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Detail Laporan Kerja
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Detail laporan kerja harian yang sudah Anda input.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('pegawai.reports.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm"
                >
                    Kembali
                </a>

                <a
                    href="{{ route('pegawai.reports.edit', $report) }}"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm"
                >
                    Edit
                </a>

                <button
                    type="button"
                    wire:click="delete"
                    wire:confirm="Yakin ingin menghapus laporan ini? Foto laporan juga akan ikut terhapus."
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm"
                >
                    Hapus
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">

                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $report->title }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $report->report_date?->format('d M Y') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <div class="text-xs text-gray-500">Tupoksi</div>
                        <div class="text-sm font-medium text-gray-800 mt-1">
                            {{ $report->duty->name ?? '-' }}
                            @if ($report->is_delegated)
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                    Delegasi
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    Normal
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 space-y-1 text-xs text-gray-500">
                        @if ($report->is_delegated)
                            <div>
                                Pemilik Tupoksi:
                                <span class="font-medium text-gray-700">
                                    {{ $report->dutyOwnerEmployee?->name ?? '-' }}
                                </span>
                            </div>

                            <div>
                                Dilaporkan Oleh:
                                <span class="font-medium text-gray-700">
                                    {{ $report->reportedByEmployee?->name ?? '-' }}
                                </span>
                            </div>
                        @else
                            <div>
                                Dilaporkan Oleh:
                                <span class="font-medium text-gray-700">
                                    {{ $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($report->is_delegated && $report->delegation)
                        <div class="mt-1 text-xs text-gray-400">
                            Periode Delegasi:
                            {{ $report->delegation->start_date?->format('d/m/Y') ?? '-' }}
                            s.d.
                            {{ $report->delegation->end_date?->format('d/m/Y') ?? 'Tidak ditentukan' }}
                        </div>
                    @endif

                    <div class="rounded-lg bg-gray-50 p-4">
                        <div class="text-xs text-gray-500">Server</div>
                        <div class="text-sm font-medium text-gray-800 mt-1">
                            {{ $report->server->name ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-4">
                        <div class="text-xs text-gray-500">Aplikasi</div>
                        <div class="text-sm font-medium text-gray-800 mt-1">
                            {{ $report->application->name ?? '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi Kegiatan
                    </h3>
                    <div class="prose max-w-none text-gray-700 whitespace-pre-line">
                        {{ $report->description }}
                    </div>
                </div>

                @if ($report->notes)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">
                            Catatan Tambahan
                        </h3>
                        <div class="rounded-lg bg-yellow-50 border border-yellow-100 p-4 text-sm text-yellow-800 whitespace-pre-line">
                            {{ $report->notes }}
                        </div>
                    </div>
                @endif

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">
                        Foto Dokumentasi
                    </h3>

                    @if ($report->photos->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($report->photos as $photo)
                                <a
                                    href="{{ asset('storage/' . $photo->file_path) }}"
                                    target="_blank"
                                    class="block border rounded-lg overflow-hidden bg-gray-50 hover:opacity-90"
                                >
                                    <img
                                        src="{{ asset('storage/' . $photo->file_path) }}"
                                        class="w-full h-40 object-cover"
                                        alt="Foto laporan"
                                    >
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">
                            Tidak ada foto dokumentasi.
                        </p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>