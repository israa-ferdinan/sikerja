<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="clipboard-list" class="h-4 w-4" />
                        Pengendalian
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Tambah Tindak Lanjut Evaluasi
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Catat tindak lanjut hasil evaluasi, tentukan unit, PIC,
                        tenggat waktu, arahan, dan catatan progres awal.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="circle-dot" class="h-3.5 w-3.5" />
                            Status awal: Open
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            PIC akan menerima notifikasi
                        </span>
                    </div>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ route('documentation.control.follow-ups.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>
                </div>
            </div>
        </section>

        {{-- SUMBER EVALUASI TERPILIH --}}
        @if ($selectedEvaluationRecord)
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="search-check" class="h-5 w-5" />
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-sm font-semibold text-sky-900">
                            Dibuat dari Hasil Evaluasi
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-sky-700">
                            Tindak lanjut ini akan dikaitkan dengan:
                        </p>

                        <p class="mt-2 break-words text-sm font-semibold text-sky-950">
                            {{ $selectedEvaluationRecord->title }}
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-sky-700">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1 ring-1 ring-inset ring-sky-200">
                                <x-icon name="calendar" class="h-3.5 w-3.5" />
                                {{ $selectedEvaluationRecord->evaluation_date?->format('d M Y') ?? 'Tanggal belum diisi' }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1 ring-1 ring-inset ring-sky-200">
                                <x-icon name="building-2" class="h-3.5 w-3.5" />
                                {{ $selectedEvaluationRecord->unit?->name ?? 'Unit terkait' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- VALIDATION SUMMARY --}}
        @if ($errors->any())
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-rose-900">
                            Data belum dapat disimpan
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
                action="{{ route('documentation.control.follow-ups.store') }}"
                class="space-y-6"
                x-data="{
                    evaluations: @js(
                        $evaluationRecords->mapWithKeys(fn ($record) => [
                            (string) $record->id => [
                                'title' => $record->title,
                                'recommendation' => $record->recommendation,
                            ],
                        ])
                    ),
                    selectedEvaluationId: @js(
                        (string) old(
                            'evaluation_record_id',
                            $selectedEvaluationRecord?->id ?? ''
                        )
                    ),
                    title: @js(
                        old(
                            'title',
                            $selectedEvaluationRecord
                                ? 'Tindak Lanjut - ' . $selectedEvaluationRecord->title
                                : ''
                        )
                    ),
                    recommendation: @js(
                        old(
                            'recommendation',
                            $selectedEvaluationRecord?->recommendation ?? ''
                        )
                    ),
                    generatedTitle: @js(
                        $selectedEvaluationRecord
                            ? 'Tindak Lanjut - ' . $selectedEvaluationRecord->title
                            : ''
                    ),

                    applyEvaluation() {
                        const evaluation = this.evaluations[this.selectedEvaluationId];

                        if (! evaluation) {
                            if (this.title === this.generatedTitle) {
                                this.title = '';
                            }

                            this.generatedTitle = '';
                            return;
                        }

                        const nextGeneratedTitle = `Tindak Lanjut - ${evaluation.title}`;

                        if (
                            this.title.trim() === ''
                            || this.title === this.generatedTitle
                        ) {
                            this.title = nextGeneratedTitle;
                        }

                        if (this.recommendation.trim() === '') {
                            this.recommendation = evaluation.recommendation ?? '';
                        }

                        this.generatedTitle = nextGeneratedTitle;
                    }
                }"
            >
            @csrf

            {{-- KETERKAITAN DAN IDENTITAS --}}
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
                            Tentukan unit, sumber evaluasi, judul, dan uraian pekerjaan.
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
                                        (string) old(
                                            'unit_id',
                                            $selectedEvaluationRecord?->unit_id
                                        ) === (string) $unit->id
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
                            x-model="selectedEvaluationId"
                            x-on:change="applyEvaluation()"
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
                                            $selectedEvaluationRecord?->id
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
                            Kosongkan bila tindak lanjut tidak berasal dari hasil evaluasi tertentu.
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
                            x-model="title"
                            required
                            maxlength="255"
                            placeholder="Contoh: Koordinasi perbaikan dokumentasi backup server"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
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
                            placeholder="Jelaskan pekerjaan, masalah yang perlu ditangani, dan hasil yang diharapkan"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('description') }}</textarea>

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
                            Tentukan PIC dan target waktu penyelesaian tindak lanjut.
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
                                        (string) old('pic_user_id')
                                        === (string) $picUser->id
                                    )
                                >
                                    {{ $picUser->employee?->name ?? $picUser->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            PIC yang dipilih akan menerima notifikasi penugasan.
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
                            value="{{ old('due_date') }}"
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
                            Arahan dan Progres Awal
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Tambahkan rekomendasi serta catatan perkembangan bila pekerjaan sudah dimulai.
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
                            x-model="recommendation"
                            placeholder="Tuliskan arahan atau rekomendasi yang perlu dilaksanakan"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        ></textarea>

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
                            Catatan Progres Awal
                        </label>

                        <textarea
                            id="progress_note"
                            name="progress_note"
                            rows="5"
                            placeholder="Opsional. Isi bila tindak lanjut sudah mulai berjalan"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('progress_note') }}</textarea>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Mengisi progres awal tidak langsung mengubah status dari Open.
                        </p>

                        @error('progress_note')
                            <p class="mt-2 text-sm font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- INFO LIFECYCLE --}}
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="activity" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-sky-900">
                            Tindak lanjut disimpan dengan status Open
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-sky-700">
                            Setelah tersimpan, status dan progres dapat diperbarui dari halaman detail.
                            Status Selesai akan mengunci perubahan data, progres, dan penambahan surat baru.
                        </p>
                    </div>
                </div>
            </section>

            {{-- AKSI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('documentation.control.follow-ups.index') }}"
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
                        Simpan Tindak Lanjut
                    </button>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>