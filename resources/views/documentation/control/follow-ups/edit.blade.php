<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="edit-3" class="h-4 w-4" />
                        Pengendalian
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Edit Tindak Lanjut Evaluasi
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Perbarui informasi tindak lanjut, sumber evaluasi, PIC,
                        tenggat waktu, arahan, dan catatan progres.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            Status: {{ $followUp->statusLabel() }}
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            Status Selesai tidak dapat diedit
                        </span>
                    </div>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ route('documentation.control.follow-ups.show', $followUp) }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali ke Detail
                    </a>
                </div>
            </div>
        </section>

        {{-- VALIDATION SUMMARY --}}
        @if ($errors->any())
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-rose-900">
                            Perubahan belum dapat disimpan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-rose-700">
                            Periksa kembali kolom yang ditandai di bawah.
                        </p>
                    </div>
                </div>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('documentation.control.follow-ups.update', $followUp) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- IDENTITAS TINDAK LANJUT --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="clipboard-list" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Identitas Tindak Lanjut
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Perbarui unit, sumber evaluasi, judul, dan uraian pekerjaan.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label
                            for="unit_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Unit
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            id="unit_id"
                            name="unit_id"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Pilih Unit</option>

                            @foreach ($units as $unit)
                                <option
                                    value="{{ $unit->id }}"
                                    @selected(
                                        (string) old('unit_id', $followUp->unit_id)
                                        === (string) $unit->id
                                    )
                                >
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('unit_id')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="evaluation_record_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Sumber Hasil Evaluasi
                        </label>

                        <select
                            id="evaluation_record_id"
                            name="evaluation_record_id"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Tindak lanjut mandiri
                            </option>

                            @foreach ($evaluationRecords as $record)
                                <option
                                    value="{{ $record->id }}"
                                    @selected(
                                        (string) old(
                                            'evaluation_record_id',
                                            $followUp->evaluation_record_id
                                        ) === (string) $record->id
                                    )
                                >
                                    {{ $record->title }}
                                    —
                                    {{ $record->evaluation_date?->format('d M Y') ?? '-' }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Kosongkan bila tindak lanjut tidak terkait hasil evaluasi tertentu.
                        </p>

                        @error('evaluation_record_id')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label
                            for="title"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Judul Tindak Lanjut
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $followUp->title) }}"
                            required
                            maxlength="255"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('title')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label
                            for="description"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Deskripsi Tindak Lanjut
                            <span class="text-rose-500">*</span>
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('description', $followUp->description) }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- PENUGASAN DAN TENGGAT --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <x-icon name="user-check" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Penugasan dan Tenggat
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Perbarui PIC dan target waktu penyelesaian tindak lanjut.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label
                            for="pic_user_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            PIC
                        </label>

                        <select
                            id="pic_user_id"
                            name="pic_user_id"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Belum ditentukan
                            </option>

                            @foreach ($picUsers as $picUser)
                                <option
                                    value="{{ $picUser->id }}"
                                    @selected(
                                        (string) old(
                                            'pic_user_id',
                                            $followUp->pic_user_id
                                        ) === (string) $picUser->id
                                    )
                                >
                                    {{ $picUser->employee?->name ?? $picUser->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Perubahan PIC akan mengirim notifikasi kepada PIC lama dan PIC baru.
                        </p>

                        @error('pic_user_id')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="due_date"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Tenggat Waktu
                        </label>

                        <input
                            id="due_date"
                            type="date"
                            name="due_date"
                            value="{{ old(
                                'due_date',
                                $followUp->due_date?->format('Y-m-d')
                            ) }}"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Opsional. Tentukan batas waktu penyelesaian pekerjaan.
                        </p>

                        @error('due_date')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ARAHAN DAN PROGRES --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <x-icon name="sticky-note" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Arahan dan Progres
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Perbarui rekomendasi dan catatan perkembangan pekerjaan.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label
                            for="recommendation"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Rekomendasi / Arahan
                        </label>

                        <textarea
                            id="recommendation"
                            name="recommendation"
                            rows="5"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('recommendation', $followUp->recommendation) }}</textarea>

                        @error('recommendation')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="progress_note"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Catatan Progres
                        </label>

                        <textarea
                            id="progress_note"
                            name="progress_note"
                            rows="5"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('progress_note', $followUp->progress_note) }}</textarea>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Mengubah catatan ini tidak otomatis mengubah status tindak lanjut.
                        </p>

                        @error('progress_note')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- INFO STATUS --}}
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="activity" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-sky-900">
                            Status dikelola dari halaman detail
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-sky-700">
                            Halaman ini hanya memperbarui data utama, PIC, tenggat, arahan,
                            dan progres. Perubahan status Open, Dalam Proses, Selesai,
                            atau Dibatalkan dilakukan melalui halaman detail.
                        </p>
                    </div>
                </div>
            </section>

            {{-- AKSI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('documentation.control.follow-ups.show', $followUp) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                    >
                        <x-icon name="check-circle" class="h-4 w-4" />
                        Simpan Perubahan
                    </button>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>