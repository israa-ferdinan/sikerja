<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cek Status Tiket SIM/TI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen">
        <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col justify-center space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            {{-- HERO --}}
            <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-xl shadow-slate-900/10">
                <div class="flex min-h-[250px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-12">
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="search" class="h-4 w-4" />
                            Tracking Tiket Publik
                        </div>

                        <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                            Cek Status Tiket SIM/TI
                        </h1>

                        <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                            Masukkan kode tiket yang diterima setelah pengajuan untuk melihat
                            status dan update publik dari petugas SIM/TI.
                        </p>
                    </div>

                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white/10 text-cyan-100 ring-1 ring-inset ring-white/15 lg:h-24 lg:w-24">
                        <x-icon name="ticket-check" class="h-10 w-10 lg:h-12 lg:w-12" />
                    </div>
                </div>
            </section>

            {{-- ERROR SUMMARY --}}
            @if ($errors->any())
                <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                            <x-icon name="alert-circle" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-rose-900">
                                Tiket tidak ditemukan
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-rose-700">
                                Periksa kembali kode tiket yang dimasukkan.
                            </p>
                        </div>
                    </div>
                </section>
            @endif

            {{-- FORM TRACKING --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon name="search" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Masukkan Kode Tiket
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Kode tiket tersedia pada halaman sukses setelah pengajuan.
                            </p>
                        </div>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('public.tickets.track') }}"
                    class="space-y-6 p-5 sm:p-6"
                >
                    @csrf

                    <div>
                        <label
                            for="ticket_code"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Kode Tiket
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="ticket_code"
                            name="ticket_code"
                            type="text"
                            value="{{ old('ticket_code') }}"
                            placeholder="Contoh: OPS-20260709-AB12"
                            required
                            autofocus
                            autocomplete="off"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-3 font-mono text-sm font-semibold uppercase tracking-wide text-slate-900 shadow-sm placeholder:font-sans placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('ticket_code')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                                <x-icon name="info" class="h-4 w-4" />
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-sky-900">
                                    Format kode tiket
                                </p>

                                <p class="mt-1 text-sm leading-6 text-sky-700">
                                    Masukkan kode lengkap seperti yang tampil pada halaman
                                    tiket berhasil dibuat. Huruf kecil akan otomatis diubah
                                    menjadi huruf besar saat diketik.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <a
                            href="{{ route('public.tickets.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <x-icon name="ticket" class="h-4 w-4" />
                            Buat Tiket Baru
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="search" class="h-4 w-4" />
                            Cek Status
                        </button>
                    </div>
                </form>
            </section>

            {{-- INFORMASI KEAMANAN --}}
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                        <x-icon name="lock" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-amber-900">
                            Jaga kerahasiaan kode tiket
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Kode tiket digunakan untuk membuka informasi status publik.
                            Bagikan hanya kepada pihak yang berkepentingan.
                        </p>
                    </div>
                </div>
            </section>

            <footer class="pb-2 text-center text-xs text-slate-500">
                Unit SIM/TI — Sistem Informasi Tiket Operasional
            </footer>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ticketCodeInput = document.getElementById('ticket_code');

            if (!ticketCodeInput) {
                return;
            }

            ticketCodeInput.addEventListener('input', function () {
                const cursorPosition = this.selectionStart;

                this.value = this.value.toUpperCase().replace(/\s+/g, '');

                if (cursorPosition !== null) {
                    this.setSelectionRange(cursorPosition, cursorPosition);
                }
            });
        });
    </script>
</body>
</html>