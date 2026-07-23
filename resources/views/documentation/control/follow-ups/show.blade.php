<x-app-layout>
        @php
            $user = auth()->user();

            $canManage =
                $user->isAdmin()
                || in_array($user->role?->name, ['kanit', 'gkm'], true);

            $canDelete =
                $canManage
                && in_array(
                    $followUp->status,
                    [
                        \App\Models\ControlFollowUp::STATUS_OPEN,
                        \App\Models\ControlFollowUp::STATUS_CANCELLED,
                    ],
                    true
                );

            $isDone =
                $followUp->status
                === \App\Models\ControlFollowUp::STATUS_DONE;

            $isCancelled =
                $followUp->status
                === \App\Models\ControlFollowUp::STATUS_CANCELLED;

            $isLocked =
                in_array(
                    $followUp->status,
                    [
                        \App\Models\ControlFollowUp::STATUS_DONE,
                        \App\Models\ControlFollowUp::STATUS_CANCELLED,
                    ],
                    true
                );

            $statusHeroClass = match ($followUp->status) {
                \App\Models\ControlFollowUp::STATUS_DONE =>
                    'bg-emerald-400/10 text-emerald-200 ring-emerald-300/20',

                \App\Models\ControlFollowUp::STATUS_CANCELLED =>
                    'bg-rose-400/10 text-rose-200 ring-rose-300/20',

                \App\Models\ControlFollowUp::STATUS_IN_PROGRESS =>
                    'bg-sky-400/10 text-sky-200 ring-sky-300/20',

                default =>
                    'bg-amber-400/10 text-amber-200 ring-amber-300/20',
            };

            $statusHeroIcon = match ($followUp->status) {
                \App\Models\ControlFollowUp::STATUS_DONE => 'check-circle',
                \App\Models\ControlFollowUp::STATUS_CANCELLED => 'x-circle',
                \App\Models\ControlFollowUp::STATUS_IN_PROGRESS => 'activity',
                default => 'circle-dot',
            };
        @endphp
    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="clipboard-list" class="h-4 w-4" />
                            Pengendalian
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusHeroClass }}">
                            <x-icon
                                name="{{ $statusHeroIcon }}"
                                class="h-3.5 w-3.5"
                            />

                            {{ $followUp->statusLabel() }}
                        </span>

                        @if ($isPic)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-400/10 px-3 py-1.5 text-xs font-semibold text-sky-200 ring-1 ring-inset ring-sky-300/20">
                                <x-icon name="user-check" class="h-3.5 w-3.5" />
                                Anda PIC
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-5 break-words text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $followUp->title }}
                    </h1>

                    <p class="mt-4 max-w-4xl text-sm leading-7 text-slate-300 sm:text-base">
                        {{ \Illuminate\Support\Str::limit(
                            $followUp->description,
                            220
                        ) }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="building-2" class="h-3.5 w-3.5" />

                            {{ $followUp->unit?->name ?? 'Unit belum ditentukan' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="user-check" class="h-3.5 w-3.5" />

                            {{ $followUp->picUser?->employee?->name
                                ?? $followUp->picUser?->name
                                ?? 'PIC belum ditentukan' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />

                            {{ $followUp->due_date?->format('d M Y')
                                ?? 'Tenggat belum ditentukan' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="mail" class="h-3.5 w-3.5" />

                            {{ $followUp->letters->count() }} surat
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:flex-wrap lg:max-w-lg lg:justify-end lg:pl-8">
                    <a
                        href="{{ route('documentation.control.follow-ups.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>

                    @if ($canManage && ! $isDone)
                        <a
                            href="{{ route('documentation.control.follow-ups.edit', $followUp) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                        >
                            <x-icon name="edit-3" class="h-4 w-4" />
                            Edit
                        </a>
                    @endif

                    @if ($canDelete)
                        <form
                            x-data
                            method="POST"
                            action="{{ route('documentation.control.follow-ups.destroy', $followUp) }}"
                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                title: 'Hapus Tindak Lanjut?',
                                message: 'Tindak lanjut ini akan dihapus. Surat yang terkait tidak ikut dihapus, tetapi keterkaitannya akan dilepas.',
                                confirmText: 'Ya, Hapus',
                                cancelText: 'Batal',
                                variant: 'danger',
                                onConfirm: () => $el.submit()
                            })"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-2.5 text-sm font-semibold text-rose-100 shadow-sm transition hover:bg-rose-500/20"
                            >
                                <x-icon name="trash-2" class="h-4 w-4" />
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>


        {{-- LIFECYCLE ALERT --}}
        @if ($isDone)
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-emerald-900">
                            Tindak lanjut sudah selesai dan dikunci
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-emerald-700">
                            Data utama, status, catatan progres, dan penambahan surat baru
                            tidak dapat diubah lagi. Data tetap tersedia sebagai arsip
                            pengendalian.
                        </p>

                        @if ($followUp->completed_at)
                            <p class="mt-2 text-xs font-semibold text-emerald-800">
                                Diselesaikan pada
                                {{ $followUp->completed_at->format('d M Y H:i') }}.
                            </p>
                        @endif
                    </div>
                </div>
            </section>
        @elseif ($isCancelled)
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <x-icon name="x-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-rose-900">
                            Tindak lanjut dibatalkan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-rose-700">
                            Catatan progres dikunci selama status Dibatalkan.
                            Admin, Kanit, atau GKM masih dapat mengoreksi data,
                            mengubah status kembali, atau menghapus tindak lanjut ini.
                        </p>

                        @if ($followUp->cancelled_note)
                            <div class="mt-3 rounded-xl border border-rose-200 bg-white/70 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-rose-500">
                                    Alasan Pembatalan
                                </p>

                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-rose-900">
                                    {{ $followUp->cancelled_note }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <div class="space-y-6">
            <div class="space-y-6">
                {{-- INFORMASI TINDAK LANJUT --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Informasi Tindak Lanjut
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Identitas, penugasan, sumber evaluasi, dan tenggat penyelesaian.
                                </p>
                            </div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="border-b border-slate-100 p-5 sm:border-r xl:border-b-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Unit
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $followUp->unit?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5 xl:border-b-0 xl:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                PIC
                            </dt>

                            <dd class="mt-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-900">
                                        {{ $followUp->picUser?->employee?->name
                                            ?? $followUp->picUser?->name
                                            ?? 'Belum ditentukan' }}
                                    </span>

                                    @if ($isPic)
                                        <span class="inline-flex rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                            Anda PIC
                                        </span>
                                    @endif
                                </div>
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tenggat Waktu
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $followUp->due_date?->format('d M Y')
                                    ?? 'Belum ditentukan' }}
                            </dd>
                        </div>

                        <div class="p-5">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status
                            </dt>

                            <dd class="mt-2">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $followUp->statusBadgeClass() }}">
                                    {{ $followUp->statusLabel() }}
                                </span>
                            </dd>
                        </div>
                    </dl>

                    <div class="border-t border-slate-100 p-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Sumber Evaluasi
                                </p>

                                @if ($followUp->evaluationRecord)
                                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                        {{ $followUp->evaluationRecord->title }}
                                    </p>

                                    <a
                                        href="{{ route(
                                            'documentation.evaluasi.show',
                                            $followUp->evaluationRecord
                                        ) }}"
                                        class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900"
                                    >
                                        Buka Hasil Evaluasi
                                        <x-icon name="external-link" class="h-4 w-4" />
                                    </a>
                                @else
                                    <div class="mt-2">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                            <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                                            Tindak lanjut mandiri
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Jumlah Surat Pengendalian
                                </p>

                                <p class="mt-2 text-2xl font-bold text-slate-900">
                                    {{ $followUp->letters->count() }}
                                </p>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Surat masuk dan surat keluar yang terkait dengan tindak lanjut ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- DESKRIPSI --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <x-icon name="file-text" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Deskripsi Tindak Lanjut
                            </h2>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Uraian pekerjaan dan hasil yang diharapkan.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50 p-5 text-sm leading-7 text-slate-700">
                        {!! nl2br(e($followUp->description)) !!}
                    </div>
                </section>

                @if ($followUp->recommendation)
                    {{-- REKOMENDASI --}}
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                <x-icon name="sticky-note" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Rekomendasi / Arahan
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Arahan yang perlu dilaksanakan dalam proses pengendalian.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-amber-100 bg-amber-50/60 p-5 text-sm leading-7 text-slate-700">
                            {!! nl2br(e($followUp->recommendation)) !!}
                        </div>
                    </section>
                @endif

                @if ($followUp->progress_note)
                    {{-- CATATAN PROGRES --}}
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="activity" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Catatan Progres Terakhir
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Perkembangan terakhir pekerjaan tindak lanjut.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-sky-100 bg-sky-50/60 p-5 text-sm leading-7 text-slate-700">
                            {!! nl2br(e($followUp->progress_note)) !!}
                        </div>
                    </section>
                @endif

                @if ($followUp->completed_note)
                    {{-- CATATAN PENYELESAIAN --}}
                    <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm sm:p-6">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <x-icon name="check-circle" class="h-5 w-5" />
                            </div>

                            <div class="min-w-0">
                                <h2 class="text-sm font-semibold text-emerald-900">
                                    Catatan Penyelesaian
                                </h2>

                                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-emerald-800">
                                    {{ $followUp->completed_note }}
                                </p>
                            </div>
                        </div>
                    </section>
                @endif

                <div
                x-data="{
                    openLetterForm: {{ $errors->any() ? 'true' : 'false' }},
                    letterType: '{{ old('letter_type', 'incoming') }}',
                    openForm(type) {
                        this.letterType = type;
                        this.openLetterForm = true;

                        this.$nextTick(() => {
                            document.getElementById('form-surat-pengendalian')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        });
                    }
                }"
                class="space-y-6"
                >

                @php
                    $canEmployeeUpdateProgress =
                        $user->role?->name === 'pegawai'
                        && (int) $followUp->pic_user_id === (int) $user->id;

                    $isProgressLocked = in_array(
                        $followUp->status,
                        [
                            \App\Models\ControlFollowUp::STATUS_DONE,
                            \App\Models\ControlFollowUp::STATUS_CANCELLED,
                        ],
                        true
                    );
                @endphp

                {{-- UPDATE PROGRES PEGAWAI PIC --}}
                @if ($canEmployeeUpdateProgress)
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                        <x-icon name="activity" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Update Progres Tindak Lanjut
                                        </h2>

                                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                            Catat perkembangan terbaru dari pekerjaan yang ditugaskan kepada lo.
                                        </p>
                                    </div>
                                </div>

                                @if ($isProgressLocked)
                                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                        <x-icon name="lock" class="h-3.5 w-3.5" />
                                        Terkunci
                                    </span>
                                @else
                                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                        <x-icon name="user-check" class="h-3.5 w-3.5" />
                                        Anda PIC
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($isProgressLocked)
                            <div class="p-5 sm:p-6">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 ring-1 ring-inset ring-slate-200">
                                            <x-icon name="lock" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                Progres tidak dapat diperbarui
                                            </h3>

                                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                                @if ($isDone)
                                                    Tindak lanjut sudah berstatus Selesai dan dikunci sebagai arsip pengendalian.
                                                @else
                                                    Tindak lanjut sedang berstatus Dibatalkan. Progres dapat diperbarui kembali jika status diaktifkan oleh Admin, Kanit, atau GKM.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form
                                method="POST"
                                action="{{ route(
                                    'documentation.control.follow-ups.update-progress',
                                    $followUp
                                ) }}"
                                class="space-y-5 p-5 sm:p-6"
                            >
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label
                                        for="employee_progress_note"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Catatan Progres Terbaru
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <textarea
                                        id="employee_progress_note"
                                        name="progress_note"
                                        rows="6"
                                        required
                                        placeholder="Tuliskan pekerjaan yang sudah dilakukan, kendala, dan langkah berikutnya"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    >{{ old('progress_note', $followUp->progress_note) }}</textarea>

                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                        Saat progres pertama kali disimpan, status Open otomatis berubah menjadi Dalam Proses.
                                    </p>

                                    @error('progress_note')
                                        <p class="mt-2 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="flex justify-end border-t border-slate-100 pt-5">
                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 sm:w-auto"
                                    >
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                        Simpan Progres
                                    </button>
                                </div>
                            </form>
                        @endif
                    </section>
                @endif

                {{-- UPDATE STATUS ADMIN / KANIT / GKM --}}
                @if ($canManage)
                    @if (! $isDone)
                        <section
                            x-data="{
                                selectedStatus: @js(old('status', $followUp->status))
                            }"
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                            <x-icon name="activity" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h2 class="text-base font-semibold text-slate-900">
                                                Update Status Tindak Lanjut
                                            </h2>

                                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                                Perbarui lifecycle pekerjaan dan catatan progres pengendalian.
                                            </p>
                                        </div>
                                    </div>

                                    <span class="inline-flex w-fit rounded-full border px-3 py-1.5 text-xs font-semibold {{ $followUp->statusBadgeClass() }}">
                                        Status saat ini: {{ $followUp->statusLabel() }}
                                    </span>
                                </div>
                            </div>

                            <form
                                method="POST"
                                action="{{ route(
                                    'documentation.control.follow-ups.update-status',
                                    $followUp
                                ) }}"
                                class="space-y-6 p-5 sm:p-6"
                            >
                                @csrf
                                @method('PATCH')

                                {{-- STATUS --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <label
                                        for="status"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Status Baru
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <select
                                        id="status"
                                        name="status"
                                        x-model="selectedStatus"
                                        required
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    >
                                        @foreach (\App\Models\ControlFollowUp::statusOptions() as $value => $label)
                                            <option value="{{ $value }}">
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                        Status Selesai akan mengunci data, progres, status, dan penambahan surat baru.
                                    </p>

                                    @error('status')
                                        <p class="mt-2 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- CATATAN PROGRES --}}
                                <div>
                                    <label
                                        for="manager_progress_note"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Catatan Progres
                                    </label>

                                    <textarea
                                        id="manager_progress_note"
                                        name="progress_note"
                                        rows="5"
                                        placeholder="Isi atau perbarui catatan perkembangan tindak lanjut"
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    >{{ old('progress_note', $followUp->progress_note) }}</textarea>

                                    @error('progress_note')
                                        <p class="mt-2 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- CATATAN SELESAI --}}
                                <div
                                    x-show="selectedStatus === @js(\App\Models\ControlFollowUp::STATUS_DONE)"
                                    x-cloak
                                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"
                                >
                                    <label
                                        for="completed_note"
                                        class="block text-sm font-semibold text-emerald-900"
                                    >
                                        Catatan Penyelesaian
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <textarea
                                        id="completed_note"
                                        name="completed_note"
                                        rows="5"
                                        x-bind:required="selectedStatus === @js(\App\Models\ControlFollowUp::STATUS_DONE)"
                                        placeholder="Jelaskan hasil akhir dan penyelesaian tindak lanjut"
                                        class="mt-2 block w-full rounded-xl border-emerald-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500"
                                    >{{ old('completed_note', $followUp->completed_note) }}</textarea>

                                    <p class="mt-2 text-xs leading-5 text-emerald-700">
                                        Setelah disimpan sebagai Selesai, tindak lanjut tidak dapat diubah kembali.
                                    </p>

                                    @error('completed_note')
                                        <p class="mt-2 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- ALASAN PEMBATALAN --}}
                                <div
                                    x-show="selectedStatus === @js(\App\Models\ControlFollowUp::STATUS_CANCELLED)"
                                    x-cloak
                                    class="rounded-2xl border border-rose-200 bg-rose-50 p-5"
                                >
                                    <label
                                        for="cancelled_note"
                                        class="block text-sm font-semibold text-rose-900"
                                    >
                                        Alasan Pembatalan
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <textarea
                                        id="cancelled_note"
                                        name="cancelled_note"
                                        rows="5"
                                        x-bind:required="selectedStatus === @js(\App\Models\ControlFollowUp::STATUS_CANCELLED)"
                                        placeholder="Jelaskan alasan tindak lanjut dibatalkan"
                                        class="mt-2 block w-full rounded-xl border-rose-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-rose-500 focus:ring-rose-500"
                                    >{{ old('cancelled_note', $followUp->cancelled_note) }}</textarea>

                                    <p class="mt-2 text-xs leading-5 text-rose-700">
                                        Status Dibatalkan masih dapat dikoreksi atau diaktifkan kembali oleh Admin, Kanit, atau GKM.
                                    </p>

                                    @error('cancelled_note')
                                        <p class="mt-2 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- PERINGATAN SELESAI --}}
                                <div
                                    x-show="selectedStatus === @js(\App\Models\ControlFollowUp::STATUS_DONE)"
                                    x-cloak
                                    class="rounded-2xl border border-amber-200 bg-amber-50 p-5"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                            <x-icon name="alert-circle" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-semibold text-amber-900">
                                                Pastikan seluruh pekerjaan sudah benar-benar selesai
                                            </h3>

                                            <p class="mt-1 text-sm leading-6 text-amber-700">
                                                Setelah status menjadi Selesai, data utama, progres, status,
                                                dan upload surat baru akan dikunci.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end border-t border-slate-100 pt-5">
                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 sm:w-auto"
                                    >
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                        Simpan Status
                                    </button>
                                </div>
                            </form>
                        </section>
                    @else
                        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                    <x-icon name="check-circle" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-sm font-semibold text-emerald-900">
                                        Lifecycle tindak lanjut sudah berakhir
                                    </h2>

                                    <p class="mt-1 text-sm leading-6 text-emerald-700">
                                        Status Selesai bersifat final. Form perubahan status tidak lagi tersedia.
                                    </p>
                                </div>
                            </div>
                        </section>
                    @endif
                @endif

                @php
                    $hasLetterErrors = $errors->hasAny([
                        'letter_type',
                        'letter_number',
                        'letter_date',
                        'subject',
                        'sender',
                        'recipient',
                        'summary',
                        'visibility',
                        'file',
                    ]);
                @endphp

                {{-- SURAT PENGENDALIAN --}}
                <section
                    x-data="{
                        openLetterForm: {{ $hasLetterErrors ? 'true' : 'false' }},
                        letterType: @js(old('letter_type', 'incoming')),

                        openForm(type) {
                            this.letterType = type;
                            this.openLetterForm = true;

                            this.$nextTick(() => {
                                document
                                    .getElementById('form-surat-pengendalian')
                                    ?.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'start'
                                    });
                            });
                        },

                        closeForm() {
                            this.openLetterForm = false;
                        }
                    }"
                    class="space-y-6"
                >
                    {{-- DAFTAR SURAT --}}
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                        <x-icon name="mail" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Surat Pengendalian
                                        </h2>

                                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                            {{ $followUp->letters->count() }} surat masuk atau surat keluar
                                            menjadi bukti koordinasi tindak lanjut ini.
                                        </p>
                                    </div>
                                </div>

                                @if ($canManage && ! $isDone)
                                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                                        <button
                                            type="button"
                                            x-on:click="openForm('incoming')"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                                        >
                                            <x-icon name="inbox" class="h-4 w-4" />
                                            Tambah Surat Masuk
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="openForm('outgoing')"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="send" class="h-4 w-4" />
                                            Tambah Surat Keluar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($followUp->letters->isEmpty())
                            {{-- EMPTY STATE --}}
                            <div class="p-5 sm:p-6">
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-inset ring-slate-200">
                                        <x-icon name="mail" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        Belum ada surat pengendalian
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        Surat masuk atau surat keluar yang menjadi bukti koordinasi
                                        tindak lanjut akan tampil di sini.
                                    </p>

                                    @if ($canManage && ! $isDone)
                                        <button
                                            type="button"
                                            x-on:click="openForm('incoming')"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="upload-cloud" class="h-4 w-4" />
                                            Unggah Surat
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- TABEL DESKTOP --}}
                            <div class="hidden overflow-x-auto lg:block">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Surat
                                            </th>

                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Tanggal
                                            </th>

                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Nomor
                                            </th>

                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Perihal
                                            </th>

                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Visibilitas
                                            </th>

                                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach ($followUp->letters as $letter)
                                            <tr class="transition hover:bg-slate-50/80">
                                                <td class="whitespace-nowrap px-5 py-4">
                                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->typeBadgeClass() }}">
                                                        {{ $letter->typeLabel() }}
                                                    </span>
                                                </td>

                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                    {{ $letter->letter_date?->format('d M Y') ?? '-' }}
                                                </td>

                                                <td class="min-w-[180px] px-5 py-4 text-sm text-slate-600">
                                                    {{ $letter->letter_number ?? '-' }}
                                                </td>

                                                <td class="min-w-[280px] px-5 py-4">
                                                    <p class="font-semibold leading-6 text-slate-900">
                                                        {{ $letter->subject }}
                                                    </p>

                                                    <p class="mt-1 max-w-sm truncate text-xs text-slate-500">
                                                        {{ $letter->original_name }}
                                                    </p>
                                                </td>

                                                <td class="whitespace-nowrap px-5 py-4">
                                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->visibilityBadgeClass() }}">
                                                        {{ $letter->visibilityLabel() }}
                                                    </span>
                                                </td>

                                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                                    <div class="flex justify-end gap-2">
                                                        <a
                                                            href="{{ route('documentation.control.letters.show', $letter) }}"
                                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                                        >
                                                            Detail
                                                            <x-icon name="chevron-right" class="h-4 w-4" />
                                                        </a>

                                                        <a
                                                            href="{{ route('documentation.control.letters.download', $letter) }}"
                                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                                        >
                                                            <x-icon name="download" class="h-4 w-4" />
                                                            Unduh
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- CARD MOBILE --}}
                            <div class="divide-y divide-slate-100 lg:hidden">
                                @foreach ($followUp->letters as $letter)
                                    <article class="p-5">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->typeBadgeClass() }}">
                                                {{ $letter->typeLabel() }}
                                            </span>

                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->visibilityBadgeClass() }}">
                                                {{ $letter->visibilityLabel() }}
                                            </span>
                                        </div>

                                        <h3 class="mt-3 text-base font-semibold leading-6 text-slate-900">
                                            {{ $letter->subject }}
                                        </h3>

                                        <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                                            <div>
                                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                    Tanggal Surat
                                                </dt>

                                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                                    {{ $letter->letter_date?->format('d M Y') ?? '-' }}
                                                </dd>
                                            </div>

                                            <div>
                                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                    Nomor Surat
                                                </dt>

                                                <dd class="mt-1 break-words text-sm font-semibold text-slate-900">
                                                    {{ $letter->letter_number ?? '-' }}
                                                </dd>
                                            </div>

                                            <div class="sm:col-span-2">
                                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                    Nama File
                                                </dt>

                                                <dd class="mt-1 break-all text-sm font-semibold text-slate-900">
                                                    {{ $letter->original_name }}
                                                </dd>
                                            </div>
                                        </dl>

                                        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                            <a
                                                href="{{ route('documentation.control.letters.show', $letter) }}"
                                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                            >
                                                Detail
                                                <x-icon name="chevron-right" class="h-4 w-4" />
                                            </a>

                                            <a
                                                href="{{ route('documentation.control.letters.download', $letter) }}"
                                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                            >
                                                <x-icon name="download" class="h-4 w-4" />
                                                Unduh
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- FORM UNGGAH SURAT --}}
                    @if ($canManage && ! $isDone)
                        <div
                            id="form-surat-pengendalian"
                            x-show="openLetterForm"
                            x-collapse
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                            <x-icon name="upload-cloud" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h2 class="text-base font-semibold text-slate-900">
                                                Unggah Surat Pengendalian
                                            </h2>

                                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                                Tambahkan surat sebagai bukti koordinasi tindak lanjut.
                                            </p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        x-on:click="closeForm()"
                                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                                        aria-label="Tutup form"
                                    >
                                        <x-icon name="x" class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <form
                                method="POST"
                                action="{{ route(
                                    'documentation.control.follow-ups.letters.store',
                                    $followUp
                                ) }}"
                                enctype="multipart/form-data"
                                class="space-y-6 p-5 sm:p-6"
                            >
                                @csrf

                                {{-- IDENTITAS SURAT --}}
                                <div>
                                    <div class="mb-5">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Identitas Surat
                                        </h3>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Tentukan jenis, visibilitas, nomor, tanggal, dan perihal surat.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                        <div>
                                            <label
                                                for="letter_type"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Jenis Surat
                                                <span class="text-rose-500">*</span>
                                            </label>

                                            <select
                                                id="letter_type"
                                                name="letter_type"
                                                x-model="letterType"
                                                required
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                            >
                                                <option value="incoming">
                                                    Surat Masuk
                                                </option>

                                                <option value="outgoing">
                                                    Surat Keluar
                                                </option>
                                            </select>

                                            @error('letter_type')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                for="visibility"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Visibilitas
                                                <span class="text-rose-500">*</span>
                                            </label>

                                            <select
                                                id="visibility"
                                                name="visibility"
                                                required
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                            >
                                                <option
                                                    value="unit"
                                                    @selected(old('visibility', 'unit') === 'unit')
                                                >
                                                    Unit — dapat dilihat Pegawai unit terkait
                                                </option>

                                                <option
                                                    value="restricted"
                                                    @selected(old('visibility') === 'restricted')
                                                >
                                                    Terbatas — hanya Admin, Kanit, dan GKM
                                                </option>
                                            </select>

                                            @error('visibility')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                for="letter_number"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Nomor Surat
                                            </label>

                                            <input
                                                id="letter_number"
                                                type="text"
                                                name="letter_number"
                                                value="{{ old('letter_number') }}"
                                                maxlength="255"
                                                placeholder="Contoh: 123/STIP/TI/VII/2026"
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                            >

                                            @error('letter_number')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                for="letter_date"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Tanggal Surat
                                            </label>

                                            <input
                                                id="letter_date"
                                                type="date"
                                                name="letter_date"
                                                value="{{ old('letter_date') }}"
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                            >

                                            @error('letter_date')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label
                                                for="subject"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Perihal
                                                <span class="text-rose-500">*</span>
                                            </label>

                                            <input
                                                id="subject"
                                                type="text"
                                                name="subject"
                                                value="{{ old('subject') }}"
                                                required
                                                maxlength="255"
                                                placeholder="Contoh: Permintaan kelengkapan dokumen backup server"
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                            >

                                            @error('subject')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- PENGIRIM, PENERIMA, DAN RINGKASAN --}}
                                <div class="border-t border-slate-100 pt-6">
                                    <div class="mb-5">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Informasi Koordinasi
                                        </h3>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Tambahkan asal, tujuan, dan ringkasan surat bila tersedia.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                        <div>
                                            <label
                                                for="sender"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Pengirim
                                            </label>

                                            <input
                                                id="sender"
                                                type="text"
                                                name="sender"
                                                value="{{ old('sender') }}"
                                                maxlength="255"
                                                placeholder="Asal atau pengirim surat"
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                            >

                                            @error('sender')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                for="recipient"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Penerima
                                            </label>

                                            <input
                                                id="recipient"
                                                type="text"
                                                name="recipient"
                                                value="{{ old('recipient') }}"
                                                maxlength="255"
                                                placeholder="Tujuan atau penerima surat"
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                            >

                                            @error('recipient')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label
                                                for="summary"
                                                class="block text-sm font-semibold text-slate-700"
                                            >
                                                Ringkasan Isi Surat
                                            </label>

                                            <textarea
                                                id="summary"
                                                name="summary"
                                                rows="4"
                                                placeholder="Tuliskan ringkasan singkat isi surat"
                                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                            >{{ old('summary') }}</textarea>

                                            @error('summary')
                                                <p class="mt-2 text-sm font-medium text-rose-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- FILE --}}
                                <div class="border-t border-slate-100 pt-6">
                                    <label
                                        for="control_letter_file"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        File Surat
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <div class="mt-2 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                                        <input
                                            id="control_letter_file"
                                            type="file"
                                            name="file"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                            required
                                            class="block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-sky-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100"
                                        >

                                        <p class="mt-3 text-xs leading-5 text-slate-500">
                                            Format PDF, Word, Excel, JPG, JPEG, atau PNG.
                                            Ukuran maksimal 10 MB.
                                        </p>
                                    </div>

                                    @error('file')
                                        <p class="mt-2 text-sm font-medium text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- AKSI --}}
                                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        x-on:click="closeForm()"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                    >
                                        <x-icon name="x" class="h-4 w-4" />
                                        Batal
                                    </button>

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon name="upload-cloud" class="h-4 w-4" />
                                        Unggah Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </section>
            </div>

            {{-- RINGKASAN DAN METADATA --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <x-icon name="history" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Ringkasan dan Metadata
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Informasi lifecycle, penugasan, surat, dan riwayat perubahan tindak lanjut.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- RINGKASAN UTAMA --}}
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Status
                                </p>

                                <div class="mt-3">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $followUp->statusBadgeClass() }}">
                                        {{ $followUp->statusLabel() }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200">
                                <x-icon
                                    name="{{ $statusHeroIcon }}"
                                    class="h-5 w-5"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">
                                    PIC
                                </p>

                                <p class="mt-3 truncate text-sm font-semibold text-sky-950">
                                    {{ $followUp->picUser?->employee?->name
                                        ?? $followUp->picUser?->name
                                        ?? 'Belum ditentukan' }}
                                </p>

                                @if ($isPic)
                                    <span class="mt-2 inline-flex rounded-full bg-white px-2 py-0.5 text-[11px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                        Anda PIC
                                    </span>
                                @endif
                            </div>

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-inset ring-sky-200">
                                <x-icon name="user-check" class="h-5 w-5" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">
                                    Surat Pengendalian
                                </p>

                                <p class="mt-3 text-2xl font-bold text-violet-950">
                                    {{ $followUp->letters->count() }}
                                </p>

                                <p class="mt-1 text-xs text-violet-700">
                                    surat terkait
                                </p>
                            </div>

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-violet-700 shadow-sm ring-1 ring-inset ring-violet-200">
                                <x-icon name="mail" class="h-5 w-5" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                                    Tenggat Waktu
                                </p>

                                <p class="mt-3 text-sm font-semibold text-amber-950">
                                    {{ $followUp->due_date?->format('d M Y')
                                        ?? 'Belum ditentukan' }}
                                </p>
                            </div>

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 shadow-sm ring-1 ring-inset ring-amber-200">
                                <x-icon name="calendar" class="h-5 w-5" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- METADATA --}}
                <div class="border-t border-slate-100">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="border-b border-slate-100 p-5 sm:border-r xl:border-b-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Dibuat Oleh
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $followUp->creator?->employee?->name
                                    ?? $followUp->creator?->name
                                    ?? '-' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5 xl:border-r xl:border-b-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tanggal Dibuat
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $followUp->created_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0 xl:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Diperbarui Oleh
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $followUp->updater?->employee?->name
                                    ?? $followUp->updater?->name
                                    ?? '-' }}
                            </dd>
                        </div>

                        <div class="p-5">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Terakhir Diubah
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $followUp->updated_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- INFORMASI AKHIR --}}
                @if ($followUp->completed_at || $followUp->completed_note || $followUp->cancelled_note)
                    <div class="border-t border-slate-100 p-5 sm:p-6">
                        @if ($followUp->completed_at || $followUp->completed_note)
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                        <x-icon name="check-circle" class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-sm font-semibold text-emerald-900">
                                            Penyelesaian Tindak Lanjut
                                        </h3>

                                        @if ($followUp->completed_at)
                                            <p class="mt-1 text-xs font-semibold text-emerald-700">
                                                Selesai pada
                                                {{ $followUp->completed_at->format('d M Y H:i') }}
                                            </p>
                                        @endif

                                        @if ($followUp->completed_note)
                                            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-emerald-800">
                                                {{ $followUp->completed_note }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif ($followUp->cancelled_note)
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                                        <x-icon name="x-circle" class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-sm font-semibold text-rose-900">
                                            Pembatalan Tindak Lanjut
                                        </h3>

                                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-rose-800">
                                            {{ $followUp->cancelled_note }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>