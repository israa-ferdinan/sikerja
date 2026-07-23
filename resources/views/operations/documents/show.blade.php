<x-app-layout>
    @php
        $user = auth()->user();

        $isManager =
            $user->isAdmin()
            || $user->isKanit()
            || $user->isGkm();

        $statusClass = match ($document->status) {
            \App\Models\OperationalDocument::STATUS_PUBLISHED
                => 'border-emerald-200 bg-emerald-50 text-emerald-700',

            \App\Models\OperationalDocument::STATUS_ARCHIVED
                => 'border-slate-200 bg-slate-100 text-slate-600',

            default
                => 'border-amber-200 bg-amber-50 text-amber-700',
        };

        $statusIcon = match ($document->status) {
            \App\Models\OperationalDocument::STATUS_PUBLISHED
                => 'check-circle',

            \App\Models\OperationalDocument::STATUS_ARCHIVED
                => 'archive',

            default
                => 'edit-3',
        };
    @endphp

    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[240px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="archive" class="h-4 w-4" />
                        Arsip Operasional
                    </div>

                    <h1 class="mt-5 max-w-4xl text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $document->title }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100">
                            {{ $document->category_label }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusClass }}">
                            <x-icon name="{{ $statusIcon }}" class="h-3.5 w-3.5" />
                            {{ $document->status_label }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100">
                            <x-icon name="lock" class="h-3.5 w-3.5" />
                            {{ $document->visibility_label }}
                        </span>
                    </div>

                    @if ($document->document_number)
                        <p class="mt-4 text-sm leading-6 text-slate-300">
                            Nomor Dokumen:
                            <span class="font-semibold text-white">
                                {{ $document->document_number }}
                            </span>
                        </p>
                    @endif
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row lg:w-auto lg:flex-col lg:pl-8">
                    <a
                        href="{{ route('operations.documents.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>

                    <a
                        href="{{ route('operations.documents.download', $document) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                    >
                        <x-icon name="download" class="h-4 w-4" />
                        Download
                    </a>

                    @if ($isManager && $document->isEditable())
                        <a
                            href="{{ route('operations.documents.edit', $document) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-300/30 bg-sky-400/10 px-4 py-2.5 text-sm font-semibold text-sky-100 shadow-sm transition hover:bg-sky-400/20"
                        >
                            <x-icon name="edit-3" class="h-4 w-4" />
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-inset ring-emerald-200">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>

                    <p class="text-sm font-medium leading-6 text-emerald-800">
                        {{ session('success') }}
                    </p>
                </div>
            </section>
        @endif

        @if (session('error'))
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-700 ring-1 ring-inset ring-rose-200">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </div>

                    <p class="text-sm font-medium leading-6 text-rose-800">
                        {{ session('error') }}
                    </p>
                </div>
            </section>
        @endif

        {{-- STATUS LIFECYCLE --}}
        <section class="rounded-2xl border p-5 shadow-sm {{ $statusClass }}">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/80 ring-1 ring-inset ring-current/10">
                    <x-icon name="{{ $statusIcon }}" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-sm font-semibold">
                        Status {{ $document->status_label }}
                    </h2>

                    <p class="mt-1 text-sm leading-6">
                        @if ($document->status === \App\Models\OperationalDocument::STATUS_DRAFT)
                            Dokumen masih dapat diedit, diganti file, dipublikasikan, atau dihapus.
                            Pegawai belum dapat melihat dokumen ini.
                        @elseif ($document->status === \App\Models\OperationalDocument::STATUS_PUBLISHED)
                            Dokumen sudah dipublikasikan dan hanya dapat diarsipkan.
                            Dokumen tidak dapat diedit kembali.
                        @else
                            Dokumen sudah diarsipkan dan menjadi read-only.
                        @endif
                    </p>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="space-y-6">
                {{-- INFORMASI DOKUMEN --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Informasi Dokumen
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Identitas dan keterangan arsip operasional.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Judul Dokumen
                            </p>

                            <p class="mt-2 text-lg font-semibold leading-7 text-slate-900">
                                {{ $document->title }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Kategori
                                </p>

                                <p class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $document->category_label }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Nomor Dokumen
                                </p>

                                <p class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $document->document_number ?: '-' }}
                                </p>
                            </div>
                        </div>

                        @if ($document->description)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Keterangan
                                </p>

                                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">
                                    {{ $document->description }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center">
                                <p class="text-sm text-slate-500">
                                    Tidak ada keterangan tambahan.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- FILE ARSIP --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <x-icon name="file-text" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    File Arsip
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    File disimpan pada storage private dan diakses melalui sistem.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 ring-1 ring-inset ring-slate-200">
                                        <x-icon name="file-text" class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-semibold leading-6 text-slate-900">
                                            {{ $document->file_original_name }}
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            {{ $document->file_mime_type ?? '-' }}
                                            ·
                                            {{ $document->file_size_label }}
                                        </p>
                                    </div>
                                </div>

                                <a
                                    href="{{ route('operations.documents.download', $document) }}"
                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                >
                                    <x-icon name="download" class="h-4 w-4" />
                                    Download File
                                </a>
                            </div>

                            @if ($isManager && $document->isEditable())
                                <div class="mt-5 border-t border-slate-200 pt-4">
                                    <a
                                        href="{{ route('operations.documents.edit', $document) }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                                    >
                                        <x-icon name="edit-3" class="h-4 w-4" />
                                        Edit Metadata atau Ganti File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- LIFECYCLE ACTION --}}
                @if ($isManager)
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <x-icon name="activity" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Lifecycle Dokumen
                                    </h2>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        Kelola status dokumen sesuai tahapan arsip.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 p-5 sm:p-6">
                            @if ($document->canPublish())
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                <x-icon name="check-circle" class="h-5 w-5" />
                                            </div>

                                            <div>
                                                <h3 class="text-sm font-semibold text-emerald-900">
                                                    Publikasikan Dokumen
                                                </h3>

                                                <p class="mt-1 text-sm leading-6 text-emerald-700">
                                                    Setelah dipublikasikan, dokumen tidak dapat diedit lagi.
                                                </p>
                                            </div>
                                        </div>

                                        <form
                                            x-data
                                            method="POST"
                                            action="{{ route('operations.documents.publish', $document) }}"
                                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                title: 'Publikasikan Arsip?',
                                                message: 'Dokumen akan dipublikasikan dan tidak dapat diedit lagi. Pegawai dapat melihatnya bila visibilitasnya Unit.',
                                                confirmText: 'Ya, Publikasikan',
                                                cancelText: 'Batal',
                                                variant: 'success',
                                                onConfirm: () => $el.submit()
                                            })"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 sm:w-auto"
                                            >
                                                <x-icon name="check-circle" class="h-4 w-4" />
                                                Publikasikan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if ($document->canArchive())
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 ring-1 ring-inset ring-slate-200">
                                                <x-icon name="archive" class="h-5 w-5" />
                                            </div>

                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900">
                                                    Arsipkan Dokumen
                                                </h3>

                                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                                    Dokumen akan menjadi read-only dan tidak lagi tampil untuk pegawai.
                                                </p>
                                            </div>
                                        </div>

                                        <form
                                            x-data
                                            method="POST"
                                            action="{{ route('operations.documents.archive', $document) }}"
                                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                title: 'Arsipkan Dokumen?',
                                                message: 'Dokumen akan dipindahkan ke status Diarsipkan dan menjadi read-only.',
                                                confirmText: 'Ya, Arsipkan',
                                                cancelText: 'Batal',
                                                variant: 'warning',
                                                onConfirm: () => $el.submit()
                                            })"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 sm:w-auto"
                                            >
                                                <x-icon name="archive" class="h-4 w-4" />
                                                Arsipkan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if ($document->isDeletable())
                                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-700 ring-1 ring-inset ring-rose-200">
                                                <x-icon name="trash-2" class="h-5 w-5" />
                                            </div>

                                            <div>
                                                <h3 class="text-sm font-semibold text-rose-900">
                                                    Hapus Dokumen Draft
                                                </h3>

                                                <p class="mt-1 text-sm leading-6 text-rose-700">
                                                    Metadata dan file pada storage akan dihapus permanen.
                                                </p>
                                            </div>
                                        </div>

                                        <form
                                            x-data
                                            method="POST"
                                            action="{{ route('operations.documents.destroy', $document) }}"
                                            x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Arsip Draft?',
                                                message: 'Dokumen dan file arsip akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
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
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 sm:w-auto"
                                            >
                                                <x-icon name="trash-2" class="h-4 w-4" />
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if (
                                ! $document->canPublish()
                                && ! $document->canArchive()
                                && ! $document->isDeletable()
                            )
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 ring-1 ring-inset ring-slate-200">
                                            <x-icon name="lock" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                Dokumen Read-only
                                            </h3>

                                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                                Tidak ada aksi lifecycle yang tersedia untuk status ini.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif
            </div>

            {{-- SIDEBAR METADATA --}}
            <aside class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <x-icon name="info" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Metadata
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Informasi pengelolaan dokumen.
                                </p>
                            </div>
                        </div>
                    </div>

                    <dl class="divide-y divide-slate-100">
                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Unit
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $document->unit?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Periode
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $document->period_label }}
                            </dd>
                        </div>

                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tanggal Dokumen
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $document->document_date?->format('d M Y') ?? '-' }}
                            </dd>
                        </div>

                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Visibilitas
                            </dt>

                            <dd class="mt-2">
                                <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $document->visibility_label }}
                                </span>
                            </dd>
                        </div>

                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Diunggah Oleh
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $document->uploadedBy?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Terakhir Diperbarui Oleh
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $document->updatedBy?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="px-5 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tanggal Upload
                            </dt>

                            <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                {{ $document->created_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </section>

                @if ($document->published_at || $document->archived_at)
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <x-icon name="history" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Riwayat Status
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <dl class="divide-y divide-slate-100">
                            @if ($document->published_at)
                                <div class="px-5 py-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Dipublikasikan Oleh
                                    </dt>

                                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                        {{ $document->publishedBy?->name ?? '-' }}
                                    </dd>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $document->published_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            @endif

                            @if ($document->archived_at)
                                <div class="px-5 py-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Diarsipkan Pada
                                    </dt>

                                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                        {{ $document->archived_at->format('d M Y H:i') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </section>
                @endif

                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 ring-1 ring-inset ring-slate-200">
                            <x-icon name="lock" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">
                                Protected Download
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                File tidak dapat diakses langsung dari public storage.
                                Sistem memeriksa role, unit, status, dan visibilitas sebelum download.
                            </p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>