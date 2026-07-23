<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>KIOSK Monitoring Tiket SIM/TI</title>

    <meta http-equiv="refresh" content="60">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    @php
        $statusClasses = [
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
        ];

        $statusIcons = [
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
        ];
    @endphp

    <main class="min-h-screen">
        <div class="w-full space-y-6 px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-8 xl:px-10">
            {{-- HERO --}}
            <header class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-xl shadow-slate-900/10">
                <div class="flex flex-col gap-8 px-6 py-7 sm:px-8 sm:py-8 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-9">
                    <div class="flex min-w-0 items-start gap-4 sm:items-center">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-cyan-100 ring-1 ring-inset ring-white/15 sm:h-16 sm:w-16">
                            <x-icon name="ticket-check" class="h-8 w-8" />
                        </div>

                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                                <x-icon name="monitor" class="h-4 w-4" />
                                KIOSK Operasional SIM/TI
                            </div>

                            <h1 class="mt-3 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                                Monitoring Tiket SIM/TI
                            </h1>

                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                                Monitoring tiket aktif dan tiket yang selesai pada hari ini.
                            </p>
                        </div>
                    </div>

                    <div class="grid shrink-0 gap-3 sm:grid-cols-2 lg:w-auto lg:grid-cols-1">
                        <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-slate-200">
                            <div class="flex items-center gap-2">
                                <x-icon name="rotate-ccw" class="h-4 w-4 text-cyan-200" />

                                <span class="font-semibold text-white">
                                    Auto-refresh 60 detik
                                </span>
                            </div>

                            <p class="mt-1.5 text-xs leading-5 text-slate-300">
                                Halaman akan memperbarui data secara otomatis.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-slate-200">
                            <div class="flex items-center gap-2">
                                <x-icon name="clock" class="h-4 w-4 text-cyan-200" />

                                <span class="font-semibold text-white">
                                    Terakhir diperbarui
                                </span>
                            </div>

                            <p class="mt-1.5 font-mono text-xs text-slate-300">
                                {{ $generatedAt->format('d M Y H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            {{-- SUMMARY --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-sky-700">
                                Tiket Baru
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-slate-950">
                                {{ $summary['baru'] }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Menunggu penanganan petugas.
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200">
                            <x-icon name="circle-dot" class="h-5 w-5" />
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-amber-700">
                                Diproses
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-slate-950">
                                {{ $summary['diproses'] }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Sedang ditangani petugas.
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">
                            <x-icon name="activity" class="h-5 w-5" />
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-violet-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-violet-700">
                                Menunggu Pemohon
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-slate-950">
                                {{ $summary['menunggu_pemohon'] }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Membutuhkan konfirmasi tambahan.
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">
                            <x-icon name="clock" class="h-5 w-5" />
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700">
                                Selesai Hari Ini
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-slate-950">
                                {{ $summary['selesai_hari_ini'] }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Ditutup pada tanggal hari ini.
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            <x-icon name="check-circle" class="h-5 w-5" />
                        </div>
                    </div>
                </article>
            </section>

            {{-- DAFTAR TIKET --}}
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <x-icon name="clipboard-list" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">
                                Daftar Tiket Monitoring
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Tiket aktif dan tiket selesai hari ini.
                            </p>
                        </div>
                    </div>

                    <div class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600">
                        <x-icon name="info" class="h-3.5 w-3.5" />
                        Maksimal 30 tiket
                    </div>
                </div>

                {{-- DESKTOP TABLE --}}
                <div class="hidden overflow-x-auto lg:block">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Kode
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Permintaan
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Unit/Bagian
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Jenis
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Pembaruan
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($tickets as $ticket)
                                @php
                                    $ticketStatusClass =
                                        $statusClasses[$ticket->status]
                                        ?? 'border-slate-200 bg-slate-50 text-slate-700';

                                    $ticketStatusIcon =
                                        $statusIcons[$ticket->status]
                                        ?? 'info';
                                @endphp

                                <tr class="transition hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-5 py-4 align-top">
                                        <p class="font-mono text-sm font-bold tracking-wide text-slate-900">
                                            {{ $ticket->ticket_code }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Dibuat {{ $ticket->created_at?->format('d M H:i') }}
                                        </p>
                                    </td>

                                    <td class="max-w-xl px-5 py-4 align-top">
                                        <p class="line-clamp-2 text-sm font-semibold leading-6 text-slate-900">
                                            {{ $ticket->title }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <p class="max-w-[220px] text-sm font-medium leading-6 text-slate-700">
                                            {{ $ticket->requester_unit ?: '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{ $ticket->category_label }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $ticketStatusClass }}">
                                            <x-icon
                                                name="{{ $ticketStatusIcon }}"
                                                class="h-3.5 w-3.5"
                                            />

                                            {{ $ticket->status_label }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ $ticket->updated_at?->format('d M H:i') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $ticket->updated_at?->diffForHumans() }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                            <x-icon name="ticket-check" class="h-7 w-7" />
                                        </div>

                                        <h3 class="mt-4 text-lg font-semibold text-slate-900">
                                            Tidak ada tiket untuk ditampilkan
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            Tiket baru, sedang diproses, atau selesai hari ini
                                            akan muncul pada halaman ini.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE/TABLET CARDS --}}
                <div class="divide-y divide-slate-100 lg:hidden">
                    @forelse ($tickets as $ticket)
                        @php
                            $ticketStatusClass =
                                $statusClasses[$ticket->status]
                                ?? 'border-slate-200 bg-slate-50 text-slate-700';

                            $ticketStatusIcon =
                                $statusIcons[$ticket->status]
                                ?? 'info';
                        @endphp

                        <article class="space-y-4 p-5 sm:p-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="font-mono text-sm font-bold tracking-wide text-slate-900">
                                        {{ $ticket->ticket_code }}
                                    </p>

                                    <h3 class="mt-2 text-base font-semibold leading-6 text-slate-900">
                                        {{ $ticket->title }}
                                    </h3>
                                </div>

                                <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $ticketStatusClass }}">
                                    <x-icon
                                        name="{{ $ticketStatusIcon }}"
                                        class="h-3.5 w-3.5"
                                    />

                                    {{ $ticket->status_label }}
                                </span>
                            </div>

                            <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Unit/Bagian
                                    </dt>

                                    <dd class="mt-1.5 font-medium leading-6 text-slate-800">
                                        {{ $ticket->requester_unit ?: '-' }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Jenis Permintaan
                                    </dt>

                                    <dd class="mt-1.5 font-medium leading-6 text-slate-800">
                                        {{ $ticket->category_label }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Dibuat
                                    </dt>

                                    <dd class="mt-1.5 font-medium leading-6 text-slate-800">
                                        {{ $ticket->created_at?->format('d M Y H:i') }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Pembaruan
                                    </dt>

                                    <dd class="mt-1.5 font-medium leading-6 text-slate-800">
                                        {{ $ticket->updated_at?->format('d M Y H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </article>
                    @empty
                        <div class="px-5 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                <x-icon name="ticket-check" class="h-7 w-7" />
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-slate-900">
                                Tidak ada tiket untuk ditampilkan
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Tiket aktif atau selesai hari ini akan muncul di sini.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- PRIVACY INFO --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <x-icon name="lock" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi publik terbatas
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            Tampilan KIOSK hanya menampilkan kode, judul permintaan,
                            unit atau bagian, jenis layanan, status, dan waktu pembaruan.
                            Kontak pemohon, deskripsi lengkap, PIC, dan catatan internal
                            tidak ditampilkan.
                        </p>
                    </div>
                </div>
            </section>

            <footer class="pb-2 text-center text-xs text-slate-500">
                Unit SIM/TI — KIOSK Monitoring Tiket Operasional
            </footer>
        </div>
    </main>
</body>
</html>