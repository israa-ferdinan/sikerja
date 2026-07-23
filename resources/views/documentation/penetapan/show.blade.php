<x-app-layout>
    @php
        $statusClass = match ($document->status) {
            'published' => 'bg-emerald-400/10 text-emerald-200 ring-emerald-300/20',
            'archived' => 'bg-violet-400/10 text-violet-200 ring-violet-300/20',
            default => 'bg-amber-400/10 text-amber-200 ring-amber-300/20',
        };

        $statusIcon = match ($document->status) {
            'published' => 'check-circle',
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
                            <x-icon name="file-text" class="h-4 w-4" />
                            {{ $document->category_label }}
                        </span>

                        @if ($canManage)
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                <x-icon name="{{ $statusIcon }}" class="h-3.5 w-3.5" />
                                {{ $document->status_label }}
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-5 break-words text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $document->title }}
                    </h1>

                    <p class="mt-4 max-w-4xl text-sm leading-7 text-slate-300 sm:text-base">
                        {{ $document->description ?: 'Dokumen Penetapan Unit SIM TI.' }}
                    </p>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:flex-wrap lg:max-w-md lg:justify-end lg:pl-8">
                    <a
                        href="{{ route('documentation.penetapan.index', ['category' => $document->category]) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>

                    @if ($canManage)
                        @if ($document->isDraft())
                            <a
                                href="{{ route('documentation.penetapan.edit', $document) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                            >
                                <x-icon name="edit-3" class="h-4 w-4" />
                                Edit
                            </a>

                            <form
                                x-data
                                method="POST"
                                action="{{ route('documentation.penetapan.publish', $document) }}"
                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                    title: 'Publish Dokumen?',
                                    message: 'Dokumen ini akan menjadi dokumen resmi dan terlihat oleh Pegawai. Setelah dipublish, dokumen tidak dapat diedit langsung.',
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
                                    <x-icon name="check-circle" class="h-4 w-4" />
                                    Publish
                                </button>
                            </form>

                            <form
                                x-data
                                method="POST"
                                action="{{ route('documentation.penetapan.destroy', $document) }}"
                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                    title: 'Hapus Draft Dokumen?',
                                    message: 'Draft dokumen dan file yang terlampir akan dihapus. Aksi ini hanya tersedia untuk dokumen yang belum dipublish.',
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
                        @elseif ($document->isPublished())
                            <form
                                x-data
                                method="POST"
                                action="{{ route('documentation.penetapan.archive', $document) }}"
                                x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                    title: 'Arsipkan Dokumen?',
                                    message: 'Dokumen Published ini akan diarsipkan dan tidak lagi terlihat oleh Pegawai. Dokumen tetap tersimpan untuk kebutuhan audit.',
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
                                    <x-icon name="archive" class="h-4 w-4" />
                                    Arsipkan
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-slate-200">
                                <x-icon name="archive" class="h-4 w-4" />
                                Dokumen Arsip
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </section>

        {{-- INFORMASI DOKUMEN --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="file-text" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Informasi Dokumen
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Metadata dan riwayat lifecycle dokumen Penetapan.
                        </p>
                    </div>
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
                <div class="border-b border-slate-100 p-5 sm:border-r xl:border-b">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Nomor Dokumen
                    </dt>

                    <dd class="mt-2 break-words text-sm font-semibold text-slate-900">
                        {{ $document->document_number ?: '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Revisi / Versi
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $document->revision ?: '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 sm:border-r xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Dokumen
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $document->document_date?->format('d M Y') ?: '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Berlaku
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $document->effective_date?->format('d M Y') ?: '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 sm:border-r xl:border-b-0">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Diunggah Oleh
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $document->uploader?->name ?: '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 p-5 xl:border-b-0 xl:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Publish
                    </dt>

                    <dd class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $document->published_at?->format('d M Y H:i') ?: '-' }}
                    </dd>
                </div>

                @if ($canManage)
                    <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0 xl:border-r">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Diarsipkan
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $document->archived_at?->format('d M Y H:i') ?: '-' }}
                        </dd>
                    </div>

                    <div class="p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Terakhir Diubah
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $document->updated_at?->format('d M Y H:i') ?: '-' }}
                        </dd>
                    </div>
                @else
                    <div class="p-5 sm:col-span-2 xl:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Terakhir Diubah
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $document->updated_at?->format('d M Y H:i') ?: '-' }}
                        </dd>
                    </div>
                @endif
            </dl>
        </section>

        {{-- FILE DOKUMEN --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <x-icon name="download" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        File Dokumen
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500">
                        File resmi atau lampiran pendukung dokumen Penetapan.
                    </p>
                </div>
            </div>

            @if ($document->file_path)
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-inset ring-slate-200">
                                <x-icon name="file-text" class="h-6 w-6" />
                            </div>

                            <div class="min-w-0">
                                <p class="break-all text-sm font-semibold text-slate-900">
                                    {{ $document->original_filename ?: 'File dokumen tersedia' }}
                                </p>

                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                    @if ($document->file_mime)
                                        <span>
                                            Tipe: {{ $document->file_mime }}
                                        </span>
                                    @endif

                                    @if ($document->file_size)
                                        <span>
                                            Ukuran: {{ number_format($document->file_size / 1024, 2) }} KB
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <a
                            href="{{ route('documentation.penetapan.download', $document) }}"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="download" class="h-4 w-4" />
                            Unduh
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-5 rounded-2xl border border-dashed border-slate-300 px-6 py-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <x-icon name="file-text" class="h-7 w-7" />
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                        Belum ada file
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Dokumen ini belum memiliki file pendukung.
                    </p>

                    @if ($canManage && $document->isDraft())
                        <a
                            href="{{ route('documentation.penetapan.edit', $document) }}"
                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="upload-cloud" class="h-4 w-4" />
                            Unggah File
                        </a>
                    @endif
                </div>
            @endif
        </section>
    </div>
</x-app-layout>