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
                    <x-icon name="target" class="h-4 w-4" />
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
        <section
            id="target-form-panel"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
            {{-- FORM HEADER --}}
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon
                                name="{{ $isEdit ? 'edit-3' : 'target' }}"
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                {{ $isEdit ? 'Edit Target Tahunan' : 'Tambah Target Tahunan' }}
                            </h2>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Isi identitas target, metode capaian, dan objek pekerjaan.
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="cancel"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Tutup Form
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="save" class="space-y-6 p-5 sm:p-6">

                {{-- VALIDATION SUMMARY --}}
                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <x-icon name="x-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />

                        <div>
                            <p class="font-semibold">
                                Terjadi Kesalahan
                            </p>

                            <p class="mt-1 leading-6">
                                Periksa kembali field yang ditandai sebelum menyimpan target.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- INFORMASI UTAMA --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-slate-200">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Informasi Utama
                                </h3>

                                <p class="mt-0.5 text-xs leading-5 text-slate-500">
                                    Tentukan unit, klasifikasi, nama, deskripsi, dan tahun target.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 lg:grid-cols-2">
                        <div>
                            <label
                                for="unit_id"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Unit
                                <span class="text-rose-600">*</span>
                            </label>

                            <select
                                id="unit_id"
                                wire:model.defer="unit_id"
                                @if ($isUnitManager) disabled @endif
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                            >
                                <option value="">Pilih Unit</option>

                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($isUnitManager)
                                <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                    Unit otomatis mengikuti unit Kanit atau GKM.
                                </p>
                            @endif

                            @error('unit_id')
                                <p class="mt-1.5 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="duty_classification_id"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Klasifikasi Tupoksi
                            </label>

                            <select
                                id="duty_classification_id"
                                wire:model.defer="duty_classification_id"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">Umum / Belum Diklasifikasikan</option>

                                @foreach ($classifications as $classification)
                                    <option value="{{ $classification->id }}">
                                        {{ $classification->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                Klasifikasi membantu sistem mencocokkan target dengan laporan harian.
                            </p>

                            @error('duty_classification_id')
                                <p class="mt-1.5 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label
                                for="target_name"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Nama Target
                                <span class="text-rose-600">*</span>
                            </label>

                            <input
                                id="target_name"
                                type="text"
                                wire:model.defer="target_name"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Contoh: Monitoring dan Pemeliharaan Aplikasi Unit TI"
                            >

                            @error('target_name')
                                <p class="mt-1.5 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label
                                for="target_description"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Deskripsi Target
                            </label>

                            <textarea
                                id="target_description"
                                wire:model.defer="target_description"
                                rows="4"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Tuliskan penjelasan singkat mengenai pekerjaan dan hasil yang ingin dicapai."
                            ></textarea>

                            @error('target_description')
                                <p class="mt-1.5 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="target_year"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Tahun Target
                                <span class="text-rose-600">*</span>
                            </label>

                            <input
                                id="target_year"
                                type="number"
                                wire:model.defer="target_year"
                                min="2020"
                                max="2100"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >

                            @error('target_year')
                                <p class="mt-1.5 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-cyan-700 shadow-sm ring-1 ring-cyan-100">
                                    <x-icon name="calendar-days" class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Periode Tahunan
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-600">
                                        Target berlaku selama satu tahun. Laporan triwulan akan membaca capaian dari target tahunan ini.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- METODE CAPAIAN --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm ring-1 ring-slate-200">
                                <x-icon name="activity" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Metode Capaian
                                </h3>

                                <p class="mt-0.5 text-xs leading-5 text-slate-500">
                                    Pilih cara sistem menghitung dan menampilkan progress target.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 md:grid-cols-3">
                        <label class="relative cursor-pointer rounded-2xl border bg-white p-4 shadow-sm transition hover:border-sky-300 {{ $achievement_method === 'auto_report' ? 'border-sky-500 ring-2 ring-sky-100' : 'border-slate-200' }}">
                            <input
                                type="radio"
                                wire:model.live="achievement_method"
                                value="auto_report"
                                class="sr-only"
                            >

                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <x-icon name="file-check-2" class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Otomatis dari Laporan
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Capaian dihitung dari laporan harian yang sesuai dengan target.
                                    </p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer rounded-2xl border bg-white p-4 shadow-sm transition hover:border-amber-300 {{ $achievement_method === 'manual_progress' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200' }}">
                            <input
                                type="radio"
                                wire:model.live="achievement_method"
                                value="manual_progress"
                                class="sr-only"
                            >

                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                    <x-icon name="trending-up" class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Manual Progress
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Progress diperbarui manual dalam persen untuk pekerjaan bertahap.
                                    </p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer rounded-2xl border bg-white p-4 shadow-sm transition hover:border-emerald-300 {{ $achievement_method === 'manual_status' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }}">
                            <input
                                type="radio"
                                wire:model.live="achievement_method"
                                value="manual_status"
                                class="sr-only"
                            >

                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <x-icon name="check-circle" class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Manual Status
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Capaian diperbarui melalui status Belum Mulai, Berjalan, atau Selesai.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('achievement_method')
                        <p class="px-5 pb-5 text-sm font-medium text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </section>

                {{-- OBJEK DAN NILAI TARGET --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-violet-700 shadow-sm ring-1 ring-slate-200">
                                <x-icon name="layers" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Objek dan Nilai Target
                                </h3>

                                <p class="mt-0.5 text-xs leading-5 text-slate-500">
                                    Tentukan objek pekerjaan serta nilai capaian yang akan digunakan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 p-5">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            <div>
                                <label
                                    for="object_type"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Jenis Objek
                                    <span class="text-rose-600">*</span>
                                </label>

                                <select
                                    id="object_type"
                                    wire:model.live="object_type"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                >
                                    <option value="none">Umum</option>
                                    <option value="server">Server</option>
                                    <option value="application">Aplikasi</option>
                                    <option value="facility">Perangkat / Fasilitas</option>
                                    <option value="document">Dokumen / Administrasi</option>
                                    <option value="user_service">Layanan Pengguna</option>
                                    <option value="other">Lainnya</option>
                                </select>

                                <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                    Server dan aplikasi dapat dicocokkan langsung dengan objek pada laporan harian.
                                </p>

                                @error('object_type')
                                    <p class="mt-1.5 text-sm font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            @if ($object_type === 'server')
                                <div>
                                    <label
                                        for="server_id"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Server
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <select
                                        id="server_id"
                                        wire:model.defer="server_id"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    >
                                        <option value="">Pilih Server</option>

                                        @foreach ($servers as $server)
                                            <option value="{{ $server->id }}">
                                                {{ $server->name }}
                                                {{ $server->ip_address ? ' — ' . $server->ip_address : '' }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('server_id')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            @if ($object_type === 'application')
                                <div>
                                    <label
                                        for="application_id"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Aplikasi
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <select
                                        id="application_id"
                                        wire:model.defer="application_id"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    >
                                        <option value="">Pilih Aplikasi</option>

                                        @foreach ($applications as $application)
                                            <option value="{{ $application->id }}">
                                                {{ $application->name }}
                                                {{ $application->server?->name ? ' — ' . $application->server->name : '' }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('application_id')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        @if ($achievement_method === 'auto_report')
                            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                <div>
                                    <label
                                        for="target_quantity"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Jumlah Target
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <input
                                        id="target_quantity"
                                        type="number"
                                        wire:model.defer="target_quantity"
                                        min="1"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    >

                                    @error('target_quantity')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="target_unit"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Satuan
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <input
                                        id="target_unit"
                                        type="text"
                                        wire:model.defer="target_unit"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                        placeholder="Contoh: kali, kegiatan, laporan"
                                    >

                                    @error('target_unit')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        @elseif ($achievement_method === 'manual_progress')
                            <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />

                                <div>
                                    <p class="font-semibold">
                                        Target memakai progress persen
                                    </p>

                                    <p class="mt-1 text-xs leading-5">
                                        Nilai target otomatis disimpan sebagai 100%. Progress awal adalah 0%.
                                    </p>
                                </div>
                            </div>
                        @elseif ($achievement_method === 'manual_status')
                            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                                <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />

                                <div>
                                    <p class="font-semibold">
                                        Target memakai status capaian
                                    </p>

                                    <p class="mt-1 text-xs leading-5">
                                        Nilai target otomatis disimpan sebagai 100%. Status awal adalah Belum Mulai.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- STATUS TARGET --}}
                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input
                            type="checkbox"
                            wire:model.defer="is_active"
                            class="mt-1 rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500"
                        >

                        <span>
                            <span class="block text-sm font-semibold text-slate-900">
                                Aktifkan Target
                            </span>

                            <span class="mt-1 block text-xs leading-5 text-slate-500">
                                Target aktif digunakan pada perhitungan capaian dan filter laporan.
                            </span>
                        </span>
                    </label>
                </section>

                {{-- FORM ACTION --}}
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-end">
                    <button
                        type="button"
                        wire:click="cancel"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Batal
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <x-icon name="check-circle" class="h-4 w-4" />

                        <span wire:loading.remove wire:target="save">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                        </span>

                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </section>
    @endif

    @if ($showDetail && $detailTarget)
        <section
            id="target-detail-panel"
            class="space-y-6"
        >
            {{-- DETAIL HEADER --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-6 px-5 py-5 sm:px-6 sm:py-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                <x-icon name="target" class="h-4 w-4" />
                                Detail Target Tahunan
                            </span>

                            <span class="inline-flex items-center rounded-full bg-cyan-50 px-3 py-1.5 text-xs font-semibold text-cyan-700 ring-1 ring-inset ring-cyan-200">
                                Tahun {{ $detailTarget->target_year }}
                            </span>

                            <span
                                class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset
                                    {{ $detailTarget->is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                        : 'bg-slate-100 text-slate-600 ring-slate-200' }}"
                            >
                                {{ $detailTarget->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <h2 class="mt-4 max-w-4xl text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                            {{ $detailTarget->target_name }}
                        </h2>

                        <p class="mt-3 max-w-4xl text-sm leading-7 text-slate-600">
                            {{ $detailTarget->target_description ?: 'Tidak ada deskripsi target.' }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-600">
                            <span>
                                Unit:
                                <span class="font-semibold text-slate-900">
                                    {{ $detailTarget->unit?->name ?? '-' }}
                                </span>
                            </span>

                            <span>
                                Klasifikasi:
                                <span class="font-semibold text-slate-900">
                                    {{ $detailTarget->classification?->name ?? 'Umum' }}
                                </span>
                            </span>

                            <span>
                                Objek:
                                <span class="font-semibold text-slate-900">
                                    {{ $detailTarget->object_summary }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row lg:pl-6">
                        <button
                            type="button"
                            wire:click="edit({{ $detailTarget->id }})"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="edit-3" class="h-4 w-4" />
                            Edit
                        </button>

                        <button
                            type="button"
                            wire:click="closeDetail"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                        >
                            <x-icon name="x" class="h-4 w-4" />
                            Tutup
                        </button>
                    </div>
                </div>
            </section>

            {{-- RINGKASAN DETAIL --}}
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="info" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-slate-900">
                                    Informasi Target
                                </h3>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Metadata, periode, metode capaian, dan histori pembuatan target.
                                </p>
                            </div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-0 sm:grid-cols-2">
                        <div class="border-b border-slate-100 px-5 py-4 sm:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Unit
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $detailTarget->unit?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Periode
                            </dt>

                            <dd class="mt-1.5">
                                <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-inset ring-cyan-200">
                                    Tahun {{ $detailTarget->target_year }}
                                </span>
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4 sm:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Klasifikasi Tupoksi
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $detailTarget->classification?->name ?? 'Umum' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Objek
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $detailTarget->object_summary }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4 sm:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status Target
                            </dt>

                            <dd class="mt-1.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset
                                    {{ $detailTarget->is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                        : 'bg-slate-100 text-slate-600 ring-slate-200' }}"
                                >
                                    {{ $detailTarget->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Metode Capaian
                            </dt>

                            <dd class="mt-1.5">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                    {{ $detailTarget->achievement_method_label }}
                                </span>
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4 sm:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Dibuat Oleh
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $detailTarget->creator?->name ?? '-' }}
                            </dd>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $detailTarget->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </p>
                        </div>

                        <div class="border-b border-slate-100 px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Terakhir Diubah
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $detailTarget->updater?->name ?? '-' }}
                            </dd>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $detailTarget->updated_at?->format('d/m/Y H:i') ?? '-' }}
                            </p>
                        </div>
                    </dl>
                </section>

                {{-- PREVIEW CAPAIAN --}}
                <section class="overflow-hidden rounded-2xl border border-sky-200 bg-sky-50 shadow-sm">
                    <div class="border-b border-sky-200 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-sky-200">
                                <x-icon name="activity" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-sky-950">
                                    Preview Capaian
                                </h3>

                                <p class="mt-0.5 text-sm text-sky-700">
                                    Ringkasan progress target saat ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-bold tracking-tight text-sky-800">
                                {{ number_format($detailTarget->achievement_percentage, 2, ',', '.') }}%
                            </span>

                            <span class="pb-1 text-sm font-medium text-sky-700">
                                tercapai
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-sky-800">
                            {{ number_format($detailTarget->achievement_count, 0, ',', '.') }}
                            dari
                            {{ $detailTarget->target_summary }}
                        </p>

                        <div class="mt-5 h-3 overflow-hidden rounded-full bg-white">
                            <div
                                class="h-full rounded-full bg-sky-600 transition-all"
                                style="width: {{ min($detailTarget->achievement_percentage, 100) }}%"
                            ></div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $detailTarget->achievement_status_badge_class }}">
                                {{ $detailTarget->achievement_status_label }}
                            </span>

                            <span class="inline-flex items-center rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                {{ $detailTarget->active_supports_count ?? $detailTarget->activeSupports->count() }}
                                data dukung
                            </span>
                        </div>
                    </div>
                </section>
            </div>

            {{-- BAGIAN BERIKUTNYA TETAP --}}

           @if ($targetAchievementSummary)
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <x-icon name="bar-chart-3" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h3 class="text-base font-semibold text-slate-900">
                                        Ringkasan Capaian Target
                                    </h3>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        @if (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'auto_report')
                                            Capaian dihitung dari laporan harian yang sesuai dengan unit, tahun, klasifikasi, dan objek target.
                                        @elseif (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'manual_progress')
                                            Capaian diperbarui manual menggunakan nilai progress persen.
                                        @else
                                            Capaian diperbarui manual berdasarkan status pekerjaan.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex w-fit items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $targetAchievementSummary['status_badge_class'] }}">
                                {{ $targetAchievementSummary['status_label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Target
                                        </p>

                                        <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                                            {{ number_format($targetAchievementSummary['target_quantity'], 0, ',', '.') }}
                                            <span class="text-base font-semibold text-slate-500">
                                                {{ $targetAchievementSummary['target_unit'] }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm ring-1 ring-slate-200">
                                        <x-icon name="target" class="h-4 w-4" />
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">
                                            Realisasi
                                        </p>

                                        <p class="mt-2 text-2xl font-bold tracking-tight text-sky-900">
                                            {{ number_format($targetAchievementSummary['achievement_count'], 0, ',', '.') }}
                                            <span class="text-base font-semibold text-sky-700">
                                                {{ $targetAchievementSummary['target_unit'] }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-sky-200">
                                        <x-icon name="activity" class="h-4 w-4" />
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                                            Sisa Target
                                        </p>

                                        <p class="mt-2 text-2xl font-bold tracking-tight text-amber-900">
                                            {{ number_format($targetAchievementSummary['remaining_target'], 0, ',', '.') }}
                                            <span class="text-base font-semibold text-amber-700">
                                                {{ $targetAchievementSummary['target_unit'] }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-amber-700 shadow-sm ring-1 ring-amber-200">
                                        <x-icon name="clock-3" class="h-4 w-4" />
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">
                                            Data Dukung
                                        </p>

                                        <p class="mt-2 text-2xl font-bold tracking-tight text-violet-900">
                                            {{ $detailTarget->active_supports_count ?? $detailTarget->activeSupports->count() }}
                                        </p>
                                    </div>

                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-violet-700 shadow-sm ring-1 ring-violet-200">
                                        <x-icon name="paperclip" class="h-4 w-4" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold text-slate-700">
                                    Progress Capaian
                                </span>

                                <span class="text-sm font-bold text-slate-900">
                                    {{ number_format($targetAchievementSummary['achievement_percentage'], 2, ',', '.') }}%
                                </span>
                            </div>

                            <div class="mt-3 h-3 overflow-hidden rounded-full bg-white ring-1 ring-inset ring-slate-200">
                                <div
                                    class="h-full rounded-full bg-sky-600 transition-all"
                                    style="width: {{ min($targetAchievementSummary['achievement_percentage'], 100) }}%"
                                ></div>
                            </div>

                            <div class="mt-3 flex flex-col gap-1 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
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
                </section>
            @endif

            @if (in_array($detailTarget->achievement_method, ['manual_progress', 'manual_status'], true))
                <section class="overflow-hidden rounded-2xl border border-amber-200 bg-amber-50 shadow-sm">
                    <div class="border-b border-amber-200 px-5 py-4 sm:px-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 shadow-sm ring-1 ring-amber-200">
                                    <x-icon name="trending-up" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h3 class="text-base font-semibold text-amber-950">
                                        Update Progress Manual
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-amber-800">
                                        Perbarui capaian target. Setiap perubahan akan disimpan dalam riwayat progress.
                                    </p>

                                    @if ($detailTarget->manual_progress_updated_at)
                                        <p class="mt-2 text-xs leading-5 text-amber-700">
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
                            </div>

                            @if (! $showProgressForm)
                                <button
                                    type="button"
                                    wire:click="openProgressForm"
                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700"
                                >
                                    <x-icon name="edit-3" class="h-4 w-4" />
                                    Update Progress
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($showProgressForm)
                        <form
                            wire:submit.prevent="saveProgressUpdate"
                            class="space-y-5 bg-white p-5 sm:p-6"
                        >
                            @if ($detailTarget->achievement_method === 'manual_progress')
                                <div>
                                    <label
                                        for="manual_progress_input"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Progress (%)
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <input
                                        id="manual_progress_input"
                                        type="number"
                                        wire:model.defer="manual_progress_input"
                                        min="0"
                                        max="100"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                    >

                                    <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                        Masukkan nilai progress antara 0 sampai 100 persen.
                                    </p>

                                    @error('manual_progress_input')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            @if ($detailTarget->achievement_method === 'manual_status')
                                <div>
                                    <label
                                        for="manual_status_input"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Status Capaian
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <select
                                        id="manual_status_input"
                                        wire:model.defer="manual_status_input"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                    >
                                        <option value="not_started">Belum Mulai</option>
                                        <option value="in_progress">Berjalan</option>
                                        <option value="completed">Selesai</option>
                                    </select>

                                    @error('manual_status_input')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label
                                    for="manual_progress_note_input"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Catatan Progress
                                </label>

                                <textarea
                                    id="manual_progress_note_input"
                                    wire:model.defer="manual_progress_note_input"
                                    rows="4"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-amber-500 focus:ring-amber-500"
                                    placeholder="Contoh: Tahap konfigurasi dan pengujian awal telah selesai."
                                ></textarea>

                                @error('manual_progress_note_input')
                                    <p class="mt-1.5 text-sm font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                                <button
                                    type="button"
                                    wire:click="cancelProgressForm"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                >
                                    <x-icon name="x" class="h-4 w-4" />
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="saveProgressUpdate"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <x-icon name="check-circle" class="h-4 w-4" />

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
                </section>
            @endif

            @if (in_array($detailTarget->achievement_method, ['manual_progress', 'manual_status'], true))
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <x-icon name="history" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-slate-900">
                                    Riwayat Progress Manual
                                </h3>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Histori perubahan progress atau status capaian target.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($detailTarget->progressUpdates as $progressUpdate)
                            <article class="p-5 sm:p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                                {{ $progressUpdate->achievement_method_label }}
                                            </span>

                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset
                                                    {{ $progressUpdate->status === 'completed'
                                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                                        : ($progressUpdate->status === 'in_progress'
                                                            ? 'bg-amber-50 text-amber-700 ring-amber-200'
                                                            : 'bg-slate-100 text-slate-600 ring-slate-200') }}"
                                            >
                                                {{ $progressUpdate->status_label }}
                                            </span>
                                        </div>

                                        <div class="mt-3 flex items-end gap-2">
                                            <span class="text-2xl font-bold tracking-tight text-slate-900">
                                                {{ number_format($progressUpdate->progress_value, 0, ',', '.') }}%
                                            </span>

                                            <span class="pb-0.5 text-sm text-slate-500">
                                                progress
                                            </span>
                                        </div>

                                        @if ($progressUpdate->note)
                                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                                                {{ $progressUpdate->note }}
                                            </p>
                                        @else
                                            <p class="mt-3 text-sm italic text-slate-400">
                                                Tidak ada catatan progress.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="shrink-0 rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500 sm:text-right">
                                        <p class="font-semibold text-slate-700">
                                            {{ $progressUpdate->created_at?->format('d/m/Y H:i') ?? '-' }}
                                        </p>

                                        <p class="mt-1">
                                            oleh {{ $progressUpdate->updater?->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <x-icon name="history" class="h-6 w-6" />
                                </div>

                                <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                    Belum ada riwayat progress
                                </h3>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Riwayat akan muncul setelah progress atau status diperbarui.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            @endif

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-slate-900">
                                    @if (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'auto_report')
                                        Laporan Harian yang Cocok
                                    @else
                                        Referensi Laporan Harian
                                    @endif
                                </h3>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    @if (($targetAchievementSummary['achievement_method'] ?? 'auto_report') === 'auto_report')
                                        Menampilkan {{ $matchingReports->count() }} dari {{ $matchingReportsTotal }} laporan yang sesuai dengan target.
                                    @else
                                        Laporan harian ini hanya menjadi referensi karena progress utama diperbarui secara manual.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                            {{ $matchingReportsTotal }} laporan
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Tanggal
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Pegawai
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Tupoksi
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Laporan
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($matchingReports as $report)
                                <tr class="transition hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-5 py-4 align-top text-sm font-medium text-slate-700">
                                        {{ optional($report->report_date)->format('d/m/Y') ?? $report->report_date }}
                                    </td>

                                    <td class="px-4 py-4 align-top text-sm text-slate-700">
                                        {{ $report->employee?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 align-top text-sm text-slate-700">
                                        {{ $report->duty?->name ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <p class="font-semibold text-slate-900">
                                            {{ $report->title ?? '-' }}
                                        </p>

                                        <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500">
                                            {{ \Illuminate\Support\Str::limit($report->description ?? '', 160) }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                            <x-icon name="clipboard-list" class="h-6 w-6" />
                                        </div>

                                        <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                            Belum ada laporan yang cocok
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            Laporan harian yang sesuai dengan target belum ditemukan.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($matchingReports->count() < $matchingReportsTotal)
                    <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 text-center">
                        <button
                            type="button"
                            wire:click="loadMoreMatchingReports"
                            wire:loading.attr="disabled"
                            wire:target="loadMoreMatchingReports"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <x-icon name="chevron-down" class="h-4 w-4" />

                            <span wire:loading.remove wire:target="loadMoreMatchingReports">
                                Tampilkan Lebih Banyak
                            </span>

                            <span wire:loading wire:target="loadMoreMatchingReports">
                                Memuat...
                            </span>
                        </button>
                    </div>
                @endif
            </section>
            {{-- DATA DUKUNG TARGET --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                <x-icon name="paperclip" class="h-5 w-5" />
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-semibold text-slate-900">
                                        Data Dukung Target
                                    </h3>

                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                        {{ $detailTarget->active_supports_count ?? $detailTarget->activeSupports->count() }}
                                    </span>
                                </div>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    File, tautan, catatan, dan bukti lain yang mendukung capaian target.
                                </p>
                            </div>
                        </div>

                        @if (! $showSupportForm)
                            <button
                                type="button"
                                wire:click="openSupportForm"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="paperclip" class="h-4 w-4" />
                                Tambah Data Dukung
                            </button>
                        @endif
                    </div>
                </div>

                {{-- FORM DATA DUKUNG --}}
                @if ($showSupportForm)
                    <div class="border-b border-slate-100 bg-slate-50 p-5 sm:p-6">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">
                                    {{ $isEditingSupport ? 'Edit Data Dukung' : 'Tambah Data Dukung' }}
                                </h4>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    {{ $isEditingSupport
                                        ? 'Perbarui jenis, judul, file, tautan, atau deskripsi data dukung.'
                                        : 'Tambahkan bukti pendukung berupa file, tautan, catatan, atau bentuk lainnya.' }}
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="cancelSupportForm"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-100"
                            >
                                <x-icon name="x" class="h-4 w-4" />
                                Tutup Form
                            </button>
                        </div>

                        <form
                            wire:submit.prevent="{{ $isEditingSupport ? 'updateSupport' : 'saveSupport' }}"
                            class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                        >
                            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                <div>
                                    <label
                                        for="support_type"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Jenis Data Dukung
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <select
                                        id="support_type"
                                        wire:model.live="support_type"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    >
                                        <option value="note">Catatan</option>
                                        <option value="file">File Dokumen</option>
                                        <option value="link">Tautan</option>
                                        <option value="other">Bukti Lainnya</option>
                                    </select>

                                    @error('support_type')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="support_title"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Judul
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <input
                                        id="support_title"
                                        type="text"
                                        wire:model.defer="support_title"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                        placeholder="Contoh: Screenshot hasil pemeliharaan aplikasi"
                                    >

                                    @error('support_title')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            @if ($support_type === 'file')
                                <div>
                                    <label
                                        for="support_file"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        File
                                        @if (! $isEditingSupport)
                                            <span class="text-rose-600">*</span>
                                        @endif
                                    </label>

                                    <input
                                        id="support_file"
                                        type="file"
                                        wire:model="support_file"
                                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100 focus:border-sky-500 focus:ring-sky-500"
                                    >

                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                        Format yang didukung: PDF, Word, Excel, PNG, JPG, dan JPEG.
                                        Ukuran maksimal 10 MB.
                                    </p>

                                    @if ($isEditingSupport)
                                        <p class="mt-1 text-xs leading-5 text-amber-700">
                                            Kosongkan input file apabila tidak ingin mengganti file lama.
                                        </p>
                                    @endif

                                    <div
                                        wire:loading
                                        wire:target="support_file"
                                        class="mt-2 flex items-center gap-2 text-xs font-medium text-sky-700"
                                    >
                                        <x-icon name="upload-cloud" class="h-4 w-4" />
                                        Mengunggah file...
                                    </div>

                                    @error('support_file')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            @if ($support_type === 'link')
                                <div>
                                    <label
                                        for="support_url"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Tautan
                                        <span class="text-rose-600">*</span>
                                    </label>

                                    <input
                                        id="support_url"
                                        type="url"
                                        wire:model.defer="support_url"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                        placeholder="https://..."
                                    >

                                    @error('support_url')
                                        <p class="mt-1.5 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label
                                    for="support_description"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Catatan atau Deskripsi
                                    @if ($support_type === 'note')
                                        <span class="text-rose-600">*</span>
                                    @endif
                                </label>

                                <textarea
                                    id="support_description"
                                    wire:model.defer="support_description"
                                    rows="4"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    placeholder="Tambahkan keterangan mengenai data dukung."
                                ></textarea>

                                @error('support_description')
                                    <p class="mt-1.5 text-sm font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                                <button
                                    type="button"
                                    wire:click="cancelSupportForm"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                >
                                    <x-icon name="x" class="h-4 w-4" />
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="saveSupport,updateSupport,support_file"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <x-icon
                                        name="{{ $isEditingSupport ? 'check-circle' : 'paperclip' }}"
                                        class="h-4 w-4"
                                    />

                                    <span wire:loading.remove wire:target="saveSupport,updateSupport">
                                        {{ $isEditingSupport ? 'Simpan Perubahan' : 'Simpan Data Dukung' }}
                                    </span>

                                    <span wire:loading wire:target="saveSupport,updateSupport">
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- DAFTAR DATA DUKUNG --}}
                <div class="divide-y divide-slate-100">
                    @forelse ($detailTarget->activeSupports as $support)
                        <article class="p-5 sm:p-6">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                        @if ($support->support_type === 'file')
                                            <x-icon name="file-text" class="h-5 w-5" />
                                        @elseif ($support->support_type === 'link')
                                            <x-icon name="external-link" class="h-5 w-5" />
                                        @elseif ($support->support_type === 'note')
                                            <x-icon name="sticky-note" class="h-5 w-5" />
                                        @else
                                            <x-icon name="paperclip" class="h-5 w-5" />
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $support->badge_class }}">
                                                {{ $support->support_type_label }}
                                            </span>

                                            <h4 class="break-words text-sm font-semibold text-slate-900">
                                                {{ $support->title }}
                                            </h4>
                                        </div>

                                        @if ($support->description)
                                            <p class="mt-3 max-w-3xl whitespace-pre-line text-sm leading-7 text-slate-600">{{ $support->description }}</p>
                                        @endif

                                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                            <span>
                                                Oleh:
                                                <span class="font-semibold text-slate-700">
                                                    {{ $support->uploader?->name ?? '-' }}
                                                </span>
                                            </span>

                                            <span>
                                                {{ $support->created_at?->format('d/m/Y H:i') ?? '-' }}
                                            </span>

                                            @if ($support->file_size)
                                                <span>
                                                    Ukuran: {{ $support->formatted_file_size }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2">
                                    @if ($support->support_type === 'file' && $support->file_url)
                                        <a
                                            href="{{ $support->file_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                        >
                                            <x-icon name="download" class="h-4 w-4" />
                                            Unduh
                                        </a>
                                    @endif

                                    @if ($support->support_type === 'link' && $support->url)
                                        <a
                                            href="{{ $support->url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                        >
                                            <x-icon name="external-link" class="h-4 w-4" />
                                            Buka Tautan
                                        </a>
                                    @endif

                                    <button
                                        type="button"
                                        wire:click="editSupport({{ $support->id }})"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                    >
                                        <x-icon name="edit-3" class="h-4 w-4" />
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteSupport({{ $support->id }})"
                                        wire:confirm="Hapus data dukung ini? Data yang sudah dihapus tidak akan ditampilkan kembali."
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs font-semibold text-rose-700 shadow-sm transition hover:border-rose-300 hover:bg-rose-100"
                                    >
                                        <x-icon name="trash-2" class="h-4 w-4" />
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                <x-icon name="paperclip" class="h-7 w-7" />
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-slate-900">
                                Belum ada data dukung
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                Tambahkan file, tautan, catatan, atau bukti lain untuk mendukung capaian target.
                            </p>

                            @if (! $showSupportForm)
                                <button
                                    type="button"
                                    wire:click="openSupportForm"
                                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                >
                                    <x-icon name="paperclip" class="h-4 w-4" />
                                    Tambah Data Dukung
                                </button>
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    @endif

    {{-- FILTER DAN DAFTAR TARGET --}}
        <div x-data="{ showAdvancedFilter: false }" class="space-y-6">
            {{-- Filter Compact --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
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

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end xl:col-span-4">
                        <button
                            type="button"
                            x-on:click="showAdvancedFilter = !showAdvancedFilter"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-100"
                        >
                            <x-icon name="filter" class="h-4 w-4" />

                            <span x-text="showAdvancedFilter ? 'Tutup Filter' : 'Filter Lanjutan'"></span>
                        </button>

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 sm:flex-1"
                        >
                            <x-icon name="rotate-ccw" class="h-4 w-4" />
                            Reset Filter
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
                            Pencarian: “{{ $search }}”
                        </span>
                    @endif

                    @if ($filterClassificationId)
                        <span class="rounded-full bg-white px-3 py-1 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
                            Klasifikasi: {{ $classifications->firstWhere('id', (int) $filterClassificationId)?->name }}
                        </span>
                    @endif
                </div>
            </section>

            {{-- List Compact --}}
            <section class="space-y-4">
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
            <section class="space-y-4">

            @if ($targets->hasPages())
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    {{ $targets->links() }}
                </div>
            @endif
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

    $wire.on('scroll-to-target-form', () => {
        setTimeout(() => {
            const formPanel = document.getElementById('target-form-panel');

            if (formPanel) {
                formPanel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 150);
    });
</script>
@endscript