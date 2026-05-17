<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Export Laporan Bulanan
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Export rekap laporan kerja harian berdasarkan bulan, tahun, dan unit kerja.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <form wire:submit.prevent="export" class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Bulan
                    </label>
                    <select
                        wire:model.live="month"
                        class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tahun
                    </label>
                    <select
                        wire:model.live="year"
                        class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Unit
                    </label>

                    @if (auth()->user()->role?->name === 'admin')
                        <select
                            wire:model.live="unit_id"
                            class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                            class="w-full rounded-xl border-gray-200 bg-gray-100 text-sm text-gray-600 shadow-sm"
                        >
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('unit_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <button
                    type="submit"
                    @disabled($this->summary['total_reports'] === 0)
                    class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                    {{ $this->summary['total_reports'] === 0
                        ? 'cursor-not-allowed bg-gray-300 text-gray-500'
                        : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                >
                    <span wire:loading.remove wire:target="export">
                        Export Excel
                    </span>

                    <span wire:loading wire:target="export">
                        Menyiapkan Excel...
                    </span>
                </button>
            </div>

            <div wire:loading.flex wire:target="month,year,unit_id"
                class="items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <span class="h-2 w-2 animate-pulse rounded-full bg-blue-600"></span>
                Memuat ulang preview rekap...
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $this->summary['total_reports'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Foto</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $this->summary['total_photos'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Pegawai Mengisi</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $this->summary['total_employees'] }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        Preview Ringkasan Pegawai
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Data ini mengikuti filter bulan, tahun, dan unit yang dipilih.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Pegawai</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jabatan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Unit</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Laporan</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Foto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($this->summary['employees'] as $employee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $employee['employee_name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $employee['position_name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $employee['unit_name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                        {{ $employee['total_reports'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700">
                                        {{ $employee['total_photos'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                        Belum ada data laporan untuk filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>