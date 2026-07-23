<x-app-layout>
    @php
        $user = auth()->user();

        $defaultRequesterName =
            old(
                'requester_name',
                $user->employee?->name ?? $user->name
            );

        $defaultRequesterUnit =
            old(
                'requester_unit',
                $user->employee?->unit?->name
            );
    @endphp

    <div class="w-full space-y-6">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[220px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="ticket" class="h-4 w-4" />
                        Operasional SIM/TI
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Input Tiket Manual
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Gunakan form ini untuk mencatat permintaan yang diterima petugas
                        melalui WhatsApp, telepon, laporan lisan, atau saluran internal lainnya.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="user-check" class="h-3.5 w-3.5" />
                            Data Pemohon
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="clipboard-list" class="h-3.5 w-3.5" />
                            Jenis Permintaan
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-100 ring-1 ring-inset ring-white/15">
                            <x-icon name="sticky-note" class="h-3.5 w-3.5" />
                            Keluhan Singkat
                        </span>
                    </div>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ route('operations.tickets.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>
                </div>
            </div>
        </section>

        {{-- INFORMASI PENGGUNAAN --}}
        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sky-700 ring-1 ring-inset ring-sky-200">
                    <x-icon name="info" class="h-5 w-5" />
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-sky-900">
                        Form khusus pencatatan oleh petugas
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-sky-700">
                        Untuk pemohon yang mengisi sendiri, gunakan Form Pemohon agar
                        tiket tercatat sebagai tiket publik dan dapat dilacak memakai kode tiket.
                    </p>

                    <a
                        href="{{ route('public.tickets.create') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-sky-800 transition hover:text-sky-950"
                    >
                        <x-icon name="external-link" class="h-4 w-4" />
                        Buka Form Pemohon
                    </a>
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
                            Form belum dapat disimpan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-rose-700">
                            Periksa kembali field yang ditandai dan lengkapi data wajib.
                        </p>
                    </div>
                </div>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('operations.tickets.store') }}"
            class="space-y-6"
        >
            @csrf

            {{-- DATA PEMOHON --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <x-icon name="user-check" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Data Pemohon
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Isi identitas pihak yang menyampaikan permintaan atau keluhan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-2">
                    <div>
                        <label
                            for="requester_name"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Nama Pemohon
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="requester_name"
                            name="requester_name"
                            type="text"
                            value="{{ $defaultRequesterName }}"
                            required
                            autocomplete="name"
                            placeholder="Masukkan nama pemohon"
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
                            for="requester_contact"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Kontak/WhatsApp
                        </label>

                        <input
                            id="requester_contact"
                            name="requester_contact"
                            type="text"
                            value="{{ old('requester_contact') }}"
                            autocomplete="tel"
                            placeholder="Contoh: 08123456789"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Digunakan bila petugas perlu menghubungi pemohon untuk klarifikasi.
                        </p>

                        @error('requester_contact')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label
                            for="requester_unit"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Asal Unit/Instansi
                        </label>

                        <input
                            id="requester_unit"
                            name="requester_unit"
                            type="text"
                            value="{{ $defaultRequesterUnit }}"
                            placeholder="Contoh: Unit SIM TI"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('requester_unit')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- INFORMASI PERMINTAAN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <x-icon name="clipboard-list" class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Informasi Permintaan
                            </h2>

                            <p class="mt-0.5 text-sm leading-6 text-slate-500">
                                Tentukan jenis permintaan dan jelaskan kebutuhan pemohon.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    <div>
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

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Jenis permintaan digunakan sebagai dasar mapping Tupoksi
                            ketika PIC tiket ditetapkan.
                        </p>

                        @error('category')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="title"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Judul/Keluhan Singkat
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            required
                            maxlength="255"
                            placeholder="Contoh: Tidak bisa login aplikasi"
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Gunakan judul singkat yang langsung menjelaskan inti permasalahan.
                        </p>

                        @error('title')
                            <p class="mt-2 text-xs font-medium text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="description"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            maxlength="2000"
                            placeholder="Jelaskan kronologi singkat, perangkat atau aplikasi yang bermasalah, waktu kejadian, dan informasi pendukung lainnya."
                            class="mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('description') }}</textarea>

                        <div class="mt-2 flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
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

            {{-- INFORMASI LIFECYCLE --}}
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-inset ring-amber-200">
                        <x-icon name="activity" class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-amber-900">
                            Setelah tiket disimpan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Tiket akan dibuat dengan status Baru dan prioritas Normal.
                            Pengelola dapat menentukan PIC, mengubah prioritas, menambahkan
                            catatan, serta memperbarui status melalui halaman detail tiket.
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
                            href="{{ route('operations.tickets.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <x-icon name="arrow-left" class="h-4 w-4" />
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                        >
                            <x-icon name="check-circle" class="h-4 w-4" />
                            Simpan Tiket
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>