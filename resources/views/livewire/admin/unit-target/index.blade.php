<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Target Unit
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola target tahunan dan triwulan unit berdasarkan klasifikasi pekerjaan.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
        >
            + Tambah Target
        </button>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($isKanit && ! auth()->user()?->employee?->unit_id)
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            Akun Kanit belum terhubung dengan unit pegawai. Silakan lengkapi relasi pegawai dan unit terlebih dahulu.
        </div>
    @endif

    @if ($showForm)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Target Unit' : 'Tambah Target Unit' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Isi target unit berdasarkan tahun, periode, klasifikasi, dan jumlah pekerjaan.
                </p>
            </div>

            <form wire:submit.prevent="save" class="space-y-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Unit
                        </label>

                        <select
                            wire:model.defer="unit_id"
                            @if($isKanit) disabled @endif
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                        >
                            <option value="">Pilih unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('unit_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if($isKanit)
                            <p class="mt-1 text-xs text-gray-500">
                                Unit otomatis mengikuti unit Kanit.
                            </p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Klasifikasi Tupoksi
                        </label>

                        <select
                            wire:model.defer="duty_classification_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Umum / Belum diklasifikasikan</option>
                            @foreach ($classifications as $classification)
                                <option value="{{ $classification->id }}">
                                    {{ $classification->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('duty_classification_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Nama Target
                    </label>

                    <input
                        type="text"
                        wire:model.defer="target_name"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Monitoring Aplikasi Unit TI"
                    >

                    @error('target_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Deskripsi Target
                    </label>

                    <textarea
                        wire:model.defer="target_description"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tuliskan penjelasan singkat target ini"
                    ></textarea>

                    @error('target_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Tahun
                        </label>

                        <input
                            type="number"
                            wire:model.defer="target_year"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="2020"
                            max="2100"
                        >

                        @error('target_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Periode
                        </label>

                        <select
                            wire:model.live="period_type"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="annual">Tahunan</option>
                            <option value="quarterly">Triwulan</option>
                        </select>

                        @error('period_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Triwulan
                        </label>

                        <select
                            wire:model.defer="quarter"
                            @if($period_type === 'annual') disabled @endif
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                        >
                            <option value="">Pilih triwulan</option>
                            <option value="1">Triwulan 1</option>
                            <option value="2">Triwulan 2</option>
                            <option value="3">Triwulan 3</option>
                            <option value="4">Triwulan 4</option>
                        </select>

                        @error('quarter')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Jenis Objek
                        </label>

                        <select
                            wire:model.live="object_type"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="none">Umum</option>
                            <option value="server">Server</option>
                            <option value="application">Aplikasi</option>
                            <option value="manual">Manual</option>
                        </select>

                        @error('object_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($object_type === 'server')
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Server
                            </label>

                            <select
                                wire:model.defer="server_id"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Pilih server</option>
                                @foreach ($servers as $server)
                                    <option value="{{ $server->id }}">
                                        {{ $server->name }}
                                        @if($server->ip_address)
                                            - {{ $server->ip_address }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('server_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if ($object_type === 'application')
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Aplikasi
                            </label>

                            <select
                                wire:model.defer="application_id"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Pilih aplikasi</option>
                                @foreach ($applications as $application)
                                    <option value="{{ $application->id }}">
                                        {{ $application->name }}
                                        @if($application->server?->name)
                                            - {{ $application->server->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('application_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if ($object_type === 'manual')
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Nama Objek Manual
                            </label>

                            <input
                                type="text"
                                wire:model.defer="object_name"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Contoh: Dokumentasi Bulanan, Backup Database"
                            >

                            @error('object_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Jumlah Target
                        </label>

                        <input
                            type="number"
                            wire:model.defer="target_quantity"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="1"
                        >

                        @error('target_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Satuan
                        </label>

                        <input
                            type="text"
                            wire:model.defer="target_unit"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: kali, dokumen, kegiatan"
                        >

                        @error('target_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        wire:model.defer="is_active"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>

                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        wire:click="cancel"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
                    >
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if ($showDetail && $detailTarget)
        <div
            id="target-detail-panel"
            class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm ring-1 ring-blue-50"
        >
            <div class="mb-5 flex flex-col gap-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-blue-900">
                        Detail Target Unit
                    </h2>
                    <p class="mt-1 text-sm text-blue-700">
                        Ringkasan target dan preview laporan harian yang cocok.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="closeDetail"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    Tutup
                </button>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 lg:col-span-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Nama Target
                    </div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $detailTarget->target_name }}
                    </div>

                    <div class="mt-3 text-sm text-gray-600">
                        {{ $detailTarget->target_description ?: 'Tidak ada deskripsi.' }}
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <div class="text-xs font-medium text-gray-500">Unit</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->unit?->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium text-gray-500">Periode</div>
                            <div class="mt-0.5">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $detailTarget->period_badge_class }}">
                                    {{ $detailTarget->target_year }} / {{ $detailTarget->period_label }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium text-gray-500">Klasifikasi</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->classification?->name ?? 'Umum' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium text-gray-500">Objek</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->object_summary }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500">Dibuat Oleh</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->creator?->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium text-gray-500">Terakhir Diubah Oleh</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->updater?->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium text-gray-500">Dibuat Pada</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium text-gray-500">Diubah Pada</div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-800">
                                {{ $detailTarget->updated_at?->format('d/m/Y H:i') ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500">Status Target</div>
                            <div class="mt-0.5">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $detailTarget->status_badge_class }}">
                                    {{ $detailTarget->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-blue-600">
                        Preview Capaian
                    </div>

                    <div class="mt-2 flex items-end gap-1">
                        <div class="text-3xl font-bold text-blue-700">
                            {{ number_format($detailTarget->achievement_percentage, 2, ',', '.') }}%
                        </div>
                    </div>

                    <div class="mt-2 text-sm text-blue-700">
                        {{ number_format($detailTarget->achievement_count, 0, ',', '.') }}
                        dari
                        {{ number_format($detailTarget->target_quantity, 0, ',', '.') }}
                        {{ $detailTarget->target_unit }}
                    </div>

                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-white">
                        <div
                            class="h-3 rounded-full bg-blue-600"
                            style="width: {{ min($detailTarget->achievement_percentage, 100) }}%"
                        ></div>
                    </div>

                    <div class="mt-3">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $detailTarget->achievement_status_badge_class }}">
                            {{ $detailTarget->achievement_status_label }}
                        </span>
                    </div>
                </div>
            </div>

            @if ($targetAchievementSummary)
                <div class="mt-5 rounded-lg border border-gray-200 bg-white p-4">
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Ringkasan Capaian Target
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Perhitungan berdasarkan laporan harian yang sesuai dengan unit, periode, klasifikasi, dan objek pekerjaan target.
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $targetAchievementSummary['status_badge_class'] }}">
                            {{ $targetAchievementSummary['status_label'] }}
                        </span>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-medium text-gray-500">
                                Target
                            </p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ number_format($targetAchievementSummary['target_quantity']) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                target pekerjaan
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-medium text-gray-500">
                                Realisasi
                            </p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ number_format($targetAchievementSummary['achievement_count']) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                laporan cocok
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-medium text-gray-500">
                                Sisa Target
                            </p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ number_format($targetAchievementSummary['remaining_target']) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                menuju tercapai
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-medium text-gray-500">
                                Data Dukung
                            </p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ $detailTarget->active_supports_count ?? $detailTarget->activeSupports->count() }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                bukti aktif
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="mb-2 flex items-center justify-between text-xs text-gray-600">
                            <span>
                                Progress Capaian
                            </span>
                            <span class="font-semibold text-gray-800">
                                {{ $targetAchievementSummary['achievement_percentage'] }}%
                            </span>
                        </div>

                        <div class="h-3 overflow-hidden rounded-full bg-gray-100">
                            <div
                                class="h-3 rounded-full bg-blue-600 transition-all"
                                style="width: {{ $targetAchievementSummary['achievement_percentage'] }}%"
                            ></div>
                        </div>

                        <div class="mt-2 flex flex-col gap-1 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between">
                            <span>
                                Periode: {{ $targetAchievementSummary['period_label'] }}
                            </span>

                            <span>
                                {{ number_format($targetAchievementSummary['achievement_count']) }}
                                dari
                                {{ number_format($targetAchievementSummary['target_quantity']) }}
                                target pekerjaan tercatat.
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-5 rounded-lg border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Laporan Harian yang Cocok
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Menampilkan {{ $matchingReports->count() }} dari {{ $matchingReportsTotal }} laporan yang cocok dengan target ini.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Pegawai</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tupoksi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul Laporan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($matchingReports as $report)
                                <tr>
                                    <td class="px-4 py-3 align-top text-gray-700">
                                        {{ optional($report->report_date)->format('d/m/Y') ?? $report->report_date }}
                                    </td>

                                    <td class="px-4 py-3 align-top text-gray-700">
                                        {{ $report->employee?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 align-top text-gray-700">
                                        {{ $report->duty?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-medium text-gray-900">
                                            {{ $report->title ?? '-' }}
                                        </div>
                                        <div class="mt-1 max-w-md text-xs text-gray-500">
                                            {{ \Illuminate\Support\Str::limit($report->description ?? '', 120) }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Belum ada laporan harian yang cocok dengan target ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($matchingReports->count() < $matchingReportsTotal)
                    <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 text-center">
                        <button
                            type="button"
                            wire:click="loadMoreMatchingReports"
                            wire:loading.attr="disabled"
                            wire:target="loadMoreMatchingReports"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="loadMoreMatchingReports">
                                Tampilkan Lebih Banyak
                            </span>

                            <span wire:loading wire:target="loadMoreMatchingReports">
                                Memuat...
                            </span>
                        </button>
                    </div>
                @endif

            </div>
                        <div class="mt-5 rounded-lg border border-gray-200">
                            <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        Data Dukung Target
                                    </h3>
                                    <p class="mt-1 text-xs text-gray-500">
                                        File, link, catatan, atau bukti tambahan yang mendukung capaian target ini.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    wire:click="openSupportForm"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white shadow-sm transition hover:bg-blue-700"
                                >
                                    + Tambah Data Dukung
                                </button>
                            </div>

                            @if ($showSupportForm)
                                <div class="border-b border-gray-200 bg-white px-4 py-4">
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-gray-900">
                                            {{ $isEditingSupport ? 'Edit Data Dukung' : 'Tambah Data Dukung' }}
                                        </h4>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $isEditingSupport ? 'Perbarui informasi data dukung target.' : 'Tambahkan file, link, catatan, atau bukti pendukung target.' }}
                                        </p>
                                    </div>
                                    <form wire:submit.prevent="{{ $isEditingSupport ? 'updateSupport' : 'saveSupport' }}" class="space-y-4">
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                                    Jenis Data Dukung
                                                </label>

                                                <select
                                                    wire:model.live="support_type"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                >
                                                    <option value="note">Catatan</option>
                                                    <option value="file">File Dokumen</option>
                                                    <option value="link">Link</option>
                                                    <option value="other">Bukti Lainnya</option>
                                                </select>

                                                @error('support_type')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                                    Judul
                                                </label>

                                                <input
                                                    type="text"
                                                    wire:model.defer="support_title"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="Contoh: Screenshot hasil backup database"
                                                >

                                                @error('support_title')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        @if ($support_type === 'file')
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                                    File
                                                </label>

                                                <input
                                                    type="file"
                                                    wire:model="support_file"
                                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                >

                                                <p class="mt-1 text-xs text-gray-500">
                                                    Format: PDF, Word, Excel, PNG, JPG, JPEG. Maksimal 10 MB.
                                                </p>

                                                @if ($isEditingSupport)
                                                    <p class="mt-1 text-xs text-amber-600">
                                                        Kosongkan file jika tidak ingin mengganti file lama.
                                                    </p>
                                                @endif

                                                <div wire:loading wire:target="support_file" class="mt-2 text-xs text-blue-600">
                                                    Mengunggah file...
                                                </div>

                                                @error('support_file')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif

                                        @if ($support_type === 'link')
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                                    Link
                                                </label>

                                                <input
                                                    type="url"
                                                    wire:model.defer="support_url"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="https://..."
                                                >

                                                @error('support_url')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif

                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                                Catatan / Deskripsi
                                            </label>

                                            <textarea
                                                wire:model.defer="support_description"
                                                rows="3"
                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Tambahkan keterangan data dukung"
                                            ></textarea>

                                            @error('support_description')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                wire:click="cancelSupportForm"
                                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                            >
                                                Batal
                                            </button>

                                            <button
                                                type="submit"
                                                wire:loading.attr="disabled"
                                                wire:target="saveSupport,support_file"
                                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                <span wire:loading.remove wire:target="saveSupport,updateSupport">
                                                    {{ $isEditingSupport ? 'Update Data Dukung' : 'Simpan Data Dukung' }}
                                                </span>

                                                <span wire:loading wire:target="saveSupport,updateSupport">
                                                    Menyimpan...
                                                </span>

                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            <div class="divide-y divide-gray-100 bg-white">
                                @forelse ($detailTarget->activeSupports as $support)
                                    <div class="px-4 py-4">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $support->badge_class }}">
                                                        {{ $support->support_type_label }}
                                                    </span>

                                                    <h4 class="text-sm font-semibold text-gray-900">
                                                        {{ $support->title }}
                                                    </h4>
                                                </div>

                                                @if ($support->description)
                                                    <p class="mt-2 text-sm text-gray-600">
                                                        {{ $support->description }}
                                                    </p>
                                                @endif

                                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                                    <span>
                                                        Diunggah oleh:
                                                        <span class="font-medium text-gray-700">
                                                            {{ $support->uploader?->name ?? '-' }}
                                                        </span>
                                                    </span>

                                                    <span>
                                                        {{ $support->created_at?->format('d/m/Y H:i') }}
                                                    </span>

                                                    @if ($support->file_size)
                                                        <span>
                                                            Ukuran: {{ $support->formatted_file_size }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex shrink-0 items-center gap-2">
                                                @if ($support->support_type === 'file' && $support->file_url)
                                                    <a
                                                        href="{{ $support->file_url }}"
                                                        target="_blank"
                                                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                                    >
                                                        Lihat File
                                                    </a>
                                                @endif

                                                @if ($support->support_type === 'link' && $support->url)
                                                    <a
                                                        href="{{ $support->url }}"
                                                        target="_blank"
                                                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                                    >
                                                        Buka Link
                                                    </a>
                                                @endif

                                                <button
                                                    type="button"
                                                    wire:click="editSupport({{ $support->id }})"
                                                    class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-100"
                                                >
                                                    Edit
                                                </button>

                                                <button
                                                    type="button"
                                                    wire:click="deleteSupport({{ $support->id }})"
                                                    wire:confirm="Yakin mau hapus data dukung ini?"
                                                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-100"
                                                >
                                                    Hapus
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-8 text-center text-sm text-gray-500">
                                        Belum ada data dukung untuk target ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-4">
            <div class="flex flex-col gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Daftar Target Unit
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Target ini akan dipakai sebagai dasar perhitungan capaian unit. Preview capaian saat ini dihitung sementara dari laporan harian yang cocok.
                    </p>
                </div>

                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-6">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Cari target..."
                    >

                    <input
                        type="number"
                        wire:model.live.debounce.400ms="filterYear"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tahun"
                    >

                    @if($isAdmin)
                        <select
                            wire:model.live="filterUnitId"
                            class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select
                        wire:model.live="filterPeriodType"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Periode</option>
                        <option value="annual">Tahunan</option>
                        <option value="quarterly">Triwulan</option>
                    </select>

                    <select
                        wire:model.live="filterQuarter"
                        @if($filterPeriodType !== 'quarterly') disabled @endif
                        class="rounded-lg text-sm shadow-sm transition
                            {{ $filterPeriodType !== 'quarterly'
                                ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400 opacity-70'
                                : 'border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500' }}"
                    >
                        <option value="">Semua Triwulan</option>
                        <option value="1">Triwulan 1</option>
                        <option value="2">Triwulan 2</option>
                        <option value="3">Triwulan 3</option>
                        <option value="4">Triwulan 4</option>
                    </select>

                    @if($filterPeriodType !== 'quarterly')
                        <p class="text-xs text-gray-400">
                            Aktif jika periode Triwulan dipilih.
                        </p>
                    @endif

                    <select
                        wire:model.live="filterStatus"
                        class="rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Status: Semua</option>
                        <option value="active">Status: Aktif</option>
                        <option value="inactive">Status: Nonaktif</option>
                    </select>
                </div>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <select
                        wire:model.live="filterClassificationId"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 md:w-72"
                    >
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($classifications as $classification)
                            <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                        @endforeach
                    </select>

                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Reset Filter
                    </button>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Total Data
                        </div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $targets->total() }}
                        </div>
                    </div>

                    <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3">
                        <div class="text-xs font-medium uppercase tracking-wide text-blue-600">
                            Filter Tahun
                        </div>
                        <div class="mt-1 text-lg font-semibold text-blue-700">
                            {{ $filterYear ?: 'Semua' }}
                        </div>
                    </div>

                    <div class="rounded-lg border border-purple-100 bg-purple-50 px-4 py-3">
                        <div class="text-xs font-medium uppercase tracking-wide text-purple-600">
                            Periode
                        </div>
                        <div class="mt-1 text-lg font-semibold text-purple-700">
                            @if ($filterPeriodType === 'annual')
                                Tahunan
                            @elseif ($filterPeriodType === 'quarterly')
                                Triwulan {{ $filterQuarter ?: 'Semua' }}
                            @else
                                Semua
                            @endif
                        </div>
                    </div>
                    <div class="rounded-lg border border-green-100 bg-green-50 px-4 py-3">
                        <div class="text-xs font-medium uppercase tracking-wide text-green-600">
                            Status
                        </div>
                        <div class="mt-1 text-lg font-semibold text-green-700">
                            @if ($filterStatus === 'active')
                                Aktif
                            @elseif ($filterStatus === 'inactive')
                                Nonaktif
                            @else
                                Semua
                            @endif
                        </div>
                    </div>

                    @if($filterPeriodType === 'quarterly' && ! $filterQuarter)
                        <p class="text-xs text-gray-500">
                            Filter periode triwulan aktif. Pilih triwulan tertentu jika ingin melihat target pada TW 1, TW 2, TW 3, atau TW 4 saja.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Target</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Periode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Klasifikasi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Objek</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Preview Capaian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Data Dukung</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($targets as $target)
                        <tr wire:key="unit-target-{{ $target->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $target->target_name }}
                                </div>
                                <div class="mt-1 max-w-md text-xs text-gray-500">
                                    {{ $target->target_description ?: '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top text-gray-700">
                                {{ $target->unit?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $target->target_year }}
                                </div>

                                <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $target->period_badge_class }}">
                                    {{ $target->period_label }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-top text-gray-700">
                                {{ $target->classification?->name ?? 'Umum' }}
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="max-w-xs rounded-lg bg-gray-50 px-3 py-2">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                        {{ $target->object_type_label }}
                                    </div>
                                    <div class="mt-0.5 text-sm font-medium text-gray-800">
                                        {{ $target->work_object_label }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-2">
                                    <span class="text-base font-semibold text-blue-700">
                                        {{ number_format($target->target_quantity, 0, ',', '.') }}
                                    </span>
                                    <span class="ml-1 text-xs font-medium text-blue-600">
                                        {{ $target->target_unit }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="min-w-40">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-xs font-medium text-gray-500">
                                            {{ number_format($target->achievement_count, 0, ',', '.') }}
                                            /
                                            {{ number_format($target->target_quantity, 0, ',', '.') }}
                                            {{ $target->target_unit }}
                                        </span>

                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $target->achievement_status_badge_class }}">
                                            {{ $target->achievement_status_label }}
                                        </span>
                                    </div>

                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100">
                                        <div
                                            class="h-2 rounded-full bg-blue-600"
                                            style="width: {{ min($target->achievement_percentage, 100) }}%"
                                        ></div>
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ number_format($target->achievement_percentage, 2, ',', '.') }}%
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center align-top">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $target->status_badge_class }}">
                                    {{ $target->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ ($target->active_supports_count ?? 0) > 0 ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $target->active_supports_count ?? 0 }} Data
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right align-top">
                                <div class="flex flex-col justify-end gap-2 sm:flex-row">
                                    <button
                                        type="button"
                                        wire:click="openDetail({{ $target->id }})"
                                        class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50"
                                    >
                                        Detail
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="edit({{ $target->id }})"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $target->id }})"
                                        wire:confirm="{{ $target->is_active ? 'Yakin ingin menonaktifkan target ini?' : 'Yakin ingin mengaktifkan kembali target ini?' }}"
                                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition
                                            {{ $target->is_active
                                                ? 'border-red-200 text-red-700 hover:bg-red-50'
                                                : 'border-green-200 text-green-700 hover:bg-green-50' }}"
                                    >
                                        {{ $target->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">
                                Belum ada data target unit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3">
            {{ $targets->links() }}
        </div>
    </div>
</div>
@script
<script>
    $wire.on('scroll-to-target-detail', () => {
        setTimeout(() => {
            const detailPanel = document.getElementById('target-detail-panel');

            if (detailPanel) {
                detailPanel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 150);
    });
</script>
@endscript