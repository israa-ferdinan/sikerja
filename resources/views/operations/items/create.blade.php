<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-slate-800">
                        Tambah Item Operasional
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Tambahkan perangkat, titik jaringan, PC, atau item pemeriksaan.
                    </p>
                </div>

                <a
                    href="{{ route('operations.items.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('operations.items.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
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
                        Jenis Item <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" class="w-full rounded-xl border-slate-300 text-sm shadow-sm" required>
                        <option value="">Pilih Jenis Item</option>
                        @foreach($categoryOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', $selectedCategory) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">
                        Nama Item / Perangkat <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Access Point Dormitory / RPK1-PC01"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                            value="{{ old('location') }}"
                            placeholder="Contoh: Lab 1 / Gedung Utama Lt. 2"
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
                            value="{{ old('identifier') }}"
                            placeholder="Contoh: RPK1-PC01 / AP-DORM-01"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                        >
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label for="brand" class="mb-1 block text-sm font-medium text-slate-700">
                            Merk
                        </label>
                        <input id="brand" type="text" name="brand" value="{{ old('brand') }}" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
                    </div>

                    <div>
                        <label for="model" class="mb-1 block text-sm font-medium text-slate-700">
                            Model / Type
                        </label>
                        <input id="model" type="text" name="model" value="{{ old('model') }}" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
                    </div>

                    <div>
                        <label for="year" class="mb-1 block text-sm font-medium text-slate-700">
                            Tahun
                        </label>
                        <input id="year" type="text" name="year" value="{{ old('year') }}" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
                    </div>

                    <div>
                        <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700">
                            Jumlah
                        </label>
                        <input id="quantity" type="number" min="0" name="quantity" value="{{ old('quantity', 1) }}" class="w-full rounded-xl border-slate-300 text-sm shadow-sm">
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
                        placeholder="Catatan tambahan item/perangkat..."
                    >{{ old('description') }}</textarea>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('operations.items.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Simpan Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>