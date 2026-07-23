<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="rocket" class="h-4 w-4" />
                        Pengembangan
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Rencana Pengembangan
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Kelola usulan, persetujuan, progres, dan realisasi rencana pengembangan
                        layanan, aplikasi, dokumentasi, serta infrastruktur Unit SIM TI.
                    </p>
                </div>

                @if ($canManage)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('developments.plans.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900 sm:w-auto"
                        >
                            <x-icon name="rocket" class="h-4 w-4" />
                            Tambah Rencana
                        </a>
                    </div>
                @endif
            </div>
        </section>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm">
                <x-icon name="check-circle" class="mt-0.5 h-5 w-5 text-emerald-600" />

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
            <div class="mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <x-icon name="filter" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Filter Rencana
                        </h2>
                        <p class="mt-0.5 text-sm text-slate-500">
                            Cari dan saring rencana pengembangan berdasarkan data yang tersedia.
                        </p>
                    </div>
                </div>
            </div>

            <form method="GET" class="space-y-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label
                            for="search"
                            class="block text-sm font-semibold text-slate-700"
                        >
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
                                placeholder="Judul atau deskripsi"
                                class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Status</option>

                            @foreach ($statuses as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected(request('status') === $status)
                                >
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            for="category"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Kategori
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Kategori</option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category }}"
                                    @selected(request('category') === $category)
                                >
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            for="priority"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Prioritas
                        </label>

                        <select
                            id="priority"
                            name="priority"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Semua Prioritas</option>

                            @foreach ($priorities as $priority)
                                <option
                                    value="{{ $priority }}"
                                    @selected(request('priority') === $priority)
                                >
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($isAdmin)
                        <div>
                            <label
                                for="unit_id"
                                class="block text-sm font-semibold text-slate-700"
                            >
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
                        href="{{ route('developments.plans.index') }}"
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
            || request()->filled('status')
            || request()->filled('category')
            || request()->filled('priority')
            || request()->filled('unit_id')
        )
            <section class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-sky-900">
                            Filter aktif
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">
                            @if (request()->filled('search'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Pencarian: {{ request('search') }}
                                </span>
                            @endif

                            @if (request()->filled('status'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Status: {{ request('status') }}
                                </span>
                            @endif

                            @if (request()->filled('category'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Kategori: {{ request('category') }}
                                </span>
                            @endif

                            @if (request()->filled('priority'))
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                    Prioritas: {{ request('priority') }}
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
                        href="{{ route('developments.plans.index') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Hapus Filter
                    </a>
                </div>
            </section>
        @endif

        {{-- TABLE --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Daftar Rencana Pengembangan
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Menampilkan {{ $plans->firstItem() ?? 0 }}–{{ $plans->lastItem() ?? 0 }}
                        dari {{ $plans->total() }} rencana.
                    </p>
                </div>
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Judul
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Unit
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Kategori
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Prioritas
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                PIC
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Progress
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($plans as $plan)
                            @php
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
                                        'bg-sky-50 text-sky-700 ring-sky-200',

                                    default =>
                                        'bg-slate-100 text-slate-700 ring-slate-200',
                                };

                                $progress = max(
                                    0,
                                    min(100, (int) $plan->progress_percentage)
                                );
                            @endphp

                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4 align-top">
                                    <a
                                        href="{{ route('developments.plans.show', $plan) }}"
                                        class="font-semibold text-slate-900 hover:text-sky-700"
                                    >
                                        {{ $plan->title }}
                                    </a>

                                    <p class="mt-1 max-w-md line-clamp-2 text-xs leading-5 text-slate-500">
                                        {{ $plan->description ?: 'Tidak ada deskripsi.' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $plan->unit?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $plan->category }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $priorityClass }}">
                                        {{ $plan->priority }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-col items-start gap-1.5">
                                        <span class="text-sm text-slate-700">
                                            {{ $plan->picEmployee?->name ?? '-' }}
                                        </span>

                                        @if (
                                            auth()->user()?->employee
                                            && (int) $plan->pic_employee_id === (int) auth()->user()->employee->id
                                        )
                                            <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-inset ring-cyan-200">
                                                Anda PIC
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                        {{ $plan->status }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="min-w-36">
                                        <div class="mb-2 flex items-center justify-between gap-3">
                                            <span class="text-xs font-medium text-slate-500">
                                                Capaian
                                            </span>

                                            <span class="text-xs font-semibold text-slate-700">
                                                {{ $progress }}%
                                            </span>
                                        </div>

                                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                            <div
                                                class="h-full rounded-full bg-sky-600 transition-all"
                                                style="width: {{ $progress }}%"
                                            ></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-right align-top">
                                    <a
                                        href="{{ route('developments.plans.show', $plan) }}"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                    >
                                        Detail
                                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="rocket" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        Belum ada rencana pengembangan
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        Belum ada data yang sesuai dengan filter atau belum ada
                                        rencana pengembangan yang dibuat.
                                    </p>

                                    @if ($canManage)
                                        <a
                                            href="{{ route('developments.plans.create') }}"
                                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                        >
                                            <x-icon name="plus" class="h-4 w-4" />
                                            Tambah Rencana
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
                @forelse ($plans as $plan)
                    @php
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
                                'bg-sky-50 text-sky-700 ring-sky-200',

                            default =>
                                'bg-slate-100 text-slate-700 ring-slate-200',
                        };

                        $progress = max(
                            0,
                            min(100, (int) $plan->progress_percentage)
                        );
                    @endphp

                    <article class="p-5">
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                {{ $plan->status }}
                            </span>

                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $priorityClass }}">
                                {{ $plan->priority }}
                            </span>

                            @if (
                                auth()->user()?->employee
                                && (int) $plan->pic_employee_id === (int) auth()->user()->employee->id
                            )
                                <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-inset ring-cyan-200">
                                    Anda PIC
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-3 text-base font-semibold text-slate-900">
                            {{ $plan->title }}
                        </h3>

                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">
                            {{ $plan->description ?: 'Tidak ada deskripsi.' }}
                        </p>

                        <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $plan->unit?->name ?? '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Kategori
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $plan->category }}
                                </dd>
                            </div>

                            <div class="col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    PIC
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $plan->picEmployee?->name ?? '-' }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">
                                    Progress
                                </span>

                                <span class="text-xs font-bold text-slate-700">
                                    {{ $progress }}%
                                </span>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-sky-600"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>
                        </div>

                        <a
                            href="{{ route('developments.plans.show', $plan) }}"
                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Detail
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </a>
                    </article>
                @empty
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="rocket" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-slate-900">
                            Belum ada rencana pengembangan
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Belum ada data yang sesuai dengan filter aktif.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($plans->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $plans->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>