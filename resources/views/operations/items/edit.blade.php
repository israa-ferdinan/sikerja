<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-slate-800">
                        Edit Item Operasional
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Perubahan master item hanya berlaku untuk rekap baru, arsip rekap lama tetap memakai snapshot saat rekap dibuat.
                    </p>
                </div>

                <a
                    href="{{ route('operations.items.index', ['category' => $item->category]) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    Kembali
                </a>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-semibold">Ada input yang belum sesuai.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('operations.items.update', $item) }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PATCH')

                @if(auth()->user()->isAdmin())
                    <div>
                        <label for="unit_id" class="mb-1 block text-sm font-medium text-slate-700">
                            Unit <span class="text-red-500">*</span>
                        </label>
                        <select id="unit_id" name="unit_id" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                            <option value="">Pilih Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected((string) old('unit_id', $item->unit_id) === (string) $unit->id)>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Unit
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $item->unit?->name ?? '-' }}
                        </p>
                    </div>
                @endif

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label for="category" class="mb-1 block text-sm font-medium text-slate-700">
                            Jenis Item <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                            <option value="">Pilih Jenis Item</option>
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('category', $item->category) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">
                            Nama Item / Perangkat <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $item->name) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                            required
                        >
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="location" class="mb-1 block text-sm font-medium text-slate-700">
                            Lokasi
                        </label>
                        <input
                            id="location"
                            type="text"
                            name="location"
                            value="{{ old('location', $item->location) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="identifier" class="mb-1 block text-sm font-medium text-slate-700">
                            Kode / No PC / Identifier
                        </label>
                        <input
                            id="identifier"
                            type="text"
                            name="identifier"
                            value="{{ old('identifier', $item->identifier) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label for="brand" class="mb-1 block text-sm font-medium text-slate-700">
                            Merk
                        </label>
                        <input
                            id="brand"
                            type="text"
                            name="brand"
                            value="{{ old('brand', $item->brand) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="model" class="mb-1 block text-sm font-medium text-slate-700">
                            Model / Type
                        </label>
                        <input
                            id="model"
                            type="text"
                            name="model"
                            value="{{ old('model', $item->model) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="year" class="mb-1 block text-sm font-medium text-slate-700">
                            Tahun
                        </label>
                        <input
                            id="year"
                            type="text"
                            name="year"
                            value="{{ old('year', $item->year) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700">
                            Jumlah
                        </label>
                        <input
                            id="quantity"
                            type="number"
                            min="0"
                            name="quantity"
                            value="{{ old('quantity', $item->quantity) }}"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium text-slate-700">
                        Keterangan
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                    >{{ old('description', $item->description) }}</textarea>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                    Edit master item tidak otomatis mengubah item di rekap yang sudah pernah dibuat.
                    Jika item ini dipakai pada rekap baru, data terbaru akan dipakai sebagai snapshot baru.
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('operations.items.index', ['category' => $item->category]) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>