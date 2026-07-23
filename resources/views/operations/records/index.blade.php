<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-slate-800">
                        Form Operasional
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Rekap operasional berbasis item untuk jaringan, inventaris lab, dan pemeriksaan lab.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('operations.items.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <x-icon name="file-text" class="mr-2 h-4 w-4" />
                        Master Item
                    </a>

                    <a
                        href="{{ route('operations.forms.create', request()->only('category')) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        <x-icon name="plus" class="mr-2 h-4 w-4" />
                        Buat Rekap
                    </a>
                </div>
            </div>
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Total
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $summary['total'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Draft
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $summary['draft'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                        Diajukan
                    </p>
                    <p class="mt-2 text-2xl font-bold text-blue-950">
                        {{ $summary['submitted'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                        Terverifikasi
                    </p>
                    <p class="mt-2 text-2xl font-bold text-emerald-950">
                        {{ $summary['verified'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-red-100 bg-red-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-red-600">
                        Dibatalkan
                    </p>
                    <p class="mt-2 text-2xl font-bold text-red-950">
                        {{ $summary['cancelled'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">
                        Bulan Ini
                    </p>
                    <p class="mt-2 text-2xl font-bold text-amber-950">
                        {{ $summary['bulan_ini'] }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('operations.forms.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                    <div>
                        <label for="category" class="mb-1 block text-xs font-medium text-slate-600">
                            Jenis Rekap
                        </label>
                        <select
                            id="category"
                            name="category"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Semua Jenis</option>
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('category') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-xs font-medium text-slate-600">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="month" class="mb-1 block text-xs font-medium text-slate-600">
                            Bulan
                        </label>
                        <select
                            id="month"
                            name="month"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Semua Bulan</option>
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}" @selected((string) request('month') === (string) $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="year" class="mb-1 block text-xs font-medium text-slate-600">
                            Tahun
                        </label>
                        <select
                            id="year"
                            name="year"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Semua Tahun</option>
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div>
                            <label for="unit_id" class="mb-1 block text-xs font-medium text-slate-600">
                                Unit
                            </label>
                            <select
                                id="unit_id"
                                name="unit_id"
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                            >
                                <option value="">Semua Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" @selected((string) request('unit_id') === (string) $unit->id)>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label for="search" class="mb-1 block text-xs font-medium text-slate-600">
                            Search
                        </label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Kode/judul/catatan..."
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                    </div>

                    <div class="flex items-end gap-2 xl:col-span-6">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('operations.forms.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-900">
                        Daftar Rekap Operasional
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Data rekap checklist item operasional Unit SIM/TI.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Kode
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Jenis
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Judul
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Periode
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Unit
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Dibuat Oleh
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($records as $record)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 align-top">
                                        <div class="font-mono text-sm font-semibold text-slate-900">
                                            {{ $record->record_code }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $record->created_at?->format('d M Y') }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $record->category_label }}
                                        </span>
                                    </td>

                                    <td class="max-w-sm px-4 py-4 align-top">
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $record->title }}
                                        </div>

                                        @if($record->notes)
                                            <div class="mt-1 line-clamp-2 text-xs text-slate-500">
                                                {{ $record->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <div class="text-sm text-slate-700">
                                            @if($record->period_month && $record->period_year)
                                                {{ $monthOptions[$record->period_month] ?? $record->period_month }} {{ $record->period_year }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                        @if($record->record_date)
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $record->record_date->format('d M Y') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <div class="text-sm text-slate-700">
                                            {{ $record->unit?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        @php
                                            $statusClass = match ($record->status) {
                                                \App\Models\OperationalRecord::STATUS_DRAFT => 'bg-slate-100 text-slate-700',
                                                \App\Models\OperationalRecord::STATUS_SUBMITTED => 'bg-blue-100 text-blue-700',
                                                \App\Models\OperationalRecord::STATUS_VERIFIED => 'bg-emerald-100 text-emerald-700',
                                                \App\Models\OperationalRecord::STATUS_CANCELLED => 'bg-red-100 text-red-700',
                                                default => 'bg-slate-100 text-slate-700',
                                            };
                                        @endphp

                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $record->status_label }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <div class="text-sm text-slate-700">
                                            {{ $record->createdByUser?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-right align-top">
                                        <div class="flex justify-end gap-2">
                                            <a
                                                href="{{ route('operations.forms.show', $record) }}"
                                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Detail
                                            </a>

                                            @if($record->isDeletable() && (auth()->user()->isAdmin() || auth()->user()->isKanit() || auth()->user()->isGkm()))
                                                <form method="POST" action="{{ route('operations.forms.destroy', $record) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50"
                                                        onclick="return confirm('Hapus rekap ini?')"
                                                    >
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                            <x-icon name="file-text" class="h-6 w-6" />
                                        </div>

                                        <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                            Belum ada rekap operasional
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Rekap jaringan, inventaris lab, dan pemeriksaan lab akan muncul di sini.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($records->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>