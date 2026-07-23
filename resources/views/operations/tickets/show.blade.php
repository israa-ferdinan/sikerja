<x-app-layout>
    @php
        $user = auth()->user();

        $canManageTicket =
            $user->isAdmin()
            || $user->isKanit()
            || $user->isGkm();

        $loggedInEmployeeId = $user->employee?->id;

        $isCurrentTicketPic =
            $loggedInEmployeeId
            && (int) $ticket->assigned_to_employee_id === (int) $loggedInEmployeeId;

        $isClosed = $ticket->isClosed();

        $statusHeroClasses = match ($ticket->status) {
            \App\Models\OperationalTicket::STATUS_BARU =>
                'bg-sky-400/10 text-sky-200 ring-sky-300/20',

            \App\Models\OperationalTicket::STATUS_DIPROSES =>
                'bg-amber-400/10 text-amber-200 ring-amber-300/20',

            \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON =>
                'bg-violet-400/10 text-violet-200 ring-violet-300/20',

            \App\Models\OperationalTicket::STATUS_SELESAI =>
                'bg-emerald-400/10 text-emerald-200 ring-emerald-300/20',

            \App\Models\OperationalTicket::STATUS_DIBATALKAN =>
                'bg-rose-400/10 text-rose-200 ring-rose-300/20',

            default =>
                'bg-white/10 text-slate-200 ring-white/15',
        };

        $statusHeroIcon = match ($ticket->status) {
            \App\Models\OperationalTicket::STATUS_BARU =>
                'circle-dot',

            \App\Models\OperationalTicket::STATUS_DIPROSES =>
                'activity',

            \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON =>
                'clock',

            \App\Models\OperationalTicket::STATUS_SELESAI =>
                'check-circle',

            \App\Models\OperationalTicket::STATUS_DIBATALKAN =>
                'x-circle',

            default =>
                'ticket',
        };

        $priorityHeroClasses = match ($ticket->priority) {
            \App\Models\OperationalTicket::PRIORITY_HIGH =>
                'bg-rose-400/10 text-rose-200 ring-rose-300/20',

            \App\Models\OperationalTicket::PRIORITY_LOW =>
                'bg-slate-400/10 text-slate-200 ring-slate-300/20',

            default =>
                'bg-sky-400/10 text-sky-200 ring-sky-300/20',
        };

        $sourceHeroClasses =
            $ticket->source === \App\Models\OperationalTicket::SOURCE_PUBLIC
                ? 'bg-cyan-400/10 text-cyan-200 ring-cyan-300/20'
                : 'bg-white/10 text-slate-200 ring-white/15';
    @endphp
    <div class="w-full space-y-6">
        @if (session('success'))
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-emerald-900">
                            Proses berhasil
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-emerald-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </section>
        @endif

        @if (session('warning'))
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-amber-900">
                            Perlu diperhatikan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            {{ session('warning') }}
                        </p>
                    </div>
                </div>
            </section>
        @endif

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[240px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="ticket-check" class="h-4 w-4" />
                            Operasional SIM/TI
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusHeroClasses }}">
                            <x-icon
                                name="{{ $statusHeroIcon }}"
                                class="h-3.5 w-3.5"
                            />
                            {{ $ticket->status_label }}
                        </span>

                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $priorityHeroClasses }}">
                            Prioritas {{ $ticket->priority_label }}
                        </span>

                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $sourceHeroClasses }}">
                            {{ $ticket->source_label }}
                        </span>
                    </div>

                    <p class="mt-5 font-mono text-sm font-semibold tracking-wide text-cyan-200">
                        {{ $ticket->ticket_code }}
                    </p>

                    <h1 class="mt-2 max-w-4xl break-words text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $ticket->title }}
                    </h1>

                    <p class="mt-4 max-w-4xl text-sm leading-7 text-slate-300 sm:text-base">
                        {{ $ticket->description
                            ?: 'Tidak ada deskripsi tambahan untuk tiket ini.' }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="user-check" class="h-3.5 w-3.5" />
                            PIC:
                            {{ $ticket->assignedToEmployee?->name
                                ?? 'Belum ditentukan' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="building-2" class="h-3.5 w-3.5" />
                            {{ $ticket->unit?->name ?? 'Unit belum ditentukan' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />
                            {{ $ticket->created_at?->format('d M Y H:i') ?? '-' }}
                        </span>

                        @if ($isCurrentTicketPic)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-400/10 px-3 py-1.5 text-xs font-semibold text-sky-200 ring-1 ring-inset ring-sky-300/20">
                                <x-icon name="user-check" class="h-3.5 w-3.5" />
                                Anda PIC
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:flex-wrap lg:max-w-md lg:justify-end lg:pl-8">
                    <a
                        href="{{ route('operations.tickets.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>
                </div>
            </div>
        </section>

        {{-- LIFECYCLE ALERT --}}
        @if ($ticket->status === \App\Models\OperationalTicket::STATUS_SELESAI)
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-emerald-900">
                            Tiket sudah selesai
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-emerald-700">
                            Tiket ditutup
                            @if ($ticket->closed_at)
                                pada {{ $ticket->closed_at->format('d M Y H:i') }}
                            @endif

                            @if ($ticket->closedByUser)
                                oleh
                                {{ $ticket->closedByUser?->employee?->name
                                    ?? $ticket->closedByUser?->name }}
                            @endif.
                            Status, PIC, dan prioritas tidak dapat diubah kembali.
                        </p>
                    </div>
                </div>
            </section>
        @elseif ($ticket->status === \App\Models\OperationalTicket::STATUS_DIBATALKAN)
            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <x-icon name="x-circle" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-rose-900">
                            Tiket dibatalkan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-rose-700">
                            Tiket berada dalam kondisi read-only. Tiket hanya dapat dihapus
                            oleh pengelola bila belum mempunyai laporan harian terkait.
                        </p>
                    </div>
                </div>
            </section>
        @endif

            <div class="grid gap-5 lg:grid-cols-3">
                <div class="space-y-5 lg:col-span-2">
                    {{-- INFORMASI PEMOHON DAN PERMINTAAN --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <x-icon name="ticket" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Informasi Tiket
                                    </h2>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        Informasi pemohon, jenis permintaan, sumber, dan waktu tiket dibuat.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                            <div class="border-b border-slate-100 p-5 sm:border-r">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Pemohon
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->requester_name }}
                                </dd>
                            </div>

                            <div class="border-b border-slate-100 p-5 xl:border-r">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Kontak Pemohon
                                </dt>

                                <dd class="mt-2 break-words text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->requester_contact ?: '-' }}
                                </dd>
                            </div>

                            <div class="border-b border-slate-100 p-5 sm:border-r xl:border-r-0">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit/Bagian Pemohon
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->requester_unit ?: '-' }}
                                </dd>
                            </div>

                            <div class="border-b border-slate-100 p-5 xl:border-b-0 xl:border-r">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Jenis Permintaan
                                </dt>

                                <dd class="mt-2">
                                    <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $ticket->category_label }}
                                    </span>
                                </dd>
                            </div>

                            <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0 xl:border-r">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Sumber Tiket
                                </dt>

                                <dd class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $ticket->source_label }}
                                </dd>
                            </div>

                            <div class="p-5">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Dibuat
                                </dt>

                                <dd class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $ticket->created_at?->format('d M Y H:i') ?? '-' }}
                                </dd>
                            </div>
                        </dl>

                        <div class="border-t border-slate-100 p-5 sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Keluhan atau Permintaan
                            </p>

                            <h3 class="mt-2 text-base font-semibold leading-7 text-slate-900">
                                {{ $ticket->title }}
                            </h3>

                            @if ($ticket->description)
                                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                                    {{ $ticket->description }}
                                </p>
                            @else
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    Tidak ada deskripsi tambahan.
                                </p>
                            @endif
                        </div>
                    </section>

                    {{-- INFORMASI PENANGANAN --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <x-icon name="user-check" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Informasi Penanganan
                                    </h2>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        Penugasan PIC, unit pengelola, prioritas, dan informasi penutupan tiket.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="border-b border-slate-100 p-5 sm:border-r xl:border-b-0">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    PIC
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->assignedToEmployee?->name
                                        ?? 'Belum ditentukan' }}
                                </dd>

                                @if ($isCurrentTicketPic)
                                    <span class="mt-2 inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                        Anda PIC
                                    </span>
                                @endif
                            </div>

                            <div class="border-b border-slate-100 p-5 xl:border-r xl:border-b-0">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Unit Pengelola
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->unit?->name ?? 'Belum ditentukan' }}
                                </dd>
                            </div>

                            <div class="border-b border-slate-100 p-5 sm:border-r sm:border-b-0 xl:border-r">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Prioritas
                                </dt>

                                <dd class="mt-2">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold
                                        @if ($ticket->priority === \App\Models\OperationalTicket::PRIORITY_HIGH)
                                            border-rose-200 bg-rose-50 text-rose-700
                                        @elseif ($ticket->priority === \App\Models\OperationalTicket::PRIORITY_LOW)
                                            border-slate-200 bg-slate-50 text-slate-600
                                        @else
                                            border-sky-200 bg-sky-50 text-sky-700
                                        @endif
                                    ">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </dd>
                            </div>

                            <div class="p-5">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Status
                                </dt>

                                <dd class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $ticket->status_label }}
                                </dd>
                            </div>
                        </dl>

                        @if ($ticket->closed_at || $ticket->closedByUser)
                            <div class="border-t border-slate-100 p-5 sm:p-6">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Ditutup Oleh
                                        </p>

                                        <p class="mt-2 text-sm font-semibold text-slate-900">
                                            {{ $ticket->closedByUser?->employee?->name
                                                ?? $ticket->closedByUser?->name
                                                ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Tanggal Ditutup
                                        </p>

                                        <p class="mt-2 text-sm font-semibold text-slate-900">
                                            {{ $ticket->closed_at?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </section>

                    @php
                        $todayTicketReport = $isCurrentTicketPic
                            ? $ticket->dailyReports
                                ->first(function ($report) use ($loggedInEmployeeId) {
                                    return (int) $report->employee_id === (int) $loggedInEmployeeId
                                        && $report->report_date?->isToday();
                                })
                            : null;
                    @endphp

                    {{-- LAPORAN PENANGANAN TIKET --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                        <x-icon name="clipboard-list" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Laporan Penanganan Tiket
                                        </h2>

                                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                            {{ $ticket->dailyReports->count() }} laporan harian terhubung
                                            dengan pekerjaan pada tiket ini.
                                        </p>
                                    </div>
                                </div>

                                @if ($isCurrentTicketPic && ! $isClosed)
                                    @if ($todayTicketReport)
                                        <a
                                            href="{{ route('pegawai.reports.edit', $todayTicketReport) }}"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100 sm:w-auto"
                                        >
                                            <x-icon name="edit-3" class="h-4 w-4" />
                                            Buka Laporan Hari Ini
                                        </a>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'operations.tickets.continuation-report',
                                                $ticket
                                            ) }}"
                                            class="w-full sm:w-auto"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                            >
                                                <x-icon name="file-text" class="h-4 w-4" />
                                                Buat Laporan Hari Ini
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- INFORMASI INTEGRASI --}}
                        @if ($isCurrentTicketPic && ! $isClosed)
                            <div class="border-b border-slate-100 bg-sky-50/60 px-5 py-4 sm:px-6">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                                        <x-icon name="user-check" class="h-4 w-4" />
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-sky-900">
                                            Anda adalah PIC aktif tiket ini
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-sky-700">
                                            @if ($todayTicketReport)
                                                Laporan tiket untuk hari ini sudah tersedia dan dapat dilengkapi
                                                melalui tombol Buka Laporan Hari Ini.
                                            @else
                                                Buat laporan lanjutan untuk mencatat pekerjaan tiket yang dilakukan
                                                hari ini. Satu tiket hanya memiliki satu laporan per PIC per tanggal.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($isClosed)
                            <div class="border-b border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 ring-1 ring-inset ring-slate-200">
                                        <x-icon name="lock" class="h-4 w-4" />
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">
                                            Pembuatan laporan baru terkunci
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-600">
                                            Tiket sudah berstatus {{ $ticket->status_label }}.
                                            Laporan yang telah terhubung tetap dapat dilihat sesuai hak akses.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($ticket->dailyReports->isEmpty())
                            {{-- EMPTY STATE --}}
                            <div class="p-5 sm:p-6">
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-inset ring-slate-200">
                                        <x-icon name="clipboard-list" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        Belum ada laporan penanganan
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        Laporan akan dibuat saat PIC pertama kali ditetapkan atau ketika
                                        PIC membuat laporan lanjutan untuk hari berikutnya.
                                    </p>

                                    @if ($isCurrentTicketPic && ! $isClosed && ! $todayTicketReport)
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'operations.tickets.continuation-report',
                                                $ticket
                                            ) }}"
                                            class="mt-5"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                            >
                                                <x-icon name="file-text" class="h-4 w-4" />
                                                Buat Laporan Hari Ini
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- DAFTAR LAPORAN --}}
                            <div class="divide-y divide-slate-100">
                                @foreach ($ticket->dailyReports as $report)
                                    @php
                                        $isOwnReport =
                                            $loggedInEmployeeId
                                            && (int) $report->employee_id === (int) $loggedInEmployeeId;

                                        $isTodayReport = $report->report_date?->isToday();

                                        $reportDateLabel = $report->report_date
                                            ? $report->report_date->format('d M Y')
                                            : '-';
                                    @endphp

                                    <article class="p-5 transition hover:bg-slate-50/60 sm:p-6">
                                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                        <x-icon name="calendar" class="h-3.5 w-3.5" />
                                                        {{ $reportDateLabel }}
                                                    </span>

                                                    @if ($isTodayReport)
                                                        <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                                            Hari Ini
                                                        </span>
                                                    @endif

                                                    @if ($isOwnReport)
                                                        <span class="inline-flex rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                                            Laporan Anda
                                                        </span>
                                                    @endif
                                                </div>

                                                <h3 class="mt-3 text-base font-semibold leading-7 text-slate-900">
                                                    {{ $report->title }}
                                                </h3>

                                                <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                                    <div class="rounded-xl bg-slate-50 p-4">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                            Pegawai
                                                        </dt>

                                                        <dd class="mt-1 text-sm font-semibold leading-6 text-slate-900">
                                                            {{ $report->employee?->name ?? '-' }}
                                                        </dd>
                                                    </div>

                                                    <div class="rounded-xl bg-slate-50 p-4">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                            Tupoksi
                                                        </dt>

                                                        <dd class="mt-1 text-sm font-semibold leading-6 text-slate-900">
                                                            {{ $report->duty?->name ?? '-' }}
                                                        </dd>
                                                    </div>

                                                    <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2 xl:col-span-1">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                            Sumber
                                                        </dt>

                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">
                                                            Tiket {{ $ticket->ticket_code }}
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </div>

                                            @if (
                                                $user->canAccessEmployeeArea()
                                                && $isOwnReport
                                            )
                                                <div class="shrink-0">
                                                    <a
                                                        href="{{ route('pegawai.reports.edit', $report) }}"
                                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100 lg:w-auto"
                                                    >
                                                        <x-icon name="edit-3" class="h-4 w-4" />
                                                        Buka Laporan
                                                    </a>
                                                </div>
                                            @else
                                                <div class="shrink-0">
                                                    <span class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-500 lg:w-auto">
                                                        <x-icon name="lock" class="h-4 w-4" />
                                                        Hanya Lihat
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @php
                        $canAddTicketNote =
                            $user->isAdmin()
                            || $user->isKanit()
                            || $user->isGkm()
                            || $isCurrentTicketPic;

                        $canChooseNoteVisibility =
                            $user->isAdmin()
                            || $user->isKanit()
                            || $user->isGkm();
                    @endphp
                                    
                    {{-- FORM CATATAN PENANGANAN --}}
                    @if ($canAddTicketNote)
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                        <x-icon name="sticky-note" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Tambah Catatan Penanganan
                                        </h2>

                                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                            Catat progres, hasil pemeriksaan, komunikasi dengan pemohon,
                                            atau informasi teknis terkait tiket.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if ($isCurrentTicketPic && ! $canChooseNoteVisibility)
                                <div class="border-b border-slate-100 bg-sky-50 px-5 py-4 sm:px-6">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                                            <x-icon name="user-check" class="h-4 w-4" />
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-sky-900">
                                                Anda menambahkan catatan sebagai PIC
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-sky-700">
                                                Catatan dari pegawai PIC otomatis disimpan sebagai catatan
                                                internal dan tidak ditampilkan pada halaman pelacakan publik.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('operations.tickets.notes.store', $ticket) }}"
                                class="space-y-5 p-5 sm:p-6"
                            >
                                @csrf

                                <div>
                                    <label
                                        for="note"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Catatan
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <textarea
                                        id="note"
                                        name="note"
                                        rows="5"
                                        required
                                        placeholder="Contoh: Pemeriksaan awal sudah dilakukan. Kendala berasal dari koneksi jaringan lokal dan sedang dilakukan penelusuran lebih lanjut."
                                        class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    >{{ old('note') }}</textarea>

                                    <div class="mt-2 flex items-start justify-between gap-3">
                                        <p class="text-xs leading-5 text-slate-500">
                                            Maksimal 3.000 karakter.
                                        </p>

                                        @error('note')
                                            <p class="text-xs font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                @if ($canChooseNoteVisibility)
                                    <div>
                                        <label
                                            for="visibility"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            Visibilitas
                                        </label>

                                        <select
                                            id="visibility"
                                            name="visibility"
                                            required
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                        >
                                            @foreach ($noteVisibilityOptions as $value => $label)
                                                <option
                                                    value="{{ $value }}"
                                                    @selected(
                                                        old('visibility', 'internal') === $value
                                                    )
                                                >
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <p class="mt-2 text-xs leading-5 text-slate-500">
                                            Catatan Internal hanya dapat dilihat oleh pengguna aplikasi.
                                            Catatan Publik dapat ditampilkan kepada pemohon melalui pelacakan tiket.
                                        </p>

                                        @error('visibility')
                                            <p class="mt-2 text-xs font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                @else
                                    <input
                                        type="hidden"
                                        name="visibility"
                                        value="internal"
                                    >
                                @endif

                                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-xs leading-5 text-slate-500">
                                        Catatan yang disimpan akan langsung masuk ke timeline tiket.
                                    </p>

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                        Simpan Catatan
                                    </button>
                                </div>
                            </form>
                        </section>
                    @else
                        <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-500 ring-1 ring-inset ring-slate-200">
                                    <x-icon name="lock" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Penambahan catatan dibatasi
                                    </h2>

                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        Catatan hanya dapat ditambahkan oleh Admin, Kanit, GKM,
                                        atau pegawai yang sedang ditunjuk sebagai PIC tiket.
                                    </p>
                                </div>
                            </div>
                        </section>
                    @endif
                    {{-- TIMELINE CATATAN --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                        <x-icon name="history" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Timeline Catatan
                                        </h2>

                                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                            Riwayat catatan penanganan yang dicatat oleh pengelola dan PIC.
                                        </p>
                                    </div>
                                </div>

                                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                    {{ $ticket->notes->count() }} catatan
                                </span>
                            </div>
                        </div>

                        @if ($ticket->notes->isEmpty())
                            <div class="p-5 sm:p-6">
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-inset ring-slate-200">
                                        <x-icon name="sticky-note" class="h-7 w-7" />
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-900">
                                        Belum ada catatan penanganan
                                    </h3>

                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                        Catatan progres, hasil pemeriksaan, dan komunikasi terkait tiket
                                        akan ditampilkan sebagai timeline di sini.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="p-5 sm:p-6">
                                <div class="relative space-y-5">
                                    <div class="absolute bottom-4 left-5 top-4 hidden w-px bg-slate-200 sm:block"></div>

                                    @foreach ($ticket->notes->sortBy('created_at') as $note)
                                        @php
                                            $isPublicNote = $note->isPublic();

                                            $noteBadgeClasses = $isPublicNote
                                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                : 'border-slate-200 bg-slate-50 text-slate-700';

                                            $noteIconClasses = $isPublicNote
                                                ? 'bg-emerald-100 text-emerald-700 ring-emerald-200'
                                                : 'bg-slate-100 text-slate-600 ring-slate-200';
                                        @endphp

                                        <article class="relative sm:pl-14">
                                            <div class="absolute left-0 top-1 hidden h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 sm:flex">
                                                <x-icon name="sticky-note" class="h-4 w-4" />
                                            </div>

                                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="flex min-w-0 items-start gap-3">
                                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 ring-inset sm:hidden {{ $noteIconClasses }}">
                                                            <x-icon name="sticky-note" class="h-4 w-4" />
                                                        </div>

                                                        <div class="min-w-0">
                                                            <p class="text-sm font-semibold leading-6 text-slate-900">
                                                                {{ $note->createdByUser?->employee?->name
                                                                    ?? $note->createdByUser?->name
                                                                    ?? 'Sistem' }}
                                                            </p>

                                                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                                                <span class="inline-flex items-center gap-1">
                                                                    <x-icon name="calendar" class="h-3.5 w-3.5" />
                                                                    {{ $note->created_at?->format('d M Y') ?? '-' }}
                                                                </span>

                                                                <span class="inline-flex items-center gap-1">
                                                                    <x-icon name="clock" class="h-3.5 w-3.5" />
                                                                    {{ $note->created_at?->format('H:i') ?? '-' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $noteBadgeClasses }}">
                                                        @if ($isPublicNote)
                                                            <x-icon name="external-link" class="h-3.5 w-3.5" />
                                                            Publik
                                                        @else
                                                            <x-icon name="lock" class="h-3.5 w-3.5" />
                                                            Internal
                                                        @endif
                                                    </span>
                                                </div>

                                                <div class="mt-4 border-t border-slate-100 pt-4">
                                                    <p class="whitespace-pre-line break-words text-sm leading-7 text-slate-700">
                                                        {{ $note->note }}
                                                    </p>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach

                                    {{-- PENUTUP LIFECYCLE TIMELINE --}}
                                    @if ($ticket->status === \App\Models\OperationalTicket::STATUS_SELESAI)
                                        <article class="relative sm:pl-14">
                                            <div class="absolute left-0 top-1 hidden h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm ring-4 ring-emerald-100 sm:flex">
                                                <x-icon name="check-circle" class="h-5 w-5" />
                                            </div>

                                            <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50 shadow-sm">
                                                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                                    <div class="flex items-start gap-3">
                                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm sm:hidden">
                                                            <x-icon name="check-circle" class="h-5 w-5" />
                                                        </div>

                                                        <div>
                                                            <p class="text-base font-semibold text-emerald-900">
                                                                Tiket Selesai
                                                            </p>

                                                            <p class="mt-1 text-sm leading-6 text-emerald-700">
                                                                Seluruh proses penanganan tiket telah diselesaikan.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="shrink-0 text-left sm:text-right">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                                                            Diselesaikan
                                                        </p>

                                                        <p class="mt-1 text-sm font-semibold text-emerald-900">
                                                            {{ $ticket->closed_at?->format('d M Y H:i') ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                @if ($ticket->closedByUser)
                                                    <div class="border-t border-emerald-200 bg-white/50 px-5 py-3">
                                                        <p class="text-xs text-emerald-700">
                                                            Ditutup oleh
                                                            <span class="font-semibold">
                                                                {{ $ticket->closedByUser?->employee?->name
                                                                    ?? $ticket->closedByUser?->name
                                                                    ?? 'Sistem' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </article>
                                    @elseif ($ticket->status === \App\Models\OperationalTicket::STATUS_DIBATALKAN)
                                        <article class="relative sm:pl-14">
                                            <div class="absolute left-0 top-1 hidden h-10 w-10 items-center justify-center rounded-xl bg-rose-600 text-white shadow-sm ring-4 ring-rose-100 sm:flex">
                                                <x-icon name="x-circle" class="h-5 w-5" />
                                            </div>

                                            <div class="overflow-hidden rounded-2xl border border-rose-200 bg-rose-50 shadow-sm">
                                                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                                    <div class="flex items-start gap-3">
                                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white shadow-sm sm:hidden">
                                                            <x-icon name="x-circle" class="h-5 w-5" />
                                                        </div>

                                                        <div>
                                                            <p class="text-base font-semibold text-rose-900">
                                                                Tiket Dibatalkan
                                                            </p>

                                                            <p class="mt-1 text-sm leading-6 text-rose-700">
                                                                Proses penanganan dihentikan dan tiket ditutup.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="shrink-0 text-left sm:text-right">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">
                                                            Status Akhir
                                                        </p>

                                                        <p class="mt-1 text-sm font-semibold text-rose-900">
                                                            Dibatalkan
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </section>
                </div>

                <div class="space-y-5">
                    {{-- SIDEBAR AKSI TIKET --}}
                    <div class="space-y-5">
                        @if ($canManageTicket && ! $isClosed)
                            {{-- UPDATE STATUS --}}
                            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-100 px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                            <x-icon name="activity" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h2 class="text-base font-semibold text-slate-900">
                                                Update Status
                                            </h2>

                                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                                Perbarui tahapan penanganan tiket.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('operations.tickets.update-status', $ticket) }}"
                                    class="space-y-5 p-5"
                                >
                                    @csrf
                                    @method('PATCH')

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
                                            required
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                        >
                                            @foreach ($statusOptions as $value => $label)
                                                <option
                                                    value="{{ $value }}"
                                                    @selected(
                                                        old('status', $ticket->status) === $value
                                                    )
                                                >
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('status')
                                            <p class="mt-2 text-xs font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    @if (! $ticket->assigned_to_employee_id)
                                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                                                    <x-icon name="alert-circle" class="h-4 w-4" />
                                                </div>

                                                <div>
                                                    <p class="text-sm font-semibold text-amber-900">
                                                        PIC belum ditentukan
                                                    </p>

                                                    <p class="mt-1 text-xs leading-5 text-amber-700">
                                                        Status Selesai akan ditolak sampai tiket mempunyai PIC.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                                                    <x-icon name="clipboard-list" class="h-4 w-4" />
                                                </div>

                                                <div>
                                                    <p class="text-sm font-semibold text-sky-900">
                                                        Integrasi laporan penyelesaian
                                                    </p>

                                                    <p class="mt-1 text-xs leading-5 text-sky-700">
                                                        Saat tiket diselesaikan, sistem memastikan PIC mempunyai
                                                        laporan pada tanggal penyelesaian. Bila belum tersedia,
                                                        laporan akan dibuat otomatis.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                        Simpan Status
                                    </button>
                                </form>
                            </section>

                            {{-- PIC DAN PRIORITAS --}}
                            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-100 px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                            <x-icon name="user-check" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h2 class="text-base font-semibold text-slate-900">
                                                PIC & Prioritas
                                            </h2>

                                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                                Atur penanggung jawab dan tingkat prioritas tiket.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('operations.tickets.update-assignment', $ticket) }}"
                                    class="space-y-5 p-5"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label
                                            for="assigned_to_employee_id"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            PIC
                                        </label>

                                        <select
                                            id="assigned_to_employee_id"
                                            name="assigned_to_employee_id"
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                        >
                                            <option value="">
                                                Belum ditentukan
                                            </option>

                                            @foreach ($employees as $employee)
                                                <option
                                                    value="{{ $employee->id }}"
                                                    @selected(
                                                        (string) old(
                                                            'assigned_to_employee_id',
                                                            $ticket->assigned_to_employee_id
                                                        ) === (string) $employee->id
                                                    )
                                                >
                                                    {{ $employee->name }}
                                                    @if ($employee->unit?->name)
                                                        — {{ $employee->unit->name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('assigned_to_employee_id')
                                            <p class="mt-2 text-xs font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                        <p class="mt-2 text-xs leading-5 text-slate-500">
                                            Pergantian PIC tidak menghapus laporan dan histori penugasan sebelumnya.
                                        </p>
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
                                            required
                                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                        >
                                            @foreach ($priorityOptions as $value => $label)
                                                <option
                                                    value="{{ $value }}"
                                                    @selected(
                                                        old('priority', $ticket->priority) === $value
                                                    )
                                                >
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('priority')
                                            <p class="mt-2 text-xs font-medium text-rose-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-slate-600 ring-1 ring-inset ring-slate-200">
                                                <x-icon name="info" class="h-4 w-4" />
                                            </div>

                                            <p class="text-xs leading-5 text-slate-600">
                                                Saat PIC baru ditetapkan, sistem akan mencoba membuat laporan
                                                awal atau laporan lanjutan sesuai mapping Tupoksi.
                                            </p>
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                    >
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                        Simpan PIC & Prioritas
                                    </button>
                                </form>
                            </section>
                        @else
                            {{-- KONDISI READ-ONLY --}}
                            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 ring-1 ring-inset ring-slate-200">
                                        <x-icon name="lock" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Tiket Read-only
                                        </h2>

                                        <p class="mt-2 text-sm leading-6 text-slate-600">
                                            @if ($isClosed)
                                                Tiket berstatus {{ $ticket->status_label }} sehingga
                                                status, PIC, dan prioritas tidak dapat diubah.
                                            @elseif (! $canManageTicket)
                                                Akun Anda tidak mempunyai akses pengelola untuk mengubah tiket ini.
                                            @else
                                                Tiket hanya dapat dilihat.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </section>
                        @endif

                        {{-- RINGKASAN LIFECYCLE --}}
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                        <x-icon name="history" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-slate-900">
                                            Ringkasan Lifecycle
                                        </h2>

                                        <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                            Kondisi terkini tiket dan integrasi arsip.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <dl class="divide-y divide-slate-100">
                                <div class="flex items-start justify-between gap-4 px-5 py-4">
                                    <dt class="text-sm text-slate-500">
                                        Status
                                    </dt>

                                    <dd class="text-right text-sm font-semibold text-slate-900">
                                        {{ $ticket->status_label }}
                                    </dd>
                                </div>

                                <div class="flex items-start justify-between gap-4 px-5 py-4">
                                    <dt class="text-sm text-slate-500">
                                        PIC Aktif
                                    </dt>

                                    <dd class="text-right text-sm font-semibold text-slate-900">
                                        {{ $ticket->assignedToEmployee?->name ?? 'Belum ditentukan' }}
                                    </dd>
                                </div>

                                <div class="flex items-start justify-between gap-4 px-5 py-4">
                                    <dt class="text-sm text-slate-500">
                                        Laporan Terkait
                                    </dt>

                                    <dd class="text-right text-sm font-semibold text-slate-900">
                                        {{ $ticket->dailyReports->count() }} laporan
                                    </dd>
                                </div>

                                <div class="flex items-start justify-between gap-4 px-5 py-4">
                                    <dt class="text-sm text-slate-500">
                                        Catatan
                                    </dt>

                                    <dd class="text-right text-sm font-semibold text-slate-900">
                                        {{ $ticket->notes->count() }} catatan
                                    </dd>
                                </div>

                                @if ($ticket->closed_at)
                                    <div class="flex items-start justify-between gap-4 px-5 py-4">
                                        <dt class="text-sm text-slate-500">
                                            Tanggal Ditutup
                                        </dt>

                                        <dd class="text-right text-sm font-semibold text-slate-900">
                                            {{ $ticket->closed_at->format('d M Y H:i') }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </section>

                        {{-- STATUS ARSIP --}}
                        @if (! $ticket->dailyReports->isEmpty())
                            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                        <x-icon name="lock" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-emerald-900">
                                            Tiket Menjadi Arsip
                                        </h2>

                                        <p class="mt-2 text-sm leading-6 text-emerald-700">
                                            Tiket sudah mempunyai
                                            {{ $ticket->dailyReports->count() }}
                                            laporan harian terkait. Tiket tidak dapat dihapus agar laporan
                                            dan histori penugasan tetap terjaga.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        @endif

                        {{-- HAPUS TIKET --}}
                        @if (
                            $canManageTicket
                            && in_array(
                                $ticket->status,
                                [
                                    \App\Models\OperationalTicket::STATUS_BARU,
                                    \App\Models\OperationalTicket::STATUS_DIBATALKAN,
                                ],
                                true
                            )
                            && $ticket->dailyReports->isEmpty()
                        )
                            <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-700 ring-1 ring-inset ring-rose-200">
                                        <x-icon name="trash-2" class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="text-base font-semibold text-rose-900">
                                            Hapus Tiket
                                        </h2>

                                        <p class="mt-2 text-sm leading-6 text-rose-700">
                                            Tiket dapat dihapus karena berstatus
                                            {{ $ticket->status_label }}
                                            dan belum mempunyai laporan harian.
                                        </p>
                                    </div>
                                </div>

                                <form
                                    x-data
                                    method="POST"
                                    action="{{ route('operations.tickets.destroy', $ticket) }}"
                                    class="mt-5"
                                    x-on:submit.prevent="$dispatch('open-confirm-modal', {
                                        title: 'Hapus Tiket Operasional?',
                                        message: 'Tiket {{ $ticket->ticket_code }} akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
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
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700"
                                    >
                                        <x-icon name="trash-2" class="h-4 w-4" />
                                        Hapus Tiket
                                    </button>
                                </form>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>