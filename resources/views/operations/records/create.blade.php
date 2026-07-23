<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-slate-800">
                        Buat Rekap Operasional
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Rekap akan dibuat otomatis dari item aktif sesuai jenis rekap.
                    </p>
                </div>

                <a
                    href="{{ route('operations.forms.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    Kembali
                </a>
            </div>
            @if(session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('operations.forms.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf

                @if(auth()->user()->isAdmin())
                    <div>
                        <label for="unit_id" class="mb-1 block text-sm font-medium text-slate-700">
                            Unit <span class="text-red-500">*</span>
                        </label>
                        <select id="unit_id" name="unit_id" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                            <option value="">Pilih Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="category" class="mb-1 block text-sm font-medium text-slate-700">
                        Jenis Rekap <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                        <option value="">Pilih Jenis Rekap</option>
                        @foreach($categoryOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', $selectedCategory) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-500">
                        Pastikan master item untuk jenis ini sudah tersedia dan aktif.
                    </p>
                </div>

                <div>
                    <label for="technician_employee_id" class="mb-1 block text-sm font-medium text-slate-700">
                        Teknisi / Petugas
                    </label>

                    <select
                        id="technician_employee_id"
                        name="technician_employee_id"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                    >
                        <option value="">Otomatis dari tupoksi / pilih manual</option>

                        @foreach($technicians as $technician)
                            <option
                                value="{{ $technician->id }}"
                                data-unit-id="{{ $technician->unit_id }}"
                                @selected((string) old('technician_employee_id', $defaultTechnicianId) === (string) $technician->id)
                            >
                                {{ $technician->name }}@if($technician->nip) — NIP. {{ $technician->nip }}@endif
                            </option>
                        @endforeach
                    </select>

                    <p class="mt-2 text-xs text-slate-500">
                        Default teknisi diambil dari tupoksi terkait jenis rekap. Jika pelaksana berbeda, pilih manual sebelum rekap diajukan.
                    </p>
                </div>

                <div>
                    <label for="source_mode" class="mb-1 block text-sm font-medium text-slate-700">
                        Sumber Data Rekap <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="source_mode"
                        name="source_mode"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        required
                    >
                        <option value="previous" @selected(old('source_mode', 'previous') === 'previous')>
                            Salin dari rekap sebelumnya
                        </option>
                        <option value="master" @selected(old('source_mode') === 'master')>
                            Dari master item aktif
                        </option>
                    </select>

                    <p class="mt-2 text-xs text-slate-500">
                        Untuk rekap bulanan rutin, gunakan salin dari rekap sebelumnya agar kondisi dan keterangan bulan lalu ikut terbawa.
                    </p>
                </div>

                <div>
                    <label for="source_record_id" class="mb-1 block text-sm font-medium text-slate-700">
                        Rekap Sumber
                    </label>

                    <select
                        id="source_record_id"
                        name="source_record_id"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                    >
                        <option value="">Otomatis pilih rekap terakhir sesuai jenis/unit</option>

                        @foreach($sourceRecords as $sourceRecord)
                            <option value="{{ $sourceRecord->id }}" @selected((string) old('source_record_id') === (string) $sourceRecord->id)>
                                {{ $sourceRecord->record_code }} — {{ $sourceRecord->title }}
                                @if($sourceRecord->period_month && $sourceRecord->period_year)
                                    ({{ $monthOptions[$sourceRecord->period_month] ?? $sourceRecord->period_month }} {{ $sourceRecord->period_year }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <p class="mt-2 text-xs text-slate-500">
                        Kosongkan jika ingin sistem memilih rekap terakhir secara otomatis.
                    </p>
                </div>

                <div>
                    <label for="title" class="mb-1 block text-sm font-medium text-slate-700">
                        Judul Rekap <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Contoh: Rekap Jaringan Bulan Juli 2026"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        required
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label for="period_month" class="mb-1 block text-sm font-medium text-slate-700">
                            Bulan <span class="text-red-500">*</span>
                        </label>
                        <select id="period_month" name="period_month" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}" @selected((string) old('period_month', now()->month) === (string) $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="period_year" class="mb-1 block text-sm font-medium text-slate-700">
                            Tahun <span class="text-red-500">*</span>
                        </label>
                        <select id="period_year" name="period_year" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}" @selected((string) old('period_year', now()->year) === (string) $year)>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="record_date" class="mb-1 block text-sm font-medium text-slate-700">
                            Tanggal Rekap
                        </label>
                        <input
                            id="record_date"
                            type="date"
                            name="record_date"
                            value="{{ old('record_date', now()->toDateString()) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>
                </div>

                <div>
                    <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">
                        Catatan Umum
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        placeholder="Catatan umum rekap jika ada..."
                    >{{ old('notes') }}</textarea>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                    <p class="font-semibold">Cara kerja pembuatan rekap:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        <li>
                            Jika memilih <strong>Salin dari rekap sebelumnya</strong>, kondisi, keterangan, tindakan, dan status komponen akan ikut disalin.
                        </li>
                        <li>
                            Item aktif baru yang belum ada di rekap sumber tetap akan otomatis ditambahkan.
                        </li>
                        <li>
                            Jika belum ada rekap sebelumnya, gunakan sumber <strong>Dari master item aktif</strong>.
                        </li>
                    </ul>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('operations.forms.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Buat Rekap
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitSelect = document.getElementById('unit_id');
            const technicianSelect = document.getElementById('technician_employee_id');

            if (!technicianSelect) {
                return;
            }

            const allOptions = Array.from(technicianSelect.querySelectorAll('option'));

            function filterTechnicianOptions() {
                if (!unitSelect) {
                    return;
                }

                const selectedUnitId = unitSelect.value;
                const selectedValue = technicianSelect.value;

                allOptions.forEach(function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        option.disabled = false;
                        return;
                    }

                    const optionUnitId = option.getAttribute('data-unit-id');
                    const isMatch = !selectedUnitId || optionUnitId === selectedUnitId;

                    option.hidden = !isMatch;
                    option.disabled = !isMatch;
                });

                const currentOption = technicianSelect.options[technicianSelect.selectedIndex];

                if (currentOption && currentOption.disabled) {
                    technicianSelect.value = '';
                }

                if (selectedValue) {
                    const selectedOptionStillAvailable = allOptions.some(function (option) {
                        return option.value === selectedValue && !option.disabled;
                    });

                    if (!selectedOptionStillAvailable) {
                        technicianSelect.value = '';
                    }
                }
            }

            if (unitSelect) {
                unitSelect.addEventListener('change', filterTechnicianOptions);
                filterTechnicianOptions();
            }
        });
    </script>
</x-app-layout>