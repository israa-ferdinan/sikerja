<x-app-layout>
    @php
        $progress = max(0, min(100, (int) $plan->progress_percentage));

        $statusClass = match ($plan->status) {
            \App\Models\DevelopmentPlan::STATUS_USULAN =>
                'bg-slate-100 text-slate-700 ring-slate-200',

            \App\Models\DevelopmentPlan::STATUS_DISETUJUI =>
                'bg-sky-50 text-sky-700 ring-sky-200',

            \App\Models\DevelopmentPlan::STATUS_DALAM_PROSES =>
                'bg-amber-50 text-amber-700 ring-amber-200',

            \App\Models\DevelopmentPlan::STATUS_SELESAI =>
                'bg-emerald-50 text-emerald-700 ring-emerald-200',

            \App\Models\DevelopmentPlan::STATUS_DITUNDA =>
                'bg-orange-50 text-orange-700 ring-orange-200',

            \App\Models\DevelopmentPlan::STATUS_DIBATALKAN =>
                'bg-rose-50 text-rose-700 ring-rose-200',

            default =>
                'bg-slate-100 text-slate-700 ring-slate-200',
        };

        $priorityClass = match (strtolower($plan->priority)) {
            'tinggi' =>
                'bg-rose-50 text-rose-700 ring-rose-200',

            'sedang' =>
                'bg-amber-50 text-amber-700 ring-amber-200',

            'rendah' =>
                'bg-emerald-50 text-emerald-700 ring-emerald-200',

            default =>
                'bg-slate-100 text-slate-700 ring-slate-200',
        };
    @endphp

    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[240px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="rocket" class="h-4 w-4" />
                            Rencana Pengembangan
                        </span>

                        @if ($isPic)
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold text-emerald-100">
                                <x-icon name="user-check" class="h-4 w-4" />
                                Anda PIC
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-5 max-w-4xl text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $plan->title }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-300">
                        <span class="inline-flex items-center gap-2">
                            <x-icon name="building-2" class="h-4 w-4 text-cyan-300" />
                            {{ $plan->unit?->name ?? '-' }}
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <x-icon name="user" class="h-4 w-4 text-cyan-300" />
                            PIC: {{ $plan->picEmployee?->name ?? 'Belum ditentukan' }}
                        </span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            {{ $plan->category }}
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            Prioritas {{ $plan->priority }}
                        </span>

                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            {{ $plan->status }}
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:max-w-sm lg:flex-wrap lg:justify-end lg:pl-8">
                    <a
                        href="{{ route('developments.plans.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:border-white/30 hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>

                    @if ($canEdit)
                        <a
                            href="{{ route('developments.plans.edit', $plan) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                        >
                            <x-icon name="edit-3" class="h-4 w-4" />
                            Edit
                        </a>
                    @endif

                    @if ($canDelete)
                        <form
                            method="POST"
                            action="{{ route('developments.plans.destroy', $plan) }}"
                            onsubmit="return confirm('Hapus rencana pengembangan ini? Data yang sudah dihapus tidak dapat dipulihkan.')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-300/20 bg-rose-500/15 px-4 py-3 text-sm font-semibold text-rose-100 shadow-sm transition hover:bg-rose-500/25"
                            >
                                <x-icon name="trash-2" class="h-4 w-4" />
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm">
                <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />

                <div class="min-w-0">
                    <p class="font-semibold">Berhasil</p>
                    <p class="mt-0.5 leading-6">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-sm">
                <x-icon name="x-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />

                <div class="min-w-0">
                    <p class="font-semibold">Terjadi Kesalahan</p>
                    <p class="mt-0.5 leading-6">
                        Periksa kembali data pada form yang ingin diperbarui.
                    </p>
                </div>
            </div>
        @endif

        {{-- MAIN LAYOUT --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">

            {{-- LEFT CONTENT --}}
            <div class="min-w-0 space-y-6">

                {{-- PROGRESS SUMMARY --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">
                                Progress Rencana
                            </p>

                            <div class="mt-1 flex items-end gap-2">
                                <span class="text-3xl font-bold tracking-tight text-slate-900">
                                    {{ $progress }}%
                                </span>

                                <span class="pb-1 text-sm text-slate-500">
                                    terselesaikan
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                {{ $plan->status }}
                            </span>

                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $priorityClass }}">
                                Prioritas {{ $plan->priority }}
                            </span>

                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                {{ $plan->category }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-100">
                        <div
                            class="h-full rounded-full bg-sky-600 transition-all"
                            style="width: {{ $progress }}%"
                        ></div>
                    </div>
                </section>

                {{-- DESCRIPTION --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Deskripsi Kebutuhan
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Latar belakang dan kebutuhan yang mendasari pengembangan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $plan->description ?: '-' }}</div>
                    </div>
                </section>

                {{-- OBJECTIVE --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <x-icon name="target" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Tujuan Pengembangan
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Hasil atau manfaat yang ingin dicapai.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $plan->objective ?: '-' }}</div>
                    </div>
                </section>

                {{-- NOTES --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                <x-icon name="sticky-note" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Catatan
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Catatan tambahan maupun perkembangan terakhir rencana.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $plan->notes ?: '-' }}</div>
                    </div>
                </section>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <aside class="min-w-0 space-y-6">

                {{-- UPDATE STATUS --}}
                @if ($canUpdateStatus && count($nextStatuses) > 0)
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <x-icon name="check-circle" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Update Status
                                    </h2>

                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Lanjutkan lifecycle rencana.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('developments.plans.update-status', $plan) }}"
                            class="space-y-4 p-5"
                        >
                            @csrf
                            @method('PATCH')

                            <div>
                                <label
                                    for="status"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Status Baru
                                    <span class="text-rose-600">*</span>
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    required
                                >
                                    <option value="">Pilih Status</option>

                                    @foreach ($nextStatuses as $status)
                                        <option
                                            value="{{ $status }}"
                                            @selected(old('status') === $status)
                                        >
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('status')
                                    <p class="mt-1.5 text-sm font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                            >
                                <x-icon name="check-circle" class="h-4 w-4" />
                                Simpan Status
                            </button>
                        </form>
                    </section>
                @endif

                {{-- UPDATE PROGRESS --}}
                @if ($canUpdateProgress)
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <x-icon name="trending-up" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Update Progress
                                    </h2>

                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Perbarui capaian pekerjaan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('developments.plans.update-progress', $plan) }}"
                            class="space-y-4 p-5"
                        >
                            @csrf
                            @method('PATCH')

                            <div>
                                <label
                                    for="progress_percentage"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Progress (%)
                                    <span class="text-rose-600">*</span>
                                </label>

                                <input
                                    id="progress_percentage"
                                    type="number"
                                    name="progress_percentage"
                                    value="{{ old('progress_percentage', $plan->progress_percentage) }}"
                                    min="0"
                                    max="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    required
                                >

                                @error('progress_percentage')
                                    <p class="mt-1.5 text-sm font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="notes"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Catatan Progress
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="4"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    placeholder="Catatan progress terbaru"
                                >{{ old('notes', $plan->notes) }}</textarea>

                                @error('notes')
                                    <p class="mt-1.5 text-sm font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="check-circle" class="h-4 w-4" />
                                Simpan Progress
                            </button>
                        </form>
                    </section>
                @endif

                {{-- PLAN INFORMATION --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <x-icon name="info" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Informasi Rencana
                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    PIC, jadwal, dan informasi pembuatan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <dl class="divide-y divide-slate-100 px-5">
                        <div class="py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                PIC
                            </dt>

                            <dd class="mt-1.5 flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-900">
                                <span>{{ $plan->picEmployee?->name ?? '-' }}</span>

                                @if ($isPic)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                        Anda PIC
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Target Mulai
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    {{ $plan->target_start_date?->format('d M Y') ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Target Selesai
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    {{ $plan->target_end_date?->format('d M Y') ?? '-' }}
                                </dd>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Realisasi Mulai
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    {{ $plan->actual_start_date?->format('d M Y') ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Realisasi Selesai
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    {{ $plan->actual_end_date?->format('d M Y') ?? '-' }}
                                </dd>
                            </div>
                        </div>

                        <div class="py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Dibuat Oleh
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $plan->creator?->name ?? '-' }}
                            </dd>
                        </div>

                        <div class="py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Dibuat Pada
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $plan->created_at?->format('d M Y H:i') ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- RELATED DOCUMENTS --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <x-icon name="file-text" class="h-5 w-5" />
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Dokumen Terkait
                                        </h2>

                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                            {{ $plan->documents->count() }}
                                        </span>
                                    </div>

                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Dokumen pendukung rencana.
                                    </p>
                                </div>
                            </div>

                            @if ($canUploadDocument)
                                <a
                                    href="{{ route('developments.documents.create', ['development_plan_id' => $plan->id]) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-3.5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                >
                                    <x-icon name="upload-cloud" class="h-4 w-4" />
                                    Unggah Dokumen
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($plan->documents as $document)
                            <article class="p-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                        <x-icon name="file-text" class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            {{ $document->title }}
                                        </h3>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            {{ $document->document_type }}
                                            ·
                                            {{ $document->visibility }}
                                        </p>

                                        <p class="mt-1 break-all text-xs leading-5 text-slate-400">
                                            {{ $document->original_name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <a
                                        href="{{ route('developments.documents.download', $document) }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                    >
                                        <x-icon name="download" class="h-4 w-4" />
                                        Unduh
                                    </a>

                                    @if ($canManage)
                                        <a
                                            href="{{ route('developments.documents.edit', $document) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                        >
                                            <x-icon name="edit-3" class="h-4 w-4" />
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <x-icon name="file-text" class="h-6 w-6" />
                                </div>

                                <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                    Belum ada dokumen terkait
                                </h3>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Dokumen pendukung rencana belum diunggah.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>