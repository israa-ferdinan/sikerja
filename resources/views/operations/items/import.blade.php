<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-slate-800">
                        Import Master Item dari Excel
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Upload data perangkat, jaringan, inventaris lab, atau PC dari file Excel lama.
                    </p>
                </div>

                <a
                    href="{{ route('operations.items.index') }}"
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

            <form
                method="POST"
                action="{{ route('operations.items.import') }}"
                enctype="multipart/form-data"
                class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
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
                </div>

                <div>
                    <label for="file" class="mb-1 block text-sm font-medium text-slate-700">
                        File Excel <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="file"
                        type="file"
                        name="file"
                        accept=".xlsx,.xls,.csv"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                        required
                    >
                    <p class="mt-2 text-xs text-slate-500">
                        Format didukung: .xlsx, .xls, .csv. Sistem akan membaca semua sheet.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                    <p class="font-semibold">Catatan import:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        <li>Sheet name akan dipakai sebagai lokasi default jika kolom lokasi kosong.</li>
                        <li>Data duplikat dengan unit, jenis, nama, lokasi, dan identifier yang sama akan dilewati.</li>
                        <li>Baris judul, total, atau tanda tangan akan otomatis dilewati sebisa mungkin.</li>
                    </ul>
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
                        Import Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>