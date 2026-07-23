<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Status Tiket {{ $ticket->ticket_code }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    @php
        $statusClass = match ($ticket->status) {
            \App\Models\OperationalTicket::STATUS_BARU
                => 'border-sky-200 bg-sky-50 text-sky-700',

            \App\Models\OperationalTicket::STATUS_DIPROSES
                => 'border-amber-200 bg-amber-50 text-amber-700',

            \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON
                => 'border-violet-200 bg-violet-50 text-violet-700',

            \App\Models\OperationalTicket::STATUS_SELESAI
                => 'border-emerald-200 bg-emerald-50 text-emerald-700',

            \App\Models\OperationalTicket::STATUS_DIBATALKAN
                => 'border-rose-200 bg-rose-50 text-rose-700',

            default
                => 'border-slate-200 bg-slate-50 text-slate-700',
        };

        $statusIcon = match ($ticket->status) {
            \App\Models\OperationalTicket::STATUS_BARU
                => 'circle-dot',

            \App\Models\OperationalTicket::STATUS_DIPROSES
                => 'activity',

            \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON
                => 'clock',

            \App\Models\OperationalTicket::STATUS_SELESAI
                => 'check-circle',

            \App\Models\OperationalTicket::STATUS_DIBATALKAN
                => 'x-circle',

            default
                => 'info',
        };

        $statusMessage = match ($ticket->status) {
            \App\Models\OperationalTicket::STATUS_BARU
                => 'Tiket sudah diterima dan sedang menunggu penanganan dari petugas SIM/TI.',

            \App\Models\OperationalTicket::STATUS_DIPROSES
                => 'Tiket sedang ditangani oleh petugas SIM/TI.',

            \App\Models\OperationalTicket::STATUS_MENUNGGU_PEMOHON
                => 'Petugas membutuhkan informasi atau konfirmasi tambahan dari pemohon.',

            \App\Models\OperationalTicket::STATUS_SELESAI
                => 'Tiket telah selesai ditangani oleh petugas SIM/TI.',

            \App\Models\OperationalTicket::STATUS_DIBATALKAN
                => 'Tiket telah dibatalkan atau tidak dapat dilanjutkan.',

            default
                => 'Status tiket sedang diperbarui.',
        };
    @endphp

    <main class="min-h-screen">
        <div class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
            {{-- HERO --}}
            <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-xl shadow-slate-900/10">
                <div class="flex min-h-[260px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-12">
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="search" class="h-4 w-4" />
                            Tracking Tiket Publik
                        </div>

                        <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                            Status Tiket SIM/TI
                        </h1>

                        <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                            Pantau status penanganan dan informasi publik terbaru dari petugas SIM/TI.
                        </p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 font-mono text-sm font-bold tracking-wide text-white ring-1 ring-inset ring-white/15">
                                <x-icon name="ticket" class="h-4 w-4" />
                                {{ $ticket->ticket_code }}
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusClass }}">
                                <x-icon name="{{ $statusIcon }}" class="h-4 w-4" />
                                {{ $ticket->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="grid shrink-0 grid-cols-1 gap-2 sm:grid-cols-2 lg:w-[360px] lg:grid-cols-1 lg:pl-8">
                        <a
                            href="{{ route('public.tickets.track-form') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                        >
                            <x-icon name="search" class="h-4 w-4" />
                            Cek Tiket Lain
                        </a>

                        <a
                            href="{{ route('public.tickets.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15"
                        >
                            <x-icon name="ticket" class="h-4 w-4" />
                            Buat Tiket Baru
                        </a>
                    </div>
                </div>
            </section>

            {{-- STATUS SUMMARY --}}
            <section class="rounded-2xl border p-5 shadow-sm {{ $statusClass }}">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/80 ring-1 ring-inset ring-current/10">
                        <x-icon name="{{ $statusIcon }}" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold">
                            {{ $ticket->status_label }}
                        </h2>

                        <p class="mt-1 text-sm leading-6">
                            {{ $statusMessage }}
                        </p>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-6">
                    {{-- INFORMASI PERMINTAAN --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <x-icon name="clipboard-list" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Informasi Permintaan
                                    </h2>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        Ringkasan keluhan atau kebutuhan yang diajukan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Judul
                                </p>

                                <h3 class="mt-2 text-lg font-semibold leading-7 text-slate-900">
                                    {{ $ticket->title }}
                                </h3>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Deskripsi
                                </p>

                                @if ($ticket->description)
                                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">
                                        {{ $ticket->description }}
                                    </p>
                                @else
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        Tidak ada deskripsi tambahan.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </section>

                    {{-- TIMELINE CATATAN PUBLIK --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <x-icon name="history" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Timeline Penanganan
                                    </h2>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        Update yang dapat dilihat oleh pemohon.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="relative space-y-5">
                                <div class="absolute bottom-4 left-5 top-4 hidden w-px bg-slate-200 sm:block"></div>

                                @forelse ($ticket->notes->sortBy('created_at') as $note)
                                    <article class="relative sm:pl-14">
                                        <div class="absolute left-0 top-1 hidden h-10 w-10 items-center justify-center rounded-xl bg-white text-violet-700 shadow-sm ring-1 ring-inset ring-slate-200 sm:flex">
                                            <x-icon name="message-square" class="h-5 w-5" />
                                        </div>

                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-5">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-violet-700 ring-1 ring-inset ring-slate-200 sm:hidden">
                                                        <x-icon name="message-square" class="h-5 w-5" />
                                                    </div>

                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">
                                                            Update dari Petugas
                                                        </p>

                                                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">
                                                            {{ $note->note }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <time class="shrink-0 text-xs font-medium text-slate-500">
                                                    {{ $note->created_at?->format('d M Y H:i') }}
                                                </time>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    @if (
                                        ! in_array(
                                            $ticket->status,
                                            [
                                                \App\Models\OperationalTicket::STATUS_SELESAI,
                                                \App\Models\OperationalTicket::STATUS_DIBATALKAN,
                                            ],
                                            true
                                        )
                                    )
                                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-500 ring-1 ring-inset ring-slate-200">
                                                <x-icon name="message-square" class="h-6 w-6" />
                                            </div>

                                            <h3 class="mt-4 text-sm font-semibold text-slate-900">
                                                Belum ada update publik
                                            </h3>

                                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                                Update dari petugas akan muncul pada halaman ini.
                                            </p>
                                        </div>
                                    @endif
                                @endforelse

                                {{-- PENUTUP TIMELINE --}}
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
                    </section>
                </div>

                {{-- SIDEBAR --}}
                <aside class="space-y-6">
                    {{-- DETAIL TIKET --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                    <x-icon name="info" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Detail Tiket
                                    </h2>

                                    <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                        Informasi pengajuan tiket.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <dl class="divide-y divide-slate-100">
                            <div class="px-5 py-4">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Nama Pemohon
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->requester_name }}
                                </dd>
                            </div>

                            <div class="px-5 py-4">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Asal Unit/Instansi
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->requester_unit ?: '-' }}
                                </dd>
                            </div>

                            <div class="px-5 py-4">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Jenis Permintaan
                                </dt>

                                <dd class="mt-2">
                                    <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $ticket->category_label }}
                                    </span>
                                </dd>
                            </div>

                            <div class="px-5 py-4">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tanggal Dibuat
                                </dt>

                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                    {{ $ticket->created_at?->format('d M Y H:i') }}
                                </dd>
                            </div>

                            @if ($ticket->closed_at)
                                <div class="px-5 py-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Tanggal Ditutup
                                    </dt>

                                    <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                        {{ $ticket->closed_at?->format('d M Y H:i') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </section>

                    {{-- INFORMASI KONTAK --}}
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                                <x-icon name="phone" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-sm font-semibold text-amber-900">
                                    Konfirmasi dari petugas
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-amber-700">
                                    Jika diperlukan, petugas akan menghubungi kontak yang
                                    dicantumkan saat tiket dibuat.
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- ACTION --}}
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="space-y-3">
                            <a
                                href="{{ route('public.tickets.track-form') }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                <x-icon name="search" class="h-4 w-4" />
                                Cek Tiket Lain
                            </a>

                            <a
                                href="{{ route('public.tickets.create') }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="ticket" class="h-4 w-4" />
                                Buat Tiket Baru
                            </a>
                        </div>
                    </section>
                </aside>
            </div>

            <footer class="pb-2 text-center text-xs text-slate-500">
                Unit SIM/TI — Sistem Informasi Tiket Operasional
            </footer>
        </div>
    </main>
</body>
</html>