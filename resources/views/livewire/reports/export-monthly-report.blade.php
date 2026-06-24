<div class="space-y-6">
    <form wire:submit.prevent="export" class="space-y-6">
        <x-page-hero
            badge="Rekap Administrasi"
            title="Siapkan rekap laporan bulanan dalam format Excel"
            description="Pilih periode dan unit kerja untuk melihat preview ringkasan pegawai sebelum data laporan diexport menjadi file Excel."
            icon="file-spreadsheet"
        >
            <x-slot:aside>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow-sm backdrop-blur">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-cyan-100">
                                Preview Rekap
                            </p>
                            <p class="mt-2 text-3xl font-bold text-white">
                                {{ number_format($this->summary['total_reports']) }}
                            </p>
                            <p class="mt-1 text-xs leading-5 text-slate-300">
                                laporan pada filter aktif
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-200">
                            <x-icon name="file-spreadsheet" class="h-6 w-6" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                            Bulan: {{ $month }}
                        </span>

                        <span class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                            Tahun: {{ $year }}
                        </span>
                    </div>
                </div>
            </x-slot:aside>
        </x-page-hero>

        {{-- Filter Export --}}
        <x-ui.card padding="p-5">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <x-icon name="filter" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Filter Export
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Tentukan periode dan unit kerja sebelum export laporan bulanan.
                        </p>
                    </div>
                </div>

                <div
                    wire:loading.flex
                    wire:target="month,year,unit_id"
                    class="items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-4 py-2 text-xs font-semibold text-cyan-700"
                >
                    <span class="h-3 w-3 animate-spin rounded-full border-2 border-cyan-600 border-t-transparent"></span>
                    Memuat preview...
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">
                <div class="xl:col-span-3">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Bulan
                    </label>

                    <select
                        wire:model.live="month"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                    >
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>

                    @error('month')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="xl:col-span-3">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Tahun
                    </label>

                    <select
                        wire:model.live="year"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                    >
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>

                    @error('year')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="xl:col-span-4">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Unit
                    </label>

                    @if (auth()->user()->role?->name === 'admin')
                        <select
                            wire:model.live="unit_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Semua Unit</option>

                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <select
                            wire:model="unit_id"
                            disabled
                            class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500 shadow-sm"
                        >
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('unit_id')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end xl:col-span-2">
                    <button
                        type="submit"
                        @disabled($this->summary['total_reports'] === 0)
                        wire:loading.attr="disabled"
                        wire:target="export"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-4
                        {{ $this->summary['total_reports'] === 0
                            ? 'cursor-not-allowed bg-slate-200 text-slate-500 focus:ring-slate-100'
                            : 'bg-slate-950 text-white hover:bg-slate-800 focus:ring-slate-200' }}"
                    >
                        <x-icon wire:loading.remove wire:target="export" name="download" class="h-4 w-4" />

                        <span wire:loading.remove wire:target="export">
                            Export Excel
                        </span>

                        <span wire:loading.flex wire:target="export" class="items-center gap-2">
                            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Export...
                        </span>
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                Link foto bukti kegiatan pada file Excel menggunakan URL terlindungi. Pengguna tetap harus login dan hanya dapat membuka foto sesuai hak aksesnya.
            </div>

            @if (session('success') || session('error') || session('warning'))
                <div class="mt-4 space-y-2">
                    @if (session('success'))
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="rounded-2xl border border-yellow-100 bg-yellow-50 px-4 py-3 text-sm font-medium text-yellow-700">
                            {{ session('warning') }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                            <x-icon name="{{ $this->approvalStatus['icon'] }}" class="h-5 w-5" />
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900">
                                    Status Finalisasi Bulanan
                                </h3>

                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $this->approvalStatus['class'] }}">
                                    {{ $this->approvalStatus['label'] }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                {{ $this->approvalStatus['description'] }}
                            </p>

                            @if ($this->approval?->status === 'approved')
                                <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                                    <div>
                                        <span class="font-semibold text-slate-800">Disetujui oleh:</span>
                                        {{ $this->approval->approver_name ?? '-' }}
                                    </div>

                                    <div>
                                        <span class="font-semibold text-slate-800">Tanggal:</span>
                                        {{ $this->approval->approved_at?->format('d/m/Y H:i') ?? '-' }}
                                    </div>

                                    <div>
                                        <span class="font-semibold text-slate-800">Jabatan:</span>
                                        {{ $this->approval->approver_position ?? '-' }}
                                    </div>

                                    <div>
                                        <span class="font-semibold text-slate-800">Unit:</span>
                                        {{ $this->approval->approver_unit_name ?? '-' }}
                                    </div>
                                </div>
                            @endif

                            @if ($this->approval?->status === 'cancelled')
                                <div class="mt-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    <span class="font-semibold">Alasan batal:</span>
                                    {{ $this->approval->cancel_reason ?? '-' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 lg:min-w-64">
                        @if (auth()->user()->role?->name === 'admin')
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                Admin dapat memantau status finalisasi. Approval dilakukan oleh Kanit unit terkait.
                            </div>
                        @endif

                        @if ($this->canApprove)
                            <button
                                type="button"
                                wire:click="approveMonthlyReport"
                                wire:confirm="Finalisasi laporan bulan ini? Setelah final, laporan pada periode ini akan terkunci."
                                wire:loading.attr="disabled"
                                wire:target="approveMonthlyReport"
                                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-sm ring-1 ring-slate-900 transition hover:bg-slate-800 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="approveMonthlyReport" class="inline-flex items-center gap-2">
                                    <x-icon name="badge-check" class="h-4 w-4 text-white" />
                                    <span>Finalisasi Bulanan</span>
                                </span>

                                <span wire:loading wire:target="approveMonthlyReport">
                                    Memfinalisasi...
                                </span>
                            </button>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Setelah finalisasi, laporan pada periode ini akan terkunci dan export akan memakai tanda tangan Kanit.
                            </p>
                        @endif

                        @if ($this->canCancelApproval)
                            <div class="rounded-xl border border-rose-100 bg-rose-50 p-3">
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-rose-700">
                                    Alasan Batal Finalisasi
                                </label>

                                <textarea
                                    wire:model.defer="cancel_reason"
                                    rows="3"
                                    class="w-full rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm outline-none transition focus:border-rose-400 focus:ring-4 focus:ring-rose-100"
                                    placeholder="Contoh: Ada koreksi laporan pegawai yang perlu diperbaiki."
                                ></textarea>

                                @error('cancel_reason')
                                    <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                @enderror

                                <button
                                    type="button"
                                    wire:click="cancelMonthlyApproval"
                                    wire:confirm="Batalkan finalisasi laporan bulan ini? Periode ini akan bisa diedit kembali."
                                    wire:loading.attr="disabled"
                                    wire:target="cancelMonthlyApproval"
                                    class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <x-icon wire:loading.remove wire:target="cancelMonthlyApproval" name="x-circle" class="h-4 w-4" />

                                    <span wire:loading.remove wire:target="cancelMonthlyApproval">
                                        Batalkan Finalisasi
                                    </span>

                                    <span wire:loading wire:target="cancelMonthlyApproval">
                                        Membatalkan...
                                    </span>
                                </button>
                            </div>
                        @endif

                        @if (auth()->user()->role?->name === 'kanit' && $this->summary['total_reports'] === 0)
                            <div class="rounded-xl border border-yellow-100 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
                                Finalisasi belum bisa dilakukan karena belum ada laporan pada periode ini.
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </x-ui.card>

        {{-- Summary --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-ui.card padding="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Laporan
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ number_format($this->summary['total_reports']) }}
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
                        <p class="text-sm font-semibold text-slate-500">
                            Total Foto
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ number_format($this->summary['total_photos']) }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <x-icon name="image" class="h-6 w-6" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card padding="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Pegawai Mengisi
                        </p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ number_format($this->summary['total_employees']) }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                        <x-icon name="user-check" class="h-6 w-6" />
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Preview --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <x-icon name="bar-chart-3" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Preview Ringkasan Pegawai
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Data ini mengikuti filter bulan, tahun, dan unit yang dipilih.
                        </p>
                    </div>
                </div>

                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                    {{ number_format(count($this->summary['employees'])) }} pegawai
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                No
                            </th>
                            <th class="min-w-[220px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Pegawai
                            </th>
                            <th class="min-w-[180px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Jabatan
                            </th>
                            <th class="min-w-[180px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Unit
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                Total Laporan
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                Total Foto
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($this->summary['employees'] as $employee)
                            <tr class="transition hover:bg-cyan-50/30">
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">
                                        {{ $employee['employee_name'] }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $employee['position_name'] }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $employee['unit_name'] }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                                        {{ number_format($employee['total_reports']) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                                        {{ number_format($employee['total_photos']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="file-text" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-bold text-slate-900">
                                        Belum ada data laporan
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Belum ada data laporan untuk filter yang dipilih.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>