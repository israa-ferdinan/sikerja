<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ajukan Tiket SIM/TI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen">
        <div class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
            {{-- HERO --}}
            <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-xl shadow-slate-900/10">
                <div class="flex min-h-[260px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-12">
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                            <x-icon name="ticket-check" class="h-4 w-4" />
                            Layanan Publik SIM/TI
                        </div>

                        <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                            Ajukan Tiket SIM/TI
                        </h1>

                        <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                            Sampaikan gangguan, permintaan bantuan, kebutuhan aplikasi,
                            jaringan, perangkat, atau dukungan kegiatan kepada petugas SIM/TI.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                                Form Sederhana
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                <x-icon name="search" class="h-3.5 w-3.5" />
                                Bisa Dilacak
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                                <x-icon name="activity" class="h-3.5 w-3.5" />
                                Dipantau Petugas
                            </span>
                        </div>
                    </div>

                    <div class="grid shrink-0 grid-cols-1 gap-2 sm:grid-cols-2 lg:w-[360px] lg:grid-cols-1 lg:pl-8">
                        <a
                            href="{{ route('public.tickets.track-form') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-400"
                        >
                            <x-icon name="search" class="h-4 w-4" />
                            Cek Status Tiket
                        </a>
                    </div>
                </div>
            </section>

            {{-- INFORMASI --}}
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                        <x-icon name="info" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-sky-900">
                            Isi data dengan benar
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-sky-700">
                            Setelah tiket dikirim, sistem akan memberikan kode dan link
                            pelacakan. Simpan informasi tersebut untuk memantau progres tiket.
                        </p>
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
                                Tiket belum dapat dikirim
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-rose-700">
                                Periksa kembali field yang ditandai dan lengkapi informasi wajib.
                            </p>
                        </div>
                    </div>
                </section>
            @endif

            <form
                method="POST"
                action="{{ route('public.tickets.store') }}"
                class="space-y-6"
            >
                @csrf

                {{-- JENIS PERMINTAAN --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                <x-icon name="clipboard-list" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Jenis Permintaan
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Pilih layanan yang paling sesuai dengan kebutuhan Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <label
                            for="category"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Jenis Permintaan
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            id="category"
                            name="category"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Pilih jenis permintaan
                            </option>

                            @foreach ($categoryOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('category') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('category')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </section>

                {{-- FORM UMUM --}}
                <section
                    id="general-fields"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <x-icon name="ticket" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Informasi Permintaan
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Lengkapi identitas dan jelaskan kebutuhan secara singkat.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label
                                    for="requester_name_general"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Nama Pemohon
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    id="requester_name_general"
                                    name="requester_name"
                                    type="text"
                                    value="{{ old('requester_name') }}"
                                    placeholder="Masukkan nama pemohon"
                                    autocomplete="name"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                >

                                @error('requester_name')
                                    <p class="mt-2 text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="requester_contact_general"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Kontak/WhatsApp
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    id="requester_contact_general"
                                    name="requester_contact"
                                    type="text"
                                    value="{{ old('requester_contact') }}"
                                    placeholder="Contoh: 08123456789"
                                    autocomplete="tel"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Petugas dapat menghubungi nomor ini bila membutuhkan klarifikasi.
                                </p>

                                @error('requester_contact')
                                    <p class="mt-2 text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                for="requester_unit_general"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Asal Unit/Instansi
                            </label>

                            <input
                                id="requester_unit_general"
                                name="requester_unit"
                                type="text"
                                value="{{ old('requester_unit') }}"
                                placeholder="Contoh: Unit Keuangan, Prodi, Bagian Umum"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >

                            @error('requester_unit')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="title_general"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Keluhan/Permintaan Singkat
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                id="title_general"
                                name="title"
                                type="text"
                                value="{{ old('title') }}"
                                maxlength="255"
                                placeholder="Contoh: Tidak bisa login aplikasi"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >

                            @error('title')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="description_general"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Deskripsi
                            </label>

                            <textarea
                                id="description_general"
                                name="description"
                                rows="6"
                                maxlength="2000"
                                placeholder="Jelaskan nama aplikasi, ruangan, perangkat, waktu kejadian, atau informasi pendukung lainnya."
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >{{ old('description') }}</textarea>

                            <div class="mt-2 flex flex-col gap-1 sm:flex-row sm:justify-between">
                                <p class="text-xs leading-5 text-slate-500">
                                    Maksimal 2.000 karakter.
                                </p>

                                @error('description')
                                    <p class="text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                {{-- FORM KHUSUS ZOOM --}}
                <section
                    id="zoom-fields"
                    class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                                <x-icon name="monitor" class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-slate-900">
                                    Permintaan Dukungan Zoom
                                </h2>

                                <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                    Lengkapi informasi kegiatan online yang membutuhkan dukungan SIM/TI.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-cyan-700 ring-1 ring-inset ring-cyan-200">
                                    <x-icon name="info" class="h-4 w-4" />
                                </div>

                                <p class="text-sm leading-6 text-cyan-800">
                                    Masukkan jadwal dan kapasitas peserta dengan benar agar
                                    petugas dapat menyiapkan kebutuhan Zoom sesuai kegiatan.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label
                                    for="requester_name_zoom"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Nama Pemohon
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    id="requester_name_zoom"
                                    name="requester_name"
                                    type="text"
                                    value="{{ old('requester_name') }}"
                                    placeholder="Masukkan nama pemohon"
                                    autocomplete="name"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                >

                                @error('requester_name')
                                    <p class="mt-2 text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="requester_unit_zoom"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Unit/Bagian
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    id="requester_unit_zoom"
                                    name="requester_unit"
                                    type="text"
                                    value="{{ old('requester_unit') }}"
                                    placeholder="Contoh: Prodi Nautika atau Bagian Umum"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                >

                                @error('requester_unit')
                                    <p class="mt-2 text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                for="title_zoom"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Nama Kegiatan
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                id="title_zoom"
                                name="title"
                                type="text"
                                value="{{ old('title') }}"
                                maxlength="255"
                                placeholder="Contoh: Rapat Koordinasi Persiapan Diklat"
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >

                            @error('title')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label
                                    for="event_time"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Waktu Kegiatan
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    id="event_time"
                                    name="event_time"
                                    type="datetime-local"
                                    value="{{ old('event_time') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                >

                                @error('event_time')
                                    <p class="mt-2 text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="participant_capacity"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Kapasitas Peserta
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    id="participant_capacity"
                                    name="participant_capacity"
                                    type="number"
                                    min="1"
                                    value="{{ old('participant_capacity') }}"
                                    placeholder="Contoh: 100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                >

                                @error('participant_capacity')
                                    <p class="mt-2 text-xs font-medium text-rose-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                for="description_zoom"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Catatan Tambahan
                            </label>

                            <textarea
                                id="description_zoom"
                                name="description"
                                rows="5"
                                maxlength="2000"
                                placeholder="Contoh: membutuhkan host, recording, breakout room, atau dukungan teknis lainnya."
                                class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                            >{{ old('description') }}</textarea>

                            @error('description')
                                <p class="mt-2 text-xs font-medium text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- INFORMASI TRACKING --}}
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                            <x-icon name="search-check" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-amber-900">
                                Simpan kode tiket
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-amber-700">
                                Setelah berhasil dikirim, sistem menampilkan kode tiket dan
                                link pelacakan. Jangan membagikan link tersebut kepada pihak
                                yang tidak berkepentingan.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- ACTION --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-slate-500">
                            Field bertanda
                            <span class="font-semibold text-rose-500">*</span>
                            wajib diisi.
                        </p>

                        <div class="flex flex-col-reverse gap-2 sm:flex-row">
                            <a
                                href="{{ route('public.tickets.track-form') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                <x-icon name="arrow-left" class="h-4 w-4" />
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <x-icon name="send" class="h-4 w-4" />
                                Kirim Tiket
                            </button>
                        </div>
                    </div>
                </section>
            </form>

            <footer class="pb-2 text-center text-xs text-slate-500">
                Unit SIM/TI — Sistem Informasi Tiket Operasional
            </footer>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category');
            const generalFields = document.getElementById('general-fields');
            const zoomFields = document.getElementById('zoom-fields');

            const zoomCategory = @json(
                \App\Models\OperationalTicket::CATEGORY_ZOOM
            );

            function setFieldsState(container, enabled) {
                if (!container) {
                    return;
                }

                container
                    .querySelectorAll('input, textarea, select')
                    .forEach(function (field) {
                        field.disabled = !enabled;
                    });
            }

            function setRequiredState(container, requiredFieldIds, enabled) {
                if (!container) {
                    return;
                }

                requiredFieldIds.forEach(function (fieldId) {
                    const field = document.getElementById(fieldId);

                    if (field) {
                        field.required = enabled;
                    }
                });
            }

            function toggleFormFields() {
                if (!categorySelect || !generalFields || !zoomFields) {
                    return;
                }

                const isZoom = categorySelect.value === zoomCategory;

                if (isZoom) {
                    generalFields.classList.add('hidden');
                    zoomFields.classList.remove('hidden');

                    setFieldsState(generalFields, false);
                    setFieldsState(zoomFields, true);

                    setRequiredState(
                        generalFields,
                        [
                            'requester_name_general',
                            'requester_contact_general',
                            'title_general',
                        ],
                        false
                    );

                    setRequiredState(
                        zoomFields,
                        [
                            'requester_name_zoom',
                            'requester_unit_zoom',
                            'title_zoom',
                            'event_time',
                            'participant_capacity',
                        ],
                        true
                    );
                } else {
                    generalFields.classList.remove('hidden');
                    zoomFields.classList.add('hidden');

                    setFieldsState(generalFields, true);
                    setFieldsState(zoomFields, false);

                    setRequiredState(
                        generalFields,
                        [
                            'requester_name_general',
                            'requester_contact_general',
                            'title_general',
                        ],
                        true
                    );

                    setRequiredState(
                        zoomFields,
                        [
                            'requester_name_zoom',
                            'requester_unit_zoom',
                            'title_zoom',
                            'event_time',
                            'participant_capacity',
                        ],
                        false
                    );
                }
            }

            if (categorySelect && generalFields && zoomFields) {
                categorySelect.addEventListener('change', toggleFormFields);

                toggleFormFields();
            }
        });
    </script>
</body>
</html>