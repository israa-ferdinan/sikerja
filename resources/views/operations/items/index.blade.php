<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-slate-800">
                        Master Item Operasional
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Daftar perangkat, titik jaringan, PC, dan item pemeriksaan operasional.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('operations.forms.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Kembali
                    </a>

                    <a
                        href="{{ route('operations.items.import-form', request()->only('category')) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Import Excel
                    </a>

                    <a
                        href="{{ route('operations.items.create', request()->only('category')) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        <x-icon name="plus" class="mr-2 h-4 w-4" />
                        Tambah Item
                    </a>
                </div>
            </div>
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('operations.items.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label for="category" class="mb-1 block text-xs font-medium text-slate-600">
                            Jenis Item
                        </label>
                        <select id="category" name="category" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
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
                        <select id="status" name="status" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                        </select>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div>
                            <label for="unit_id" class="mb-1 block text-xs font-medium text-slate-600">
                                Unit
                            </label>
                            <select id="unit_id" name="unit_id" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
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
                            placeholder="Nama/lokasi/merk/model..."
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                            Filter
                        </button>

                        <a href="{{ route('operations.items.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Spesifikasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Unit</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($items as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 align-top">
                                        <div class="text-sm font-semibold text-slate-900">{{ $item->name }}</div>
                                        @if($item->identifier)
                                            <div class="mt-1 text-xs text-slate-500">{{ $item->identifier }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $item->category_label }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 align-top text-sm text-slate-700">
                                        {{ $item->location ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <div class="text-sm text-slate-700">
                                            {{ $item->brand ?? '-' }} {{ $item->model ?? '' }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Tahun: {{ $item->year ?? '-' }} · Jumlah: {{ $item->quantity ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 align-top text-sm text-slate-700">
                                        {{ $item->unit?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 align-top">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-right align-top">
                                        <div class="flex justify-end gap-2">
                                            <a
                                                href="{{ route('operations.items.edit', $item) }}"
                                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('operations.items.toggle-active', $item) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-xl border px-3 py-1.5 text-xs font-semibold transition {{ $item->is_active ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}"
                                                    onclick="return confirm('{{ $item->is_active ? 'Nonaktifkan item ini?' : 'Aktifkan item ini?' }}')"
                                                >
                                                    {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                            <x-icon name="file-text" class="h-6 w-6" />
                                        </div>

                                        <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                            Belum ada item operasional
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Tambahkan perangkat, PC, atau titik pemeriksaan terlebih dahulu.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>