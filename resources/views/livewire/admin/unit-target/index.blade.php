<div class="space-y-6">
    <x-page-hero
        badge="Target Unit"
        title="Kelola target tahunan unit"
        description="Pantau target tahunan unit, metode capaian, progress laporan kerja, dan data dukung target dalam satu halaman yang lebih rapi."
        icon="target"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100/80">
                            Ringkasan Target
                        </p>
                        <p class="mt-2 text-3xl font-bold text-white">
                            {{ method_exists($targets, 'total') ? $targets->total() : $targets->count() }}
                        </p>
                        <p class="mt-1 text-sm text-slate-300">
                            target sesuai filter aktif
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-cyan-200 ring-1 ring-white/15">
                        <x-icon name="list-checks" class="h-5 w-5" />
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200 ring-1 ring-white/10">
                        Tahun: {{ $filterYear ?: 'Semua' }}
                    </span>

                    <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200 ring-1 ring-white/10">
                        Target Tahunan
                    </span>
                </div>

                <button
                    type="button"
                    wire:click="create"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-950 shadow-sm transition hover:bg-cyan-50"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Target
                </button>
            </div>
        </x-slot:aside>
    </x-page-hero>

    @if ($isKanit && ! auth()->user()?->employee?->unit_id)
        <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <x-icon name="alert-circle" class="mt-0.5 h-5 w-5 text-amber-600" />
            <div>
                <p class="font-semibold">Relasi unit Kanit belum lengkap.</p>
                <p class="mt-1">Akun Kanit belum terhubung dengan unit pegawai. Silakan lengkapi relasi pegawai dan unit terlebih dahulu.</p>
            </div>
        </div>
    @endif

    @if ($showForm)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Target Unit' : 'Tambah Target Unit' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Isi target unit berdasarkan tahun, klasifikasi, objek pekerjaan, dan metode capaian.
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

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Tahun Target
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

                    <div class="rounded-xl border border-cyan-100 bg-cyan-50 px-4 py-3">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-cyan-700 ring-1 ring-cyan-100">
                                <x-icon name="calendar-days" class="h-4 w-4" />
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    Target Tahunan
                                </p>
                                <p class="mt-1 text-xs leading-5 text-slate-600">
                                    R7 hanya memakai target tahunan. Laporan 3 bulanan nanti membaca capaian dari target tahunan ini, bukan membuat target triwulan baru.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-semibold text-slate-800">
                            Metode Capaian
                        </label>
                        <p class="text-xs leading-5 text-slate-500">
                            Pilih cara sistem membaca capaian target ini.
                        </p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        <label class="relative cursor-pointer rounded-xl border bg-white p-4 shadow-sm transition hover:border-cyan-300 {{ $achievement_method === 'auto_report' ? 'border-cyan-500 ring-2 ring-cyan-100' : 'border-slate-200' }}">
                            <input
                                type="radio"
                                wire:model.live="achievement_method"
                                value="auto_report"
                                class="sr-only"
                            >

                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                                    <x-icon name="file-check-2" class="h-4 w-4" />
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        Otomatis dari Laporan
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Capaian dihitung dari jumlah laporan harian yang cocok dengan target.
                                    </p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer rounded-xl border bg-white p-4 shadow-sm transition hover:border-cyan-300 {{ $achievement_method === 'manual_progress' ? 'border-cyan-500 ring-2 ring-cyan-100' : 'border-slate-200' }}">
                            <input
                                type="radio"
                                wire:model.live="achievement_method"
                                value="manual_progress"
                                class="sr-only"
                            >

                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                    <x-icon name="activity" class="h-4 w-4" />
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        Manual Progress
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Progress diisi manual dalam persen. Cocok untuk pekerjaan bertahap/proyek.
                                    </p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer rounded-xl border bg-white p-4 shadow-sm transition hover:border-cyan-300 {{ $achievement_method === 'manual_status' ? 'border-cyan-500 ring-2 ring-cyan-100' : 'border-slate-200' }}">
                            <input
                                type="radio"
                                wire:model.live="achievement_method"
                                value="manual_status"
                                class="sr-only"
                            >

                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <x-icon name="check-circle-2" class="h-4 w-4" />
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        Manual Status
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Target ditandai Belum Mulai, Berjalan, atau Selesai.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('achievement_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                            <option value="facility">Perangkat / Fasilitas</option>
                            <option value="document">Dokumen / Administrasi</option>
                            <option value="user_service">Layanan Pengguna</option>
                            <option value="other">Lainnya</option>
                        </select>

                        @error('object_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-1 text-xs text-gray-500">
                            Pilih kategori objek target. Jika memilih Server atau Aplikasi, target akan dihitung dari laporan harian yang memilih server/aplikasi tersebut. Untuk kategori lain, detail pekerjaan cukup dicatat di uraian laporan.
                        </p>
                    </div>

                    @if ($object_type === 'server')
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Server <span class="text-red-500">*</span>
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
                                Aplikasi <span class="text-red-500">*</span>
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

                </div>

                @if ($achievement_method === 'auto_report')
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
                                placeholder="Contoh: kali, kegiatan, laporan"
                            >

                            @error('target_unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @elseif ($achievement_method === 'manual_progress')
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <div class="flex items-start gap-3">
                            <x-icon name="info" class="mt-0.5 h-5 w-5 text-amber-600" />
                            <div>
                                <p class="font-semibold">Target manual progress memakai satuan persen.</p>
                                <p class="mt-1 text-xs leading-5">
                                    Nilai target otomatis disimpan sebagai 100%. Progress awal 0% dan nanti bisa diperbarui dari detail target.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif ($achievement_method === 'manual_status')
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <div class="flex items-start gap-3">
                            <x-icon name="info" class="mt-0.5 h-5 w-5 text-emerald-600" />
                            <div>
                                <p class="font-semibold">Target manual status memakai status capaian.</p>
                                <p class="mt-1 text-xs leading-5">
                                    Target otomatis disimpan sebagai 100%. Status awal Belum Mulai dan nanti bisa diperbarui dari detail target.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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

                        <div>
                            <div class="text-xs font-medium text-gray-500">Metode Capaian</div>
                            <div class="mt-0.5">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">
                                    {{ $detailTarget->achievement_method_label }}
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
                        {{ $detailTarget->target_summary }}
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
                                @if (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'auto_report')
                                    Perhitungan berdasarkan laporan harian yang sesuai dengan unit, tahun, klasifikasi, dan objek pekerjaan target.
                                @elseif (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'manual_progress')
                                    Capaian target diperbarui manual dalam bentuk progress persen.
                                @else
                                    Capaian target diperbarui manual berdasarkan status pekerjaan.
                                @endif
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
                                {{ number_format($targetAchievementSummary['target_quantity'], 0, ',', '.') }}
                                {{ $targetAchievementSummary['target_unit'] }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                target capaian
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-medium text-gray-500">
                                Realisasi
                            </p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ number_format($targetAchievementSummary['achievement_count'], 0, ',', '.') }}
                                {{ $targetAchievementSummary['target_unit'] }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                realisasi capaian
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-medium text-gray-500">
                                Sisa Target
                            </p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ number_format($targetAchievementSummary['remaining_target'], 0, ',', '.') }}
                                {{ $targetAchievementSummary['target_unit'] }}
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
                                {{ number_format($targetAchievementSummary['achievement_count'], 0, ',', '.') }}
                                dari
                                {{ number_format($targetAchievementSummary['target_quantity'], 0, ',', '.') }}
                                {{ $targetAchievementSummary['target_unit'] }}
                                tercapai.
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array($detailTarget->achievement_method, ['manual_progress', 'manual_status'], true))
                <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-amber-900">
                                Update Progress Manual
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-amber-700">
                                Perbarui capaian target manual. Setiap perubahan akan disimpan sebagai riwayat progress.
                            </p>

                            @if ($detailTarget->manual_progress_updated_at)
                                <p class="mt-2 text-xs text-amber-700">
                                    Terakhir diperbarui oleh
                                    <span class="font-semibold">
                                        {{ $detailTarget->manualProgressUpdater?->name ?? '-' }}
                                    </span>
                                    pada
                                    <span class="font-semibold">
                                        {{ $detailTarget->manual_progress_updated_at?->format('d/m/Y H:i') }}
                                    </span>.
                                </p>
                            @endif
                        </div>

                        @if (! $showProgressForm)
                            <button
                                type="button"
                                wire:click="openProgressForm"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700"
                            >
                                <x-icon name="edit-3" class="h-4 w-4" />
                                Update Progress
                            </button>
                        @endif
                    </div>

                    @if ($showProgressForm)
                        <form wire:submit.prevent="saveProgressUpdate" class="mt-4 rounded-xl border border-amber-200 bg-white p-4">
                            @if ($detailTarget->achievement_method === 'manual_progress')
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">
                                        Progress (%)
                                    </label>

                                    <input
                                        type="number"
                                        wire:model.defer="manual_progress_input"
                                        min="0"
                                        max="100"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                    >

                                    @error('manual_progress_input')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            @if ($detailTarget->achievement_method === 'manual_status')
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">
                                        Status Capaian
                                    </label>

                                    <select
                                        wire:model.defer="manual_status_input"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                    >
                                        <option value="not_started">Belum Mulai</option>
                                        <option value="in_progress">Berjalan</option>
                                        <option value="completed">Selesai</option>
                                    </select>

                                    @error('manual_status_input')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div class="mt-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Catatan Progress
                                </label>

                                <textarea
                                    wire:model.defer="manual_progress_note_input"
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                    placeholder="Contoh: Progress meningkat karena tahap konfigurasi selesai."
                                ></textarea>

                                @error('manual_progress_note_input')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                                <button
                                    type="button"
                                    wire:click="cancelProgressForm"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="saveProgressUpdate"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <x-icon name="save" class="h-4 w-4" />

                                    <span wire:loading.remove wire:target="saveProgressUpdate">
                                        Simpan Progress
                                    </span>

                                    <span wire:loading wire:target="saveProgressUpdate">
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif

            @if (in_array($detailTarget->achievement_method, ['manual_progress', 'manual_status'], true))
                <div class="mt-5 rounded-lg border border-gray-200 bg-white">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Riwayat Progress Manual
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">
                            Menampilkan histori perubahan progress/status target.
                        </p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($detailTarget->progressUpdates as $progressUpdate)
                            <div class="p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                                {{ $progressUpdate->achievement_method_label }}
                                            </span>

                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $progressUpdate->status === 'completed' ? 'bg-green-100 text-green-700' : ($progressUpdate->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $progressUpdate->status_label }}
                                            </span>
                                        </div>

                                        <p class="mt-2 text-sm font-semibold text-gray-900">
                                            Progress:
                                            {{ number_format($progressUpdate->progress_value, 0, ',', '.') }}%
                                        </p>

                                        @if ($progressUpdate->note)
                                            <p class="mt-1 text-sm leading-6 text-gray-600">
                                                {{ $progressUpdate->note }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="text-xs text-gray-500 sm:text-right">
                                        <div>
                                            {{ $progressUpdate->created_at?->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="mt-1">
                                            oleh {{ $progressUpdate->updater?->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-gray-500">
                                Belum ada riwayat progress manual.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <div class="mt-5 rounded-lg border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        @if (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'auto_report')
                            Laporan Harian yang Cocok
                        @else
                            Referensi Laporan Harian
                        @endif
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">
                        @if (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'auto_report')
                            Menampilkan {{ $matchingReports->count() }} dari {{ $matchingReportsTotal }} laporan yang cocok dengan target ini.
                        @else
                            Menampilkan laporan harian yang cocok sebagai referensi pendukung. Progress utama target ini diperbarui secara manual.
                        @endif
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
                                    <x-icon name="plus" class="h-4 w-4" />
                                    Tambah Data Dukung
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

    {{-- Daftar Target Unit - V3 Compact --}}
    <x-ui.card padding="p-5">
        <div x-data="{ showAdvancedFilter: false }">
            {{-- Filter Compact --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-12">
                    <div class="xl:col-span-4">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Pencarian
                        </label>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-icon name="search" class="h-4 w-4" />
                            </div>

                            <input
                                type="text"
                                wire:model.live.debounce.400ms="search"
                                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                placeholder="Cari target..."
                            >
                        </div>
                    </div>

                    <div class="xl:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tahun
                        </label>

                        <input
                            type="number"
                            wire:model.live.debounce.400ms="filterYear"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                            placeholder="Tahun"
                        >
                    </div>
                    
                    <div class="xl:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <select
                            wire:model.live="filterStatus"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Semua</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2 xl:col-span-2">
                        <button
                            type="button"
                            x-on:click="showAdvancedFilter = !showAdvancedFilter"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-100"
                        >
                            <x-icon name="filter" class="h-4 w-4" />

                            <span x-text="showAdvancedFilter ? 'Tutup' : 'Detail'"></span>
                        </button>

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            title="Reset Filter"
                        >
                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                {{-- Filter Detail --}}
                <div
                    x-show="showAdvancedFilter"
                    class="mt-4 rounded-2xl border border-slate-200 bg-white p-4"
                    style="display: none;"
                >
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                        @if($isAdmin)
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Unit
                                </label>

                                <select
                                    wire:model.live="filterUnitId"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                >
                                    <option value="">Semua Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="{{ $isAdmin ? 'xl:col-span-2' : 'xl:col-span-3' }}">
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Klasifikasi
                            </label>

                            <select
                                wire:model.live="filterClassificationId"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                            >
                                <option value="">Semua Klasifikasi</option>
                                @foreach ($classifications as $classification)
                                    <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filter Chips --}}
                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-semibold text-slate-500">Filter aktif:</span>

                    <span class="rounded-full bg-white px-3 py-1 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
                        Tahun: {{ $filterYear ?: 'Semua' }}
                    </span>

                    @if ($filterStatus)
                        <span class="rounded-full bg-white px-3 py-1 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Status: {{ $filterStatus === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    @endif

                    @if ($search)
                        <span class="rounded-full bg-white px-3 py-1 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Search: "{{ $search }}"
                        </span>
                    @endif

                    @if ($filterClassificationId)
                        <span class="rounded-full bg-white px-3 py-1 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Klasifikasi: {{ $classifications->firstWhere('id', (int) $filterClassificationId)?->name }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- List Compact --}}
            <div class="mt-6 space-y-3">
                @forelse ($targets as $target)
                    @php
                        $achievementPercentage = min($target->achievement_percentage, 100);
                    @endphp

                    <div
                        wire:key="unit-target-compact-{{ $target->id }}"
                        class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-cyan-200 hover:shadow-md"
                    >
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-12 xl:items-center">
                            {{-- Target Info --}}
                            <div class="min-w-0 xl:col-span-5">
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                                        Tahun {{ $target->target_year }}
                                    </span>

                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $target->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-slate-100 text-slate-500 ring-1 ring-slate-200' }}">
                                        {{ $target->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>

                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $target->achievement_status_badge_class }}">
                                        {{ $target->achievement_status_label }}
                                    </span>
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                        {{ $target->achievement_method_label }}
                                    </span>
                                </div>

                                <h3 class="truncate text-sm font-bold text-slate-900">
                                    {{ $target->target_name }}
                                </h3>

                                <p class="mt-1 line-clamp-1 text-xs leading-5 text-slate-500">
                                    {{ $target->target_description ?: ($target->classification?->name ?? 'Target unit') }}
                                </p>
                            </div>

                            {{-- Progress --}}
                            <div class="xl:col-span-4">
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                                    <span class="font-semibold text-slate-600">
                                        Progress Capaian
                                    </span>

                                    <span class="font-bold text-slate-900">
                                        {{ number_format($target->achievement_percentage, 2, ',', '.') }}%
                                    </span>
                                </div>

                                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                    <div
                                        class="h-full rounded-full bg-cyan-600"
                                        style="width: {{ $achievementPercentage }}%"
                                    ></div>
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span>
                                        {{ number_format($target->achievement_count, 0, ',', '.') }}
                                        /
                                        {{ $target->target_summary }}
                                    </span>

                                    <span class="text-slate-300">•</span>

                                    <span>
                                        {{ $target->active_supports_count ?? 0 }} data dukung
                                    </span>
                                </div>
                            </div>

                            {{-- Action --}}
                            <div class="flex flex-wrap justify-start gap-2 xl:col-span-3 xl:justify-end">
                                <button
                                    type="button"
                                    wire:click="openDetail({{ $target->id }})"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                >
                                    Detail
                                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                                </button>

                                <button
                                    type="button"
                                    wire:click="edit({{ $target->id }})"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $target->id }})"
                                    wire:confirm="{{ $target->is_active ? 'Yakin ingin menonaktifkan target ini?' : 'Yakin ingin mengaktifkan kembali target ini?' }}"
                                    class="inline-flex items-center justify-center rounded-xl border px-3 py-2 text-xs font-semibold shadow-sm transition
                                        {{ $target->is_active
                                            ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100'
                                            : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                >
                                    {{ $target->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                            <x-icon name="target" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-bold text-slate-900">
                            Belum ada target unit
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Target tahunan yang dibuat akan tampil di sini.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($targets->hasPages())
                <div class="mt-5 border-t border-slate-200 pt-4">
                    {{ $targets->links() }}
                </div>
            @endif
        </div>
    </x-ui.card>
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