<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="file-text" class="h-4 w-4" />
                        Pengembangan
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Dokumen Pengembangan
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Kelola dokumen pendukung yang berdiri sendiri maupun berkaitan dengan
                        rencana pengembangan layanan, aplikasi, dokumentasi, dan infrastruktur SIM TI.
                    </p>
                </div>

                @if ($canManage)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('developments.documents.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900 sm:w-auto"
                        >
                            <x-icon name="upload-cloud" class="h-4 w-4" />
                            Unggah Dokumen
                        </a>
                    </div>
                @endif
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

        {{-- FILTER --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <x-icon name="filter" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Filter Dokumen
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500">
                        Cari dan saring dokumen berdasarkan jenis, visibilitas, rencana, dan unit.
                    </p>
                </div>
            </div>

            <form method="GET" class="space-y-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label for="search" class="block text-sm font-semibold text-slate-700">
                            Pencarian
                        </label>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-icon name="search" class="h-4 w-4 text-slate-400" />
                            </div>

                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Judul atau nama file"
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="document_type" class="block text-sm font-semibold text-slate-700">
                            Jenis Dokumen
                        </label>

                        <select
                            id="document_type"
                            name="document_type"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Jenis</option>

                            @foreach ($documentTypes as $type)
                                <option
                                    value="{{ $type }}"
                                    @selected(request('document_type') === $type)
                                >
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="visibility" class="block text-sm font-semibold text-slate-700">
                            Visibilitas
                        </label>

                        <select
                            id="visibility"
                            name="visibility"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Visibilitas</option>

                            @foreach ($visibilities as $visibility)
                                <option
                                    value="{{ $visibility }}"
                                    @selected(request('visibility') === $visibility)
                                >
                                    {{ $visibility }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="development_plan_id" class="block text-sm font-semibold text-slate-700">
                            Rencana Terkait
                        </label>

                        <select
                            id="development_plan_id"
                            name="development_plan_id"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Rencana</option>

                            <option
                                value="none"
                                @selected(request('development_plan_id') === 'none')
                            >
                                Dokumen Mandiri
                            </option>

                            @foreach ($plans as $plan)
                                <option
                                    value="{{ $plan->id }}"
                                    @selected((string) request('development_plan_id') === (string) $plan->id)
                                >
                                    {{ $plan->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($isAdmin)
                        <div>
                            <label for="unit_id" class="block text-sm font-semibold text-slate-700">
                                Unit
                            </label>

                            <select
                                id="unit_id"
                                name="unit_id"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                                <option value="">Semua Unit</option>

                                @foreach ($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        @selected((string) request('unit_id') === (string) $unit->id)
                                    >
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('developments.documents.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                    >
                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                        Reset Filter
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                    >
                        <x-icon name="filter" class="h-4 w-4" />
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </section>

        {{-- ACTIVE FILTER --}}
        @if (
            request()->filled('search')
            || request()->filled('document_type')
            || request()->filled('visibility')
            || request()->filled('development_plan_id')
            || request()->filled('unit_id')
        )
            <section class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-sky-900">
                            Filter aktif
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">
                            @if (request()->filled('search'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Pencarian: {{ request('search') }}
                                </span>
                            @endif

                            @if (request()->filled('document_type'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Jenis: {{ request('document_type') }}
                                </span>
                            @endif

                            @if (request()->filled('visibility'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Visibilitas: {{ request('visibility') }}
                                </span>
                            @endif

                            @if (request()->filled('development_plan_id'))
                                @php
                                    $selectedPlanLabel = request('development_plan_id') === 'none'
                                        ? 'Dokumen Mandiri'
                                        : $plans->firstWhere(
                                            'id',
                                            (int) request('development_plan_id')
                                        )?->title;
                                @endphp

                                <span class="inline-flex max-w-full items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    <span class="truncate">
                                        Rencana: {{ $selectedPlanLabel ?? '-' }}
                                    </span>
                                </span>
                            @endif

                            @if ($isAdmin && request()->filled('unit_id'))
                                @php
                                    $selectedUnit = $units->firstWhere(
                                        'id',
                                        (int) request('unit_id')
                                    );
                                @endphp

                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Unit: {{ $selectedUnit?->name ?? '-' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <a
                        href="{{ route('developments.documents.index') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Hapus Filter
                    </a>
                </div>
            </section>
        @endif

        {{-- DOCUMENT LIST --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-semibold text-slate-900">
                    Daftar Dokumen Pengembangan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Menampilkan {{ $documents->firstItem() ?? 0 }}–{{ $documents->lastItem() ?? 0 }}
                    dari {{ $documents->total() }} dokumen.
                </p>
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Dokumen
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Jenis
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Rencana Terkait
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Unit
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Visibilitas
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Pengunggah
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($documents as $document)
                            @php
                                $visibilityClass = match (strtolower($document->visibility)) {
                                    'unit' =>
                                        'bg-sky-50 text-sky-700 ring-sky-200',

                                    'restricted' =>
                                        'bg-amber-50 text-amber-700 ring-amber-200',

                                    default =>
                                        'bg-slate-100 text-slate-700 ring-slate-200',
                                };
                            @endphp

                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                            <x-icon name="file-text" class="h-5 w-5" />
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900">
                                                {{ $document->title }}
                                            </p>

                                            <p class="mt-1 max-w-sm truncate text-xs text-slate-500">
                                                {{ $document->original_name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $document->document_type }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    @if ($document->developmentPlan)
                                        <a
                                            href="{{ route('developments.plans.show', $document->developmentPlan) }}"
                                            class="line-clamp-2 max-w-xs text-sm font-medium text-slate-700 hover:text-sky-700"
                                        >
                                            {{ $document->developmentPlan->title }}
                                        </a>
                                    @else
                                        <span class="text-sm text-slate-500">
                                            Dokumen Mandiri
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $document->unit?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $visibilityClass }}">
                                        {{ $document->visibility }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $document->uploader?->name ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-right align-top">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a
                                            href="{{ route('developments.documents.download', $document) }}"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                        >
                                            <x-icon name="download" class="h-3.5 w-3.5" />
                                            Unduh
                                        </a>

                                        @if ($canManage)
                                            <a
                                                href="{{ route('developments.documents.edit', $document) }}"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                            >
                                                <x-icon name="edit-3" class="h-3.5 w-3.5" />
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('developments.documents.destroy', $document) }}"
                                                onsubmit="return confirm('Hapus dokumen pengembangan ini? File juga akan dihapus dari penyimpanan dan tidak dapat dipulihkan.')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 shadow-sm transition hover:border-rose-300 hover:bg-rose-100"
                                                >
                                                    <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="file-text" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        Belum ada dokumen pengembangan
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        Belum ada dokumen yang sesuai dengan filter atau belum
                                        ada dokumen pengembangan yang diunggah.
                                    </p>

                                    @if ($canManage)
                                        <a
                                            href="{{ route('developments.documents.create') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="upload-cloud" class="h-4 w-4" />
                                            Unggah Dokumen
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARDS --}}
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($documents as $document)
                    @php
                        $visibilityClass = match (strtolower($document->visibility)) {
                            'unit' =>
                                'bg-sky-50 text-sky-700 ring-sky-200',

                            'restricted' =>
                                'bg-amber-50 text-amber-700 ring-amber-200',

                            default =>
                                'bg-slate-100 text-slate-700 ring-slate-200',
                        };
                    @endphp

                    <article class="p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="file-text" class="h-5 w-5" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $visibilityClass }}">
                                        {{ $document->visibility }}
                                    </span>

                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                        {{ $document->document_type }}
                                    </span>
                                </div>

                                <h3 class="mt-3 text-base font-semibold text-slate-900">
                                    {{ $document->title }}
                                </h3>

                                <p class="mt-1 break-all text-xs leading-5 text-slate-500">
                                    {{ $document->original_name }}
                                </p>
                            </div>
                        </div>

                        <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $document->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Pengunggah
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $document->uploader?->name ?? '-' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Rencana Terkait
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $document->developmentPlan?->title ?? 'Dokumen Mandiri' }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-3">
                            <a
                                href="{{ route('developments.documents.download', $document) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                            >
                                <x-icon name="download" class="h-4 w-4" />
                                Unduh
                            </a>

                            @if ($canManage)
                                <a
                                    href="{{ route('developments.documents.edit', $document) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                                >
                                    <x-icon name="edit-3" class="h-4 w-4" />
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('developments.documents.destroy', $document) }}"
                                    onsubmit="return confirm('Hapus dokumen pengembangan ini? File juga akan dihapus dari penyimpanan dan tidak dapat dipulihkan.')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 shadow-sm transition hover:border-rose-300 hover:bg-rose-100"
                                    >
                                        <x-icon name="trash-2" class="h-4 w-4" />
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="file-text" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-slate-900">
                            Belum ada dokumen pengembangan
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Belum ada dokumen yang sesuai dengan filter aktif.
                        </p>

                        @if ($canManage)
                            <a
                                href="{{ route('developments.documents.create') }}"
                                class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="upload-cloud" class="h-4 w-4" />
                                Unggah Dokumen
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($documents->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $documents->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>