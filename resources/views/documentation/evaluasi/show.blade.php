<x-app-layout>
    @php
        $user = auth()->user();

        $canManage =
            $user->isAdmin()
            || $user->isKanit()
            || $user->role?->name === 'gkm';

        $hasCompletedFollowUp = $record->controlFollowUps
            ->contains(
                'status',
                \App\Models\ControlFollowUp::STATUS_DONE
            );

        $statusClass = match ($record->status) {
            'published' =>
                'bg-emerald-400/10 text-emerald-200 ring-emerald-300/20',

            'archived' =>
                'bg-violet-400/10 text-violet-200 ring-violet-300/20',

            default =>
                'bg-amber-400/10 text-amber-200 ring-amber-300/20',
        };

        $statusIcon = match ($record->status) {
            'published' => 'badge-check',
            'archived' => 'archive',
            default => 'edit-3',
        };
    @endphp

    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon
                                name="search-check"
                                class="h-4 w-4"
                            />

                            {{ $record->evaluation_type_label }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                            <x-icon
                                name="{{ $statusIcon }}"
                                class="h-3.5 w-3.5"
                            />

                            {{ $record->status_label }}
                        </span>
                    </div>

                    <h1 class="mt-5 break-words text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $record->title }}
                    </h1>

                    <p class="mt-4 max-w-4xl text-sm leading-7 text-slate-300 sm:text-base">
                        {{ $record->source ?: 'Detail hasil evaluasi, temuan, rekomendasi, dokumen pendukung, dan tindak lanjut Unit SIM TI.' }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon
                                name="calendar"
                                class="h-3.5 w-3.5"
                            />

                            {{ $record->evaluation_date?->format('d M Y') ?? 'Tanggal belum diisi' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon
                                name="building-2"
                                class="h-3.5 w-3.5"
                            />

                            {{ $record->unit?->name ?? 'Unit belum diisi' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon
                                name="paperclip"
                                class="h-3.5 w-3.5"
                            />

                            {{ $record->documents->count() }} dokumen
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:flex-wrap lg:max-w-lg lg:justify-end lg:pl-8">
                    <a
                        href="{{ route('documentation.evaluasi.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon
                            name="arrow-left"
                            class="h-4 w-4"
                        />

                        Kembali
                    </a>

                    @if ($canManage && $record->isDraft())
                        <a
                            href="{{ route('documentation.evaluasi.edit', $record) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                        >
                            <x-icon
                                name="edit-3"
                                class="h-4 w-4"
                            />

                            Edit
                        </a>

                        @if ($record->canBePublished())
                            <form
                                x-data
                                method="POST"
                                action="{{ route('documentation.evaluasi.publish', $record) }}"
                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                    title: 'Publish Hasil Evaluasi?',
                                    message: 'Hasil evaluasi ini akan menjadi catatan resmi dan terlihat oleh Pegawai. Setelah dipublish, data tidak dapat diedit langsung.',
                                    confirmText: 'Ya, Publish',
                                    cancelText: 'Batal',
                                    variant: 'info',
                                    onConfirm: () => $el.submit()
                                })"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-400"
                                >
                                    <x-icon
                                        name="badge-check"
                                        class="h-4 w-4"
                                    />

                                    Publish
                                </button>
                            </form>
                        @else
                            <button
                                type="button"
                                disabled
                                title="Lengkapi data wajib sebelum publish"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-slate-400"
                            >
                                <x-icon
                                    name="badge-check"
                                    class="h-4 w-4"
                                />

                                Publish
                            </button>
                        @endif

                        <form
                            x-data
                            method="POST"
                            action="{{ route('documentation.evaluasi.destroy', $record) }}"
                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                title: 'Hapus Draft Evaluasi?',
                                message: 'Draft hasil evaluasi akan dihapus dan tidak dapat dikembalikan.',
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
                                <x-icon
                                    name="trash-2"
                                    class="h-4 w-4"
                                />

                                Hapus
                            </button>
                        </form>
                    @elseif ($canManage && $record->isPublished())
                        @if (! $hasCompletedFollowUp)
                            <a
                                href="{{ route('documentation.control.follow-ups.create', ['evaluation_record_id' => $record->id]) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                            >
                                <x-icon
                                    name="clipboard-list"
                                    class="h-4 w-4"
                                />

                                Buat Tindak Lanjut
                            </a>
                        @endif

                        <form
                            x-data
                            method="POST"
                            action="{{ route('documentation.evaluasi.archive', $record) }}"
                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                title: 'Arsipkan Hasil Evaluasi?',
                                message: 'Hasil evaluasi Published ini akan diarsipkan dan tidak lagi terlihat oleh Pegawai.',
                                confirmText: 'Ya, Arsipkan',
                                cancelText: 'Batal',
                                variant: 'info',
                                onConfirm: () => $el.submit()
                            })"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-violet-400/30 bg-violet-500/10 px-4 py-2.5 text-sm font-semibold text-violet-100 shadow-sm transition hover:bg-violet-500/20"
                            >
                                <x-icon
                                    name="archive"
                                    class="h-4 w-4"
                                />

                                Arsipkan
                            </button>
                        </form>
                    @elseif ($record->isArchived())
                        <span class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-slate-200">
                            <x-icon
                                name="archive"
                                class="h-4 w-4"
                            />

                            Hasil Evaluasi Diarsipkan
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- PERINGATAN PUBLISH --}}
        @if ($canManage && $record->isDraft() && ! $record->canBePublished())
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <x-icon
                            name="alert-circle"
                            class="h-5 w-5"
                        />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-amber-900">
                            Hasil evaluasi belum siap dipublish
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-800">
                            Lengkapi data berikut sebelum hasil evaluasi dijadikan catatan resmi:
                        </p>

                        <ul class="mt-3 space-y-2">
                            @foreach ($record->publishMissingFields() as $field)
                                <li class="flex items-start gap-2 text-sm text-amber-800">
                                    <x-icon
                                        name="circle"
                                        class="mt-1.5 h-2 w-2 shrink-0"
                                    />

                                    {{ $field }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- KONTEN UTAMA --}}
            <div class="space-y-6 xl:col-span-2">

                {{-- INFORMASI EVALUASI --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon
                                    name="search-check"
                                    class="h-5 w-5"
                                />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Informasi Evaluasi
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Identitas, sumber, status, dan tautan pendukung evaluasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2">
                        <div class="border-b border-slate-100 p-5 sm:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tanggal Evaluasi
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $record->evaluation_date?->format('d M Y') ?? '-' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Jenis Evaluasi
                            </dt>

                            <dd class="mt-2">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $record->evaluation_type_badge_class }}">
                                    {{ $record->evaluation_type_label }}
                                </span>
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5 sm:border-r">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Unit
                            </dt>

                            <dd class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $record->unit?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="border-b border-slate-100 p-5">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status
                            </dt>

                            <dd class="mt-2">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $record->status_badge_class }}">
                                    {{ $record->status_label }}
                                </span>
                            </dd>
                        </div>

                        <div class="p-5 sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Sumber / Kegiatan
                            </dt>

                            <dd class="mt-2 text-sm leading-6 text-slate-700">
                                {{ $record->source ?: '-' }}
                            </dd>
                        </div>
                    </dl>

                    @if ($record->zoom_link || $record->google_drive_link)
                        <div class="border-t border-slate-100 p-5 sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tautan Rapat dan Rekaman
                            </p>

                            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                                @if ($record->zoom_link)
                                    <a
                                        href="{{ $record->zoom_link }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100"
                                    >
                                        <x-icon
                                            name="video"
                                            class="h-4 w-4"
                                        />

                                        Buka Zoom

                                        <x-icon
                                            name="external-link"
                                            class="h-3.5 w-3.5"
                                        />
                                    </a>
                                @endif

                                @if ($record->google_drive_link)
                                    <a
                                        href="{{ $record->google_drive_link }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100"
                                    >
                                        <x-icon
                                            name="folder"
                                            class="h-4 w-4"
                                        />

                                        Buka Google Drive

                                        <x-icon
                                            name="external-link"
                                            class="h-3.5 w-3.5"
                                        />
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </section>

                {{-- TEMUAN --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <x-icon
                                name="sticky-note"
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Temuan / Hasil Evaluasi
                            </h2>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Temuan, kendala, kesimpulan, atau catatan penting.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50 p-5 text-sm leading-7 text-slate-700">
                        {!! nl2br(e(
                            $record->findings
                            ?: 'Belum ada temuan atau hasil evaluasi yang dicatat.'
                        )) !!}
                    </div>
                </section>

                {{-- REKOMENDASI --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <x-icon
                                name="lightbulb"
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Rekomendasi
                            </h2>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Rekomendasi perbaikan berdasarkan hasil evaluasi.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50 p-5 text-sm leading-7 text-slate-700">
                        {!! nl2br(e(
                            $record->recommendation
                            ?: 'Belum ada rekomendasi yang dicatat.'
                        )) !!}
                    </div>
                </section>

                {{-- DOKUMEN PENDUKUNG --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <x-icon
                                        name="paperclip"
                                        class="h-5 w-5"
                                    />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Dokumen Pendukung
                                    </h2>

                                    <p class="mt-0.5 text-sm text-slate-500">
                                        {{ $record->documents->count() }} dokumen terlampir pada hasil evaluasi ini.
                                    </p>
                                </div>
                            </div>

                            @if ($canManage && $record->isDraft())
                                <button
                                    type="button"
                                    x-data
                                    x-on:click="$dispatch('open-upload-evaluation-document')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                >
                                    <x-icon
                                        name="upload-cloud"
                                        class="h-4 w-4"
                                    />

                                    Unggah Dokumen
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($canManage && $record->isDraft())
                        <div
                            x-data="{ open: {{ $errors->has('file') || $errors->has('document_type') ? 'true' : 'false' }} }"
                            x-on:open-upload-evaluation-document.window="open = true"
                            x-show="open"
                            x-collapse
                            class="border-b border-slate-100 bg-slate-50 p-5 sm:p-6"
                        >
                            <div class="mb-5 flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-900">
                                        Unggah Dokumen Pendukung
                                    </h3>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Tambahkan notulen, berita acara, dokumentasi, atau file lainnya.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    x-on:click="open = false"
                                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                    aria-label="Tutup form"
                                >
                                    <x-icon
                                        name="x"
                                        class="h-4 w-4"
                                    />
                                </button>
                            </div>

                            <form
                                method="POST"
                                action="{{ route('documentation.evaluasi.documents.store', $record) }}"
                                enctype="multipart/form-data"
                                class="space-y-5"
                            >
                                @csrf

                                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                    <div>
                                        <label
                                            for="document_title"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            Judul Dokumen
                                            <span class="text-rose-500">*</span>
                                        </label>

                                        <input
                                            id="document_title"
                                            type="text"
                                            name="title"
                                            value="{{ old('title') }}"
                                            required
                                            maxlength="255"
                                            placeholder="Contoh: Notulen Rapat Evaluasi"
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                        >

                                        @error('title')
                                            <p class="mt-2 text-sm font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            for="document_type"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            Jenis Dokumen
                                            <span class="text-rose-500">*</span>
                                        </label>

                                        <select
                                            id="document_type"
                                            name="document_type"
                                            required
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                        >
                                            @foreach (\App\Models\EvaluationDocument::documentTypeOptions() as $value => $label)
                                                <option
                                                    value="{{ $value }}"
                                                    @selected(old('document_type') === $value)
                                                >
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('document_type')
                                            <p class="mt-2 text-sm font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label
                                            for="evaluation_document_file"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            File Dokumen
                                            <span class="text-rose-500">*</span>
                                        </label>

                                        <div class="mt-2 rounded-2xl border border-dashed border-slate-300 bg-white p-5">
                                            <input
                                                id="evaluation_document_file"
                                                type="file"
                                                name="file"
                                                required
                                                class="block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-sky-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100"
                                            >

                                            <p class="mt-3 text-xs leading-5 text-slate-500">
                                                Ukuran maksimal 10 MB. Disarankan memakai PDF,
                                                Word, Excel, JPG, atau PNG.
                                            </p>
                                        </div>

                                        @error('file')
                                            <p class="mt-2 text-sm font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label
                                            for="document_description"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            Deskripsi
                                        </label>

                                        <textarea
                                            id="document_description"
                                            name="description"
                                            rows="3"
                                            placeholder="Catatan tambahan dokumen"
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                        >{{ old('description') }}</textarea>

                                        @error('description')
                                            <p class="mt-2 text-sm font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        x-on:click="open = false"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                    >
                                        <x-icon
                                            name="x"
                                            class="h-4 w-4"
                                        />

                                        Batal
                                    </button>

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon
                                            name="upload-cloud"
                                            class="h-4 w-4"
                                        />

                                        Unggah Dokumen
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="p-5 sm:p-6">
                        @if ($record->documents->isEmpty())
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-inset ring-slate-200">
                                    <x-icon
                                        name="file-text"
                                        class="h-7 w-7"
                                    />
                                </div>

                                <h3 class="mt-4 text-base font-semibold text-slate-900">
                                    Belum ada dokumen pendukung
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                    Dokumen seperti notulen, berita acara, laporan,
                                    atau dokumentasi akan tampil di sini.
                                </p>

                                @if ($canManage && $record->isDraft())
                                    <button
                                        type="button"
                                        x-data
                                        x-on:click="$dispatch('open-upload-evaluation-document')"
                                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon
                                            name="upload-cloud"
                                            class="h-4 w-4"
                                        />

                                        Unggah Dokumen
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-3">
                                @foreach ($record->documents as $document)
                                    <article class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-sky-200 hover:shadow-sm">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="flex min-w-0 items-start gap-3">
                                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                                    <x-icon
                                                        name="file-text"
                                                        class="h-5 w-5"
                                                    />
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <h3 class="text-sm font-semibold text-slate-900">
                                                            {{ $document->title }}
                                                        </h3>

                                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                                            {{ $document->document_type_label }}
                                                        </span>
                                                    </div>

                                                    <p class="mt-2 break-all text-xs text-slate-500">
                                                        {{ $document->original_name ?: 'File dokumen tersedia' }}
                                                    </p>

                                                    <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-400">
                                                        <span>
                                                            {{ $document->formatted_file_size }}
                                                        </span>

                                                        <span>
                                                            Diunggah oleh {{ $document->uploader?->name ?? '-' }}
                                                        </span>

                                                        <span>
                                                            {{ $document->created_at?->format('d M Y H:i') ?? '-' }}
                                                        </span>
                                                    </div>

                                                    @if ($document->description)
                                                        <p class="mt-3 rounded-xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                                                            {{ $document->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                                                <a
                                                    href="{{ route('documentation.evaluasi.documents.download', $document) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                                >
                                                    <x-icon
                                                        name="download"
                                                        class="h-4 w-4"
                                                    />

                                                    Unduh
                                                </a>

                                                @if ($canManage && $record->isDraft())
                                                    <form
                                                        x-data
                                                        method="POST"
                                                        action="{{ route('documentation.evaluasi.documents.destroy', $document) }}"
                                                        x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                            title: 'Hapus Dokumen Pendukung?',
                                                            message: 'File dokumen pendukung ini akan dihapus dan tidak dapat dikembalikan.',
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
                                                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 shadow-sm transition hover:bg-rose-50"
                                                        >
                                                            <x-icon
                                                                name="trash-2"
                                                                class="h-4 w-4"
                                                            />

                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- SIDEBAR DETAIL --}}
            <div class="space-y-6">

                {{-- METADATA --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <x-icon
                                name="history"
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Metadata
                            </h2>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Riwayat pembuatan dan lifecycle evaluasi.
                            </p>
                        </div>
                    </div>

                    <dl class="mt-5 divide-y divide-slate-100">
                        <div class="py-3 first:pt-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Dibuat Oleh
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $record->creator?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tanggal Dibuat
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $record->created_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>

                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Terakhir Diubah
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $record->updated_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>

                        @if ($record->published_at)
                            <div class="py-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Publish
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-emerald-700">
                                    {{ $record->published_at->format('d M Y H:i') }}
                                </dd>
                            </div>
                        @endif

                        @if ($record->archived_at)
                            <div class="py-3 last:pb-0">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Diarsipkan
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-violet-700">
                                    {{ $record->archived_at->format('d M Y H:i') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </section>

                {{-- TINDAK LANJUT --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <x-icon
                                        name="clipboard-list"
                                        class="h-5 w-5"
                                    />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Tindak Lanjut Pengendalian
                                    </h2>

                                    <p class="mt-0.5 text-sm text-slate-500">
                                        {{ $record->controlFollowUps->count() }} tindak lanjut terkait.
                                    </p>
                                </div>
                            </div>

                            @if (
                                $canManage
                                && $record->isPublished()
                                && ! $hasCompletedFollowUp
                            )
                                <a
                                    href="{{ route('documentation.control.follow-ups.create', ['evaluation_record_id' => $record->id]) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                >
                                    <x-icon
                                        name="clipboard-list"
                                        class="h-4 w-4"
                                    />

                                    Buat Tindak Lanjut
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($record->controlFollowUps as $followUp)
                            <article class="p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $followUp->statusBadgeClass() }}">
                                        {{ $followUp->statusLabel() }}
                                    </span>

                                    @if ($followUp->due_date)
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                            <x-icon
                                                name="calendar"
                                                class="h-3.5 w-3.5"
                                            />

                                            {{ $followUp->due_date->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-3 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $followUp->title }}
                                </h3>

                                <div class="mt-3 rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        PIC
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ $followUp->picUser?->employee?->name
                                            ?? $followUp->picUser?->name
                                            ?? 'Belum ditentukan' }}
                                    </p>
                                </div>

                                @if ($followUp->progress_note)
                                    <p class="mt-3 line-clamp-3 text-xs leading-5 text-slate-500">
                                        {{ $followUp->progress_note }}
                                    </p>
                                @endif

                                <a
                                    href="{{ route('documentation.control.follow-ups.show', $followUp) }}"
                                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                >
                                    Detail
                                    <x-icon
                                        name="chevron-right"
                                        class="h-3.5 w-3.5"
                                    />
                                </a>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <x-icon
                                        name="clipboard-list"
                                        class="h-6 w-6"
                                    />
                                </div>

                                <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                    Belum ada tindak lanjut
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Tindak lanjut dapat dibuat setelah hasil evaluasi dipublish.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>

                @if ($hasCompletedFollowUp)
                    <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <x-icon
                                    name="check-circle"
                                    class="h-5 w-5"
                                />
                            </div>

                            <div>
                                <h2 class="text-sm font-semibold text-emerald-900">
                                    Evaluasi sudah terkendali
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-emerald-700">
                                    Sudah ada tindak lanjut berstatus Selesai,
                                    sehingga tindak lanjut baru tidak diperlukan.
                                </p>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>