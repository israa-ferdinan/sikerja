@php
    $selectedUnitId = old(
        'unit_id',
        $plan?->unit_id ?? ($units->count() === 1 ? $units->first()->id : null)
    );

    $inputClass = 'mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500';

    $errorInputClass = 'border-rose-300 focus:border-rose-500 focus:ring-rose-500';
@endphp

{{-- INFORMASI UTAMA --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                <x-icon name="clipboard-list" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-semibold text-slate-900">
                    Informasi Utama
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Tentukan unit, PIC, judul, kategori, dan prioritas rencana.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-2">
        <div>
            <label
                for="unit_id"
                class="block text-sm font-semibold text-slate-700"
            >
                Unit
                <span class="text-rose-600">*</span>
            </label>

            <select
                id="unit_id"
                name="unit_id"
                class="{{ $inputClass }} @error('unit_id') {{ $errorInputClass }} @enderror"
                required
            >
                <option value="">Pilih Unit</option>

                @foreach ($units as $unit)
                    <option
                        value="{{ $unit->id }}"
                        @selected((string) $selectedUnitId === (string) $unit->id)
                    >
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                Rencana hanya dapat dilihat dan dikelola sesuai scope unit pengguna.
            </p>

            @error('unit_id')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="pic_employee_id"
                class="block text-sm font-semibold text-slate-700"
            >
                PIC
            </label>

            <select
                id="pic_employee_id"
                name="pic_employee_id"
                class="{{ $inputClass }} @error('pic_employee_id') {{ $errorInputClass }} @enderror"
            >
                <option value="">Belum ditentukan</option>

                @foreach ($employees as $employee)
                    <option
                        value="{{ $employee->id }}"
                        @selected(
                            (string) old('pic_employee_id', $plan?->pic_employee_id)
                            === (string) $employee->id
                        )
                    >
                        {{ $employee->name }}
                        {{ $employee->unit?->name ? ' — ' . $employee->unit->name : '' }}
                    </option>
                @endforeach
            </select>

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                PIC akan menerima notifikasi penugasan setelah rencana disimpan.
            </p>

            @error('pic_employee_id')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="lg:col-span-2">
            <label
                for="title"
                class="block text-sm font-semibold text-slate-700"
            >
                Judul Pengembangan
                <span class="text-rose-600">*</span>
            </label>

            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title', $plan?->title) }}"
                class="{{ $inputClass }} @error('title') {{ $errorInputClass }} @enderror"
                placeholder="Contoh: Pengembangan Dashboard Monitoring SIM TI"
                required
            >

            @error('title')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="category"
                class="block text-sm font-semibold text-slate-700"
            >
                Kategori
                <span class="text-rose-600">*</span>
            </label>

            <select
                id="category"
                name="category"
                class="{{ $inputClass }} @error('category') {{ $errorInputClass }} @enderror"
                required
            >
                @foreach ($categories as $category)
                    <option
                        value="{{ $category }}"
                        @selected(
                            old('category', $plan?->category ?? 'Aplikasi') === $category
                        )
                    >
                        {{ $category }}
                    </option>
                @endforeach
            </select>

            @error('category')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="priority"
                class="block text-sm font-semibold text-slate-700"
            >
                Prioritas
                <span class="text-rose-600">*</span>
            </label>

            <select
                id="priority"
                name="priority"
                class="{{ $inputClass }} @error('priority') {{ $errorInputClass }} @enderror"
                required
            >
                @foreach ($priorities as $priority)
                    <option
                        value="{{ $priority }}"
                        @selected(
                            old('priority', $plan?->priority ?? 'Sedang') === $priority
                        )
                    >
                        {{ $priority }}
                    </option>
                @endforeach
            </select>

            @error('priority')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</section>

{{-- JADWAL --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                <x-icon name="calendar-days" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-semibold text-slate-900">
                    Jadwal Pengembangan
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Catat target dan realisasi waktu pelaksanaan rencana.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
        <div>
            <label
                for="target_start_date"
                class="block text-sm font-semibold text-slate-700"
            >
                Target Mulai
            </label>

            <input
                id="target_start_date"
                type="date"
                name="target_start_date"
                value="{{ old('target_start_date', $plan?->target_start_date?->format('Y-m-d')) }}"
                class="{{ $inputClass }} @error('target_start_date') {{ $errorInputClass }} @enderror"
            >

            @error('target_start_date')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="target_end_date"
                class="block text-sm font-semibold text-slate-700"
            >
                Target Selesai
            </label>

            <input
                id="target_end_date"
                type="date"
                name="target_end_date"
                value="{{ old('target_end_date', $plan?->target_end_date?->format('Y-m-d')) }}"
                class="{{ $inputClass }} @error('target_end_date') {{ $errorInputClass }} @enderror"
            >

            @error('target_end_date')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="actual_start_date"
                class="block text-sm font-semibold text-slate-700"
            >
                Realisasi Mulai
            </label>

            <input
                id="actual_start_date"
                type="date"
                name="actual_start_date"
                value="{{ old('actual_start_date', $plan?->actual_start_date?->format('Y-m-d')) }}"
                class="{{ $inputClass }} @error('actual_start_date') {{ $errorInputClass }} @enderror"
            >

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                Dapat dikosongkan apabila pelaksanaan belum dimulai.
            </p>

            @error('actual_start_date')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="actual_end_date"
                class="block text-sm font-semibold text-slate-700"
            >
                Realisasi Selesai
            </label>

            <input
                id="actual_end_date"
                type="date"
                name="actual_end_date"
                value="{{ old('actual_end_date', $plan?->actual_end_date?->format('Y-m-d')) }}"
                class="{{ $inputClass }} @error('actual_end_date') {{ $errorInputClass }} @enderror"
            >

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                Dapat dikosongkan sampai rencana benar-benar selesai.
            </p>

            @error('actual_end_date')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</section>

{{-- URAIAN --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                <x-icon name="file-text" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-semibold text-slate-900">
                    Uraian Rencana
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Jelaskan kebutuhan, tujuan, dan catatan pendukung pengembangan.
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-5 p-5 sm:p-6">
        <div>
            <label
                for="description"
                class="block text-sm font-semibold text-slate-700"
            >
                Deskripsi Kebutuhan
                <span class="text-rose-600">*</span>
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                class="{{ $inputClass }} @error('description') {{ $errorInputClass }} @enderror"
                placeholder="Jelaskan kebutuhan atau masalah yang melatarbelakangi pengembangan."
                required
            >{{ old('description', $plan?->description) }}</textarea>

            @error('description')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="objective"
                class="block text-sm font-semibold text-slate-700"
            >
                Tujuan Pengembangan
            </label>

            <textarea
                id="objective"
                name="objective"
                rows="4"
                class="{{ $inputClass }} @error('objective') {{ $errorInputClass }} @enderror"
                placeholder="Jelaskan tujuan atau hasil yang ingin dicapai."
            >{{ old('objective', $plan?->objective) }}</textarea>

            @error('objective')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="notes"
                class="block text-sm font-semibold text-slate-700"
            >
                Catatan
            </label>

            <textarea
                id="notes"
                name="notes"
                rows="4"
                class="{{ $inputClass }} @error('notes') {{ $errorInputClass }} @enderror"
                placeholder="Tambahkan catatan bila diperlukan."
            >{{ old('notes', $plan?->notes) }}</textarea>

            @error('notes')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</section>

{{-- FORM ACTION --}}
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ $plan
                ? route('developments.plans.show', $plan)
                : route('developments.plans.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
        >
            <x-icon name="x" class="h-4 w-4" />
            Batal
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
        >
            <x-icon name="check-circle" class="h-4 w-4" />
            {{ $submitLabel }}
        </button>
    </div>
</section>