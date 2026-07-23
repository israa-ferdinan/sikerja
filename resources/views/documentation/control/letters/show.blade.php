<x-app-layout>
    @php
        $user = auth()->user();
        $canManage = $user->role?->name !== 'pegawai';

        $fileSizeLabel = $letter->file_size
            ? number_format($letter->file_size / 1024, 1) . ' KB'
            : '-';

        $typeHeroIcon = $letter->letter_type === \App\Models\ControlLetter::TYPE_INCOMING
            ? 'inbox'
            : 'send';

        $typeHeroClass = $letter->letter_type === \App\Models\ControlLetter::TYPE_INCOMING
            ? 'bg-sky-400/10 text-sky-200 ring-sky-300/20'
            : 'bg-violet-400/10 text-violet-200 ring-violet-300/20';

        $visibilityHeroClass = $letter->visibility === \App\Models\ControlLetter::VISIBILITY_UNIT
            ? 'bg-emerald-400/10 text-emerald-200 ring-emerald-300/20'
            : 'bg-amber-400/10 text-amber-200 ring-amber-300/20';
    @endphp

    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="mail" class="h-4 w-4" />
                            Pengendalian
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $typeHeroClass }}">
                            <x-icon
                                name="{{ $typeHeroIcon }}"
                                class="h-3.5 w-3.5"
                            />
                            {{ $letter->typeLabel() }}
                        </span>

                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $visibilityHeroClass }}">
                            {{ $letter->visibilityLabel() }}
                        </span>
                    </div>

                    <h1 class="mt-5 break-words text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $letter->subject }}
                    </h1>

                    <p class="mt-4 max-w-4xl text-sm leading-7 text-slate-300 sm:text-base">
                        Detail metadata, keterkaitan, akses, dan file surat pengendalian
                        yang tersimpan secara protected.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="building-2" class="h-3.5 w-3.5" />
                            {{ $letter->unit?->name ?? 'Unit belum ditentukan' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />
                            {{ $letter->letter_date?->format('d M Y') ?? 'Tanggal belum diisi' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="file-text" class="h-3.5 w-3.5" />
                            {{ $fileSizeLabel }}
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:flex-wrap lg:max-w-lg lg:justify-end lg:pl-8">
                    <a
                        href="{{ route('documentation.control.letters.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>

                    <a
                        href="{{ route('documentation.control.letters.download', $letter) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                    >
                        <x-icon name="download" class="h-4 w-4" />
                        Unduh
                    </a>

                    @if ($canManage)
                        <a
                            href="{{ route('documentation.control.letters.edit', $letter) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/30 bg-sky-500/10 px-4 py-2.5 text-sm font-semibold text-sky-100 shadow-sm transition hover:bg-sky-500/20"
                        >
                            <x-icon name="edit-3" class="h-4 w-4" />
                            Edit
                        </a>

                        <form
                            x-data
                            method="POST"
                            action="{{ route('documentation.control.letters.destroy', $letter) }}"
                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                title: 'Hapus Surat Pengendalian?',
                                message: 'Metadata dan file surat akan dihapus permanen dari arsip.',
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

        {{-- INFORMASI UTAMA --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="mail" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Informasi Surat
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Identitas, unit, visibilitas, dan informasi pengarsipan surat.
                        </p>
                    </div>
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                <div class="border-b border-slate-100 p-5 sm:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Jenis Surat
                    </dt>

                    <dd class="mt-2">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->typeBadgeClass() }}">
                            {{ $letter->typeLabel() }}
                        </span>
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Unit
                    </dt>

                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                        {{ $letter->unit?->name ?? '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 sm:border-r xl:border-r-0">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Visibilitas
                    </dt>

                    <dd class="mt-2">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->visibilityBadgeClass() }}">
                            {{ $letter->visibilityLabel() }}
                        </span>
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 xl:border-b-0 xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Nomor Surat
                    </dt>

                    <dd class="mt-2 break-words text-sm font-semibold leading-6 text-slate-900">
                        {{ $letter->letter_number ?? '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0 xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Surat
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $letter->letter_date?->format('d M Y') ?? '-' }}
                    </dd>
                </div>

                <div class="p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Diunggah Oleh
                    </dt>

                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                        {{ $letter->uploader?->employee?->name
                            ?? $letter->uploader?->name
                            ?? '-' }}
                    </dd>
                </div>
            </dl>
        </section>

        {{-- INFORMASI KOORDINASI --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <x-icon name="send" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Informasi Koordinasi
                    </h2>

                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                        Informasi pengirim, penerima, dan ringkasan isi surat.
                    </p>
                </div>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Pengirim
                    </dt>

                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                        {{ $letter->sender ?? '-' }}
                    </dd>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Penerima
                    </dt>

                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                        {{ $letter->recipient ?? '-' }}
                    </dd>
                </div>
            </dl>

            @if ($letter->summary)
                <div class="mt-4 rounded-2xl border border-amber-100 bg-amber-50/60 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                        Ringkasan Isi Surat
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">
                        {{ $letter->summary }}
                    </p>
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <p class="text-sm leading-6 text-slate-500">
                        Ringkasan isi surat belum ditambahkan.
                    </p>
                </div>
            @endif
        </section>

        {{-- FILE DAN KETERKAITAN --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{-- FILE --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            File Surat
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Informasi file yang disimpan pada penyimpanan protected.
                        </p>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <dl class="space-y-5">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nama File
                            </dt>

                            <dd class="mt-2 break-all text-sm font-semibold leading-6 text-slate-900">
                                {{ $letter->original_name }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Jenis File
                                </dt>

                                <dd class="mt-2 break-words text-sm font-semibold text-slate-900">
                                    {{ $letter->mime_type ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Ukuran File
                                </dt>

                                <dd class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $fileSizeLabel }}
                                </dd>
                            </div>
                        </div>
                    </dl>

                    <a
                        href="{{ route('documentation.control.letters.download', $letter) }}"
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 sm:w-auto"
                    >
                        <x-icon name="download" class="h-4 w-4" />
                        Unduh File
                    </a>
                </div>
            </section>

            {{-- KETERKAITAN --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <x-icon name="clipboard-list" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Keterkaitan Tindak Lanjut
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Tindak lanjut evaluasi yang berkaitan dengan surat ini.
                        </p>
                    </div>
                </div>

                @if ($letter->followUp)
                    <div class="mt-6 rounded-2xl border border-violet-100 bg-violet-50/60 p-5">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letter->followUp->statusBadgeClass() }}">
                            {{ $letter->followUp->statusLabel() }}
                        </span>

                        <h3 class="mt-3 text-base font-semibold leading-6 text-slate-900">
                            {{ $letter->followUp->title }}
                        </h3>

                        <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $letter->followUp->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tenggat
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $letter->followUp->due_date?->format('d M Y')
                                        ?? 'Belum ditentukan' }}
                                </dd>
                            </div>
                        </dl>

                        @if ($letter->followUp->evaluationRecord)
                            <div class="mt-4 rounded-xl bg-white/70 p-4 ring-1 ring-inset ring-violet-100">
                                <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">
                                    Sumber Evaluasi
                                </p>

                                <p class="mt-1 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $letter->followUp->evaluationRecord->title }}
                                </p>
                            </div>
                        @endif

                        <a
                            href="{{ route(
                                'documentation.control.follow-ups.show',
                                $letter->followUp
                            ) }}"
                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-semibold text-violet-700 shadow-sm transition hover:bg-violet-50"
                        >
                            Buka Tindak Lanjut
                            <x-icon name="external-link" class="h-4 w-4" />
                        </a>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-inset ring-slate-200">
                            <x-icon name="clipboard-list" class="h-6 w-6" />
                        </div>

                        <h3 class="mt-4 text-sm font-semibold text-slate-900">
                            Arsip surat mandiri
                        </h3>

                        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Surat ini belum dikaitkan dengan tindak lanjut evaluasi tertentu.
                        </p>

                        @if ($canManage)
                            <a
                                href="{{ route('documentation.control.letters.edit', $letter) }}"
                                class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                            >
                                <x-icon name="edit-3" class="h-4 w-4" />
                                Atur Keterkaitan
                            </a>
                        @endif
                    </div>
                @endif
            </section>
        </div>

        {{-- METADATA ARSIP --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <x-icon name="history" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Metadata Arsip
                        </h2>

                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                            Waktu pengunggahan dan pembaruan terakhir surat.
                        </p>
                    </div>
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
                <div class="border-b border-slate-100 p-5 sm:border-r xl:border-b-0">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Diunggah Oleh
                    </dt>

                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                        {{ $letter->uploader?->employee?->name
                            ?? $letter->uploader?->name
                            ?? '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 xl:border-r xl:border-b-0">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Diunggah
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $letter->created_at?->format('d M Y H:i') ?? '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0 xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Terakhir Diubah
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $letter->updated_at?->format('d M Y H:i') ?? '-' }}
                    </dd>
                </div>

                <div class="p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Akses File
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        Protected
                    </dd>
                </div>
            </dl>
        </section>

        {{-- INFORMASI AKSES --}}
        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm sm:p-6">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                    <x-icon name="lock" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-sky-900">
                        File surat dilindungi oleh pemeriksaan akses
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-sky-700">
                        Pengguna harus melewati pemeriksaan role, unit, dan visibilitas
                        sebelum file dapat diunduh. Surat Terbatas tidak dapat diakses
                        oleh Pegawai.
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>