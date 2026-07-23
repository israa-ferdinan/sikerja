<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tiket Berhasil Dibuat</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen">
        <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col justify-center space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            {{-- HERO SUKSES --}}
            <section class="overflow-hidden rounded-3xl border border-emerald-800 bg-gradient-to-br from-emerald-950 via-emerald-900 to-cyan-950 shadow-xl shadow-emerald-900/10">
                <div class="flex min-h-[250px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-12">
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-emerald-100">
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Tiket Berhasil Dikirim
                        </div>

                        <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                            Permintaan Anda sudah tercatat
                        </h1>

                        <p class="mt-4 max-w-3xl text-sm leading-7 text-emerald-100/80 sm:text-base">
                            Simpan kode tiket dan link pelacakan berikut. Informasi tersebut
                            digunakan untuk memantau status penanganan tiket oleh petugas SIM/TI.
                        </p>
                    </div>

                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white/10 text-emerald-100 ring-1 ring-inset ring-white/15 lg:h-24 lg:w-24">
                        <x-icon name="ticket-check" class="h-10 w-10 lg:h-12 lg:w-12" />
                    </div>
                </div>
            </section>

            {{-- KODE TIKET --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon name="ticket" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Kode Tiket
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Gunakan kode ini untuk pengecekan status tiket.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 text-center sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">
                            Kode Tiket Anda
                        </p>

                        <input
                            id="ticket_code_copy"
                            type="text"
                            value="{{ $ticket->ticket_code }}"
                            readonly
                            class="mt-3 w-full border-0 bg-transparent p-0 text-center font-mono text-2xl font-bold tracking-wide text-sky-950 focus:ring-0 sm:text-3xl"
                        >

                        <button
                            id="copy_ticket_code_button"
                            type="button"
                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <span id="copy_ticket_code_icon">
                                <x-icon name="clipboard-list" class="h-4 w-4" />
                            </span>

                            <span
                                id="copy_ticket_code_success_icon"
                                class="hidden"
                            >
                                <x-icon name="check-circle" class="h-4 w-4" />
                            </span>

                            <span id="copy_ticket_code_text">
                                Salin Kode
                            </span>
                        </button>
                    </div>
                </div>
            </section>

            {{-- LINK TRACKING --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <x-icon name="external-link" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Link Pelacakan
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Simpan link berikut untuk membuka detail status secara langsung.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 p-5 sm:p-6">
                    <div>
                        <label
                            for="tracking_url"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Link Tracking
                        </label>

                        <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                            <input
                                id="tracking_url"
                                type="text"
                                value="{{ $trackingUrl }}"
                                readonly
                                onclick="this.select()"
                                class="block min-w-0 flex-1 rounded-xl border-slate-300 bg-slate-50 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            >

                            <button
                                id="copy_tracking_url_button"
                                type="button"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-100"
                            >
                                <span id="copy_tracking_url_icon">
                                    <x-icon name="clipboard-list" class="h-4 w-4" />
                                </span>

                                <span
                                    id="copy_tracking_url_success_icon"
                                    class="hidden"
                                >
                                    <x-icon name="check-circle" class="h-4 w-4" />
                                </span>

                                <span id="copy_tracking_url_text">
                                    Salin Link
                                </span>
                            </button>
                        </div>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Jangan bagikan link ini kepada pihak yang tidak berkepentingan.
                        </p>
                    </div>

                    <a
                        href="{{ $trackingUrl }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 sm:w-auto"
                    >
                        <x-icon name="external-link" class="h-4 w-4" />
                        Buka Status Tiket
                    </a>
                </div>
            </section>

            {{-- RINGKASAN TIKET --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <x-icon name="clipboard-list" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Ringkasan Tiket
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Pastikan informasi tiket sudah sesuai.
                            </p>
                        </div>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2">
                    <div class="border-b border-slate-100 p-5 sm:border-r">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama Pemohon
                        </dt>

                        <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                            {{ $ticket->requester_name }}
                        </dd>
                    </div>

                    <div class="border-b border-slate-100 p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Kontak
                        </dt>

                        <dd class="mt-2 break-words text-sm font-semibold leading-6 text-slate-900">
                            {{ $ticket->requester_contact ?: '-' }}
                        </dd>
                    </div>

                    <div class="border-b border-slate-100 p-5 sm:border-b-0 sm:border-r">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Jenis Permintaan
                        </dt>

                        <dd class="mt-2">
                            <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                {{ $ticket->category_label }}
                            </span>
                        </dd>
                    </div>

                    <div class="p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Status
                        </dt>

                        <dd class="mt-2">
                            <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                {{ $ticket->status_label }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- PERINGATAN --}}
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                        <x-icon name="lock" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-amber-900">
                            Jaga kerahasiaan link pelacakan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Link tracking digunakan untuk melihat informasi status tiket.
                            Bagikan hanya kepada pihak yang berkepentingan.
                        </p>
                    </div>
                </div>
            </section>

            {{-- ACTION --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a
                        href="{{ route('public.tickets.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <x-icon name="ticket" class="h-4 w-4" />
                        Buat Tiket Lagi
                    </a>

                    <a
                        href="{{ $trackingUrl }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        Lihat Status Tiket
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </div>
            </section>

            <footer class="pb-2 text-center text-xs text-slate-500">
                Unit SIM/TI — Sistem Informasi Tiket Operasional
            </footer>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            async function copyText(value, inputElement) {
                if (
                    navigator.clipboard
                    && window.isSecureContext
                ) {
                    await navigator.clipboard.writeText(value);
                    return;
                }

                inputElement.focus();
                inputElement.select();
                inputElement.setSelectionRange(0, inputElement.value.length);

                document.execCommand('copy');
            }

            function showCopiedState(options) {
                options.defaultIcon.classList.add('hidden');
                options.successIcon.classList.remove('hidden');
                options.text.textContent = options.successText;

                window.setTimeout(function () {
                    options.defaultIcon.classList.remove('hidden');
                    options.successIcon.classList.add('hidden');
                    options.text.textContent = options.defaultText;
                }, 2000);
            }

            const codeInput = document.getElementById('ticket_code_copy');
            const codeButton = document.getElementById('copy_ticket_code_button');
            const codeIcon = document.getElementById('copy_ticket_code_icon');
            const codeSuccessIcon = document.getElementById(
                'copy_ticket_code_success_icon'
            );
            const codeText = document.getElementById('copy_ticket_code_text');

            if (
                codeInput
                && codeButton
                && codeIcon
                && codeSuccessIcon
                && codeText
            ) {
                codeButton.addEventListener('click', async function () {
                    try {
                        await copyText(codeInput.value, codeInput);

                        showCopiedState({
                            defaultIcon: codeIcon,
                            successIcon: codeSuccessIcon,
                            text: codeText,
                            defaultText: 'Salin Kode',
                            successText: 'Kode Tersalin',
                        });
                    } catch (error) {
                        console.error('Gagal menyalin kode tiket:', error);

                        codeInput.focus();
                        codeInput.select();
                    }
                });
            }

            const trackingInput = document.getElementById('tracking_url');
            const trackingButton = document.getElementById(
                'copy_tracking_url_button'
            );
            const trackingIcon = document.getElementById(
                'copy_tracking_url_icon'
            );
            const trackingSuccessIcon = document.getElementById(
                'copy_tracking_url_success_icon'
            );
            const trackingText = document.getElementById(
                'copy_tracking_url_text'
            );

            if (
                trackingInput
                && trackingButton
                && trackingIcon
                && trackingSuccessIcon
                && trackingText
            ) {
                trackingButton.addEventListener('click', async function () {
                    try {
                        await copyText(trackingInput.value, trackingInput);

                        showCopiedState({
                            defaultIcon: trackingIcon,
                            successIcon: trackingSuccessIcon,
                            text: trackingText,
                            defaultText: 'Salin Link',
                            successText: 'Link Tersalin',
                        });
                    } catch (error) {
                        console.error('Gagal menyalin link tracking:', error);

                        trackingInput.focus();
                        trackingInput.select();
                    }
                });
            }
        });
    </script>
</body>
</html>