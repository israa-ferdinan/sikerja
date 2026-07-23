<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-semibold leading-tight text-slate-800">
                            Detail Rekap Operasional
                        </h2>

                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                            {{ $record->record_code }}
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $record->title }}
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
                        href="{{ route('operations.forms.export.excel', $record) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100"
                    >
                        Export Excel
                    </a>

                    @if($record->canSubmit())
                        <form method="POST" action="{{ route('operations.forms.submit', $record) }}">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                Ajukan
                            </button>
                        </form>
                    @endif

                    @if($record->canVerify() && (auth()->user()->isAdmin() || auth()->user()->isKanit() || auth()->user()->isGkm()))
                        <form method="POST" action="{{ route('operations.forms.verify', $record) }}">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                            >
                                Verifikasi
                            </button>
                        </form>
                    @endif

                    @if($record->canCancel() && (auth()->user()->isAdmin() || auth()->user()->isKanit() || auth()->user()->isGkm()))
                        <form method="POST" action="{{ route('operations.forms.cancel', $record) }}">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="cancel_reason" value="">

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100"
                                onclick="return confirm('Batalkan rekap ini?')"
                            >
                                Batalkan
                            </button>
                        </form>
                    @endif

                    @if($record->isDeletable() && (auth()->user()->isAdmin() || auth()->user()->isKanit() || auth()->user()->isGkm()))
                        <form method="POST" action="{{ route('operations.forms.destroy', $record) }}">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50"
                                onclick="return confirm('Hapus rekap ini? Data detail item juga akan terhapus.')"
                            >
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Jenis Rekap
                    </p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $record->category_label }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Periode
                    </p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        @if($record->period_month && $record->period_year)
                            {{ $monthOptions[$record->period_month] ?? $record->period_month }} {{ $record->period_year }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Unit
                    </p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $record->unit?->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Status
                    </p>

                    @php
                        $statusClass = match ($record->status) {
                            \App\Models\OperationalRecord::STATUS_DRAFT => 'bg-slate-100 text-slate-700',
                            \App\Models\OperationalRecord::STATUS_SUBMITTED => 'bg-blue-100 text-blue-700',
                            \App\Models\OperationalRecord::STATUS_VERIFIED => 'bg-emerald-100 text-emerald-700',
                            \App\Models\OperationalRecord::STATUS_CANCELLED => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ $record->status_label }}
                    </span>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Dibuat Oleh
                    </p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $record->createdByUser?->name ?? '-' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $record->created_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Diajukan
                    </p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $record->submitted_at ? $record->submitted_at->format('d M Y H:i') : '-' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Diverifikasi / Dibatalkan
                    </p>

                    @if($record->verified_at)
                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $record->verifiedByUser?->name ?? '-' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $record->verified_at->format('d M Y H:i') }}
                        </p>
                    @elseif($record->cancelled_at)
                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $record->cancelledByUser?->name ?? '-' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $record->cancelled_at->format('d M Y H:i') }}
                        </p>
                    @else
                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            -
                        </p>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Teknisi / Petugas
                    </p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $record->technician?->name ?? '-' }}
                    </p>
                    @if($record->technician?->nip)
                        <p class="mt-1 text-xs text-slate-500">
                            NIP. {{ $record->technician->nip }}
                        </p>
                    @endif
                </div>
            </div>

            @if($record->notes)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Catatan Umum
                    </p>
                    <p class="mt-2 text-sm leading-6 text-slate-700">
                        {{ $record->notes }}
                    </p>
                </div>
            @endif

            @if(! $record->isEditable())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Rekap ini sudah tidak dalam status Draft, sehingga detail item bersifat read-only.
                </div>
            @endif

            @if($record->isEditable())
                <div
                    x-data="{ openAddItem: {{ $errors->any() ? 'true' : 'false' }} }"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">
                                Tambah Item ke Rekap Ini
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Item baru akan otomatis masuk ke master item aktif dan detail rekap ini.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="openAddItem = !openAddItem"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            <x-icon name="plus" class="mr-2 h-4 w-4" />
                            Tambah Item
                        </button>
                    </div>

                    <div x-show="openAddItem" x-transition style="display: none;" class="p-5">
                        <form method="POST" action="{{ route('operations.forms.items.store', $record) }}" class="space-y-5">
                            @csrf

                            <div class="grid gap-4 lg:grid-cols-3">
                                <div>
                                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">
                                        Nama Item / Perangkat <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="Contoh: AP Baru / RPK1-PC31"
                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                        required
                                    >
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="location" class="mb-1 block text-sm font-medium text-slate-700">
                                        Lokasi
                                    </label>
                                    <input
                                        id="location"
                                        type="text"
                                        name="location"
                                        value="{{ old('location') }}"
                                        placeholder="Contoh: Lab 1 / Dormitory"
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
                                        placeholder="Contoh: RPK1-PC31 / AP-DORM-02"
                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                    >
                                </div>
                            </div>

                            <div class="grid gap-4 lg:grid-cols-5">
                                <div>
                                    <label for="brand" class="mb-1 block text-sm font-medium text-slate-700">
                                        Merk
                                    </label>
                                    <input
                                        id="brand"
                                        type="text"
                                        name="brand"
                                        value="{{ old('brand') }}"
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
                                        value="{{ old('model') }}"
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
                                        value="{{ old('year') }}"
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
                                        value="{{ old('quantity', 1) }}"
                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                    >
                                </div>

                                <div>
                                    <label for="condition_status" class="mb-1 block text-sm font-medium text-slate-700">
                                        Kondisi Awal <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="condition_status"
                                        name="condition_status"
                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                        required
                                    >
                                        @foreach($conditionOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('condition_status', \App\Models\OperationalRecordItem::CONDITION_NORMAL) === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 lg:grid-cols-2">
                                <div>
                                    <label for="description" class="mb-1 block text-sm font-medium text-slate-700">
                                        Keterangan
                                    </label>
                                    <textarea
                                        id="description"
                                        name="description"
                                        rows="3"
                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                        placeholder="Keterangan item/perangkat..."
                                    >{{ old('description') }}</textarea>
                                </div>

                                <div>
                                    <label for="action_taken" class="mb-1 block text-sm font-medium text-slate-700">
                                        Tindakan
                                    </label>
                                    <textarea
                                        id="action_taken"
                                        name="action_taken"
                                        rows="3"
                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                        placeholder="Tindakan awal jika ada..."
                                    >{{ old('action_taken') }}</textarea>
                                </div>
                            </div>

                            @if($record->category === \App\Models\OperationalItem::CATEGORY_LAB_CHECK)
                                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                                    Untuk Pemeriksaan Lab, status komponen item baru akan otomatis dibuat default <strong>Baik</strong>.
                                    Setelah item tersimpan, komponen bisa diubah dari detail item.
                                </div>
                            @endif

                            <div class="flex justify-end gap-2 border-t border-slate-100 pt-5">
                                <button
                                    type="button"
                                    @click="openAddItem = false"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                >
                                    Simpan Item Baru
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-900">
                        Detail Item Rekap
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Isi kondisi, keterangan, dan tindakan untuk item operasional.
                    </p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($record->items as $recordItem)
                        <div class="p-5">
                            <form method="POST" action="{{ route('operations.forms.items.update', [$record, $recordItem]) }}" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-4 lg:grid-cols-12">
                                    <div class="lg:col-span-3">
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $recordItem->item_name }}
                                        </p>

                                        <div class="mt-1 space-y-1 text-xs text-slate-500">
                                            <p>Lokasi: {{ $recordItem->item_location ?? '-' }}</p>
                                            <p>Kode: {{ $recordItem->item_identifier ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Kondisi
                                        </label>
                                        <select
                                            name="condition_status"
                                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                            @disabled(! $record->isEditable())
                                        >
                                            @foreach($conditionOptions as $value => $label)
                                                <option value="{{ $value }}" @selected($recordItem->condition_status === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="lg:col-span-3">
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Keterangan
                                        </label>
                                        <textarea
                                            name="description"
                                            rows="3"
                                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                            placeholder="Keterangan kondisi..."
                                            @disabled(! $record->isEditable())
                                        >{{ old('description', $recordItem->description) }}</textarea>
                                    </div>

                                    <div class="lg:col-span-3">
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Tindakan
                                        </label>
                                        <textarea
                                            name="action_taken"
                                            rows="3"
                                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                            placeholder="Tindakan yang dilakukan..."
                                            @disabled(! $record->isEditable())
                                        >{{ old('action_taken', $recordItem->action_taken) }}</textarea>
                                    </div>

                                    <div class="flex items-end justify-end lg:col-span-1">
                                        @if($record->isEditable())
                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800"
                                            >
                                                Simpan
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Read-only
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($record->category === \App\Models\OperationalItem::CATEGORY_LAB_CHECK)
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            Status Komponen Lab
                                        </p>

                                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                                            @foreach($labComponentKeys as $key => $label)
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-600">
                                                        {{ $label }}
                                                    </label>
                                                    <select
                                                        name="component_status[{{ $key }}]"
                                                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm"
                                                        @disabled(! $record->isEditable())
                                                    >
                                                        @foreach($componentOptions as $value => $componentLabel)
                                                            <option
                                                                value="{{ $value }}"
                                                                @selected(($recordItem->component_status[$key] ?? \App\Models\OperationalRecordItem::COMPONENT_GOOD) === $value)
                                                            >
                                                                {{ $componentLabel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @empty
                        <div class="px-5 py-14 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                <x-icon name="file-text" class="h-6 w-6" />
                            </div>

                            <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                Belum ada item rekap
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Detail item akan dibuat otomatis dari master item aktif. Jika ada perangkat baru, gunakan tombol Tambah Item di atas.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>