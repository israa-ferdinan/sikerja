<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Detail Laporan Pegawai
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Detail laporan kerja harian pegawai dalam unit Anda.
            </p>
        </div>

        <a href="{{ route('kanit.reports.monitoring') }}"
           class="inline-flex items-center justify-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
            ← Kembali
        </a>
    </div>

    {{-- Info Utama --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $report->title }}
                        </h2>

                        <p class="mt-2 text-sm text-gray-500">
                            Dibuat pada {{ optional($report->report_date)->format('d/m/Y') }}
                        </p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ ucfirst($report->status ?? 'draft') }}
                    </span>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Deskripsi Pekerjaan
                        </h3>
                        <div class="mt-2 rounded-xl bg-gray-50 p-4 text-sm leading-6 text-gray-700">
                            {!! nl2br(e($report->description ?? '-')) !!}
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Hasil / Keterangan
                        </h3>
                        <div class="mt-2 rounded-xl bg-gray-50 p-4 text-sm leading-6 text-gray-700">
                            {!! nl2br(e($report->result ?? '-')) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Foto --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">
                            Foto Laporan
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Dokumentasi foto yang diunggah pegawai.
                        </p>
                    </div>

                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                        {{ $report->photos->count() }} foto
                    </span>
                </div>

                @if($report->photos->count())
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                        @foreach($report->photos as $photo)
                            <a href="{{ Storage::url($photo->file_path) }}"
                               target="_blank"
                               class="group overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                                <img src="{{ Storage::url($photo->file_path) }}"
                                     alt="Foto laporan"
                                     class="h-36 w-full object-cover transition duration-200 group-hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 p-6 text-center text-sm text-gray-500">
                        Tidak ada foto pada laporan ini.
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">
                    Informasi Pegawai
                </h2>

                <div class="mt-4 space-y-4 text-sm">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Nama Pegawai
                        </div>
                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $report->employee->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Jabatan
                        </div>
                        <div class="mt-1 text-gray-700">
                            {{ $report->employee->position ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Unit
                        </div>
                        <div class="mt-1 text-gray-700">
                            {{ $report->employee->unit->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">
                    Informasi Laporan
                </h2>

                <div class="mt-4 space-y-4 text-sm">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Tanggal Laporan
                        </div>
                        <div class="mt-1 font-semibold text-gray-900">
                            {{ optional($report->report_date)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Tupoksi
                        </div>
                        <div class="mt-1 text-gray-700">
                            {{ $report->duty->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Server
                        </div>
                        <div class="mt-1 text-gray-700">
                            {{ $report->server->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Aplikasi
                        </div>
                        <div class="mt-1 text-gray-700">
                            {{ $report->application->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <h3 class="text-sm font-semibold text-blue-900">
                    Akses Kanit
                </h3>
                <p class="mt-2 text-sm leading-6 text-blue-700">
                    Halaman ini hanya menampilkan detail laporan pegawai yang berada dalam unit Kanit yang sedang login.
                </p>
            </div>
        </div>
    </div>
</div>