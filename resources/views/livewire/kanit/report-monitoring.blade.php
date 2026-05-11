<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Monitoring Laporan Unit
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Pantau laporan kerja harian pegawai dalam unit Anda.
            </p>
        </div>
    </div>

    <div class="mb-4 rounded-xl border border-yellow-300 bg-yellow-50 p-4">

    {{-- Filter --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Bulan
                </label>

                <select
                    wire:model.change="month"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Tahun
                </label>

                <select
                    wire:model.change="year"
                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($years as $item)
                        <option value="{{ $item }}">
                            {{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button
                    type="button"
                    wire:click="resetFilter"
                    class="w-full rounded-xl bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-200"
                >
                    Reset ke Bulan Ini
                </button>
            </div>
        </div>

        <div class="mt-4 flex flex-col gap-2 rounded-xl bg-gray-50 px-4 py-3 text-xs text-gray-600 sm:flex-row sm:items-center sm:justify-between">
            <div>
                Filter aktif:
                <span class="font-semibold text-gray-800">
                    {{ $months[(int) $month] ?? '-' }} {{ $year }}
                </span>
            </div>

            <div wire:loading class="text-blue-600">
                Memuat data laporan...
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="text-base font-semibold text-gray-900">
                Daftar Laporan Pegawai
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Data laporan hanya menampilkan pegawai dalam unit Kanit.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tanggal
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Pegawai
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Laporan
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tupoksi
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Server / Aplikasi
                        </th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Foto
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ optional($report->report_date)->format('d/m/Y') }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $report->employee->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $report->employee->position ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="max-w-sm">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $report->title }}
                                    </div>
                                    <div class="mt-1 line-clamp-2 text-xs leading-5 text-gray-500">
                                        {{ $report->description }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-700">
                                {{ $report->duty->name ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="text-sm text-gray-800">
                                    {{ $report->server->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $report->application->name ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                    {{ $report->photos->count() }} foto
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                <a href="#"
                                   class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="text-base font-semibold text-gray-900">
                                        Belum ada laporan
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Tidak ada laporan pada bulan dan tahun yang dipilih.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>