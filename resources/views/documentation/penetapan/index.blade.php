<x-app-layout>
    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="file-check-2" class="h-4 w-4" />
                        Penetapan
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $currentCategoryLabel ?: 'Dokumen Penetapan SIM TI' }}
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        @if ($currentCategoryLabel)
                            Kelola dokumen, referensi, dan informasi pendukung
                            {{ $currentCategoryLabel }} sebagai dasar pelaksanaan kegiatan Unit SIM TI.
                        @else
                            Kelola dokumen dasar berupa Tupoksi, Struktur Organisasi, SK SDM,
                            Standar, SOP, dan Formulir Unit SIM TI.
                        @endif
                    </p>

                    @if ($canManage)
                        <div class="mt-5 flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                Draft dapat diedit
                            </span>

                            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                Published dan Archived terkunci
                            </span>
                        </div>
                    @endif
                </div>

                @if ($canManage)
                    <div class="shrink-0 lg:pl-8">
                        <a
                            href="{{ route('documentation.penetapan.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900 sm:w-auto"
                        >
                            <x-icon name="file-text" class="h-4 w-4" />
                            Tambah Dokumen
                        </a>
                    </div>
                @endif
            </div>
        </section>

        @if ($currentCategoryLabel)
            <section class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-sm text-sky-800 shadow-sm">
                <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-sky-600" />

                <div>
                    <p class="font-semibold">
                        Kategori Aktif
                    </p>

                    <p class="mt-0.5 leading-6">
                        {{ $currentCategoryLabel }}
                    </p>
                </div>
            </section>
        @endif

            @if ($selectedCategory === \App\Models\DocumentationDocument::CATEGORY_TUPOKSI_SIM_TI)
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Daftar Tupoksi SIM TI
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Referensi read-only yang bersumber dari Master Data Tupoksi.
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                            {{ $tupoksiItems->count() }} tupoksi
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-5 sm:p-6 xl:grid-cols-2">
                    @forelse ($tupoksiItems as $tupoksi)
                        @php
                            $statusClass = $tupoksi->is_active
                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                : 'bg-slate-100 text-slate-600 ring-slate-200';
                        @endphp

                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-sky-200 hover:shadow-md">
                            <div class="p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                        {{ $tupoksi->unit?->name ?: 'Tanpa Unit' }}
                                    </span>

                                    @if ($tupoksi->classification)
                                        <span class="inline-flex items-center rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700 ring-1 ring-inset ring-violet-200">
                                            {{ $tupoksi->classification->name }}
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                        {{ $tupoksi->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>

                                <h3 class="mt-4 text-base font-semibold leading-6 text-slate-900">
                                    {{ $tupoksi->name }}
                                </h3>

                                <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Deskripsi Tupoksi
                                    </p>

                                    <p class="mt-2 text-sm leading-7 text-slate-600">
                                        {{ $tupoksi->description ?: 'Belum ada deskripsi Tupoksi.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 bg-slate-50 px-5 py-4">
                                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Jenis Objek
                                        </dt>

                                        <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                            {{ $tupoksi->object_type_label ?: '-' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Detail Objek
                                        </dt>

                                        <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                            {{ $tupoksi->work_object_label ?: '-' }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-5 border-t border-slate-200 pt-4">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                PIC Tupoksi
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Pegawai yang terhubung dengan Tupoksi ini.
                                            </p>
                                        </div>

                                        <span class="inline-flex w-fit items-center rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                            {{ $tupoksi->employees->count() }} PIC
                                        </span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        @forelse ($tupoksi->employees as $employee)
                                            <div class="flex min-h-[72px] items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                                                    {{ strtoupper(mb_substr($employee->name, 0, 1)) }}
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        <p class="truncate text-sm font-semibold text-slate-900">
                                                            {{ $employee->name }}
                                                        </p>

                                                        @if ((bool) $employee->pivot?->is_primary)
                                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-1.5 py-0.5 text-[10px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                                PIC Utama
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <p class="mt-1 truncate text-xs text-slate-500">
                                                        {{ $employee->jobPosition?->name ?: 'Jabatan belum diisi' }}
                                                    </p>

                                                    @if ($employee->unit?->name)
                                                        <p class="mt-1 truncate text-xs text-sky-600">
                                                            {{ $employee->unit->name }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-5 text-center sm:col-span-2">
                                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                                    <x-icon name="user-x" class="h-5 w-5" />
                                                </div>

                                                <p class="mt-2 text-sm font-semibold text-slate-700">
                                                    Belum ada PIC
                                                </p>

                                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                                    Tupoksi ini belum dihubungkan ke pegawai.
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="xl:col-span-2 px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                <x-icon name="clipboard-list" class="h-7 w-7" />
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-slate-900">
                                Belum ada Tupoksi SIM TI
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                Data Tupoksi akan mengikuti Master Data Tupoksi yang sudah dibuat sebelumnya.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endif
        @if ($selectedCategory === \App\Models\DocumentationDocument::CATEGORY_STRUKTUR_ORGANISASI)
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                {{-- HEADER --}}
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="network" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Struktur Organisasi SIM TI
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Struktur read-only berdasarkan data pegawai aktif dan role user yang terdaftar.
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                            {{ ($organizationHead ? 1 : 0) + $organizationMembers->count() }} personel
                        </span>
                    </div>
                </div>

                <div class="space-y-8 p-5 sm:p-6">
                    {{-- KEPALA UNIT --}}
                    @if ($organizationHead)
                        <div class="flex flex-col items-center">
                            <article class="w-full max-w-xl overflow-hidden rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 via-white to-cyan-50 shadow-sm">
                                <div class="p-6 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-950 text-xl font-bold text-white shadow-sm">
                                        {{ strtoupper(mb_substr($organizationHead->name, 0, 1)) }}
                                    </div>

                                    <span class="mt-4 inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                        Kepala Unit
                                    </span>

                                    <h3 class="mt-3 text-lg font-bold text-slate-900 sm:text-xl">
                                        {{ $organizationHead->name }}
                                    </h3>

                                    <p class="mt-1 text-sm font-semibold text-sky-700">
                                        {{ $organizationHead->jobPosition?->name ?: 'Kepala Unit / Kanit' }}
                                    </p>

                                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                                        @if ($organizationHead->unit)
                                            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                                {{ $organizationHead->unit->name }}
                                            </span>
                                        @endif

                                        @if ($organizationHead->nip)
                                            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                                NIP: {{ $organizationHead->nip }}
                                            </span>
                                        @endif
                                    </div>

                                    @if (isset($organizationHead->duties_count))
                                        <p class="mt-4 text-xs text-slate-500">
                                            {{ $organizationHead->duties_count }} tupoksi personal
                                        </p>
                                    @endif
                                </div>
                            </article>

                            @if ($organizationMembers->isNotEmpty())
                                <div class="h-8 w-px bg-slate-300"></div>

                                <div class="relative w-full max-w-5xl">
                                    <div class="h-px w-full bg-slate-300"></div>

                                    <div class="absolute left-1/2 top-0 h-4 w-px -translate-x-1/2 bg-slate-300"></div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-6 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                <x-icon name="user-x" class="h-6 w-6" />
                            </div>

                            <h3 class="mt-3 font-semibold text-amber-900">
                                Kepala Unit belum terdeteksi
                            </h3>

                            <p class="mx-auto mt-1 max-w-xl text-sm leading-6 text-amber-700">
                                Belum ada pegawai aktif yang terhubung dengan user ber-role Kanit.
                            </p>
                        </div>
                    @endif

                    {{-- ANGGOTA UNIT --}}
                    @if ($organizationMembers->isNotEmpty())
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-900">
                                        Anggota Unit
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Daftar pegawai aktif yang berada dalam struktur Unit SIM TI.
                                    </p>
                                </div>

                                <span class="inline-flex w-fit items-center rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                    {{ $organizationMembers->count() }} anggota
                                </span>
                            </div>

                            <div class="max-h-[620px] overflow-y-auto pr-1">
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($organizationMembers as $employee)
                                        <article class="flex min-h-[118px] items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-200 hover:shadow-md">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-950 text-sm font-bold text-white">
                                                {{ strtoupper(mb_substr($employee->name, 0, 1)) }}
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="truncate text-sm font-semibold text-slate-900">
                                                        {{ $employee->name }}
                                                    </h4>

                                                    @if ($employee->user?->isGkm())
                                                        <span class="inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-semibold text-violet-700 ring-1 ring-inset ring-violet-200">
                                                            GKM
                                                        </span>
                                                    @elseif ($employee->user?->isKanit())
                                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                            Kanit
                                                        </span>
                                                    @elseif ($employee->user?->isPegawai())
                                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                            Pegawai
                                                        </span>
                                                    @endif
                                                </div>

                                                <p class="mt-1 truncate text-xs text-slate-500">
                                                    {{ $employee->jobPosition?->name ?: 'Jabatan belum diisi' }}
                                                </p>

                                                @if ($employee->unit)
                                                    <p class="mt-1 truncate text-xs text-sky-600">
                                                        {{ $employee->unit->name }}
                                                    </p>
                                                @endif

                                                <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-slate-400">
                                                    @if ($employee->nip)
                                                        <span class="truncate">
                                                            NIP: {{ $employee->nip }}
                                                        </span>
                                                    @endif

                                                    @if (isset($employee->duties_count))
                                                        <span>
                                                            {{ $employee->duties_count }} tupoksi
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                <x-icon name="users" class="h-7 w-7" />
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-slate-900">
                                Belum ada anggota struktur
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                Data struktur akan mengikuti Master Data Pegawai yang sudah dibuat sebelumnya.
                            </p>
                        </div>
                    @endif
                </div>
            </section>
        @endif                     
            {{-- FILTER DOKUMEN --}}
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
                            Cari dokumen berdasarkan status, judul, nomor, deskripsi, atau revisi.
                        </p>
                    </div>
                </div>

                <form
                    method="GET"
                    action="{{ route('documentation.penetapan.index') }}"
                    class="space-y-5"
                >
                    @if ($selectedCategory)
                        <input
                            type="hidden"
                            name="category"
                            value="{{ $selectedCategory }}"
                        >
                    @endif

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                        @if ($canManage)
                            <div class="lg:col-span-3">
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

                                    @foreach ($statuses as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            @selected($selectedStatus === $value)
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="{{ $canManage ? 'lg:col-span-6' : 'lg:col-span-9' }}">
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
                                    value="{{ $search }}"
                                    placeholder="Cari judul, nomor, deskripsi, atau revisi"
                                    class="block w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                >
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end lg:col-span-3">
                            <a
                                href="{{ $selectedCategory
                                    ? route('documentation.penetapan.index', ['category' => $selectedCategory])
                                    : route('documentation.penetapan.index') }}"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                            >
                                <x-icon name="rotate-ccw" class="h-4 w-4" />
                                Reset Filter
                            </a>

                            <button
                                type="submit"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="filter" class="h-4 w-4" />
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            @if (! empty($activeFilters))
                <section class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-sky-900">
                                Filter aktif
                            </p>

                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($activeFilters as $filter)
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">
                                        {{ $filter['label'] }}: {{ $filter['value'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <a
                            href="{{ $selectedCategory
                                ? route('documentation.penetapan.index', ['category' => $selectedCategory])
                                : route('documentation.penetapan.index') }}"
                            class="inline-flex shrink-0 items-center justify-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950"
                        >
                            <x-icon name="x" class="h-4 w-4" />
                            Hapus Filter
                        </a>
                    </div>
                </section>
            @endif

            {{-- DAFTAR DOKUMEN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="file-text" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    @if ($selectedCategory === \App\Models\DocumentationDocument::CATEGORY_TUPOKSI_SIM_TI)
                                        File Pendukung Tupoksi SIM TI
                                    @elseif ($selectedCategory === \App\Models\DocumentationDocument::CATEGORY_STRUKTUR_ORGANISASI)
                                        File Pendukung Struktur Organisasi
                                    @else
                                        Daftar Dokumen Penetapan
                                    @endif
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    @if ($selectedCategory === \App\Models\DocumentationDocument::CATEGORY_TUPOKSI_SIM_TI)
                                        Dokumen pendukung resmi untuk data Tupoksi SIM TI.
                                    @elseif ($selectedCategory === \App\Models\DocumentationDocument::CATEGORY_STRUKTUR_ORGANISASI)
                                        SK struktur, bagan resmi, atau dokumen organisasi lainnya.
                                    @elseif ($canManage)
                                        Menampilkan dokumen Draft, Published, dan Archived.
                                    @else
                                        Menampilkan dokumen Published yang dapat diakses pegawai.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                            {{ $documents->total() }} dokumen
                        </span>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($documents as $document)
                        @php
                            $statusClass = match ($document->status) {
                                'published' =>
                                    'bg-emerald-50 text-emerald-700 ring-emerald-200',

                                'archived' =>
                                    'bg-violet-50 text-violet-700 ring-violet-200',

                                default =>
                                    'bg-amber-50 text-amber-700 ring-amber-200',
                            };
                        @endphp

                        <article class="p-5 transition hover:bg-slate-50/80 sm:p-6">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                        <x-icon name="file-text" class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                {{ $document->category_label }}
                                            </span>

                                            @if ($canManage)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                                    {{ $document->status_label }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="mt-3 text-base font-semibold text-slate-900 sm:text-lg">
                                            {{ $document->title }}
                                        </h3>

                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 sm:text-sm">
                                            @if ($document->document_number)
                                                <span>
                                                    Nomor: {{ $document->document_number }}
                                                </span>
                                            @endif

                                            @if ($document->revision)
                                                <span>
                                                    Revisi: {{ $document->revision }}
                                                </span>
                                            @endif

                                            @if ($document->effective_date)
                                                <span>
                                                    Berlaku: {{ $document->effective_date->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($document->description)
                                            <p class="mt-3 max-w-4xl line-clamp-2 text-sm leading-6 text-slate-600">
                                                {{ $document->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <a
                                    href="{{ route('documentation.penetapan.show', $document) }}"
                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                >
                                    Detail
                                    <x-icon name="chevron-right" class="h-4 w-4" />
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                <x-icon name="file-text" class="h-7 w-7" />
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-slate-900">
                                @if (! empty($activeFilters))
                                    Tidak ada dokumen yang cocok
                                @else
                                    Belum ada dokumen Penetapan
                                @endif
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                @if (! empty($activeFilters))
                                    Ubah filter untuk menemukan dokumen yang sesuai.
                                @elseif ($canManage)
                                    Tambahkan dokumen dasar Unit SIM TI sesuai kategori yang dibutuhkan.
                                @else
                                    Dokumen yang sudah dipublish akan tampil di halaman ini.
                                @endif
                            </p>

                            @if (! empty($activeFilters))
                                <a
                                    href="{{ $selectedCategory
                                        ? route('documentation.penetapan.index', ['category' => $selectedCategory])
                                        : route('documentation.penetapan.index') }}"
                                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                >
                                    <x-icon name="rotate-ccw" class="h-4 w-4" />
                                    Reset Filter
                                </a>
                            @elseif ($canManage)
                                <a
                                    href="{{ route('documentation.penetapan.create') }}"
                                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                >
                                    <x-icon name="file-text" class="h-4 w-4" />
                                    Tambah Dokumen
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